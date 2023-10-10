<?php
namespace app\admin\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;



class File  extends BaseController
{   

    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];



    public function list(){

        $sort = Request::param("sort") ?? "";

        $domain_data = Db::table("tp_domain")->order("id","asc")->select();

        //var_dump($domain_data);

        if($sort == ""){
            $list = Db::table('tp_file')->order('id', 'desc')->paginate([
                'list_rows' => Config::get('app.admin_page_num'),
                'path'  => "/".Config::get("app.admin_path").'/file/list'
            ]);
        }else{
            $list = Db::table('tp_file')->order('hits', 'desc')->paginate([
                'list_rows' => Config::get('app.admin_page_num'),
                'path'  => "/".Config::get("app.admin_path").'/file/list',
                'query' => Request::only(["sort"])
            ]);
        }

        View::assign("domain_data",$domain_data);
        View::assign('username',Session::get('username'));
        View::assign('list',$list);
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('admin_url',Config::get('app.admin_url'));
        View::assign('admin_path',Config::get('app.admin_path'));

        return View::fetch('/File/file_list');       

    }



    public function edit(){

        $id = Request::param('id');
        $id = trim($id);
        $data = Db::query("select a.*,b.file_id,b.user_ip,b.country,b.is_pc,b.user_agent,b.user_language from tp_file a, tp_file_creater_info b where a.id=b.file_id and a.id=".$id);




        View::assign('username',Session::get('username'));
        View::assign('data',$data);
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/File/file_edit');      

    }

    public function editPost(){
        $id = Request::param("id");
        $id = trim($id);
        $data = Request::post();
    

        

        if(Db::table('tp_file')->strict(false)->where('id',"=",$id)->update($data)){
            $this->success("Data edit success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Data edit fail.",$_SERVER["HTTP_REFERER"],2);
        }


       

    }


    public function query(){


        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/File/file_query');
    }

    public function querypost(){
        $short_str = trim(Request::param("short_str"));

        if(strlen($short_str) == 0){
            $this->error("short_str is empty!",$_SERVER["HTTP_REFERER"],2);
        }

        //是否是查询文件，长度为5说明查询的是archive，长度为6说明查询的是file
        if(strlen($short_str) == 5){
            $is_query_file = 0;
        }else{
            $is_query_file = 1;
        }

        if($is_query_file == 1){
            $list = Db::table("tp_file")->where("short_str","=",$short_str)->order('id','desc')->paginate([
                'list_rows' => Config::get('app.admin_page_num'),
                'path'     => "/".Config::get("app.admin_path").'/file/querypost',
                'query'    => Request::param(),
            ]);
        }else{
            $list = Db::table("tp_file_archive")->where("short_str","=",$short_str)->order('id','desc')->paginate([
                'list_rows' => Config::get('app.admin_page_num'),
                'path'     => "/".Config::get("app.admin_path").'/file/querypost',
                'query'    => Request::param(),
            ]);
        }
        

        $domain_data = Db::table("tp_domain")->order("id","asc")->select();
        View::assign('domain_data', $domain_data);
        
        View::assign('list',$list);
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        if($is_query_file == 1){
            return View::fetch('/File/file_list');
        }else{
            return View::fetch('/File/archive_list');
        }
        
    }



    public function querypost_like(){
        $like_str = trim(Request::param("like_str"));

        if(strlen($like_str) == 0){
            $this->error("like_str is empty!",$_SERVER["HTTP_REFERER"],2);
        }


        $list = Db::query("select * from tp_file where file_name like \"%".$like_str."%\" order by id desc;");


        $domain_data = Db::table("tp_domain")->order("id","asc")->select();
        View::assign('domain_data', $domain_data);


        //click total
        $total = 0;
        foreach($list as $item){
            $total += $item["hits"];
        }
        View::assign("total",$total);
        
        View::assign('list',$list);
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/File/query_list_like');
    }


    public function querypost_in(){
        $in_str = trim(Request::param("in_str"));

        //前端默认提交的字符串是使用逗号或者空格隔开的，要替换字符串拼接SQL
        //如：select * from tp_file where short_str in("WHJidG","QkN4WD","MWVqbG","eGNoRF","eE1qNT");
        $in_str = preg_replace("# #i",",",$in_str);
        $in_str = preg_replace("#，#i",",",$in_str);
        $in_str = preg_replace("#,#i","\",\"",$in_str);

    
        if(strlen($in_str) == 0){
            $this->error("in_str is empty!",$_SERVER["HTTP_REFERER"],2);
        }
        $query_sql = "select * from tp_file where short_str in (\"".$in_str."\");";


        $list = Db::query($query_sql);



        $domain_data = Db::table("tp_domain")->order("id","asc")->select();
        View::assign('domain_data', $domain_data);


        
        View::assign("total",count($list));
        View::assign('list',$list);
        View::assign('username',Session::get('username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/File/query_list_in');
    }




    public function delete(){
        $id = trim(Request::param("id"));

        

        if(Db::table('tp_file')->where('id',$id)->delete()){
            $this->success("Data delete success.","/".Config::get('app.admin_path')."/File/list",1);
        }else{
            $this->error("Data delete fail.","/".Config::get('app.admin_path')."/File/list",2);
        }
    }


    public function delete_file(){
        $id = trim(Request::param("id"));
        $data = Db::table("tp_file")->where("id",$id)->select();

        if(count($data) == 0){
            $this->error("Data langth less than 1.","/".Config::get('app.admin_path')."/File/list",2);
        }

        //try{
            //var_dump($item);
            $file_server_data =  Db::table("tp_server")->where("id",$data[0]['server_id'])->select();
            if(count($file_server_data) == 0){
                $this->error("File CDN server error.","/".Config::get('app.admin_path')."/File/list",2);
            }



            $file_server_url = $file_server_data[0]['server_url']."/delete_file";
            
            $api_token = Config::get("app.api_token");
            $httpclass = new \app\index\controller\HttpClass($this->app);
            $http_data = $httpclass->post($file_server_url, 
                [
                    "api_token" => $api_token,
                    "file_hash" => $data[0]['file_hash']
                ]
                ,$api_token);

            $json_data = json_decode((string)$http_data,true);

            //var_dump($json_data);
                    
            if($json_data["status"] == 200){
                if($json_data['data']['delete_status'] == 1){
                    Db::table("tp_file")->where("id",$id)->update(["delete_status"=>3]);
                    $this->success("Data delete success.","/".Config::get('app.admin_path')."/File/list",1);
                    
                }else{
                    $this->error("Data delete fail.","/".Config::get('app.admin_path')."/File/list",2);
                }
            }else{
                //如果服务器上返回file_data less than 1则说明服务器上不存在此文件，将delete_status状态值设置为1
                if($json_data["status"] == 300 && $json_data["message"] == "file_data less than 1"){
                    Db::table("tp_file")->where("id",$id)->update(["delete_status"=>1]);
                }
                $this->error("Data delete fail，File_data less than 1 ","/".Config::get('app.admin_path')."/File/list",2);
            }
        

            
        // }catch(\Exception $e){
        //     var_dump($e);
            
        // }

    }



    public function  area_analysis(){

        //分析时间，0时为当天时间，1为昨天，以此类推
        $day = Request::param("day") ? Request::param("day") : "0";
        
        $begin_timstamp_day = strtotime(date('Y-m-d').'00:00:00');
        $end_timstamp_day = strtotime(date('Y-m-d').'23:59:59');

        $begin_timestamp = $begin_timstamp_day - 60*60*24*$day;
        $end_timestamp = $end_timstamp_day - 60*60*24*$day;


        $data = Db::table("tp_file")->where("timestamp",">",$begin_timestamp)->where("timestamp","<",$end_timestamp)->order("id","desc")->select();



        $analysis_array = array();
        //用于统计pc和m端的占比,以及根据IP统计用户数量
        $is_pc_num = 0;
        $is_m_num = 0;
        $pc_user = array();
        $m_user = array();
        foreach($data as $item){
            $country = $item['country'];
            if(array_key_exists($country,$analysis_array)){
                //获取key=>value的值，并将value自增1
                
                $key_value = $analysis_array[$country] + 1;
                $analysis_array[$country] = $key_value;
            }else{
                
                $analysis_array[$country] = 1;
            }

            $user_ip = $item['remote_ip'];
            //pc和移动端的统计
            if($item["is_pc"] == 1){
                $is_pc_num += 1;
         
                if(!in_array($user_ip,$pc_user)){
                    array_push($pc_user, $user_ip);
                }

            }else{
                $is_m_num +=1;
                if(!in_array($user_ip,$m_user)){
                    array_push($m_user, $user_ip);
                }
            }

        }
        

        $total = 0;
        foreach($analysis_array as $k=>$v){
            $total += $v;
        }
        
        //echo ">> Total:{$total}\n";
        arsort($analysis_array);
        //var_dump($analysis_array);
        
        View::assign("is_pc_num",$is_pc_num);
        View::assign("is_m_num",$is_m_num);
        View::assign("pc_user",count($pc_user));
        View::assign("m_user",count($m_user));
        View::assign("list",$analysis_array);
        View::assign("total",$total);
        View::assign("username",Session::get('username'));
        View::assign("admin_path",Config::get('app.admin_path'));
        View::assign("pc_url",Config::get('app.admin_url'));
        return View::fetch('/File/area_analysis');

    }


    public function black_file_hash(){
        $id = Request::param("id") ?? "";
        $id = trim($id);

        if($id == ""){
            $this->error("id is null",$_SERVER["HTTP_REFERER"],2);
        }

        $file_data = Db::table("tp_file")->where("id",$id)->select();
        if(count($file_data) == 0){
            $this->error("data length is null",$_SERVER["HTTP_REFERER"],2);
        }

        $insert_data = [
            "file_hash" => $file_data[0]['file_hash'],
            "file_name" => $file_data[0]['file_name'],
            "file_size" => $file_data[0]['file_size'],
            "timestamp" => time(),
            "comment"   => "人工巡查手工拉黑"
        ];

        $data_ = Db::table("tp_malicious_file")->where("file_hash",$file_data[0]['file_hash'])->select();

        if(count($data_) > 0){
            $this->error("Malicious file_hash is already in database.",$_SERVER["HTTP_REFERER"],2);
        }else{
            if(Db::table('tp_malicious_file')->strict(false)->insert($insert_data)){
                $this->success("Malicious file_hash insert success.",$_SERVER["HTTP_REFERER"],1);
            }else{
                $this->error("Malicious file_hash insert failed.",$_SERVER["HTTP_REFERER"],2);
            }
        }

    }

    public function set_is_404_in(){
        $id_str = Request::param("id_str") ?? "";
        $id_str = trim($id_str);

        if($id_str == ""){
            $this->error("id_str is null",$_SERVER["HTTP_REFERER"],2);
        }

        $sql = "update tp_file set is_404=1 where id in (".$id_str.");";
        if(Db::query($sql)){
            $this->success("Setting success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Setting failed.",$_SERVER["HTTP_REFERER"],2);
        }
     

    }

    public function set_delete_status_in(){
        $id_str = Request::param("id_str") ?? "";
        $id_str = trim($id_str);

        if($id_str == ""){
            $this->error("id_str is null",$_SERVER["HTTP_REFERER"],2);
        }

        $sql = "update tp_file set delete_status=2 where id in (".$id_str.");";
        if(Db::query($sql)){
            $this->success("Setting success.",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("Setting failed.",$_SERVER["HTTP_REFERER"],2);
        }
     

    }

}
