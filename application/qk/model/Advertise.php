<?php
namespace app\qk\model;

use think\Model;
use think\Db;

class Advertise extends Model
{
	public function alladve_l()
	{
		$result = Db::name('advertise')
			->column('link'); 
		return $result;
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
  
	public function alladve($page)
	{
		$star=($page-1)*8;
		$re=$this->limit($star,8)->select();
		if($re){
		    return $re;
		}else{
		    return json(['return' => "没有广告数据！"]);
		}
	}
}