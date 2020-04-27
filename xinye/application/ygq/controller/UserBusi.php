<?php


namespace app\ygq\controller;
use Symfony\Component\Yaml\Tests\B;
use think\Image;
use think\Validate;
use app\ygq\validate\Question as Que;
use think\Controller;
use think\Request;
use think\Db;
use app\ygq\model\Business;
use traits\think\Instance;

//问题反馈类
class UserBusi extends Controller
{
    //后台展示问题反馈
    public function index(){
        //$question = new QuestionRe;
        //查询一条记录，需要的话就传入id
        //$question = QuestionRe::getById('1');
        //print_r($question);
        //echo $question['tel']."<br>";
        //echo $question['content'];

//        $list = Business::all();
//        foreach ($list as $l){
//            echo $l->title."<br>";
//            echo $l->image."<br>";
//            echo $l->explains."<br>";
//            echo "<br>"."-------------"."<br>";
//        }

        $data = Db::table('business')
            ->where('id','>=',1)
            ->select();
        //print_r($data);
        return json($data);
    }

    //添加业务种类
    public function add(){
        //输入与验证方式存入数据库
//        $business = new Business();
//        if($business->allowField(true)->validate(true)->save(input('post.'))){
//            return '添加成功';
//        }else{
//            return $business->getError();
//        }

        $business = new Business();
        $data = input('post.');
        //$data中没有image
        //print_r($data);
        $result1 = Validate::is($data['title'],'require');
        $result2 = Validate::is($data['explains'],'require');
        if(!$result1 || !$result2){
            return json(['error'=>'图片或内容不能为空'],404);
        }

        //单独处理上传图片
        $request = Request::instance();

        $file = $request->file('image');
//        print_r($file);
        //两种判断方法

//        $check = Validate::is($file,'image');
//        if( false == $check){
//            $this->error('请选择正确上传文件');
//        }

        $check = $this->validate(['image'=>$file],['image'=>'require|image'],
            ['image.require'=>'请选择上传文件','image.image'=>'非法图像文件']);
        if(true !== $check){
            $this->error($check);
        }

        //移动目录
        //$info = $file->rule('date')->move(ROOT_PATH.'public'.DS.'static'.DS.'picture');
//        if($info){
//            echo $info->getSaveName();
//        }
//        if(!$info){
//            $this->error('文件上传错误');
//        }
        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        $image->save($path);




        //数据保存
        //$path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.$info->getSaveName();
        //$path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        //echo $path;
        $data['image'] = $path;
        if($business->allowField(true)->save($data)){
            $this->success('提交成功');
        }else{
            return json(['error'=>'记录添加失败'],404);
        }



        //存入数据库中
        //查询业务种类表最后一条记录，然后插入image路径
//        $result = Db::name('business')
//            ->order('id desc')
//            ->limit(1)
//            ->select();
//        $id = $result[0]['id'];
//        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture';
//        $buisArr['image'] = $path;
//        Business::update($buisArr,['id'=>$id]);



//        $requset = Request::instance();
//        $file = $requset->file('image');
//        $saveName = $requset->time().'.png';
//        $image = Image::open($file);
//        $path = ROOT_PATH . 'public'.'DS'.'uploads'.'DS' . $saveName;
//        $image->save($path);
//        $img = new Business;
//        $img->allowField(true)->save($path);

    }

    //业务种类删
    public function del($id){
        $business = Business::get($id);
        if($business->delete()){
            return $this->success('id为'.$id.'的数据删除成功');
        }else{
            return json(['error'=>'id为'.$id.'的记录删除失败'],404);
        }

    }

    //更新
    public function update($id){
        //$busi = Business::get($id);
        $data = input('post.');

//        $busi->explains = $data['explains'];
//        $busi->title    = $data['title'];

        $businessArr['explains'] = $data['explains'];
        $businessArr['title']    = $data['title'];

        //单独处理上传图片
        $request = Request::instance();
        $file = $request->file('image');

        $check = $this->validate(['image'=>$file],['image'=>'require|image'],
            ['image.require'=>'请选择上传文件','image.image'=>'非法图像文件']);
        if(true !== $check){
            $this->error($check);
        }

        //移动目录
//        $info = $file->rule('date')->move(ROOT_PATH.'public'.DS.'static'.DS.'picture');
//        if(!$info){
//            $this->error('文件上传错误');
//        }

        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        $image->save($path);

        //数据保存
        //$path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.$info->getSaveName();

        $businessArr['image'] = $path;
        if(Business::update($businessArr,['id'=>$id])){
            return $this->success('id为'.$id.'的记录更新成功');
        }else{
            //return $this->error('id为'.$id.'的记录更新失败');
            return json(['error'=>'id为'.$id.'的记录更新失败'],404);
        }


//        if(false !== $busi->save()){
//            return '问题反馈更新成功';
//        }else{
//            $busi->getError();
//        }
    }

    public function test(){
        $id = 1;
        //echo ROOT_PATH.'public'.DS.'static'.DS.'picture';
        //return json('id为{$id}的记录更新失败');
        return json(['error'=>'id为'.$id.'的记录更新失败'],404);

        $time = strtotime("now");
        echo $time."<br>";
        print_r(date("Y-m-d H:i",$time));


    }



}
//余广谦写