<?php 
//--客服
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/player_config.inc.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/player_top.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/log.php");
require_once callPlayerVer($server['server_ver']);//调用不同版本
if ($adminWebServers) 
{
	$adminWebServersArr = explode(',',$adminWebServers);	
	if(!in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
	{	
		showMsg('NOSERVERPOWER');	
		exit();
	
	}
}

switch (ReqStr('action'))
{
	case 'Logs': serverAdmin('logs'); Logs();break;
	case 'PlayerView': serverAdmin('player'); PlayerView();break;
	case 'Faction': serverAdmin('faction');Faction();break;
	case 'SuperSportRanking': serverAdmin('data_key');SuperSportRanking();break;
	case 'Data': Data();break;
	case 'TollGate': serverAdmin('data_key');TollGate();break;
	case 'DataSource': serverAdmin('source');DataSource();break;
	default: serverAdmin('player');Player();

}


?>