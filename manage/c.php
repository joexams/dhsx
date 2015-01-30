<?php 
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(UCTIME_ROOT."/conn.php");
	if ($adminWebType != 'c')
	{
		showMsg('NOPOWER','login.php','web');
		exit();		
	}
	@include language($adminWebLang);
	include_once(UCTIME_ROOT."/c_top.php");
	
	if(KillBad('in')!='' && file_exists('c_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'server';
	}

	$contentUrl = "c_".$mod.".php"; //单一入口识
	include_once template('c');
	
	include_once("bot.php");
?>