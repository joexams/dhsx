<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SetMissionAreaMonsterTeam': SetMissionAreaMonsterTeam();break;
	case 'SetMissionAreaMonster': SetMissionAreaMonster();break;
	default:  MissionList();
}
function MissionList()
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//副本名称
	$mission_list = globalDataList('chinese_text','type=1');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			mission
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&action=MissionList");	
	}	
	include_once template('t_mission');
}
function  SetMission() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$name = ReqArray('name');
	$lock = ReqArray('lock');
	$quest_lock = ReqArray('quest_lock');
	$award_gift_id = ReqArray('award_gift_id');
	
	$name_n = ReqNum('name_n');
	$lock_n = ReqStr('lock_n');
	$quest_lock_n = ReqNum('quest_lock_n');
	$award_gift_id_n = ReqNum('award_gift_id_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mission where id in ($id_arr)");
		$db->query("delete from mission_area where mission_id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $name[$i])
			{

				$db->query("
				update 
					mission 
				set 
					`name`='$name[$i]',
					`lock`='$lock[$i]',
					`quest_lock`='$quest_lock[$i]',
					`award_gift_id`='$award_gift_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($monster_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission
			(`id`,`name`,`lock`,`quest_lock`,`award_gift_id`) 
		values 
			('','$name_n','$lock_n','$quest_lock_n','$award_gift_id_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	showMsg($msg,'','','greentext');	
}

function  SetMissionAreaMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$mission_area_monster_team_id = ReqStr('mission_area_monster_team_id');
	$monster_id = ReqArray('monster_id');
	$pos = ReqArray('pos');
	
	$monster_id_n = ReqNum('monster_id_n');
	$pos_n = ReqStr('pos_n');
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mission_area_monster where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $monster_id[$i])
			{

				$db->query("
				update 
					mission_area_monster 
				set 
					`monster_id`='$monster_id[$i]',
					`pos`='$pos[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($monster_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_area_monster
			(`id`,`mission_area_monster_team_id`,`monster_id`,`pos`) 
		values 
			('','$mission_area_monster_team_id','$monster_id_n','$pos_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}
	$msg = urlencode($msg);
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}