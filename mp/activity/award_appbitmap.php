<?php
require('common.php');
$now = date('Y-m-d H:i');

$openid 	= isset($_GET['openid']) ? trim($_GET['openid']) : '';
$zoneid 	= isset($_GET['zoneid']) ? intval($_GET['zoneid']) : 0;
$sign 		= isset($_GET['sign']) ? trim($_GET['sign']) : '';
$customflag = isset($_GET['customflag']) ? trim($_GET['customflag']) : 0;
$ip 		= getIp();

$check_sign = md5($openid . '_' . $zoneid . '_' . $customflag .'_{5ce06196-f92a-4310-bf90-8e196fdf76a6}');

if ($sign != $check_sign)  retjson(4, $sign.'-sig error-'.$check_sign);

$domain = 's'.$zoneid.'.app100616996.qqopenapp.com';
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

$sql = "select success from active_gift_appbitmap where sid="$sid" and username='$openid'";
$active = $dblink->getRow($sql);
if ($active) {	//有记录
	if ($active['success']) {
		retjson(0, 'ok');
	}else{	//待状态
		retjson(4, 'billno error');
	}
}

$api_admin = common::load_api_class('api_admin', $rs['server_ver'], 1);
if ($api_admin === false)  retjson(4, 'server version error'.$rs['server_ver']);

api_admin::$SERVER    = $rs['api_server'];
api_admin::$PORT      = $rs['api_port'];
api_admin::$ADMIN_PWD = $rs['api_pwd'];

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
	'sid' => $sid,
	'player_id' => $player_id,
	'username' => $openid,
	'nickname' => $nickname,
	'success' => 0,
	'dateline' => $dateline,
);

$dblink->insert('active_gift_appbitmap' ,$insertarr);
$aid = $dblink->lastInsertId();

if (!$aid) retjson(4, 'statement exec error');

$item_list = array(
	array(
        'item_id' => '1600',
        'level' => '1',
        'number'  => '1'
    ),
    array(
    	'item_id' => '1719',
        'level' => '1',
        'number'  => '10'
    ),
    array(
    	'item_id' => '1768',
        'level' => '1',
        'number'  => '2'
    ),
    array(
    	'item_id' => '1494',
        'level' => '1',
        'number'  => '1'
    ),
);
$callback = api_admin::add_player_active_gift ($player_id, 98, 1252, '恭喜你获得回流礼包！', array(), $item_list, array(), array());
if ($callback['result'] == 1) {
	$dblink->update('active_gift_appbitmap' , array('success' => 1), array('aid' => $aid));
	retjson(0, 'ok');
}

retjson(4, 'Call Api failed');

function retjson($ret = 4, $msg = '') {
	$ret = intval($ret);
    if ($ret != 0) {
		$filepath = './log/';
		$filename = date('Ymd').'_appbitmap_award.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".getIp().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}
    echo json_encode(array('ret'=> $ret,'msg'=> $msg));
	exit();
}
