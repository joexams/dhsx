<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SetTown': SetTown();break;
	case 'SetTownNPC': SetTownNPC();break;
	case 'SetTownNpcItem': SetTownNpcItem();break;
	case 'SetTownNpcSoul': SetTownNpcSoul(); break;

	default:  Town();
}
//--------------------------------------------------------------------------------------------城镇

function  Town() 
{
	global $db; 
	$type = ReqNum('type');
	$camp_list = globalDataList('camp');//门派
	//$maxlock = $db->result($db->query("select max(`lock`) from town"),0);
	//$lock_n = $maxlock+100;	
	
	$query = $db->query("
	select 
		*
	from 
		town
	where 
		type = '$type'
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_town');
}

//--------------------------------------------------------------------------------------------批量设置城镇
function  SetTown() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$lock = ReqArray('lock');
	$description = ReqArray('description');
	$training_coins = ReqArray('training_coins');
	$camp_id = ReqArray('camp_id');
	$start_x = ReqArray('start_x');
	$start_y = ReqArray('start_y');
	
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$lock_n = ReqNum('lock_n');
	$description_n = ReqStr('description_n');
	$training_coins_n = ReqNum('training_coins_n');
	$camp_id_n = ReqNum('camp_id_n');
	$start_x_n = ReqNum('start_x_n');
	$start_y_n = ReqNum('start_y_n');
	$type = ReqNum('type');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					town 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`lock`='$lock[$i]',
					`description` = '$description[$i]',
					`training_coins` = '$training_coins[$i]',
					`camp_id` = '$camp_id[$i]',
					`start_x` = '$start_x[$i]',
					`start_y` = '$start_y[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n && $description_n)
	{
		if (!$lock_n) 
		{
			$maxlock = $db->result($db->query("select max(`lock`) from town"),0);
			$lock_n = $maxlock+100;
		}
		$db->query("
		insert into 
			town
			(`name`,`sign`,`lock`,`description`,`training_coins`,`camp_id`,`start_x`,`start_y`,`type`) 
		values 
			('$name_n','$sign_n','$lock_n','$description_n','$training_coins_n','$start_x_n','$camp_id_n','$start_y_n','$type')
		") ;
		$msg .= "<br />增加成功！";
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$delsql = "select id from mission_section where town_id in ($id_arr)";
		$delsql_2 = "select id from mission where mission_section_id in ($delsql)";
		$delsql_3 = "select id from mission_scene where mission_id in ($delsql_2)";
		$delsql_4 = "select id from mission_monster_team where mission_scene_id in ($delsql_3)";
		$delsql_5 = "select id from mission_monster where mission_monster_team_id in ($delsql_4)";
		
		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($delsql_5)");
		$db->query("delete from mission_monster where mission_monster_team_id in ($delsql_4)");			
		$db->query("delete from mission_monster_team where mission_scene_id in ($delsql_3)");	
		$db->query("delete from mission_scene where mission_id in ($delsql_2)");
		$db->query("delete from mission_item where mission_id in ($delsql_2)");	
		$db->query("delete from mission_failed_tips where mission_id in ($delsql_2)");	
		$db->query("delete from mission where mission_section_id in ($delsql)");	
		$db->query("delete from mission_item where mission_section_id in ($delsql)");	
		$db->query("delete from mission_section where town_id in ($id_arr)");
		$db->query("delete from town_npc_item where town_npc_id in (select id from town_npc where town_id in ($id_arr))");
		$db->query("delete from town_npc where town_id in ($id_arr)");
		$db->query("delete from town where id in ($id_arr)");

		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量设置城镇NPC
function  SetTownNPC() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$position_x = ReqArray('position_x');
	$position_y = ReqArray('position_y');
	$resource_id = ReqArray('resource_id');
	
	$town_id = ReqNum('town_id');
	$npc_id_n = ReqNum('npc_id_n');
	$position_x_n = ReqNum('position_x_n');
	$position_y_n = ReqNum('position_y_n');
	//$resource_id_n = ReqNum('resource_id_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					town_npc 
				set 
					`position_x`='$position_x[$i]',
					`position_y`='$position_y[$i]',
					`resource_id` = '$resource_id[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($npc_id_n)
	{
	
		$query = $db->query("
		insert into 
			town_npc
			(`town_id`,`npc_id`,`position_x`,`position_y`,`resource_id`) 
		values 
			('$town_id','$npc_id_n','$position_x_n','$position_y_n','$resource_id_n')
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
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from town_npc where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);	
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}
//--------------------------------------------------------------------------------------------批量设置城镇NPC的物品
function  SetTownNpcItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$func_lock = ReqArray('func_lock');

	$town_npc_id = ReqNum('town_npc_id');
	$item_id_n = ReqNum('item_id_n');
	$func_lock_n = ReqNum('func_lock_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					town_npc_item 
				set 
					`func_lock`='$func_lock[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($town_npc_id && $item_id_n)
	{
	
		$query = $db->query("
		insert into 
			town_npc_item
			(`town_npc_id`,`item_id`,`func_lock`) 
		values 
			('$town_npc_id','$item_id_n','$func_lock_n')
		") ;	
		if($query)
		{
			$msg = "增加成功！<br />";
		}
		else
		{
			$msg = '<strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong><br />';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from town_npc_item where id in ($id_arr)");
		$msg .= "删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}



//--------------------------------------------------------------------------------------------批量设置城镇NPC的灵件
function  SetTownNpcSoul() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	//$func_lock = ReqArray('func_lock');

	$town_npc_id = ReqNum('town_npc_id');
	$soul_id_n = ReqNum('soul_id_n');
	//$func_lock_n = ReqNum('func_lock_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
/*	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					town_npc_item 
				set 
					`func_lock`='$func_lock[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
*/		
	//-----------------增加记录-------------------------------------------
	if ($town_npc_id && $soul_id_n)
	{
	
		$query = $db->query("
		insert into 
			town_npc_soul
			(`town_npc_id`,`soul_id`) 
		values 
			('$town_npc_id','$soul_id_n')
		") ;	
		if($query)
		{
			$msg = "增加成功！<br />";
		}
		else
		{
			$msg = '<strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong><br />';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from town_npc_soul where id in ($id_arr)");
		$msg .= "删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

?>