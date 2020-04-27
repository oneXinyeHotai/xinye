<?php
namespace app\qk\controller;

use app\qk\model\Advertise;
use app\common\model\User;
use think\Controller;
use think\Session;
use think\Request;

class AdvertiseCon extends Controller
{
	//显示广告
	public function index(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$advertise = new Advertise;
		//获取当前页面数
      $page=$request->get('page');
		$page=empty($page)?1:$request->get('page');
		$pages=$advertise->Pages();
		if($page>$pages||$page<=0){
		    return json(["return"=>'页码不能超过总页数！']);
		}
		$advertise = $advertise->alladve($page);
		return json(['advertise'=>$advertise,'pages'=>$pages]);
	}
	
	//添加广告
	public function add(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$data = input('post.');
		// 数据验证
		$result	= $this->validate($data,'Advertise');
		$file=$request->file('file');
		//调用控制器中的文件进行验证
		$result = $this->validate(['file' => $file],
		    ['file' => 'require|image|fileExt:png,jpg,gif'],
		    ['file.require' => '请选择上传文件','file.image'=>'必须是图片！','file.fileExt'=>'文件格式不对']);
		//移动到框架图片目录下
		$info=$file->move(ROOT_PATH.'public' . DS . 'static' . DS . 'picture' . DS . 'advertise',date('Ymd-His'));
     	$name=$info->getSaveName();
		$data['link'] = DS .  'static' . DS . 'picture' . DS . 'advertise' . DS .$name ;
		$advertise = new Advertise;
		if($advertise -> allowField(true)->save($data))
		{
			return json('广告新增成功!');
		}
		else
		{
			json(['error' => "广告增加失败！"]);
		}
	}
	
	//读取广告
	public function read(Request $request)
	{
		$id=$request->get('id');
		$advertise = Advertise::get($id);
		if($advertise)
		{
			return	json_encode($advertise);
		}
		else
		{
			return json(['error' => "广告不存在！"]);
		}
	}
	
	//修改广告
	public function update(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
		$id=$request->get('id');
		$advertise = Advertise::get($id);
		// 数据验证
		$result	= $this->validate($advertise,'Advertise');
		if(!$advertise)
		{
			return json(['error' => "广告不存在！"]);
		}
		$advertise -> picture = $_POST['picture'];
		//判断用户是否修改广告路径
		if($file=$request->file('file'))
		{
			//调用控制器中的文件进行验证
			$result = $this->validate(['file' => $file],
				['file' => 'require|image|fileExt:png,jpg,gif'],
				['file.require' => '请选择上传文件','file.image'=>'必须是图片哦','file.fileExt'=>'文件格式不对']);
			//移动到框架图片目录下
			$info = $file->move(ROOT_PATH.'public' . DS . 'static' . DS . 'picture' . DS . 'advertise',date('Ymd-His'));
			if(!$info)
			{
				return json('文件上传失败!');
			}
			$advertise -> link = DS . 'static' . DS . 'picture' . DS . 'advertise' . DS . $info->getSaveName();
		}
		if	(false != $advertise ->save())
		{
			return	json(['error' => "修改广告成功！"]);
		}
		else
		{
			return json(['error' => "广告修改失败！"]);
		}
	}
	
	//删除广告
	public function delete(Request $request)
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
		//$this->user_yz(); //有问题，引用成功,但是无法实现？
      	$id=$request->get('id');
		$advertise = Advertise::get($id);
		if($advertise)
		{
          
			$advertise ->delete();
           return json('广告删除成功!');
		}
		else 
		{
			return json(['error' => "广告不存在！"]);
		}
	}
	
	//前台显示广告图片
	public function show_adve()
	{
		$advertise = new Advertise;
		$advertise = $advertise->alladve_l();
		if($advertise)
		{
			return	json_encode($advertise);
		}
		else 
		{
			return json(['error' => "无广告图片！"]);
		}
	}
	
	/* //用户权限验证 //有问题
	public function user_yz()
	{
		if(!Session::get('user_data'))
		{
			return json('请登陆！');
		}
		$type = Session::get('user_data.type');
		if($type!=1 && $type!=2)
		{
			return json('你不是管理员，无权访问！');
		}
	} */
}