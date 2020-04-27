<?php
namespace app\common\vendor;

class Address
{
    static public function Get($filename=0){
        $a = json_decode(file_get_contents(ROOT_PATH.'public'.DS.'static'.DS.'city'.DS.$filename.'.json'));
        print_r($a);
    }
}

//王康写