<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");

$username = ReqStr('username');//用户名
$username = stripslashes($username);//反转义昵称
$time = ReqNum('time');//UNIX时间戳
$domain = ReqStr('webdomain');//是哪个服二级域名
$overtime = 600; #设定300秒超时	


if (!$username || !$domain  || !$time )//参数错误
{
	echo 2;
	exit();
}
//---------判断是否合服-----------------------------------------------------------------------------------------------------------------------------------------------------------
$cts = $db->fetch_first("select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0");
if($cts)
{
	$name = $cts['name'];
	$sid_old = $cts['sid'];
	$combined_to = $cts['combined_to'];
	$set_where = "where sid = '$combined_to'";
}else{
	$set_where = "where FIND_IN_SET('$domain',server) <> 0";
}

/*//------------------------检查通讯密码------------------------------------
$sign_u = strtoupper(md5($username.'_'.$time.'_'.$domain));//组合MD5
if ($sign_f != $sign_u)
{
	echo 3;
	exit();
}*/
/*//------------------------超时检查------------------------------------

if ($time < time() - $overtime || $time > time()+$overtime){
	echo 4;
	exit();
}*/

$query = $db->query("select * from servers $set_where");		
if($db->num_rows($query))
{
	$servers = $db->fetch_array($query);	
}else{
	//$sid = 0;//未知
	echo 3;//来路不明，也给玩家提示成功，省得对方一直提交入库或试探
	exit();		
}

require_once callApiVer($servers['server_ver']);
api_base::$SERVER = $servers['api_server'];
api_base::$PORT   = $servers['api_port'];
api_base::$ADMIN_PWD   = $servers['api_pwd'];

//----------------------帐号不存在--------------------------------------
$player = api_admin::find_player_by_nickname($username);
if (!$player['result']) {
	echo 4;//无此玩家
	exit();
}
//if($servers['slug'] == 'verycd'){
	$set_name = ",is_over,pj";
//}
$query = $odb->query("
select 
	id,
	content,
	submit_time,
	reply_content,
	reply_time
	$set_name

from 
	gm_bug
where
	sid = '$servers[sid]'
	and player_id = '$player[player_id]'
	and status <> -1
order by
	submit_time desc
limit
	20	
	
");
if($odb->num_rows($query))
{
	while($grs = $db->fetch_array($query))
	{	
		$g[] =  $grs;
	}
}else{
	$g =  array();
}

//print_r($g);
echo json_encode($g);
$odb->close();
$db->close();


/*echo '<pre>';
print_r(unserialize($o));
echo '</pre>';
*/
?>