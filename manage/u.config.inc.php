<?php
$udbhost = '10.190.237.140:8810';//数据库服务器
$udbuser = 'root';//数据库用户名
$udbpw = 'YuUkD<%PsB(0u]!x';//数据库密码
$udbname = 'qq_log';//数据库名	
$udbcharset = $mydbcharset;//数据库编码,不建议修改.
$uconnect = $mypconnect;// 数据库持久连接 0=关闭, 1=打开

$udb = new mysql();
$udb->connect($udbhost, $udbuser, $udbpw, $udbname, $uconnect, true, $udbcharset);
unset($udbhost, $udbuser, $udbpw, $udbname,$udbcharset);
?>
