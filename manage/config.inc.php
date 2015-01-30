<?php
error_reporting(1);
date_default_timezone_set('Asia/Shanghai');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$stime = microtime(true); //页面执行开始时间 
$mydbhost = 'localhost';//数据库服务器
$mydbuser = 'root';//数据库用户名
$mydbpw = 'ybybyb';//数据库密码
$mydbname = 'game_manage_qq';//数据库名	
$mydbcharset = 'utf8';//数据库编码,不建议修改.
$mypconnect = 0;				// 数据库持久连接 0=关闭, 1=打开

$tplrefresh = 1;	// 模板自动刷新开关 0=关闭, 1=打开

$cookiedomain = ''; // cookie 作用域
$cookiepath = '/';	// cookie 作用路径

//--------------------------------------------
define('IN_UCTIME', TRUE);	
define('UCTIME_ROOT', dirname(__FILE__));
define('UC_KEY','24234hERRTJ@$&*@FZGR·#YK');
require_once UCTIME_ROOT.'/include/db_mysql.class.php';//数据库连接类
require_once UCTIME_ROOT.'/include/global.func.php';//一些字符处理等函数文件
include_once UCTIME_ROOT."/include/api.inc.php";//加载API类库
require_once UCTIME_ROOT.'/o.config.inc.php';//一些字符处理等函数文件
define('RAND_NUM', random(5));	
//-----------------------------------------------------------------------------------------------
$db = new mysql();
$db->connect($mydbhost, $mydbuser, $mydbpw, $mydbname, $mypconnect, true, $mydbcharset);
unset($mydbhost, $mydbuser, $mydbpw, $mydbname,$mydbcharset);

$path_index = pathinfo($_SERVER['PHP_SELF']);
$nowUrl = $path_index["basename"] ;//当前页面文件名

ob_start();
?>
