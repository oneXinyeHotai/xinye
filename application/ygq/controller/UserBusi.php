<?php


namespace app\ygq\controller;
use Symfony\Component\Yaml\Tests\B;
use think\Image;
use think\Session;
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
        //主要业务就5个，没有必要分页了
        $list = Business::all();
        if(empty($list)){
            return json('当前页面没有数据');
        }else{
            return json_encode($list);
        }

    }

    //添加业务种类
    public function add(){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $business = new Business();
        $data = input('post.');
        $result1 = Validate::is($data['title'],'require');
        $result2 = Validate::is($data['explains'],'require');
        if(!$result1 || !$result2){
            return json(['error'=>'图片或内容不能为空'],404);
        }
        //单独处理上传图片
        $request = Request::instance();
        $file = $request->file('image');
        $check = $this->validate(['image'=>$file],['image'=>'require|image'],
            ['image.require'=>'请选择上传文件','image.image'=>'非法图像文件']);
        if(true !== $check){
            $this->error($check);
        }

        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        $image->save($path);
        $data['image'] = $path;
        if($business->allowField(true)->save($data)){
            return json(['success'=>'记录添加成功'],200);
        }else{
            return json(['error'=>'记录添加失败'],404);
        }
    }

    //业务种类删
    public function del($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);

        $business = Business::get($id);
        if($business->delete()){
            return json(['success'=>'记录删除成功'],200);
        }else{
            return json(['error'=>'记录删除失败'],404);
        }
    }

    //更新
    public function update($id){
        $limit = Session::get('user_data.type');
        if( $limit != 1 && $limit != 2) return json(['error'=>'非法操作']);
        $data = input('post.');

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

        $image = Image::open($file);
        $saveName = $request->time().'.png';
        $path = ROOT_PATH.'public'.DS.'static'.DS.'picture'.DS.'business'.DS.$saveName;
        $image->save($path);

        $businessArr['image'] = $path;
        if(Business::update($businessArr,['id'=>$id])){
            return json(['success'=>'记录更新成功'],200);
        }else{
            return json(['error'=>'记录更新失败'],404);
        }
    }
}
//余广谦写