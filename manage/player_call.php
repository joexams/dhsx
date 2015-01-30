<?php
include_once(dirname(__FILE__)."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
include_once(UCTIME_ROOT."/player_config.inc.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/player_top.php");
include_once(UCTIME_ROOT.'/mod/'.$server['server_ver']."/player_call.php");
//require_once callPlayerCallVer($server['server_ver']);
if (!$adminWebID)
{
	exit();		
}
@include language($adminWebLang);
header("expires:mon,26jul199705:00:00gmt"); 
header("cache-control:no-cache,must-revalidate"); 
header("pragma:no-cache");//禁止缓存
header("Content-Type:text/html;charset=utf-8");//避免输出乱码
	
switch (ReqStr('action'))
{
	case 'callAchievement': callAchievement();break;
	case 'callSoul': callSoul();break;
	case 'callFate': callFate();break;
	case 'callItemType': callItemType();break;
	case 'callItem': callItem();break;
	case 'callPlayerRole': callPlayerRole();break;
	case 'callPlayerSoulLog': callPlayerSoulLog();break;
	case 'callPlayerFateLog': callPlayerFateLog();break;
	case 'callItemGift': callItemGift();break;
	case 'callNowOnline': callNowOnline();break;
	case 'callTownOnline': callTownOnline();break;
	case 'callTodayConsume': callTodayConsume();break;
	
	//------------------------------------------------------------------
	case 'callPlayerStone': callPlayerStone();break;
	case 'CallPlayerFactionWarMember': CallPlayerFactionWarMember();break;
	case 'callPlayerDataFateLevel': callPlayerDataFateLevel();break;
	case 'callPlayerDataRegHour': callPlayerDataRegHour();break;
	case 'callPlayerDataOnline': callPlayerDataOnline();break;
	case 'callAchievement': callAchievement();break;
	case 'callPlayerItem': callPlayerItem();break;
	case 'callPlayerGift': callPlayerGift();break;
	case 'callPlayerMissionRecord': callPlayerMissionRecord();break;
	case 'callPlayerQuest': callPlayerQuest();break;
	case 'callPlayerDayQuest': callPlayerDayQuest();break;
	case 'callPlayerRoleEqui': callPlayerRoleEqui();break;
	case 'callPlayerResearch': callPlayerResearch();break;
	case 'callPlayerFriends': callPlayerFriends();break;
	case 'CallFactionNotice': CallFactionNotice();break;
	case 'CallFactionMember': CallFactionMember();break;
	case 'CallFactionRequest': CallFactionRequest();break;
	case 'callPlayerFarmland': callPlayerFarmland();break;
	case 'callPlayerKey': callPlayerKey();break;
	case 'callPlayerFate': callPlayerFate();break;
	case 'callPlayerSoul': callPlayerSoul();break;
	case 'callPlayerRoleElixir': callPlayerRoleElixir();break;
	case 'callPlayerRoleFate': callPlayerRoleFate();break;
	case 'callPlayerLevelUpRecord': callPlayerLevelUpRecord();break;
	case 'callPlayerDataConsumeLevel': serverAdmin('data','','','web');callPlayerDataConsumeLevel();break;
	case 'callPlayerDataItemLevel': serverAdmin('data','','','web');callPlayerDataItemLevel();break;
	case 'callPlayerDataRoleLevel': serverAdmin('data','','','web');callPlayerDataRoleLevel();break;
	case 'callPlayerOtherServerP': serverAdmin('data','','','web');callPlayerOtherServerP();break;
	
	case 'CallTemplates': serverAdmin('t','','','web');CallTemplates();break;
	
}

?>