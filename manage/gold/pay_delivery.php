<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(dirname(dirname(__FILE__))."/o.config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once(UCTIME_ROOT."/gold/OpenApiV3.php");

$sdk = new OpenApiV3('100616996', '12731d393543f86736b8d92654d3e6f1');
$sdk->setServerName('openapi.tencentyun.com');
$script_name = '/v3/pay/confirm_delivery';

$runsql = 'SELECT pid,ts FROM pay_data_running order by pid asc limit 50';
$runquery = $db->query($runsql);
while($runrs = $db->fetch_array($runquery)) {
	$pid = $runrs['pid'];
	if ($pid <= 0) continue;
	$paydata = $db->fetch_first("SELECT cid, sid, player_id, username, nickname, amount, coins, oid, success, status, dtime_unix, vip_level_up FROM pay_data WHERE pid='$pid'");
	if (!$paydata) continue;
	if (intval($paydata['success']) == 1)  continue;

	$cid    = $paydata['cid'];
	$sid	= $paydata['sid'];
	$openid = $paydata['username'];
	$pdate 	= date('Y-m-d', $paydata['dtime_unix']);
	$coins	= intval($paydata['coins']);
	$status = $paydata['status'];
	$amount = $paydata['amount'];
	$billno = $paydata['oid'];
	$dtime_unix = $paydata['dtime_unix'];
	$vip_level_up = $paydata['vip_level_up'];

	$delivery = $rtn = array();
	$delivery = $db->fetch_first("SELECT * FROM pay_data_delivery WHERE pid='$pid'");
    if (!$delivery)	continue;
	$deliveryparams = array(
		'openid' => $delivery['openid'],
		'openkey' => $delivery['openkey'],
		'pf' => $delivery['pf'],
		'ts' => $delivery['ts'],
		'payitem' => $delivery['payitem'],
		'token_id' => $delivery['token_id'],
		'billno' => $delivery['billno'],
		'version' => $delivery['version'],
		'zoneid' => $delivery['zoneid'],
		'providetype' => 0,
		'provide_errno' => 0,
		'provide_errmsg' => 'ok',
		'amt' => $delivery['amt'],
		'payamt_coins' => $delivery['payamt_coins'],
		'pubacct_payamt_coins' => $delivery['pubacct_payamt_coins'],
        'userip' => $delivery['userip'],
	);

	$rtn = $sdk->api($script_name, $deliveryparams, 'POST', 'https');
	$db->query("UPDATE pay_data_delivery SET ret = '".$rtn['ret']."',msg = '".$rtn['msg']."' WHERE pid='$pid'");
	if (!in_array($rtn['ret'], array(0, 1061, 1069))) {
		error_log('<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$rtn['ret']."\t".$rtn['msg']."\t".$rtn['is_lost'].PHP_EOL, 3, './log/'.date('Ym').'_confirm_delivery_err.php');
		continue;
	}

	$server = $db->fetch_first("SELECT sid,name,server_ver,api_server,api_port,api_pwd,open_date FROM servers WHERE sid='$sid'");
	if (!$server)  continue;

	$open_date = $server['open_date'];

	callapi::load_api_class($server['server_ver']);
	api_base::$SERVER 	 = $server['api_server'];
	api_base::$PORT   	 = $server['api_port'];
	api_base::$ADMIN_PWD = $server['api_pwd'];

	$player = api_admin::find_player_by_username($openid);
	if (!$player['result']) continue;

	$nickname = api_admin::get_nickname_by_username($openid);
	if (!isset($nickname['nickname'][1]))  continue;

	$player_id = $player['player_id'];
	$nickname  = addslashes($nickname['nickname'][1]);
	if ($vip_level_up == 0) {
		$callback  = api_admin::charge($player_id, $billno, $coins);	//充值累积用于VIP等级提升
		if ($callback['result'] != 1)  continue;
	}

	$db->query("UPDATE pay_data SET vip_level_up = 1,ditme_up = now(),player_id=$player_id,nickname='$nickname' WHERE pid='$pid'"); //确定VIP等级接口执行后再更新
    $amt = round($amount * 10);
	$present_ingot = $coins - $amt;	//赠送元宝
	$charge_ingot  = $coins - $present_ingot;	//实际充值元宝
	$msgingot = api_admin::increase_player_ingot($player_id, $charge_ingot, $present_ingot);	//加元宝
	if ($msgingot['result'] != 1) continue;

	$db->query("UPDATE pay_data SET success = 1,ditme_up = now() WHERE pid='$pid'"); //确定充元宝也成功后在更新
	$db->query("DELETE FROM pay_data_running WHERE pid='$pid'");

	$delivery = array();
	$delivery = $db->fetch_first("SELECT * FROM pay_data_delivery WHERE pid='$pid'");
	$deliveryparams = array(
		'ts' => $delivery['ts'],
		'payitem' => $delivery['payitem'],
		'token_id' => $delivery['token'],
		'billno' => $delivery['billno'],
		'version' => $delivery['version'],
		'zoneid' => $delivery['zoneid'],
		'providetype' => 0,
		'provide_errno' => 0,
		'provide_errmsg' => 'ok',
		'amt' => $delivery['amt'],
		'payamt_coins' => $delivery['payamt_coins'],
		'pubacct_payamt_coins' => $delivery['pubacct_payamt_coins'],
	);

	$script_name = '/v3/pay/confirm_delivery';
	$sdk = new OpenApiV3('100616996', '12731d393543f86736b8d92654d3e6f1');
	$sdk->api($script_name, $deliveryparams );


	$sid_old = $combined_to = 0;
    $openid_old = '';
    $qqname = '';
    $oldserver = array();
    $openid_arr = array();
	if (strpos($openid, '.s') !== false) {
		$openid_arr = explode('.', $openid);
		$combined_to = 1;
		$openid_old = $openid_arr[0];
		$qqname = 'qq_'.$openid_arr[1];
		$oldserver = $db->fetch_first("SELECT sid,name FROM servers WHERE name='$qqname'");
		$sid_old = $oldserver['sid'];
	}

	$isnew = 1;
	if (!$status) {
		if ($sid_old > 0) {
			$payscount = $db->result($db->query("select count(*) from pay_player_servers where cid='$cid' and sid='$sid_old' and username='$openid_old'"), 0);
			if (!$payscount) {
				$db->query("INSERT INTO pay_player_servers(cid,sid,username,nickname,player_id,pay_num,amount,last_pay_amount,last_pay_time) VALUES ('$cid','$sid_old','$openid_old','$nickname', '$player_id','1','$amount','$amount','$dtime_unix')");
	            $isnew = 0;
			}else {
				$db->query("UPDATE pay_player_servers SET pay_num=pay_num+1,amount=amount+$amount,last_pay_amount=$amount,last_pay_time='$dtime_unix' WHERE cid='$cid' AND sid='$sid_old' AND username='$openid_old'");
			}
		}else {
			$payscount = $db->result($db->query("select count(*) from pay_player_servers where cid='$cid' and sid='$sid' and username='$openid'"), 0);
			if (!$payscount) {
				$db->query("INSERT INTO pay_player_servers(cid,sid,username,nickname,player_id,pay_num,amount,last_pay_amount,last_pay_time) VALUES ('$cid','$sid','$openid','$nickname', '$player_id','1','$amount','$amount','$dtime_unix')");
	            $isnew = 0;
			}else {
				$db->query("UPDATE pay_player_servers SET pay_num=pay_num+1,amount=amount+$amount,last_pay_amount=$amount,last_pay_time='$dtime_unix' WHERE cid='$cid' AND sid='$sid' AND username='$openid'");
			}
		}
	}

	if (!$isnew)	//记录每日新增充值用户数
	{
		$d = $db->result($db->query("select count(*) from pay_day_new where cid = '$cid' and sid = '$sid' and pdate = '$pdate'"),0);
		if (!$d)
		{
			$db->query("insert into pay_day_new(cid,sid,pdate,new_player) values ('$cid','$sid','$pdate',1)");
		}else{
			$db->query("update pay_day_new set new_player = new_player+1 where cid = '$cid' and sid = '$sid' and pdate = '$pdate'");
		}	
	}


	require_once UCTIME_ROOT.'/mod/'.$server['server_ver'].'/set_api.php';

	// if (strcmp($server['server_ver'], '2012102701') > 0) {
	if ($combined_to > 0) {
		$isexists = $db->result($db->query("SELECT COUNT(*) FROM pay_data_first WHERE cid=$cid AND sid=$sid_old AND username='$openid_old'"),0);
		if (!$isexists) {
			$db->query("INSERT INTO pay_data_first(pid,cid,sid,username,success,dateline) VALUES($pid, $cid, $sid_old, '$openid_old', 0, $dtime_unix);");
			$ret = SetGiftFirstPay($player_id,$cid,$sid,$openid);
			if ($ret > 0) {
				$db->query("UPDATE pay_data_first SET success=1 WHERE pid=$pid");
			}
		}
	}else {
		$isexists = $db->result($db->query("SELECT COUNT(*) FROM pay_data_first WHERE cid=$cid AND sid=$sid AND username='$openid'"),0);
		if (!$isexists) {
			$db->query("INSERT INTO pay_data_first(pid,cid,sid,username,success,dateline) VALUES($pid, $cid, $sid, '$openid', 0, $dtime_unix);");
			$ret = SetGiftFirstPay($player_id,$cid,$sid,$openid);
			if ($ret > 0) {
				$db->query("UPDATE pay_data_first SET success=1 WHERE pid=$pid");
			}
		}
	}
	// }

	//------------------------统计个人充值------------------------------------------------------
	SetPayPlayer($openid,$nickname,$amount,$cid,$sid,$combined_to);
	//------------------------开服前3天充值活动------------------------------------------------------
	if(!$combined_to && date('Y-m-d H:i:s') >= '2012-03-02 10:00:00') SetGiftDays3($player_id,$coins,$open_date);//非合服
	//------------------------送伙伴活动------------------------------------------------------
	// SetAddRole('2012-06-27 00:00:00','2012-06-30 23:59:59',$player_id,$openid,$coins,27,5000);//端午送财神活动
}

retdie(0, 'ok');



function retdie($ret = 4, $msg = '') {
	if ($ret != 0) {
		$filepath = './log/';	//当前目录
		$filename = date('Ym').'_pay_delivery_error_log.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".getIp().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}
	
	exit($msg);
}
