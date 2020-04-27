<?php


namespace app\ygq\controller;
use think\Db;
use app\ygq\model\Task;
use think\Controller;
use think\Session;

class UserTask extends Controller
{
    //展示用户自己的订单
    public function showOne(){
        $uid = Session::get('user_data.id');
        $result = Db::name('task')
            ->where(['uid' => $uid])
            ->select();
        if(empty($result)){
            return json(['msg'=>'目前还没有订单']);
        } else{
            return json_encode($result);
        }

    }

    public function index(){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);
        //分页
        $task = new Task();
        //获取当前页面数
        $page  = empty($_GET['page']) ? 1 : $_GET['page'];
        $pages = $task ->Pages();
        if($pages == 0) return json(["return"=>'当前页面无数据！']);
        if($page>$pages||$page<=0){
            return json(["return"=>'页码不能超过总页数！']);
        }
        $message = $task ->show($page);
        return json(['message'=>$message,'pages'=>$pages]);
    }
    public function add(){
        $user_data=Session::get('user_data');
        if(empty($user_data)) return json(['error'=>'请先登录']);
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);
        //验证器方式
        //bid应该做个下拉选择
        $task = new Task;
        $data = input('post.');
        $uid = Session::get('user_data.id');
        $data['uid'] = $uid;
        if($task->allowField(true)->save($data)){
            $this->success('提交成功');
            return json('记录添加成功',200);
        }else{
            //return $task->getError();
            return json('记录添加失败',404);
        }
    }

    public function del($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $task = Task::get($id);
        if($task->delete()){
            return json('记录删除成功',200);
        }else{
            return json('记录删除失败',404);
        }

    }

    public function update($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $data = input('post.');
        //print_r($data);
        $taskArr['name']    = $data['name'];
        $taskArr['client']  = $data['client'];
        $taskArr['telt']     = $data['telt'];
        $taskArr['content'] = $data['content'];
        $taskArr['money']   = $data['money'];
        $taskArr['status']  = $data['status'];
        if(Task::update($taskArr,['id'=>$id])){
            return json('记录更新成功',200);
        }else{
            //$this->error('id为'.$id.'的记录更新失败');
            return json('记录更新失败',404);
        }
    }
}