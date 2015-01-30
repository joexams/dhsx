<?php 
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);
require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');
require(ROOT_PATH.DIRECTORY_SEPARATOR.'gold/OpenApiV3.php');

// $debug = 1;

$cid    = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$sid_old= isset($_GET['sid']) ? intval($_GET['sid']) : 0;
$paysig = isset($_GET['paysig']) ? trim($_GET['paysig']) : '';
$sig    = isset($_GET['sig']) ? trim($_GET['sig']) : '';
$token  = isset($_GET['token']) ? trim($_GET['token']) : '';
$billno = isset($_GET['billno']) ? trim($_GET['billno']) : '';
if ($cid < 1 || empty($token) || empty($billno) || empty($paysig) || empty($sig)) retdie(4, 'args no exists');
if ($paysig != md5($cid.'_'.$sid_old.'_'.$sig)) retdie(4, 'paysig no exists');

$pubdb = common::load_model('public_model');
$pubdb->table_name = 'pay_data';

$payinfo = $pubdb->get_one(array('cid'=>$cid, 'oid'=>$billno), 'cid, sid, player_id, username, nickname, amount, coins, oid, success, status, dtime_unix');
if (!$payinfo) retdie(4, 'args no exists');
if (intval($payinfo['success']) == 1) retdie(0, 'ok');

$pubdb->table_name = 'servers';
$server = $pubdb->get_one(array('sid'=>$payinfo['sid']), 'sid,name,server_ver,api_server,api_port,api_pwd,open_date');
if (!$server) retdie(4, 'server no exists');

$api_admin = common::load_api_class('api_admin', $server['server_ver']);
if ($api_admin === false) retdie(4, 'api no exists');

$api_admin::$SERVER    = $server['api_server'];
$api_admin::$PORT      = $server['api_port'];
$api_admin::$ADMIN_PWD = $server['api_pwd'];

$sid	   = $payinfo['sid'];
$player_id = intval($payinfo['player_id']);
$openid    = $payinfo['username'];
$pdate 	   = date('Y-m-d', $payinfo['dtime_unix']);
$coins	   = intval($payinfo['coins']);
$callback  = $api_admin::charge($player_id,$billno,$coins);	//充值累积用于VIP等级提升
if ($callback['result'] != 1) retdie(4, 'charge error');

//充值成功
$pubdb->table_name = 'pay_data';
$pubdb->update(array('vip_level_up'=>1, 'ditme_up'=>date('Y-m-d H:i:s')), array('cid'=>$cid, 'oid'=>$billno));	//确定VIP等级接口执行后再更新

$pubdb->table_name = 'pay_data_detail';
$paydetail = $pubdb->get_one(array('oid'=>$billno), 'oid,amt,payamt_coins,pubacct_payamt_coins');
$amt = isset($paydetail['amt']) ? intval($paydetail['amt']) : 0;
$present_ingot = $coins - round($amt/10);	//赠送元宝
$charge_ingot  = $coins - $present_ingot;	//实际充值元宝
$callbackingot = $api_admin::increase_player_ingot($player_id,$charge_ingot,$present_ingot);	//加元宝
if ($callbackingot['result'] != 1) retdie(4, 'increase ingot error');

//加元宝成功
$pubdb->table_name = 'pay_data';
$pubdb->update(array('success'=>1, 'ditme_up'=>date('Y-m-d H:i:s')), array('cid'=>$cid, 'oid'=>$billno));	//确定充元宝也成功后在更新

$pay_player = array();
$is_combined = 0;
$pay_player['username'] = $openid;
$openid_suf = $openid.'.s';

$pubdb->table_name = 'pay_player_servers';
if (stripos($openid, '.s') !== false) {
	$user = explode(".",$openid);
	$suf = '.'.end($user);
	$pay_player['username'] = str_replace($suf, '', $openid);
	$openid_suf = $pay_player['username'].'.s';
	$is_combined = 1;
}

// if (!$debug) {
$pubdb->table_name = 'pay_token';
$pubdb->delete(array('token'=>$token));
// }

//判断是否首充
$isnew = $pubdb->count(array('cid'=>$cid, 'sid'=>$sid_old > 0 ? $sid_old : $sid, 'username'=>$pay_player['username']), '*');
if (!$isnew) {
	//非测试
	if (!$payinfo['status']) {
		$pubdb->table_name = 'pay_day_new';
		$daycount = $pubdb->count(array('cid'=>$cid, 'sid'=>$sid, 'pdate'=>$pdate), '*');
		if (!$daycount) {
			$pubdb->insert(array('cid'=>$cid, 'sid'=>$sid, 'pdate'=>$pdate, 'new_player'=>1));
		}else {
			$pubdb->update('new_player=new_player+1', array('cid'=>$cid, 'sid'=>$sid, 'pdate'=>$pdate));
		}
	}

	$pubdb->table_name = 'pay_player';
	$pay_player['cid'] = $cid;
	$pay_player['sid'] = $sid;
	$pay_player['sid_arr'] = $sid;
	$pay_player['amount'] = $payinfo['amount'];
	$pay_player['last_pay_amount'] = $payinfo['amount'];
	$pay_player['last_pay_time'] = $payinfo['dtime_unix'];
	$pay_player['pay_num'] = 1;
	$pay_player['username'] = $pay_player['username'];
	$pay_player['nickname'] = ext_addslashes($payinfo['nickname']);
	$pubdb->insert($pay_player);

	unset($pay_player['sid_arr']);
	$pay_player['player_id'] = $player_id;
	$pay_player['sid'] = $sid_old > 0 ? $sid_old : $sid;
	$pubdb->table_name = 'pay_player_servers';
	$pubdb->insert($pay_player);
}else {
	//重新计算统计
	$pubdb->table_name = 'pay_data';
	$where = "cid='$cid' AND (username='".$pay_player['username']."' OR username LIKE '$openid_suf%' AND success<>0 AND status<>1)";
	$wheres= "cid='$cid' AND sid='$sid' AND username='".$pay_player['username']."'";
	$allamount = $pubdb->get_one($where, 'SUM(amount) AS amount');	//统计个人充值总额
	$allamounts= $pubdb->get_one($wheres, 'SUM(amount) AS amount');	//统计个人充值总额

	$pay_num   = $pubdb->count($where, 'pid');	//统计个人充值次数
	$pay_nums  = $pubdb->count($wheres, 'pid');	//统计个人充值次数

	$last      = $pubdb->get_one($where, 'sid,dtime_unix,nickname,amount', 'dtime DESC');
	$lasts     = $pubdb->get_one($wheres, 'sid,dtime_unix,nickname,amount', 'dtime DESC');

	$sidlist   = $pubdb->select($where, 'DISTINCT(sid) AS sid');
	$sidarr = array();
	if ($sidlist) {
		foreach ($sidlist as $key => $value) {
			$sidarr[] = $value['sid'];
		}
	}

	$update_pay_player = array(
		'sid'             => $last['sid'],
		'sid_arr'         => implode($sidarr, ','),
		'amount'          => $allamount['amount'],
		'pay_num'         => $pay_num,
		'last_pay_amount' => $last['amount'],
		'last_pay_time'   => $last['dtime_unix'],
		'nickname'        => ext_addslashes($last['nickname']),
	);
	$pubdb->table_name = 'pay_player';
	$pubdb->update($update_pay_player, array('cid'=>$cid, 'username'=>$pay_player['username']));

	$update_pay_player = array();
	$update_pay_player = array(
		'amount' => $allamounts['amount'],
		'pay_num' => $pay_nums,
		'last_pay_amount' => $lasts['amount'],
		'last_pay_time'   => $lasts['dtime_unix'],
	);
	$pubdb->table_name = 'pay_player_servers';
	$pubdb->update($update_pay_player, array('cid'=>$cid, 'sid'=>$sid_old>0 ? $sid_old: $sid, 'username'=>$pay_player['username']));
}

//非合服 前3日充值送元宝
if(!$is_combined && date('Y-m-d H:i:s') >= '2012-03-02 10:00:00') {
	$startdate = date('Y-m-d',strtotime($server['open_date']));
	$enddate = date("Y-m-d",strtotime($server['open_date']."+2 day"));
	//如果在活动时间范围内
	if (date('Y-m-d') >= $startdate &&  date('Y-m-d') <= $enddate) {
		$ingot = SetGiftIngot($coins);
		if ($ingot > 0) {
			$msggift = $api_admin::add_player_gift_data($player_id, 3, $ingot,0, 0, '您刚充值['.$coins.']元宝，获得额外赠送['.$ingot.']元宝！您在'.$enddate.' 23:59:59前充值满100元宝都能享受到不同额度的元宝赠送哦！', array());//首充送
			
			$logdb = common::load_model('log_model');
			$loginfo = array(
				'cid'            => $cid,
				'sid'            => $sid,
				'playerid'       => $player_id,
				'playername'     => $openid,
				'playernickname' => ext_addslashes($payinfo['nickname']),
				'key'            => 'pay',
				'content'        => '开服前3日充值送'.$ingot.'元宝'.($msggift['result'] == 1 ? '成功' : '失败').'！(帐号)',
				'dateline'       => time(),
			);
			$logdb->add('activity', $loginfo);
		}
	}
}

//计算赠送元宝
function  SetGiftIngot($ingot) {
	if ($ingot >= 100000) {
		$i = $ingot-100000;
		return 13500+SetGiftIngot($i);
	}
	if ($ingot >= 50000) {
		$i = $ingot-50000;
		return 6475+SetGiftIngot($i);
	}
	if ($ingot >= 10000) {
		$i = $ingot-10000;
		return 1175+SetGiftIngot($i);
	}
	if ($ingot >= 5000) {
		$i = $ingot-5000;
		return 575+SetGiftIngot($i);
	}
	if ($ingot >= 1000) {
		$i = $ingot-1000;
		return 105+SetGiftIngot($i);
	}
	if ($ingot >= 100) {
		$i = $ingot-100;
		return 10+SetGiftIngot($i);
	}

	return 0;
}

function retdie($ret = 4, $msg = '') {
	if ($ret != 0) {
		$filepath = './log/';	//当前目录
		$filename = date('Ym').'_pay_delivery_error_log.php';
		$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$ret."\t".$msg."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."\t".ip().PHP_EOL;
		error_log($current, 3, $filepath.$filename);
	}
	
	exit($msg);
}