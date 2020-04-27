<?php
namespace app\common\vendor;
use think\Session;

class Verify
{
    private $x=80;
    private $y=30;
    private $img=null;
    private $str='';
    public function show()
    {
        $this->x=80;
        $this->y=30;
        $this->ini1();
        $this->rangshu();
        $this->printhua();
    }
    public function getCode()
    {
        print_r($this->str);
    }
    private function ini1()
    {
        $this->img=imagecreatetruecolor($this->x,$this->y);
        $color1=imagecolorallocate($this->img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
        imagefill($this->img,0,0,$color1);
    }
    private function rangshu()
    {
        $color2=imagecolorallocate($this->img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
        $color3=imagecolorallocate($this->img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
        //随机产生四位数的字符串
        $arr1=range('0','9');
        $arr2=range('a','z');
        $arr3=range('A','Z');
        $arr=array_merge($arr1,$arr2,$arr3);
        shuffle($arr);
        $str2='';
        $str1=array_rand($arr,4);
        foreach ($str1 as $value)
        {
            $this->str.=$arr[$value];
        }
        Session::set('verify',$this->str);
        //$_SESSION['verify']=$this->str;
        //的到四位数后旧才是化矩形填充
        imagefilledrectangle($this->img,0,0,$this->x,$this->y,$color2);
        //填加字符串
        $filename=ROOT_PATH."public".DS."static".DS."msyhbd.ttf";
        imagettftext($this->img,18,0,8,25,$color3,$filename,$this->str);

    }
    public function printhua()
    {
        for($i=0;$i<150;$i++)
        {
            $color4=imagecolorallocate($this->img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($this->img,mt_rand(0,$this->x),mt_rand(0,$this->y),$color4);
        }
        for($i=0;$i<10;$i++)
        {
            $color4=imagecolorallocate($this->img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imageline($this->img,mt_rand(0,$this->x),mt_rand(0,$this->y), mt_rand(0,$this->x), mt_rand(0,$this->y),$color4);
        }
        //在输出钱要告诉浏览器这个格式
        //header("Content-type:image/png");
        ob_clean();
        header('Content-type:image/png');
        imagepng($this->img);
        //print_r($this->img);
    }

}
?>

<!-- session_start();
//产生一张画布 -->
