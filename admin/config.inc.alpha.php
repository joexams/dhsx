<?php
	//error_reporting(0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	date_default_timezone_set('Asia/Shanghai'); 
	$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$stime = microtime(true); //页面执行开始时间 
	define('GAMETEMPLATENAME','uctime');//模版后台帐号
	define('GAMETEMPLATEPWD','uctime2208755');//模版后台密码
	define('GAMETEMPLATEPOWER','t');//模版后台帐号权限
	//--------------------------------------------

	define('IN_UCTIME', TRUE);	
	define('UCTIME_ROOT', dirname(__FILE__));
	define('UC_KEY','24234hERRTJ@$&*@FZGR·#YK');
	define('QQ_ADMIN','/Ha/index.php');
	define('LEFT_MENU_SHOW_ALL',true);
	require_once UCTIME_ROOT.'/include/global.func.php';//一些字符处理等函数文件
	require_once UCTIME_ROOT.'/include/db_mysql.class.php';//数据库连接类
	require_once UCTIME_ROOT.'/include/db_global.php';//数据库表调用文件
	
	$mydbhost = 'localhost';//数据库服务器
	$mydbuser = 'root';//数据库用户名
	$mydbpw = 'ybybyb';//数据库密码
	$mydbname = 'gamedb_qq_alpha';//数据库名

	$database = array(
		'admin' => array(
				'dbhost' => 'localhost',
				'dbuser' => 'root',
				'dbpwd'  => 'ybybyb',
				'dbname' => 'gamedb_qq_admin',
			),
	);
	$menudbhost =  $database['admin']['dbhost'];
	$menudbuser = $database['admin']['dbuser'];
	$menudbpw   = $database['admin']['dbpwd'];
	$menudbname = $database['admin']['dbname'];

	$table_db = $database[$databasekey]['dbname'];
	
	$olddbname = $mydbname;//检测新旧数据库用
	$mydbcharset = 'utf8';//数据库编码,不建议修改.
	$mypconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
	
	$tplrefresh = 1;	// 模板自动刷新开关 0=关闭, 1=打开
	
	$cookiedomain = ''; // cookie 作用域
	$cookiepath = '/';	// cookie 作用路径
	
	//--------------------------------------------
	if ($t_webname)
	{
		define('GAMETITLENAME',$t_webname.'-模版数据后台');//服务器标题
	}else{
		define('GAMETITLENAME','模版数据后台');//服务器标题
		
	}
	//-----------------------------------------------------------------------------------------------
	$db = new mysql();
	$db->connect($mydbhost, $mydbuser, $mydbpw, $mydbname, $mypconnect, true, $mydbcharset);
	$menu_db = new mysql();
	$menu_db->connect($menudbhost, $menudbuser, $menudbpw, $menudbname, $mypconnect, true, $mydbcharset);
	unset($mydbhost, $mydbuser, $mydbpw, $mydbname,$mydbcharset);
	
	//	unset($menudbhost, $menudbuser, $menudbpw, $menudbname);
	
	
	$path_index = pathinfo($_SERVER['PHP_SELF']);
	$nowUrl = $path_index["basename"] ;//当前页面文件名
	
	ob_start();

?>