<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule=[
        ['name','min:2|max:10','用户名要在2~10个字符中'],
        ['passwd','length:32','密码出现错误'],
        ['mail','email','要使用正确的邮箱地址'],
    ];
}



//王康写