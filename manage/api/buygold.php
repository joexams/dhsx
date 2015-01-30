<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");

//----------------------------充值接口--------------------------------------------

$username = ReqStr('user');//用户名
$coins = ReqStr('gold');//游戏币
$amount = ReqStr('amount');//充值金额
$oid = ReqStr('order');//订单号
$domain = ReqStr('domain');//充的是哪个服二级域名
$sign = ReqStr('sign');//来路MD5
$time = ReqNum('time');//充值时间
$ip = getIp();//来路IP	
$overtime = 300; #设定300秒超时

if (!$username || !$sign || !$coins  || !$amount || !$domain  || !$oid) {
	echo 3;
	exit();
}
//---------判断是否合服-----------------------------------------------------------------------------------------------------------------------------------------------------------
$cts = $db->fetch_first("select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0");
if($cts)
{
	$name = $cts['name'];
	$sid_old = $cts['sid'];
	$combined_to = $cts['combined_to'];
	
	$set_where = "where A.sid = '$combined_to'";
}else{
	$set_where = "where FIND_IN_SET('$domain',A.server) <> 0";
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


$query = $db->query("
	select 
		A.sid,
		A.name,
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name,
		A.open_date,
		A.test,
		A.is_new,
		B.cid,
		B.charge_ips,
		B.key,
		B.coins_rate,
		B.slug
	from 
		servers A 
		left join company B on A.cid = B.cid 
		$set_where
	");		
if($db->num_rows($query))
{
	$rs = $db->fetch_array($query);	
	$open_date = $rs['open_date']; //开服时间	
	$sname = $rs['name']; //代号
	$cid = $rs['cid']; //所属运营
	$sid = $rs['sid']; //服务器编号
	$test = $rs['test']; //是否测试服
	$charge_ips = $rs['charge_ips'] ? explode("|",trim($rs['charge_ips'])) : ''; //允许IP
	$key = $rs['key']; //密钥
	$coins_rate = $rs['coins_rate']; //元宝兑换比率	
	$slug = $rs['slug']; //标示	
}else{
	echo 2;//充值的服务器不存在
	exit();
}

//------------------------检查游戏币------------------------------------
if ($coins <= 0) {
	echo 3;
	exit();
}
//------------------------检查来路IP------------------------------------
if ($charge_ips) {//有设置允许IP才执行
	if (!in_array($ip,$charge_ips)) {
		echo 4;
		exit();
	}
}

//------------------------检查通讯密码------------------------------------

if ($sign != md5($username.'_'.$coins.'_'.$oid.'_'.$domain.'_'.$key)) {
	echo 5;
	exit();
}
//------------------------获取充值定单是否存在------------------------------------
$paydata = $db->fetch_first("select success from pay_data where cid = '$cid' and oid = '$oid'");
if ($paydata) {//有定单记录

	if ($paydata['success']) {//成功充过值

		echo 6;
		exit();
	}else{//待充状态
		echo 0;
		exit();		
	}
}
//----------------------是否关闭充值--------------------------------------
if (SXD_SYSTEM_PAY_CLOSE == 1) {
	echo 8;
	exit();
}

//------------------------数据操作接口------------------------------------

require_once callApiVer($rs['server_ver']);
api_base::$SERVER = $rs['api_server'];
api_base::$PORT   = $rs['api_port'];
api_base::$ADMIN_PWD   = $rs['api_pwd'];

//---
if($combined_to)
{
	$username_old = $username;//先记录合服前的帐号
}
$username = CombinedUser($username,$name,$combined_to);

//---
$player = api_admin::find_player_by_username($username);
if (!$player['result']) {
	echo 7;
	exit();
}
$nickname = api_admin::get_nickname_by_username($username);
if (!isset($nickname['nickname'][1])) {	
	echo 7;
	exit();
}
//------------------------------------------------------------
$dtime = $time ? date('Y-m-d H:i:s',$time) : date('Y-m-d H:i:s');//转为时间
$timenow = $dtime;
$dtime_unix = strtotime($dtime);
$pdate = date('Y-m-d');
$player_id = $player['player_id'];//获取玩家ID
if($test == 1){
	$status = 1;//测试服，充值都是测试
}else{
	$status = $dtime < $open_date  ? 1 : 0;//状态，判断是否测试期
}

$nickname = addslashes($nickname['nickname'][1]);//取用户游戏呢称
//$amount = round($coins/$coins_rate,2);//通过元宝兑换比率转换成当前运营商结算的货币

$present_ingot = $coins-round($amount*10);//赠送元宝
$charge_ingot = $coins-$present_ingot;//实际充值元宝

$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$cid' and sid = '$sid' and username = '$username'"),0); //是否首充
if($combined_to && !$isnew)//如果是合服则判断被合服的充值
{
	$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$cid' and sid = '$sid_old' and username = '$username_old'"),0); //合服前是否首充
}
$db->query("insert into pay_data(cid,sid,player_id,username,nickname,amount,coins,oid,dtime,dtime_unix,success,status,vip_level_up,sign,ip) values ('$cid','$sid','$player_id','$username','$nickname','$amount','$coins','$oid','$dtime','$dtime_unix',0,'$status',0,'$sign_f','$ip_f2')") ;
$msg = api_admin::charge($player_id,$oid,$coins);//充值累积用于VIP等级提升
if ($msg['result'] == 1) {//充值成功
	//$nickname = addslashes($msg['nickname'][1]);//取用户游戏呢称
	$db->query("update pay_data set vip_level_up = 1,ditme_up = now() where  cid= '$cid' and oid = '$oid'");//确定VIP等级接口执行后再更新
	//-----------------------------------------------------------------------------------------------------------------------------------------------------
	
	$msgingot = api_admin::increase_player_ingot($player_id,$charge_ingot,$present_ingot);//加元宝
	if ($msgingot['result'] == 1) {//加元宝成功
		$db->query("update pay_data set success = 1,ditme_up = now() where cid = '$cid' and oid = '$oid'");//确定充元宝也成功后在更新
		
		//------------------------------------------------------------------------------		
		require_once UCTIME_ROOT.'/mod/'.$rs['server_ver'].'/set_api.php';
		if (!$isnew)//如果第一次冲
		{
			//------------------------------记录每日新增充值用户数-----------------------------
			if (!$status)//并且是非测试其
			{
				$d = $db->result($db->query("select count(*) from pay_day_new where cid = '$cid' and sid = '$sid' and pdate = '$pdate'"),0);
				if (!$d)
				{
					$db->query("insert into pay_day_new(cid,sid,pdate,new_player) values ('$cid','$sid','$pdate',1)");
				}else{
					$db->query("update pay_day_new set new_player = new_player+1 where cid = '$cid' and sid = '$sid' and pdate = '$pdate'");
				}
			}
			
		}
		if(!$combined_to && date('Y-m-d H:i:s') >= '2012-03-02 10:00:00') SetGiftDays3($player_id,$coins,$open_date);//非合服，开服前3天充值活动
		//------------------------统计个人充值------------------------------------------------------
		SetPayPlayer($username,$nickname,$amount,$cid,$sid,$combined_to);
		//------------------------送伙伴活动------------------------------------------------------
		if($domain == 'sxd-qq.1234n.com') {//测试服才执行
			SetAddRole('2012-06-18 00:00:00','2012-06-30 23:59:59',$player_id,$username,$coins,27,3880);//端午送财神活动
		}	
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
$db->close();

?>
