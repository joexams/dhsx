<?php 
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(dirname(__FILE__)."/conn.php");
	webAdmin('s','','','web');
	
	if(KillBad('in')!='' && file_exists('s_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'setup';
	}

	$contentUrl = "s_".$mod.".php"; //单一入口识
	include_once template('s');
	
	include_once(dirname(__FILE__)."/bot.php");
?>