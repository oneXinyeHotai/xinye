<?php


namespace app\ygq\controller;
use app\ygq\model\Activity;
use think\Controller;
use think\Request;
use think\Db;
use app\ygq\model\QuestionRe;
use think\Session;

//问题反馈类
class UserQues extends Controller
{
    //后台展示问题反馈
    public function index(){
        //分页
        $ques = new QuestionRe();
        //获取当前页面数
        $page  = empty($_GET['page']) ? 1 : $_GET['page'];
        $pages = $ques->Pages();
        if($pages == 0){
            return json(["return"=>'当前页面无数据！']);
        }
        if($page > $pages || $page <= 0){
            return json(["return"=>'页码不能超过总页数！']);
        }
        $message = $ques->show($page);
        return json(['message'=>$message,'pages'=>$pages]);

    }

    //获取问题并存入数据库
    public function add(){
        $limit = Session::get('user_data.type');

        $ques = new QuestionRe;
        $data = input('post.');
        $uid = Session::get('user_data.id');
       // $uid = 4;
        $data['uid'] = $uid;
        if($ques->save($data)){
            return json('问题反馈添加成功',200);
        }else{
            //return $task->getError();
            return json('问题反馈添加失败',404);
        }
    }

    //问题反馈删
    public function del($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $ques = QuestionRe::get($id);
        if($ques->delete()){
            return json(['success'=>'记录删除成功'],200);
        }else{
            return json(['error'=>'记录删除失败'],404);
        }

    }

    public function update(){

    }

}
//余广谦写