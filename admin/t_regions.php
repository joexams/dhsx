<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'NineRegionsHiddenLevel': NineRegionsHiddenLevel();break;
	case 'SetNineRegions': SetNineRegions();break;
	case 'SetNineRegionsLevel': SetNineRegionsLevel();break;
	case 'SetNineRegionsHiddenLevel': SetNineRegionsHiddenLevel();break;
	default:  NineRegions();
}

//--------------------------------------------------------------------------------------------九界隐藏关卡表

function  NineRegionsHiddenLevel() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------
	$query = $db->query("
	select 
		A.id,
		A.name as  scene_name
	from 
		mission_scene A
		left join mission B on A.mission_id = B.id
	where
		B.type = 12
	order by 
		A.lock asc
	limit 
		$start_num,$pageNum
	");	
	while($mrs = $db->fetch_array($query))
	{
		$mission_scene_list[] =  $mrs;
	}
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		nine_regions_hidden_level
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			nine_regions_hidden_level
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
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=NineRegions&action=NineRegionsHiddenLevel");	

	}	
	include_once template('t_nine_regions_hidden_level');
}
//--------------------------------------------------------------------------------------------九界信息表

function  NineRegions() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$war_attribute_type_list = globalDataList('war_attribute_type');
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		nine_regions
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			nine_regions
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
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=NineRegions");	

	}	
	include_once template('t_nine_regions');
}

//--------------------------------------------------------------------------------------------批量设置魔王试炼
function  SetNineRegions() 
{
	global $db; 
	global $id,$id_del,$name,$require_level,$bless_name,$bless_effect,$require_stone,$war_attribute_type_id,$three_star,$four_star,$five_star,$six_star; 
	global $id_n,$name_n,$require_level_n,$bless_name_n,$bless_effect_n,$require_stone_n,$war_attribute_type_id_n,$three_star_n,$four_star_n,$five_star_n,$six_star_n; 
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i]  && $name[$i])
			{

				$db->query("
				update 
					nine_regions 
				set 
					`require_level`='$require_level[$i]',
					`name`='$name[$i]',
					`bless_name`='$bless_name[$i]',
					`war_attribute_type_id`='$war_attribute_type_id[$i]',
					`three_star`='$three_star[$i]',
					`four_star`='$four_star[$i]',
					`five_star`='$five_star[$i]',
					`six_star`='$six_star[$i]'
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
			nine_regions
			(`id`,`require_level`,`name`,`bless_name`,`war_attribute_type_id`,`three_star`,`four_star`,`five_star`,`six_star`) 
		values 
			('$id_n','$require_level_n','$name_n','$bless_name_n','$war_attribute_type_id_n','$three_star_n','$four_star_n','$five_star_n','$six_star_n')
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
		$db->query("delete from nine_regions where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}



//--------------------------------------------------------------------------------------------批量设置九界关卡表
function  SetNineRegionsLevel() 
{
	global $db; 
	global $region_id,$id_del,$region_level,$monster_team_id,$award_fame,$award_coin,$talk_content,$award_stone,$stone_item_id, $enhance_weapon; 
	global $region_id_n,$region_level_n,$monster_team_id_n,$award_fame_n,$award_coin_n,$talk_content_n,$award_stone_n,$stone_item_id_n, $enhance_weapon_n; 
	global $url,$winid;

	
	//-----------------更新-------------------------------------------
	if ($region_id)
	{
	
		$id_num = count($region_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($region_id[$i] && $region_level[$i])
			{

				$db->query("
				update 
					nine_regions_level 
				set 
					`monster_team_id`='$monster_team_id[$i]',
					`award_fame`='$award_fame[$i]',
					`award_coin`='$award_coin[$i]',
					`award_stone`='$award_stone[$i]',
					`stone_item_id`='$stone_item_id[$i]',
					`enhance_weapon`='$enhance_weapon[$i]',
					`talk_content`='$talk_content[$i]'
				where 
					region_id = '$region_id[$i]'
				and 
					region_level = '$region_level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($region_id_n && $region_level_n )
	{
	
		$query = $db->query("
		insert into 
			nine_regions_level
			(`region_id`,`region_level`,`monster_team_id`,`award_fame`,`award_coin`,`talk_content`,`award_stone`,`$stone_item_id`,`enhance_weapon`) 
		values 
			('$region_id_n','$region_level_n','$monster_team_id_n','$award_fame_n','$award_coin_n','$talk_content_n','$award_stone_n','$enhance_weapon_n')
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
			$db->query("delete from nine_regions_level where region_id = '$idArr[0]' and region_level = '$idArr[1]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------批量
function  SetNineRegionsHiddenLevel() 
{
	global $db; 
	global $id,$id_del,$name,$region_id,$level_id,$monster_team_id,$award_fame,$award_aura,$tips; 
	global $id_n,$name_n,$region_id_n,$level_id_n,$monster_team_id_n,$award_fame_n,$award_aura_n,$tips_n; 
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i]  && $name[$i])
			{

				$db->query("
				update 
					nine_regions_hidden_level 
				set 
					`region_id`='$region_id[$i]',
					`name`='$name[$i]',
					`level_id`='$level_id[$i]',
					`monster_team_id`='$monster_team_id[$i]',
					`award_fame`='$award_fame[$i]',
					`award_aura`='$award_aura[$i]',
					`tips`='$tips[$i]'
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
			nine_regions_hidden_level
			(`id`,`region_id`,`name`,`level_id`,`monster_team_id`,`award_fame`,`award_aura`,`tips`) 
		values 
			($id_n,'$region_id_n','$name_n','$level_id_n','$monster_team_id_n','$award_fame_n','$award_aura_n','$tips_n')
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
		$db->query("delete from nine_regions_hidden_level where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}


?>