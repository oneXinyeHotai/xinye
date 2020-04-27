<?php
namespace app\wk\controller;

use app\wk\model\Works;
use think\Controller;
use think\exception\ErrorException;
use think\Request;
use think\Session;

class WorksCon extends Controller
{
    //作品增加
    public function WorksAdd(Request $request)
    {
        if(Session::get('user_data.type')!=1&&Session::get('user_data.type')!=2)
        {
            return json(['retuen'=>'权限不够哦']);
        }else{
            $data=$request->post();
            $data['picture']=$request->file('picture');
            $works=new Works();
            return $works->WorksAdd($data);
        }

    }
    //作品单个删除
    public function WorksDelete(Request $request)
    {
        try{
            if(Session::get('user_data.type')!=1&&Session::get('user_data.type')!=2)
            {
                return json(['retuen'=>'权限不够哦']);
            }else{
                $ids[0]=$request->get('id');
                print_r($ids);
                $works=new Works();
                return $works->WorksDelete($ids);
            }
        }catch (ErrorException $e){
            return json(['return'=>'未知错误，请稍后删除']);
        }
    }
    //批量删除
    public function WorksDeletes()
    {
        try{
            if(Session::get('user_data.type')!=1&&Session::get('user_data.type')!=2)
            {
                return json(['retuen'=>'权限不够哦']);
            }else{
                $ids=$_POST['id'];
                $works=new Works();
                return $works->WorksDelete($ids);
            }
        }catch (ErrorException $e){
            return json(['return'=>'未知错误，请稍后删除']);
        }
    }
    //作品查看
    public function WorksSeek()
    {
        //默认为当前全部作品，第一页，每页6条数据
        $data['page']=empty($_GET['page'])?1:$_GET['page'];
        $data['num']=empty($_GET['num'])?6:$_GET['num'];
        $data['key']=empty($_GET['key'])?'name':$_GET['key'];
        $data['value']=empty($_GET['value'])?'':$_GET['value'];
        //判断要搜索的健是否正确
        $Works=new Works();
        if(!$Works->LabelKey($data['key'])){
            return json(['return'=>'要搜索的key不对哦']);
        }
        //获取总页数
        $pages=$Works->GetPages($data);
        //判断页码是否超过
        if($data['page']>$pages||$data['page']<=0)
        {
            return json(['return'=>'没有作品了哦']);
        }
        $contest=$Works->WorksSeek($data);
        return json(['pages'=>$pages,'contest'=>$contest]);
    }
    //当前登录用户作品数据返回
    public function LoginWorks()
    {
        //判断是否登录
      	$user_data=Session::get('user_data');
        if(empty($user_data))
        {
            return json(['return'=>'还没有登陆哦']);
        }else{
            $data['uid']=$user_data['id'];
        }
        //默认为当前用户全部作品，第一页，每页6条数据
        $data['page']=empty($_GET['page'])?1:$_GET['page'];
        $data['key']=empty($_GET['key'])?'name':$_GET['key'];
        $data['value']=empty($_GET['value'])?'':$_GET['value'];
        $data['num']=6;
        //判断要搜索的健是否正确
        $Works=new Works();
        if(!$Works->LabelKey($data['key'])){
            return json(['return'=>'要搜索的key不对哦']);
        }
        //获取总页数
        $pages=$Works->GetPages($data);
        //判断页码是否超过
        if($data['page']>$pages||$data['page']<=0)
        {
            return json(['return'=>'没有作品了哦']);
        }
        $contest=$Works->WorksSeek($data);
        return json(['pages'=>$pages,'contest'=>$contest]);
    }
}




//王康写