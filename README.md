

## 简介

1、FILE.GD是仿造workupload.com开发的文件分享管理程序，前后迭代两月有余，目前基本趋于稳定。

2、软件设计开发时就较为着重于性能，能精简的代码逻辑部分基本都精简了，希望大家能用的习惯~~

3、软件系免费分享，项目也只适用于国外英文项目，各位大佬千万不要在国内瞎搞（害怕~~）。

   QQ:12391046  邮箱：petersonjames5838@gmail.com

## 技术相关

* 环境：linux + nginx + mysql + php + redis
* 后端：ThinkPHP6.0
* 前端：Tabler + Bootstrap + jQuery + fileUpload（最新版已改为filePond，支持大文件切片上传）

## 安装使用

* 本程序仅支持LNMP环境，其它环境未测试，建议安装使用linux宝塔。（MYSQL5.7 + PHP7.4 + REDIS）
* 安装：将网站目录上传到web，然后在宝塔面板中绑定好主域名，然后是修改/file_gd/config/app.php配置文件 
       网站配置文件：/file_gd/config/app.php (改大写字母配置部分)
~~~

...
'install_path'           => '/www/wwwroot/YOUR_SITE_PATH/',  
'admin_url'              => 'https://YOUR_DOMAIN.COM/', 
...
~~~




* MYSQL:建立空数据库，恢复根目录下的/file_gd/file_gd_20230924.sql文件，然后配置数据库文件
       数据库配置文件：/file_gd/config/database.php, 并修改下列行(字母大写部分)
~~~

...
'database'        => env('database.database', 'YOUR_DATABASE'),
'username'        => env('database.username', 'YOUR_MYSQL_USERNAME'),
'password'        => env('database.password', 'YOUR_MYSQL_PASSWORD'),
...
~~~

* 伪静态文件目录(只做了Nginx适配)：/file_gd/public/.htaccess  内容复制宝塔配置里即可
* 后台地址：https://yoursite.com/admin.php/login/login  用户名：admin  密码：admin888



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
2、修改网站配置文件：/file_gd/config/app.php    （admin_path地址必须与步骤一修改的一致）如:

'admin_path'             => 'loginasadad.php',//后台入口文件，防止后台被爆破
~~~




## CDN端请查看FILE_GD_CDN项目的配置教程