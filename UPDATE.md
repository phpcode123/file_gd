## 版本更新说明

## v1.3.3  -- 20231009
* [Edited]首页hash_str验证时间改为12小时，之前是60分钟，避免用户上传时间过久导致的提交失败。
* [Edited]file_gd_cdn的切片上传目录由/tmp改为public_path()."tmp/"目录，且完善了delete_method()方法，即当用户点击取消上传后会删除./tmp/*.patch.* 匹配部分，此可避免系统更目录/tmp全部被占满。

## v1.3.2  -- 20231003
* [Added]首页提交按钮设置只能提交一次，避免重复提交。

## v1.3.1  -- 20231002
* [Added]后台数据统计页面将蜘蛛单独剥离统计。
       1、tp_http_referer增加is_spider表项用于统计蜘蛛；
       2、后台统计数据页面修改；
       3、前台File.php控制器修改相关统计逻辑
* [Edited]修改了一些逻辑细节：将user_language为none的客户端is_spider状态设置为1，即判定为蜘蛛。

## v1.3  -- 20231001
* [Edited]首页上传插件由Jquery-file-upload改为filepond(花费一周左右时间)
* [Edited]file_gd_cdn的Upload.php也改变了逻辑
* [Added]首页提交按钮新增在文件处理完成后才能提交，避免用户在文件未上传完之前点提交按钮，日志中总有首页错误记录。


## v1.2.6 --20230926
* [Added]增加后台手工通过api删除远程cdn server上的文件
* [Edited]全站链接url新增token、timestamp参数，用于防盗链，防止用户直接访问链接地址。直接下载大的文件非常占用服务器带宽，带宽几乎直接拉满。
* [Edited]cdn服务器上的下载方式改变，以前是用户直接访问源网址，修改为使用php token下载地址，避免用户直接访问资源盗链。

## v1.2.5 --20230924
* [Added]前台file、archive页面增加了图片lazy懒加载功能，避免出现页面加载图片时过慢，导致用户等待时间长
* [Added]前台archive生成逻辑中增加了文件图片title关键词判断，用于控制archive的display开关
* [Editer]将tp_file_archive file_id由varchar(1000)改为varchar(2000)

## v1.2.4 --20230922
* [Added]后台和前台修复了一些bug


## v1.2.4 --20230920
* [Added]show\down\archive 底部增加Report按钮，新增伪静态一条，tp_report表新增表项reason。
* [Fixed]后台修改了一些report页，并美化了click/list的report数量显示，更方便阅读。
* [Fixed]redis_index command修复一些逻辑。


## v1.2.3 --20230919
* [Fixed]修复了一些后台bug
* [Edited]前台仅index加载google adsense后台代码

## v1.2.2 --20230918
* [Added]后台file_list模板增加了file的archive显示
* [Edited]前台逻辑将page_from修改为file_show、file_down、file_archive


## v1.2.2 --20230917
* [Added]凡是图片页面自动将display_ad设置为0
* [Added]增加file_name黑名单关键词，凡是匹配到了黑名单关键词就将display_ad设置为0


## v1.2.1 --20230915
* [Added]新增文件hard_link项，主要用于文件去重复，文件通过file_hash来定位，上传多个相同文件时只增加hard_link值，避免浪费服务器储存空间。当多个相同文件，用户删除文件时只减少file_hash值。此项多文件增值灵感来源于linux下文件软链接原理。
       去掉了file_gd_cdn和file_gd数据库里无用的表项，如:file_key  全部改为file_hash定位文件

## v1.2.0 --20230911
* [Added]数据库增加adsense表和相关编辑页，用于adsense账号申请。

## v1.1.5 --20230901
* [Added]file edit界面增加一键拉黑当前file_hash
* [Added]增加根据id_str批量设置404和禁止文件下载，is_404=1\delete_status=2
* [Added]将.bat后缀文件列为风险提示，警示用户下载

## v1.1.4 --20230830
* [Added]增加tp_malicious_file表，根据file_hash禁止用户上传已拉黑的文件，当匹配file_hash时则将delete_status值设置为2，start页面禁止用户下载
* [Added]file show页面增加black_extension禁止后缀，如当出现.apk或者.exe后缀时提示用户要当心有病毒
* [Edited]将url中的start下载改为down
* [Added]cdn的upload增加exe和apk后缀的判断，在文件后缀后增加下划线“_“，避免可执行文件。

## v1.1.3 --20230823
* [Added]前台增加了index_click访问统计以及后台index_click数据分析列表,新建表tp_index_log
* [Added]tp_file表增加country字段，方便后台查看file_list数据时分析
* [Added]增加index上传文件时的错误统计日志，/public/logs/index_error_log.txt


## v1.1.2 --20230820
* [Edited]前台archive页面size提示更改
* [Added]后台file页面增加short_str来源链接和查询表单Input

## v1.1.1 --20230723
* [Edited]更改file和archive页面的字符串长度分别为6位和5位，之前是8位和7位


## v1.1.0 --20230713 
* [Edited]从服务器有远程请求主服务器short_str改为从服务器主动生成file_key(file_hash),由从服务器file_key(file_hash)来确认定位文件。

## v1.0.0 --20230705   
* [Added]File Uploader初始版本上线开发, 主服务器端