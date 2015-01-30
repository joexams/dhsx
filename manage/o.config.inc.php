<?php
$odbhost = $mydbhost;//数据库服务器
$odbuser = $mydbuser;//数据库用户名
$odbpw = $mydbpw;//数据库密码
$odbname = 'game_other';//数据库名	
$odbcharset = $mydbcharset;//数据库编码,不建议修改.
$oconnect = $mypconnect;// 数据库持久连接 0=关闭, 1=打开

$odb = new mysql();
$odb->connect($odbhost, $odbuser, $odbpw, $odbname, $oconnect, true, $odbcharset);
unset($odbhost, $odbuser, $odbpw, $odbname,$odbcharset);
?>
