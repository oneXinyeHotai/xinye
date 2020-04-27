<?php
namespace app\qk\model;

use think\Model;
use think\Db;

class Register extends Model
{
	//查询用户本月签到数据
	public function user_regi($uid)
	{
		//日期查询条件
		$a = '_____'.date('m').'___';
		$result = Db::name('register')
			->where('uid',$uid)
			->where('date','like',$a)
			->column('date'); 
		return $result;
	}
}