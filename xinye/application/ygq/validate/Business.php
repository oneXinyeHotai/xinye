<?php


namespace app\ygq\validate;


use think\Validate;

class Business extends Validate
{
    protected $rule = [
        ['explains','require','内容不能为空'],
        ['title','require','内容不能为空'],
        //['image','require','图片不能为空'],
    ];
}