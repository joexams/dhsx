<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'MissionSection': MissionSection();break;
	case 'MissionList': MissionList();break;
	case 'MissionView': MissionView();break;
	case 'MissionSceneMonsterQuestItem': MissionSceneMonsterQuestItem();break;
	case 'MultipleMission': MultipleMission();break;
	case 'MissionVideo': MissionVideo();break;
	case 'MissionFailedTipsType': MissionFailedTipsType();break;
	case 'SpecialPartnerMission': SpecialPartnerMission(); break;
	
	case 'SetMission': SetMission();break;
	case 'SetMissionSection': SetMissionSection();break;
	case 'SetMissionList': SetMissionList();break;
	case 'SetMissionScene': SetMissionScene();break;
	case 'SetMultipleMission': SetMultipleMission();break;
	case 'SetMultipleMissionMonsterTeam': SetMultipleMissionMonsterTeam();break;
	case 'SetMultipleMissionMonster': SetMultipleMissionMonster();break;
	case 'SetMissionVideo': SetMissionVideo();break;
	case 'SetMissionMonsterTeam': SetMissionMonsterTeam();break;
	case 'SetMissionMonster': SetMissionMonster();break;
	case 'SetMissionSectionItem': SetMissionSectionItem();break;	
	case 'SetMissionMonsterQuestItem': SetMissionMonsterQuestItem();break;
	case 'SetMissionMonsterItem': SetMissionMonsterItem();break;
	case 'SetMissionItem': SetMissionItem();break;
	case 'SetMissionFailedTipsType': SetMissionFailedTipsType();break;	
	case 'SetMissionFailedTips': SetMissionFailedTips();break;
	case 'SetSpecialPartnerMission': SpecialPartnerMission(); break;

	default:  MissionSection();
}

//--------------------------------------------------------------------------------------------副本

function  MissionSection() 
{
	global $db,$page; 
	$section_name=ReqStr('section_name');
	$type=ReqNum('type');
	$town_id=ReqNum('town_id');
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$town_list = globalDataList('town');//城镇
	
	$mission_section_id=ReqNum('mission_section_id');
	//------------------------------------------------------------
	if ($section_name) 
	{
		$set_name = " and A.name like '%$section_name%'";	
	}
	if ($type) 
	{
		$set_type = " and A.type = '$type'";
	}
	if ($town_id == 100000) 
	{
		$set_town = '';
	}elseif(!$town_id){
		$town_id = $db->result_first("select min(id) from town ");
		$set_town = " and A.town_id = '$town_id'";	
	}else{
		$set_town = " and A.town_id = '$town_id'";	
	}
	//------------------------------------------------------------
	
	//$maxlock = $db->result($db->query("select max(`lock`) from mission_section A"),0);
	//$lock_n = $maxlock+100;	
	
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission_section A 
	where 
		A.id <> 0
		$set_town
		$set_name
		$set_type 
		$set_town
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as town_name
		from 
			mission_section A
			left join town B ON A.town_id = B.id
		where
			A.id <> 0
			$set_town
			$set_name
			$set_type
			$set_town
		order by 
			A.id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&section_name=".$section_name."&type=".$type."&town_id=".$town_id."");	

	}	
	include_once template('t_mission_section');
}

//--------------------------------------------------------------------------------------------剧情

function  MissionList() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;		
	//$mission_section_list = globalDataList('mission_section');//副本
	$type=ReqNum('type');
	$monster_list = globalDataList('monster',"role_job_id > 0",'level desc');//怪物
	$quest_list = globalDataList('quest');//任务	
	$mission_video_list = globalDataList('mission_video');//视频
	$game_function_list = globalDataList('game_function','','`lock` asc');//游戏功能
	$name=ReqStr('name');
	$mission_section_id=ReqNum('mission_section_id');
	
	
	if(!$mission_section_id){
		$mission_section_id = $db->result_first("select min(id) from mission_section");
		$set_mission_section = " and A.mission_section_id = '$mission_section_id'";	
	}else{
		$set_mission_section = " and A.mission_section_id = '$mission_section_id'";	
	}
	

	if ($name) 
	{
		$set_name = " and A.name like '%$name%'";	
	}	
	//------------------------------------------------------------
	$msquery = $db->query("
	select 
		A.*,
		B.name as town_name 
	from 
		mission_section A 
		left join town B on A.town_id  = B.id 
	");
	if($db->num_rows($msquery))
	{
		
		while($msrs = $db->fetch_array($msquery))
		{	
			$mission_section_list[] =  $msrs;
		}
	}
	
	//------------------------------------------------------------
	
	//$maxlock = $db->result($db->query("select max(`lock`) from mission A"),0);
	//$lock_n = $maxlock+100;	
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission A 
	where 
		A.id <> 0
		and A.type = '$type'
		$set_mission_section
		$set_name
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as mission_section_name
		from 
			mission A
			left join mission_section B ON A.mission_section_id = B.id
		where 
			A.id <> 0
			and A.type = '$type'
			$set_mission_section
			$set_name
		order by 
			A.lock asc,A.id asc
		limit 
			$start_num,$pageNum			
		");

		$i = 1;
		while($rs = $db->fetch_array($query))
		{	
			$rs['i'] = $i++;			
			//if ($type == 1) $rs['name_hero'] = '-英雄';
			$rs['name_url'] = urlencode($rs['name']);
			$mission_section_name = $rs['mission_section_name'];
			$mission_section_name_url = urlencode($mission_section_name);
			$list_array[] =  $rs;
		}
		$newi = $num+1;
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&action=MissionList&mission_section_id=$mission_section_id&name=$name&type=$type");	
	}	
	include_once template('t_mission_list');
}

//--------------------------------------------------------------------------------------------副本情况查看

function  MissionView() 
{
	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$type=ReqNum('type');
	//$mission_section_list = globalDataList('mission_section');//剧情
	//$mission_scene_list = globalDataList('mission_scene');//房间	
	

	
	
	$query = $db->query("
	select 
		A.*,
		B.title as quest_title,
		C.name as game_function_name,
		D.name as award_mission_name,
		E.name as role_name
	from 
		mission A 
		left join quest B on A.releate_quest_id  = B.id
		left join game_function C on A.award_function_key  = C.lock
		left join mission D on A.award_mission_key  = D.lock
		left join role E on A.award_role_key  = E.lock
	where 
		A.id = '$id'
	");
	if($db->num_rows($query))
	{				
		$rs = $db->fetch_array($query);
		
		$prs = $db->fetch_first("select id from mission where mission_section_id = '$rs[mission_section_id]' and id < '$id'  order by id desc limit 1");	//上一条
		if($prs)
		{
			$pid = $prs['id'];
		}	
		$nrs = $db->fetch_first("select id from mission where mission_section_id = '$rs[mission_section_id]' and id > '$id'  order by id asc limit 1");	//下一条
		if($nrs)
		{
			$nid = $nrs['id'];
		}			
			
		
	}
	//---奖励
	$query = $db->query("
	select 
		A.*,
		B.name as item_name
	from 
		mission_item A
		left join item B on A.item_id = B.id
	where 
		A.mission_id = '$id' 
	order by 
		A.item_id asc
	");
	if($db->num_rows($query))
	{				
		while($mirs = $db->fetch_array($query))
		{	
			$mission_item_array[] =  $mirs;
		}
	}

	
	//---房间
	$query = $db->query("
	select 
		*
	from 
		mission_scene
		
	where 
		mission_id = '$id' 
	order by 
		id asc
	");
	if($db->num_rows($query))
	{				
		while($msrs = $db->fetch_array($query))
		{	
			//-------------------------------------------------------------------------
			$query_t = $db->query("
			select 
				A.*,
				B.name as deploy_mode_name,
				C.name as monster_name
			from 
				mission_monster_team A
				left join deploy_mode B on A.deploy_mode_id = B.id
				left join monster C on A.monster_id = C.id
				
			where 
				A.mission_scene_id = '$msrs[id]' 
			order by 
				A.id asc
			");
			if($db->num_rows($query_t))
			{				
				while($trs = $db->fetch_array($query_t))
				{	
					//-------------------------------------------------------------------------
						$query_m = $db->query("
						select 
							C.*,
							D.name as monster_name,
							D.level,
							D.award_experience,
							F.desc as deploy_grid_name,
							G.name as award_item_name
						from 
							mission_monster C
							left join monster D on C.monster_id = D.id
							left join deploy_grid E on C.deploy_grid_id = E.id
							left join deploy_grid_type F on E.deploy_grid_type_id = F.id
							left join item G on D.award_item_id = G.id
						where 
							C.mission_monster_team_id = '$trs[id]' 
						order by 
							C.id asc
						");
						if($db->num_rows($query_m))
						{				
							$m = 1;
							while($mrs = $db->fetch_array($query_m))
							{
								//-------------------------------------------------------------------------
									$query_i = $db->query("
									select
										A.probability,
										B.name as quest_item_name
									from 
										mission_monster_quest_item A
										left join item B on A.item_id = B.id
									where 
										A.mission_monster_id = '$mrs[id]' 
									order by 
										A.item_id asc
									");
									if($db->num_rows($query_i))
									{				
										while($mqirs = $db->fetch_array($query_i))
										{	
											$mrs['mission_monster_quest_item_array'][] =  $mqirs;
										}
									}

								
								$monster_experience += $mrs['award_experience'];
								//-------------------------------------------------------------------------
								$trs['monster_num'] = $m++;
								$trs['mission_monster_array'][] =  $mrs;
								//$trs['m'][$mrs['deploy_grid_name']] =  $mrs;
							}
						}				
				
					//-------------------------------------------------------------------------
					$trs['name_url'] = urlencode($msrs['name'].' ＞ '.$trs['deploy_mode_name']);
					$msrs['mission_monster_team_array'][] =  $trs;
				}
			}			
			
			//-------------------------------------------------------------------------
			$msrs['name_url'] = urlencode($msrs['name']);
			$mission_scene_array[] =  $msrs;
		}
	}

	include_once template('t_mission_view');
}




//--------------------------------------------------------------------------------------------多人副本

function  MultipleMission() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;		
	$mission_list = globalDataList('mission','','`lock` asc');//副本
	$item_list = globalDataList('item','type_id=10006');//物品
	//$item_list = globalDataList('item');//物品
	$type=ReqNum('type');
	$name=ReqStr('name');
	if ($name) 
	{
		$set_name = " and name like '%$name%'";	
	}	
	$mission_id=ReqNum('mission_id');
	if ($mission_id) 
	{
		$set_mission_id = " and mission_id = '$mission_id'";	
	}
	

	//------------------------------------------------------------
	
	//$maxlock = $db->result($db->query("select max(`lock`) from mission A"),0);
	//$lock_n = $maxlock+100;	
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		multiple_mission
	where
		id > 0
		and `type` = $type
		$set_name
		$set_mission_id
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			multiple_mission
		where
			id > 0	
			and `type` = $type		
			$set_name
			$set_mission_id
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			//$mission_section_name = $rs['mission_section_name'];
			//$mission_section_name_url = urlencode($mission_section_name);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&action=MultipleMission&name=$name&type=$type");	
	}	
	include_once template('t_multiple_mission');
}


//--------------------------------------------------------------------------------------------多人副本

function  MissionVideo() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;		
	$name=ReqStr('name');
	if ($name) 
	{
		$set_name = " where name like '%$name%'";	
	}	

	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission_video
		$set_name
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			mission_video
			$set_name
		order by 
			`lock` asc,
			id asc
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&action=MissionVideo&name=$name");	
	}	
	include_once template('t_mission_video');
}
//--------------------------------------------------------------------------------------------副本战败提示类型

function  MissionFailedTipsType() 
{
	global $db,$page; 
	$pageNum = 100; 
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission_failed_tips_type	
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			mission_failed_tips_type			
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mission&action=CallMissionFailedTipsType");	
	}	
	include_once template('t_mission_failed_tips_type');
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

	$query = $db->query("select id,name from item where type_id in (11001, 11002, 11003)");	
	while($rs = $db->fetch_array($query)) {
		$awarditem_list[] =  $rs;
	}

	include_once template('t_special_partner_mission');
}

//--------------------------------------------------------------------------------------------批量设置剧情
function  SetMissionSection() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$lock = ReqArray('lock');
	$town_id = ReqArray('town_id');
	$award_skill = ReqArray('award_skill');
	$award_coins = ReqArray('award_coins');
	$award_experience = ReqArray('award_experience');
	$award_section_key = ReqArray('award_section_key');

	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$lock_n = ReqNum('lock_n');
	$town_id_n = ReqNum('town_id_n');
	$award_skill_n = ReqNum('award_skill_n');
	$award_coins_n = ReqNum('award_coins_n');
	$award_experience_n = ReqNum('award_experience_n');
	$award_section_key_n = ReqNum('award_section_key_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i] && $town_id[$i])
			{

				$db->query("
				update 
					mission_section 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`lock`='$lock[$i]',
					`town_id` = '$town_id[$i]',
					`award_skill` = '$award_skill[$i]',
					`award_coins` = '$award_coins[$i]',
					`award_experience` = '$award_experience[$i]',
					`award_section_key` = '$award_section_key[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n &&$town_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_section(
				`name`,
				`sign`,
				`lock`,
				`town_id`,
				`award_skill`,
				`award_coins`,
				`award_experience`,
				`award_section_key`
			) values (
				'$name_n',
				'$sign_n',
				'$lock_n',
				'$town_id_n',
				'$award_skill_n',
				'$award_coins_n',
				'$award_experience_n',
				'$award_section_key_n'
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
		
		$delsql = "select id from mission where mission_section_id in ($id_arr)";
		$delsql_2 = "select id from mission_scene where mission_id in ($delsql)";
		$delsql_3 = "select id from mission_monster_team where mission_scene_id in ($delsql_2)";
		$delsql_4 = "select id from mission_monster where mission_monster_team_id in ($delsql_3)";
		
		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($delsql_4)");		
		$db->query("delete from mission_monster where mission_monster_team_id in ($delsql_3)");			
		$db->query("delete from mission_monster_team where mission_scene_id in ($delsql_2)");	
		$db->query("delete from mission_scene where mission_id in ($delsql)");
		$db->query("delete from mission_failed_tips where mission_id in ($delsql)");	
		$db->query("delete from mission_item where mission_id in ($delsql)");	
		$db->query("delete from mission where mission_section_id in ($id_arr)");	
		$db->query("delete from mission_section_item where mission_section_id in ($id_arr)");	
		$db->query("delete from mission_section where id in ($id_arr)");
		
		
		
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置副本
function  SetMissionList() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$lock = ReqArray('lock');
	$name = ReqArray('name');
	$require_power = ReqArray('require_power');
	$require_level = ReqArray('require_level');
	$award_skill = ReqArray('award_skill');
	$award_coins = ReqArray('award_coins');
	$completion = ReqArray('completion');
	$award_experience = ReqArray('award_experience');
	$description = ReqArray('description');
	$releate_quest_id = ReqArray('releate_quest_id');
	$award_mission_key = ReqArray('award_mission_key');
	$award_function_key = ReqArray('award_function_key');
	$mission_video_id = ReqArray('mission_video_id');	
	$average_attack = ReqArray('average_attack');
	$average_damage = ReqArray('average_damage');
	$award_role_key = ReqArray('award_role_key');	
	$monster_id = ReqArray('monster_id');
	$is_boss = ReqArray('is_boss');
	$item_probability = ReqArray('item_probability');
	$add_strength = ReqArray('add_strength');
	$add_agile = ReqArray('add_agile');
	$add_intellect = ReqArray('add_intellect');
	$is_disable = ReqArray('is_disable');

	$mission_section_id_n = ReqNum('mission_section_id_n');
	$lock_n = ReqNum('lock_n');
	$name_n = ReqStr('name_n');
	$require_power_n = ReqNum('require_power_n');
	$require_level_n = ReqNum('require_level_n');
	$award_skill_n = ReqNum('award_skill_n');
	$award_coins_n = ReqNum('award_coins_n');
	$completion_n = ReqNum('completion_n');
	$award_experience_n = ReqNum('award_experience_n');
	$description_n = ReqStr('description_n');
	$releate_quest_id_n = ReqNum('releate_quest_id_n');
	$award_mission_key_n = ReqNum('award_mission_key_n');
	$award_function_key_n = ReqNum('award_function_key_n');
	$mission_video_id_n = ReqStr('mission_video_id_n');
	$average_attack_n = ReqNum('average_attack_n');
	$average_damage_n = ReqNum('average_damage_n');
	$award_role_key_n = ReqNum('award_role_key_n');
	$monster_id_n = ReqStr('monster_id_n');
	$type_n = ReqNum('type_n');
	$is_boss_n = ReqNum('is_boss_n');
	$item_probability_n = ReqNum('item_probability_n');
	$add_strength_n = ReqNum('add_strength_n');
	$add_agile_n = ReqNum('add_agile_n');
	$add_intellect_n = ReqNum('add_intellect_n');
	$is_disable_n = ReqNum('is_disable_n');
	if (!$monster_id_n) $monster_id_n = 'NULL';
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $lock[$i] && $name[$i])
			{
				if (!$monster_id[$i]) $monster_id[$i] = 'NULL';
				
				$db->query("
				update 
					mission 
				set 
					`name`='$name[$i]',
					`lock`='$lock[$i]',
					`completion` = '$completion[$i]',
					`require_power`='$require_power[$i]',
					`require_level` = '$require_level[$i]',
					`award_skill` = '$award_skill[$i]',
					`award_coins` = '$award_coins[$i]',
					`award_experience` = '$award_experience[$i]',
					`description` = '$description[$i]',
					`releate_quest_id` = '$releate_quest_id[$i]',
					`award_mission_key` = '$award_mission_key[$i]',
					`award_function_key` = '$award_function_key[$i]',
					`mission_video_id` = $mission_video_id[$i],
					`average_attack` = '$average_attack[$i]',
					`average_damage` = '$average_damage[$i]',
					`award_role_key` = '$award_role_key[$i]',
					`is_boss` = '$is_boss[$i]',		
					`monster_id` = $monster_id[$i],
					`item_probability` = $item_probability[$i],
					`add_strength` = $add_strength[$i],
					`add_agile` = $add_agile[$i],
					`add_intellect` = $add_intellect[$i],
					`is_disable` = $is_disable[$i]
					
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($lock_n && $name_n && $mission_section_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission(
				`mission_section_id`,
				`lock`,
				`name`,
				`require_power`,
				`require_level`,
				`award_skill`,
				`award_coins`,
				`completion`,
				`award_experience`,
				`description`,
				`releate_quest_id`,
				`award_mission_key`,
				`award_function_key`,
				`mission_video_id`,
				`average_attack`,
				`average_damage`,
				`award_role_key`,
				`monster_id`,
				`is_boss`,
				`item_probability`,
				`type`,
				`add_strength`,
				`add_agile`,
				`add_intellect`,
				`is_disable`
			)values(
				'$mission_section_id_n',
				'$lock_n',
				'$name_n',
				'$require_power_n',
				'$require_level_n',
				'$award_skill_n',
				'$award_coins_n',
				'$completion_n',
				'$award_experience_n',
				'$description_n',
				'$releate_quest_id_n',
				'$award_mission_key_n',
				'$award_function_key_n',
				$mission_video_id_n,
				'$average_attack_n',
				'$average_damage_n',
				'$award_role_key_n',
				$monster_id_n,
				'$is_boss_n',
				'$item_probability_n',
				'$type_n',
				'$add_strength_n',
				'$add_agile_n',
				'$add_intellect_n',
				'$is_disable_n'
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
		$delsql = "select id from mission_scene where mission_id in ($id_arr)";
		$delsql_2 = "select id from mission_monster_team where mission_scene_id in ($delsql)";
		$delsql_3 = "select id from mission_monster where mission_monster_team_id in ($delsql_2)";

		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($delsql_3)");		
		$db->query("delete from mission_monster where mission_monster_team_id in ($delsql_2)");		
		$db->query("delete from mission_monster_team where mission_scene_id in ($delsql)");
		$db->query("delete from mission_scene where mission_id in ($id_arr)");
		$db->query("delete from mission_item where mission_id in ($id_arr)");	
		$db->query("delete from mission_failed_tips where mission_id in ($id_arr)");	
		$db->query("delete from mission where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置的副本房间信息
function  SetMissionScene() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$lock = ReqArray('lock');
	$map = ReqArray('map');

	$mission_id = ReqNum('mission_id');
	$name_n = ReqStr('name_n');
	$lock_n = ReqStr('lock_n');
	$map_n = ReqStr('map_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
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
					mission_scene 
				set 
					`name`='$name[$i]',
					`lock`='$lock[$i]',
					`map`='$map[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_id && $name_n)
	{
	
		$query = $db->query("
		insert into 
			mission_scene
			(`mission_id`,`name`,`lock`,`map`) 
		values 
			('$mission_id','$name_n','$lock_n','$map_n')
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
		$delsql = "select id from mission_monster_team where mission_scene_id in ($id_arr)";	
		$delsql_2 = "select id from mission_monster where mission_monster_team_id in ($delsql)";

		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($delsql_2)");			
		$db->query("delete from mission_monster where mission_monster_team_id in ($delsql)");		
		$db->query("delete from mission_monster_team where mission_scene_id in ($id_arr)");				
		$db->query("delete from mission_scene where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}		
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}



//--------------------------------------------------------------------------------------------批量设置多人副本
function  SetMultipleMission() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$mission_id = ReqArray('mission_id');
	$award_skill = ReqArray('award_skill');
	$award_experience = ReqArray('award_experience');
	$award_item = ReqArray('award_item');
        $award_fame = ReqArray('award_fame');

	$name_n = ReqStr('name_n');
	$mission_id_n = ReqNum('mission_id_n');
	$award_skill_n = ReqNum('award_skill_n');
	$award_experience_n = ReqNum('award_experience_n');
	$award_item_n = ReqNum('award_item_n');
	$award_fame_n = ReqNum('award_fame_n');
	$type_n = ReqNum('type_n');
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
					multiple_mission 
				set 
					`name`='$name[$i]',
					`mission_id` = '$mission_id[$i]',
					`award_skill` = '$award_skill[$i]',
					`award_experience` = '$award_experience[$i]',
					`award_item` = '$award_item[$i]',
					`award_fame` = '$award_fame[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
		$query = $db->query("
		insert into 
			multiple_mission(
				`name`,
				`mission_id`,
				`award_skill`,
				`award_experience`,
				`award_item`,
				`award_fame`,
				`type`
			) values (
				'$name_n',
				'$mission_id_n',
				'$award_skill_n',
				'$award_experience_n',
				'$award_item_n',
				'$award_fame_n',
				'$type_n'
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
		
		$delsql = "select id from multiple_mission_monster_team where multiple_mission_id in ($id_arr)";	
		
		$db->query("delete from multiple_mission_monster where multiple_mission_monster_team_id in ($delsql)");
		$db->query("delete from multiple_mission_monster_team where multiple_mission_id in ($id_arr)");
		$db->query("delete from multiple_mission where id in ($id_arr)");
		
		
		
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置视频
function  SetMissionVideo() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$lock = ReqArray('lock');
	$file_name = ReqArray('file_name');

	$name_n = ReqStr('name_n');
	$lock_n = ReqStr('lock_n');
	$file_name_n = ReqNum('file_name_n');
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
					mission_video 
				set 
					`name`='$name[$i]',
					`lock`='$lock[$i]',
					`file_name` = '$file_name[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
		$query = $db->query("
		insert into 
			mission_video(
				`name`,
				`lock`,
				`file_name`
			) values (
				'$name_n',
				'$lock_n',
				'$file_name_n'
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
		$db->query("delete from mission_video where id in ($id_arr)");
		
		
		
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置多人副本怪物团
function  SetMultipleMissionMonsterTeam() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$deploy_mode_id = ReqArray('deploy_mode_id');
	$lock = ReqArray('lock');

	$multiple_mission_id = ReqNum('multiple_mission_id');
	$lock_n = ReqNum('lock_n');
	$deploy_mode_id_n = ReqNum('deploy_mode_id_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $deploy_mode_id[$i])
			{

				$db->query("
				update 
					multiple_mission_monster_team 
				set 
					`lock`='$lock[$i]',
					`deploy_mode_id` = '$deploy_mode_id[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($lock_n && $deploy_mode_id_n && $multiple_mission_id)
	{
		$query = $db->query("
		insert into 
			multiple_mission_monster_team(
				`lock`,
				`deploy_mode_id`,
				`multiple_mission_id`
			) values (
				'$lock_n',
				'$deploy_mode_id_n',
				'$multiple_mission_id'
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
		$db->query("delete from multiple_mission_monster where multiple_mission_monster_team_id in ($id_arr)");
		$db->query("delete from multiple_mission_monster_team where id in ($id_arr)");	
		$msg .= "<br />删除成功！";
	}	
			
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}


//--------------------------------------------------------------------------------------------批量设置多人副本怪物团成员
function  SetMultipleMissionMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$monster_id = ReqArray('monster_id');
	$deploy_grid_id = ReqArray('deploy_grid_id');
	$speed = ReqArray('speed');
	
	$multiple_mission_monster_team_id= ReqNum('multiple_mission_monster_team_id');
	$monster_id_n= ReqNum('monster_id_n');
	$deploy_grid_id_n = ReqNum('deploy_grid_id_n');
	$speed_n = ReqNum('speed_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $monster_id[$i] && $deploy_grid_id[$i])
			{

				$db->query("
				update 
					multiple_mission_monster 
				set 
					`monster_id`='$monster_id[$i]',
					`deploy_grid_id`='$deploy_grid_id[$i]',
					`speed`='$speed[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($multiple_mission_monster_team_id && $monster_id_n && $deploy_grid_id_n)
	{
	
		$query = $db->query("
		insert into 
			multiple_mission_monster
			(`multiple_mission_monster_team_id`,`monster_id`,`deploy_grid_id`,`speed`) 
		values 
			('$multiple_mission_monster_team_id','$monster_id_n','$deploy_grid_id_n','$speed_n')
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
		$db->query("delete from multiple_mission_monster where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}		
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------批量设置的副本房间中的怪物组信息
function  SetMissionMonsterTeam() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$lock = ReqArray('lock');
	$deploy_mode_id = ReqArray('deploy_mode_id');
	$monster_id = ReqArray('monster_id');
	$position_x = ReqArray('position_x');
	$position_y = ReqArray('position_y');
	$map_margin_x = ReqArray('map_margin_x');
	$map_margin_y = ReqArray('map_margin_y');	
	$start_mission_video_id = ReqArray('start_mission_video_id');
	$end_mission_video_id = ReqArray('end_mission_video_id');
	$award_aura = ReqArray('award_aura');
	$max_bout_number = ReqArray('max_bout_number');
	$request_bout_number = ReqArray('request_bout_number');
	$fuhuo_mission_monster_id = ReqArray('fuhuo_mission_monster_id');
	$fuhuo_bout_number = ReqArray('fuhuo_bout_number');
	$attack_can_not_dead_number = ReqArray('attack_can_not_dead_number');
	$kill_boss_id = ReqArray('kill_boss_id');

	$lock_n = ReqNum('lock_n');
	$mission_scene_id = ReqNum('mission_scene_id');
	$deploy_mode_id_n = ReqNum('deploy_mode_id_n');
	$monster_id_n = ReqNum('monster_id_n');	
	$position_x_n = ReqNum('position_x_n');	
	$position_y_n = ReqNum('position_y_n');	
	$map_margin_x_n = ReqNum('map_margin_x_n');
	$map_margin_y_n = ReqNum('map_margin_y_n');	
	$start_mission_video_id_n = ReqStr('start_mission_video_id_n');
	$end_mission_video_id_n = ReqStr('end_mission_video_id_n');
	$award_aura_n = ReqStr('award_aura_n');
	$max_bout_number_n = ReqStr('max_bout_number_n');
	$request_bout_number_n = ReqStr('request_bout_number_n');
	$fuhuo_mission_monster_id_n = ReqNum('fuhuo_mission_monster_id_n');
	$fuhuo_bout_number_n = ReqNum('fuhuo_bout_number_n');
	$attack_can_not_dead_number_n = ReqNum('attack_can_not_dead_number_n');
	$kill_boss_id_n = ReqNum('kill_boss_id_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);
		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $deploy_mode_id[$i])
			{
				$db->query("
				update 
					mission_monster_team 
				set 
					`lock`='$lock[$i]',
					`deploy_mode_id`='$deploy_mode_id[$i]',
					`monster_id`='$monster_id[$i]',
					`position_x`='$position_x[$i]',
					`position_y`='$position_y[$i]',
					`map_margin_x`='$map_margin_x[$i]',
					`map_margin_y`='$map_margin_y[$i]',					
					`start_mission_video_id`='$start_mission_video_id[$i]',
					`end_mission_video_id`='$end_mission_video_id[$i]',
					`max_bout_number`=$max_bout_number[$i],
					`request_bout_number`=$request_bout_number[$i],
					`fuhuo_mission_monster_id`='$fuhuo_mission_monster_id[$i]',
					`attack_can_not_dead_number`='$attack_can_not_dead_number[$i]',
					`fuhuo_bout_number`='$fuhuo_bout_number[$i]',
					`kill_boss_id`='$kill_boss_id[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_scene_id && $deploy_mode_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_monster_team
			(`mission_scene_id`,`deploy_mode_id`,`lock`,`monster_id`,`position_x`,`position_y`,`start_mission_video_id`,`end_mission_video_id`,`map_margin_x`,`map_margin_y`,`max_bout_number`,`request_bout_number`,`fuhuo_mission_monster_id`,`fuhuo_bout_number`,`attack_can_not_dead_number`,`kill_boss_id`) 
		values 
			('$mission_scene_id','$deploy_mode_id_n','$lock_n','$monster_id_n','$position_x_n','$position_y_n',$start_mission_video_id_n,$end_mission_video_id_n,$map_margin_x_n,$map_margin_y_n,$max_bout_number_n,$request_bout_number_n,$fuhuo_mission_monster_id_n,$fuhuo_bout_number_n, '$attack_can_not_dead_number_n', '$kill_boss_id_n')
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
		$delsql = "select id from mission_monster where mission_monster_team_id in ($id_arr)";		
				
		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($delsql)");					
		$db->query("delete from mission_monster where mission_monster_team_id in ($id_arr)");
		$db->query("delete from mission_monster_team where id in ($id_arr)");
		
		$msg .= "<br />删除成功！";
		
	}		
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}



//--------------------------------------------------------------------------------------------批量设置的副本房间中的怪物组成员信息
function  SetMissionMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$monster_id = ReqArray('monster_id');
	$deploy_grid_id = ReqArray('deploy_grid_id');
	$momentum = ReqArray('momentum');
	$speed = ReqArray('speed');
	
	$mission_monster_team_id= ReqNum('mission_monster_team_id');
	$monster_id_n= ReqNum('monster_id_n');
	$deploy_grid_id_n = ReqNum('deploy_grid_id_n');
	$momentum_n = ReqNum('momentum_n') ? ReqNum('momentum_n') : 50;
	$speed_n = ReqNum('speed_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $monster_id[$i] && $deploy_grid_id[$i])
			{
				$momentum[$i] = $momentum[$i] ? $momentum[$i] : 50;

				$db->query("
				update 
					mission_monster 
				set 
					`monster_id`='$monster_id[$i]',
					`deploy_grid_id`='$deploy_grid_id[$i]',
					`momentum`='$momentum[$i]',
					`speed`='$speed[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_monster_team_id && $monster_id_n && $deploy_grid_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_monster
			(`mission_monster_team_id`,`monster_id`,`deploy_grid_id`,`momentum`,`speed`) 
		values 
			('$mission_monster_team_id','$monster_id_n','$deploy_grid_id_n','$momentum_n','$speed_n')
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
		$db->query("delete from mission_monster_quest_item where mission_monster_id in ($id_arr)");					
		$db->query("delete from mission_monster where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}		
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}






//--------------------------------------------------------------------------------------------批量设置剧情奖励
function  SetMissionSectionItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$mission_id = ReqArray('mission_id');
	$item_id = ReqArray('item_id');
	$number = ReqArray('number');
	
	$mission_id_n = ReqNum('mission_id_n');
	$item_id_n = ReqNum('item_id_n');
	$number_n = ReqNum('number_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($mission_id)
	{
	
		$id_num = count($mission_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($mission_id[$i] && $item_id[$i] && $number[$i])
			{

				$db->query("
				update 
					mission_item 
				set 
					`item_id`='$item_id[$i]',
					`number`='$number[$i]'
				where 
					mission_id = '$mission_id[$i]'
				and 
					item_id = '$item_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_section_id_n && $item_id_n && $number_n)
	{
	
		$query = $db->query("
		insert into 
			mission_item
			(`mission_id`,`item_id`,`number`) 
		values 
			('$mission_id_n','$item_id_n','$number_n')
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
			$db->query("delete from mission_item where mission_id = '$idArr[0]' and item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}




//--------------------------------------------------------------------------------------------批量设置副本奖励
function  SetMissionItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$mission_id = ReqArray('mission_id');
	$item_id = ReqArray('item_id');
	$number = ReqArray('number');
	$probability = ReqArray('probability');
	
	$mission_id_n = ReqNum('mission_id_n');
	$item_id_n = ReqNum('item_id_n');
	$number_n = ReqNum('number_n');
	$probability_n = ReqStr('probability_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($mission_id)
	{
	
		$id_num = count($mission_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($mission_id[$i] && $item_id[$i] && $number[$i])
			{

				$db->query("
				update 
					mission_item 
				set 
					`item_id`='$item_id[$i]',
					`number`='$number[$i]',
					`probability`='$probability[$i]'
				where 
					mission_id = '$mission_id[$i]'
				and 
					item_id = '$item_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_id_n && $item_id_n && $number_n)
	{
	
		$query = $db->query("
		insert into 
			mission_item
			(`mission_id`,`item_id`,`number`,`probability`) 
		values 
			('$mission_id_n','$item_id_n','$number_n','$probability_n')
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
			$db->query("delete from mission_item where mission_id = '$idArr[0]' and item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


/*
//--------------------------------------------------------------------------------------------批量设置关联功能
function  SetMissionFunction() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$mission_id = ReqArray('mission_id');
	$game_function_id = ReqArray('game_function_id');

	
	$mission_id_n = ReqNum('mission_id_n');
	$game_function_id_n = ReqNum('game_function_id_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	

		
	//-----------------增加记录-------------------------------------------
	if ($mission_id_n && $game_function_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_function
			(`mission_id`,`game_function_id`) 
		values 
			('$mission_id_n','$game_function_id_n')
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
			$db->query("delete from mission_function where mission_id = '$idArr[0]' and game_function_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

*/

//--------------------------------------------------------------------------------------------批量设置怪物任务奖励
function  SetMissionMonsterQuestItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$mission_monster_id = ReqArray('mission_monster_id');
	$item_id = ReqArray('item_id');
	$quest_id = ReqArray('quest_id');
	$probability = ReqArray('probability');
	
	$mission_monster_id_n = ReqNum('mission_monster_id_n');
	$item_id_n = ReqNum('item_id_n');
	$quest_id_n = ReqNum('quest_id_n');
	$probability_n = ReqNum('probability_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($mission_monster_id)
	{
	
		$id_num = count($mission_monster_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($mission_monster_id[$i] && $item_id[$i] && $quest_id[$i] && $probability[$i])
			{

				$db->query("
				update 
					mission_monster_quest_item 
				set 
					`item_id`='$item_id[$i]',
					`probability`='$probability[$i]'
				where 
					mission_monster_id = '$mission_monster_id[$i]'
				and 
					quest_id = '$quest_id[$i]'
					
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_monster_id_n && $item_id_n && $quest_id_n && $probability_n)
	{
	
		$query = $db->query("
		insert into 
			mission_monster_quest_item
			(`mission_monster_id`,`item_id`,`quest_id`,`probability`) 
		values 
			('$mission_monster_id_n','$item_id_n','$quest_id_n','$probability_n')
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
			$db->query("delete from mission_monster_quest_item where mission_monster_id = '$idArr[0]' and quest_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

//--------------------------------------------------------------------------------------------批量设置怪物掉落
function SetMissionMonsterItem(){
	global $db; 
	$id_del = ReqArray('id_del');
	$mission_monster_id = ReqArray('mission_monster_id');
	$item_id = ReqArray('item_id');
	$number = ReqArray('number');
	$probability = ReqArray('probability');
	
	$mission_monster_id_n = ReqNum('mission_monster_id_n');
	$item_id_n = ReqNum('item_id_n');
	$number_n = ReqNum('number_n');
	$probability_n = ReqNum('probability_n');

	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($mission_monster_id)
	{
	
		$id_num = count($mission_monster_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($mission_monster_id[$i] && $item_id[$i])
			{

				$db->query("
				update 
					mission_monster_item 
				set 
					`number`=$number[$i],
					`item_id`='$item_id[$i]',
					`probability`='$probability[$i]'
				where 
					mission_monster_id = '$mission_monster_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mission_monster_id_n && $item_id_n && $probability_n)
	{
	
		$query = $db->query("
		insert into 
			mission_monster_item
			(`mission_monster_id`,`item_id`,`number`,`probability`) 
		values 
			('$mission_monster_id_n','$item_id_n','$number_n','$probability_n')
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
			$db->query("delete from mission_monster_item where mission_monster_id = '$idArr[0]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}
//--------------------------------------------------------------------------------------------批量设置副本战败提示类型
function  SetMissionFailedTipsType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$tips_name = ReqArray('tips_name');
	$tips_sign = ReqArray('tips_sign');
	$description = ReqArray('description');
	
	$tips_name_n = ReqStr('tips_name_n');
	$tips_sign_n = ReqStr('tips_sign_n');
	$description_n = ReqStr('description_n');

	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $tips_name[$i] && $tips_sign[$i])
			{

				$db->query("
				update 
					mission_failed_tips_type
				set 
					`tips_sign`='$tips_sign[$i]',
					`tips_name`='$tips_name[$i]',
					`description`='$description[$i]'
				where 
					id = '$id[$i]'
					
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($tips_name_n && $tips_sign_n)
	{
	
		$query = $db->query("
		insert into 
			mission_failed_tips_type
			(`tips_name`,`tips_sign`,`description`) 
		values 
			('$tips_name_n','$tips_sign_n','$description_n')
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
		$db->query("delete from mission_failed_tips_type where id in ($id_arr)");					
		$msg .= "<br />删除成功！";
		
	}		
	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置副本战败提示
function  SetMissionFailedTips() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	
	$mission_id_n = ReqNum('mission_id_n');
	$mission_failed_tips_type_id_n = ReqStr('mission_failed_tips_type_id_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from mission_failed_tips where mission_id = '$idArr[0]' and mission_failed_tips_type_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}
	//-----------------增加记录-------------------------------------------
	if ($mission_id_n && $mission_failed_tips_type_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_failed_tips
			(`mission_id`,`mission_failed_tips_type_id`) 
		values 
			('$mission_id_n','$mission_failed_tips_type_id_n')
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

?>