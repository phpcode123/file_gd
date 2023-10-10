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

class FileServer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\fileserver')
            ->setDescription('the app\command\fileserver command');
    }

    protected function execute(Input $input, Output $output)
    {
        while(True){
            
            try{
                $server_data = Db::table("tp_server")->order("id","asc")->where("use_status","1")->select();
            }catch(\Exception $e){
                echo "Mysql server error, sleep(30)"; //防止mysql server宕机导致监控程序报错停止
                sleep(30);
                break;
            }
            foreach($server_data as $item){
                //var_dump($item);
                try{
                    //将此command命令运行状态储存在redis中
                    $command_redis_key = Config::get("app.redis_prefix")."command_file_server";
                    Cache::set($command_redis_key,time(),60);//储存60秒,如果redis中没有此值，说明FileServer失效了
                    

                    echo ">> server_id:{$item['id']} ".$item['server_url']."/index.php/api/get_server_status".PHP_EOL;

                    $httpclass = new \app\index\controller\HttpClass($this->app);
                    $http_data = $httpclass->post(
                        $item['server_url']."/index.php/api/get_server_status",
                        ["api_token"=>Config::get("app.api_token")],
                        Config::get("app.api_token")
                    );

                    $json_data = json_decode((string)$http_data,True);
                    //var_dump($json_data);
                    if($json_data['status'] == 200){
                        $disk_status = $json_data['data']['disk_status'];
                        $disk_avail = $json_data['data']['disk_avail'];
                        $server_status = $json_data['data']['server_status'];
                        Db::table("tp_server")->where("id",$item['id'])->update(
                            [
                                "disk_avail" => $disk_avail,
                                "disk_status" => $disk_status,
                                "times" => $item['times']+1,
                                "error_times" => 0,
                                "server_status" => $server_status,
                                "timestamp" => time()
                            
                            ]
                        );
                        echo ">> ".date("Y-m-d H:i:s")." server_id: {$item['id']} disk_status:{$disk_status} server_command_status: {$server_status}".PHP_EOL;
                    }else{
                        echo "status_code:{$json_data["status"]}".PHP_EOL;
                        if($item['error_times'] > 3){
                            $update_data = [
                                "server_status"=>0
                            ];
                            Db::table("tp_server")->where("id",$item['id'])->update($update_data);

                        }else{
                            Db::table("tp_server")->where("id",$item['id'])->update(["error_times"=>$item['error_times']+1]);
                        }
                        

                    }
                }catch(\Exception $e){
                    dump($e);
                    sleep(10);
                    continue;
                }

                sleep(1);
            }
            sleep(5);
             
        }
    }
}
