<?php
namespace app\qk\validate;

use think\Validate;

class Message extends Validate
{
    protected $rule=[
        ['tiltle','require|max:20','公告标题不能超过20个字符！'],
		['content','text','公告为文本格式！'],
        ['uname2','require|min:2|max:10','用户名要在2~10个字符中'],
    ];
}