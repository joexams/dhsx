<?php
defined('IN_CMD') or exit('No permission resources.');
@header("Content-Type: application/json; charset=utf-8");
$crontime = date('Y-m-d H:i:s');
$time = microtime(true);
$pubdb = common::load_model('public_model');
$pubdb->table_name = 'servers';

$today = date('Y-m-d');
$startdate = date('Y-m-d 00:00:00');
$enddate   = date('Y-m-d 23:59:59');
if ($today < '2013-05-20' || $today>'2013-05-28') exit('');
$wherestr = "open_date<now() AND open=1 AND test=0 AND combined_to=0";
$serverlist = $pubdb->select($wherestr, 'sid,name,o_name,db_server,db_root,db_pwd,db_name,open_date,is_combined');

$list = $playerList = array();
$today_get_favor = 0;
foreach($serverlist as $server) {
	$db_host = explode(':', $server['db_server']);
	$gamelink = new mysqli($db_host[0], $server['db_root'], $server['db_pwd'], $server['db_name'], $db_host[1]);
	if ($gamelink->connect_error) continue;
	$gamelink->query('SET NAMES "utf8"');

	$player = array();
	if ($server['name'] == 'qq_s1') {
		$sql = "SELECT today_get_favor,m_player_id,last_get_time,f_player_id,marry_time FROM player_marry_info WHERE today_get_favor>$today_get_favor AND id<>213 ORDER BY today_get_favor DESC LIMIT 9";
	}else {
		$sql = "SELECT today_get_favor,m_player_id,last_get_time,f_player_id,marry_time FROM player_marry_info WHERE today_get_favor>$today_get_favor ORDER BY today_get_favor DESC LIMIT 9";
	}
	
	if ($result = $gamelink->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$row['sid'] = $server['sid'];
			$row['server'] = $server['name'];
			$list[] = $row;
			$player[] = $row['m_player_id'];
			$player[] = $row['f_player_id'];
			$today_get_favor = $today_get_favor > intval($row['today_get_favor']) ? intval($row['today_get_favor']) : $today_get_favor;
		}
		$result->close();
	}

	if (!empty($player)) {
		$sql = "SELECT id, nickname FROM player WHERE id IN (".implode(',', $player).")";
		if ($result = $gamelink->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$playerList[$server['sid']][$row['id']] = $row['nickname'];
			}
			$result->close();
		}
	}
}
usort($list, 'arrCmp');
$list = array_slice($list,0,9);
foreach ($list as $key => $value) {
	$list[$key]['man_nickname'] = $playerList[$value['sid']][$value['m_player_id']];
	$list[$key]['feman_nickname'] = $playerList[$value['sid']][$value['f_player_id']];
	$list[$key]['total_favor_value'] = $value['today_get_favor'];
	unset($list[$key]['today_get_favor']);
	unset($list[$key]['m_player_id']);
	unset($list[$key]['f_player_id']);
	unset($list[$key]['sid']);
}

foreach ($list as $key => $value) {
	foreach ($list as $skey => $svalue) {
		if ($key == $skey)	continue;
		if ($value['total_favor_value'] == $svalue['total_favor_value']) {
			if ($value['last_get_time'] < $svalue['last_get_time']) {
				if ($key < $skey)	continue;
				$list[$key] = $svalue;
				$list[$skey] = $value;
			}else {
				if ($key > $skey)	continue;
				if ($key < $skey) {
					$list[$key] = $svalue;
					$list[$skey] = $value;
				}
			}
		}
	}
}

file_put_contents(CACHE_PATH . 'log/favor_total_'.$today.'.json', json_encode($list));
$logdb = common::load_model('log_model');
$logdb->set_model('cron');
$insertArr = array(
	'key' => 'FavorData',
	'content' => '执行获取亲密度排行活动数据计划任务',
	'crontime' => $crontime,
	'dateline' => time()
);
$logdb->insert($insertArr);
function arrCmp($a,$b){  
	if($a['today_get_favor'] == $b['today_get_favor']){  
		return 0;
	}   
	return($a['today_get_favor']>$b['today_get_favor']) ? -1 : 1;
}
