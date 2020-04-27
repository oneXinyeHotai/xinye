<?php
namespace app\qk\controller;

use app\qk\model\Register;
use app\common\model\User;
use think\Controller;
use	think\Session;

class RegisterCon extends Controller
{	
	//新增签到
	public function add()
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$data = array();
		$data['uid'] = Session::get('user_data.id');
		$data['date'] = date("Y-m-d");
		$register = new Register;
		if($a=Register::get(['date'=> $data['date'], 'uid' => $data['uid']]))
		{
			return json('你今天已签到');
		}
		$user = new User;
		if($register->allowField(true)->save($data) && $user->addregi($data['uid']))
		{
			return json('签到成功!');
		}
		else
		{
			return json('签到失败!');
		}
	}
	
	//读取签到
	public function read()
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$register = new Register;
		$register = $register->user_regi( Session::get('user_data.id') );
		if($register)
		{
			for($i=0; $i<count($register); $i++)
			{
				$register[$i] = substr($register[$i],-2);
			}
			return	json_encode($register);
		}
		else
		{
			return json(['error' => "无用户本月签到数据！"]);
		}
	}
	
}