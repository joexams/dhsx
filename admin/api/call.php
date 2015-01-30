<?php 
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(dirname(dirname(__FILE__))."/conn.php");
switch (ReqStr('action')){
	case 'Login': Login();break;
	case 'Out': Out();break;
	default:  main();
}

function Login() {//---------------------------------------------------------------------提交页面登陆
	global $cookiepath,$cookiedomain;
	$key = API_PWD;
	$time = ReqNum('time');
	$code = ReqStr('code');
	
	list($adminWebName,$adminWebPower,$sign_f) = explode("@#$%", authcode($code, 'DECODE'));
	$sign_u = md5("$adminWebName$time$adminWebPower$key");

	
	if($sign_f == $sign_u){
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		if (setcookie('game_auth_admin', authcode($adminWebName."@#$%".$adminWebPower, 'ENCODE'),0,$cookiepath,$cookiedomain)){
			echo 1;
		}else{
			echo 0;
		}
	}else{
		echo 0;
	}

}
function out() {//---------------------------------------------------------------------退出	
	global $cookiepath,$cookiedomain; 
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	setcookie('game_auth_admin', '', -86400,$cookiepath,$cookiedomain);
	header('location:login.php');
}
?>