<?php


namespace app\ygq\model;
use think\Model;

class Activity extends Model
{

    //修改器
//    protected function setTimeAttr($value){
//        return strtotime($value);
//    }
    public function Pages(){
        try{
            $re = $this->count();
            if($re==0){
                $pages=(int)( $re / 4);
            }else{
                $pages=(int)($re / 4) + 1;
            }
            return $pages;
        }catch (ErrorException $e){
            return json(['return'=>"获取页面数失败"]);
        }
    }

    public function show($page)
    {
        $star = ($page - 1) * 4;
        $re = $this->limit($star,4)->select();
        if($re){
            return $re;
        }else{
            return json(['return' => "没有数据！"]);
        }
    }
}