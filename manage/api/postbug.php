<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
/*	echo 1;
	exit();*/
//----------------------------提交BUG--------------------------------------------

$nickname = ReqStr('username');//用户名
$nickname = stripslashes($nickname);//反转义昵称
$contents = trim(ReqStr('contents'));//内容

$type = ReqNum('type');//类型
//$sign_f = ReqStr('sign');//来路MD5
$time = ReqNum('time');//UNIX时间戳
$domain = ReqStr('webdomain');//是哪个服二级域名

$ip = getIp();//来路IP
$overtime = 600; #设定300秒超时	


//------------------------类型错误------------------------------------

if (!$type || !$nickname || !$contents  || !$domain  || !$time)//类型错误
{
	echo 2;
	exit();
}


/*//------------------------检查通讯密码------------------------------------
$sign_u = strtoupper(md5($type.'_'.$time.'_'.$domain));//组合MD5
if ($sign_f != $sign_u)
{
	echo 3;
	exit();
}
*///------------------------超时检查------------------------------------
/*
if ($time < time() - $overtime || $time > time()+$overtime){
	echo 4;
	exit();
}*/

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


//------------------------查找要提交的服------------------------------------

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
$player = api_admin::find_player_by_nickname($nickname);
if (!$player['result']) {
	echo 4;//无此玩家
	exit();
}else{
	$user = api_admin::get_username_by_nickname($nickname);//找帐号
	if ($user['result'] == 1) {
		$username = addslashes($user['username'][1]);
	}else{
		echo 4;//无此玩家
		exit();
	
	}
}
$today = strtotime(date('Y-m-d 00:00:00'));//今天数据

$num = $odb->result($odb->query("select count(*) from gm_bug where cid = '$servers[cid]' and sid = '$servers[sid]' and player_id = '$player[player_id]' and submit_time >= '$today'"),0);
if($num >= 10) 
{
	echo 5;//今日已达提交上限
	exit();
}
$username = urldecode($username);//用户名
$contents = urldecode($contents);//内容
$nickname = addslashes($nickname);
$msg = $query = $odb->query("
insert into 
	gm_bug
	(`cid`,`sid`,`player_id`,`username`,`nickname`,`content`,`type`,`gip`,`submit_time`) 
values 
	('$servers[cid]','$servers[sid]','$player[player_id]','$username','$nickname','$contents','$type','$ip','$time')
");

if ($msg) 
{
	echo 1;
	exit();
}else{
	echo 0;//异常错误
	exit();
}
$odb->close();
$db->close();

?>