<?php

namespace app\ygq\validate;
use think\Validate;


class QuestionRe extends Validate
{
    protected $rule = [
        ['tel', '/^1[34578]\d{9}$/', '手机格式不正确'],
        ['content','require','内容不能为空'],
    ];
    //验证手机号
//    protected function checkTel($value,$rule){
//        $result = preg_match('/^1[34578]\d{9}$/',$value);
//        if($result){
//            return true;
//        }else{
//            return "手机格式不正确";
//        }
//    }

}