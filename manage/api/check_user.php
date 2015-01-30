<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");

//----------------------------检查拥护是否存在接口--------------------------------------------

$username = ReqStr('user');//用户名
$domain = ReqStr('domain');//充的是哪个服二级域名
$sign_f = ReqStr('sign');//来路MD5

//---------判断是否合服-----------------------------------------------------------------------------------------------------------------------------------------------------------
$cts = $db->fetch_first("select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0");
if($cts)
{
	$name = $cts['name'];
	$combined_to = $cts['combined_to'];
	$set_where = "where A.sid = '$combined_to'";
}else{
	$set_where = "where FIND_IN_SET('$domain',A.server) <> 0";
}

//------------------------获取运营商及服务器数据------------------------------------
$query = $db->query("
	select 
		A.sid,
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name,		
		A.is_new,
		B.key
	from 
		servers A 
		left join company B on A.cid = B.cid 
		$set_where
	");		
if($db->num_rows($query))
{
	$rs = $db->fetch_array($query);	
}else{
	echo 2;//服务器不存在
	exit();
}

//------------------------检查通讯密码------------------------------------

$sign_u = md5($username.'_'.$domain.'_'.$rs['key']);//组合MD5
if ($sign_f != $sign_u) {
	echo 3;
	exit();
}

//------------------------数据操作接口------------------------------------

require_once callApiVer($rs['server_ver']);
api_base::$SERVER = $rs['api_server'];
api_base::$PORT   = $rs['api_port'];
api_base::$ADMIN_PWD   = $rs['api_pwd'];

//----------------------帐号不存在--------------------------------------

$username = CombinedUser($username,$name,$combined_to);
//---

$nickname = api_admin::get_nickname_by_username($username);
if (!$nickname['nickname'][1]) {	
	echo 4;
	exit();
}else{
	echo 1;
	exit();
}

$db->close();

?>