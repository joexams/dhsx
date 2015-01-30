<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");

//----------------------------检查拥护是否存在接口--------------------------------------------

$username = ReqStr('user');//用户名
$username_old = ReqStr('user');//用户名
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

if ($rs['is_new'] == 0){
	$username = msubstr($username, 0, 20);
}
//---
$username = CombinedUser($username,$name,$combined_to);
//---

$nickname = api_admin::get_nickname_by_username($username);
if (!$nickname['nickname'][1]) {	
	echo 4;
	exit();
}



$pdbhost = SetToDB($rs['db_server']);//数据库服务器
$pdbuser = $rs['db_root'];//数据库用户名
$pdbpw = $rs['db_pwd'];//数据库密码
$pdbname = $rs['db_name'];//数据库名	
$pdbcharset = 'utf8';//数据库编码,不建议修改.
$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开


$pdb = new mysql();
$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
$p = $pdb->fetch_first("
select 		
	A.nickname,
	A.vip_level,
	B.level as player_level,
	C.last_offline_time
from 
	player A
	left join player_role B on A.id = B.player_id and A.main_role_id = B.id
	left join player_trace C on A.id = C.player_id
where 
	 A.username = '$username'
");
if($p)
{
	echo json_encode(array('username'=>$username_old,'nickname'=>$p['nickname'],'player_level'=>$p['player_level'],'last_offline_time'=>$p['last_offline_time']));
	exit();
}

echo 0;//异常错误
exit();
$pdb->close();
$db->close();
?>