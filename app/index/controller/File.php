<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Cache;


class File extends BaseController
{
    //show page
    public function file(){
        
        $otherclass = new Otherclass($this->app);
        $host_data = $otherclass->get_host(Request::host());
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
        $user_agent = $otherclass->get_user_agent();
        $user_ip = $otherclass->get_user_ip();

        $user_language = $otherclass->get_user_language();
        $country = $otherclass->get_country($user_ip);
        $is_pc = $otherclass->get_pc_status();
        $is_spider = $otherclass->get_spider_status();

        //short_str不为空，并且长度为8
        $short_str = Request::param("short_str") ?? "";
        //if($short_str == "" || strlen($short_str) != 8){
        if($short_str == ""){
            abort(404, "short_str error!");
        }


        $file_data = Db::table("tp_file")->where("short_str",$short_str)->select();
        if(count($file_data) == 0){
            $this->error("File not exest!",'/',3);
        }


        if($file_data[0]['is_404'] == 1){
            abort(404,"is_404");
        }

        $title = $file_data[0]['file_name'];
        if(strlen($file_data[0]['comment']) < 10){
            $keywords = $file_data[0]['file_name'];
            $description = $file_data[0]['file_name'];

        }else{
            $keywords = $file_data[0]['file_name'].",".$file_data[0]['comment'];
            $description = $file_data[0]['file_name'].",".$file_data[0]['comment'];
        }
        
        
        #判断文件名是否有违禁后缀，提示用户当心下载 begin 文件大小必须小于10M  && $file_data[0]['file_byte'] < 10000000
        if(preg_match("#".Config::get("app.black_extension")."#i",$title) ){
            $black_extension_num =1 ;
        }else{
            $black_extension_num =0 ;
        }
        View::assign("black_extension_num",$black_extension_num);
        #判断文件名是否有违禁后缀，提示用户当心下载 end

        
        $user_redis_key = Config::get("app.redis_prefix")."file_hits_".$short_str."_".md5($user_agent.$user_ip);
        if(!Cache::has($user_redis_key)){

            //----------------------- hits点击量自增1 begin -------------------------- 
            Cache::set($user_redis_key, 1, 60*30);
            if($is_spider == 0){ //当为非蜘蛛时，更新数据库
                Db::table("tp_file")->where("short_str",$short_str)->inc('hits')->update();
                Db::table("tp_file")->where("short_str",$short_str)->update(['last_timestamp'=>time()]);
            }
        
            //----------------------- hits点击量自增1 end  -------------------------- 
         


            //----------------------  http_referer begin -------------------------------
   
            $http_referer_data = [
                'short_str'      => $short_str,
                'http_referer'   => Request::server("HTTP_REFERER") ?? "none",
                'user_agent'     => $user_agent,
                'is_pc'          => $is_pc,
                'is_spider'      => $is_spider,
                'user_language'  => $user_language,
                'user_ip'        => $user_ip,
                'country'        => $country,
                'page_from'      => "file_show",
                'timestamp'      => time()
            ];
            Db::table("tp_http_referer")->strict(false)->insert($http_referer_data);
            //----------------------  http_referer end  -------------------------------
        




            //-------------当天点击数REDIS进行缓存统计  Begin  ----------------
            if($is_pc==1){
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_pc_".date("Y-m-d",time());
            }else{
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_m_".date("Y-m-d",time());
            }

            if(Cache::get($is_pc_m_key)){
                Cache::inc($is_pc_m_key); //自增1
            }else{
                Cache::set($is_pc_m_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }

            $file_click_key = Config::get("app.redis_prefix")."click_file_file_".date("Y-m-d", time());
            
            if(Cache::get($file_click_key)){
                Cache::inc($file_click_key); //自增1
            }else{
                Cache::set($file_click_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }
            //-------------   当天点击数REDIS进行缓存统计  end  -----------------------
        }


        //---------------   google adsense 单用户的最大展示次数 begin  ---------------------------
        $user_adsense_redis_key = Config::get("app.redis_prefix")."_adsense_".md5($user_agent.$user_ip);
        if(!Cache::has($user_adsense_redis_key)){
            Cache::set($user_adsense_redis_key, 1, 60*30);
        }else{
            //redis key值自增1
            Cache::inc($user_adsense_redis_key, 1);
        }

        //与config文件中的配置值比对
        $adsense_display_times = Cache::get($user_adsense_redis_key);
        if($adsense_display_times > Config::get("app.adsense_max_display_times") && $is_spider == 0){
            $display_adsense_num = 0; //是否展示adsense广告，1为展示，0为不展示
        }else{
            $display_adsense_num = 1;
        }
        View::assign("display_adsense_num", $display_adsense_num);
        //---------------   google adsense 单用户的最大展示次数 end  ---------------------------

        #----------------- read cookies Begin ------------------------------------ 
        $cookie_name = Config::get("app.redis_prefix")."key_".$short_str;
        if(Cookie::has($cookie_name)){
            $cookie_value = Cookie::get($cookie_name);
        }else{
            $cookie_value = 0;
        }

        #判断cookies的值uuid是否与数据库中的一致,用此项来控制file页面的delete button按钮的显示和隐藏
        if((string)$cookie_value == (string)$file_data[0]['uuid'].$file_data[0]['id']){
            $delete_button_status = 1;
        }else{
            $delete_button_status = 0;
        }

        View::assign("delete_button_status", $delete_button_status);
        #-----------------  read cookies End  ------------------------------------ 


        #-----------------  report_form report_short_str begin -------------------
        View::assign("report_short_str", $short_str);
        #-----------------  report_form report_short_str end   -------------------

        View::assign("host_data", $host_data);
        View::assign("file_data", $file_data);
        View::assign("domain_url", $domain_url);
        View::assign("title", $title);
        View::assign("keywords", $keywords);
        View::assign("description", $description);
        View::assign("year_num", Config::get("app.year_num"));
 

        #------------------------ 页面token值设置，防止用户直接访问下载链接 begin ------------------------
        #在down url页面加上参数，token和timestamp，如果时间戳大于半小时或是token值加密计算不匹配，则跳转到showpage页面，避免用户盗链占用服务器资源
        $file_timestamp = time(); 
        $file_token = md5(Config::get("app.api_token").$file_timestamp);
        
        View::assign("file_timestamp",$file_timestamp);
        View::assign("file_token",$file_token);
        #------------------------ 页面token值设置，防止用户直接访问下载链接 end ------------------------
       

        return View::fetch("/File/file");
     

    }




    public function archive(){
        $otherclass = new Otherclass($this->app);
        $host_data = $otherclass->get_host(Request::host());
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
        $user_agent = $otherclass->get_user_agent();
        $user_ip = $otherclass->get_user_ip();


        $user_language = $otherclass->get_user_language();
        $country = $otherclass->get_country($user_ip);
        $is_pc = $otherclass->get_pc_status();
        $is_spider = $otherclass->get_spider_status();

        //short_str不为空，并且长度为7
        $short_str = Request::param("short_str") ?? "";
        //if($short_str == "" || strlen($short_str) != 7){
        if($short_str == ""){
            abort(404, "short_str error!");
        }


        $archive_data = Db::table("tp_file_archive")->where("short_str",$short_str)->select();
        if(count($archive_data) == 0){
            $this->error("File archive not exest!",'/',3);
        }

        
        if($archive_data[0]['is_404'] == 1){
            abort(404,"is_404");
        }
        

        $file_data = Db::table("tp_file")->where("id","in",$archive_data[0]['file_id'])->select();





        
        $user_redis_key = Config::get("app.redis_prefix")."archive_hits_".$short_str."_".md5($user_agent.$user_ip);
        if(!Cache::has($user_redis_key)){
            //----------------------- hits点击量自增1 begin -------------------------- 
            Cache::set($user_redis_key, 1, 60*30);
            if($is_spider == 0){ //当为非蜘蛛时，更新数据库
                Db::table("tp_file_archive")->where("short_str",$short_str)->inc('hits')->update();
                Db::table("tp_file_archive")->where("short_str",$short_str)->update(['last_timestamp'=>time()]);
            }
            //----------------------- hits点击量自增1 end  -------------------------- 


            //----------------------  http_referer begin -------------------------------
            

            $http_referer_data = [
                'short_str'      => $short_str,
                'http_referer'   => Request::server("HTTP_REFERER") ?? "none",
                'user_agent'     => $user_agent,
                'is_pc'          => $is_pc,
                'is_spider'      => $is_spider,
                'user_language'  => $user_language,
                'user_ip'        => $user_ip,
                'country'        => $country,
                'page_from'      => "file_archive",
                'timestamp'      => time()
            ];
            Db::table("tp_http_referer")->strict(false)->insert($http_referer_data);
        
            //----------------------  http_referer end  -------------------------------
        

        
            //-------------   当天点击数REDIS进行缓存统计  Begin  ----------------------
            if($is_pc==1){
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_pc_".date("Y-m-d",time());
            }else{
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_m_".date("Y-m-d",time());
            }

            if(Cache::get($is_pc_m_key)){
                Cache::inc($is_pc_m_key); //自增1
            }else{
                Cache::set($is_pc_m_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }

            $archive_click_key = Config::get("app.redis_prefix")."click_file_archive_".date("Y-m-d", time());
            
            if(Cache::get($archive_click_key)){
                Cache::inc($archive_click_key); //自增1
            }else{
                Cache::set($archive_click_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }
            //-------------  当天点击数REDIS进行缓存统计  end  --------------------
        }

        //---------------   google adsense 单用户的最大展示次数 begin  ---------------------------
        $user_adsense_redis_key = Config::get("app.redis_prefix")."_adsense_".md5($user_agent.$user_ip);
        if(!Cache::has($user_adsense_redis_key)){
            Cache::set($user_adsense_redis_key, 1, 60*30);
        }else{
            //redis key值自增1
            Cache::inc($user_adsense_redis_key, 1);
        }

        //与config文件中的配置值比对
        $adsense_display_times = Cache::get($user_adsense_redis_key);
        if($adsense_display_times > Config::get("app.adsense_max_display_times") && $is_spider == 0){
            $display_adsense_num = 0; //是否展示adsense广告，1为展示，0为不展示
        }else{
            $display_adsense_num = 1;
        }
        View::assign("display_adsense_num", $display_adsense_num);
        //---------------   google adsense 单用户的最大展示次数 end  ---------------------------


        $title = "File Archive ".$archive_data[0]['short_str']."(".$archive_data[0]['file_size'].")";
        $keywords = "File Archive ".$archive_data[0]['short_str']."(".$archive_data[0]['file_size'].")";
        $description = "File Archive ".$archive_data[0]['short_str']."(".$archive_data[0]['file_size'].")";
        


        #-----------------  report_form report_short_str begin -------------------
        View::assign("report_short_str", $short_str);
        #-----------------  report_form report_short_str end   -------------------      
        

        View::assign("host_data", $host_data);
        View::assign("archive_data", $archive_data);
        View::assign("file_data", $file_data);
        View::assign("domain_url", $domain_url);
        View::assign("title", $title);
        View::assign("keywords", $keywords);
        View::assign("description", $description);
        View::assign("year_num", Config::get("app.year_num"));

        #------------------------ 页面token值设置，防止用户直接访问下载链接 begin ------------------------
        #在down url页面加上参数，token和timestamp，如果时间戳大于半小时或是token值加密计算不匹配，则跳转到showpage页面，避免用户盗链占用服务器资源
        $file_timestamp = time(); 
        $file_token = md5(Config::get("app.api_token").$file_timestamp);
        View::assign("file_timestamp",$file_timestamp);
        View::assign("file_token",$file_token);
        #------------------------ 页面token值设置，防止用户直接访问下载链接 end ------------------------
 
        return View::fetch("/File/archive");
     
    }


    //down page
    public function start(){
        $otherclass = new Otherclass($this->app);
        $host_data = $otherclass->get_host(Request::host());
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";
        $user_agent = $otherclass->get_user_agent();
        $user_ip = $otherclass->get_user_ip();
        $user_language = $otherclass->get_user_language();
        $country = $otherclass->get_country($user_ip);
        $is_pc = $otherclass->get_pc_status();
        $is_spider = $otherclass->get_spider_status();

        //short_str不为空，并且长度为8
        $short_str = Request::param("short_str") ?? "";
        //if($short_str == "" || strlen($short_str) != 8){
        if($short_str == ""){
            abort(404, "short_str error!");
        }



        #--------- 判断file_token与timestamp begin -----------------
        # 非蜘蛛用户禁止直接访问download页面
        if($is_spider == 0){
            $file_timestamp = Request::param("t") ?? 0;
            $file_token = Request::param("token") ?? "";

            $file_token_ = md5(Config::get("app.api_token").$file_timestamp);  //验证md5的token加密值
    
            if(($file_token != $file_token_) || (time()-$file_timestamp) > 60*30){  //超时30分针则跳转show file页面
                return redirect('/'.$short_str);
            
            }
        }
        #--------- 判断file_token与timestamp end -----------------





        $file_data = Db::table("tp_file")->where("short_str",$short_str)->select();
        if(count($file_data) == 0){
            $this->error("File not exest!",'/',2);
        }

        if($file_data[0]['is_404'] == 1){
            abort(404,"is_404");
        }

        if($file_data[0]['delete_status'] == 2){
            $this->error("Malicious file! Download  disabled!",'/',5);
        }



        $http_referer =  $_SERVER['HTTP_REFERER'] ?? "/";
        //判断文件的delete_status ,如果此状态为1则说明文件已删除
        if($file_data[0]['delete_status'] == 1){
            $this->error("This file has been deleted!",$http_referer,2);
        }



        //----------------------  http_referer begin -------------------------------
        
        $redis_referer_key = Config::get("app.redis_prefix")."_file_start_referer".md5($user_agent.$user_ip.$short_str);
        //如果是同一个用户就只记录一次来源记录
        if(!Cache::has($redis_referer_key)){
            $http_referer_data = [
                'short_str'      => $short_str,
                'http_referer'   => Request::server("HTTP_REFERER") ?? "none",
                'user_agent'     => $user_agent,
                'is_pc'          => $is_pc,
                'is_spider'      => $is_spider,
                'user_language'  => $user_language,
                'user_ip'        => $user_ip,
                'country'        => $country,
                'page_from'      => "file_down",
                'timestamp'      => time()
            ];
            Db::table("tp_http_referer")->strict(false)->insert($http_referer_data);
            Cache::set($redis_referer_key, 1, 3600);//缓存一个小时
        }
        //----------------------  http_referer end  -------------------------------


        
        $user_redis_key = Config::get("app.redis_prefix")."file_download_".$short_str."_".md5($user_agent.$user_ip);
        if(!Cache::has($user_redis_key)){
            //----------------------- downloads下载量自增1 begin -------------------------- 
            Cache::set($user_redis_key, 1, 60*30);
            
            if($is_spider == 0){
                Db::table("tp_file")->where("short_str",$short_str)->inc('downloads')->update();
                Db::table("tp_file")->where("short_str",$short_str)->update(["last_timestamp"=>time()]);//如果非蜘蛛，更新最后访问时间
            }
            
        
            //----------------------- downloads下载量自增1 end  -------------------------- 
            //----------------------------   当天点击数REDIS进行缓存统计  Begin  ----------------------------------------------
            if($is_pc==1){
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_pc_".date("Y-m-d",time());
            }else{
                $is_pc_m_key = Config::get("app.redis_prefix")."click_is_m_".date("Y-m-d",time());
            }

            if(Cache::get($is_pc_m_key)){
                Cache::inc($is_pc_m_key); //自增1
            }else{
                Cache::set($is_pc_m_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }

            $start_click_key = Config::get("app.redis_prefix")."click_file_start_".date("Y-m-d", time());
            
            if(Cache::get($start_click_key)){
                Cache::inc($start_click_key); //自增1
            }else{
                Cache::set($start_click_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }
            //----------------------------   当天点击数REDIS进行缓存统计  end  ----------------------------------------------
        }


        //---------------   google adsense 单用户的最大展示次数 begin  ---------------------------
        $user_adsense_redis_key = Config::get("app.redis_prefix")."_adsense_".md5($user_agent.$user_ip);
        if(!Cache::has($user_adsense_redis_key)){
            Cache::set($user_adsense_redis_key, 1, 60*30);
        }else{
            //redis key值自增1
            Cache::inc($user_adsense_redis_key, 1);
        }

        //与config文件中的配置值比对
        $adsense_display_times = Cache::get($user_adsense_redis_key);
        if($adsense_display_times > Config::get("app.adsense_max_display_times") && $is_spider == 0){
            $display_adsense_num = 0; //是否展示adsense广告，1为展示，0为不展示
        }else{
            $display_adsense_num = 1;
        }
        View::assign("display_adsense_num", $display_adsense_num);
        //---------------   google adsense 单用户的最大展示次数 end  ---------------------------




        $title = "Download file ".$file_data[0]['file_name'];
        if(strlen($file_data[0]['comment']) < 10){
            $keywords = "Download file ".$file_data[0]['file_name'];
            $description = "Download file ".$file_data[0]['file_name'];

        }else{
            $keywords = "Download file ".$file_data[0]['file_name'].",".$file_data[0]['comment'];
            $description ="Download file ".$file_data[0]['file_name'].",".$file_data[0]['comment'];
        }
        

        $file_path = preg_replace("#^.*?/upload/#i","/upload",$file_data[0]['file_url']);
        $file_path_str = str_rot13(base64_encode($file_path));

        #判断文件名是否有违禁后缀，提示用户当心下载 begin 文件大小必须小于10M  &&  $file_data[0]['file_byte'] < 10000000
        if(preg_match("#".Config::get("app.black_extension")."#i",$title)){
            $black_extension_num =1 ;
        }else{
            $black_extension_num =0 ;
        }
        View::assign("black_extension_num",$black_extension_num);
        #判断文件名是否有违禁后缀，提示用户当心下载 end
        


        //如果后台删除了server，当cdn_server_data返回的数据长度为0会导致前端报错
        //后台的server不可随笔被删
        $cdn_server_data = Db::table("tp_server")->where("id",$file_data[0]['server_id'])->select();
        View::assign("cdn_server_data", $cdn_server_data);

        View::assign("file_path_str", $file_path_str);
        View::assign("host_data", $host_data);
        View::assign("file_data", $file_data);
        View::assign("domain_url", $domain_url);
        View::assign("title", $title);
        View::assign("keywords", $keywords);
        View::assign("description", $description);
        View::assign("is_spider", $is_spider);
        View::assign("year_num", Config::get("app.year_num"));
        View::assign("start_wait_time", Config::get("app.start_wait_time"));
 
       
        #-----------------  report_form report_short_str begin -------------------
        View::assign("report_short_str", $short_str);
        #-----------------  report_form report_short_str end   -------------------


        // #主要思路是get和post状态，get状态下如果有密码就展示密码输入页，无就正常展示
        // #如果有密码就展示密码输入页然后post
        // if(Request::isGet()){
            
        //     if($file_data[0]["password"] != ""){
        //         return View::fetch("/File/password");
        //     }else{

        //         return View::fetch("/File/start");
        //     }
            
        // }else{
        //     $file_password = Request::param("password") ?? "";

        //     #匹配密码
        //     if($file_password == $file_data[0]['password']){
        //         return View::fetch("/File/start");
        //     }else{
        //         $this->error("Password error! Please try again.",$_SERVER['HTTP_REFERER']??"/",3);
        //     }
            
        // }
        
        // #  ------------ short_end  begin ----------------------

        #------------------------ 页面token值设置，防止用户直接访问下载链接 begin ------------------------
        #在down url页面加上参数，token和timestamp，如果时间戳大于半小时或是token值加密计算不匹配，则跳转到showpage页面，避免用户盗链占用服务器资源
        #down page页面的参数用于file_gd_cdn服务器上的下载验证服务
        $file_timestamp = time(); 
        $file_token = md5(Config::get("app.api_token").$file_timestamp);
        View::assign("file_timestamp",$file_timestamp);
        View::assign("file_token",$file_token);
        #------------------------ 页面token值设置，防止用户直接访问下载链接 end ------------------------

        return View::fetch("/File/start");
     

    }


    public function delete(){
        $otherclass = new Otherclass($this->app);
        $user_agent = $otherclass->get_user_agent();
        $user_ip = $otherclass->get_user_ip();
        

        //short_str不为空，并且长度为8
        $short_str = Request::param("short_str") ?? "";
        //if($short_str == "" || strlen($short_str) != 8){
        if($short_str == ""){
            abort(404, "short_str error!");
        }

        $http_referer =  $_SERVER['HTTP_REFERER'] ?? "/";

        $file_data = Db::table("tp_file")->where("short_str",$short_str)->select();
        if(count($file_data) == 0){
            $this->error("File not exest!","/",3);
        }

        if($file_data[0]['is_404'] == 1){
            abort(404,"is_404");
        }

        //判断文件的delete_status ,如果此状态为1则说明文件已删除
        if($file_data[0]['delete_status'] == 1){
            $this->error("File Deleted!",$http_referer,2);
        }
       

        $file_server_data =  Db::table("tp_server")->where("id",$file_data[0]['server_id'])->select();
        if(count($file_server_data) == 0){
            $this->error("File server error, please try again later!",'/',3);
        }


        #----------------- read cookies Begin ------------------------------------ 
        $cookie_name = Config::get("app.redis_prefix")."key_".$short_str;
        if(Cookie::has($cookie_name)){
            $cookie_value = Cookie::get($cookie_name);
        }else{
            $cookie_value = 0;
        }

        #判断cookies的值uuid是否与数据库中的一致,用此项来控制file页面的delete button按钮的显示和隐藏
        if((string)$cookie_value == (string)$file_data[0]['uuid'].$file_data[0]['id']){
            $delete_button_status = 1;
        }else{
            $delete_button_status = 0;
        }

        #-----------------  read cookies End  ------------------------------------ 


        $user_redis_key = Config::get("app.redis_prefix")."file_delete_".$short_str."_".md5($user_agent.$user_ip);
        if(!Cache::has($user_redis_key)){
            //----------------------- downloads下载量自增1 begin -------------------------- 
            Cache::set($user_redis_key, 1, 60*30);
            $user_redis_key = Config::get("app.redis_prefix")."click_file_delete_".date("Y-m-d", time());
            


            if(Cache::get($user_redis_key)){
                Cache::inc($user_redis_key); //自增1
            }else{
                Cache::set($user_redis_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }
        }

        

        if($delete_button_status == 1){

            $file_server_url = $file_server_data[0]['server_url']."/delete_file";
            //echo $file_server_url;
            $api_token = Config::get("app.api_token");
            $httpclass = new HttpClass($this->app);
            $http_data = $httpclass->post($file_server_url, 
                [
                    "api_token" => $api_token,
                    "file_hash" => $file_data[0]['file_hash']
                ]
                ,$api_token);

            $json_data = json_decode((string)$http_data,true);

           
                    
            if($json_data["status"] == 200){
                if($json_data['data']['delete_status'] == 1){
                    Db::table("tp_file")->where("short_str",$short_str)->update(["delete_status"=>1]);
                    $this->success("File deleted successfully", $http_referer, 2);
                    
                }else{
                    $this->error("File deleted fail", $http_referer, 2);
                }
            }else{
                $this->error("Error, please try again later.", $http_referer, 2);
            }
        }else{
            $this->error("Permission denied!", $http_referer, 2);
        }

    }



}