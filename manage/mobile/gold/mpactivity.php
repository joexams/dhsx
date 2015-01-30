<?php 
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);
require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');
require(ROOT_PATH.DIRECTORY_SEPARATOR.'gold/OpenApiV3.php');

if(!get_magic_quotes_gpc()) {
	$_GET = ext_addslashes($_GET);
}

$openid      = $_GET['openid'];//用户名
$appid       = $_GET['appid'];
$ts          = $_GET['ts'];
$payitem     = $_GET['payitem'];
$discountid  = $_GET['discountid'];
$token       = $_GET['token'];
$billno      = $_GET['billno'];
$version     = $_GET['version'];
$zoneid      = $_GET['zoneid'];
$providetype = $_GET['providetype'];
$sig         = $_GET['sig'];
$ip 		 = ip();	//来路IP	

if (!$openid || !$appid || !$ts || !$payitem  || !$discountid || !$token || !$billno || !$sig || !$zoneid) retjson(4, 'get null');

// 蓝钻大礼包号(PackageID)：1690598208PID201209171338466901
// 年费蓝钻大礼包(PackageID)：1690598208PID201209171338472068
$package = array(
	'1690598208PID201209171338466901' => array(
		'giftid' => 1443,
		'item'	 => array(
			array('item_id'=>1441, 'level'=>1, 'number'=>1),
			array('item_id'=>1332, 'level'=>4, 'number'=>4),
			array('item_id'=>1326, 'level'=>1, 'number'=>4),
			array('item_id'=>1328, 'level'=>1, 'number'=>10),
		),
		'message' => '',
	),
	'1690598208PID201209171338472068' => array(
		'giftid' => 1444,
		'item'	 => array(
			array('item_id'=>1442, 'level'=>1, 'number'=>1),
			array('item_id'=>1332, 'level'=>4, 'number'=>10),
			array('item_id'=>1327, 'level'=>5, 'number'=>1),
			array('item_id'=>1326, 'level'=>1, 'number'=>10),
			array('item_id'=>1369, 'level'=>1, 'number'=>10),
		),
		'message' => '',
	),
);

if (!array_key_exists($discountid, $package)) retjson(4, 'PackageID No Exists');

$domain = 's'.$zoneid.'.app100616996.qqopenapp.com';//组合成游戏服地址
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

$verifysig = SnsSigCheck::verifySig('get', $url_path, $_GET, $key.'&', $sig);
if (!$verifysig) retjson(4, 'sig error');

//------------------------获取充值定单是否存在------------------------------------
$paydata = $db->fetch_first("select success from pay_gift_data where cid = '$cid' and oid = '$billno'");
if ($paydata) {	//有定单记录
	if ($paydata['success']) {	//成功充过值
		retjson(0, 'ok');
	}else{	//待充状态
		retjson(4, 'billno error');	
	}
}

//判断是否充值过
$sourcesid = $sid_old > 0 ? $sid_old : $sid;
$sourceopenid = $openid;
$isexists = $db->fetch_first("select * from pay_gift_data where cid='$cid' and sid='$sourcesid' and username='$openid' and packageid='$discountid'");
if ($isexists) retjson(0, 'ok');


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

$db->query("insert into pay_gift_data(cid,sid,player_id,username,nickname,packageid,oid,success,sign,ip,dateline) values ('$cid','$sourcesid','$player_id','$sourceopenid','$nickname','$discountid','$billno',0,'$sig','$ip','$dateline');");
$pid = $db->insert_id();

if (!$pid) retjson(4, 'statement exec error');

$callback = api_admin::add_player_super_gift($player_id, 3, 0, 0, array(), array(), $package[$discountid]['giftid'], $package[$discountid]['message'], $package[$discountid]['item'], array(), array());
if ($callback['result'] == 1) {
	$db->query("update pay_gift_data set success=1 where pid='$pid'");
	retjson(0, 'ok');
}

retjson(4, 'Call Api failed');

function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);

	echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit();
}