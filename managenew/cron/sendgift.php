<?php
defined('IN_CMD') or exit('No permission resources.');

$today = date('Y-m-d');
$shortoday = date('m月d日');
$filepath = ROOT_PATH.'/cron/top3/';
$giftingot = array(1=>500,2=>300,3=>100);//奖励元宝
$crontime = date('Y-m-d H:i:s');


$logdb = common::load_model('log_model');
$pubdb = common::load_model('public_model');
$pubdb->table_name = 'servers';

$wherestr = "DATE_ADD(open_date, INTERVAL 3 DAY) > DATE_ADD(now(), INTERVAL 1 DAY_HOUR) AND open_date<>'' AND open_date<now() AND open=1 AND is_combined=0 AND (level_act=1 OR mission_act=1)";
$wherestr = "open_date<>'' AND open_date<now() AND open=1 AND is_combined=0 AND (level_act=1 OR mission_act=1) AND sid=4851";
$serverlist = $pubdb->select($wherestr, 'cid,sid,api_server,api_port,api_pwd,server_ver', '', 'open_date DESC');
foreach ($serverlist as $key => $server) {
	if (empty($server['api_server']) || empty($server['api_port']) || empty($server['api_pwd']) || empty($server['server_ver'])) {
		continue;
	}
	$file = $filepath.$today.'_s'.$server['sid'].'.php';
	if(!file_exists($file)) {
		continue;
	}
	$version = trim($server['server_ver']);
	$api_admin = common::load_api_class('api_admin', $version);
	if ($api_admin == false) {
		continue;
	}
	$api_admin::$SERVER    = $server['api_server'];
	$api_admin::$PORT      = $server['api_port'];
	$api_admin::$ADMIN_PWD = $server['api_pwd'];

	$loginfo = array(
		'cid' => $server['cid'],
		'sid' => $server['sid'],
		'crontime' => $crontime,
		'key' => 'sendgift',
		'dateline' => time(),
	);

	include($file);
	$level_ranking = unserialize($level_ranking);
	for ($i=1;$i<=3;$i++) {
		if($level_ranking[$i]['player_id']) {
			$contents = str_replace(array("{order}","{obj}"), array($i,$giftingot[$i].'元宝'), '您于 ['.$shortoday.'] 等级排行中排名 [第{order}位]，奖励 [{obj}]！');
			$msg = $api_admin::add_player_gift_data($level_ranking[$i]['player_id'], 16, $giftingot[$i],0, 0, $contents, array());

			$loginfo['playerid'] = $level_ranking[$i]['player_id'];
			$loginfo['playername'] = ext_addslashes($level_ranking[$i]['username']);
			$loginfo['playernickname'] = ext_addslashes($level_ranking[$i]['nickname']);
			$loginfo['content']  = '活动奖励：'.$contents.'(玩家昵称)('.($msg['result'] == 1 ? '成功' : '失败').')';
			$logdb->add('activity', $loginfo);
		}
	}

	$mission_ranking = unserialize($mission_ranking);
	for ($i=1;$i<=3;$i++) {
		if($mission_ranking[$i]['player_id']){
			$contents = str_replace(array("{order}","{mission}","{obj}"), array($i,$mission_ranking[$i]['mission_name'],$giftingot[$i].'元宝'),'您于 ['.$shortoday.'] 副本排名 [第{order}名]，奖励 [{obj}]！');
			$msg = $api_admin::add_player_gift_data($mission_ranking[$i]['player_id'], 16, $giftingot[$i],0, 0, $contents, array());

			$loginfo['playerid'] = $mission_ranking[$i]['player_id'];
			$loginfo['playername'] = isset($mission_ranking[$i]['username']) ? ext_addslashes($mission_ranking[$i]['username']) : '';
			$loginfo['playernickname'] = ext_addslashes($mission_ranking[$i]['nickname']);
			$loginfo['content']  = '活动奖励：'.$contents.'(玩家昵称)('.($msg['result'] == 1 ? '成功' : '失败').')';
			$logdb->add('activity', $loginfo);
		}
	}
}