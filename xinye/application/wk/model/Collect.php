<?php
namespace app\wk\model;

use think\exception\ErrorException;
use think\Model;
use think\Session;

class Collect extends Model
{
    //添加-取消添加
    public function CollectAdd($data)
    {
        $data['uid']=Session::get('user_data.id');
        try{
            $where=[
                'uid'=>['=',$data['uid']],
                'wid'=>['=',$data['wid']]
            ];
            if($this->where($where)->delete())
                return json(['return'=>'取消收藏成功']);
            else {
                if ($this->save($data)) {
                    return json(['return' => '收藏成功']);
                } else {
                    return json(['return' => '操作失误']);
                }
            }
        }catch (ErrorException $e){
            return json(['return' => '系统出错']);
        }
    }
    //分页返回当前用户所有的收藏
    public function UserCollect($page)
    {
        $start=(($page-1)*5);
        $where=[
            'uid'=>Session::get('user_data.id')
        ];
        if($re=$this->where($where)->limit($start,5)->select())
        {
            $res=null;
            $i=0;
            foreach ($re as $colls)
            {
                if($a=$this->CW()->where(['id'=>['=',$colls['wid']]])->select()){
                    $res[$i]=$a;
                }else{
                    $res[$i]='改作品不存在';
                    continue;
                }
                $i++;
            }
            return $res;
        }
    }

    //返回当前用户收藏的总数页码5条记录一页
    public function GetPages()
    {
        $where=[
            'uid'=>Session::get('user_data.id')
        ];
        $data=$this->where($where)->count();
        if($data%5==0){
            $pages = (int)($data/5);
        }else{
            $pages = (int)($data/5)+1;
        }
        return $pages;
    }

    //删除当前登录用户的收藏
    public function CollectDelete($wid)
    {
        try {
            $where=[
                'wid'=>['=',$wid],
                'uid'=>['=',Session::get('user_data.id')]
            ];
            if($this->where($where)->delete())
            {
                return json(['return'=>'删除成功']);
            }else{
                return json(['return'=>'删除失败']);
            }
        }catch (ErrorException $e){
            return json(['return'=>'系统出现错误']);
        }

    }


    //一对一和作品表
    public function CW()
    {
        return $this->hasOne('Works','wid','id');
    }
}


//王康写