<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once(UCTIME_ROOT."/gold/OpenApiV3.php");

$url_path	  = '/gold/pay.php';
$delivery_url_path = '/gold/pay_delivery.php';

$val = var_export($_GET, TRUE);
$openid = ReqStr('openid');
$openkey= ReqStr('openkey');
$pf = ReqStr('pf');
$appid = ReqStr('appid');
$ts = ReqNum('ts');
$payitem = ReqStr('payitem');
$amt = ReqStr('amt');
$token = ReqStr('token');
$billno = ReqStr('billno');
$version = ReqStr('version');
$providetype = ReqStr('providetype');
$payamt_coins = ReqStr('payamt_coins');
$pubacct_payamt_coins = ReqStr('pubacct_payamt_coins');
$sig = ReqStr('sig');
$ip = getIp();

$goldcoupon   = isset($_GET['goldcoupon']) && intval($_GET['goldcoupon']) > 0 ? intval($_GET['goldcoupon']) : 0;
$slivercoupon = isset($_GET['slivercoupon']) && intval($_GET['slivercoupon']) > 0 ? intval($_GET['slivercoupon']) : 0;
$coppercoupon = isset($_GET['coppercoupon']) && intval($_GET['coppercoupon']) > 0 ? intval($_GET['coppercoupon']) : 0;
$payamt_coins = intval($payamt_coins) > 0 ? intval($payamt_coins) : 0;
$pubacct_payamt_coins = intval($pubacct_payamt_coins) > 0 ? intval($pubacct_payamt_coins) : 0;
$oldamt = $amt;
$amt = $amt + ($pubacct_payamt_coins * 10) + ($payamt_coins * 10);

if (!$openid || !$appid || !$ts || !$payitem  || !$amt || !$token || !$billno || !$sig)  retjson(4, 'get null');


$trs = $db->fetch_first("SELECT * FROM pay_token WHERE token = '$token'");
if(!$trs)  retjson(4, 'token err');
$domain = 's'.$trs['zoneid'].'.app100616996.qqopenapp.com';

$cts = $db->fetch_first("SELECT combined_to,sid,`name` FROM servers WHERE FIND_IN_SET('$domain',server) <> 0 AND combined_to > 0");
$sid_old = $combined_to = 0;
if($cts) {
	$name = $cts['name'];
	$sid_old = $cts['sid'];
	$combined_to = $cts['combined_to'];
	
	$set_where = "WHERE A.sid = '$combined_to'";
}else {
	$set_where = "WHERE FIND_IN_SET('$domain',A.server) <> 0";
}

$query = $db->query("SELECT A.sid,A.name,A.server_ver,A.api_server,A.api_port,A.api_pwd,A.open_date,A.test,A.is_new,B.cid,B.charge_ips,B.key,B.coins_rate,B.slug FROM servers A LEFT JOIN company B ON A.cid = B.cid $set_where");		
if($db->num_rows($query)) {
	$rs = $db->fetch_array($query);	
	$open_date = $rs['open_date'];
	$sname = $rs['name'];
	$cid = $rs['cid'];
	$sid = $rs['sid'];
	$test = $rs['test'];
	$key = $rs['key'];
	$coins_rate = $rs['coins_rate'];
	$slug = $rs['slug'];
}else{
	retjson(4, 'server null');
}

$_GET['billno'] = str_replace("-", "%2D", $_GET['billno']);
foreach($_GET  as $k => $value){ 
       $params[$k] = $value;
}
$sig = $params["sig"];
unset($params["sig"]);

$my_sig = SnsSigCheck::makeSig("get", $url_path, $params, $key.'&');
if ($my_sig != $sig)  retjson(4, 'sig error');


$paydata = $db->fetch_first("select success from pay_data where cid = '$cid' and oid = '$billno'");
if ($paydata) {
	if ($paydata['success']) {
		retjson(0, 'ok');
	}else{
		retjson(4, 'billno error');
	}
}

$openid = CombinedUser($openid,$name,$combined_to);
$dtime 		= $ts ? date('Y-m-d H:i:s',$ts) : date('Y-m-d H:i:s');
$timenow 	= $dtime;
$dtime_unix = strtotime($dtime);
$pdate 		= date('Y-m-d');
$player_id  = 0;
$nickname 	= '';
$amount 	= round($amt/100,2);//转换成人民币
$yb 		= explode("*",$payitem);
$coins 		= $yb[1]*$yb[2];//转换成要加的元宝

$present_ingot  = $coins-round($oldamt/10);//赠送元宝
$charge_ingot   = $coins-$present_ingot;//实际充值元宝

if ($test == 1) {
	$status = 1;
}else {
	$status = $dtime < $open_date ? 1 : 0;
}

$db->query("INSERT INTO pay_data(cid,sid,player_id,username,nickname,amount,coins,oid,dtime,dtime_unix,success,status,vip_level_up,sign,ip) VALUES ('$cid','$sid','$player_id','$openid','$nickname','$amount','$coins','$billno','$dtime','$dtime_unix',0,'$status',0,'$sig','$ip')") ;
$pid = $db->insert_id();

if (!$pid) retjson(4, 'pay error');

if ($goldcoupon > 0 || $slivercoupon > 0 || $coppercoupon > 0 || $payamt_coins > 0 || $pubacct_payamt_coins > 0){
	$db->query("INSERT INTO pay_data_detail(oid,amt,goldcoupon,slivercoupon,coppercoupon,payamt_coins,pubacct_payamt_coins,dateline) VALUES ('$billno','$oldamt','$goldcoupon','$slivercoupon', '$coppercoupon','$payamt_coins','$pubacct_payamt_coins','$dtime_unix')");
}
$db->query("INSERT INTO pay_data_running(pid, ts) VALUES($pid, $dtime_unix)");
$db->query("INSERT INTO pay_data_delivery(pid, payitem, token_id, billno, zoneid, providetype, amt, payamt_coins, pubacct_payamt_coins, ts) VALUES($pid, $payitem, $token, $billno, $zoneid, 0, $oldamt, $payamt_coins, $pubacct_payamt_coins, $ts)");
$db->query("delete from pay_token where token = '$token'");
retjson(0, 'ok');



function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);
	if ($ret != 0) {
		$filepath = './log/';
		$filename = date('Ym').'_pay_error_log.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".getIp().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}

	echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit;
}
