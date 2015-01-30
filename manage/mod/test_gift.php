<?php
	$ver = $argv[1];
	$sidArr = $argv[2];
	$ingot = $argv[3] == 'n' ? 0 : $argv[3];
	$coins = $argv[4] == 'n' ? 0 : $argv[4];
	$vip = $argv[5];
	if (!$vip) $vip = 0;
	if (!$ver || !$sidArr) {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	require_once callApiVer($ver);
	require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
	
	$query = $db->query("
	select 
		A.test_name_arr,
		B.api_server,
		B.api_port,
		B.api_pwd
	from 
		servers_data A
		left join servers B on A.sid = B.sid
	where 
		A.sid in ($sidArr)
		and B.server_ver = '$ver'
		AND B.combined_to = 0
	order by 
		A.sid asc
	");
	while($srs = $db->fetch_array($query)){
		api_base::$SERVER = $srs['api_server'];
		api_base::$PORT   = $srs['api_port'];
		api_base::$ADMIN_PWD   = $srs['api_pwd'];
	
		$test_name_arr =  $srs['test_name_arr'] ? explode('%',$srs['test_name_arr']) : array();
		//$test_name_arr = array_diff($test_name_arr,array(0=>''));

		for ($i = 0;$i<count($test_name_arr);$i++) 
		{
			if($test_name_arr[$i]) {
				$player = api_admin::find_player_by_nickname($test_name_arr[$i]);
				if ($player['result']) {
					if ($ingot) api_admin::system_send_ingot($player['player_id'],$ingot);
					if ($coins) api_admin::increase_player_coins($player['player_id'],$coins);
	
					if ($vip !== 'n') api_admin::set_player_vip_level($player['player_id'],$vip);
	
				}
			}
			
		}	
		
	}
	$db->close();

?> 