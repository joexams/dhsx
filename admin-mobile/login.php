<?php 
include_once(dirname(__FILE__)."/config.inc.php");
include_once(dirname(__FILE__)."/conn.php");
switch (ReqStr('action')){
	case 'login': login();break;
	case 'out': out();break;
	default:  main();
}
function main() {
	global $adminWebName; 
	$AbackUrl=trim(ReqStr('AbackUrl'));
	if (!$AbackUrl) {
		$AbackUrl =  't.php';	
	}
	if (!empty($_COOKIE['qq_game_auth_admin'])){		
		header('location:t.php');
	}else{
		include_once template('login');
	}

}
function login() {//---------------------------------------------------------------------提交页面登陆
	global $db,$cookiepath,$cookiedomain; 
	$ip = getIp();	
	$Aname=ReqStr('Aname');
	$ApassWord=trim(ReqStr('ApassWord'));
	$selectdb = ReqStr('selectdb');

	$mainurl = 't.php';
	if (!empty($selectdb) && $selectdb == 'alpha'){
		$mainurl = 't_alpha.php';
	}
	if(empty($Aname) ||empty($ApassWord)){ 
		showMsg("有项目未填！",'','web');
		exit();
	} 
	if(GAMETEMPLATENAME != $Aname){
		showMsg('帐号不正确！','login.php','web');
		
	}elseif(GAMETEMPLATEPWD != $ApassWord){
		showMsg('密码不正确！','login.php','web');
		
	}else{
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		setcookie('qq_game_auth_admin', authcode(GAMETEMPLATENAME."@#$%".GAMETEMPLATEPOWER, 'ENCODE'),0,$cookiepath,$cookiedomain);
		header("location: ".$mainurl);
	}
	
}
function out() {//---------------------------------------------------------------------退出	
	global $cookiepath,$cookiedomain; 
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	setcookie('qq_game_auth_admin', '', -86400,$cookiepath,$cookiedomain);
	setcookie('qq_game_mysql_admin', '', -86400,$cookiepath,$cookiedomain);
	header('location:login.php');
}
?>