<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(dirname(dirname(__FILE__))."/conn.php");

//----------------------------赠送元宝接口--------------------------------------------

$ipallow = explode("|",trim(API_IPALLOW)); //允许IP
$time = ReqNum('time');
$code = ReqStr('code');
$ip_f = getIp();//来路IP	
list($username,$ingot,$sign_f) = explode("@#$%", authcode($code, 'DECODE'));


//------------------------检查金额------------------------------------

if ($ingot <= 0) {//获得的元宝有错
	echo 3;
	exit();
}

//-----------------------------检查来路IP-------------------------------
if (!in_array($ip_f,$ipallow)) {
	echo 4;
	exit();
}
//--------------------------检查通讯密码----------------------------------
$sign_u = md5("username=$username&ingot=$ingot&time=$time&key=".API_PWD."");
if ($sign_f != $sign_u) {
	echo 5;
	exit();
}

//----------------------帐号不存在--------------------------------------
$player = api_admin::find_player_by_username($username);
if (!$player['result']) {
	echo 7;
	exit();
}

//------------------------------------------------------------
$msg = api_admin::increase_player_ingot($player['player_id'],$ingot);
if ($msg['result'] == 1) {
	echo 1;
	exit();
}else{
	echo 0;
	exit();
}

?>