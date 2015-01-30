<?php 
//--开发

if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/player_config.inc.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/player_top.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/log.php");
require_once callPlayerVer($server['server_ver']);//调用不同版本
$cid=ReqNum('cid');

switch (ReqStr('action'))
{
	case 'Logs': serverAdmin('logs'); Logs();break;
	case 'PlayerView': serverAdmin('player'); PlayerView();break;
	case 'PlayerExport': serverAdmin('player'); PlayerExport();break;
	case 'Faction': serverAdmin('faction');Faction();break;
	case 'SuperSportRanking': serverAdmin('data_key');SuperSportRanking();break;
	case 'Data': Data();break;
	case 'TollGate': serverAdmin('data_key');TollGate();break;
	case 'DataSource': serverAdmin('source');DataSource();break;
	case 'UpdateData': UpdateData();break;
	case 'SavePlayerEdit': serverAdmin('player_edit');SavePlayerEdit();break;
	default: serverAdmin('player');Player();

}
?>