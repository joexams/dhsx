<?php 
include_once(dirname(__FILE__)."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
@include language(SXD_SYSTEM_LANG);
switch (ReqStr('action')){
	case 'Login': Login();break;
	case 'Out': Out();break;
	default:  Main();
}
function Main() {
	global $adminWebType; 
	if (!empty($_COOKIE['game_auth_manage_qq'])){		
		header("location:$adminWebType.php");
	}else{
		include_once template('login');
	}

}
function Login() {//---------------------------------------------------------------------提交页面登陆
	global $db,$cookiedomain,$cookiepath; 
	$ip = getIp();	
	$Aname=trim(ReqStr('Aname'));
	$ApassWord=trim(ReqStr('ApassWord'));
	$time=time();
	if(empty($Aname) ||empty($ApassWord)){ 
		showMsg("ERROR",'','web');
		exit();
	} 
 	$query = $db->query("select * from admin where adminName = '$Aname'");	
	
	$num = $db->num_rows($query);	
	if($num){ 
	
		$ars = $db->fetch_array($query);
		$adminType = $ars['adminType'];	
		$adminID = $ars['adminID'];	
		$adminAllowLoginIP = $ars['adminAllowLoginIP'] ? explode("|",trim($ars['adminAllowLoginIP'])) : ''; //允许IP
		if ($adminAllowLoginIP && !in_array($ip,$adminAllowLoginIP)) {
			showMsg('LOGINIPERR','login.php','web');
		}elseif(!$ars['adminLock']){
			showMsg('LOGINUESRERR','login.php','web');
		}elseif($ars['adminLoginErr'] >= 3 && $ars['adminLoginErrTime'] > 0 && $ars['adminLoginErrTime']+900 > time()){
			showMsg('LOGINERRNUM','login.php','web');	
		}elseif($ars['adminPassWord'] != md5($ApassWord)){
			$ApassWord = substr_replace($ApassWord,'****','1','4');
			$db->query("update admin set adminLoginErrTime ='$time',adminLoginErr=adminLoginErr+1 where adminName = '$Aname' ");
			$db->query("insert into admin_login_err (adminName,adminPassWord,adminLoingIP,adminLoingTime) values ('$Aname','$ApassWord','$ip',now())");//插入登陆记录		
			showMsg('LOGINPWDERR','login.php','web');
			
		//}elseif(date('Y-m-d',strtotime($ars['adminLoingTime'])+86400*7) < date('Y-m-d') && $ars['adminLock'] == 1 && SXD_SYSTEM_ADMIN_CLOSE == 1){
		//	$db->query("update admin set adminLock = 0,adminLoingTime=now() where adminName = '$Aname' ");//设置锁定
		//	showMsg('您已一周未登陆，帐号已锁定，请联系管理员！','login.php','web');
		}else{
			setcookie('game_auth_manage_qq', authcode($ars['adminID']."\t".$ars['adminName'], 'ENCODE'),0,$cookiepath,$cookiedomain);
			$db->query("update admin set adminLoingIP ='$ip',adminLoginHits=adminLoginHits+1,adminLoginErr=0,adminLoingTime=now() where adminName = '$Aname' ");
			if (SXD_SYSTEM_LOGIN)  $db->query("insert into servers_admin_login (adminID,loginTime,LoingIP,sid) values ('$adminID',now(),'$ip',0)");//插入登陆记录
			header("location:$adminType.php");
		}
	}else{
		showMsg('LOGINUSERNULL','login.php','web');
	}
	$db->close();
}
function Out() {//---------------------------------------------------------------------退出登陆
	global $cookiedomain,$cookiepath;
	
	setcookie('game_auth_manage_qq', '', -86400,$cookiepath,$cookiedomain);
	header('location:login.php');
}
include_once("bot.php");
?>