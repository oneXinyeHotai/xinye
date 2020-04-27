<?php
namespace app\common\model;

use app\common\vendor\Mailer;
use app\common\vendor\Picture;
use think\exception\ErrorException;
use think\Image;
use think\Model;
use think\Session;

class User extends Model
{
    //查找用户表中是否存在数据，默认为用户名是否存在
    public function SeekUser($value,$type='name',$id=0)//(查找的用户民，查找类型，如name,mail等~，出这个id外的其它用户名)
    {
        $where=[
            $type=>['=',$value],
            'id'=>['not in',$id]
        ];
       if($re=$this->where($where)->select())
       {
           return true;
       }else{
           return false;
       }
    }

    //注册
    public function AddUser($data)//添加用户
    {
        if($data['test']!=Session::get('test'))//判断验证码是否正确
        {
            return json(["return"=>'验证码不对']);
        }else if($this->SeekUser($data['name'],$type='name')!==false){//判断用户名存在
            return json(["return"=>'用户名已存在']);
        }else if($this->SeekUser($data['mail'],$type='mail')!==false){//判断邮箱存在
            return json(["return"=>'邮箱已绑定用户']);
        }else if(strlen($data['passwd'])>=5&&strlen($data['passwd'])<=15){//判断密码是否规范
            $data['head']=DS.'static'.DS."picture".DS."user".DS.'0.png';
            $data['passwd']=md5($data['passwd']);
            if($this->save($data))//写入数据库，看是否成功
            {
                Session::delete('test');
                return json(["return"=>'用户注册成功']);
            }else{
                return json(["return"=>$this->getError()]);
            }
        }
    }

    //登录
    public function LoginDeal($data)
    {
        if(strtolower($data['test'])!=strtolower(Session::get('verify')))//判断验证码是否正确
        {
            return json(["return"=>"验证码不对"]);
        }else{
            $where=[
                'name'=>['=',$data['name']],
            ];
            if(!$user=$this->where($where)->find())
            {
                return json(["return"=>"用户或者密码不正确，请检查大小写"]);
            }else{
                if(md5($data['passwd'])!==$user['passwd'])
                {
                    return json(["return"=>"用户或者密码不正确，请检查大小写"]);
                }else{
                    Session::set('user_data',$user);
                    Session::delete('verify');
                    return json(["return"=>"登录成功"]);
                }
            }
        }
    }

    //修改当前用户头像
    public function ReviseHead($file)
    {
        $id=Session::get('user_data.id');
        $pictury=new Picture();//调用图像处理函数
        $link=$pictury->Up($file,'user',$id);
        if($this->where(['id'=>['=',$id]])->update(['head'=>$link])||$link){
            return json(['return'=> "修改成功"]);
        }else{
            return json(['return'=> "修改失败"]);
        }
    }
    //修改其它的
    public function ReviseOther($data)
    {
        $id=Session::get('user_data.id');
        switch($data['key']){
            //修改姓名
            case 'name':
                if($this->SeekUser($data['value'],$data['key'],$id)){
                    return json(['return'=> "用户名存在"]);
                }else if(strlen($data['value'])<2||strlen($data['value'])>15){
                    return json(['return'=> "用户名格式不合法"]);
                }
                break;
            //修改手机号码
            case "tele":
                if(strlen($data['value'])!=11){
                    return json(['return'=> "手机号格式不合法"]);
                }
                break;
            //修改邮箱
            case "mail":
                if($this->SeekUser($data['value'],$data['key'],$id)){
                    return json(['return'=> "此邮箱判定用户，不可再次判定"]);
                }
                break;
            //修改性别
            case 'sex':
                if($data['value']!=0&&$data['value']!=1){
                    return json(['return'=> "性别填写错误，请小心填写哦"]);
                }
                break;
            //修改职业
            case 'professions':
                if(empty($data['value'])||strlen($data['value'])>10){
                    return json(['return'=> "职业可不能为空！也不可以写得太长了哦"]);
                }
            //修改省
            case 'address_sheng':
                if(empty($data['value'])||strlen($data['value'])>10){
                    return json(['return'=> "省可不能为空！也不可以写得太长了哦"]);
                }
            //修改市
            case 'address_shi':
                if(empty($data['value'])||strlen($data['value'])>10){
                    return json(['return'=> "市可不能为空！也不可以写得太长了哦"]);
                }
        }

        //如果符合条件，就进行写入数据库
        if($this->where(['id'=>['=',$id]])->update([$data['key']=>$data['value']])){
            return json(['return'=> "修改成功"]);
        }else{
            return json(['return'=> "未知错误,也许是你还没有进行修改哦"]);
        }
    }

    //密码验证
    public function PasswdYz($passwd,$id){
        $passwd=md5($passwd);
        $a=$this->where(['id'=>['=',$id]])->column('passwd');
        if($passwd==$a[0]){
            return true;
        }else{
            return false;
        }
    }
    //删除用户
    public function DeleteUser($id)
    {
        try{
            //查询用户级别
            if($this->UserType($id)==1&&Session::get('user_data.id!=2'))
            {
                return json(['retuen'=>'权限不够哦,他可是管理员用户，你不可以随便删除']);
            }else{
                //删除收藏
                $this->UC()->where(['uid'=>['=',$id]])->delete();
                //删除公告
                $this->UB1()->where(['uid1'=>['=',$id]])->delete();
                $this->UB2()->where(['uid2'=>['=',$id]])->delete();
                //问题反馈
                $this->UQR()->where(['uid'=>['=',$id]])->delete();
                //删除用户
                if($this->where(['id'=>['=',$id]])->delete()){
                    return json(['return'=> "删除成功"]);
                    //发送邮件进行提示
                }else{
                    return json(['return'=> "未知错误"]);
                }
            }
            //删除签到
            //发送邮件进行说明
        }catch (ErrorException $e){
            return json(['return'=> "未知错误,请稍后再试"]);
        }
    }
    //查找用户级别
    public function UserType($id)
    {
       if($type=$this->where(['id'=>['=',$id]])->column('type'))
       {
           print_r($type[0]);
       }else{
           return json(['return'=> "未知错误"]);
       }
    }
    //显示用户
    public function ShowUsers($page=-1)
    {
        try {
            if($page==-1)
            {
                //返回当前用户信息
                $id=Session::get('user_data.id');
                $where=[
                    'id'    =>  ['like',$id]
                ];
                if($re=$this->where($where)->select())
                {
                    return json($re[0]);
                }else{
                    return json(['return'=>'系统出错，用户信息暂时显示不了，请联系2205086744']);
                }
            }else{
                $star=($page-1)*5;
                $re=$this->limit($star,5)->select();
                if($re){
                    return $re;
                }else{
                    return json(['return' => "出现错误，也许是没有数据咯"]);
                }
            }
        }catch (ErrorException $e){
            return json(['return'=>'系统出错，用户信息暂时显示不了，请联系2205086744']);
        }
    }
    //返回共页面数
    public function Pages(){
        try {
            $re=$this->count();
            $yu=$re%5;
            if($re==0)
            {
                $pages=(int)($re/5);
            }else{
                $pages=(int)($re/5)+1;
            }
            return $pages;
        }catch (ErrorException $e){
            return json(['return'=>"获取页面数失败"]);
        }
    }
    //找回密码和修改密码
    public function BackPass($data){
        //验证验证码是否正确
        if($data['test']!=Session::get('test'))
        {
            return json(['return'=>'验证码错误']);
        }else{
            $where=[
                'name'=>['=',$data['name']],
                'mail'=>['=',$data['mail']]
            ];
            $updata=[
                'passwd'=>md5($data['passwd']),
            ];
            if($this->where($where)->update($updata))
            {
               Session::delete('test');
                return json(['return'=>'修改成功']);
             
            }else{
                return json(['return'=>'修改失败，注意你填写的信息哦,也许是密码没有进行修改哦']);
            }
        }
    }

    //增加用户签到数//全康写
    public function addregi($id)
    {
        $user = $this->where('id',$id)->find();
        $user -> register+=1;
        if(!$user ->save())
        {
            return json('用户总签到数增加失败！');
        }
        return 1;
    }
    //通过用户名获取id//全康写
    public function getuserid($name)
    {
        $user = $this->where('name',$name)->value('id');
        return $user;
    }

    //一对多和收藏表
    public function UC()
    {
        return $this->hasMany('Collect','id','uid');
    }
    //一对多签到表
    public function UQ()
    {
        return $this->hasMany('Collect','id','uid');
    }
    //一对多和公告表
    public function UB1()
    {
        return $this->hasMany('message','id','uid1');
    }
    //一对多和公告表
    public function UB2()
    {
        return $this->hasMany('message','id','uid2');
    }
    //一对多和问题反馈表
    public function UQR()
    {
        return $this->hasMany('question_re','id','uid');
    }
}



//王康写