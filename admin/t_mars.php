<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	//关公等级
	case 'MarsLevel': MarsLevel();	break;
	//祭拜关公香
	case 'MarsOfferings': MarsOfferings();	break;
	//十二宫
	case 'ZodiacLevel': ZodiacLevel();	break;
	//猴子数据
	case 'PeachData': PeachData(); break;
	//桃子数据
	case 'MonkeyData': MonkeyData(); break;

	case 'SetMarsLevel': SetMarsLevel(); break;
	case 'SetMarsOfferings': SetMarsOfferings(); break;
	case 'SetZodiacLevel': SetZodiacLevel(); break;
	case 'SetZodiacRequire': SetZodiacRequire(); break;
	case 'SetMonkeyData': SetMonkeyData();break;
	case 'SetPeachData': SetPeachData();break;	
}




//--------------------------------------------------------------------------------------------批量设置关公等级
function  SetMarsLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$level = ReqArray('level');
	$require_exp = ReqArray('require_exp');
	$mission_exp_add = ReqArray('mission_exp_add');

	$level_n = ReqNum('level_n');
	$require_exp_n = ReqNum('require_exp_n');
	$mission_exp_add_n = ReqNum('mission_exp_add_n');
	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$id_num = count($level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($level[$i])
			{

				$db->query("
				update 
					mars_level 
				set 
					`require_exp`='$require_exp[$i]',
					`mission_exp_add`='$mission_exp_add[$i]'
				where 
					`level` = '$level[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($level_n)
	{
	
		$query = $db->query("
		insert into 
			mars_level
			(`level`,`require_exp`,`mission_exp_add`) 
		values 
			('$level_n','$require_exp_n','$mission_exp_add_n')
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
		$db->query("delete from mars_level where `level` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}
//--------------------------------------------------------------------------------------------批量设置祭拜关公香
function  SetMarsOfferings() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$exp = ReqArray('exp');
	$blessing_count = ReqArray('blessing_count');
	$fame = ReqArray('fame');
	$ingot = ReqArray('ingot');
	$skill = ReqArray('skill');
	$vip_level = ReqArray('vip_level');

	$name_n = ReqStr('name_n');
	$exp_n = ReqNum('exp_n');
	$blessing_count_n = ReqNum('blessing_count_n');
	$fame_n = ReqNum('fame_n');
	$ingot_n = ReqNum('ingot_n');
	$skill_n = ReqNum('skill_n');
	$vip_level_n = ReqNum('vip_level_n');
	
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
					mars_offerings 
				set 
					`name`='$name[$i]',
					`exp`='$exp[$i]',
					`blessing_count`='$blessing_count[$i]',
					`fame`='$fame[$i]',
					`ingot`='$ingot[$i]',
					`skill`='$skill[$i]',
					`vip_level`='$vip_level[$i]'
				where 
					`id` = '$id[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			mars_offerings
			(`name`,`exp`,`blessing_count`,`fame`,`ingot`,`skill`,`vip_level`) 
		values 
			('$name_n','$exp_n','$blessing_count_n','$fame_n','$ingot_n','$skill_n','$vip_level_n')
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
		$db->query("delete from mars_offerings where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------关公等级

function  MarsLevel() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mars_level
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			mars_level
		order by 
			level asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mars&action=MarsLevel");	
	}	
	include_once template('t_mars_level');
}


//--------------------------------------------------------------------------------------------祭拜关公香

function  MarsOfferings() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mars_offerings
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			mars_offerings
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mars&action=MarsOfferings");	
	}	
	include_once template('t_mars_offerings');
}


//-----------------------------------------------------------------------------十二宫
//--------------------------------------------------------------------------------------------十二宫等级

function  ZodiacLevel() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$gold_oil_list = globalDataList('gold_oil');	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		zodiac_level 
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.name as gold_name
		from 
			zodiac_level A
			left join gold_oil B on A.gold_oil_id = B.item_id
		order by 
			A.level asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mars&action=ZodiacLevel");	
	}	
	include_once template('t_zodiac_level');
}


//--------------------------------------------------------------------------------------------批量设置十二宫等级
function  SetZodiacLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$level = ReqArray('level');
	$name = ReqArray('name');
	$gold_oil_id = ReqArray('gold_oil_id');
	$require_level = ReqArray('require_level');

	$level_n = ReqNum('level_n');
	$name_n = ReqStr('name_n');
	$gold_oil_id_n = ReqNum('gold_oil_id_n');
	$require_level_n = ReqNum('require_level_n');

	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$id_num = count($level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($level[$i])
			{

				$db->query("
				update 
					zodiac_level 
				set 
					`name`='$name[$i]',
					`gold_oil_id`='$gold_oil_id[$i]',
					`require_level`='$require_level[$i]'				
					
				where 
					`level` = '$level[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($level_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			zodiac_level
			(`level`,`name`,`gold_oil_id`,`require_level`) 
		values 
			('$level_n','$name_n','$gold_oil_id_n','$require_level_n')
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
		$db->query("delete from zodiac_level where `level` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}



//--------------------------------------------------------------------------------------------批量设置十二宫生肖关卡
function  SetZodiacRequire() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$zodiac_level = ReqArray('zodiac_level');
	$barrier = ReqArray('barrier');
	$monster_team_id = ReqArray('monster_team_id');

	$zodiac_level_n = ReqNum('zodiac_level_n');
	$barrier_n = ReqArray('barrier_n');
	$monster_team_id_n = ReqArray('monster_team_id_n');

	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($zodiac_level)
	{
	
		$id_num = count($zodiac_level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($zodiac_level[$i] && $monster_team_id[$i] && $barrier[$i])
			{

				$db->query("
				update 
					zodiac_require 
				set 
					`monster_team_id`='$monster_team_id[$i]'
				where 
					zodiac_level = '$zodiac_level[$i]'
				and 
					barrier = '$barrier[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($zodiac_level_n && $barrier_n && $monster_team_id_n)
	{
	
		$query = $db->query("
		insert into 
			zodiac_require
			(`monster_team_id`,`zodiac_level`,`barrier`) 
		values 
			('$monster_team_id_n','$zodiac_level_n','$barrier_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from zodiac_require where zodiac_level = '$idArr[0]' and barrier = '$idArr[1]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------桃树数据

function  PeachData() 
{
	global $db,$page; 
	$pageNum = 30;
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
		D.type = 6
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
		peach_data
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			peach_data
		order by 
			peach_lv asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mars&action=PeachData");	
	}	
	include_once template('t_peach_data');
}


//--------------------------------------------------------------------------------------------批量设置桃树数据
function  SetPeachData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$peach_lv = ReqArray('peach_lv');
	$exp = ReqArray('exp');
	$monster_team_id = ReqArray('monster_team_id');

	$peach_lv_n = ReqNum('peach_lv_n');
	$exp_n = ReqNum('exp_n');
	$monster_team_id_n = ReqNum('monster_team_id_n');
	//-----------------更新-------------------------------------------
	if ($peach_lv)
	{
	
		$id_num = count($peach_lv);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($peach_lv[$i])
			{

				$db->query("
				update 
					peach_data 
				set 
					`exp`='$exp[$i]',
					`monster_team_id`='$monster_team_id[$i]'
				where 
					`peach_lv` = '$peach_lv[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($peach_lv_n)
	{
	
		$query = $db->query("
		insert into 
			peach_data
			(`peach_lv`,`exp`,`monster_team_id`) 
		values 
			('$peach_lv_n','$exp_n','$monster_team_id_n')
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
		$db->query("delete from peach_data where `peach_lv` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------猴子数据

function  MonkeyData() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$monster_list = globalDataList('monster','type=5');//怪物	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		monkey_data
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			monkey_data
		order by 
			level asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mars&action=MonkeyData");	
	}	
	include_once template('t_monkey_data');
}

//--------------------------------------------------------------------------------------------批量设置猴子
function  SetMonkeyData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$level = ReqArray('level');
	$monkey_id = ReqArray('monkey_id');
	
	$level_n = ReqNum('level_n');
	$monkey_id_n = ReqStr('monkey_id_n');

	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$id_num = count($level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($level[$i] && $monkey_id[$i])
			{

				$db->query("
				update 
					monkey_data 
				set 
					`monkey_id`='$monkey_id[$i]'
				where 
					`level` = '$level[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($level_n && $monkey_id_n)
	{
	
		$query = $db->query("
		insert into 
			monkey_data
			(`level`,`monkey_id`) 
		values 
			('$level_n','$monkey_id_n')
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
		$db->query("delete from monkey_data where `level` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}
