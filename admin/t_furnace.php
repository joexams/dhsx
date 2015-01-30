<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'FurnaceTalk': FurnaceTalk();break;
	case 'FurnaceMakeItemProb': FurnaceMakeItemProb();break;
	case 'RoleLevelupInfo': RoleLevelupInfo();break;
	case 'FavorItemRegular': FavorItemRegular();break;

	case 'SetFurnaceTalk': SetFurnaceTalk();break;
	case 'SetFurnaceMakeItemProb': SetFurnaceMakeItemProb();break;
	case 'SetRoleLevelupInfo': SetRoleLevelupInfo();break;
	case 'SetFavorItemRegular': SetFavorItemRegular();break;

}
//--------------------------------------------------------------------------------------------喜好品合成规则

function  FavorItemRegular() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$item_list = globalDataList('item','type_id=10006');//物品
	$item_list_2 = globalDataList('item');//物品
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		favor_item_regular
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as src_item_name,
			C.name as dst_item_name
		from 
			favor_item_regular A
			left join item B on A.src_item_id = B.id
			left join item C on A.dst_item_id = C.id
		order by 
			A.src_item_id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=furnace&action=FavorItemRegular");	

	}	
	include_once template('t_favor_item_regular');
}
//--------------------------------------------------------------------------------------------可升级角色信息

function  RoleLevelupInfo() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$item_list = globalDataList('item');//物品
	$role_list = globalDataList('role');
	//------------------------------------------------------------
	$roletype[0] = '普通';
	$roletype[1] = '紫色';
	$roletype[2] = '光环';
	$roletype[3] = '金色';
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		role_levelup_info
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			role_levelup_info
		order by 
			role_id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=furnace&action=RoleLevelupInfo");	

	}	
	include_once template('t_role_levelup_info');
}
//--------------------------------------------------------------------------------------------百炼壶合成概率

function  FurnaceMakeItemProb() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$item_list = globalDataList('item');//物品
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		furnace_make_item_prob
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			furnace_make_item_prob
		order by 
			item_id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=furnace&action=FurnaceMakeItemProb");	

	}	
	include_once template('t_furnace_make_item_prob');
}
//--------------------------------------------------------------------------------------------百炼壶角色对话内容

function  FurnaceTalk() 
{
	global $db,$page; 
	global $type; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$role_list = globalDataList('role');
	$type = $type ? $type : 0;
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		furnace_talk
	where 
		type = '$type'
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			furnace_talk
		where 
			type = '$type'
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=furnace&action=FurnaceTalk&type=$type");	

	}	
	include_once template('t_furnace_talk');
}



//--------------------------------------------------------------------------------------------批量设置百炼壶角色对话内容
function  SetFurnaceTalk() 
{
	global $db; 
	global $id,$id_del,$role_id,$content,$type; 
	global $role_id_n,$content_n,$type_n; 
	
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
					furnace_talk 
				set 
					`role_id`='$role_id[$i]',
					`content`='$content[$i]',
					`type`='$type[$i]'					
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
			furnace_talk
			(`role_id`,`content`,`type`) 
		values 
			('$role_id_n','$content_n','$type_n')
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
		$db->query("delete from furnace_talk where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置百炼壶合成概率
function  SetFurnaceMakeItemProb() 
{
	global $db; 
	global $id_del,$item_id,$item_id_old,$item_prob,$stone_prob,$scrap_prob; 
	global $item_id_n,$item_prob_n,$stone_prob_n,$scrap_prob_n; 
	
	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i] && $item_id_old[$i])
			{

				$db->query("
				update 
					furnace_make_item_prob 
				set 
					`item_id`='$item_id[$i]',
					`item_prob`='$item_prob[$i]',
					`stone_prob`='$stone_prob[$i]',
					`scrap_prob`='$scrap_prob[$i]'
				where 
					item_id = '$item_id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			furnace_make_item_prob
			(`item_id`,`item_prob`,`stone_prob`,`scrap_prob`) 
		values 
			('$item_id_n','$item_prob_n','$stone_prob_n','$scrap_prob_n')
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
		$db->query("delete from furnace_make_item_prob where `item_id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置可升级角色信息
function  SetRoleLevelupInfo() 
{
	global $db; 
	global $id_del,$role_id,$role_id_old,$favor_item_id,$levelup_role_id,$need_favor_value,$random_min,$random_max,$award_strength,$award_agile,$award_intellect,$award_coin,$star; 
	global $role_id_n,$favor_item_id_n,$levelup_role_id_n,$need_favor_value_n,$random_min_n,$random_max_n,$award_strength_n,$award_agile_n,$award_intellect_n,$award_aura_n,$star_n; 
	
	//-----------------更新-------------------------------------------
	if ($role_id)
	{
	
		$id_num = count($role_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($role_id[$i] && $role_id_old[$i])
			{

				$db->query("
				update 
					role_levelup_info 
				set 
					`role_id`='$role_id[$i]',
					`favor_item_id`='$favor_item_id[$i]',
					`levelup_role_id`='$levelup_role_id[$i]',
					`need_favor_value`='$need_favor_value[$i]',
					`random_min`='$random_min[$i]',
					`random_max`='$random_max[$i]',
					`award_strength`='$award_strength[$i]',
					`award_agile`='$award_agile[$i]',
					`award_intellect`='$award_intellect[$i]',
					`award_aura`='$award_coin[$i]',
					`star`='$star[$i]'				
				where 
					role_id = '$role_id_old[$i]'
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
			role_levelup_info
			(`role_id`,`favor_item_id`,`levelup_role_id`,`need_favor_value`,`random_min`,`random_max`,`award_strength`,`award_agile`,`award_intellect`,`award_aura`,`star`) 
		values 
			('$role_id_n','$favor_item_id_n','$levelup_role_id_n','$need_favor_value_n','$random_min_n','$random_max_n','$award_strength_n','$award_agile_n','$award_intellect_n','$award_aura_n','$star_n')
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
		$db->query("delete from role_levelup_info where `role_id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}



//--------------------------------------------------------------------------------------------批量设置喜好品合成规则
function  SetFavorItemRegular() 
{
	global $db; 
	global $id_del,$src_item_id,$dst_item_id; 
	global $role_id_n,$src_item_id_n,$dst_item_id_n; 
	

		
	//-----------------增加记录-------------------------------------------
	if ($src_item_id_n)
	{
	
		$query = $db->query("
		insert into 
			favor_item_regular
			(`src_item_id`,`dst_item_id`) 
		values 
			('$src_item_id_n','$dst_item_id_n')
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
			$db->query("delete from favor_item_regular where src_item_id = '$idArr[0]' and dst_item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	
	showMsg($msg,'','','greentext');	

}

?>