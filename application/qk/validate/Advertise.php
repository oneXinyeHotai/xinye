<?php
namespace app\qk\validate;

use think\Validate;

class Advertise extends Validate
{
    protected $rule=[
		['link','str','广告路径为字符串格式！'],
		['picture','require|text','广告解释为文本格式！'],
    ];
}