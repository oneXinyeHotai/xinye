<?php
namespace app\wk\controller;

use app\common\model\User;
use app\common\vendor\Mailer;
use app\common\vendor\Verify;
use think\Controller;
use think\db\Query;
use think\exception\ErrorException;
use think\Request;
use think\Session;

class UserCon extends Controller
{

    public function Index(Request $request)
    {
        print_r(Session::get('verify'));
    }
    //登录数据处理
    public function LoginDeal()
    {
        $data=input('post.');
        $user=new User;
        return $user->LoginDeal($data);
    }

    //发送邮箱
    public function Mail()
    {
        $mail=new Mailer();
        $yanz=rand(1000,9999);
        Session::set('test',$yanz);
        $data['title']="(｡･∀･)ﾉﾞ嗨，这个是一个测试哦";
        $data['body']="验证码为".$yanz."你肯定猜不到我是谁吧！娃哈哈....";
        $data['sendto']=$_POST['mail'];
        if($mail->GetYz($data)){
            return json(["return"=>"邮箱发送成功"]);
        }else {
            return json(["return"=>"邮箱发送失败"]);
        }
    }
    //登录图片验证码
    public function Verify(){
        $a=new Verify();
        $a->show();
    }
    //注册的数据处理
    public function RegisterDeal()
    {
        $data=input('post.');//获取注册表单的值；
        $user=new User();
        return $user->allowField(true)->validate(true)->AddUser($data);

    }
    //修改数据
    public function ReviseDeal()
    {
        try {
            $user_data=Session::get('user_data');
            if(empty($user_data))
            {
                return json(['retuen'=>'请先进行登录']);
            }else{
                $data=input('post.');
                $user=new User;
                switch($data['key']){
                    case 'head':
                        $requst=new Request();
                        $file=$requst->file('value');
                        //调用控制器中的文件进行验证
                        $result = $this->validate(['file' => $file],
                            ['file' => 'require|image|fileExt:png,jpg,gif'],
                            ['file.require' => '请选择上传文件','file.image'=>'必须是图片哦','file.fileExt'=>'文件格式不对']);
                        //验证完进行处理
                        if($result!==true){
                            return json(['error'=>$result]);
                        }else {
                            return $user->ReviseHead($file);
                        }
                        break;
                    default :
                        return $user->ReviseOther($data);
                        break;
                }
            }
        }catch (ErrorException $e){
            return json(['return'=>"未知错误，也许是你操作失误哦"]);
        }

    }
    //删除用户
    public function DeleteUser(Request $request)
    {
        try {
            if(Session::get('user_data.type')!=1||Session::get('user_data.type')!=2)
            {
                return json(['retuen'=>'权限不够哦']);
            }else{
                $id=$request->get('id');
                $user=new User();
                return $user->DeleteUser($id);
            }
        }catch (ErrorException $errorException){
            return json(['retuen'=>'权限不够哦']);
        }

    }

    //返回当前用户信息或者当个信息
    public function ShowUser()
    {
        $la=Session::get('user_data');
        if(empty($la))
        {
            return json(['retuen'=>'请先进行登录']);
        }else{
            $user=new User();
            return $user->ShowUsers();
        }
    }
    //分页返回全部用户信息
    public function ShowUsers(Request $request)
    {
        $user_data=Session::get('user_data');
        if($user_data['type']!=1&&$user_data['type']!=2)
        {
            return json(['retuen'=>'权限不够哦']);
        }else{
            $user=new User();
            $page=$request->get('page');
            $page=empty($page)?1:$request->get('page');
            $pages=$user->Pages();
            if($page>$pages||$page<=0){
                return json(["return"=>'页码不能超过总页数哦']);
            }
            $contect=$user->ShowUsers($page);
            return json(['contect'=>$contect,'pages'=>$pages]);
        }

    }
    //找回密码和修改密码
    public function BackPass(Request $request)
    {
        $user=new user();
        $data=$request->post();
        return $user->BackPass($data);
    }
    //9、验证邮箱和用户名是否已经存在
    public function Label(Request $request)
    {
        try{
            $data['key']=$_GET['key'];
            $data['value']=$_GET['value'];
            $data['id']=empty($_GET['id'])?0:$_GET['id'];
            $user=new User();
            if($user->SeekUser($data['value'],$data['key'],$data['id']))
            {
                return json(['return'=>'此'.$data['key'].'已经存在']);
            }else{
                return json(['return'=>'此'.$data['key'].'不存在']);
            }
        }catch (ErrorException $e){
            return json(['return'=>'未知错误啊']);
        }

    }
    //用户退出登录
    public function OutLogin()
    {
        try {
            Session::clear();
            Session::delete('think');
            return json(['return'=>'安全退出']);
        }catch (ErrorException $e){
            return json(['return'=>'出现一点小错误，不过不要紧的哦']);
        }
    }

    //放回省
    public function GetCity()
    {
        $filename = empty($_GET['city']) ? 0:$_GET['city'];
        echo file_get_contents(ROOT_PATH."public".DS."static".DS."city".DS."$filename.json");
    }

}



//王康写