<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;


class Terms extends BaseController
{

    public function index(){


        
        $otherclass = new Otherclass($this->app);
        $host_data = $otherclass->get_host();



        $title = "Terms of use - ".$host_data[0]['site_name'];
        $keywords = "terms of use";
        $description = "terms of use";
        
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";




        View::assign("domain_url", $domain_url);
        View::assign("title", $title);
        View::assign("keywords", $keywords);
        View::assign("description", $description);
        View::assign("year_num", Config::get("app.year_num"));
    
        return View::fetch("/Terms/terms");
    



    }
}
