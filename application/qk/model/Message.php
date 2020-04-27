<?php
namespace app\qk\model;

use think\Model;
use think\Db;

class Message extends Model
{
	public function show_mess($page)
	{
		$star=($page-1)*8;
		$re=$this->limit($star,8)->select();
		if($re){
		    return $re;
		}else{
		    return json(['return' => "没有公告数据！"]);
		}
	}
	
	public function showusermess($uid2,$page)
	{
		$star=($page-1)*8;
		$re = Db::name('message')
		->alias("m") //取一个别名
		->join('user u', 'm.uid1 = u.id')
		->where('uid2',$uid2)
		->limit($star,8)
		->field('m.id,m.title,m.content,m.time,u.name as uname1,u.head as head1')
		->select();
		
		/* $result	= Db::view('message','id,title,content,time')
			->view('user',['head'=>'head1','name'=>'uname1'],'user.id=message.uid1')
			->where('uid2',$uid2)
			->limit($star,8)
			->select(); */ //未测试,不知道能否通过
		//$re=$this->where('uid2',$uid2)->limit($star,8)->select();//原来的查询代码
		
		if($re){
		    return $re;
		}else{
		    return json(['return' => "没有用户公告数据！"]);
		}
	}
	
	public function showadminmess($uid1,$page)
	{
		$star=($page-1)*8;
		$re = Db::name('message')
		->alias("m") //取一个别名
		->join('user u', 'm.uid2 = u.id')
		->where('uid1',$uid1)
		->limit($star,8)
		->field('m.id,m.title,m.content,m.time,u.name as uname2,u.head as head2')
		->select();
		
		if($re){
			return $re;
		}else{
			return json(['return' => "没有管理员公告数据！"]);
		}
	}
		
	
	public function Pages(){
	    try {
	        $re=$this->count();
	        $yu=$re%8;
	        if($yu==0)
	        {
	            $pages=(int)($re/8);
	        }else{
	            $pages=(int)($re/8)+1;
	        }
	        return $pages;
	    }catch (ErrorException $e){
	        return json(['return'=>"获取页面数失败"]);
	    }
	}
}