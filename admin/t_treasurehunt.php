<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
    case 'TreasureHuntMission': TreasureHuntMission();break;
    case 'TreasureHuntExpLevel': TreasureHuntExpLevel();break;
    case 'TreasureHuntAward': TreasureHuntAward();break;

    case 'SetTreasureHuntMission': SetTreasureHuntMission();break;
    case 'SetTreasureHuntExpLevel': SetTreasureHuntExpLevel();break;
    case 'SetTreasureHuntAward': SetTreasureHuntAward();break;
    case 'SetTreasureHuntMissionAward': SetTreasureHuntMissionAward(); break;
    case 'SetTreasureHuntMissionExtraAward': SetTreasureHuntMissionExtraAward(); break;

}

//--------------------------------------------------------------------------------------------寻宝关卡表
function TreasureHuntMission() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		treasure_hunt_mission
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			treasure_hunt_mission
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=TreasureHuntMission");	

	}	
	include_once template('t_treasure_hunt_mission');
}
//--------------------------------------------------------------------------------------------寻宝经验等级表
function TreasureHuntExpLevel() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','type_id=25000');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		treasure_hunt_exp_level
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			treasure_hunt_exp_level
		order by 
			level asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=TreasureHuntExpLevel");	

	}	
	include_once template('t_treasure_hunt_exp_level');
}

//--------------------------------------------------------------------------------------------寻宝奖励表
function TreasureHuntAward() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		treasure_hunt_award
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			treasure_hunt_award
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=TreasureHuntAward");	

	}	
	include_once template('t_treasure_hunt_award');

}
//--------------------------------------------------------------------------------------------保存寻宝关卡表
function SetTreasureHuntMission() {
	global $db;
	global $id_del, $id_old, $id, $monster, $ingot_cost, $health, $need_exp, $name;
	global $id_n, $monster_n, $ingot_cost_n, $health_n, $need_exp_n, $name_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from treasure_hunt_mission where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $id[$i] && $monster[$i])
			{

				$db->query("
				update 
					treasure_hunt_mission 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`monster`='$monster[$i]',
					`ingot_cost`='$ingot_cost[$i]',
					`health`='$health[$i]',
					`need_exp`='$need_exp[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			treasure_hunt_mission
			(`id`,`name`,`monster`,`ingot_cost`,`health`,`need_exp`) 
		values 
			('$id_n','$name_n','$monster_n','$ingot_cost_n','$health_n','$need_exp_n')
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
//--------------------------------------------------------------------------------------------保存寻宝经验等级表
function SetTreasureHuntExpLevel() {
	global $db;
	global $id_del, $id_old, $level, $need_exp, $item_id, $item_amount;
	global $level_n, $need_exp_n, $item_id_n, $item_amount_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from treasure_hunt_exp_level where level in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $level[$i])
			{

				$db->query("
				update 
					treasure_hunt_exp_level 
				set 
					`level`='$level[$i]',
					`need_exp`='$need_exp[$i]',
					`item_id`='$item_id[$i]',
					`item_amount`='$item_amount[$i]'
				where 
					level = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n)
	{
	
		$query = $db->query("
		insert into 
			treasure_hunt_exp_level
			(`level`,`need_exp`,`item_id`,`item_amount`) 
		values 
			('$level_n','$need_exp_n','$item_id_n','$item_amount_n')
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
//--------------------------------------------------------------------------------------------保存寻宝奖励表
function SetTreasureHuntAward() {
	global $db;
	global $id_del, $id, $name, $coins, $item_id, $item_amount, $exp, $fame, $skill, $power;
	global $name_n, $coins_n, $item_id_n, $item_amount_n, $exp_n, $fame_n, $skill_n, $power_n;

	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from treasure_hunt_award where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{

				$db->query("
				update 
					treasure_hunt_award 
				set 
					`name`='$name[$i]',
					`coins`='$coins[$i]',
					`fame`='$fame[$i]',
					`skill`='$skill[$i]',
					`power`='$power[$i]',
					`exp`='$exp[$i]',
					`item_id`='$item_id[$i]',
					`item_amount`='$item_amount[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			treasure_hunt_award
			(`name`,`coins`,`skill`,`fame`,`power`,`exp`,`item_id`,`item_amount`) 
		values 
			('$name_n','$coins_n','$skill_n','$fame_n','$power_n','$exp_n','$item_id_n','$item_amount_n')
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
//--------------------------------------------------------------------------------------------保存寻宝关卡奖励表
function SetTreasureHuntMissionAward() {
	global $db;
	global $th_award_id_del, $th_award_id_old, $th_award_id, $award_prob, $th_mission_id;
	global $th_award_id_n, $award_prob_n;
	global $url,$winid;

	if (intval($th_mission_id) > 0) {
		//----------------------删除--------------------------------------
		if ($th_award_id_del)
		{
		
			$id_arr = implode(",",$th_award_id_del);
			$db->query("delete from treasure_hunt_mission_award where th_mission_id='$th_mission_id' and th_award_id in ($id_arr)");
			$msg = "删除成功！";
			
		}	
		
		
		//-----------------更新-------------------------------------------
		if ($th_award_id_old)
		{
		
			$id_num = count($th_award_id_old);

			for ($i=0;$i<=$id_num;$i++)	
			{
				if ($th_award_id_old[$i] && $th_award_id[$i])
				{

					$db->query("
					update 
						treasure_hunt_mission_award 
					set 
						`th_award_id`='$th_award_id[$i]',
						`award_prob`='$award_prob[$i]'
					where 
						th_mission_id = '$th_mission_id' and th_award_id = '$th_award_id_old[$i]'
					");
				}
				
			}
			$msg .= "<br />更新成功！";
		}
			
		//-----------------增加记录-------------------------------------------
		if ($th_award_id_n)
		{
		
			$query = $db->query("
			insert into 
				treasure_hunt_mission_award
				(`th_mission_id`,`th_award_id`,`award_prob`) 
			values 
				('$th_mission_id','$th_award_id_n','$award_prob_n')
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
	}
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}
//--------------------------------------------------------------------------------------------保存寻宝关卡奖励额外表
function SetTreasureHuntMissionExtraAward() {
	global $db;
	global $th_award_id_del, $th_award_id_old, $th_award_id, $award_prob, $th_mission_id;
	global $th_award_id_n, $award_prob_n;
	global $url,$winid;

	if (intval($th_mission_id) > 0) {
		//----------------------删除--------------------------------------
		if ($th_award_id_del)
		{
		
			$id_arr = implode(",",$th_award_id_del);
			$db->query("delete from treasure_hunt_mission_extra_award where th_mission_id='$th_mission_id' and th_award_id in ($id_arr)");
			$msg = "删除成功！";
			
		}	
		
		
		//-----------------更新-------------------------------------------
		if ($th_award_id_old)
		{
		
			$id_num = count($th_award_id_old);

			for ($i=0;$i<=$id_num;$i++)	
			{
				if ($th_award_id_old[$i] && $th_award_id[$i])
				{

					$db->query("
					update 
						treasure_hunt_mission_extra_award 
					set 
						`th_award_id`='$th_award_id[$i]',
						`award_prob`='$award_prob[$i]'
					where 
						th_mission_id = '$th_mission_id' and th_award_id = '$th_award_id_old[$i]'
					");
				}
				
			}
			$msg .= "<br />更新成功！";
		}
			
		//-----------------增加记录-------------------------------------------
		if ($th_award_id_n)
		{
		
			$query = $db->query("
			insert into 
				treasure_hunt_mission_extra_award
				(`th_mission_id`,`th_award_id`,`award_prob`) 
			values 
				('$th_mission_id','$th_award_id_n','$award_prob_n')
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
	}
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}