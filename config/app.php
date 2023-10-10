<?php
return [
    // +------------------------------------------------------------------
    // | 网站设置
    // +------------------------------------------------------------------
    //app_name用于后台title显示，最终会显示：{$app_name} Admin Dashboard，取名尽量好区分，避免多个应用时总是误点
    'app_name'               => 'File Uploader',


    //admin后台登录的账号用户名和密码
    'admin_username'         => 'admin',    //后台用户名
    'admin_password'         => 'admin888', //后台密码
    'api_token'              => '28sdasdasdasdasdasdasd3',//api用于通信，此值必须与file_gd_cdn中的一致
    'install_path'           => '/www/wwwroot/YOUR_SITE_PATH/',  //app()->getRootPath()可使用助手函数 root_path()

    //后台相关配置项
    'admin_url'              => 'https://file.gd/', //后台登录网址
    'admin_path'             => 'admin.php',//后台入口文件，防止后台被爆破
    'admin_page_num'         => '50',//后台分页数量
    'email'                  => 'admin@gmail.com',

    //全站底部的年数数字
    'year_num'             => '2023',

    //sitemaps_url_num
    'sitemaps_url_num'     => "5000",


    //用于储存当天click点击数和short_str统计数的天数,单位：天，
    'click_analysis_days'  => 10,

    //服务器升级维护中 ，0为正常,1为维护
    'server_upgrade_status' => 0,
    'server_upgrade_tips'   => 'Server upgrade, please try again after half an hour...',



    //redis 缓存设置
    'redis_host'             => '127.0.0.1',
    'redis_port'             =>  6379,
    'redis_prefix'           => 'file_705_', 



    //black_country  黑名单国家  多个国家使用|隔开
    'black_country' => "",


    //黑名单后缀，用于file show页面提示用户当心下载(飘红)，多个国家使用|隔开
    'black_extension' => "\.(exe|apk|bat|php|reg|cmd|dll|bin)$",

    //文件file_name黑名单关键词，如果包含此类关键词则程序逻辑将display_ad设置为0,多个关键词使用|隔开
    'black_title_keyword' => "girl|sex|pron|cute|asia|1080|HD",


    //contact联系方式黑名单关键词，避免恶意灌水,可以匹配邮箱、联系人姓名、联系内容
    'contact_black_word'   => array("rmikhail1985"," Eric ","no-repl"," Eric,","cloedcolela","Helina Aziz"," nісe mаn "," mу sistеr ","website's design","bode-roesch","battletech-newsletter","SEO","henry","forum",".dk","robot","blueliners",".de","money","mailme","mail-online","nіce mаn","pussy","fuck ","href","http","pertitfer","pouring","mаrried","automatic","@o2.pl","Cryto"," href ","contactform_","Contact us","Telegram","lupamailler"),

    //file_key short_str长度 file为8  archive为7   //更改file页字符串长度为6，archive页字符串长度为5
    // 新建字符串，用于file的show页和archive聚合页字符串长度控制
    'short_str_length'   => 6,
    'archive_str_length' => 5,
  


    //常见蜘蛛User-agent, 注意不要以｜结尾，否则会匹配到所有的数据,｜为或运算符
    'spider_user_agent'      =>  'baiduspider|sogou|google|360spider|YisouSpider|Bytespider|bing|yodao|bot|robot|facebook|meta|twitter|reddit|WhatsApp|tiktok|Dalvik|telegram|crawler|ZaloPC|Zalo|discord|Aloha|CFNetwork|redditbot|HttpClient|tw\.ystudio\.BePTT|CFNetwork|com\.joshua\.jptt|okhttp/4|admantx',
    

    //移动端user_agent 用于程序逻辑判断是否是移动端
    'mobile_user_agent'      =>  'iPhone|Android|ios|mobile',


    //start自动下载页面等待时间，单位秒，默认设置为3
    'start_wait_time'          => 3, 


    //单用户adsense最大展示次数，如果超过此次数则停止展示,避免用户恶意点击广告
    'adsense_max_display_times' => 3,


    //CleanExpiredFile 清理过期文件最长时间，即超过30天还没有用户访问的文件监控程序自动删除。
    'file_expired_days' => 15,





    // -------------------以下是系统默认配置，如果不知道配置项请勿轻易修改-------------------
    // 默认跳转页面对应的模板文件【新增】
    'dispatch_success_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'  => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',


    'http_exception_template'    =>  [
        // 定义404错误的模板文件地址
        404 =>  app()->getRootPath() . '/public/404.html',
        // 还可以定义其它的HTTP status
        401 =>  app()->getRootPath() . '/public/404.html',
    ],


    // ------------------------------------------------------------------
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ["middleware","command"],
    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => 'Page error! Please try again later.',
    // 显示错误信息
    'show_error_msg'   => false,


];
