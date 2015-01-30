<?php

header("expires:mon,26jul199705:00:00gmt"); 
header("cache-control:no-cache,must-revalidate"); 
header("pragma:no-cache");//禁止缓存
header("Content-Type:text/html;charset=utf-8");//避免输出乱码
	
if (isset($_GET['dbs']) && $_GET['dbs']=='alpha'){
	include_once(dirname(__FILE__)."/config.inc.alpha.php");
}else{
	include_once(dirname(__FILE__)."/config.inc.php");
}
include_once(dirname(__FILE__)."/conn.php");
webAdmin('t','','','web');
switch (ReqStr('action'))
{
	case 'CallNineRegionsLevel': CallNineRegionsLevel();break;
        case 'CallWeekRankingDayAward': CallWeekRankingDayAward();break;
	case 'CallWeekRankingAward': CallWeekRankingAward();break;
	case 'CallGoldOilData': CallGoldOilData();break;
	case 'CallSpiritStateRequire': CallSpiritStateRequire();break;
	case 'CallPetAnimalStage': CallPetAnimalStage();break;
	case 'CallTownNPC': CallTownNPC();break;
	case 'CallTownNpcItem': CallTownNpcItem();break;
	case 'CallZodiacRequire': CallZodiacRequire();break;
	case 'CallShowTown': CallShowTown();break;
	case 'CallRoleJobLevelData': CallRoleJobLevelData();break;
	case 'CallDeployGrid': CallDeployGrid();break;
	case 'CallMissionScene': CallMissionScene();break;
	case 'CallMissionMonsterTeam': CallMissionMonsterTeam();break;
	case 'CallMissionMonster': CallMissionMonster();break;
	case 'CallMissionSectionItem': CallMissionSectionItem();break;
	case 'CallMissionMonsterQuestItem': CallMissionMonsterQuestItem();break;
	case 'CallMissionMonsterItem': CallMissionMonsterItem(); break;
	case 'CallMissionItem': CallMissionItem();break;
	case 'CallItemEquipJob': CallItemEquipJob();break;
	case 'CallResearchLevelData': CallResearchLevelData();break;
	case 'CallMultipleMissionMonsterTeam': CallMultipleMissionMonsterTeam();break;
	case 'CallMultipleMissionMonster': CallMultipleMissionMonster();break;
	case 'CallQuestNeedItem': CallQuestNeedItem();break;
	case 'CallQuestNeedMonster': CallQuestNeedMonster();break;
	case 'CallAvatarItemMonster': CallAvatarItemMonster();break;
	case 'CallTravelEventAnswer': CallTravelEventAnswer();break;
	case 'CallMissionFailedTips': CallMissionFailedTips();break;
	case 'CallWorldBossData': CallWorldBossData();break;
	case 'CallFateLevel': CallFateLevel();break;
	case 'CallFateQualityLevel': CallFateQualityLevel();break;
	case 'CallSoulQualityValue': CallSoulQualityValue();break;
	case 'CallTownNpcSoul': CallTownNpcSoul(); break;
	case 'CallTreasureHuntMissionAward': CallTreasureHuntMissionAward(); break;
	case 'CallTreasureHuntMissionExtraAward': CallTreasureHuntMissionExtraAward(); break;

	case 'CallEnhanceWeaponLevelup': CallEnhanceWeaponLevelup(); break;
}

//------------------------------------------------------神兵等级信息表
function CallEnhanceWeaponLevelup()
{
	global $db,$page;
	$id=ReqNum('id');
	$effect = ReqNum('effect');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;	
	
	$effect_list = globalDataList('enhance_weapon_effect');//装备类型
	$weapon_list = globalDataList('enhance_weapon');//装备类型

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			enhance_weapon_levelup
		where 
			weapon_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			enhance_weapon_levelup
		where 
			weapon_id = '$id' 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallEnhanceWeaponLevelup&id=$id&name=$name_url&effect=$effect&winid=$winid", '',10,$winid);		
	}


	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_enhance_weapon_levelup');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_enhance_weapon_levelup');
	}
}

//------------------------------------------------------寻宝关卡奖励表
function CallTreasureHuntMissionAward() {
	global $db,$page;
	$id=ReqNum('id');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;

	
	$awardquery = $db->query("
		select 
			*
		from 
			treasure_hunt_award
		order by 
			id asc
		");
			
	while($ars = $db->fetch_array($awardquery)){	
		$award_list[] =  $ars;
	}

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			treasure_hunt_mission_award
		where 
			th_mission_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			treasure_hunt_mission_award
		where 
			th_mission_id = '$id' 
		order by 
			th_mission_id asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallTreasureHuntMissionAward&id=$id&winid=$winid", '',10,$winid);		
	}
	
	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_treasure_hunt_mission_award');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_treasure_hunt_mission_award');
	}

}
//------------------------------------------------------寻宝关卡奖励额外表
function CallTreasureHuntMissionExtraAward() {
	global $db,$page;
	$id=ReqNum('id');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;

	
	$awardquery = $db->query("
		select 
			*
		from 
			treasure_hunt_award
		order by 
			id asc
		");
			
	while($ars = $db->fetch_array($awardquery)){	
		$award_list[] =  $ars;
	}

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			treasure_hunt_mission_extra_award
		where 
			th_mission_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			treasure_hunt_mission_extra_award
		where 
			th_mission_id = '$id' 
		order by 
			th_mission_id asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallTreasureHuntMissionExtraAward&id=$id&winid=$winid", '',10,$winid);		
	}
	
	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_treasure_hunt_mission_extra_award');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_treasure_hunt_mission_extra_award');
	}

}
//------------------------------------------------------九界关卡表
function CallNineRegionsLevel() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;	
	$item_list = globalDataList('item', 'type_id=22000');	
	$enhance_weapon_list = globalDataList('enhance_weapon');	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.id,
		A.name as  scene_name
	from 
		mission_scene A
		left join mission B on A.mission_id = B.id
	where
		B.type = 11
	order by 
		A.lock asc
	");	
	while($mrs = $db->fetch_array($query))
	{
		$nine_regions_list[] =  $mrs;
	}

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			nine_regions_level
		where 
			region_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			nine_regions_level
		where 
			region_id = '$id' 
		order by 
			region_level asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallNineRegionsLevel&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}


	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_nine_regions_level');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_nine_regions_level');
	}


}


//------------------------------------------------------周排行日奖励
function CallWeekRankingDayAward() 
{

	global $db,$page;
	$id=ReqNum('id');
	$desc=ReqStr('desc');
	$desc_url = urlencode($desc); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	$item_list = globalDataList('item');

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			week_ranking_day_award
		where 
			week_ranking_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			week_ranking_day_award
		where 
			week_ranking_id = '$id' 
		order by 
			rank asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallWeekRankingDayAward&id=$id&desc=$desc_url&winid=$winid", '',10,$winid);		
	}


	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_week_ranking_day_award');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_week_ranking_day_award');
	}


}

//------------------------------------------------------周排行奖励
function CallWeekRankingAward() 
{

	global $db,$page;
	$id=ReqNum('id');
	$desc=ReqStr('desc');
	$desc_url = urlencode($desc); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	$item_list = globalDataList('item');

	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			week_ranking_award
		where 
			week_ranking_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			week_ranking_award
		where 
			week_ranking_id = '$id' 
		order by 
			rank asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallWeekRankingAward&id=$id&desc=$desc_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_week_ranking_award');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_week_ranking_award');
	}

}

//------------------------------------------------------十二宫生肖关卡数据
function CallZodiacRequire() 
{

	global $db,$page;
	$level=ReqNum('level');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	
	$query = $db->query("
	select 
		A.id,
		B.name as monster_name,
		B.level

	from 
		mission_monster_team A
		left join monster B on A.monster_id = B.id
		left join mission_scene C on A.mission_scene_id = C.id
		left join mission D on C.mission_id = D.id
	where
		D.type = 7
	order by 
		level desc		
	");
	while($rs = $db->fetch_array($query))
	{	
		//$rs['name_url'] = urlencode($rs['name']);
		$monster_team_list[] =  $rs;
	}
	//------------------------------------------------------------


	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			zodiac_require
		where 
			zodiac_level = '$level' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			zodiac_require
		where 
			zodiac_level = '$level' 
		order by 
			barrier asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallZodiacRequire&level=$level&name=$name_url&winid=$winid", '',10,$winid);		
	}
	
	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_zodiac_require');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_zodiac_require');
	}

}


//------------------------------------------------------金油提升装备数据
function CallGoldOilData() 
{

	global $db,$page;
	$item_id=ReqNum('item_id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	$item_type_list = globalDataList('item_type');//物品类型
	$role_job_list = globalDataList('role_job');
	//------------------------------------------------------------


	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			gold_oil_data
		where 
			gold_oil_item_id = '$item_id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			gold_oil_data
		where 
			gold_oil_item_id = '$item_id' 
		order by 
			item_type asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallGoldOilData&item_id=$item_id&name=$name_url&winid=$winid", '',10,$winid);		
	}
	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_gold_oil_data');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_gold_oil_data');
	}

}


//------------------------------------------------------宠物阶段表
function CallPetAnimalStage() 
{

	global $db,$page;
	$lv=ReqNum('lv');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	//$item_type_list = globalDataList('item_type');//物品类型

	//------------------------------------------------------------


	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			pet_animal_stage
		where 
			pet_animal_lv = '$lv' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			pet_animal_stage
		where 
			pet_animal_lv = '$lv' 
		order by 
			stage asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallPetAnimalStage&lv=$lv&name=$name_url&winid=$winid", '',10,$winid);		
	}
	include_once template('t_pet_animal_stage');

}

//------------------------------------------------------渡劫境界等级需求
function CallSpiritStateRequire() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;		
	$mission_list = globalDataList('mission','type=4');//多人副本

	$query = $db->query("
	select 
		A.id,
		B.name as monster_name,
		B.level
	from 
		mission_monster_team A
		left join monster B on A.monster_id = B.id
		left join mission_scene C on A.mission_scene_id = C.id
		left join mission D on C.mission_id = D.id
	where
		D.type = 4
	order by 
		level desc		
	");
	while($rs = $db->fetch_array($query))
	{	
		//$rs['name_url'] = urlencode($rs['name']);
		$monster_team_list[] =  $rs;
	}
	


	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			spirit_state_require
		where 
			spirit_state_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			spirit_state_require
		where 
			spirit_state_id = '$id' 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallSpiritStateRequire&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_spirit_state_require');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_spirit_state_require');
	}

}
//------------------------------------------------------城镇NPC
function CallTownNPC() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');
	$npc_list = globalDataList('npc');//NPC
	//$npc_function_list = globalDataList('npc_function');//NPC功能
	$winid=ReqStr('winid');	
	
	
	//------------------------------------------------------------
/*	$query = $db->query("
	select 
		A.*,
		B.name as npc_name,
		C.name as npc_function_name
	from 
		town_npc  A
		left join npc B on A.npc_id = B.id
		left join npc_function C on A.resource_id = C.id
	where 
		A.town_id = '$id' 
	order by 
		A.id asc
	");*/
	
	$query = $db->query("
	select 
		A.*,
		B.name as npc_name
	from 
		town_npc  A
		left join npc B on A.npc_id = B.id
	where 
		A.town_id = '$id' 
	order by 
		A.id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($name.' ＞ '.$rs['npc_name']);
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_town_npc');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_town_npc');
	}

}

//------------------------------------------------------城镇NPC携带物品表
function CallTownNpcItem() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$item_list = golbalItemList('','require_level desc,type_id asc','id,name,sign,type_id,require_level');//物品	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.require_level,
		B.name as item_name
	from 
		town_npc_item A
		left join item B on A.item_id = B.id
	where 
		A.town_npc_id = '$id' 
	order by 
		A.item_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_town_npc_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_town_npc_item');
	}

}
//------------------------------------------------------角色职业等级信息
function CallRoleJobLevelData() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 30; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			role_job_level_data
		where 
			job_id = '$id' 
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			role_job_level_data
		where 
			job_id = '$id' 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
			
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallRoleJobLevelData&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_role_job_level_data');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_role_job_level_data');
	}
}

//------------------------------------------------------阵法站位
function CallDeployGrid() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$deploy_grid_type_list = globalDataList('deploy_grid_type');//站位
	$winid=ReqStr('winid');	

	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as deploy_grid_type_name
	from 
		deploy_grid A
		left join deploy_grid_type B on A.deploy_grid_type_id = B.id
	where 
		A.deploy_mode_id = '$id' 
	order by 
		A.id asc
	");
	$num = $db->num_rows($query);
	if($num)
	{			
		$i = 1;	
		while($rs = $db->fetch_array($query))
		{	
			$n[] = $rs['deploy_grid_type_name']; 
			$rs['i']=$i++;
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_deploy_grid');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_deploy_grid');
	}

}

//------------------------------------------------------副本中的场景
function CallMissionScene() 
{

	global $db;
	$id=ReqNum('id');
	$type=ReqNum('type');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*
	from 
		mission_scene A
		
	where 
		A.mission_id = '$id' 
	order by 
		A.id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($name.' ＞ '.$rs['name']);
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_scene');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_scene');
	}
}

//------------------------------------------------------副本中的场景中的怪物团
function CallMissionMonsterTeam() 
{

	global $db,$page;
	$id=ReqNum('id');
	$type=ReqNum('type');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$deploy_mode_list = globalDataList('deploy_mode');//阵法
	$monster_list = globalDataList('monster',"role_job_id > 0 and type=$type",'level desc');//怪物
	$mission_video_list = globalDataList('mission_video');//视频
	$winid=ReqStr('winid');	
	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			mission_monster_team A
		where 
			A.mission_scene_id = '$id' 				
		"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*,
			B.name as deploy_mode_name
		from 
			mission_monster_team A
			left join deploy_mode B on A.deploy_mode_id = B.id
		where 
			A.mission_scene_id = '$id' 
		order by 
			A.id asc
		limit 
			$start_num,$pageNum				
		");			
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($name.' ＞ '.$rs['deploy_mode_name']);
			$rs['mission_monster'] = array();
			$mission_monster = array();
			$squery = $db->query("
				select 
					A.*,
					B.name
				from 
					mission_monster A
				left join monster B on A.monster_id = B.id
				left join deploy_grid C on A.deploy_grid_id = C.id
				where 
					A.mission_monster_team_id = '$rs[id]' 
				order by 
					A.id asc
				");
			$snum = $db->num_rows($squery);
			if($snum)
			{			
				while($srs = $db->fetch_array($squery))
				{	
					$mission_monster[] = array('id'=>$srs['id'], 'name'=>$srs['name']);
				}
				$rs['mission_monster'] = $mission_monster;
			}

			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallMissionMonsterTeam&id=$id&type=$type&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_monster_team');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_monster_team');
	}

}


//------------------------------------------------------副本中的场景中的怪物成员
function CallMissionMonster() 
{

	global $db;
	$id=ReqNum('id');
	$type=ReqNum('type');
	$deploy_mode_id=ReqNum('deploy_mode_id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$monster_list = globalDataList('monster',"role_job_id > 0 and type=$type",'level desc');//怪物
	
	$query = $db->query("
	select 
		A.*,
		B.name,
		B.desc
	from 
		deploy_grid A
		left join deploy_grid_type B on A.deploy_grid_type_id = B.id
	where 
		A.deploy_mode_id = '$deploy_mode_id' 
	order by 
		A.id asc
	");
	if($db->num_rows($query))
	{				
		while($drs = $db->fetch_array($query))
		{	
			$n[] = $drs['name']; 
			$deploy_grid_list[] =  $drs;//阵法站位
		}
	}	
	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name
	from 
		mission_monster A
		left join monster B on A.monster_id = B.id
		left join deploy_grid C on A.deploy_grid_id = C.id
	where 
		A.mission_monster_team_id = '$id' 
	order by 
		A.id asc
	");
	$num = $db->num_rows($query);
	if($num)
	{			
		$i = 1;	
		while($rs = $db->fetch_array($query))
		{	
			$rs['i']=$i++;	
			$rs['name_url'] = urlencode($name.' ＞ '.$rs['name']);
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_monster');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_monster');
	}
}

//------------------------------------------------------副本奖励
function CallMissionSectionItem() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	//$item_list = globalDataList('item');//物品	
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
	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as item_name,
		C.name as type_name
	from 
		mission_item A
		left join item B on A.item_id = B.id
		left join item_type C on B.type_id = C.id
	where 
		A.mission_id = '$id' 
	order by 
		A.item_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_section_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_section_item');
	}

}



//------------------------------------------------------剧情奖励
function CallMissionItem() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	//$item_list = globalDataList('item');//物品	
	$query = $db->query("
	select 
		A.*,
		B.name as type_name
	from 
		item A
		left join item_type B on A.type_id = B.id
	order by 
		A.id desc
	");
	if($db->num_rows($query))
	{				
		while($irs = $db->fetch_array($query))
		{	
			$item_list[] =  $irs;
		}
	}			
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as item_name,
		C.name as type_name
	from 
		mission_item A
		left join item B on A.item_id = B.id
		left join item_type C on B.type_id = C.id
	where 
		A.mission_id = '$id' 
	order by 
		A.item_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_item');
	}

}
//------------------------------------------------------场景中的怪物的任务奖励
function CallMissionMonsterQuestItem() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$item_list = globalDataList('item');//物品	
	$quest_list = globalDataList('quest');//任务	

	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as item_name,
		C.title
	from 
		mission_monster_quest_item A
		left join item B on A.item_id = B.id
		left join quest C on A.quest_id = C.id
	where 
		A.mission_monster_id = '$id' 
	order by 
		A.quest_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_monster_quest_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_monster_quest_item');
	}

}

//------------------------------------------------------场景中的怪物的掉落物品
function CallMissionMonsterItem(){
	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$item_list = globalDataList('item');//物品	

	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as item_name
	from 
		mission_monster_item A
		left join item B on A.item_id = B.id
	where 
		A.mission_monster_id = '$id'
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_monster_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_monster_item');
	}
	// `mission_monster_id`     INTEGER NOT NULL               COMMENT '怪物id'
	//           ,`item_id`               INTEGER NOT NULL               COMMENT '物品id'
	//           ,`number`                INTEGER NOT NULL DEFAULT  1    COMMENT '数量'
	//           ,`probability`           FLOAT   NOT NULL DEFAULT  100  COMMENT '掉落概率'
}

//------------------------------------------------------装备穿戴要求职业对应表
function CallItemEquipJob() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$role_job_list = globalDataList('role_job','id not in (8,9,10)');//角色	

	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as role_job_name
	from 
		item_equip_job A
		left join role_job B on A.role_job_id = B.id
	where 
		A.item_id = '$id' 
	order by 
		B.id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_item_equip_job');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_item_equip_job');
	}

}

//------------------------------------------------------奇术等级表
function CallResearchLevelData() 
{

	global $db,$page;
	$id=ReqNum('id');
	$addition_type_id=ReqNum('addition_type_id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			research_level_data
		where 
			research_id = '$id'					
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			research_level_data
		where 
			research_id = '$id' 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			if ($addition_type_id == 2) $rs['research_value'] = $rs['research_value']/100;
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallResearchLevelData&id=$id&addition_type_id=$addition_type_id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_research_level_data');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_research_level_data');
	}

}



//------------------------------------------------------多人副本怪物团
function CallMultipleMissionMonsterTeam() 
{

	global $db;
	$id=ReqNum('id');
	$type=ReqNum('type');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$deploy_mode_list = globalDataList('deploy_mode');//阵法
	//$monster_list = globalDataList('monster');//怪物
	$winid=ReqStr('winid');	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as deploy_mode_name
	from 
		multiple_mission_monster_team A
		left join deploy_mode B on A.deploy_mode_id = B.id
	where 
		A.multiple_mission_id = '$id' 
	order by 
		A.lock asc,
		A.id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($name.' ＞ '.$rs['deploy_mode_name']);
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_multiple_mission_monster_team');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_multiple_mission_monster_team');
	}

}

//------------------------------------------------------多人副本怪物团的成员
function CallMultipleMissionMonster() 
{


	global $db;
	$id=ReqNum('id');
	$type=ReqNum('type');
	$deploy_mode_id=ReqNum('deploy_mode_id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$type = !$type ? $type : 4;
	$monster_list = globalDataList('monster',"role_job_id > 0 and type in (0,1)",'level desc');//怪物
	$query = $db->query("
	select 
		A.*,
		B.name,
		B.desc
	from 
		deploy_grid A
		left join deploy_grid_type B on A.deploy_grid_type_id = B.id
	where 
		A.deploy_mode_id = '$deploy_mode_id' 
	order by 
		A.id asc
	");
	if($db->num_rows($query))
	{				
		while($drs = $db->fetch_array($query))
		{	
			$n[] = $drs['name']; 
			$deploy_grid_list[] =  $drs;//阵法站位
		}
	}	
	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name
	from 
		multiple_mission_monster A
		left join monster B on A.monster_id = B.id
		left join deploy_grid C on A.deploy_grid_id = C.id
	where 
		A.multiple_mission_monster_team_id = '$id' 
	order by 
		A.id asc
	");
	$num = $db->num_rows($query);
	if($num)
	{			
		$i = 1;	
		while($rs = $db->fetch_array($query))
		{	
			$rs['i']=$i++;	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_multiple_mission_monster');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_multiple_mission_monster');
	}

}

//------------------------------------------------------任务物品关联
function CallQuestNeedItem() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	//$item_list = globalDataList('item','type_id=10004');//物品	
	
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
	
	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			quest_need_item
		where 
			quest_id = '$id'					
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			A.*,
			B.name as item_name,
			C.name as type_name
		from 
			quest_need_item A
			left join item B on A.item_id = B.id
			left join item_type C on B.type_id = C.id
		where 
			A.quest_id = '$id' 
		order by 
			A.item_id asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallQuestNeedItem&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_quest_need_item');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_quest_need_item');
	}

}



//------------------------------------------------------任务怪物关联
function CallQuestNeedMonster() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$monster_list = globalDataList('monster','role_job_id > 0','level desc');//怪物	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			quest_need_monster
		where 
			quest_id = '$id'					
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			A.*,
			B.level as monster_level,
			B.name as monster_name
		from 
			quest_need_monster A
			left join monster B on A.monster_id = B.id
		where 
			A.quest_id = '$id' 
		order by 
			A.monster_id asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallQuestNeedMonster&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_quest_need_monster');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_quest_need_monster');
	}

}
//------------------------------------------------------变身卡对应怪物
function CallAvatarItemMonster() 
{


	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$monster_list = globalDataList('monster','role_job_id > 0');//NPC
	
	$query = $db->query("
	select 
		A.*,
		B.name as monster_name
	from 
		avatar_item_monster A
		left join monster B on A.monster_id = B.id
	where 
		A.item_id = '$id' 
	order by 
		A.monster_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_avatar_item_monster');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_avatar_item_monster');
	}

}

//------------------------------------------------------事件答案
function CallTravelEventAnswer() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			travel_event_answer
		where 
			event_id = '$id'					
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			travel_event_answer
		where 
			event_id = '$id' 
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallTravelEventAnswer&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_travel_event_answer');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_travel_event_answer');
	}

}
//--------------------------------------------------------------------------------------------副本战败提示

function  CallMissionFailedTips() 
{
	global $db,$page; 
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 100; 
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$mission_failed_tips_type_list = globalDataList('mission_failed_tips_type');//战败提示类型
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission_failed_tips
	where 
		mission_id = '$id' 		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.tips_name
		from 
			mission_failed_tips A
			left join mission_failed_tips_type B on A.mission_failed_tips_type_id = B.id
		where 
			A.mission_id = '$id'			
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallMissionFailedTips&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}	

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_mission_failed_tips');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_mission_failed_tips');
	}
}


//--------------------------------------------------------------------------------------------世界BOSS对应怪物

function  CallWorldBossData() 
{
	global $db,$page; 
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
		$query = $db->query("
		select 
			A.*,
			C.name as mission_name,
			D.name as mission_section_name,
			E.name as monster_name,
			E.level as monster_level
		from 
			mission_monster_team A
			left join mission_scene B on A.mission_scene_id = B.id
			left join mission C on B.mission_id = C.id
			left join mission_section D on C.mission_section_id = D.id
			left join monster E on A.monster_id = E.id
		where 
			C.type = 2 order by monster_level desc
		");


		while($rs = $db->fetch_array($query))
		{	
			$monster_team_list[] =  $rs;
		}	
	
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		world_boss_data A
		left join mission_monster_team B on A.monster_team_id = B.id
		left join mission_scene C on B.mission_scene_id = C.id
		left join mission D on C.mission_id = D.id
		
		
	where 
		A.world_boss_id = '$id'	
		and D.type = 2		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			D.name as mission_name,
			E.name as mission_section_name,
			F.name as monster_name,
			F.level as monster_level
		from 
			world_boss_data A
			left join mission_monster_team B on A.monster_team_id = B.id
			left join mission_scene C on B.mission_scene_id = C.id
			left join mission D on C.mission_id = D.id
			left join mission_section E on D.mission_section_id = E.id
			left join monster F on B.monster_id = F.id
		where 
			A.world_boss_id = '$id'	
			and D.type = 2	
		order by 
			A.level asc	
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "t_call.php?action=CallWorldBossData&id=$id&name=$name_url&winid=$winid", '',10,$winid);		
	}	

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_world_boss_data');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_world_boss_data');
	}
}

//------------------------------------------------------命格等级
function CallFateLevel() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	

	$query = $db->query("
	select 
		*
	from 
		fate_level
	where 
		fate_id = '$id' 
	order by 
		level asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_fate_level');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_fate_level');
	}

}
//------------------------------------------------------命格品质等级
function CallFateQualityLevel() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	

	$query = $db->query("
	select 
		*
	from 
		fate_quality_level
	where 
		fate_quality_id = '$id' 
	order by 
		level asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_fate_quality_level');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_fate_quality_level');
	}

}
//------------------------------------------------------命格品质等级
function CallSoulQualityValue() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	

	$colour = array(
		'red' => array( "c" => 'red', "name" => '红色'),
		'golden' => array( "c" => 'golden', "name" => '金色'),
		'purple' => array( "c" => 'purple', "name" => '紫色'),
		'blue' => array( "c" => 'blue', "name" => '蓝色'),
		'green' => array( "c" => 'green', "name" => '绿色'),
	);

	$query = $db->query("
	select 
		*
	from 
		soul_quality_value
	where 
		soul_quality_id = '$id' 
	order by 
		unit asc,
		probability asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}

	if (isset($_GET['sdb']) && $_GET['sdb'] == 'alpha'){
		ob_start();
		include_once template('t_soul_quality_value');
		$string = ob_get_contents();
		$string = str_replace('href="?', 'href="?sdb=alpha&', $string);
		$string = str_replace('action="?', 'action="?sdb=alpha&', $string);
		$string = str_replace('t_call.php?', 't_call.php?sdb=alpha&', $string);
		$string = str_replace('t.php?', 't.php?sdb=alpha&', $string);

		ob_end_clean();
		echo $string;
	}else {
		include_once template('t_soul_quality_value');
	}

}

/*

//------------------------------------------------------副本挑战关联功能列表
function CallMissionFunction() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$game_function_list = globalDataList('game_function');//功能	
	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as game_function_name
	from 
		mission_function A
		left join game_function B on A.game_function_id = B.id
	where 
		A.mission_id = '$id' 
	order by 
		A.game_function_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}
	include_once template('t_mission_function');

}

*/
//------------------------------------------------------城镇

function CallShowTown() 
{

	$town_list = globalDataList('town');//城镇
	foreach($town_list as $rs){
		echo "obj.options[obj.options.length] = new Option('".$rs["name"]."','".$rs["id"]."');\n"; 
	}
}

//------------------------------------------------------城镇NPC携带灵件表
function CallTownNpcSoul() 
{

	global $db;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$soul_list = globalDataList('soul');//物品	
	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.*,
		B.name as soul_name
	from 
		town_npc_soul A
		left join soul B on A.soul_id = B.id
	where 
		A.town_npc_id = '$id' 
	order by 
		A.soul_id asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}
	include_once template('t_town_npc_soul');

}
?>