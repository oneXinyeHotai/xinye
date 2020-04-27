<?php


namespace app\ygq\controller;
use app\ygq\model\Activity;
use think\Controller;
use think\Request;
use think\Db;
use app\ygq\model\QuestionRe;
//问题反馈类
class UserQues extends Controller
{
    //后台展示问题反馈
    public function index(){
        //$question = new QuestionRe;
        //查询一条记录，需要的话就传入id
        //$question = QuestionRe::getById('1');
        //print_r($question);
        //echo $question['tel']."<br>";
        //echo $question['content'];

        $list = QuestionRe::all();
        foreach ($list as $l){
            //echo $l->tel;
            //echo "<br>";
            //echo $l->content;
            //echo "<br>"."-------------"."<br>";
        }

        $data = Db::table('question_re')
            ->where('id','>=',1)
            ->select();
        //print_r($data);
        return json($data);
    }

    //获取问题并存入数据库
    public function add(){
        //判断用户是否登录（session中存放）
        //$uid = $_SESSION['uid'];

        //$request = Request::instance();
        //print_r($request->param());
        //$data = $request->param();
        /*if(empty($data['content']) || empty($data['tel'])){
            return '内容或电话不能为空';
            die();
        }
        if (!preg_match('/^1[34578]\d{9}$/', $data['tel'])) {
            return '手机号码格式不正确';
            die();
        }*/
        //存入数据空
        //Db::name('question_re')
        //    ->insert( ['tel' => $data['tel'], 'content' => $data['content'], 'uid' => 1]);

        //$db = db('question_re');
        // 插入记录
        //$db->insert(['tel' => $data['tel'], 'content' => $data['content'], 'uid' => 1]);
        //dump(input('post.name'));

        //模型方法存入数据中
//        $question = new QuestionRe;
//        $question->tel = $data['tel'];
//        $question->content = $data['content'];
//        $question->uid = 1;
//        $question->save();

        //输入与验证方式存入数据库
        $question = new QuestionRe();
        if($question->allowField(true)->validate(true)->save(input('post.'))){
            //获取uid
            //$uid = Session::get('user_data.id');
            $uid = 9;
            //查询订单表最后一条记录，然后插入uid
            $result = Db::name('question_re')
                ->order('id desc')
                ->limit(1)
                ->select();
            $id = $result[0]['id'];
            $quesArr['uid'] = $uid;
            QuestionRe::update($quesArr,['id'=>$id]);

            return $this->success('记录添加成功');
        }else{
            //return $question->getError();
            return json(['error'=>'记录添加失败'],404);
        }
    }

    //问题反馈删
    public function del($id){
        $ques = QuestionRe::get($id);
        if($ques->delete()){
            return $this->success('id为'.$id.'的数据删除成功');
        }else{
            return json(['error'=>'id为'.$id.'的记录删除失败'],404);
        }

    }

    public function update(){

    }



}
//余广谦写