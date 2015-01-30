<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'ResearchType': ResearchType();break;
	case 'ResearchDataType': ResearchDataType();break;
	case 'SetResearch': SetResearch();break;
	case 'SetResearchType': SetResearchType();break;
	case 'SetResearchDataType': SetResearchDataType();break;
	case 'SetResearchLevelData': SetResearchLevelData();break;
	default:  Research();
}
//--------------------------------------------------------------------------------------------奇术

function  Research() 
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$name=ReqStr('name');
	$type_id = ReqNum('type_id');
	$research_type_list = globalDataList('research_type');//奇术类型
	$research_data_type_list = globalDataList('research_data_type');//奇术研究类型
	$addition_type_list = globalDataList('addition_type');//数值类型
	$deploy_mode_list = globalDataList('deploy_mode');//阵法
	
	if($type_id)
	{
		$set_type = " and research_type_id = '$type_id'";
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
		research
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
			research
		where
			id <> 0		
			$set_type
			$set_name			
		order by 
			`order` asc,
			id asc
		");

		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=research&type_id=$type_id&name=$name");				
	}	
	include_once template('t_research');
}
//--------------------------------------------------------------------------------------------奇术类型

function  ResearchType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		research_type
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
	include_once template('t_research_type');
}
//--------------------------------------------------------------------------------------------奇术研究类型

function  ResearchDataType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		research_data_type
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
	include_once template('t_research_data_type');
}

//--------------------------------------------------------------------------------------------批量奇术
function  SetResearch() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$order = ReqArray('order');
	$name = ReqArray('name');
	$research_type_id = ReqArray('research_type_id');
	$research_data_type_id = ReqArray('research_data_type_id');
	$content = ReqArray('content');
	$addition_type_id = ReqArray('addition_type_id');
	$player_level = ReqArray('player_level');
	$deploy_mode_id = ReqArray('deploy_mode_id');
	
	$order_n = ReqStr('order_n');
	$name_n = ReqStr('name_n');
	$research_type_id_n = ReqStr('research_type_id_n');
	$research_data_type_id_n = ReqStr('research_data_type_id_n');
	$content_n = ReqStr('content_n');
	$addition_type_id_n = ReqNum('addition_type_id_n');
	$player_level_n = ReqNum('player_level_n');
	$deploy_mode_id_n = ReqNum('deploy_mode_id_n');
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
					research 
				set 
					`order`='$order[$i]',
					`name`='$name[$i]',
					`research_type_id`='$research_type_id[$i]',
					`research_data_type_id`='$research_data_type_id[$i]',
					`content`='$content[$i]',
					`addition_type_id`='$addition_type_id[$i]',
					`player_level`='$player_level[$i]',
					`deploy_mode_id`='$deploy_mode_id[$i]'
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
			research
			(`order`,`name`,`research_type_id`,`research_data_type_id`,`content`,`addition_type_id`,`player_level`,`deploy_mode_id`) 
		values 
			('$order_n','$name_n','$research_type_id_n','$research_data_type_id_n','$content_n','$addition_type_id_n','$player_level_n','$deploy_mode_id_n')
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
		$db->query("delete from research where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量奇术类型
function  SetResearchType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from research_type where id in ($id_arr)");
		$msg = "删除成功！";
		
	}			
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{

				$db->query("
				update 
					research_type 
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
			research_type
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

//--------------------------------------------------------------------------------------------批量奇术研究类型
function  SetResearchDataType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$sign = ReqArray('sign');
	$name = ReqArray('name');
	$sign_n = ReqStr('sign_n');
	$name_n = ReqStr('name_n');
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
					research_data_type 
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
			research_data_type
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
		$db->query("delete from research_data_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量设置奇术等级
function  SetResearchLevelData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$research_id = ReqArray('research_id');
	$level = ReqArray('level');
	$skill = ReqArray('skill');
	$research_value = ReqArray('research_value');
	$cd_time = ReqArray('cd_time');
	$player_level = ReqArray('player_level');
	$add_speed = ReqArray('add_speed');
	$health = ReqArray('health');
	$block = ReqArray('block');
	$attack = ReqArray('attack');
	$dodge = ReqArray('dodge');
	$stunt_attack = ReqArray('stunt_attack');
	$hit = ReqArray('hit');
	$magic_attack = ReqArray('magic_attack');
	$critical = ReqArray('critical');


	$research_id_n = ReqNum('research_id_n');
	$level_n = ReqNum('level_n');
	$skill_n = ReqNum('skill_n');
	$research_value_n = ReqStr('research_value_n');
	$cd_time_n = ReqNum('cd_time_n');
	$player_level_n = ReqNum('player_level_n');
	$add_speed_n = ReqNum('add_speed_n');
	$addition_type_id = ReqNum('addition_type_id');

	$health_n = ReqNum('health_n');
	$block_n = ReqNum('block_n');
	$attack_n = ReqNum('attack_n');
	$dodge_n = ReqNum('dodge_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$hit_n = ReqNum('hit_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$critical_n = ReqNum('critical_n');

	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
//print_r($cd_time);
//exit();
	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$id_num = count($level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($research_id[$i] && $level[$i])
			{
				if ($addition_type_id == 2)  $research_value[$i] = $research_value[$i]*100;

				$db->query("
				update 
					research_level_data 
				set 
					`skill`='$skill[$i]',
					`research_value`='$research_value[$i]',
					`cd_time`='$cd_time[$i]',
					`player_level`='$player_level[$i]',
					`add_speed`='$add_speed[$i]',
					`health`='$health[$i]',
					`block`='$block[$i]',
					`attack`='$attack[$i]',
					`dodge`='$dodge[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`hit`='$hit[$i]',
					`magic_attack`='$magic_attack[$i]',
					`critical`='$critical[$i]'
				where 
					research_id = '$research_id[$i]'
				and 
					level = '$level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($research_id_n && $level_n)
	{
		if ($addition_type_id == 2)  $research_value_n = $research_value_n*100;
		$query = $db->query("
		insert into 
			research_level_data
			(`research_id`,`level`,`skill`,`research_value`,`cd_time`,`player_level`,`add_speed`,`health`,`block`,`attack`,`dodge`,`stunt_attack`,`hit`,`magic_attack`,`critical`) 
		values 
			('$research_id_n','$level_n','$skill_n','$research_value_n','$cd_time_n','$player_level_n','$add_speed_n','$health_n','$block_n','$attack_n','$dodge_n','$stunt_attack_n','$hit_n','$magic_attack_n','$critical_n')
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
			$db->query("delete from research_level_data where research_id = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

?>