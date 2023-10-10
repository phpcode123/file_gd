<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Request;
use think\facade\Config;
use think\facade\Db;
use think\facade\Cache;
use GeoIp2\Database\Reader;


class Otherclass  extends BaseController
{


    //返回随机字符串
    public static function get_short_str($length=6) {

        if($length != 6){
            $str_length = $length;
        }else{
            $str_length = Config::get("app.short_str_length");
        }



        $short_str = "";
        
        //随机生成32-50位长度的字符串，然后从0-6开始截取字符串去数据库中查询，如果能匹配到则自动增加1，直到匹配不到数据为止。
        //随机字符串不要太长，会占用cpu性能
        $num = mt_rand(32,32);

        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $rand_str = ''; 
        for ($i = 0; $i < $num; $i++) { 
            $index = mt_rand(0, strlen($characters) - 1); 
            $rand_str .= $characters[$index]; 
        }


        $timestamp = time();
        //将字符串使用base64加密, 替换掉base64加密后面可能产生的==号
        $rand_str = base64_encode($rand_str.$timestamp);
        $rand_str = preg_replace("#=#i", "", $rand_str);


        $start_num = 0;
        while(true){
            //截取指定长度的字符串
            $short_str = substr($rand_str, $start_num, $str_length);
            $redis_key = Config::get("app.redis_prefix").$short_str;

            //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环  
            if(!Cache::has($redis_key)){
                Cache::set($redis_key, 1, 43200); //60*60*12 设置12小时过期，避免生成大量的数据造成消耗redis资源
                break;
            }

            $start_num += 1;
            //当当前循环超过指定次数时会导致$start_num超过一个长度值
            if($start_num > (strlen($rand_str) - $str_length-1)){
                
                
                //------------    如果所有的数据都匹配完了还是没有匹配到short_str，就将长度+1   begin ------------

                //截取指定长度的字符串
                $short_str = substr($rand_str, $start_num, $str_length+1);
                $redis_key = Config::get("app.redis_prefix").$short_str;

                //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环  
                if(!Cache::has($redis_key)){
                    Cache::set($redis_key, 1, 43200); //60*60*12 设置12小时过期，避免生成大量的数据造成消耗redis资源
                    break;
                }
                
                //------------    如果所有的数据都匹配完了还是没有匹配到short_str，就将长度+1   end ------------

                //如果还是超出指定长度还没有匹配到数据，就将shortUrtStr设置为指定值
                if($start_num > strlen($rand_str) * 2){

                    //截取指定长度的字符串
                    $short_str = "errorShortStr".$timestamp."-".time();
                    $redis_key = Config::get("app.redis_prefix").$short_str;

                    //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环  
                    if(!Cache::has($redis_key)){
                        Cache::set($redis_key, 1, 43200); //60*60*12 设置12小时过期，避免生成大量的数据造成消耗redis资源
                        break;
                    }
                }

            }
        }

        return $short_str; 
    }



    public static function get_host($http_host="none"){
        if($http_host == "none"){
            $http_host = Request::host();
        }
        $host_data = Db::table("tp_domain")->where("domain",$http_host)->order("id","asc")->limit(1)->select();

        //var_dump($host_data);
        //让所有的url都可以有数据
        if(count($host_data) == 0){
            $host_data = Db::table("tp_domain")->where("id","1")->order("id","asc")->limit(1)->select();
        }


        return $host_data;
    }


    //返回是否是蜘蛛 1 true ; 0 false
    public static function get_spider_status($user_agent=""){
        if($user_agent == ""){
            $user_agent = self::get_user_agent();
        }

        if(preg_match("#".Config::get("app.spider_user_agent")."#i", $user_agent)){
            $spider_status = 1;
        }else{
            $spider_status = 0;
        }
        return $spider_status;
    }

    //判断当前是否是pc端
    public static function get_pc_status($user_agent =""){
        if($user_agent == ""){
            $user_agent = self::get_user_agent();
        }
         //---------------- 判断当前是否是pc端  begin ----------------
         if(!preg_match("/".Config::get("app.mobile_user_agent")."/i", $user_agent)){
            $pc_status = 1;
        }else{
            $pc_status = 0;
        }
        //---------------- 判断当前是否是pc端  end ----------------
        return $pc_status;

    }
    
    //根据file_byte返回当前文件的size单位
    public static function get_file_size($num){
        $p = 0;
        $format = 'B';
        if ($num > 0 && $num < 1024) {$p = 0;return number_format($num)." ".$format;}
        if ($num >= 1024 && $num < pow(1024, 2)) {$p = 1;$format = 'KB';}
        if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {$p = 2;$format = 'MB';}
        if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {$p = 3;$format = 'GB';}
        if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {$p = 3;$format = 'TB';}
        $num /= pow(1024, $p);
        return number_format($num, 2)." ".$format;
    }


    //从shorten程序提取的ip获取工具，能够很准确的获取到IP，只要能获取到用户的IP，可以去掉之前使用用户UA判断的选项
    public static function get_user_ip(){
        if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) $ipaddress =  $_SERVER['HTTP_CF_CONNECTING_IP'];
        elseif (isset($_SERVER['HTTP_X_REAL_IP']))          $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))             $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED']))        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_FORWARDED']))    $ipaddress = $_SERVER['HTTP_FORWARDED'];
        elseif (isset($_SERVER['REMOTE_ADDR']))    $ipaddress = $_SERVER['REMOTE_ADDR'];
        else $ipaddress = "null";

        return $ipaddress;
    }



    public static function get_user_agent(){
        try{
            $user_agent = $_SERVER["HTTP_USER_AGENT"] ? $_SERVER["HTTP_USER_AGENT"] : "none";
        }catch(\Exception $e){
            $user_agent = "none";
        }

        if(strlen($user_agent) > 254){
            $user_agent = substr($user_agent,0,254);
        }

        return $user_agent;
    }

    public static  function get_user_language(){
        try{
            $user_language = Request::header('accept-language') ? Request::header('accept-language') : "none";
        }catch(\Exception $e){
            $user_language = "none";
        }

        if(strlen($user_language) > 254){
            $user_language = substr($user_language,0,254);
        }

        return $user_language ;
    }


    public function get_device(){
        $platform =   "Unknown OS";
        $os =  [
            '/windows nt 11.0/i'    =>  'Windows 11',
            '/windows nt 10.0/i'    =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/bb10/i'                 =>  'BlackBerry',
            '/cros/i'                =>    'Chrome OS',
            '/webos/i'              =>  'Mobile'
        ];
        foreach ($os as $regex => $value) { 
            if (preg_match($regex, $this->get_user_agent())) {
                $platform    =   $value;
            }
        }   
        return $platform;    
    }


    public function get_browser() {
        $matched   =     false;
        $browser   =   "Unknown Browser";
        $browsers  =   [
            '/safari/i'     =>  'Safari',            
            '/firefox/i'    =>  'Firefox',
            '/fxios/i'        =>  'Firefox',                        
            '/msie/i'       =>  'Internet Explorer',
            '/Trident\/7.0/i'  =>  'Internet Explorer',
            '/chrome/i'     =>  'Chrome',
            '/crios/i'        =>    'Chrome',
            '/opera/i'      =>  'Opera',
            '/opr/i'          =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/edg/i'       =>  'Edge',
        ];
        
        foreach ($browsers as $regex => $value) { 
            if (preg_match($regex,  $this->get_user_agent())) {
                $browser  =  $value;
                $matched = true;
            }
        }
        
        if(!$matched && preg_match('/mobile/i', $this->get_user_agent())){
            $browser = 'Mobile Browser';
        }

        return $browser;
    } 



    //返回英文国家名称
    //https://github.com/maxmind/GeoIP2-php#city-example
    public function get_country($ip){
        try {
            $reader = new \GeoIp2\Database\Reader(Config::get("app.install_path").'vendor/geoip2/GeoLite2-City.mmdb');
            $record = $reader->city($ip);
            $country = $record->country->name;

            if(empty($country)){
                $country = "None";
            }
        }catch(\Exception $e){
            //var_dump($e);
            $country = "None-Exception";
        }


        return $country;
    }

}