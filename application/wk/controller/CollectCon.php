<?php
namespace app\wk\controller;

use app\wk\model\Collect;
use think\Request;
use think\Controller;
use think\Session;

class CollectCon extends Controller
{
    public function Index()
    {
        print_r(Session::get('user_data'));
    }
    //添加收藏
    public function CollectAdd(Request $request)
    {
      	$user_data=Session::get('user_data');
        if(empty($user_data))
        {
            return json(['retuen'=>'权限不够哦']);
        }else{
            $data['wid'] = $request->get('wid');
            $collect=new Collect();
            return $collect->CollectAdd($data);
        }

    }
    //放回当前用户收藏
    public function UserCollect(){
        $user_data=Session::get('user_data');
        if(empty($user_data))
        {
            return json(['retuen'=>'还没有进行登录哦']);
        }else{
            $page=empty($_GET['page'])?1:$_GET['page'];
            $collect=new Collect();
            $pages=$collect->GetPages();
            if($page>$pages||$pages<=0){
                return json(['return'=>'页码不能超过总页数']);
            }
            $contect=$collect->UserCollect($page);
            return json(['pages'=>$pages,'contect'=>$contect]);
        }
    }
    //删除收藏
    public function CollectDelete(Request $request)
    {
        $user_data=Session::get('user_data');
        if(empty($user_data))
        {
            return json(['retuen'=>'还没有进行登录哦']);
        }else{
            $wid=$request->get('wid');
            $collect=new Collect();
            return $collect->CollectDelete($wid);
        }
    }
}



//王康写