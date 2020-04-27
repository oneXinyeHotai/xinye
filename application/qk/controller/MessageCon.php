<?php
namespace app\qk\controller;

use app\qk\model\Message;
use app\common\model\User;
use think\Controller;
use	think\Session;
use think\Request;
use think\Paginator;

class MessageCon extends Controller
{
	/* //用户权限验证
	public function user_yz()
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
	} */
	
	//显示公告
	public function index(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$message = new Message;
		//获取当前页面数
        $page=$request->get('page');
		$page=empty($page)?1:$request->get('page');
		$pages=$message->Pages();
		if($page>$pages||$page<=0){
		    return json(["return"=>'页码不能超过总页数！']);
		}
		$message = $message->show_mess($page);
		return json(['message'=>$message,'pages'=>$pages]);
	}
	
	//添加公告
	public function add()
	{
		/* if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		} */
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$data = input('post.');
		$data['uid1'] = Session::get('user_data.id');
		// 数据验证
		$result	= $this->validate($data,'Message'); 
		//验证收件人是否存在
		if(!User::get(['name' => $data['uname2'] ]) )
		{
			return json(['error' => "收件人不存在！"]);
		}
		$user = new User;
		$data['uid2'] = $user -> getuserid($data['uname2']);
		$message = new Message;
		if($message->allowField(true)->save($data))
		{
			return json('公告添加成功!');
		}
		else
		{
			return json(['error' => "公告添加失败！"]);
		}
	}
	
	//读取公告
	public function read(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$id=$request->get('id');
		$message = Message::get($id);
		if($message)
		{
			return	json_encode($message);
		}
		else
		{
			return json(['error' => "公告不存在！"]);
		}
	}
	
	/* //修改公告 (未完成，因不需要此功能)
	public function update(Request $request)
	{
		$this->user_yz();
		$id=$request->get('id');
		$message = Message::get($id);
		$message->title = $_POST['title'];
		$message->content = $_POST['content'];
		//$message->time = date("Y-m-d H:i:s");
		$message->uname1 = $_POST['uname1'];
		$message->uname2 = $_POST['uname2'];
		if(!User::get(['name' => $_POST['uname1'] ]) )
		{
			return json(['error' => "发件人不存在！"]);
		}
		if(!User::get(['name' => $_POST['uname2'] ]) )
		{
			return json(['error' => "收件人不存在！"]);
		}
		if	(false != $message->save())
		{
			return	json('公告修改成功！');
		}
		else
		{
			return json(['error' => "公告修改失败！"]);
		}
	} */
	
	//删除公告
	public function delete(Request $request)
	{
		
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$id=$request->get('id');
		$message = Message::get($id);
		if($message)
		{
			$message ->delete();
			return	json('公告删除成功！');
		}
		else 
		{
			return json(['error' => "公告不存在！"]);
		}
	}
	
	//显示用户的有关公告
	public function showusermess(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$uid2 = Session::get('user_data.id');
		$message = new Message;
		//获取当前页面数
        $page=$request->get('page');
		$page=empty($page)?1:$request->get('page');
		$pages=$message->Pages();
		if($page>$pages||$page<=0){
		    return json(['error'=>'页码不能超过总页数！']);
		}
		$message = $message->showusermess($uid2,$page);
		return json(['message'=>$message,'pages'=>$pages]);
	}
	
	//显示管理员的有关公告
	public function showadminmess(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$uid1 = Session::get('user_data.id');
		$message = new Message;
		//获取当前页面数
	    $page=$request->get('page');
		$page=empty($page)?1:$request->get('page');
		$pages=$message->Pages();
		if($page>$pages||$page<=0){
		    return json(['error'=>'页码不能超过总页数！']);
		}
		$message = $message->showadminmess($uid1,$page);
		return json(['message'=>$message,'pages'=>$pages]);
	}
	
}