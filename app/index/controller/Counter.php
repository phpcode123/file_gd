<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;


class Counter extends BaseController
{

    public function index(){


        //当请求模式为post时
        if(Request::isPost()){
            $otherclass = new Otherclass($this->app);
        

            // ---------------  时间戳验证码验证 begin  ------------------------------
            $hash_str = Request::param("hash_str") ? Request::param("hash_str") : "";
            if(!Cache::has(Config::get("app.redis_prefix")."counter_hash_str".$hash_str) || $hash_str == ""){
                $this->error("Please try again.",'/',5);
            }

            // ---------------  时间戳验证码验证 end  ------------------------------


            $remote_ip = $otherclass->get_user_ip();
            $user_country = $otherclass->get_country($remote_ip);
            $host_data = $otherclass->get_host();

            

            $url = Request::param("url") ? Request::param("url") : "";
           
    
            //当url为空时就直接展示模板，否则就启用查询url
            if($url == ""){
                $this->error("URL error, Please input your file URL.",'/counter',3);
            }
            //当url不为空时
            //将url开始分割，将short_str分割出来
            $url_array = preg_split("/\//", $url);

            //var_dump($url_array);
    
            $short_str = $url_array[count($url_array)-1];

            //echo $short_str;
            
            //当short_str长度为0时，说明short_str传递参数有问题，提示错误
            if(strlen($short_str)==0){
                $this->error("URL error",'/counter',3);
            }
            
     

            $data_count_num = 0;
            if(strlen($short_str) == Config::get("app.archive_str_length")){
                $archive_data = Db::table("tp_file_archive")->where("short_str",$short_str)->select();
                if(count($archive_data) == 0){
                    $data_count_num = 1;
                }else{
                    $data = Db::table("tp_file")->where("id","in",$archive_data[0]['file_id'])->select();
                }
            }else{
                $data = Db::table("tp_file")->where("short_str",$short_str)->select();
                if(count($data)==0){
                    $data_count_num = 1;
                }
                
            }


            //说明数据有错误，直接展示404
            if($data_count_num == 1){
                $this->error("URL error",'/counter',3);
            }


            //记录日志
            try{
                $this->log($remote_ip." - ".$user_country." - ".$url, "counter_file.log");
            }catch(\Exception $e){
                //do something
            }
    
            $title = "Total URL Clicks - ".$host_data[0]['site_name'];
            $keywords = "Total URL Clicks";
            $description = "The number of clicks that your file URL received.";
            $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
    
            View::assign("domain_url", $domain_url);
            View::assign("title", $title);
            View::assign("keywords", $keywords);
            View::assign("description", $description);
            View::assign("data", $data);
            View::assign("year_num", Config::get("app.year_num"));
            
    
            return View::fetch("/Counter/counter_post");



        }else{
            $otherclass = new Otherclass($this->app);
            $host_data = $otherclass->get_host(Request::host());
    
    

            //随机验证码,验证码的核心逻辑就是客户端与服务端的md5(ua+"-"+timestamp)进行验证,index首页使用timestamp进行伪装
            //verification
            $index_timestamp = time();
            $hash_str = md5(Request::header('user-agent')."-".$index_timestamp);
            Cache::set(Config::get("app.redis_prefix")."counter_hash_str".$hash_str, 1, 60*30);
            View::assign("index_timestamp",$index_timestamp);
            // hash验证 end



            $title = "URL Click Counter";
            $keywords = "URL Click Counter,URL Click,counter";
            $description = "Click counter shows in real time how many clicks your shortened URL received so far.";
    
            $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
    
    


            View::assign("domain_url", $domain_url);
            View::assign("title", $title);
            View::assign("keywords", $keywords);
            View::assign("description", $description);
            View::assign("year_num", Config::get("app.year_num"));
        
            return View::fetch("/Counter/counter");
        }



    }
}
