<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'WorldBoss': WorldBoss();break;
	case 'TakeBibleMonster': TakeBibleMonster(); break;

	case 'SetMonster': SetMonster();break;
	case 'SetWorldBoss': SetWorldBoss();break;
	case 'SetWorldBossData': SetWorldBossData();break;
	case 'SetTakeBibleMonster': SetTakeBibleMonster();break;
	default:  Monster();
}

//--------------------------------------------------------------------------------------------护送取经怪
function TakeBibleMonster() {
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	
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
		D.type = 13
	order by 
		level desc		
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
		take_bible_monster
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			take_bible_monster
		order by 
			player_level asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=monster&action=TakeBibleMonster");	
	}	
	include_once template('t_take_bible_monster');
}


function SetTakeBibleMonster() {
	global $db; 
	$id_old = ReqArray('id_old');
	$id_del = ReqArray('id_del');
	$player_level = ReqArray('player_level');
	$mission_monster_team_id = ReqArray('mission_monster_team_id');
	
	$player_level_n = ReqNum('player_level_n');
	$mission_monster_team_id_n = ReqNum('mission_monster_team_id_n');
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($player_level[$i])
			{

				$db->query("
				update 
					take_bible_monster 
				set 
					`player_level`='$player_level[$i]',
					`mission_monster_team_id`='$mission_monster_team_id[$i]'
				where 
					player_level = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($player_level_n)
	{
	
		$query = $db->query("
		insert into 
			take_bible_monster
			(`player_level`,`mission_monster_team_id`) 
		values 
			('$player_level_n','$mission_monster_team_id_n')
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
			$db->query("delete from take_bible_monster where player_level = '$idArr[0]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');
}


//--------------------------------------------------------------------------------------------怪物

function  Monster() 
{
	global $db,$page; 
	$type=ReqNum('type');
	$level=ReqStr('level');
	$name=ReqStr('name');
	$mission_section_id=ReqStr('mission_section_id');
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;		
	$item_list = globalDataList('item','type_id in (10003,10005)');//物品
	$role_stunt_list = globalDataList('role_stunt');//战法
	$role_job_list = globalDataList('role_job');//职业
	if ($type ==  0 || $type == 1){
		$mtype = '0,4,13,12,16,11';
	}elseif($type == 5) {
		$mtype = 0;
	}elseif ($type ==  3) 
	{
		$mtype = '0,3';
	}elseif ($type ==  7) 
	{
		$mtype = '0,7';
	}elseif ($type ==  8) 
	{
		$mtype = '0,8';
	}elseif ($type ==  9) 
	{
		$mtype = '0,9,3,7';
	}elseif ($type ==  10) 
	{
		$mtype = '0';
	}elseif ($type ==  11) 
	{
		$mtype = '0,11';
	}elseif ($type ==  12) 
	{
		$mtype = '0,11,12';
	}elseif ($type ==  14) 
	{
		$mtype = '0,14';
	}elseif ($type ==  15) 
	{
		$mtype = '0,15';
	}elseif ($type ==  16) 
	{
		$mtype = '0,14,16';
	}elseif ($type ==  17) 
	{
		$mtype = '0,17';
	}elseif ($type ==  18) 
	{
		$mtype = '0,18';
	}elseif ($type ==  19) 
	{
		$mtype = '0,19';
	}else{
		$mtype = $type;
		$set_order = "name asc,level asc,sign asc,";
	}
	$monster_list = globalDataList('monster',"role_job_id > 0 and type in ($mtype)",'level desc');//怪物
	
	//------------------------------------------------------------
	if ($name) 
	{
		$set_name = " and A.name like '%$name%'";	
	}	
	if ($level) 
	{
		$level_arr = explode(',',$level);
		$set_level = " and A.level >= '$level_arr[0]' and A.level <= '$level_arr[1]'" ;	
	}		
	//-------------------------所有副本-----------------------------------
	
	$mquery = $db->query("
	select 
		A.*,
		B.name as town_name 
	from 
		mission_section A 
		left join town B on A.town_id  = B.id 
	");
	if($db->num_rows($mquery))
	{
		
		while($mrs = $db->fetch_array($mquery))
		{	
			$mission_section_list[] =  $mrs;
		}
	}
	//--------------------------副本下的所有怪物----------------------------------
	if($mission_section_id){


		$mquery = $db->query("
		select 
			A.monster_id
		from 
			mission_monster A 
			left join mission_monster_team B on A.mission_monster_team_id  = B.id 
			left join mission_scene C on B.mission_scene_id  = C.id 
			left join mission D on C.mission_id  = D.id
			left join mission_section E on D.mission_section_id  = E.id
			left join monster F on A.monster_id  = F.id
		where 
			E.id = '$mission_section_id'
			and D.type = '$type'
			and F.type = '$type'
		");
		if($db->num_rows($mquery))
		{
			
			while($mrs = $db->fetch_array($mquery))
			{	
				$mission_monster_id .=  $mrs['monster_id'].',';
			}
		}
		if($mission_monster_id)
		{
			$monster_id = substr($mission_monster_id,0,strlen($mission_monster_id)-1);
			$set_id = " and A.id in ($monster_id)";
		}
	}

	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		monster A
	where 
		A.type= '$type'		
		$set_name
		$set_level
		$set_id
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as item_name
		from 
			monster A
			left join item B ON A.award_item_id = B.id
		where 
			A.type= '$type'
			$set_name
			$set_level
			$set_id
		order by 
			$set_order
			A.id asc
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=monster&type=$type&name=$name&level=$level&mission_section_id=$mission_section_id");	
	}	
	include_once template('t_monster');
}


//--------------------------------------------------------------------------------------------世界BOSS数据

function  WorldBoss() 
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$town_id = ReqNum('town_id');
	$town_list = globalDataList('town',"`type` = 1 or `type` = 4 or `type` = 13");//世界BOSS城镇
	if(!$town_id){
		$town_id = $db->result_first("select min(id) from town where `type` = 1 or `type` = 4 or `type` = 13");
	}
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		world_boss
	where 
		town_id= '$town_id'		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			world_boss
		where 
			town_id= '$town_id'
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['level'].'级');
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=monster&action=WorldBoss&town_id=$town_id");	
	}	
	include_once template('t_world_boss');
}




//--------------------------------------------------------------------------------------------批量设置怪物
function  SetMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$talk = ReqArray('talk');
	$level = ReqArray('level');
	$attack = ReqArray('attack');
	$defense = ReqArray('defense');
	$stunt_attack = ReqArray('stunt_attack');
	$stunt_defense = ReqArray('stunt_defense');
	$magic_attack = ReqArray('magic_attack');
	$magic_defense = ReqArray('magic_defense');
	$health = ReqArray('health');
	$award_item_id = ReqArray('award_item_id');
	$award_experience = ReqArray('award_experience');
	$critical = ReqArray('critical');
	$dodge = ReqArray('dodge');
	$hit = ReqArray('hit');
	$block = ReqArray('block');
	$role_stunt_id = ReqArray('role_stunt_id');
	$role_job_id = ReqArray('role_job_id');
	$resource_monster_id = ReqArray('resource_monster_id');
	$break_critical = ReqArray('break_critical');
	$break_block = ReqArray('break_block');
	$kill = ReqArray('kill');

	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$talk_n = ReqStr('talk_n');
	$level_n = ReqStr('level_n');
	$attack_n = ReqNum('attack_n');
	$defense_n = ReqNum('defense_n');
	$stunt_attack_n= ReqNum('stunt_attack_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$health_n = ReqNum('health_n');
	$award_item_id_n = ReqNum('award_item_id_n');
	$award_experience_n = ReqNum('award_experience_n');
	$critical_n = ReqNum('critical_n');
	$dodge_n = ReqNum('dodge_n');
	$hit_n = ReqNum('hit_n');
	$block_n = ReqNum('block_n');
	$role_stunt_id_n = ReqNum('role_stunt_id_n');
	$role_job_id_n = ReqNum('role_job_id_n');
	$resource_monster_id_n = ReqNum('resource_monster_id_n');
	$break_critical_n = ReqNum('break_critical_n');
	$break_block_n = ReqNum('break_block_n');
	$kill_n = ReqNum('kill_n');
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
					monster 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`talk`='$talk[$i]',
					`level`='$level[$i]',
					`attack`='$attack[$i]',
					`defense` = '$defense[$i]',
					`stunt_attack` = '$stunt_attack[$i]',
					`stunt_defense` = '$stunt_defense[$i]',
					`magic_attack` = '$magic_attack[$i]',
					`magic_defense` = '$magic_defense[$i]',
					`health` = '$health[$i]',
					`award_item_id` = '$award_item_id[$i]',
					`award_experience` = '$award_experience[$i]',
					`critical`='$critical[$i]',
					`dodge`='$dodge[$i]',
					`hit`='$hit[$i]',
					`block`='$block[$i]',
					`role_stunt_id`='$role_stunt_id[$i]',
					`role_job_id`='$role_job_id[$i]',
					`resource_monster_id`='$resource_monster_id[$i]',
					`break_critical`='$break_critical[$i]',
					`break_block`='$break_block[$i]',
					`kill`='$kill[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			monster(
			`name`,
			`sign`,
			`talk`,
			`level`,
			`attack`,
			`defense`,
			`stunt_attack`,
			`stunt_defense`,
			`magic_attack`,
			`magic_defense`,
			`health`,
			`award_item_id`,
			`award_experience`,
			`critical`,
			`dodge`,
			`hit`,
			`block`,
			`role_stunt_id`,
			`role_job_id`,
			`resource_monster_id`,
			`break_critical`,
			`break_block`,
			`kill`,
			`type`
			) 
		values 
			(
			'$name_n',
			'$sign_n',
			'$talk_n',
			'$level_n',
			'$attack_n',
			'$defense_n',
			'$stunt_attack_n',
			'$stunt_defense_n',
			'$magic_attack_n',
			'$magic_defense_n',
			'$health_n',
			'$award_item_id_n',
			'$award_experience_n',
			'$critical_n',
			'$dodge_n',
			'$hit_n',
			'$block_n',
			'$role_stunt_id_n',
			'$role_job_id_n',
			'$resource_monster_id_n',
			'$break_critical_n',
			'$break_block_n',
			'$kill_n',
			'$type'
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
		$db->query("delete from monster where id in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置世界BOSS数据
function  SetWorldBoss() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	//$begin_hour = ReqArray('begin_hour');
	//$begin_minute = ReqArray('begin_minute');
	//$end_hour = ReqArray('end_hour');
	//$end_minute = ReqArray('end_minute');
	$position_x = ReqArray('position_x');
	$position_y = ReqArray('position_y');

	$town_id = ReqNum('town_id');
	//$begin_hour_n = ReqNum('begin_hour_n');
	//$begin_minute_n = ReqNum('begin_minute_n');
	//$end_hour_n = ReqNum('end_hour_n');
	//$end_minute_n = ReqNum('end_minute_n');
	$position_x_n = ReqNum('position_x_n');
	$position_y_n = ReqNum('position_y_n');

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
					world_boss 
				set 
					`position_x` = '$position_x[$i]',
					`position_y` = '$position_y[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($town_id && $position_x_n && $position_y_n)
	{
	
		$query = $db->query("
		insert into 
			world_boss (
			`position_x`,
			`position_y`,
			`town_id`
			) 
		values 
			(
			'$position_x_n',
			'$position_y_n',
			'$town_id'
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
		$db->query("delete from world_boss_data where world_boss_id in ($id_arr)");
		$db->query("delete from world_boss where id in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}	
			
	showMsg($msg,'','','greentext');	
}




//--------------------------------------------------------------------------------------------批量设置世界BOSS数据对应怪物团
function  SetWorldBossData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	
	$world_boss_id = ReqNum('world_boss_id');
	$level_n = ReqNum('level_n');
	$monster_team_id_n = ReqNum('monster_team_id_n');

	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	

	//-----------------增加记录-------------------------------------------
	if ($world_boss_id  && $level_n && $monster_team_id_n)
	{
	
		$query = $db->query("
		insert into 
			world_boss_data
			(`world_boss_id`,`level`,`monster_team_id`) 
		values 
			('$world_boss_id','$level_n','$monster_team_id_n')
		") ;	
		if($query)
		{
			$msg = "增加成功！";
		}
		else
		{
			$msg = '<strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from world_boss_data where world_boss_id = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


?>