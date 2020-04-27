<?php
//用来进行测试百度地图的接口
namespace app\common\vendor;

class BaiduAdress
{
    //查找地址的经纬度,放回地址信息
    static public function Baidu($address)
    {
        $data=[
            'address'=>$address,
            'ak'=>'gGqOk8FvDAX2YLGfSIPIA3h95ZNOECQz',
            'output'=>'json',//放回数据类型
        ];
        $url="http://api.map.baidu.com/geocoding/v3/?".http_build_query($data);
       //$a=self::doCurl($url);//通过curl这个php类进行get访问
        $a=file_get_contents($url);//通过文件打开的方式直接访问
       return $a;
    }
    //通过curl这个php类进行访问

    //通过curl这个php类进行访问
    static function doCurl($url,$type=0,$data=[])
    {
        $ch = curl_init();//初始化
        //设置选项
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,0);
        if($type == 1){
            //post
            curl_setopt($ch,CURLOPT_PORT,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        $a=curl_exec($ch);
        curl_close($ch);
        return $a;
    }

    //调用百度地图的静态地图
    static public function Jmap($address)//地址或者（经，纬度）
    {
        $add=$address;
        //ak
        $ak="gGqOk8FvDAX2YLGfSIPIA3h95ZNOECQz";
        //域名
        $qurl="http://api.map.baidu.com/staticimage/v2?";
        //连接
        $url=$qurl."ak={$ak}&width=280&height=140&zoom=11&center={$add}&markers={$add}";//先经后纬
        //传入前台
        return $url;
    }

    //路线规划//现在还没有身份验证不能使用这个功能
    static public function GJmap($origin,$destination){//起点，终点
        $ak='gGqOk8FvDAX2YLGfSIPIA3h95ZNOECQz';
        $qurl="http://api.map.baidu.com/directionlite/v1/driving?";
        $url=$qurl."ak={$ak}&origin={$origin}&destination={$destination}";
        return $url;
    }
}
