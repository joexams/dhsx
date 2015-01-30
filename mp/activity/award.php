<?php
require('common.php');
require('..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'SnsSigCheck.php');

$now = date('Y-m-d H:i');
//if ($now < '2013-10-21 00:00' || $now > '2013-10-31 23:59') exit();

$openid 	= isset($_GET['openid']) ? trim($_GET['openid']) : '';
$appid 		= isset($_GET['appid']) ? trim($_GET['appid']) : '';
$ts 		= isset($_GET['ts']) ? intval($_GET['ts']) : 0;
$payitem 	= isset($_GET['payitem']) ? trim($_GET['payitem']) : '';
$discountid = isset($_GET['discountid']) ? trim($_GET['discountid']) : '';
$token 		= isset($_GET['token']) ? trim($_GET['token']) : '';
$billno 	= isset($_GET['billno']) ? trim($_GET['billno']) : '';
$version 	= isset($_GET['version']) ? trim($_GET['version']) : '';
$zoneid 	= isset($_GET['zoneid']) ? intval($_GET['zoneid']) : 0;
$sig 		= isset($_GET['sig']) ? trim($_GET['sig']) : '';
$providetype = isset($_GET['providetype']) ? intval($_GET['providetype']) : 0;
$ip 		= getIp();

if (empty($openid) || empty($appid) || $ts < 10 || empty($payitem)  || empty($discountid) || empty($token) || empty($billno) || empty($sig)) retjson(4, 'get null');

$verify = SnsSigCheck::verifySig('get', '/activity/award.php', $_GET, $appkey.'&', $sig);
if (!$verify)	retjson(4, $my_sig.'-sig error-'.$sig);

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
	),
	'1001' => array(
		'times'      => 1, 
	 	'discountid' => 'UM121012173440423',
	 	'message' => '恭喜你获得1次蓝钻抽奖机会！',
	),
	'1002' => array(
	 	'times'      => 12, 
	 	'discountid' => 'UM121012173440423',
	 	'message' => '恭喜你获得12次蓝钻抽奖机会！',
	),
	'1003' => array(
	 	'times'      => 1, 
	 	'discountid' => 'UM121012173755347',
	 	'message'    => '恭喜你获得1次黄钻抽奖机会！',
	),
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
	'1012' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得1次蓝钻充值抽奖机会！',
	),
	'1013' => array(
		'times'      => 12, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得12次蓝钻充值抽奖机会！',
	),
	'1015' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得1次黄钻抽奖机会！',
	),
	'1019' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得1次黄钻抽奖机会！',
	),
	'1021' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得会员礼包！',
	),
	'1022' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得年费会员礼包！',
	),
	'1024' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得1次蓝钻抽奖机会！',
	),
	'1026' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得豪华黄钻礼包！',
	),
	'1028' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得豪华蓝钻礼包！',
	),
	'1029' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得会员礼包！',
	),
	'1031' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得1次蓝钻抽奖机会！',
	),
	'1032' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得会员礼包！',
	),
	'1033' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得黄钻礼包！',
	),*/
	'1031' => array(
		'times'      => 1, 
		'discountid' => 'UM130703105525492',
		'message' => '恭喜你获得豪华蓝钻活动礼包！',
	),
	'1034' => array(
		'times'      => 1, 
		'discountid' => 'UM13031517012766',
		'message' => '恭喜你获得黄钻坐骑礼包！',
	),
	'1035' => array(
		'times'      => 1, 
		'discountid' => 'UM130315170222509',
		'message' => '恭喜你获得蓝钻坐骑礼包！',
	),
	'1036' => array(
		'times'      => 1, 
		'discountid' => 'UM130329142426527',
		'message' => '恭喜你获得会员坐骑礼包！',
	),
	'1037' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得蓝钻心愿活动礼包！',
	),
	'1038' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得黄钻礼包！',
	),
	'1039' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得蓝钻活动礼包！',
	),
	'1040' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得会员活动礼包！',
	),
	'1041' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得黄钻礼包！',
	),
	'1042' => array(
		'times'      => 1, 
		'discountid' => 'UM121128141251192',
		'message' => '恭喜你获得会员活动礼包！',
	),
	'1043' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得黄钻礼包！',
	),
	'1044' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得蓝钻活动礼包！',
	),

	'1045' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173755347',
		'message' => '恭喜你获得黄钻礼包！',
	),
	'1046' => array(
		'times'      => 1, 
		'discountid' => 'UM121012173440423',
		'message' => '恭喜你获得蓝钻活动礼包！',
	),
	'1047' => array(
		'times'      => 1, 
		'discountid' => 'UM130703105525492',
		'message' => '恭喜你获得豪华蓝钻活动礼包！',
	),
	'1048' => array(
		'times'      => 1, 
		'discountid' => 'UM130703105525492',
		'message' => '恭喜你获得豪华蓝钻活动礼包！',
	),

);

$token_table = '';
$pay_table   = '';
switch ($discountid) {
	case 'UM130329142426527':
	case 'UM13031517012766':
	case 'UM130315170222509':
	case 'UM121012173440423':
	case 'UM121012173755347':
	case 'UM121128141251192':
	case 'UM130703105525492':
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

try{
	$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);
}catch(Exception $e) {
	retjson(4, 'DataBase Connect Error');
}

$sql = "select * from {$token_table} where token = '$token'";
$trs = $dblink->getRow($sql);
if(!$trs) retjson(4, 'token err');

$domain = 's'.$trs['zoneid'].'.app100616996.qqopenapp.com';
$sql = "select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0";
$cts = $dblink->getRow($sql);
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

$sql = "select A.sid, A.name, A.server_ver,	A.api_server, A.api_port, A.api_pwd, A.open_date, A.test, A.is_new,	B.cid, B.charge_ips, B.key,	B.coins_rate, B.slug from servers A left join company B on A.cid = B.cid $set_where";
$rs = $dblink->getRow($sql);
if($rs) {
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

$sourcesid = $sid_old > 0 ? $sid_old : $sid;
$sourceopenid = $openid;
if ($discountid == 'UM120917133846681') {
	//判断是否充值过
	$sql = "select * from $pay_table where cid='$cid' and sid='$sourcesid' and username='$openid' and packageid='$mpid'";
	$isexists = $dblink->getRow($sql);
	if ($isexists) retjson(0, 'ok');
}else {
	$sql = "select success from $pay_table where cid = '$cid' and oid = '$billno'";
	$paydata = $dblink->getRow($sql);
	if ($paydata) {	//有订单记录
		if ($paydata['success']) {	//成功充过值
			retjson(0, 'ok');
		}else{	//待充状态
			retjson(4, 'billno error');	
		}
	}
}

$api_admin = common::load_api_class('api_admin', $rs['server_ver'], 1);
if ($api_admin === false)  retjson(4, 'server version error'.$rs['server_ver']);

api_admin::$SERVER    = $rs['api_server'];
api_admin::$PORT      = $rs['api_port'];
api_admin::$ADMIN_PWD = $rs['api_pwd'];

if($combined_to) {
	$openid_old = $openid;//先记录合服前的帐号
	if (strpos($name, '_') !== fasle) {
		$s = explode("_",trim($name));
		$suf = $s[1];
	}else{
		$suf = $name;
	}
	$openid = $openid.'.'.$suf;
}

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

$insertarr = array(
	'cid' => $cid,
	'player_id' => $player_id,
	'nickname' => $nickname,
	'discountid' => $discountid,
	'packageid' => $mpid,
	'oid' => $billno,
	'success' => 0,
	'sign' => $sig,
	'ip'  => $ip,
	'dateline' => $dateline,

);
if ($discountid == 'UM120917133846681') {
	$insertarr['sid'] = $sourcesid;
	$insertarr['username'] = $sourceopenid;
}else {
	$insertarr['sid'] = $sid;
	$insertarr['username'] = $openid;
}

$dblink->insert($pay_table ,$insertarr);
$pid = $dblink->lastInsertId();

if (!$pid) retjson(4, 'statement exec error');

switch ($discountid) {
	case 'UM130329142426527':
	case 'UM13031517012766':
	case 'UM130315170222509':
	case 'UM121128141251192':
	case 'UM121012173440423':
	case 'UM121012173755347':
	case 'UM130703105525492':
		//抽奖活动
		$callback = api_admin::add_lottery_times($player_id, $package[$mpid]['times'], $mpid);
		break;
	case 'UM120917133846681':
		//蓝钻大礼包活动
		$callback = api_admin::add_player_super_gift($player_id, $package[$mpid]['gifttype'], 0, 0, 0, 0, $package[$mpid]['giftid'], $package[$mpid]['message'], $package[$mpid]['item'], array(), array());
		break;
}

if ($callback['result'] == 1) {
	$dblink->update($pay_table, array('success' => 1), array('pid' => $pid));
	retjson(0, 'ok');
}

retjson(4, 'Call Api failed');

function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);
    if ($ret != 0) {
		$filepath = './log/';
		$filename = date('Ymd').'_award.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".getIp().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}
    echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit();
}
