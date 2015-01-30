<?php 
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(UCTIME_ROOT."/conn.php");	
	if ($adminWebType != 'u')
	{
		showMsg("NOPOWER",'login.php','web');
		exit();		
	}
	@include language($adminWebLang);
	include_once(UCTIME_ROOT."/u_top.php");

	if(KillBad('in')!='' && file_exists('u_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'server';
	}

	$contentUrl = "u_".$mod.".php"; //单一入口识
	include_once template('u');
		
	include_once("bot.php");
?>