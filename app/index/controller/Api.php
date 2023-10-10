<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Cache;


class Api extends BaseController
{

    public function get_short_str(){
        if(Request::isPost()){
            $api_token = Request::param("api_token") ?? "";
            if($api_token == Config::get("app.api_token")){
                
                
                //short_str length 获取长度
                $length = Request::param("length") ?? 0;
                if($length == 0){
                    $length = 8;
                }

                $otherclass= new Otherclass($this->app);
                $short_str = $otherclass->get_short_str($length);


                $status_code = 200;
                $message = "success";
                $data = ["short_str" => $short_str];

            }else{
                $status_code = 300;
                $message = "Api token error";
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
            abort(404,"Only allow post method!");
        }
    }

    
    public function get_file_status(){
        if(Request::isPost()){
            $api_token = Request::param("api_token") ?? "";
            if($api_token == Config::get("app.api_token")){
                $short_str = Request::param("short_str") ?? "";
                
                if(Cache::has(Config::get("app.redis_prefix").$short_str)){
                    $status_code = 200;
                    $message = "success";
                    $data = [];
                }else{
                    $status_code = 201;
                    $message = "Data length less 1";
                    $data = [];
                }

            }else{
                $status_code = 300;
                $message = "Api token error";
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
            abort(404,"Only allow post method!");
        }
    }


}