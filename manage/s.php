<?php 
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(UCTIME_ROOT."/conn.php");
	if ($adminWebType != 's')
	{
		showMsg('NOPOWER','login.php','web');
		exit();		
	}
	@include language($adminWebLang);
	include_once(UCTIME_ROOT."/s_top.php");
	
	if(KillBad('in')!='' && file_exists('s_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'server';
	}

/*	查各平台版本使用情况
	select 
	B.name as company_name,
	count(A.sid) as s_num
from 
	servers A 
	left join company B on A.cid = B.cid
WHERE
	server_ver = 2011070801
group by 
	A.cid
*/	

	$contentUrl = "s_".$mod.".php"; //单一入口识
	include_once template('s');
	
	include_once("bot.php");
?>