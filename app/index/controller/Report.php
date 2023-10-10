<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;


class Report  extends BaseController
{

    public function index(){

        //当请求模式为post时
        if(Request::isPost()){
            $otherclass = new Otherclass($this->app);
            $data = Request::post();


            // ---------------  时间戳验证码验证 begin  ------------------------------
            $hash_str = Request::param("hash_str") ? Request::param("hash_str") : "";
            if(!Cache::has(Config::get("app.redis_prefix")."report_hash_str".$hash_str) || $hash_str == ""){
                $this->error("Please try again.123",'/',5);
            }

            // ---------------  时间戳验证码验证 end  ------------------------------

            //数字验证码
            $cptc = $data["cptc"];
            $cptc_number_1 = $data["cptc_number_1"];
            $cptc_number_2 = $data["cptc_number_2"];

            $url = $data["url"];
            $comment = $data["comment"];
            $email = $data["email"];


            $remote_ip = $otherclass->get_user_ip();
            $user_county = $otherclass->get_country($remote_ip);

            //验证通过就开始插入数据库
            if($cptc_number_1 + $cptc_number_2 == $cptc){
                $insert_data = [
                    "url" => $url,
                    "remote_ip" => $remote_ip,
                    'country' => $user_county,
                    "comment" => $comment,
                    "email" => $email,
                    "timestamp" => time()
                ];

                if(Db::table("tp_report")->strict(false)->insert($insert_data)){
                    $this->success("Report successful",'/report',2);
                }else{
                    $this->error("Unknown error",'/report',3);
                }

            }else{
                $this->error("Captcha error",'/report',3);
            }

        }else{
            $otherclass = new Otherclass($this->app);
            $host_data = $otherclass->get_host();
    


            //随机验证码,验证码的核心逻辑就是客户端与服务端的md5(ua+"-"+timestamp)进行验证,index首页使用timestamp进行伪装
            //verification
            $index_timestamp = time();
            $hash_str = md5(Request::header('user-agent')."-".$index_timestamp);
            Cache::set(Config::get("app.redis_prefix")."report_hash_str".$hash_str, 1, 60*30);
            View::assign("index_timestamp",$index_timestamp);

            // hash验证 end

    
            $title = "Report malicious files link URL - ".$host_data[0]['site_name'];
            $keywords = "Report malicious files link URL ";
            $description = "Use the form to report malicious files link url to our team.";
            
    
            $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
    
            $cptc_number_1 = mt_rand(0,9);
            $cptc_number_2 = mt_rand(0,9);
            View::assign("cptc_number_1",$cptc_number_1);
            View::assign("cptc_number_2",$cptc_number_2);


            View::assign("domain_url", $domain_url);
            View::assign("title", $title);
            View::assign("keywords", $keywords);
            View::assign("description", $description);
            View::assign("year_num", Config::get("app.year_num"));
            
        
            return View::fetch("/Report/report");
        }



    }



    public function page(){
        if(Request::ispost()){

            $otherclass = new Otherclass($this->app);
            $data = Request::post();

            $short_str = $data["report_short_str"];
            $reason = $data["reason"] ?? "";
            $comment = $data["comment"] ?? "";
            $email = "";


            $short_str = substr($short_str,0,10);
            $reason = substr($reason,0,99);
            $comment = substr($comment,0,999);



            $user_ip = $otherclass->get_user_ip();
            $user_agent = substr($otherclass->get_user_agent(),0,254);
            $user_county = $otherclass->get_country($user_ip);

            //单个用户同一个short_str最大只能提交2次，避免后台收到太多的垃圾数据
            $user_key = Config::get("app.redis_prefix")."report_page_".$short_str."_".md5($user_agent.$user_ip);

            $over_limit_num = 0;//是否超限，1为超限，0为未超
            if(!Cache::has($user_key)){
                Cache::set($user_key, 1, 60*30);

            }else{
                if(Cache::get($user_key) > 1){   //当大于1时则说明超限
                    $over_limit_num = 1;
                }else{
                    Cache::inc($user_key); //自增1
                } 
            }

            if($over_limit_num == 0){
                $insert_data = [
                    "url" => Config::get("app.admin_url").$short_str,
                    "reason" => $reason,
                    "remote_ip" => $user_ip,
                    'country' => $user_county,
                    "comment" => $comment,
                    "email" => $email,
                    "user_agent" => $user_agent,
                    "timestamp" => time()
                ];
    
                if(Db::table("tp_report")->strict(false)->insert($insert_data)){
                   
                    $status_code = 200;
                    $message = "Report successful.";
                    $data = [];
    
                }else{
                    $status_code = 201;
                    $message = "Report failed.";
                    $data = [];
                }
    
            }else{
                $status_code = 201;
                $message = "You have already reported.";
                $data = [];
            }


            //返回数据
            $return_data = [
                "status" => $status_code,
                "message" => $message,
                "data" => $data
            ];

            return (string)json_encode($return_data);

        }else{
            abort(404,"Only allow post.");
        }
       
    }
}
