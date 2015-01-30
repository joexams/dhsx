<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'DeployGridType': DeployGridType();break;
	case 'SetDeployMode': SetDeployMode();break;
	case 'SetDeployGridType': SetDeployGridType();break;
	case 'SetDeployGrid': SetDeployGrid();break;

	case 'DeployStart': DeployStart();break;
	case 'SetDeployStart': SetDeployStart();break;
	

	case 'RefinedArray': RefinedArray();break;
	case 'SetRefinedArray': SetRefinedArray();break;
	
	default:  DeployMode();
}

//--------------------------------------------------------------------------------------------炼阵
function RefinedArray()
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		refined_array
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			refined_array
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=deploy&action=RefinedArray");	
	}	
	include_once template('t_refined_array');
}



function SetRefinedArray()
{
	global $db; 
	global $id, $id_del, $id_old, $need_player_level, $need_pearl, $level, $ball, $name, $health, $attack, $defense, $stunt_attack, $stunt_defense, $hit, $block, $dodge, $break_block, $critical, $break_critical;
	global $id_n, $need_player_level_n, $need_pearl_n, $level_n, $ball_n, $name_n, $health_n, $attack_n, $defense_n, $stunt_attack_n, $stunt_defense_n, $hit_n, $block_n, $dodge_n, $break_block_n, $critical_n, $break_critical_n;

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from refined_array where id in ($id_arr)");
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $id[$i])
			{

				$db->query("
				update 
					refined_array 
				set 
					`id`='$id[$i]',
					`need_player_level`='$need_player_level[$i]',
					`need_pearl`='$need_pearl[$i]',
					`level`='$level[$i]',
					`ball`='$ball[$i]',
					`name`='$name[$i]',
					`health`='$health[$i]',
					`attack`='$attack[$i]',
					`defense`='$defense[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`hit`='$hit[$i]',
					`block`='$block[$i]',
					`dodge`='$dodge[$i]',
					`break_block`='$break_block[$i]',
					`critical`='$critical[$i]',
					`break_critical`='$break_critical[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n > 0)
	{
	
		$query = $db->query("
		insert into 
			refined_array
			(`id`, `need_player_level`,`need_pearl`,`level`,`ball`,`name`,`health`,`attack`,`defense`,`stunt_attack`,`stunt_defense`,`hit`,`block`,`dodge`,`break_block`,`critical`,`break_critical`) 
		values 
			('$id_n', '$need_player_level_n','$need_pearl_n','$level_n','$ball_n','$name_n','$health_n','$attack_n','$defense_n','$stunt_attack_n','$stunt_defense_n','$hit_n','$block_n','$dodge_n','$break_block_n','$critical_n','$break_critical_n')
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



function SetDeployStart()
{
	global $db; 
	global $id, $id_del, $attribute_id, $stone_amount, $value;
	global $attribute_id_n, $stone_amount_n, $value_n;

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from deploy_start where id in ($id_arr)");
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $attribute_id[$i])
			{

				$db->query("
				update 
					deploy_start 
				set 
					`attribute_id`='$attribute_id[$i]',
					`stone_amount`='$stone_amount[$i]',
					`value`='$value[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($attribute_id_n > 0)
	{
	
		$query = $db->query("
		insert into 
			deploy_start
			(`attribute_id`,`stone_amount`,`value`) 
		values 
			('$attribute_id_n','$stone_amount_n','$value_n')
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

function DeployStart()
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$war_attribute_type_list = globalDataList('war_attribute_type');//影响战争属性

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		deploy_start
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			deploy_start
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=deploy&action=DeployStart");	
	}	
	include_once template('t_deploy_start');
}


//--------------------------------------------------------------------------------------------阵法

function  DeployMode() 
{
	global $db; 
	
	$research_list = globalDataList('research','research_type_id=2');//奇术(阵法类)
	$query = $db->query("
	select 
		*
	from 
		deploy_mode
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
	include_once template('t_deploy_mode');
}
//--------------------------------------------------------------------------------------------阵法站位类型

function  DeployGridType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		deploy_grid_type
	order by 
		id asc
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
	include_once template('t_deploy_grid_type');
}

//--------------------------------------------------------------------------------------------批量设置阵法
function  SetDeployMode() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$research_id = ReqArray('research_id');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$research_id_n = ReqNum('research_id_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from deploy_mode where id in ($id_arr)");
		$db->query("delete from deploy_grid where deploy_mode_id in ($id_arr)");
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
					deploy_mode 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`research_id`='$research_id[$i]'
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
			deploy_mode
			(`id`,`name`,`research_id`) 
		values 
			('$id_n','$name_n','$research_id_n')
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
//--------------------------------------------------------------------------------------------批量设置阵法站位类型
function  SetDeployGridType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$desc = ReqArray('desc');
	$name_n = ReqStr('name_n');
	$desc_n = ReqStr('desc_n');
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
					deploy_grid_type 
				set 
					`name`='$name[$i]',
					`desc`='$desc[$i]'
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
			deploy_grid_type
			(`name`,`desc`) 
		values 
			('$name_n','$desc_n')
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
		$db->query("delete from deploy_grid_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置阵法站位信息
function  SetDeployGrid() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$require_level = ReqArray('require_level');
	$deploy_grid_type_id = ReqArray('deploy_grid_type_id');
	$type = ReqArray('type');
	
	$deploy_mode_id_n = ReqNum('deploy_mode_id_n');
	$require_level_n = ReqNum('require_level_n');
	$deploy_grid_type_id_n = ReqNum('deploy_grid_type_id_n');
	$type_n = ReqNum('type_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $deploy_grid_type_id[$i])
			{
				$tmptype = isset($type[$id[$i]]) && intval($type[$id[$i]]) > 0 ? intval($type[$id[$i]]) :0; 
				$db->query("
				update 
					deploy_grid 
				set 
					`require_level`='$require_level[$i]',
					`deploy_grid_type_id`='$deploy_grid_type_id[$i]',
					`type`='$tmptype'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}

	//-----------------增加记录-------------------------------------------
	if ($deploy_mode_id_n && $deploy_grid_type_id_n)
	{
	
		$query = $db->query("
		insert into 
			deploy_grid
			(`deploy_mode_id`,`require_level`,`deploy_grid_type_id`,`type`) 
		values 
			('$deploy_mode_id_n','$require_level_n','$deploy_grid_type_id_n','$type_n')
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
		$db->query("delete from deploy_grid where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}
?>