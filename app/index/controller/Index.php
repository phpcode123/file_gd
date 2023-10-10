<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Cache;


class Index extends BaseController
{

    public function index(){
        $otherclass = new Otherclass($this->app);
        $user_ip = $otherclass->get_user_ip();
        $country = $otherclass->get_country($user_ip);
        $user_agent = $otherclass->get_user_agent();
        $host_data = $otherclass->get_host(Request::host());
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";

        $user_language = $otherclass->get_user_language();
        $is_pc = $otherclass->get_pc_status();
        $is_spider = $otherclass->get_spider_status();
    
        $title = $host_data[0]['index_title'];
        $keywords = $host_data[0]['index_keyword'];
        $description = $host_data[0]['index_description'];
        
        if(Config::get("app.server_upgrade_status") == 1){
            return  Config::get("app.server_upgrade_tips");
            
        }


        $user_index_redis_key = Config::get("app.redis_prefix")."index_".md5($user_agent.$user_ip);
        if(!Cache::has($user_index_redis_key)){


            //---------------------- index http_referer begin -------------------------------
            
   
            $http_referer_data = [
                'http_referer'   => Request::server("HTTP_REFERER") ?? "none",
                'user_agent'     => $user_agent,
                'is_pc'          => $is_pc,
                'is_spider'      => $is_spider,
                'user_language'  => $user_language,
                'user_ip'        => $user_ip,
                'country'        => $country,
                'timestamp'      => time()
            ];
            Db::table("tp_index_log")->strict(false)->insert($http_referer_data);
            //---------------------- index http_referer end  -------------------------------
        

            //-------------当天首页点击数REDIS进行缓存统计  Begin  ----------------
            
            $index_click_key = Config::get("app.redis_prefix")."index_click_".date("Y-m-d",time());
            if(Cache::get($index_click_key)){
                Cache::inc($index_click_key); //自增1
            }else{
                Cache::set($index_click_key, 1, 60*60*24*Config::get("app.click_analysis_days"));//设置一个值
            }
            //-------------   当天首页点击数REDIS进行缓存统计  end  -----------------------
            Cache::set($user_index_redis_key, 1, 60*30);
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





        //----------------------------   获取总点击数统计   Begin  ----------------------------------------------
        //设置点击数统计key:redis_prefix-shortener-clicks-
        $total_redis_key = Config::get("app.redis_prefix")."_total_clicks";
        
        //echo $redis_key;
        //当redis数据库中存在此键值时，直接读取此键值的数据，否则就新建一个
        if(Cache::has($total_redis_key)){
            $total_clicks_value = Cache::get($total_redis_key);

        }else{
            $total_clicks_value = 1;
        }
        $total_clicks = Config::get("app.index_total_clicks_base_num")+$total_clicks_value;
        //----------------------------   获取总的点击数统计   End  ----------------------------------------------
        

        //--------------------- 首页随机验证码 begin ------------------------------
        //验证码的核心逻辑就是客户端与服务端的md5(ua+"-"+timestamp)进行验证,index首页使用timestamp进行伪装
        $index_timestamp = time();
        $hash_str = md5(Request::header('user-agent')."-".$index_timestamp);
        Cache::set(Config::get("app.redis_prefix")."index_hash_str".$hash_str, 1, 60*60);
        View::assign("index_timestamp",$index_timestamp);
        //--------------------- 首页随机验证码 end  ------------------------------



        //----------------  随机获取一个服务器状态在线cdn_server begin -------------------------
        $cdn_server_data = Db::table("tp_server")->where("disk_status","1")->where("use_status","1")->select();
        $cdn_server = $cdn_server_data[mt_rand(0,count($cdn_server_data)-1)];
        View::assign("cdn_server",$cdn_server);
        //----------------  随机获取一个服务器状态在线cdn_server end -------------------------

        
        $domain_data = Db::table("tp_domain")->order("id","asc")->select();
        View::assign("domain_data", $domain_data);



        View::assign("total_clicks",$total_clicks);
        View::assign("index_display_user_cookies_data",Config::get("app.index_display_user_cookies_data"));


        View::assign("host_data", $host_data);
        View::assign("domain_url", $domain_url);
        View::assign("title", $title);
        View::assign("keywords", $keywords);
        View::assign("description", $description);
        View::assign("year_num", Config::get("app.year_num"));
 
       


        return View::fetch("/Index/index");
     

    }



    public function generate_link(){
        $otherclass = new Otherclass($this->app);
        $user_ip = $otherclass->get_user_ip();
        $country = $otherclass->get_country($user_ip);
        $host_data = $otherclass->get_host();
        $domain_url = $host_data[0]['http_prefix'].Request::host()."/";


        //获取用户UA，如果长度大于254，就只截取254的长度
        $user_agent = $otherclass->get_user_agent();
        $user_language = $otherclass->get_user_language();


        //---------------- 判断当前是否是pc端 ----------------
        $is_pc = $otherclass->get_pc_status($user_agent);



        // ---------------  时间戳验证码验证 begin  ------------------------------
        $hash_str = Request::param("hash_str") ?? "";
        if(!Cache::has(Config::get("app.redis_prefix")."index_hash_str".$hash_str) || $hash_str == "" || (Cache::has(Config::get("app.redis_prefix")."index_hash_str".$hash_str) > 1)){
            $this->error("Please try again, Error code 1.",'/',3);
        }else{
            //将index_hash设置为2，
            Cache::set(Config::get("app.redis_prefix")."index_hash_str".$hash_str, 2, 60*60);
        }
        // ---------------  时间戳验证码验证 end  ------------------------------

        $files_json_data = Request::param("file_data") ?? "";
        $server_id = Request::param("server_id") ?? "0";
        $comment = substr(Request::param("comment"),0,254) ?? "";


        

        $files_data = json_decode($files_json_data, true);
        
        //var_dump($files_data);
        
 
        //判断文件数组的长度，当长度为0时说明文件为0，提示选择文件
        if(count($files_data) == 0){
            //记录错误日志
            //var_dump(Request::param());
            try{
                $this->log($user_ip." - ".$country." - "." index-error-log  ".(string)Request::param("files[]"),"index_error_log.txt");
            }catch(\Exception $e){
                $this->log($user_ip." - ".$country." - "." index-error-log, file write error\n---------------------\n".(String)$e."\n------------------------\n\n","index_error_log.txt");
            }
            $this->error("Error.",'/',2);
        }else{
            //先将file数据批量插入数据库
            $file_id_array = [];
            $file_short_str_array = [];

            //如果当文件数量大于1则统计archive的file_byte
            $archive_file_byte = 0;
            $archive_display_ad = 1;

            for($i=0; $i<count($files_data); $i++){
                try{
                    //$file_key = $files_data[$i]['key'];  定位文件使用file_hash 已全面去掉file_key
                    $short_str = $otherclass->get_short_str(Config::get("app.short_str_length"));

                    $archive_file_byte += $files_data[$i]['size'];//archive file_byte
                    $uuid = uniqid();

                    # ---------------  判断当前文件是否是恶意文文件 begin ----------------
                    # 主要通过file_hash来匹配tp_malicious_file 表
                    # delete_status 默认为0，1为用户自己删除文件，2为系统判断为恶意文件，禁止用户下载
                    $malicious_file_data = Db::table("tp_malicious_file")->where("file_hash",$files_data[$i]['hash'])->select();
                    if(count($malicious_file_data)>0){
                        $delete_status = 2;
                    }else{
                        $delete_status = 0;
                    }
                    # ---------------  判断当前文件是否是恶意文文件 end ----------------

                    //-------------------- 如果是图片文件则不展示google adsense，即将display_ad设置为0 begin--------------------
                    if(preg_match("#image#i",$files_data[$i]['type'])){
                        $display_ad = 0;
                    }else{
                        $display_ad = 1;
                    }


                    $black_title_keyword_num = 1; //用于判断$archive_display_ad的状态
                    if($display_ad == 1){
                        if(preg_match("#".Config::get("app.black_title_keyword")."#i",$files_data[$i]['name'])){
                            $display_ad = 0;
                            $black_title_keyword_num = 0;
                        }else{
                            $display_ad = 1;
                        }
                    }

                    //-------------------   archive display_ad的设置状态  begin --------------------
                    //$display_ad表示file的display_ad状态, 只有当archive_display_ad的状态为1时才进行判断
                    //archive_display_ad的值要在插入tp_file_archive时使用
                    if($archive_display_ad == 1 && $black_title_keyword_num == 0){       
                        $archive_display_ad = 0;
                    }
                    //-------------------   archive display_ad的设置状态  end --------------------

                    //-------------------- 如果是图片文件则不展示google adsense，即将display_ad设置为0 end--------------------

                    $insert_data = [
                        "uuid" => $uuid,
                        "site_id" => $host_data[0]['id'],
                        "server_id" => $server_id,
                        "short_str" => $short_str,
                        "file_hash" => $files_data[$i]['hash'],
                        "file_name" => $files_data[$i]['name'],
                        "file_byte" => $files_data[$i]['size'],
                        "file_size" => $otherclass->get_file_size($files_data[$i]['size']),
                        "file_type" => $files_data[$i]['type'],
                        "file_url"  => $files_data[$i]['url'],
                        "comment" => $comment,
                        "display_ad" => $display_ad,
                        "country" => $country,
                        "timestamp" => time(),
                        "delete_status" => $delete_status
                        
                    ];

          
                    
                    //判断当前short_str是否在redis缓存中，如果在缓存中且redis缓存值为1，则开始处理数据
                    if(Cache::get(Config::get("app.redis_prefix").$short_str) == 1){

                        //判断当前的short_str(file['key'])是否在数组中，如果在数组中说明已经处理过数据
                        if(!in_array($short_str,$file_short_str_array)){
                            array_push($file_short_str_array, $short_str);
                            $get_file_id = Db::Table("tp_file")->strict(false)->insertGetId($insert_data);

                            //先将返回的id值加入array中，并将当前的返回的id值储存在redis中,且更新redis_index为1
                            array_push($file_id_array, $get_file_id);
                            Cache::set(Config::get("app.redis_prefix").$short_str, $get_file_id);
                            Db::table("tp_file")->where("id",$get_file_id)->update(["redis_index"=>1]);


                            //插入当前用户数据到tp_file_creater_info表中
                            $user_insert_data = [
                                "file_id" => $get_file_id,
                                "user_ip" => substr($user_ip,0,99),
                                "country" => $country,
                                "is_pc" => $is_pc,
                                "user_agent" => $user_agent,
                                "user_language" => $user_language,
                                "timestamp" => time(),
                            ]; 
                            Db::table("tp_file_creater_info")->strict(false)->insert($user_insert_data);
                           
                        }



                        # -------------------- save cookies begin ----------------
                        #  --------  short_str cookie begin ----------------------
                        $cookie_name = Config::get("app.redis_prefix")."key_array";

                        if(Cookie::has($cookie_name)){
                            $cookie_value = Cookie::get($cookie_name);

                            #分割字符串并重新组成新的字符串
                            $new_cookie_array = explode(",",$cookie_value);

                            #将short_str添加至数组中
                            array_push($new_cookie_array,$short_str);

                            $cookie_value = "";
                            for($x=0;$x<count($new_cookie_array);$x++){
                                if($x==0){
                                    $cookie_value = $new_cookie_array[$x];
                                }else{
                                    $cookie_value .= ",".$new_cookie_array[$x];
                                }
                            }
                        }else{
                            $cookie_value = $short_str;
                        }
                        Cookie::forever($cookie_name,$cookie_value);
                        #  --------  short_str cookie end ---------------------------
                    

                        #   -------- short_str:delete file begin -----------
                        $cookie_name_2 = Config::get("app.redis_prefix")."key_".$short_str;
                        Cookie::set($cookie_name_2, $uuid.$get_file_id, 43200); //60*60*12
                        #   -------- short_str:delete file end -----------
                        # -------------------- save cookies end ----------------
                    
                       
                        #--------- redis储存当天的文件上传数量和byte数 begin --------------
                        $redis_file_num_key  = Config::get("app.redis_prefix")."click_file_num_".date("Y-m-d", time());
                        $redis_file_byte_key = Config::get("app.redis_prefix")."click_file_byte_".date("Y-m-d", time());
                        $redis_file_size_key = Config::get("app.redis_prefix")."click_file_size_".date("Y-m-d", time());
                        
                        if(Cache::has($redis_file_num_key)){
                            Cache::inc($redis_file_num_key,1);
                        }else{
                            Cache::set($redis_file_num_key,1,60*60*24*Config::get("app.click_analysis_days"));
                        }

                        if(Cache::has($redis_file_byte_key) && Cache::has($redis_file_size_key)){
                            $file_byte_value = Cache::get($redis_file_byte_key);
                            $file_byte_value += $files_data[$i]['size'];
                            Cache::set($redis_file_byte_key, $file_byte_value,60*60*24*Config::get("app.click_analysis_days"));
                            Cache::set($redis_file_size_key, $otherclass->get_file_size($file_byte_value),60*60*24*Config::get("app.click_analysis_days"));
                        }else{
                            
                            Cache::set($redis_file_byte_key,$files_data[$i]['size'],60*60*24*Config::get("app.click_analysis_days"));
                            Cache::set($redis_file_size_key,$otherclass->get_file_size($files_data[$i]['size']),60*60*24*Config::get("app.click_analysis_days"));
                        }

                        #--------- redis储存当天的文件上传数量和byte数 end --------------





                    }else{
                        continue;
                    }
                }catch(\Exception $e){
                    //do something
                }
                
            }


            //判断$file_id_array的长度，如果长度大1则说明是archive，archive short_str是5位长度
            if(count($file_id_array) > 1){
                $archive_short_str = $otherclass->get_short_str(Config::get("app.archive_str_length"));
                $file_id_str = implode(",",$file_id_array);

                $archive_insert_data = [
                    "uuid" => uniqid(),
                    "site_id" => $host_data[0]['id'], 
                    "server_id" => $server_id, 
                    "file_id" => $file_id_str, 
                    "short_str" => $archive_short_str,
                    "file_byte" => $archive_file_byte,
                    "file_size" => $otherclass->get_file_size($archive_file_byte),
                    "country" => $country,
                    "timestamp" => time(),
                    "display_ad" => $archive_display_ad
                ];

                $archive_file_id = Db::table("tp_file_archive")->strict(false)->insertGetId($archive_insert_data);
                if($archive_file_id ){
                    //将当前archive short_str储存在redis中， 并更新redis_index为1
                    Cache::set(Config::get("app.redis_prefix").$archive_short_str, $archive_file_id);
                    Db::table("tp_file_archive")->where("id",$archive_file_id)->update(["redis_index"=>1]);
                }


                //--------------- 将当前的archive short_str 更新到当前所有的file的id  begin---------------------
                try{
                    Db::table("tp_file")->where("id","in",$file_id_str)->update(["archive_str"=>$archive_short_str]);
                }catch(\Exception $e){
                    //do something
                }
                //--------------- 将当前的archive short_str 更新到当前所有的file的id  end ---------------------

                return redirect($domain_url.$archive_short_str, 302);
            }else{
                return redirect($domain_url.$short_str, 302);
            }


        }

    }


}