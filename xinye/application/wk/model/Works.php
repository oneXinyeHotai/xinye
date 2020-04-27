<?php
namespace app\wk\model;

use app\common\vendor\Picture;
use think\exception\ErrorException;
use think\Model;
use think\Session;

class Works extends Model
{
    //添加作品
    public function WorksAdd($data)
    {
        try {
            //图片处理，放回路径
            $picture=new Picture();
            $data['picture']=$picture->Up($data['picture'],'works');
            $data['time']=date('Y-m-d');
            $data['uid']=Session::get('user_data.id');
            // 验证和写入数据库
            if(!$this->allowField(true)->validate(true)->save($data))
            {
                return json(['return'=>$this->getError()]) ;
            }else{
                return json(['return'=>'作品添加成功']) ;
            }
        }catch (ErrorException $e){
            return json(['return'=>'作品添加失败，未知错误，请稍后再试']) ;
        }
    }

    //删除
    public function WorksDelete($ids)
    {
        try {
            foreach ($ids as $id)
            {
                $this->WC()->where(['wid'=>['=',$id]])->delete();
                if(!$this->where(['id'=>['=',$id]])->delete()){
                    return json(['return'=>'删除错误，也许是你删除了没有的作品哦']);
                }
            }
            return json(['return'=>'作品删除成功']) ;
        }catch (ErrorException $e){
            return json(['return'=>'系统错误']) ;
        }
    }
    //查找
    public function WorksSeek($data)
    {
        $star=($data['page']-1)*$data['num'];
        //判断是否有当前用户
        if(!empty($data['uid']))
        {
            $where=[
                $data['key']=>['like','%'.$data['value'].'%'],
                'uid'=>$data['uid'],
            ];
        }else{
            $where=[
                $data['key']=>['like','%'.$data['value'].'%'],
            ];
        }
        //执行
        if($contest=$this->where($where)->limit($star,$data['num'])->select())
        {
          $user_data=Session::get('user_data');
            $Loginid=empty($user_data['id'])?-1:$user_data['id'];
            $contest=$this->LabelColl($contest,$Loginid);
            return $contest;
        }else{
            return '有错误哦';
        }
    }
    //判断当前用户是否收藏
    public function LabelColl($contest,$Loginid)
    {
        foreach ($contest as $work)
        {
            $where=[
                'wid'=>['=',$work['id']],
                'uid'=>['=',$Loginid]
            ];
            if($this->WC()->where($where)->select())
            {
                $work['labelCol']=1;
            }else{
                $work['labelCol']=0;
            }
        }
        return $contest;
    }
    //判断是否是作品表中的字段
    public function LabelKey($key)
    {
        if($key=='name'||$key=='content'||$key=='time'||$key=="designation")
        {
            return true;
        }else{
            return false;
        }
    }
    //获取总页数
    public function GetPages($data)
    {
        try {
            //判断是否返回当前用户
            if(!empty($data['uid']))
            {
                $where=[
                    $data['key']=>['like','%'.$data['value'].'%'],
                    'uid'=>$data['uid'],
                ];
            }else{
                $where=[
                    $data['key']=>['like','%'.$data['value'].'%'],
                ];
            }
            //执行
            if($nums=$this->where($where)->count())
            {
                if($nums%$data['num']==0)
                {
                    return $nums/$data['num'];
                }else{
                    return (int)($nums/$data['num'])+1;
                }
            }else{
                return 0;
            }
        }catch (ErrorException $e){
            return json(['return'=>'显示作品失败，请稍后再试']);
        }
    }

    //一对多和收藏表
    public function WC()
    {
        return $this->hasMany('Collect','id','Wid');
    }
}


//王康写