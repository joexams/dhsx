<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
//----------------------------充值接口--------------------------------------------
$time = time();
$token = ReqStr('token');
$zoneid = ReqStr('zoneid');
$sign = ReqStr('sign');
$ip = getIp();//来路IP	
if (!$token || !$zoneid || !$sign) {
	echo 2;
	exit();
}

if ($sign != md5($token.'_'.$zoneid.'_{5ce06196-f92a-4310-bf90-8e196fdf76a6}')) {
	echo 3;
	exit();
}

//------------------------token是否存在------------------------------------
$paytoken = $db->result($db->query("select count(*) from  pay_token where token = '$token'"),0);
if ($paytoken) {
	echo 4;
	exit();

}

$msg = $db->query("insert into pay_token (token,zoneid,time) values ('$token','$zoneid','$time')") ;

if ($msg) {
	echo 1;
}else{
	echo 0;
}
$db->close();

?>
