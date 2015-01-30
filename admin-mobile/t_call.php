<?php

header("expires:mon,26jul199705:00:00gmt"); 
header("cache-control:no-cache,must-revalidate"); 
header("pragma:no-cache");//禁止缓存
header("Content-Type:text/html;charset=utf-8");//避免输出乱码
	
include_once(dirname(__FILE__)."/config.inc.php");
include_once(dirname(__FILE__)."/conn.php");
webAdmin('t','','','web');
switch (ReqStr('action'))
{
	case 'CallMissionAreaMonster': CallMissionAreaMonster(); break;
	case 'CallMonsterSkill': CallMonsterSkill(); break;
}
function CallMissionAreaMonster() 
{

	global $db;
	$id=ReqNum('id');
	$winid=ReqStr('winid');
	//怪物列表
	$query = $db->query('select a.id,b.text as name from monster a,chinese_text b where a.name_text_id=b.id');
	while($rs = $db->fetch_array($query)){
		$monster_list[] =  $rs;
	}
	$query = $db->query('select a.* from mission_area_monster a where a.mission_area_monster_team_id='.$id);
	$num = $db->num_rows($query);
	if($num)
	{		
		$i = 1;	
		while($rs = $db->fetch_array($query))
		{	
			$rs['i']=$i++;
			$list_array[] =  $rs;
			$n[] = $rs["pos"];
		}
	}
		include_once template('t_mission_area_monster');

}

function CallMonsterSkill() 
{

	global $db;
	$id=ReqNum('id');
	$winid=ReqStr('winid');
	//技能列表
	$query = $db->query('select a.id,b.text as name from skill a,chinese_text b where a.name_text_id=b.id');
	while($rs = $db->fetch_array($query)){
		$skill_list[] =  $rs;
	}
	$query = $db->query('select a.* from monster_skill a where a.monster_id='.$id);
	$num = $db->num_rows($query);
	if($num)
	{			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}
		include_once template('t_monster_skill');

}