<?php


namespace app\ygq\controller;
use think\Db;
use app\ygq\model\Task;
use think\Controller;
use think\Session;

class UserTask extends Controller
{
    public function index(){
        //$list = Task::all();
//        foreach ($list as $l){
//            echo $l->title."<br>";
//            echo $l->image."<br>";
//            echo $l->explains."<br>";
//            echo "<br>"."-------------"."<br>";
//        }

        $data = Db::table('task')
            ->where('id','>=',1)
            ->select();
        //print_r($data);
        return json($data);
    }
    public function add(){
        //验证器方式
        //bid应该做个下拉选择
        $task = new task;
        if($task->allowField(true)->validate(true)->save(input('post.'))){
            //从session中获取uid值
            //$uid = Session::get('user_data.id');
            $uid = 9;

            //查询订单表最后一条记录，然后插入uid
            $result = Db::name('task')
                ->order('id desc')
                ->limit(1)
                ->select();

//            print_r($result);

            $id = $result[0]['id'];
//            $task = Task::get($id);
//            $task->uid = $uid;
//            $task->save();
            //直接更新
            $taskArr['uid'] = $uid;
            Task::update($taskArr,['id'=>$id]);

            return $this->success('订单添加成功');
        }else{
            //return $task->getError();
            return json('记录添加失败',404);
        }

        //从session中获取uid值
        //$uid = Session::get('user_data.id');
//        $uid = 5;
//        //查询订单表最后一条记录，然后插入uid
//        $result = Db::name('task')
//            ->order('id desc')
//            ->limit(1)
//            ->select();
//
//        print_r($result);
//
//
//        $id = $result[0]['id'];
//        $task = Task::get($id);
//        $task->uid = $uid;
//        $task->save();
    }

    public function del($id){
        $task = Task::get($id);
        if($task->delete()){
            return $this->success('id为'.$id.'的数据删除成功');
        }else{
            return json('id为'.$id.'的记录删除失败',404);
        }

    }

    public function update($id){

        $data = input('post.');
        //print_r($data);
        $taskArr['name']    = $data['name'];
        $taskArr['client']  = $data['client'];
        $taskArr['tel']     = $data['tel'];
        $taskArr['content'] = $data['content'];
        $taskArr['money']   = $data['money'];
        $taskArr['status']  = $data['status'];
        if(Task::update($taskArr,['id'=>$id])){
            $this->success('id为'.$id.'的记录更新成功');
        }else{
            //$this->error('id为'.$id.'的记录更新失败');
            return json('id为'.$id.'的记录更新失败',404);
        }
    }
}