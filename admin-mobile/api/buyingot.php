<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(dirname(dirname(__FILE__))."/conn.php");

//----------------------------转接从用户中心发来的充值接口--------------------------------------------

$ipallow = API_IPALLOW; //允许IP
$time = ReqNum('time');
$code = ReqStr('code');
$ip_f = getIp();//来路IP	
list($username,$money,$ingot,$transactionid,$sign_f) = explode("@#$%", authcode($code, 'DECODE'));

//------------------------检查金额------------------------------------

if ($money <= 0) {//金额有误
	echo 2;
	exit();
}
if ($ingot <= 0) {//获得的元宝有错
	echo 3;
	exit();
}

/*//-----------------------------检查来路IP-------------------------------
if (!in_array($ip_f,$ipallow)) {
	echo 4;
	exit();
}*/
//--------------------------检查通讯密码----------------------------------
$sign_u = md5("username=$username&money=$money&ingot=$ingot&transactionid=$transactionid&time=$time&key=".API_PWD."");
if ($sign_f != $sign_u) {
	echo 5;
	exit();
}

//--------------------已经充过值----------------------------------------
//$ispay = $db->result($db->query("select count(*) from pay_data where transactionid = '$transactionid'"),0); //获取充值定单是否存在
if ($ispay) {//已经充过值
	echo 6;
	exit();
}

//----------------------帐号不存在--------------------------------------
$player = api_admin::find_player_by_username($username);
if (!$player['result']) {
	echo 7;
	exit();
}

//------------------------------------------------------------
//$db->query("insert into pay_data(sid,username,money,ingot,transactionid,pdate) values ('$sid','$username','$money','$ingot','$transactionid',now())") ;//插入充值记录
$msg = api_admin::charge($player['player_id'],$money);//充值累积人民币
if ($msg['result'] == 1) {//充值成功
	$msgingot = api_admin::increase_player_ingot($player['player_id'],$ingot);//加元宝
	if ($msgingot['result'] == 1) {//加元宝成功
		echo 1;
		exit();
	}else{
		echo 0;
		exit();
	}
}else{
	echo 0;
	exit();
}
?>