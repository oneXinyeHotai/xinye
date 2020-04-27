<?php
namespace app\common\vendor;

use think\Image;
use think\Request;

class Picture
{
    public function Up($file,$type,$id=0)//（图片文件，什么需要处理,上传图片人的id号）
    {
        //进行图片命名和打开图片
        $request=new Request();
        $saveName=$request->time();//获取时间挫
        $image=Image::open($file);
        //$link得命名规范，static/picture/数据库名/时间挫.png(最好是用户id+时间挫.png)

        //图片处理，如果需要，可以在下面添加case进行图片处理，
        switch($type){
            //用户
            case 'user':
                $deal=$image->thumb(100,100,6);
                $link=DS.'static'.DS.'picture'.DS.$type.DS.$id.'.png';
                break;
            //作品王康写
            case 'works':
                $deal=$image->thumb(780,720,6);
                $link=DS.'static'.DS.'picture'.DS.$type.DS.$id.$saveName.'.png';
                break;

                //广告1922:444
            //订单种类220：200
        }
        //上传图片
        if($deal->save(ROOT_PATH.'public'.DS.$link)){
            //echo $deal->width()."<br/>".$deal->height();
            return $link;//放回路径
        }else{
            echo "图片处理失败，未知错误";
        }
    }
}



//王康写