<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'QuestType': QuestType();break;
	case 'QuestCompleteType': QuestCompleteType();break;
	case 'DayQuestAward': DayQuestAward();break;
	case 'SpecialPartnerMission': SpecialPartnerMission(); break;

	case 'SetQuest': SetQuest();break;
	case 'SetQuestType': SetQuestType();break;
	case 'SetQuestNeedItem': SetQuestNeedItem();break;
	case 'SetQuestNeedMonster': SetQuestNeedMonster();break;
	case 'SetQuestCompleteType': SetQuestCompleteType();break;
	case 'SetDayQuestAward': SetDayQuestAward();break;
	case 'SetSpecialPartnerMission': SetSpecialPartnerMission(); break;
	default:  Quest();

}
//--------------------------------------------------------------------------------------------特殊伙伴任务信息
function SpecialPartnerMission(){
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$role_list = globalDataList('role');

	$query = $db->query("select id,name from item where type_id=21000");	
	while($rs = $db->fetch_array($query)) {
		$item_list[] =  $rs;
	}

	$query = $db->query("select id,name from item where type_id in (1001, 11001, 11002, 11003)");	
	while($rs = $db->fetch_array($query)) {
		$awarditem_list[] =  $rs;
	}

	$query = $db->query('select id, name from mission');
	while($rs = $db->fetch_array($query)) {
		$mission_list[] =  $rs;
	}

	$query = $db->query('select id, name from monster');
	while($rs = $db->fetch_array($query)) {
		$monster_list[] =  $rs;
	}

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		special_partner_mission
	"),0);	
	if($num)
	{		
		$query = $db->query("select * from special_partner_mission limit $start_num,$pageNum");	
		while($rs = $db->fetch_array($query)) {
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=quest&action=SpecialPartnerMission");	
	}	

	include_once template('t_special_partner_mission');
}
//--------------------------------------------------------------------------------------------NPC

function  Quest() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$title = ReqStr('title');
	$type = ReqNum('type');
	$town_list = globalDataList('town');//城镇
	$quest_type_list = globalDataList('quest_type');//任务类型
	$quest_complete_type_list = globalDataList('quest_complete_type');//任务完成类型
	$game_function_list = globalDataList('game_function');//游戏功能
	$camp_list = globalDataList('camp');//门派
	$query = $db->query("
	select 
		A.*,
		B.name as type_name
	from 
		item A
		left join item_type B on A.type_id = B.id
	order by 
		A.type_id asc
	");
	if($db->num_rows($query))
	{				
		while($irs = $db->fetch_array($query))
		{	
			$item_list[] =  $irs;
		}
	}		
	
	if($title)
	{
		$set_title = "and A.`title` like '%$title%'";	
	}
	if(!$type)
	{
		$type = $db->result_first("select min(id) from quest_type ");
		$set_type = "and A.type = '$type'";	
	}else{
		$set_type = "and A.type = '$type'";	
	}
	
	
	
	//--------------------MISSION----------------------------------------

	
	$query = $db->query("
	select 
		A.id,
		A.lock,
		A.name as mission_name,
		B.town_id,
		B.name as mission_section_name,
		C.name as town_name
	from 
		mission A
		left join mission_section B on A.mission_section_id = B.id
		left join town C on B.town_id = C.id
	order by
		B.town_id desc,
		A.lock desc,
		A.id desc
	");
	$i = 1;
	while($mrs = $db->fetch_array($query))
	{	
		$mrs['i'] = $i++;
		$mission_section_name = $mrs['mission_section_name'];
		//$mrs['mission_section_name_last'] = $mrs['mission_section_name'];
		//if ($mrs['mission_section_name_last'] != $mrs['mission_section_name'])  $mrs['i'] = 1;
		$mission_list[] =  $mrs;
	}	

	//--------------------NPC----------------------------------------

	
	$query = $db->query("
	select 
		A.*,
		B.name as npc_name,
		C.name as town_name
	from 
		town_npc A
		left join npc B on A.npc_id = B.id
		left join town C on A.town_id = C.id
	order by 
		A.town_id asc,
		A.npc_id asc					
	");

	while($tnrs = $db->fetch_array($query))
	{	
		$town_npc_list[] =  $tnrs;
	}	
	
	//------------------------------------------------------------
	//$maxlock = $db->result($db->query("select max(`lock`) from quest"),0);
	//$lock_n = $maxlock+100;			
	
			
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		quest A	
	where 
		A.id <> ''
		$set_type		
		$set_title	
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as quest_type_name,
			C.name as item_name,
			D.name as mission_name,
			E.name as mission_section_name,
			F.name as town_name
		from 
			quest A
			left join quest_type B on A.type = B.id
			left join item C on A.award_item_id = C.id
			left join mission D on A.mission_id = D.id
			left join mission_section E on D.mission_section_id = E.id
			left join town F on E.town_id = F.id

		where 
			A.id <> ''
			$set_type		
			$set_title
		order by 
			A.lock asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$quest_type_name = $rs['quest_type_name'];
			if (!$rs['item_name']) $rs['item_name'] = '无';
			if (!$rs['mission_name']) $rs['mission_name'] = '无';
			$rs['name_url'] = urlencode($rs['title']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=quest&type=$type&title=$title");				
	}	
	include_once template('t_quest');
}
//--------------------------------------------------------------------------------------------任务类型

function  QuestType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		quest_type
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_quest_type');
}
//--------------------------------------------------------------------------------------------完成任务场景类型

function  QuestCompleteType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		quest_complete_type
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_quest_complete_type');
}


//--------------------------------------------------------------------------------------------每日任务物品星级奖励

function  DayQuestAward() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	
	$quest_id = ReqNum('quest_id');
	$item_list = globalDataList('item');//装备
	$quest_list = globalDataList('quest','type=3');//任务
	if(!$quest_id){
		$quest_id = $db->result_first("select min(id) from quest where type=3");
	}


	if ($quest_id) 
	{
		//$quest_id = $db->result_first("select min(id) from quest ");
	//}else{
		$set_quest = "where A.quest_id = '$quest_id'";
	}
	
	
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		day_quest_award	 A 
		$set_quest
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.title as quest_title
		from 
			day_quest_award A
			left join quest B on A.quest_id = B.id
			$set_quest			
		order by 
			A.quest_id asc,
			A.star_count asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			if ($quest_id) $quest_title = $rs['quest_title'];
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=quest&action=DayQuestAward&quest_id=$quest_id");				
	}	
	include_once template('t_day_quest_award');
}


//--------------------------------------------------------------------------------------------特殊伙伴任务
function SetSpecialPartnerMission(){
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$role_id = ReqArray('role_id');
	$mission_index = ReqArray('mission_index');
	$require_level = ReqArray('require_level');
	$require_item_id = ReqArray('require_item_id');
	$require_item_amount = ReqArray('require_item_amount');
	$award_power = ReqArray('award_power');
	$award_fame = ReqArray('award_fame');
	$award_item_id = ReqArray('award_item_id');
	$award_item_amount = ReqArray('award_item_amount');
	$award_experience = ReqArray('award_experience');
	$mission_id = ReqArray('mission_id');
	$monster_id = ReqArray('monster_id');
	$item_price = ReqArray('item_price');

	$role_id_n = ReqNum('role_id_n');
	$mission_index_n = ReqNum('mission_index_n');
	$require_level_n = ReqNum('require_level_n');
	$require_item_id_n = ReqNum('require_item_id_n');
	$require_item_amount_n = ReqNum('require_item_amount_n');
	$award_power_n = ReqNum('award_power_n');
	$award_fame_n = ReqNum('award_fame_n');
	$award_item_id_n = ReqNum('award_item_id_n');
	$award_item_amount_n = ReqNum('award_item_amount_n');
	$award_experience_n = ReqNum('award_experience_n');
	$mission_id_n = ReqNum('mission_id_n');
	$monster_id_n = ReqNum('monster_id_n');
	$item_price_n = ReqNum('item_price_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $role_id[$i])
			{

				$db->query("
				update 
					special_partner_mission 
				set 
					`role_id`='$role_id[$i]',
					`mission_index`='$mission_index[$i]',
					`require_level`='$require_level[$i]',
					`require_item_id`='$require_item_id[$i]',
					`require_item_amount`='$require_item_amount[$i]',
					`award_power`='$award_power[$i]',
					`award_fame`='$award_fame[$i]',
					`award_item_id`='$award_item_id[$i]',
					`award_item_amount`='$award_item_amount[$i]',
					`award_experience`='$award_experience[$i]',
					`mission_id`='$mission_id[$i]',
					`monster_id`='$monster_id[$i]',
					`item_price`='$item_price[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($role_id_n)
	{
		$query = $db->query("
		insert into 
			special_partner_mission(
				`role_id`,
				`mission_index`,
				`require_level`,
				`require_item_id`,
				`require_item_amount`,
				`award_power`,
				`award_fame`,
				`award_item_id`,
				`award_item_amount`,
				`award_experience`,
				`mission_id`,
				`monster_id`,
				`item_price`
			)values(
				'$role_id_n',
				'$mission_index_n',
				'$require_level_n',
				'$require_item_id_n',
				'$require_item_amount_n',
				'$award_power_n',
				'$award_fame_n',
				'$award_item_id_n',
				'$award_item_amount_n',
				'$award_experience_n',
				'$mission_id_n',
				'$monster_id_n',
				'$item_price_n'
			)
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
		$db->query("delete from special_partner_mission where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置任务
function  SetQuest() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$title = ReqArray('title');
	$type = ReqArray('type');
	$lock = ReqArray('lock');
	$level = ReqArray('level');
	$content = ReqArray('content');
	$conditions = ReqArray('conditions');
	$begin_npc_id = ReqArray('begin_npc_id');
	$end_npc_id = ReqArray('end_npc_id');
	$award_experience = ReqArray('award_experience');
	$award_coins = ReqArray('award_coins');
	$award_item_id = ReqArray('award_item_id');
	$award_town_key = ReqArray('award_town_key');
	$award_quest_key = ReqArray('award_quest_key');
	$quest_complete_type_id = ReqArray('quest_complete_type_id');	
	$accept_talk = ReqArray('accept_talk');
	$accepted_talk = ReqArray('accepted_talk');
	$completed_talk = ReqArray('completed_talk');
	$is_talk_quest = ReqArray('is_talk_quest');
	$town_text = ReqArray('town_text');
	$award_skill = ReqArray('award_skill');
	$mission_id = ReqArray('mission_id');
	$accept_town_key = ReqArray('accept_town_key');
	$award_item_count = ReqArray('award_item_count');
	$depend_fun = ReqArray('depend_fun');

	$title_n = ReqStr('title_n');
	$type_n = ReqNum('type_n');
	$lock_n = ReqNum('lock_n');
	$level_n = ReqNum('level_n');
	$content_n = ReqStr('content_n');
	$conditions_n = ReqStr('conditions_n');
	$begin_npc_id_n = ReqNum('begin_npc_id_n');
	$end_npc_id_n = ReqNum('end_npc_id_n');
	$award_experience_n = ReqNum('award_experience_n');
	$award_coins_n = ReqNum('award_coins_n');
	$award_item_id_n = ReqNum('award_item_id_n');
	$award_town_key_n = ReqNum('award_town_key_n');
	$award_quest_key_n = ReqNum('award_quest_key_n');
	$quest_complete_type_id_n = ReqNum('quest_complete_type_id_n');	
	$accept_talk_n = ReqStr('accept_talk_n');
	$accepted_talk_n = ReqStr('accepted_talk_n');
	$completed_talk_n = ReqStr('completed_talk_n');
	$is_talk_quest_n = ReqNum('is_talk_quest_n');	
	$town_text_n = ReqStr('town_text_n');
	$award_skill_n = ReqNum('award_skill_n');
	$mission_id_n = ReqNum('mission_id_n');
	$accept_town_key_n = ReqNum('accept_town_key_n');
	$award_item_count_n = ReqNum('award_item_count_n');
	$depend_fun_n = ReqNum('depend_fun_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $title[$i] && $type[$i])
			{

				$db->query("
				update 
					quest 
				set 
					`title`='$title[$i]',
					`type`='$type[$i]',
					`lock`='$lock[$i]',
					`level`='$level[$i]',
					`content`='$content[$i]',
					`conditions`='$conditions[$i]',
					`begin_npc_id`='$begin_npc_id[$i]',
					`end_npc_id`='$end_npc_id[$i]',
					`award_experience`='$award_experience[$i]',
					`award_coins`='$award_coins[$i]',
					`award_item_id`='$award_item_id[$i]',
					`award_town_key`='$award_town_key[$i]',
					`award_quest_key`='$award_quest_key[$i]',
					`quest_complete_type_id`='$quest_complete_type_id[$i]',
					`accept_talk`='$accept_talk[$i]',
					`accepted_talk`='$accepted_talk[$i]',
					`completed_talk`='$completed_talk[$i]',
					`is_talk_quest`='$is_talk_quest[$i]',
					`town_text`='$town_text[$i]',
					`award_skill`='$award_skill[$i]',
					`mission_id`='$mission_id[$i]',
					`accept_town_key`='$accept_town_key[$i]',
					`award_item_count`='$award_item_count[$i]',
					`depend_fun`='$depend_fun[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($title_n && $type_n)
	{
	
		$query = $db->query("
		insert into 
			quest(
				`title`,
				`type`,
				`lock`,
				`level`,
				`content`,
				`conditions`,
				`begin_npc_id`,
				`end_npc_id`,
				`award_experience`,
				`award_coins`,
				`award_item_id`,
				`award_town_key`,
				`award_quest_key`,
				`quest_complete_type_id`,
				`accept_talk`,
				`accepted_talk`,
				`completed_talk`,
				`is_talk_quest`,
				`town_text`,
				`award_skill`,
				`mission_id`,
				`accept_town_key`,
				`award_item_count`,
				`depend_fun`
			)values(
				'$title_n',
				'$type_n',
				'$lock_n',
				'$level_n',
				'$content_n',
				'$conditions_n',
				'$begin_npc_id_n',
				'$end_npc_id_n',
				'$award_experience_n',
				'$award_coins_n',
				'$award_item_id_n',
				'$award_town_key_n',
				'$award_quest_key_n',
				'$quest_complete_type_id_n',
				'$accept_talk_n',
				'$accepted_talk_n',
				'$completed_talk_n',
				'$is_talk_quest_n',
				'$town_text_n',
				'$award_skill_n',
				'$mission_id_n',
				'$accept_town_key_n',
				'$award_item_count_n',
				'$depend_fun_n'
			)
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
		$db->query("delete from quest where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}



//--------------------------------------------------------------------------------------------批量设置任务类型
function  SetQuestType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	
	$id_n = ReqNum('id_n');
	$name = ReqArray('name');
	$name_n = ReqStr('name_n');


	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from quest_type where id in ($id_arr)");
		$msg = "删除成功！";
		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i])
			{

				$db->query("
				update 
					quest_type 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]'
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
			quest_type
			(`id`,`name`) 
		values 
			('$id_n','$name_n')
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



//--------------------------------------------------------------------------------------------批量设完成置任务场景类型
function  SetQuestCompleteType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	
	$id_n = ReqNum('id_n');
	$name = ReqArray('name');
	$name_n = ReqStr('name_n');


	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from quest_complete_type where id in ($id_arr)");
		$msg = "删除成功！";
		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i])
			{

				$db->query("
				update 
					quest_complete_type 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]'
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
			quest_complete_type
			(`id`,`name`) 
		values 
			('$id_n','$name_n')
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



//--------------------------------------------------------------------------------------------批量设置每日任务物品星级奖励
function  SetDayQuestAward() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$quest_id = ReqArray('quest_id');
	$item_id = ReqArray('item_id');
	$skill = ReqArray('skill');
	
	$star_count_n = ReqNum('star_count_n');
	$quest_id_n = ReqNum('quest_id_n');
	$item_id_n = ReqNum('item_id_n');
	$skill_n = ReqNum('skill_n');


	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from day_quest_award where star_count in ($id_arr)");
		$msg = "删除成功！";
		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $quest_id[$i])
			{

				$db->query("
				update 
					day_quest_award 
				set 
					`item_id`='$item_id[$i]',
					`skill`='$skill[$i]'
				where 
					quest_id = '$quest_id[$i]'
					and star_count = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($star_count_n && $quest_id_n)
	{
	
		$query = $db->query("
		insert into 
			day_quest_award
			(`star_count`,`quest_id`,`item_id`,`skill`) 
		values 
			('$star_count_n','$quest_id_n','$item_id_n','$skill_n')
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



//--------------------------------------------------------------------------------------------批量设置任务物品关联
function  SetQuestNeedItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$item_id = ReqArray('item_id');
	$item_count = ReqArray('item_count');
	
	$item_id_n = ReqNum('item_id_n');
	$item_count_n = ReqNum('item_count_n');
	$quest_id = ReqNum('quest_id');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $item_id[$i] && $item_count[$i])
			{

				$db->query("
				update 
					quest_need_item 
				set 
					`item_count`='$item_count[$i]'
				where 
					quest_id = '$id[$i]'
					and  item_id = '$item_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n && $item_count_n && $quest_id)
	{
	
		$query = $db->query("
		insert into 
			quest_need_item
			(`quest_id`,`item_count`,`item_id`) 
		values 
			('$quest_id','$item_count_n','$item_id_n')
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
	
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from quest_need_item where quest_id = '$idArr[0]' and item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		

		
	}		
	
		
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}


//--------------------------------------------------------------------------------------------批量设置任务物品关联
function  SetQuestNeedMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$monster_id = ReqArray('monster_id');
	$monster_count = ReqArray('monster_count');
	
	$monster_id_n = ReqNum('monster_id_n');
	$monster_count_n = ReqNum('monster_count_n');
	$quest_id = ReqNum('quest_id');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $monster_id[$i] && $monster_count[$i])
			{

				$db->query("
				update 
					quest_need_monster 
				set 
					`monster_count`='$monster_count[$i]'
				where 
					quest_id = '$id[$i]'
					and  monster_id = '$monster_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($monster_id_n && $monster_count_n && $quest_id)
	{
	
		$query = $db->query("
		insert into 
			quest_need_monster
			(`quest_id`,`monster_count`,`monster_id`) 
		values 
			('$quest_id','$monster_count_n','$monster_id_n')
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
	
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from quest_need_monster where quest_id = '$idArr[0]' and monster_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		

		
	}		
	
		
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}
?>