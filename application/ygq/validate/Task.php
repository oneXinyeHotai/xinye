<?php


namespace app\ygq\validate;


use think\Validate;

class Task extends Validate
{
    protected $rule = [
        ['name','require','订单名不能为空'],
        ['client','require','委托人不能为空'],
        ['tel', '/^1[34578]\d{9}$/', '手机格式不正确'],
        ['content','require','订单内容不能为空'],
        ['money','require','订单价格不能为空'],
    ];

//    public function checkTel($value,$rule){
//        $result = preg_match("{$rule}",'$value');
//        if($result){
//            return true;
//        }else{
//            return "手机格式不正确";
//        }
//    }
}