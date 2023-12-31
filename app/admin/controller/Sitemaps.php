<?php
namespace app\admin\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;



class Sitemaps  extends BaseController
{   
    
    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];



    public function index(){
    

        $domain_data = Db::table("tp_domain")->order("id","desc")->where("sitemap","1")->select();
        $short_str_data = Db::table("tp_file")->where("is_404","0")->where("delete_status","0")->order("id","desc")->limit(Config::get("app.sitemaps_url_num"))->select();


        for($i=0; $i<count($domain_data); $i++){
            $domain_url = $domain_data[$i]['http_prefix'].$domain_data[$i]['domain']."/";
            

            //echo __DIR__."/../../../public/".$domain_data[$i]['domain_url']."_sitemaps.txt";
            //读写文件，文件名为：short.by_sitemaps.txt
            $file = fopen(__DIR__."/../../../public/sitemaps/".$domain_data[$i]['domain'].".txt","w+");
            for($x=0; $x<count($short_str_data); $x++){
                fwrite($file, $domain_url.$short_str_data[$x]['short_str']."\n");
            }
            fclose($file);
        }

        $this->success("Sitemap create success.", "/".Config::get("app.admin_path")."/click/list", 1);

    }

}
