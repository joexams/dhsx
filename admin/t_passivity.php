<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'PassivityType': PassivityType();break;
	case 'SetPassivityType': SetPassivityType();break;
	case 'SetPassivity': SetPassivity();break;
	case 'PassivityDataType': PassivityDataType();break;
	case 'SetPassivityDataType': SetPassivityDataType();break;
	default:  Passivity();
}
//--------------------------------------------------------------------------------------------被动技能类型

function  PassivityType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		passivity_stunt
	order by 
		id asc
	");
	$num = $db->num_rows($query);
	if($num)
	{	
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_passivity_type');
}

//--------------------------------------------------------------------------------------------批量被动技能类型
function  SetPassivityType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$name = ReqArray('name');
	$description = ReqArray('description');
	$sign = ReqArray('sign');
	$type = ReqArray('type');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$description_n = ReqStr('description_n');
	$sign_n = ReqStr('sign_n');
	$type_n = ReqStr('type_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from passivity_stunt where id in ($id_arr)");
		$msg = "删除成功！";
		
	}			
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($name[$i])
			{

				$db->query("
				update 
					passivity_stunt 
				set 
					`name`='$name[$i]',
					`description`='$description[$i]',
					`sign`='$sign[$i]',
					`type`='$type[$i]'
				where 
					id = '$id_old[$i]'
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
			passivity_stunt
			(`name`,`description`,`sign`,`type`) 
		values 
			('$name_n','$description_n','$sign_n','$type_n')
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

//--------------------------------------------------------------------------------------------被动技能列表

function  Passivity() 
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$name=ReqStr('name');
	$type_id = ReqNum('type_id');
	$passivity_stunt_list = globalDataList('passivity_stunt');//被动技能类型
	
	if($type_id)
	{
		$set_type = " and passivity_stunt_id = '$type_id'";
	}
		
	if ($name) 
	{
		$set_name = " and name like '%$name%'";	
	}		
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		passivity_stunt_data
	where
		id <> 0		
		$set_type
		$set_name		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			passivity_stunt_data
		where
			id <> 0		
			$set_type
			$set_name		
		order by 
			id asc
		limit
			$start_num,$pageNum	
		");

		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=passivity&type_id=$type_id&name=$name");				
	}	
	include_once template('t_passivity');
}

//--------------------------------------------------------------------------------------------批量被动技能
function  SetPassivity() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$passivity_stunt_id = ReqArray('passivity_stunt_id');
	$name = ReqArray('name');
	$level = ReqArray('level');
	$value = ReqArray('value');
	$value2 = ReqArray('value2');
	$value3 = ReqArray('value3');
	$description = ReqArray('description');
	
	$passivity_stunt_id_n = ReqNum('passivity_stunt_id_n');
	$name_n = ReqStr('name_n');
	$level_n = ReqNum('level_n');
	$value_n = ReqNum('value_n');
	$value2_n = ReqNum('value2_n');
	$value3_n = ReqNum('value3_n');
	$description_n = ReqStr('description_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from passivity_stunt_data where id in ($id_arr)");
		$msg = "删除成功！";
		
	}			
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($passivity_stunt_id[$i] && $name[$i])
			{

				$db->query("
				update 
					passivity_stunt_data 
				set 
					`passivity_stunt_id`='$passivity_stunt_id[$i]',
					`name`='$name[$i]',
					`level`='$level[$i]',
					`value`='$value[$i]',
					`value2`='$value2[$i]',
					`value3`='$value3[$i]',
					`description`='$description[$i]'
				where 
					id = '$id_old[$i]'
				");
			}			
		}
		
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($passivity_stunt_id_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			passivity_stunt_data
			(`passivity_stunt_id`,`name`,`level`,`value`,`value2`,`value3`,`description`) 
		values 
			('$passivity_stunt_id_n','$name_n','$level_n','$value_n','$value2_n','$value3_n','$description_n')
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

//--------------------------------------------------------------------------------------------被动技能属性表

function  PassivityDataType() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$passivity_stunt_data_list = globalDataList('passivity_stunt_data');//被动技能
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		passivity_level_war_attr
	order by 
		type asc,
		level asc	
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			passivity_level_war_attr
		order by 
		type asc,
		level asc 
		limit
			$start_num,$pageNum	
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=passivity&action=PassivityDataType");				
	}
	include_once template('t_passivity_data_type');
}

//--------------------------------------------------------------------------------------------批量被动技能属性表
function  SetPassivityDataType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$type = ReqArray('type');
	$level = ReqArray('level');
	$need_xian_ling = ReqArray('need_xian_ling');
	$need_player_level = ReqArray('need_player_level');
	$need_zhanshen_lv = ReqArray('need_zhanshen_lv');
	$need_jingang_lv = ReqArray('need_jingang_lv');
	$need_xianfa_lv = ReqArray('need_xianfa_lv');
	$role_stunt_id = ReqArray('role_stunt_id');
	$strength = ReqArray('strength');
	$agile = ReqArray('agile');
	$intellect = ReqArray('intellect');
	$health = ReqArray('health');
	$attack = ReqArray('attack');
	$defense = ReqArray('defense');
	$magic_attack = ReqArray('magic_attack');
	$magic_defense = ReqArray('magic_defense');
	$stunt_attack = ReqArray('stunt_attack');
	$stunt_defense = ReqArray('stunt_defense');
	$hit = ReqArray('hit');
	$block = ReqArray('block');
	$dodge = ReqArray('dodge');
	$critical = ReqArray('critical');
	$break_block = ReqArray('break_block');
	$break_critical = ReqArray('break_critical');
	$kill = ReqArray('kill');
	
	
	$type_n = ReqNum('type_n');
	$level_n = ReqNum('level_n');
	$need_xian_ling_n = ReqNum('need_xian_ling_n');
	$need_player_level_n = ReqNum('need_player_level_n');
	$need_zhanshen_lv_n = ReqNum('need_zhanshen_lv_n');
	$need_jingang_lv_n = ReqNum('need_jingang_lv_n');
	$need_xianfa_lv_n = ReqNum('need_xianfa_lv_n');
	$role_stunt_id_n = ReqNum('role_stunt_id_n');
	$strength_n = ReqNum('strength_n');
	$agile_n = ReqNum('agile_n');
	$intellect_n = ReqNum('intellect_n');
	$health_n = ReqNum('health_n');
	$attack_n = ReqNum('attack_n');
	$defense_n = ReqNum('defense_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	$hit_n = ReqStr('hit_n');
	$block_n = ReqStr('block_n');
	$dodge_n = ReqStr('dodge_n');
	$critical_n = ReqStr('critical_n');
	$break_block_n = ReqStr('break_block_n');
	$break_critical_n = ReqStr('break_critical_n');
	$kill_n = ReqStr('kill_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from passivity_level_war_attr where type = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";
	}			
	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$num = count($level);

		for ($i=0;$i<=$num;$i++)	
		{
			if ($type[$i] && $level[$i])
			{

				$db->query("
				update 
					passivity_level_war_attr 
				set 
					`type`='$type[$i]',
					`level`='$level[$i]',
					`need_xian_ling`='$need_xian_ling[$i]',
					`need_player_level`='$need_player_level[$i]',
					`need_zhanshen_lv`='$need_zhanshen_lv[$i]',
					`need_jingang_lv`='$need_jingang_lv[$i]',
					`need_xianfa_lv`='$need_xianfa_lv[$i]',
					`role_stunt_id`='$role_stunt_id[$i]',
					`strength`='$strength[$i]',
					`agile`='$agile[$i]',
					`intellect`='$intellect[$i]',
					`health`='$health[$i]',
					`attack`='$attack[$i]',
					`defense`='$defense[$i]',
					`magic_attack`='$magic_attack[$i]',
					`magic_defense`='$magic_defense[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`hit`='$hit[$i]',
					`block`='$block[$i]',
					`dodge`='$dodge[$i]',
					`critical`='$critical[$i]',
					`break_block`='$break_block[$i]',
					`break_critical`='$break_critical[$i]',
					`kill`='$kill[$i]'
				where 
					type = '$type[$i]' and
					level = '$level[$i]'
				");
			}			
		}
		
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($type_n && $level_n)
	{
	
		$query = $db->query("
		insert into 
			passivity_level_war_attr
			(`type`,
			`level`,
			`need_xian_ling`,
			`need_player_level`,
			`need_zhanshen_lv`,
			`need_jingang_lv`,
			`need_xianfa_lv`,
			`role_stunt_id`,
			`strength`,
			`agile`,
			`intellect`,
			`health`,
			`attack`,
			`defense`,
			`magic_attack`,
			`magic_defense`,
			`stunt_attack`,
			`stunt_defense`,
			`hit`,
			`block`,
			`dodge`,
			`critical`,
			`break_block`,
			`break_critical`,
			`kill`) 
		values 
			('$type_n','$level_n','$need_xian_ling_n','$need_player_level_n','$need_zhanshen_lv_n','$need_jingang_lv_n','$need_xianfa_lv_n','$role_stunt_id_n','$strength_n','$agile_n','$intellect_n','$health_n','$attack_n','$defense_n','$magic_attack_n','$magic_defense_n','$stunt_attack_n','$stunt_defense_n','$hit_n','$block_n','$dodge_n','$critical_n','$break_block_n','$break_critical_n','$kill_n')
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

?>