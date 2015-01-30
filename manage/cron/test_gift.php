	<?php
	//测试号奖励
	$ver = isset($argv[1]) ? trim($argv[1]) : null;
	if (!$ver) {
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
		B.api_pwd,
		B.db_server,
		B.db_root,
		B.db_pwd,
		B.db_name,
		B.sid,
		B.name,
		C.type
	from 
		servers_data A
		left join servers B on A.sid = B.sid
		left join company C on A.cid = C.cid
	where 
		B.server_ver = '$ver'
		AND B.combined_to = 0
	order by 
		A.sid asc
	");
	while($srs = $db->fetch_array($query))
	{
		if($srs['type'] == 1)
		{
			$ingot = 12000;
		}elseif($srs['type'] == 2){
			$ingot = 5000;
		}elseif($srs['type'] == 3){
			$ingot = 1000;
		}else{
			$ingot = 0;
		}
		if($ingot){
			if($srs['api_server'] && $srs['api_port'] && $srs['api_pwd'])
			{
			
				$test_name_arr = '';
				$player = '';
				$msg  = '';		
			
				api_base::$SERVER = $srs['api_server'];
				api_base::$PORT   = $srs['api_port'];
				api_base::$ADMIN_PWD   = $srs['api_pwd'];
				$test_name_arr =  $srs['test_name_arr'] ? explode('%',$srs['test_name_arr']) : array();
				//$test_name_arr = array_diff($test_name_arr,array(0=>''));
		
				//if($test_name_arr[0]) SendIngotTest($test_name_arr[0],$ingot); 
				//if($test_name_arr[1]) SendIngotTest($test_name_arr[1],$ingot); 
				//if($test_name_arr[2]) SendIngotTest($test_name_arr[2],$ingot); 
				//---------------------------------------
		
				for ($i = 0;$i<count($test_name_arr);$i++) 
				{
	
					if($test_name_arr[$i] && $ingot) SendIngotTest($test_name_arr[$i],$ingot); 
					
				}
				
			}else{
				echo '<strong>'.$srs['name'].'|'.$srs['sid'].' API NO FIND</strong><br />';
			}	
		}else{
			echo '<strong>'.$srs['name'].'|'.$srs['sid'].' INGOT NULL</strong><br />';
		}
	}
	
function  SendIngotTest($nickname,$ingot) {
	global $srs;

	$player = api_admin::find_player_by_nickname($nickname);
	if ($player['result']) {
		//echo $player['player_id'];
		if ($ingot) 
		{
			$msg = api_admin::system_send_ingot($player['player_id'],$ingot);

			if ($msg['result']) 
			{
				echo $nickname.' ('.$srs['name'].'|'.$srs['sid'].') OK<br />';
			}else{
				echo '<strong>'.$nickname.' ('.$srs['name'].'|'.$srs['sid'].') ERR</strong><br />';
			}
		}
	}else{
		echo '<strong>'.$nickname.' ('.$srs['name'].'|'.$srs['sid'].') USER NO FIND</strong><br />';
	}
}

	$db->close();

?> 