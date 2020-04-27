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
        $data = Db::table('Activity')
            ->where('id','>=',1)
            ->select();
        //print_r($data);
        return json($data);
    }
    public function add(){
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
            return json(['error'=>'标题或内容不能为空'],404);
        }else if(!$result3){
            return json(['error'=>'日期格式不正确'],404);
        }

        //日期
        //$act->time = $data['time'];

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

        //$uid = Session::get('user_data.id');
        $uid = 5;
        $data['uid'] = $uid;

        if($act->allowField(true)->save($data)){
            $this->success('提交成功');
        }else{
            return json(['error'=>'记录添加失败'],404);
        }
    }

    public function del($id){
        $act = Activity::get($id);
        if($act->delete()){
            return $this->success('id为'.$id.'的数据删除成功');
        }else{
            return json('id为'.$id.'的记录删除失败',404);
        }

    }

    public function update($id){
        $data = input('post.');
        $actArr['title']   = $data['title'];
        $actArr['content'] = $data['content'];
        $actArr['time']    = $data['time'];

        $result1 = Validate::is($data['title'],'require');
        $result2 = Validate::is($data['content'],'require');
        $result3 = Validate::is($data['time'],'date');
        if(!$result1 || !$result2){
            return json(['error'=>'标题或内容不能为空'],404);
        }else if(!$result3){
            return json(['error'=>'日期格式不正确'],404);
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
            return $this->success('id为'.$id.'的记录更新成功');
        }else{
            return json(['error'=>'id为'.$id.'的记录更新失败'],404);
        }
    }
}