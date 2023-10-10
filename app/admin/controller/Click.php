<?php
namespace app\admin\controller;


use app\BaseController;
use Exception;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;
use think\facade\Cache;


class Click  extends BaseController
{   
    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];

    public function list(){
        //获取当前url的参数
        $otherclass = new Otherclass($this->app);

        $list = array();
        for($i=0; $i< Config::get("app.click_analysis_days"); $i++){
            $new_array_ = array();
            $date_time = date("Y-m-d", time() - 60*60*24*$i);

            //pc和m端redis_key
            $is_pc_key = Config::get("app.redis_prefix")."click_is_pc_".date("Y-m-d", time() - 60*60*24*$i);
            $is_m_key = Config::get("app.redis_prefix")."click_is_m_".date("Y-m-d", time() - 60*60*24*$i);
            //file文件页面redis_key
            $file_click_key = Config::get("app.redis_prefix")."click_file_file_".date("Y-m-d", time() - 60*60*24*$i);
            $archive_click_key = Config::get("app.redis_prefix")."click_file_archive_".date("Y-m-d", time() - 60*60*24*$i);
            $start_click_key = Config::get("app.redis_prefix")."click_file_start_".date("Y-m-d", time() - 60*60*24*$i);
            $delete_click_key = Config::get("app.redis_prefix")."click_file_delete_".date("Y-m-d", time() - 60*60*24*$i);
            $index_click_key = Config::get("app.redis_prefix")."index_click_".date("Y-m-d", time() - 60*60*24*$i);
            $file_num_key = Config::get("app.redis_prefix")."click_file_num_".date("Y-m-d", time() - 60*60*24*$i);
            $file_byte_key = Config::get("app.redis_prefix")."click_file_byte_".date("Y-m-d", time() - 60*60*24*$i);
            $file_size_key = Config::get("app.redis_prefix")."click_file_size_".date("Y-m-d", time() - 60*60*24*$i);


            
            //pc点击数
            if(Cache::get($is_pc_key)){
                $is_pc_click = Cache::get($is_pc_key);
            }else{
                $is_pc_click = 0;
            }

            //m端点击数
            if(Cache::get($is_m_key)){
                $is_m_click = Cache::get($is_m_key);
            }else{
                $is_m_click = 0;
            }

            //获取file\archive\start页面点击
            if(Cache::has($file_click_key)){
                $file_click = Cache::get($file_click_key);
            }else{
                $file_click = 0;
            }
            if(Cache::has($archive_click_key)){
                $archive_click = Cache::get($archive_click_key);
            }else{
                $archive_click = 0;
            }
            if(Cache::has($start_click_key)){
                $start_click = Cache::get($start_click_key);
            }else{
                $start_click = 0;
            }
            if(Cache::has($delete_click_key)){
                $delete_click = Cache::get($delete_click_key);
            }else{
                $delete_click = 0;
            }

            if(Cache::has($index_click_key)){
                $index_click = Cache::get($index_click_key);
            }else{
                $index_click = 0;
            }

            if(Cache::has($file_num_key)){
                $file_num = Cache::get($file_num_key);
            }else{
                $file_num = 0;
            }

            if(Cache::has($file_byte_key)){
                $file_byte = Cache::get($file_byte_key);
            }else{
                $file_byte = 0;
            }

            if(Cache::has($file_size_key)){
                $file_size = Cache::get($file_size_key);
            }else{
                $file_size = 0;
            }




            //将数据增加到new_array()容器中
            $new_array_["date_time"] = $date_time;
            $new_array_["all_click"] = $is_pc_click + $is_m_click;
            $new_array_["is_pc"] = $is_pc_click;
            $new_array_["is_m"] = $is_m_click;
            $new_array_["file_click"] = $file_click;
            $new_array_["archive_click"] = $archive_click;
            $new_array_["start_click"] = $start_click;
            $new_array_["delete_click"] = $delete_click;
            $new_array_["index_click"] = $index_click;
            $new_array_["file_num"] = $file_num;
            $new_array_["file_byte"] = $file_byte;
            $new_array_["file_size"] = $file_size;
            array_push($list, $new_array_);
        }
        //var_dump($list);

        //------------  将读取的数据储存在数据库中 begin ---------------------
        $insert_click_data = array();

        //将数组使用日期作为键，然后再对其进行升序，这样使用日期插入到数据库中的值就是按照最新日期排序的
        for($i=0; $i < count($list); $i++){
            $insert_click_data[$list[$i]['date_time']] = $list[$i];
        }
        
        asort($insert_click_data);
        foreach($insert_click_data as $key => $value){
            //当天最新的数据不要插入到数据库中（当天最新的数据值还未积累完）
            if($key != date("Y-m-d", time())){
                $analysis_data = Db::table("tp_click")->where("date_time","=",$key)->select();
                //如果返回的数据值大于0，则说明数据库中已经有此项数据，否则就将此项数据插入数据库
                if(count($analysis_data) == 0){
                    //var_dump($value);
                    Db::table("tp_click")->strict(false)->insert($value);
                }
            }
        }
        //------------  将读取的数据储存在数据库中 end ----------------------


        $data_list = Db::table('tp_click')->order('id', 'desc')->paginate([
            'list_rows' => Config::get('app.admin_page_num'),
            'path'  => "/".Config::get("app.admin_path").'/click/list',
            'query' => Request::param(),
        ]); 

        $today_datetime = date("Y-m-d", time());

        $page_num = Request::param("page") ? Request::param("page") : "1";


        //获取command命令行FileServer的状态
        $command_redis_key = Config::get("app.redis_prefix")."command_file_server";
        if(Cache::has($command_redis_key)){
            $command_status = date("Y-m-d H:i:s", Cache::has($command_redis_key));
        }else{
            $command_status = 0;
        }
        View::assign("command_status",$command_status);





        $report_data = Db::table("tp_report")->where("status","0")->select();
        $contact_data = Db::table("tp_contact")->where("status","0")->select();
        $file_last_data = Db::table("tp_file")->order("id","desc")->limit(1)->select();
        
        View::assign("now_timestamp",time());
        View::assign("report_data_num",count($report_data));
        View::assign("contact_data_num",count($contact_data));
        View::assign("file_last_data",$file_last_data);
        View::assign('data_list',$data_list);
        View::assign('today_datetime',$today_datetime);
        View::assign('page_num',$page_num);
        View::assign('username',Session::get('username'));
        View::assign('list',$list);
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('admin_url',Config::get('app.admin_url'));
        View::assign('admin_path',Config::get('app.admin_path'));

        return View::fetch('/Click/click_list');       

    }

    public function edit(){
        $id = Request::param("id") ? Request::param("id") : 0;
        if(empty($id)){
            $this->error("id is empty.",$_SERVER["HTTP_REFERER"],1);
        }

        $data = Db::table('tp_click')->where('id','=',$id)->select();
        

        View::assign('username',Session::get('username'));
        View::assign('data',$data);
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        return View::fetch('/Click/click_edit');      
    }

    public function editPost(){
        $data = Request::post();

        $id = $data['id'];
        $ad_income = $data['ad_income'];
        $ad_views = $data['ad_views'];
        $ad_display = $data['ad_display'];
        $ad_hits = $data['ad_hits'];
        $ad_cpc = $data['ad_cpc'];
        //分母不能为0
        try{
            $ad_rpm = $ad_income/$ad_views*1000;
            $ad_ctr = $ad_hits/$ad_views*100;
        }catch(\Exception $e){
            $this->error("AD_VIEWS can not be 0.","/".Config::get("app.admin_path")."/click/list",2);
        }


        $update_data = [
            "ad_views" => $ad_views,
            "ad_display" => $ad_display,
            "ad_hits" => $ad_hits,
            "ad_rpm" => $ad_rpm,
            "ad_income" => $ad_income,
            "ad_ctr" => $ad_ctr,
            "ad_cpc" => $ad_cpc
        ];

    

        if(Db::table('tp_click')->strict(false)->where('id',$id)->update($update_data)){
            $this->success("Data edit success.","/".Config::get("app.admin_path")."/click/list",1);
        }else{
            $this->error("Data edit fail.","/".Config::get("app.admin_path")."/click/list",2);
        }
    }
}
