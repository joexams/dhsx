<?php
//惩罚公告
	$ver = $argv[1];
	$content = urldecode($argv[2]);
	$cid = urldecode($argv[3]);
	$t = urldecode($argv[4]);
	$url = $argv[5]  == 'n' ? '' : urldecode($argv[5]);
	if (!$ver || !$content || !$t || !$cid) {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	require_once callApiVer($ver);
	
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
		cid = '$cid'
		and server_ver = '$ver'
		and private = 1
		and `open` = 1
	order by 
		sid asc
	");
	while($srs = $db->fetch_array($query)){
		api_base::$SERVER = $srs['api_server'];
		api_base::$PORT   = $srs['api_port'];
		api_base::$ADMIN_PWD   = $srs['api_pwd'];
		if($t == 'a')
		{
			$msg = api_admin::add_warning_affiche($content);
		}elseif($t == 'd'){
			$msg = api_admin::delete_warning_affiche($content);
		}

		
	}
	$db->close();

?> 