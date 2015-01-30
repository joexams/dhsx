<?php
defined('IN_CMD') or exit('No permission resources.');
$key = 'paydata';
$crontime = date('Y-m-d H:i:s');

$pubdb = common::load_model('public_model');
$logdb = common::load_model('log_model');
$pubdb->table_name = 'servers';

$now = time();
$yesterday = date('Y-m-d', strtotime('-1 day'));
$ystarttime = strtotime(date('Y-m-d 00:00:00', $yesterday));
$yendtime = strtotime(date('Y-m-d 23:59:59', $yesterday));

$sql = "SELECT a.cid, a.sid, a.name, a.coins_rate FROM servers a LEFT JOIN company b ON a.cid=b.cid WHERE a.open_date<='$yendtime'";
$serverlist = $pubdb->get_list($sql);

$pubdb->table_name = 'pay_data';
foreach ($serverlist as $key => $value) {
	$cid = $value['cid'];
	$sid = $value['sid'];
	$name = $value['name'];
	$coins_rate = $value['coins_rate'];

	$pay_player_count = $pay_amount = $pay_num = 0;

	$pubdb->table_name = 'pay_data';
	$pay = $pubdb->get_one("cid=%d AND sid=%d AND status<>1 AND success<>0 AND dtime>='$ystarttime' AND dtime<=$yendtime", 'COUNT(DISTINCT(player_id)) AS pay_player_count, SUM(amount) AS pay_amount, COUNT(pid) AS pay_num');
	if ($pay) {
		$pay_player_count = $pay['pay_player_count'];
		$pay_amount = round($pay['pay_amount'], 2);
		$pay_num = $pay['pay_num'];
	}

	$pubdb->table_name = 'pay_day_new';
	$new_player = $pubdb->get_one(array('cid'=>$cid, 'sid'=>$sid, 'pdate'=>$yesterday), '*');
	$new_player = $new_player['new_player'];

	$pubdb->table_name = 'game_data';
	$exists = $pubdb->count(array('cid'=>$cid, 'sid'=>$sid, 'gdate'=>$yesterday), '*');
	if (!$exists) {
		$insertarr = array(
			'cid' => $cid,
			'sid' => $sid,
			'pay_player_count' => $pay_player_count,
			'pay_amount' => $pay_amount,
			'pay_num' => $pay_num,
			'new_player' => $new_player,
			'gdate' => $yesterday,
		);
		$pubdb->insert($insertarr);
	}else {
		$updatearr = array(
			'pay_player_count' => $pay_player_count,
			'pay_amount' => $pay_amount,
			'pay_num' => $pay_num,
			'new_player' => $new_player,
		);
		$pubdb->update($updatearr, array('cid'=>$cid, 'sid'=>$sid, 'gdate'=>$yesterday));
	}
}

$logarr = array(
	'key' => $key,
	'content' => '执行每日充值数据汇总计划任务',
	'crontime' => $crontime,
	'dateline' => time(),
);
$logdb->add('cron', $logarr);
