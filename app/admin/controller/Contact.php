<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;



class Contact  extends BaseController
{   

    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];


    public function list(){
        $status = Request::param('status');

        $list = Db::table('tp_contact')->where("status", "=", $status)->order('id', 'desc')->paginate([
            'list_rows' => Config::get('app.admin_page_num'),
            'path'  => "/".Config::get("app.admin_path").'/contact/list',
            'query' => Request::only(['status'])
        ]);

        View::assign('username',Session::get('username'));
        View::assign('list',$list);
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('admin_url',Config::get('app.admin_url'));
        View::assign('admin_path',Config::get('app.admin_path'));

        return View::fetch('/Contact/contact_list');       

    }


    
    public function read(){
        if(Db::table('tp_contact')->strict(false)->where('status',"=",0)->update(["status" => 1])){
            $this->success("Status set success.",$_SERVER["HTTP_REFERER"], 1);
        }else{
            $this->error("Status set fail.",$_SERVER["HTTP_REFERER"], 2);
        }    

    }

    public function edit(){

        $id = Request::param('id'); 
        $data = Db::table('tp_contact')->where('id','=',$id)->select();


        View::assign('username',Session::get('username'));
        View::assign('data',$data);
        View::assign('id',$id);
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/Contact/contact_edit');      

    }

    public function editPost(){
        $id = Request::param("id");
        $data = Request::post();
    

        

        if(Db::table('tp_contact')->strict(false)->where('id',"=",$id)->update($data)){
            $this->success("Data edit success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Data edit fail.",$_SERVER["HTTP_REFERER"],2);
        }
    }

    
    public function delete(){
        $id = Request::param("id");


        if(Db::table('tp_contact')->where('id',$id)->delete()){
            $this->success("Data delete success.","/".Config::get('app.admin_path')."/contact/list",1);
        }else{
            $this->error("Data delete fail.","/".Config::get('app.admin_path')."/contact/list",2);
        }
    }

}
