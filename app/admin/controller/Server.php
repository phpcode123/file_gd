<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;
use think\facade\Config;
use think\facade\Cache;


class Server  extends BaseController
{
    
    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];



    public function add(){
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/Server/server_add');
    }


    public function addpost(){
        $data = Request::post();
        if(Db::table('tp_server')->strict(false)->data($data)->insert()){
            $this->success("Data add success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Data add fail.",$_SERVER["HTTP_REFERER"],2);
        }
       
    }
    
    public function list(){

        $list = Db::table('tp_server')->order('id','desc')->paginate([
            'list_rows' => Config::get('app.admin_page_num'),
            'path'     => "/".Config::get("app.admin_path").'/server/list',
        ]);


        //获取command命令行FileServer的状态
        $command_redis_key = Config::get("app.redis_prefix")."command_file_server";
        if(Cache::has($command_redis_key)){
            $command_status = date("Y-m-d H:i:s", Cache::has($command_redis_key));
        }else{
            $command_status = 0;
        }
        View::assign("command_status",$command_status);

        
        View::assign('list',$list);
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/Server/server_list');
    }

    public function edit(){
        $id = Request::param('id');
        $data = Db::table('tp_server')->where('id',$id)->select();
        View::assign('data',$data);
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/Server/server_edit');
    }

    public function editpost(){
        $data = Request::param();
        if(Db::table('tp_server')->strict(false)->where('id',$data['id'])->update($data)){
            $this->success("Data edit success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Data edit fail.",$_SERVER["HTTP_REFERER"],2);
        }
    }
    
    public function delete(){
        $id = Request::param('id');
        
        if(Db::table('tp_server')->where('id',$id)->delete()){
            $this->success("Data delete success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Data delete fail.",$_SERVER["HTTP_REFERER"],2);
        }
    }
}
