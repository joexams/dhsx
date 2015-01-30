<?php
	$ver = $argv[1];
	$sidArr = $argv[2];
	$content = urldecode($argv[3]);
	$time = $argv[4]  == 'n' ? 0 : $argv[4];;
	$url = $argv[5]  == 'n' ? '' : urldecode($argv[5]);
	if (!$ver || !$sidArr || !$content) {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	require_once callApiVer($ver);
	require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
	
	if ($url)
	{
		$content = '<a href="'.$url.'" target="_blank">'.$content.'</a>';
	}	
	
	$query = $db->query("
	select 
		sid,
		api_server,
		api_port,
		api_pwd
	from 
		servers
	where 
		sid in ($sidArr)
		and server_ver = '$ver'
	order by 
		sid asc
	");
	while($srs = $db->fetch_array($query)){
		$sid = $srs['sid'];
		api_base::$SERVER = $srs['api_server'];
		api_base::$PORT   = $srs['api_port'];
		api_base::$ADMIN_PWD   = $srs['api_pwd'];
	
		if ($ver < 2011071801){
			$list_array = api_admin::get_affiche_list();
			if ($list_array)
			{
				foreach($list_array as $rs)
				{
					if ($rs[0]['id']) api_admin::delete_affiche($rs[0]['id']);
				}
			}
			$msg = api_admin::add_affiche($content);
		}else{
			$msg = api_admin::add_affiche($content,$time);
		
		}
		
		
		//$msg = api_admin::add_affiche($content);
		
/*		if ($msg['result'] == 1)
		{		
			echo  '服务器SID:'.$sid.'成功！<br />';	
		}else{
			echo  '<strong class="redtext">服务器SID:'.$sid.'失败！</strong><br />';	
		}	*/	
	}
	$db->close();

?> 