<?php


namespace app\ygq\controller;
use think\Db;
use app\ygq\model\Activity;
use think\Controller;
use think\Session;
use think\Validate;
use think\Image;
use think\Request;

class UserAct extends Controller
{
    public function index(){
        //分页
        $act = new Activity();
        //获取当前页面数
        $page  = empty($_GET['page']) ? 1 : $_GET['page'];
        $pages = $act->Pages();
        if($pages == 0){
            return json(["return"=>'当前页面无数据！']);
        }
        if($page>$pages || $page<=0){
            return json(["return"=>'页码不能超过总页数！']);
        }
        $message = $act->show($page);
        return json(['message'=>$message,'pages'=>$pages]);
    }
    public function add(){
        $user_data=Session::get('user_data');
        if(empty($user_data)) return json(['error'=>'请先登录']);

        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        //验证器方式
        $act= new Activity;
        //$act->allowField('true')->validate('true')->save('post.');
        $data = input('post.');
        //print_r($data);
        $data['time']=date('Y-m-d H:i:s');
        $result1 = Validate::is($data['title'],'require');
        $result2 = Validate::is($data['content'],'require');
        $result3 = Validate::is($data['time'],'date');
        if(!$result1 || !$result2){
            return json(['error'=>'标题或内容不能为空'],200);
        }else if(!$result3){
            return json(['error'=>'日期格式不正确'],404);
        }
        //图片
        $request = Request::instance();
        $file = $request->file('image');

        $check = $this->validate(['image'=>$file],['image'=>'require|image'],
            ['image.require'=>'请选择上传文件','image.image'=>'非法图像文件']);
        if(true !== $check){
            return json(['error'=>'图片上传失败']);
        }

        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'activity'.DS.$saveName;
        $image->save($path);

        $data['image'] = $path;
        $uid = Session::get('user_data.id');
        $uid = 5;
        $data['uid'] = $uid;

        if($act->allowField(true)->save($data)){
            return json(['success'=>'记录添加成功'],200);
        }else{
            return json(['error'=>'记录添加失败'],404);
        }
    }

    public function del(){
        $id=input('get.id');
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $act = Activity::get($id);
        if($act->delete()){
            return json(['success'=>'记录删除成功'],200);
        }else{
            return json(['error'=>'记录删除失败'],404);
        }

    }

    public function update($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $data = input('post.');
        $actArr['title']   = $data['title'];
        $actArr['content'] = $data['content'];
        //$actArr['time']    = $data['time'];

        $result1 = Validate::is($data['title'],'require');
        $result2 = Validate::is($data['content'],'require');
       // $result3 = Validate::is($data['time'],'date');
        if(!$result1 || !$result2){
            return json(['error'=>'标题或内容不能为空'],404);
        }

        //单独处理上传图片
        $request = Request::instance();
        $file = $request->file('image');
        $check = $this->validate(['image'=>$file],['image'=>'require|image'],
            ['image.require'=>'请选择上传文件','image.image'=>'非法图像文件']);
        if(true !== $check){
            return json(['error'=>'图像上传失败']);
        }
        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        $image->save($path);

        //数据保存
        $actArr['image'] = $path;
        if(Activity::update($actArr,['id'=>$id])){
            return json(['success'=>'记录更新失败'],200);
        }else{
            return json(['error'=>'记录更新失败'],404);
        }
    }
}