<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'CampSalary': CampSalary();break;
	case 'FactionGodOfferings': FactionGodOfferings();break;
	case 'FactionLevel': FactionLevel();break;
	case 'FactionGodLevel': FactionGodLevel();break;
	case 'FactionJob': FactionJob();break;
	case 'FactionFlags': FactionFlags();break;
	case 'FactionFlagsLimit': FactionFlagsLimit();break;
	case 'FactionQuest': FactionQuest();break;
	case 'FactionGoldenRoomInfo': FactionGoldenRoomInfo(); break;

	case 'SetFactionQuest': SetFactionQuest();break;
	case 'SetCamp': SetCamp();break;
	case 'SetCampSalary': SetCampSalary();break;
	case 'SetFactionLevel': SetFactionLevel();break;
	case 'SetFactionGodLevel': SetFactionGodLevel();break;
	case 'SetFactionJob': SetFactionJob();break;
	case 'SetFactionGodOfferings': SetFactionGodOfferings();break;
	case 'SetFactionFlags': SetFactionFlags();break;
	case 'SetFactionFlagsLimit': SetFactionFlagsLimit();break;
	case 'SetFactionGoldenRoomInfo': SetFactionGoldenRoomInfo(); break;
	default:  Camp();
}


//--------------------------------------------------------------------------------------------帮派金库
function FactionGoldenRoomInfo()
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		faction_golden_room_info
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
	include_once template('t_faction_golden_room_info');
}
//--------------------------------------------------------------------------------------------保存帮派金库
function SetFactionGoldenRoomInfo() {
	global $db;
	global $id_del, $id_old, $level, $upper_limit_money, $levelup_need_cost, $maintain_cost, $lower_limit_money, $levelup_need_exp, $need_day;
	global $level_n, $upper_limit_money_n, $levelup_need_cost_n, $maintain_cost_n, $lower_limit_money_n, $levelup_need_exp_n, $need_day_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from faction_golden_room_info where level in ($id_arr)");
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
					faction_golden_room_info 
				set 
					`level`='$level[$i]',
					`upper_limit_money`='$upper_limit_money[$i]',
					`levelup_need_cost`='$levelup_need_cost[$i]',
					`maintain_cost`='$maintain_cost[$i]',
					`lower_limit_money`='$lower_limit_money[$i]',
					`levelup_need_exp`='$levelup_need_exp[$i]',
					`need_day`='$need_day[$i]'
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
			faction_golden_room_info
			(`level`,`upper_limit_money`,`levelup_need_cost`,`maintain_cost`,`lower_limit_money`,`levelup_need_exp`,`need_day`) 
		values 
			('$level_n','$upper_limit_money_n','$levelup_need_cost_n','$maintain_cost_n','$lower_limit_money_n','$levelup_need_exp_n','$need_day_n')
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


//--------------------------------------------------------------------------------------------帮派任务
function  FactionQuest() 
{
	global $db; 	
	$item_list = globalDataList('item', 'type_id  in (30000)');
	
	$query = $db->query("
	select 
		a.id,b.name as town_name, c.name as npc_name 
	from 
		town_npc a 
	left join 
		town b 
	on a.town_id=b.id 
	left join 
		npc c
	on a.npc_id=c.id
	order by 
		a.id asc
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$npc_list[] =  $rs;
		}
	}

	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		faction_quest
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
	include_once template('t_faction_quest');
}


//--------------------------------------------------------------------------------------------保存帮派任务
function SetFactionQuest() {
	global $db;
	global $id_del, $id_old, $sign, $title, $discribe, $award_faction_con, $need_gold_room_lv, $use_item_id, $init_coin, $need_coin, $type, $npc_id;
	global $sign_n, $title_n, $discribe_n, $award_faction_con_n, $need_gold_room_lv_n, $use_item_id_n, $init_coin_n, $need_coin_n, $type_n, $npc_id_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from faction_quest where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $sign[$i])
			{

				$db->query("
				update 
					faction_quest 
				set 
					`sign`='$sign[$i]',
					`discribe`='$discribe[$i]',
					`award_faction_con`='$award_faction_con[$i]',
					`need_gold_room_lv`='$need_gold_room_lv[$i]',
					`use_item_id`='$use_item_id[$i]',
					`title`='$title[$i]',
					`type`='$type[$i]',
					`init_coin`='$init_coin[$i]',
					`need_coin`='$need_coin[$i]',
					`npc_id`='$npc_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($sign_n)
	{
	
		$query = $db->query("
		insert into 
			faction_quest
			(`sign`,`title`,`discribe`,`award_faction_con`,`need_gold_room_lv`,`use_item_id`,`init_coin`,`need_coin`,`type`,`npc_id`) 
		values 
			('$sign_n','$title_n','$discribe_n','$award_faction_con_n','$need_gold_room_lv_n','$use_item_id_n','$init_coin_n','$need_coin_n','$type_n','$npc_id_n')
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


//--------------------------------------------------------------------------------------------帮派战旗
function  FactionFlags() 
{
	global $db; 	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		faction_flags
	order by 
		flags_level asc
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_faction_flags');
}

function  SetFactionFlags() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$flags_level = ReqArray('flags_level');
	$health = ReqArray('health');
	$speed = ReqArray('speed');
	$block = ReqArray('block');
	$hit = ReqArray('hit');
	$dodge = ReqArray('dodge');
	$critical = ReqArray('critical');
	$momentum = ReqArray('momentum');
	$break_block = ReqArray('break_block');
	$break_critical = ReqArray('break_critical');
	$kill = ReqArray('kill');
	$normal_attack = ReqArray('normal_attack');
	$attack = ReqArray('attack');
	$defense = ReqArray('defense');
	$stunt_attack = ReqArray('stunt_attack');
	$stunt_defense = ReqArray('stunt_defense');
	$magic_attack = ReqArray('magic_attack');
	$magic_defense = ReqArray('magic_defense');
	$require_exp = ReqArray('require_exp');
	$need_faction_lv = ReqArray('need_faction_lv');

	$flags_level_n = ReqNum('flags_level_n');
	$health_n = ReqNum('health_n');
	$speed_n = ReqNum('speed_n');
	$block_n = ReqNum('block_n');
	$hit_n = ReqNum('hit_n');
	$dodge_n = ReqNum('dodge_n');
	$speed_n = ReqNum('speed_n');
	$critical_n = ReqNum('critical_n');
	$momentum_n = ReqNum('momentum_n');
	$break_block_n = ReqNum('break_block_n');
	$break_critical_n = ReqNum('break_critical_n');
	$kill_n = ReqNum('kill_n');
	$normal_attack_n = ReqNum('normal_attack_n');
	$attack_n = ReqNum('attack_n');
	$defense_n = ReqNum('defense_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$require_exp_n = ReqNum('require_exp_n');
	$need_faction_lv_n = ReqNum('need_faction_lv_n');
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
					faction_flags 
				set 
					`flags_level`='$flags_level[$i]',
					`health`='$health[$i]',
					`speed`='$speed[$i]',
					`block`='$block[$i]',
					`hit`='$hit[$i]',
					`dodge`='$dodge[$i]',
					`critical`='$critical[$i]',
					`momentum`='$momentum[$i]',
					`break_block`='$break_block[$i]',
					`kill`='$kill[$i]',
					`normal_attack`='$normal_attack[$i]',
					`attack`='$attack[$i]',
					`defense`='$defense[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`magic_attack`='$magic_attack[$i]',
					`magic_defense`='$magic_defense[$i]',
					`need_faction_lv`='$need_faction_lv[$i]',
					`break_critical`='$break_critical[$i]',
					`require_exp`='$require_exp[$i]'
				where 
					flags_level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($flags_level_n)
	{
	
		$query = $db->query("
		insert into 
			faction_flags
			(`flags_level`,`health`,`speed`,`block`,`hit`,`dodge`,`critical`,`momentum`,`break_block`,`kill`,`normal_attack`,`attack`,`defense`,`stunt_attack`,`stunt_defense`,`magic_attack`,`magic_defense`,`require_exp`,`need_faction_lv`,`break_critical`) 
		values 
			('$flags_level_n','$health_n','$speed_n','$block_n','$hit_n','$dodge_n','$critical_n','$momentum_n','$break_block_n','$kill_n','$normal_attack_n','$attack_n','$defense_n','$stunt_attack_n','$stunt_defense_n','$magic_attack_n','$magic_defense_n','$require_exp_n','$need_faction_lv_n','$break_critical_n')
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
		$db->query("delete from faction_flags where flags_level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}
//--------------------------------------------------------------------------------------------帮派战旗限制
function  FactionFlagsLimit() 
{
	global $db; 	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		faction_flags_limit
	order by 
		faction_level asc
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_faction_flags_limit');
}

function SetFactionFlagsLimit() 
{
	global $db; 
		$id_del = ReqArray('id_del');
		$id = ReqArray('id');
		$faction_level = ReqArray('faction_level');
		$health = ReqArray('health');
		$speed = ReqArray('speed');
		$block = ReqArray('block');
		$hit = ReqArray('hit');
		$dodge = ReqArray('dodge');
		$critical = ReqArray('critical');
		$momentum = ReqArray('momentum');
		$break_block = ReqArray('break_block');
		$break_critical = ReqArray('break_critical');
		$kill = ReqArray('kill');
		$normal_attack = ReqArray('normal_attack');
		$attack = ReqArray('attack');
		$defense = ReqArray('defense');
		$stunt_attack = ReqArray('stunt_attack');
		$stunt_defense = ReqArray('stunt_defense');
		$magic_attack = ReqArray('magic_attack');
		$magic_defense = ReqArray('magic_defense');
		
		$faction_level_n = ReqNum('faction_level_n');
		$health_n = ReqNum('health_n');
		$speed_n = ReqNum('speed_n');
		$block_n = ReqNum('block_n');
		$hit_n = ReqNum('hit_n');
		$dodge_n = ReqNum('dodge_n');
		$speed_n = ReqNum('speed_n');
		$critical_n = ReqNum('critical_n');
		$momentum_n = ReqNum('momentum_n');
		$break_block_n = ReqNum('break_block_n');
		$break_critical_n = ReqNum('break_critical_n');
		$kill_n = ReqNum('kill_n');
		$normal_attack_n = ReqNum('normal_attack_n');
		$attack_n = ReqNum('attack_n');
		$defense_n = ReqNum('defense_n');
		$stunt_attack_n = ReqNum('stunt_attack_n');
		$stunt_defense_n = ReqNum('stunt_defense_n');
		$magic_attack_n = ReqNum('magic_attack_n');
		$magic_defense_n = ReqNum('magic_defense_n');
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
						faction_flags_limit 
					set 
						`faction_level`='$faction_level[$i]',
						`health`='$health[$i]',
						`speed`='$speed[$i]',
						`block`='$block[$i]',
						`hit`='$hit[$i]',
						`dodge`='$dodge[$i]',
						`critical`='$critical[$i]',
						`momentum`='$momentum[$i]',
						`break_block`='$break_block[$i]',
						`kill`='$kill[$i]',
						`normal_attack`='$normal_attack[$i]',
						`attack`='$attack[$i]',
						`defense`='$defense[$i]',
						`stunt_attack`='$stunt_attack[$i]',
						`stunt_defense`='$stunt_defense[$i]',
						`magic_attack`='$magic_attack[$i]',
						`break_critical`='$break_critical[$i]',
						`magic_defense`='$magic_defense[$i]'
					where 
						faction_level = '$id[$i]'
					");
				}
				
			}
			$msg = "更新成功！";
		}
			
		//-----------------增加记录-------------------------------------------
		if ($level_n)
		{
		
			$query = $db->query("
			insert into 
				faction_flags_limit
				(`faction_level`,`health`,`speed`,`block`,`hit`,`dodge`,`critical`,`momentum`,`break_block`,`kill`,`normal_attack`,`attack`,`defense`,`stunt_attack`,`stunt_defense`,`magic_attack`,`magic_defense`,`break_critical`) 
			values 
				('$faction_level_n','$health_n','$speed_n','$block_n','$hit_n','$dodge_n','$critical_n','$momentum_n','$break_block_n','$kill_n','$normal_attack_n','$attack_n','$defense_n','$stunt_attack_n','$stunt_defense_n','$magic_attack_n','$magic_defense_n','$break_critical_n')
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
			$db->query("delete from faction_flags_limit where faction_level in ($id_arr)");
			$msg .= "<br />删除成功！";
			
		}	
		showMsg($msg,'','','greentext');
}


//--------------------------------------------------------------------------------------------阵营表

function  Camp() 
{
	global $db; 	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		camp
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
	include_once template('t_camp');
}

//--------------------------------------------------------------------------------------------门派俸禄

function  CampSalary() 
{
	global $db; 	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		camp_salary
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
	include_once template('t_camp_salary');
}


//--------------------------------------------------------------------------------------------帮派祭神的香

function  FactionGodOfferings() 
{
	global $db; 	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		*
	from 
		faction_god_offerings
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
	include_once template('t_faction_god_offerings');
}

//--------------------------------------------------------------------------------------------帮派等级表

function  FactionLevel() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		faction_level
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
	include_once template('t_faction_level');
}

//--------------------------------------------------------------------------------------------帮派神像等级

function  FactionGodLevel() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		faction_god_level
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
	include_once template('t_faction_god_level');
}
//--------------------------------------------------------------------------------------------帮派职务表

function  FactionJob() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		faction_job
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
	include_once template('t_faction_job');
}

//--------------------------------------------------------------------------------------------批量帮派阵营表
function  SetCamp() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i] )
			{

				$db->query("
				update 
					camp 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			camp
			(`name`,`sign`) 
		values 
			('$name_n','$sign_n')
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
		$db->query("delete from camp where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量门派俸禄
function  SetCampSalary() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$salary = ReqArray('salary');
	$level_n = ReqNum('level_n');
	$salary_n = ReqStr('salary_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $salary[$i] )
			{

				$db->query("
				update 
					camp_salary 
				set 
					`salary`='$salary[$i]'
				where 
					level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $salary_n)
	{
	
		$query = $db->query("
		insert into 
			camp_salary
			(`level`,`salary`) 
		values 
			('$level_n','$salary_n')
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
		$db->query("delete from camp_salary where level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量帮派祭神的香
function  SetFactionGodOfferings() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$ingot = ReqArray('ingot');
	$exp = ReqArray('exp');
	$fame = ReqArray('fame');
	$blessing_count = ReqArray('blessing_count');
	$vip_level = ReqArray('vip_level');
	$skill = ReqArray('skill');	
	
	$name_n = ReqStr('name_n');
	$ingot_n = ReqNum('ingot_n');
	$exp_n = ReqNum('exp_n');
	$fame_n = ReqNum('fame_n');
	$blessing_count_n = ReqNum('blessing_count_n');
	$vip_level_n = ReqNum('vip_level_n');
	$skill_n = ReqNum('skill_n');	
		
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
					faction_god_offerings 
				set 
					`name`='$name[$i]',
					`ingot`='$ingot[$i]',
					`exp`='$exp[$i]',
					`fame`='$fame[$i]',
					`blessing_count`='$blessing_count[$i]',
					`vip_level`='$vip_level[$i]',
					`skill`='$skill[$i]'
					
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
			faction_god_offerings
			(`name`,`ingot`,`exp`,`fame`,`blessing_count`,`vip_level`,`skill`) 
		values 
			('$name_n','$ingot_n','$exp_n','$fame_n','$blessing_count_n','$vip_level_n','$skill_n')
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
		$db->query("delete from faction_god_offerings where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量帮派等级表
function  SetFactionLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$max_member = ReqArray('max_member');
	$require_exp = ReqArray('require_exp');
	
	$level_n = ReqNum('level_n');
	$max_member_n = ReqNum('max_member_n');
	$require_exp_n = ReqNum('require_exp_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $max_member[$i])
			{

				$db->query("
				update 
					faction_level 
				set 
					`max_member`='$max_member[$i]',
					`require_exp`='$require_exp[$i]'
				where 
					level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $max_member_n)
	{
	
		$query = $db->query("
		insert into 
			faction_level
			(`level`,`max_member`,`require_exp`) 
		values 
			('$level_n','$require_exp_n','$require_exp_n')
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
		$db->query("delete from faction_level where id level ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量帮派神像等级表
function  SetFactionGodLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$require_exp = ReqArray('require_exp');
	$mission_coins_add = ReqArray('mission_coins_add');
	
	$level_n = ReqNum('level_n');
	$require_exp_n = ReqNum('require_exp_n');
	$mission_coins_add_n = ReqNum('mission_coins_add_n');
	
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
					faction_god_level 
				set 
					`require_exp`='$require_exp[$i]',
					`mission_coins_add`='$mission_coins_add[$i]'
				where 
					level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n)
	{
	
		$query = $db->query("
		insert into 
			faction_god_level
			(`level`,`require_exp`,`mission_coins_add`) 
		values 
			('$level_n','$require_exp_n','$mission_coins_add_n')
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
		$db->query("delete from faction_god_level where level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量帮派职务表
function  SetFactionJob() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i] )
			{

				$db->query("
				update 
					faction_job 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			faction_job
			(`name`,`sign`) 
		values 
			('$name_n','$sign_n')
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
		$db->query("delete from faction_job where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
?>