<?php
defined('IN_CMD') or exit('No permission resources.');
$key = 'gamedata';
$crontime = date('Y-m-d H:i:s');

$pubdb = common::load_model('public_model');
$logdb = common::load_model('log_model');
$pubdb->table_name = 'servers';

$now = time();
$yesterday = date('Y-m-d', strtotime('-1 day'));	//昨天
$beforeyesterday = date('Y-m-d', strtotime('-2 day'));	//前天

$bystarttime = strtotime(date('Y-m-d 00:00:00', $beforeyesterday));
$byendtime = strtotime(date('Y-m-d 23:59:59', $beforeyesterday));

$ystarttime = strtotime(date('Y-m-d 00:00:00', $yesterday));
$yendtime = strtotime(date('Y-m-d 23:59:59', $yesterday));

$ydate = date('Y-m-d 23:59:59', strtotime($yesterday));

$sql = "SELECT a.cid, sid, a.name, db_server, db_pwd, db_name, open_date, coins_rate FROM servers a LEFT join company b ON a.cid=b.cid WHERE private=1 AND open=1 AND combined_to=0 AND open_date<='$ydate' ORDER BY  sid DESC";
$serverlist = $pubdb->get_list($sql);


foreach ($serverlist as $key => $value) {
	$cid =  $server['cid'];
	$sid =  $server['sid'];
	$name =  $server['name'];
	$coins_rate =  $server['coins_rate'];
	$open_date =  strtotime($server['open_date']);
	echo $server['cid'].'|'.$server['sid'].'<br />';

	$db_host = explode(':', $value['db_server']);
	$gdb = new mysqli($db_host[0], $value['db_root'], $value['db_pwd'], $value['db_name'], $db_host[1]);
	if ($gdb->connect_error) continue;

	$max_online_count = $max_online_time = $avg_online_count = $register_count = $login_count = $create_count = 0;
	$sql = "SELECT `time` AS max_online_time, MAX(online_count) AS max_online_count	FROM server_state WHERE `time` >= '$ystarttime' AND `time` <= '$yendtime' GROUP BY online_count	ORDER BY 		max_online_count DESC LIMIT 1"
	if ($result = $gdb->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$max_online_count = $max['max_online_count'];
			$max_online_time = $max['max_online_time'];	
		}
		$result->close();
	}

	//------------avg online------------
	$sql = "SELECT SUM(online_count) AS online_count, COUNT(*) AS hour_count FROM server_state WHERE `time`>='$ystarttime' AND `time`<='$yendtime'";
	if ($result = $gdb->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$avg_online_count = round($row['online_count']/$row['hour_count']);
		}
		$result->close();
	}

	//------------reg create login------
	$sql = "SELECT SUM(register_count) AS register_count, SUM(login_count) AS login_count FROM server_state WHERE `time`>='$ystarttime' AND `time`<='$yendtime'";
	if ($result = $gdb->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$register_count = $row['register_count'];
			$login_count    = $row['login_count'];	
		}
		$result->close();
	}

	$sql = "SELECT COUNT(*) AS create_count FROM player a LEFT JOIN player_trace b ON a.id=b.player_id WHERE nickname<>'' AND first_login_time>='$ystarttime' AND first_login_time<='$yendtime'";
	if ($result = $gdb->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$create_count = $row['register_count'];
		}
		$result->close();
	}

	//------------newer loss------------
	$sql = "SELECT COUNT(b.player_id) AS out_count FROM player a LEFT JOIN player_trace b ON a.id=b.player_id LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id WHERE c.level < 10 AND b.first_login_time >='$bystarttime' AND b.first_login_time <= '$byendtime'";
	if ($result = $gdb->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$out_count = $row['out_count'];
		}
		$result->close();
	}

	$pubdb->table_name = 'game_data';
	$exists = $pubdb->count(array('cid'=>$cid, 'sid'=>$sid, 'gdate'=>$yesterday), '*');
	$insertarr = array(
		'cid' => $cid, 
		'sid' => $sid, 
		'max_online_time' => $max_online_time, 
		'max_online_count' => $max_online_count, 
		'avg_online_count' => $avg_online_count, 
		'register_count' => $register_count, 
		'create_count' => $create_count, 
		'login_count' => $login_count, 
		'out_count' => $out_count, 
		'consume' => 0, 
		'gdate' => $yesterday, 
	);
	if (!$exists) {
		$pubdb->insert($insertarr);
	}else  {
		$pubdb->update($insertarr, array('cid'=>$cid, 'sid'=>$sid, 'gdate'=>$yesterday));
	}
}

$logarr = array(
	'key' => $key,
	'content' => '执行获取远程数据计划任务',
	'crontime' => $crontime,
	'dateline' => time(),
);
$logdb->add('cron', $logarr);
