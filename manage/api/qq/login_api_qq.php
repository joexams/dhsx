<?php
require_once(dirname(__FILE__)."/lib_qq/OpenApiV3.php");

function login_action_qq() { 
	global $the_cookie;

    $server_name = $_SERVER["SERVER_NAME"];
    $openid = $_GET["openid"];
    $openkey = $_GET["openkey"];
    $pf = $_GET["platform"];
    if (isset($_GET['test_server']))
        $server_name = $_GET['test_server'];

    $server = get_server($server_name);

    if (isset($server['stop_msg']) && !isset($_GET['force'])) {
        require_once '/stop.php';
        return;
    }
	
	
	
/**
 * OpenAPI V3 SDK 示例代码
 *
 * @author open.qq.com
 * @copyright © 2011, Tencent Corporation. All rights reserved.
 */



// 应用基本信息
$appid = 100616996;
$appkey = '12731d393543f86736b8d92654d3e6f1';

// OpenAPI的服务器IP 
// 最新的API服务器地址请参考wiki文档: http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3 
$server_name = '113.108.20.23';//正式IP
$server_name = '119.147.19.43';//测试IP，上线需要注释掉
// 用户的OpenID/OpenKey
if(!$openid && !$openkey){
	$openid = '34E40EF93722F3BE4EC2CB0FC85DC79E';
	$openkey = '8C36E912999E549DA5E08DA13AE88C9B';
}

// 所要访问的平台, pf的其他取值参考wiki文档: http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3 
if(!$pf){
	$pf = 'qqgame';
}
$charset  = 'utf-8';
$flag  = 1;

$sdk = new OpenApiV3($appid, $appkey);
$sdk->setServerName($server_name);

$is_login = is_login(&$sdk, $openid, $openkey, $pf, $charset, $flag);
//print_r($is_login);
//echo'<br />';
if ($is_login['ret'] == 0){
	//$user = get_user_info(&$sdk, $openid, $openkey, $pf, $charset, $flag);
	//print_r($user);
	
	
 	//if($user['ret'] == 0)
	//{
		$the_cookie['user'] = encrypt($openid, '{4EE14058-9927}');
		$the_cookie['source'] = isset($_GET['source']) ? $_GET['source'] : '';
		$the_cookie['regdate'] =isset($_GET['regdate']) ? $_GET['regdate'] : '0';
		$the_cookie['non_kid'] =isset($_GET['non_kid']) ? '0' : '1';


/*		if (isset($_GET['force']))
			header("Location: /?force=1"); //&t=".time());
		else
			header("Location: /"); //?t=".time());
		
		exit;*/
		
	//}else{
	//	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />ERR!';
	//	exit();
	//}	
	
	
	
	
}else{
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />登陆验证失败...';
	exit();
}

/**
 * 获取好友资料
 *
 * @param object $sdk OpenApiV3 Object
 * @param string $openid openid
 * @param string $openkey openkey
 * @param string $pf 平台
 * @return array 好友资料数组
 */


	
}




function get_user_info($sdk, $openid, $openkey, $pf, $charset, $flag)//获取用户信息
{
	$params = array(
		'openid' => $openid,
		'openkey' => $openkey,
		'pf' => $pf,
		'charset' => $charset,
		'flag' => $flag,
	);
	
	$script_name = '/v3/user/get_info';

	return $sdk->api($script_name, $params);
}


function is_login($sdk, $openid, $openkey, $pf, $charset, $flag)//检查是否登陆
{
	$params = array(
		'openid' => $openid,
		'openkey' => $openkey,
		'pf' => $pf,
		'charset' => $charset,
		'flag' => $flag,
	);
	
	$script_name = '/v3/user/is_login';

	return $sdk->api($script_name, $params);
}

?>
