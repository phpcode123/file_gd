<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'redis_index'  => 'app\command\RedisIndex',
        'file_server'  => 'app\command\FileServer',
        'clean_file'  => 'app\command\CleanExpiredFile',
  
    ],
];
