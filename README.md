## 避免程序滥用，此项目仅为展示，部分核心代码删除。



## 简介

1、FILE.GD英文文件分享管理程序，前后迭代两月有余，目前基本趋于稳定。

2、软件设计开发时较为注重整体效率，程序代码精简、性能强悍，可应付高并发和高负载。

3、软件系免费分享，项目也只适用于国外英文项目，请勿恶意使用。  
  



## 技术相关

* 环境：linux + nginx + mysql + php + redis
* 后端：ThinkPHP6.0
* 前端：Tabler + Bootstrap + jQuery + fileUpload（最新版已改为filePond，支持大文件切片上传）

## 安装使用

* 本程序仅支持LNMP环境，其它环境未测试，建议安装使用linux宝塔。（MYSQL5.7 + PHP7.4 + REDIS）
* 安装：主程序上传到web，在宝塔面板中绑定好主域名，然后修改/file_gd/config/app.php配置文件 

~~~

/file_gd/config/app.php (改大写部分)
...
'install_path'           => '/www/wwwroot/YOUR_SITE_PATH/',  
'admin_url'              => 'https://YOUR_DOMAIN.COM/', 
...
~~~

* 且换到网站根目录，执行命令：composer upgrade && composer update






* MYSQL:建立空数据库，恢复/file_gd/file_gd_20230924.sql文件，然后配置数据库文件
~~~

/file_gd/config/database.php, 并修改下列行(字母大写部分)
...
'database'        => env('database.database', 'YOUR_DATABASE'),
'username'        => env('database.username', 'YOUR_MYSQL_USERNAME'),
'password'        => env('database.password', 'YOUR_MYSQL_PASSWORD'),
...
~~~

* 伪静态文件目录(只做了Nginx适配)：/file_gd/public/.htaccess  内容复制宝塔配置里即可
* 后台地址：https://yoursite.com/admin.php/login/login  用户名：admin  密码：admin888 (默认用户名和密码)

## 定时清理

* 此命令程序会定时清理超过15天无人访问的文件，节约服务器磁盘，天数可自定义，详情请阅读command控制器逻辑部分：/file_gd/app/command/CleanExpiredFile.php

~~~
cd FILE_GD_PATH    //在linux终端切换到FILE_GD目录
screen -S clean_file   //screen 新建命令行窗口挂载
php think clean_file   // 执行监控程序
CTRL+A一起按，然后再按d键  //退出当前screen窗口，再次进入此窗口查看：screen -r clean_file
~~~


## 其它问题

* 如何更改后台登录账号密码？
~~~
修改网站配置文件：/file_gd/config/app.php    （修改大写字母部分即可）
    'admin_username'         => 'YOUR_ADMIN_USERNAME', //后台用户名
    'admin_password'         => 'YOUR_ADMIN_PASSWORD', //后台密码
~~~


* 如何更改后台登录地址？
~~~
1、先将/file_gd/public/admin.php admin.php文件命名为自己想要的 如：loginasadad.php
2、修改网站配置文件：/file_gd/config/app.php    （admin_path地址必须与步骤1修改的一致）如:

'admin_path'             => 'loginasadad.php',//后台入口文件，防止后台被爆破

后台地址：https://yoursite.com/loginasadad.php/login/login
~~~
  


## 项目截图

* 前台    

![](/public/static/images/index.gif)   


* 后台  

![](/public/static/images/4.png)  

![](/public/static/images/8.png)  

![](/public/static/images/9.png)  

![](/public/static/images/5.png)  

![](/public/static/images/6.png)  

![](/public/static/images/7.png)  

  


## CDN SERVER端
[FILE_GD_CDN](https://github.com/PHPCODE123/file_gd_cdn "FILE_GD_CDN")  


  

## 版本更新说明

[请查看UPDATE.md](https://github.com/PHPCODE123/file_gd/blob/master/UPDATE.md "UPDATE")



