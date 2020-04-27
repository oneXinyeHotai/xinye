<?php
namespace app\wk\validate;

use think\Validate;

class Works extends Validate
{
    protected $rule=[
        ['name','max:20','标题不能超过20个字符'],
        ['content','require','内容不能为空'],
        ['picture','require','示例图片也不能为空'],
        ['uid','require','系统出现错误，请稍后再试'],
        ['time','date','时间出现错误'],
    ];
}



//王康写