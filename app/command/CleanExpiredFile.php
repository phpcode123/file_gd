<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use think\facade\Db;
use think\facade\Cache;

class CleanExpiredFile extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\cleanexpiredfile')
            ->setDescription('the app\command\cleanexpiredfile command');
    }

    protected function execute(Input $input, Output $output)
    {
        //文件保存时间，超过设置天数就会自动删除
        //注意：此天数并不是硬性天数，而是文档半个月内没人访问就自动删除，以节约服务器资源，如果有人访问则自动延续半个月。文件总是有连续的访问，则永远不会删除。


        $days=15;
        while(True){
            try{
                //注意还有hits和downloads参数,在archive中，有可能会出现hits为0，但是downloads不为零的情况
                $file_data = Db::table("tp_file")->where("last_timestamp","<",time()-60*60*24*Config::get("app.file_expired_days"))->where("timestamp","<",time()-60*60*24*$days)->where("delete_status","0")->order("id","asc")->select();
            }catch(\Exception $e){
                echo "MySql Exception Error. Sleep(1Hours)";
                sleep(60*60);
            }
            foreach($file_data as $item){
                try{
                    //var_dump($item);
                    $file_server_data =  Db::table("tp_server")->where("id",$item['server_id'])->select();
                    if(count($file_server_data) == 0){
                        echo ">> ".$item["id"]." File server error, please try again later!".PHP_EOL;
                        sleep(3);
                        continue;
                    }



                    $file_server_url = $file_server_data[0]['server_url']."/delete_file";
                    
                    $api_token = Config::get("app.api_token");
                    $httpclass = new \app\index\controller\HttpClass($this->app);
                    $http_data = $httpclass->post($file_server_url, 
                        [
                            "api_token" => $api_token,
                            "file_hash" => $item['file_hash']
                        ]
                        ,$api_token);

                    $json_data = json_decode((string)$http_data,true);

                    //var_dump($json_data);
                            
                    if($json_data["status"] == 200){
                        if($json_data['data']['delete_status'] == 1){
                            Db::table("tp_file")->where("short_str",$item['short_str'])->update(["delete_status"=>3]);
                            echo ">> id:".$item['id']."-".$item['short_str']." File_size:".$item['file_size']." -  File deleted successfully".PHP_EOL;
                            
                        }else{
                            echo ">> id:".$item['id']."-".$item['short_str']." File deleted fail".PHP_EOL;
                        }
                    }else{
                        //如果服务器上返回file_data less than 1则说明服务器上不存在此文件，将delete_status状态值设置为1
                        if($json_data["status"] == 300 && $json_data["message"] == "file_data less than 1"){
                            Db::table("tp_file")->where("short_str",$item['short_str'])->update(["delete_status"=>1]);
                        }
                        echo ">> id:".$item['id']."-".$item['short_str']." File data length less than 1".", Error, please try again later.".PHP_EOL;
                    }
                

                    sleep(1);
                }catch(\Exception $e){
                    var_dump($e);
                    sleep(10);
                }



            }
            echo ">> Running success, sleep(24hours)".PHP_EOL;
            sleep(60*60*24);
        }
    }
}
