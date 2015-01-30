<?php
defined('IN_CMD') or exit('No permission resources.');

$pubdb = common::load_model('public_model');
$pubdb->table_name = 'servers';

$today = date('Y-m-d');
$startdate = date('Y-m-d 00:00:00');
$enddate   = date('Y-m-d 23:59:59');
$pubdb->update('private=1', "open_date>='$startdate' AND open_date<='$enddate' AND private=0");		//设置今日日开服为公开

$wherestr = "DATE_ADD(open_date, INTERVAL 3 DAY) > DATE_ADD(now(), INTERVAL 1 DAY_HOUR) AND open_date<>'' AND open_date<now() AND open=1 AND is_combined=0 AND (level_act=1 OR mission_act=1)";
$wherestr = "open_date<>'' AND open_date<now() AND open=1 AND is_combined=0 AND (level_act=1 OR mission_act=1) AND sid=4851";
$serverlist = $pubdb->select($wherestr, 'sid,api_server,api_port,api_pwd,server_ver', '', 'open_date ASC,sid ASC');

$filepath = ROOT_PATH.'/cron/top3/';
foreach ($serverlist as $key => $server) {
	if (empty($server['api_server']) || empty($server['api_port']) || empty($server['api_pwd']) || empty($server['server_ver'])) {
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

	//等级
	$level_ranking = $api_admin::get_player_level_ranking(3);

	//副本
	$mission_ranking = $api_admin::get_player_mission_ranking(3);

	$filename = $today.'_s'.$server['sid'].'.php';
	$current .= '<?php '.PHP_EOL;
	$current .= '//@Last-Modified '.date('Y-m-d H:i:s').PHP_EOL;
	$current .= 'defined(\'IN_CMD\') or exit(\'No permission resources.\');'.PHP_EOL.PHP_EOL;
	$current .= '$level_ranking=\''.$level_ranking.'\';'.PHP_EOL.PHP_EOL;
	$current .= '$mission_ranking=\''.$mission_ranking.'\';'.PHP_EOL;
	$file     = $filepath.$filename;
	file_put_contents($file, $current);
}