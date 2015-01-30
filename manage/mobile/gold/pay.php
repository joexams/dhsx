<?php 
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);
require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');
require(ROOT_PATH.DIRECTORY_SEPARATOR.'gold/OpenApiV3.php');
// $debug = 1;
// if ($debug) {
// 	$_GET['payitem'] = 'G001*10*10';
// 	$_GET['openid'] = 'hello';
// 	$_GET['appid'] = '100616996';
// 	$_GET['ts'] = time();
// 	$_GET['token'] = 'abced';
// 	$_GET['amt'] = '940';
// 	$_GET['billno'] = '1ee2idset6xeee24deuxue2exee33230';
// 	$_GET['version'] = 'v3';
// 	$_GET['providetype'] = '1';
// 	$_GET['sig'] = '1232fdfgdfg3444444';
// 	$_GET['payamt_coins'] = '10';
// 	$_GET['pubacct_payamt_coins'] = '50';
// 	$_GET['goldcoupon'] = '0';
// 	$_GET['slivercoupon'] = '0';
// 	$_GET['coppercoupon'] = '0';

// 	$query_string = array();
// 	foreach ($_GET as $key => $val ) 
// 	{ 
// 	    array_push($query_string, $key . '=' . $val);
// 	}
// 	$_SERVER['QUERY_STRING'] = join('&', $query_string);
// 	$domain = 's0.app100616996.qqopenapp.com';
// }

if(!get_magic_quotes_gpc()) {
	$_GET = ext_addslashes($_GET);
}

$url_path	  = '/gold/pay.php';
$delivery_url_path = '/hooa/gold/pay_delivery.php';

$openid      = $_GET['openid'];
$appid       = $_GET['appid'];
$ts          = intval($_GET['ts']);
$payitem     = $_GET['payitem'];
$amt         = intval($_GET['amt']);
$token       = $_GET['token'];
$billno      = $_GET['billno'];
$version     = $_GET['version'];
$providetype = $_GET['providetype'];
$sig         = $_GET['sig'];
$ip          = ip();

$payamt_coins         = intval($_GET['payamt_coins']) >0 ? intval($_GET['payamt_coins']) : 0;
$pubacct_payamt_coins = intval($_GET['pubacct_payamt_coins']) > 0 ? intval($_GET['pubacct_payamt_coins']) : 0;
$goldcoupon   = isset($_GET['goldcoupon']) && intval($_GET['goldcoupon']) > 0 ? intval($_GET['goldcoupon']) : 0;
$slivercoupon = isset($_GET['slivercoupon']) && intval($_GET['slivercoupon']) > 0 ? intval($_GET['slivercoupon']) : 0;
$coppercoupon = isset($_GET['coppercoupon']) && intval($_GET['coppercoupon']) > 0 ? intval($_GET['coppercoupon']) : 0;
$oldamt = $amt;
$amt    = $amt + ($pubacct_payamt_coins + $payamt_coins) * 10;
if (empty($openid) || empty($appid) || $ts < 1 || empty($payitem)  || $amt < 1 || empty($token) || empty($billno) || empty($sig)) retjson(4, 'get null');

$pubdb = common::load_model('public_model');

// if (!$debug) {
$pubdb->table_name = 'pay_token';
$trs = $pubdb->get_one(array('token'=>$token));
if (!$trs) retjson(4, 'token err');
$domain = 's'.$trs['zoneid'].'.app100616996.qqopenapp.com';//组合成游戏服地址
// }

$pubdb->table_name = 'servers';
$server = $pubdb->get_one("FIND_IN_SET('$domain',server) <> 0 and combined_to > 0", 'combined_to,sid,name');
$wherestr = $name = '';
$sid_old  = $combined_to = 0;
if ($server) {
	$name = $server['name'];
	$sid_old = $server['sid'];
	$combined_to = $server['combined_to'];
	
	$wherestr = "sid='$combined_to'";
}else {
	$wherestr = "FIND_IN_SET('$domain',server) <> 0";
}

$server = $pubdb->get_one($wherestr, 'cid,sid,name,server_ver,api_server,api_port,api_pwd,open_date,test,is_new');
if (!$server) retjson(4, 'server null');
$cid = $server['cid'];

$pubdb->table_name = 'company';
$company = $pubdb->get_one(array('cid'=>$cid), 'cid,charge_ips,key,coins_rate,slug');
if (!$company) retjson(4, 'server null');

$sid       = $server['sid'];
$test      = $server['test'];
$sname     = $server['name'];
$opendate  = $server['open_date'];
$serverver = $server['server_ver'];

$key        = $company['key'];
$coins_rate = $company['coins_rate'];
$slug       = $company['slug'];

// if (!$debug) {
$verifysig = SnsSigCheck::verifySig('get', $url_path, $_GET, $key.'&', $sig);
if (!$verifysig) retjson(4, 'sig error');
// }

$pubdb->table_name = 'pay_data';
$paydata = $pubdb->get_one(array('cid'=>$cid, 'oid'=>$billno), 'success');
if ($paydata) {
	if ($paydata['success']) retjson(0, 'ok');
	retjson(4, 'billno error');
}

$api_admin = common::load_api_class('api_admin', $serverver);
if ($api_admin === false) retjson(4, 'api no exists');

$api_admin::$SERVER    = $server['api_server'];
$api_admin::$PORT      = $server['api_port'];
$api_admin::$ADMIN_PWD = $server['api_pwd'];

if($combined_to) {
	$openid_old = $openid;
	if (strpos($name, '_') !== false) {
		$s = explode('_',trim($name));
		$sname = $s[1];
	}else {
		$sname = $name;
	}
	$openid = $openid.'.'.$sname;
}

$player = $api_admin::find_player_by_username($openid);
if (!$player['result']) retjson(4, 'openid null');

$nickname = $api_admin::get_nickname_by_username($openid);
if (!isset($nickname['nickname'][1])) retjson(4, 'openid null');

$dtime         = date('Y-m-d H:i:s',$ts);
$player_id     = $player['player_id'];	//获取玩家ID
$status        = $test == 1 ? 1 : ($dtime < $opendate ? 1 : 0);	//测试服，充值都是测试, 状态，判断是否测试期
$nickname      = addslashes($nickname['nickname'][1]);	//取用户游戏呢称

$amount        = round($amt/100, 2);	//转换成人民币
$yb            = explode("*", $payitem);
$coins         = $yb[1] * $yb[2];	//转换成要加的元宝

$payinfo = array();
$payinfo['cid']          = $cid;
$payinfo['sid']          = $sid;
$payinfo['player_id']    = $player_id;
$payinfo['username']     = $openid;
$payinfo['nickname']     = $nickname;
$payinfo['amount']       = $amount;
$payinfo['coins']        = $coins;
$payinfo['oid']          = $billno;
$payinfo['dtime']        = $dtime;
$payinfo['dtime_unix']   = $ts;
$payinfo['success']      = 0;
$payinfo['status']       = $status;
$payinfo['vip_level_up'] = 0;
$payinfo['sign']         = $sig;
$payinfo['ip']           = $ip;
$pubdb->insert($payinfo);

if ($goldcoupon > 0 || $slivercoupon > 0 || $coppercoupon > 0 || $payamt_coins > 0 || $pubacct_payamt_coins > 0){
	$payinfo = array();
	$payinfo['oid']                  = $billno;
	$payinfo['amt']                  = $oldamt;
	$payinfo['goldcoupon']           = $goldcoupon;
	$payinfo['slivercoupon']         = $slivercoupon;
	$payinfo['coppercoupon']         = $coppercoupon;
	$payinfo['payamt_coins']         = $payamt_coins;
	$payinfo['pubacct_payamt_coins'] = $pubacct_payamt_coins;
	$payinfo['dateline']             = $ts;

	$pubdb->table_name = 'pay_data_detail';
	$pubdb->insert($payinfo);
}

$method = 'GET';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$port = isset($_SERVER['SERVER_PORT'])? $_SERVER['SERVER_PORT'] : 80;
$fp = fsockopen($host, $port, $errno, $errstr, 30);
if ($fp) {
	$paysig = md5($cid.'_'.$sid_old.'_'.$sig);
	$query_string = '?cid='.$cid.'&sid='.$sid_old.'&paysig='.$paysig.'&';
	$deliverypath = $delivery_url_path.$query_string.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
	$header  = $method.' '.$deliverypath;
	$header .= ' HTTP/1.1'.PHP_EOL;
	$header .= 'Host: '.$host.PHP_EOL;
	$header .= 'Connection:Close'.PHP_EOL.PHP_EOL;

	fwrite($fp, $header);
	fclose($fp);
}
retjson(0, 'ok');

function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);
	if ($ret != 0) {
		$filepath = './log/'; //当前目录
		$filename = date('Ym').'_pay_error_log.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".ip().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}

	echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit();
}