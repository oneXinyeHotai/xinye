<?php


namespace app\ygq\model;


use think\Model;

class Task extends Model
{
    public function Pages(){
        try{
            $re = $this->count();
            if($re==0){
                $pages=(int)( $re / 6);
            }else{
                $pages=(int)($re / 6) + 1;
            }
            return $pages;
        }catch (ErrorException $e){
            return json(['return'=>"获取页面数失败"]);
        }
    }

    public function show($page)
    {
        $star = ($page - 1) * 6;
        $re = $this->limit($star,6)->select();
        if($re){
            return $re;
        }else{
            return json(['return' => "没有数据！"]);
        }
    }
}