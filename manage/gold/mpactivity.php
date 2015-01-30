<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");

$now = date('Y-m-d H:i');
if ($now < '2012-10-12 00:00' || $now > '2012-10-24 23:59') exit();

include_once(UCTIME_ROOT."/conn.php");
require_once(UCTIME_ROOT."/gold/OpenApiV3.php");
$adminWebType = 's';

$openid      = ReqStr('openid');//用户名
$appid       = ReqStr('appid');
$ts          = ReqNum('ts');
$payitem     = ReqStr('payitem');
$discountid  = ReqStr('discountid');
$token       = ReqStr('token');
$billno      = ReqStr('billno');
$version     = ReqStr('version');
$zoneid      = ReqNum('zoneid');
$providetype = ReqNum('providetype');
$sig         = ReqStr('sig');
$ip 		 = getIp();	//来路IP
if (!$openid || !$appid || !$ts || !$payitem  || !$discountid || !$token || !$billno || !$sig) retjson(4, 'get null');

$package = array(
	/*'1443' => array(
		'gifttype' => 27,
		'giftid' => 1443,
		'item'	 => array(
			array('item_id'=>1441, 'level'=>1, 'number'=>1),
			array('item_id'=>1332, 'level'=>1, 'number'=>4),
			array('item_id'=>1326, 'level'=>1, 'number'=>4),
			array('item_id'=>1328, 'level'=>1, 'number'=>10),
		),
		'discountid' => 'UM120917133846681',
		'message' => '恭喜你获得蓝钻大礼包！',
	),
	'1444' => array(
		'gifttype' => 28,
		'giftid' => 1444,
		'item'	 => array(
			array('item_id'=>1442, 'level'=>1, 'number'=>1),
			array('item_id'=>1332, 'level'=>1, 'number'=>10),
			array('item_id'=>1327, 'level'=>1, 'number'=>1),
			array('item_id'=>1326, 'level'=>1, 'number'=>10),
			array('item_id'=>1369, 'level'=>1, 'number'=>10),
		),
		'discountid' => 'UM120917133846681',
		'message' => '恭喜你获得年费蓝钻大礼包！',
	),*/
	// '1001' => array(
	// 	'times'      => 1, 
	// 	'discountid' => 'UM121012173440423',
	// 	'message' => '恭喜你获得1次蓝钻抽奖机会！',
	// ),
	// '1002' => array(
	// 	'times'      => 12, 
	// 	'discountid' => 'UM121012173440423',
	// 	'message' => '恭喜你获得12次蓝钻抽奖机会！',
	// ),
	// '1003' => array(
	// 	'times'      => 1, 
	// 	'discountid' => 'UM121012173755347',
	// 	'message'    => '恭喜你获得1次黄钻抽奖机会！',
	// ),
	
	'1009' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得1次蓝钻抽奖机会！',
	),
	'1010' => array(
		'times'      => 12, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得12次蓝钻抽奖机会！',
	),
	'1006' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message'    => '恭喜你获得1次黄钻抽奖机会！',
	),
);

$token_table = '';
$pay_table   = '';
switch ($discountid) {
	case 'UM121012173440423':
	case 'UM121012173755347':
		//抽奖活动
		$token_table = 'pay_lottery_token';
		$pay_table   = 'pay_lottery_data';
		break;
	case 'UM120917133846681':
		//蓝钻大礼包活动
		$token_table = 'pay_gift_token';
		$pay_table   = 'pay_gift_data';
		break;
}

if (empty($token_table)) retjson(4, 'mp_id not valid');


$activity = explode('*', $payitem);
$mpid = $activity[0];
if (!array_key_exists($mpid, $package)) retjson(4, 'PackageID No Exists');

$paydata = $db->fetch_first("select success from {$pay_table} where oid='$billno'");
if ($paydata) {	
	if ($paydata['success']) {
		retjson(0, 'ok');
	}else{
		retjson(4, 'billno error');	
	}
}


$trs = $db->fetch_first("select * from {$token_table} where token = '$token'");
if(!$trs) retjson(4, 'token err');

$domain = 's'.$trs['zoneid'].'.app100616996.qqopenapp.com';
$cts = $db->fetch_first("select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0");
$sid_old = 0;
if($cts)
{
	$name = $cts['name'];
	$sid_old = $cts['sid'];
	$combined_to = $cts['combined_to'];
	
	$set_where = "where A.sid = '$combined_to'";
}else{
	$set_where = "where FIND_IN_SET('$domain',A.server) <> 0";
}

$query = $db->query("
	select 
		A.sid,
		A.name,
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
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
	$key = $rs['key']; //密钥
	$slug = $rs['slug']; //标示	
}else{
	retjson(4, 'server null');
}

$params = $_GET;
unset($params['sig']);
foreach ($params as $k => $v) {
    $params[$k] = sigencodeValue($v);
}
$my_sig = SnsSigCheck::makeSig("get", "/gold/mpactivity.php", $params, $key.'&');
if ($my_sig != $sig) retjson(4, $my_sig.'-sig error-'.$sig);

//------------------------获取充值定单是否存在------------------------------------

$sourcesid = $sid_old > 0 ? $sid_old : $sid;
$sourceopenid = $openid;
if ($discountid == 'UM120917133846681') {
	//判断是否充值过
	$isexists = $db->fetch_first("select * from {$pay_table} where cid='$cid' and sid='$sourcesid' and username='$openid' and packageid='$mpid'");
	if ($isexists) retjson(0, 'ok');
}else {
	$paydata = $db->fetch_first("select success from {$pay_table} where cid = '$cid' and oid = '$billno'");
	if ($paydata) {	//有订单记录
		if ($paydata['success']) {	//成功充过值
			retjson(0, 'ok');
		}else{	//待充状态
			retjson(4, 'billno error');	
		}
	}
}

require_once callApiVer($rs['server_ver']);
api_base::$SERVER    = $rs['api_server'];
api_base::$PORT      = $rs['api_port'];
api_base::$ADMIN_PWD = $rs['api_pwd'];


if($combined_to) {
	$openid_old = $openid;//先记录合服前的帐号
}
$openid = CombinedUser($openid,$name,$combined_to);

//---
$player = api_admin::find_player_by_username($openid);
if (!$player['result']) {
	retjson(4, 'openid null');
}
$nickname = api_admin::get_nickname_by_username($openid);
if (!isset($nickname['nickname'][1])) {	
	retjson(4, 'openid null');
}
$player_id = $player['player_id'];	//获取玩家ID

$nickname = addslashes($nickname['nickname'][1]);
$dateline = time();
$db->query("insert into $pay_table(cid,sid,player_id,username,nickname,discountid,packageid,oid,success,sign,ip,dateline) values ('$cid','$sourcesid','$player_id','$sourceopenid','$nickname','$discountid','$mpid','$billno',0,'$sig','$ip','$dateline');");
$pid = $db->insert_id();

if (!$pid) retjson(4, 'statement exec error');


switch ($discountid) {
	case 'UM121012173440423':
	case 'UM121012173755347':
		//抽奖活动
		$callback = api_admin::add_lottery_times($player_id, $package[$mpid]['times'], $mpid);
		break;
	case 'UM120917133846681':
		//蓝钻大礼包活动
		$callback = api_admin::add_player_super_gift($player_id, $package[$mpid]['gifttype'], 0, 0, 0, 0, $package[$mpid]['giftid'], $package[$mpid]['message'], $package[$mpid]['item'], array(), array());
		break;
}

if ($callback['result'] == 1) {
	$db->query("update {$pay_table} set success=1 where pid='$pid'");
    $db->query("delete from {$token_table} where token='$token'");
	retjson(0, 'ok');
}

retjson(4, 'Call Api failed');

function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);
    if ($ret != 0) {
		$filepath = './log/';
		$filename = date('Ymd').'_mplog.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".getIp().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}
    echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit();
}

function sigencodeValue($value) {
    $rst = '';
    $len = strlen($value);
    for ($i=0; $i<$len; $i++)
    {
        $c = $value[$i];
        if (preg_match ("/[a-zA-Z0-9!\(\)*]{1,1}/", $c))
        {
            $rst .= $c;
        }
        else
        {
            $rst .= ("%" . sprintf("%02X", ord($c)));                                                                                                               }   
     }
     return $rst;
} 
