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
use app\index\controller\Otherclass;

class RedisIndex extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\redisindex')
            ->setDescription('the app\command\redisindex command');
    }

    protected function execute(Input $input, Output $output)
    {


        $otherclass= new Otherclass($this->app);
        $count_num = 0;
        while(true){


            $data = Db::table("tp_file")->where("redis_index","0")->order("id","asc")->limit(5000)->select();



            //count_num 用于计数，当大于10次时就跳出当前循环结束程序
            if(count($data) == 0){
                $count_num += 1;

                if($count_num > 3){
                    echo ">> Running success!\n";
                    break;
                }
            }else{
                //归0
                $count_num = 0;
            }
            



            
            if(count($data) > 0){
                for($i=0; $i<count($data); $i++){
                    $id = $data[$i]['id'];
                    $short_str = $data[$i]['short_str'];  //short_str
                    
                    //储存至redis
                    if(Cache::set(Config::get("app.redis_prefix").$short_str, $id)){

                        Db::table("tp_file")->where("id",$id)->update(["redis_index"=>1]);

                        //服务器上输出较慢，1000行才输入一次
                        if($i%1000 == 0){
                            echo ">> id:".$data[$i]['id']." short_str:".$data[$i]['short_str']." set redis success!\n";
                        }
                    }
                }
            }else{
                echo ">> Running success! ".date("Y-m-d H:i:s")."\n";
                break;
            } 
            
        }

    }
}
