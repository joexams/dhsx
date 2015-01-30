<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{

	case 'Soul': Soul();break;
	case 'SetSoul': SetSoul();break;
	
	case 'SoulAllType': SoulAllType();break;
	case 'SetSoulAllType': SetSoulAllType();break;	
	
	case 'SoulAttribute': SoulAttribute();break;
	case 'SetSoulAttribute': SetSoulAttribute();break;	

	case 'SoulType': SoulType();break;
	case 'SetSoulType': SetSoulType();break;
	
	case 'SoulLocation': SoulLocation();break;
	case 'SetSoulLocation': SetSoulLocation();break;

	case 'SoulQuality': SoulQuality();break;
	case 'SetSoulQuality': SetSoulQuality();break;
	
	case 'TowerLayerSoul': TowerLayerSoul();break;
	case 'SetTowerLayerSoul': SetTowerLayerSoul();break;
	
	case 'SetSoulQualityValue': SetSoulQualityValue();break;	
	

	case 'SetTowerLayer': SetTowerLayer();break;

	case 'TowerInfo': TowerInfo(); break;
	case 'SetTowerInfo': SetTowerInfo(); break;
	
	case 'SoulToNineRegionsInfo': SoulToNineRegionsInfo(); break;
	case 'SetSoulToNineRegionsInfo': SetSoulToNineRegionsInfo(); break; 

	default:  TowerLayer();
	
}


//--------------------------------------------------------------------------------------------爬塔信息

function  TowerInfo() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$soul_list = globalDataList('soul');	
	//$monster_list = globalDataList('monster',"role_job_id > 0",'level desc');//怪物
	
	
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		tower_info
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			tower_info
		order by 
			tower_layer asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=tower&action=TowerInfo");	
	}	
	include_once template('t_tower_info');
}

//--------------------------------------------------------------------------------------------爬塔

function  TowerLayer() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//$monster_team_list = globalDataList('mission_monster_team');	
	//$monster_list = globalDataList('monster',"role_job_id > 0",'level desc');//怪物
	
	
	//------------------------------------------------------------
	
	$query = $db->query("
	select 
		A.id,
		B.name as monster_name
	from 
		mission_monster_team A
		left join monster B on A.monster_id = B.id
		left join mission_scene C on A.mission_scene_id = C.id
		left join mission D on C.mission_id = D.id
	where
		D.type = 3
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
		tower_layer
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			tower_layer
		order by 
			layer asc,
			sequence asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=tower");	
	}	
	include_once template('t_tower_layer');
}

//--------------------------------------------------------------------------------------------灵件

function  Soul() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$soul_all_type_list = globalDataList('soul_all_type');	
	$soul_quality_list = globalDataList('soul_quality');	
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		soul
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			soul
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=tower&action=Soul");	
	}	
	include_once template('t_soul');
}
//--------------------------------------------------------------------------------------------灵件类型

function  SoulAllType() 
{
	global $db; 
	$soul_type_list = globalDataList('soul_type');	
	$soul_location_list = globalDataList('soul_location');
	$query = $db->query("
	select 
		*
	from 
		soul_all_type
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
	include_once template('t_soul_all_type');
}
//--------------------------------------------------------------------------------------------灵件类型

function  SoulType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		soul_type
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
	include_once template('t_soul_type');
}

//--------------------------------------------------------------------------------------------灵件位置类型

function  SoulLocation() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		soul_location
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
	include_once template('t_soul_location');
}



//--------------------------------------------------------------------------------------------灵件品质

function  SoulQuality() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		soul_quality
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
	include_once template('t_soul_quality');
}

//--------------------------------------------------------------------------------------------灵件属性

function  SoulAttribute() 
{
	global $db; 
	$war_attribute_type_list = globalDataList('war_attribute_type');	
	$soul_quality_list = globalDataList('soul_quality');
	$query = $db->query("
	select 
		*
	from 
		soul_attribute
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
	include_once template('t_soul_attribute');
}

//--------------------------------------------------------------------------------------------爬塔

function  TowerLayerSoul() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$soul_quality_list = globalDataList('soul_quality');	
	$soul_location_list = globalDataList('soul_location');
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		tower_layer_soul
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			tower_layer_soul
		order by 
			layer asc,
			sequence asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=tower&action=TowerLayerSoul");	
	}	
	include_once template('t_tower_layer_soul');
}

//--------------------------------------------------------------------------------------------批量设置
function  SetTowerLayer() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$layer = ReqArray('layer');
	$sequence = ReqArray('sequence');
	$monster_team_id = ReqArray('monster_team_id');
	$award_experience = ReqArray('award_experience');
	$award_fame = ReqArray('award_fame');
	$award_coin = ReqArray('award_coin');
	$award_stone = ReqArray('award_stone');
	
	$layer_n = ReqNum('layer_n');
	$sequence_n = ReqNum('sequence_n');
	$monster_team_id_n = ReqNum('monster_team_id_n');
	$award_experience_n = ReqNum('award_experience_n');
	$award_fame_n = ReqNum('award_fame_n');
	$award_coin_n = ReqNum('award_coin_n');
	$award_stone_n = ReqNum('award_stone_n');
	//-----------------更新-------------------------------------------
	if ($layer)
	{
	
		$id_num = count($layer);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($layer[$i] && $sequence[$i])
			{

				$db->query("
				update 
					tower_layer 
				set 
					`monster_team_id`='$monster_team_id[$i]',
					`award_experience`='$award_experience[$i]',
					`award_fame`='$award_fame[$i]',
					`award_coin`='$award_coin[$i]',
					`award_stone`='$award_stone[$i]'
				where 
					layer = '$layer[$i]'
					and sequence = '$sequence[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($layer_n && $sequence_n)
	{
	
		$query = $db->query("
		insert into 
			tower_layer
			(`layer`,`sequence`,`monster_team_id`,`award_experience`,`award_fame`,`award_coin`,`award_stone`) 
		values 
			('$layer_n','$sequence_n','$monster_team_id_n','$award_experience_n','$award_fame_n','$award_coin_n','$award_stone_n')
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
			$db->query("delete from tower_layer where layer = '$idArr[0]' and sequence = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置
function  SetSoulType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	
	$name_n = ReqStr('name_n');
		
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
					soul_type 
				set 
					`name`='$name[$i]'
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
			soul_type
			(`name`) 
		values 
			('$name_n')
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
		$db->query("delete from soul_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置
function  SetSoulLocation() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$describe = ReqArray('describe');
	
	$describe_n = ReqStr('describe_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $describe[$i])
			{

				$db->query("
				update 
					soul_location 
				set 
					`describe`='$describe[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($describe_n)
	{
	
		$query = $db->query("
		insert into 
			soul_location
			(`describe`) 
		values 
			('$describe_n')
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
		$db->query("delete from soul_location where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}



//--------------------------------------------------------------------------------------------批量设置
function  SetSoulQuality() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	
	$name_n = ReqStr('name_n');
		
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
					soul_quality 
				set 
					`name`='$name[$i]'
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
			soul_quality
			(`name`) 
		values 
			('$name_n')
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
		$db->query("delete from soul_quality where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置
function  SetSoulAllType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$soul_type_id = ReqArray('soul_type_id');
	$soul_location_id = ReqArray('soul_location_id');

	$name_n = ReqStr('name_n');
	$soul_type_id_n = ReqStr('soul_type_id_n');
	$soul_location_id_n = ReqStr('soul_location_id_n');
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
					soul_all_type 
				set 
					`name`='$name[$i]',
					`soul_type_id`='$soul_type_id[$i]',
					`soul_location_id`='$soul_location_id[$i]'
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
			soul_all_type
			(`name`,`soul_type_id`,`soul_location_id`) 
		values 
			('$name_n','$soul_type_id_n','$soul_location_id_n')
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
		$db->query("delete from soul_all_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置
function  SetSoulAttribute() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$war_attribute_type_id = ReqArray('war_attribute_type_id');
	$soul_quality_id = ReqArray('soul_quality_id');
	$min = ReqArray('min');
	$max = ReqArray('max');
	$unit = ReqArray('unit');

	$war_attribute_type_id_n = ReqStr('war_attribute_type_id_n');
	$soul_quality_id_n = ReqStr('soul_quality_id_n');
	$min_n = ReqNum('min_n');
	$max_n = ReqNum('max_n');
	$unit_n = ReqNum('unit_n');
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
					soul_attribute 
				set 
					`war_attribute_type_id`='$war_attribute_type_id[$i]',
					`soul_quality_id`='$soul_quality_id[$i]',
					`min`='$min[$i]',
					`max`='$max[$i]',
					`unit`='$unit[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($war_attribute_type_id_n && $soul_quality_id_n)
	{
	
		$query = $db->query("
		insert into 
			soul_attribute
			(`war_attribute_type_id`,`soul_quality_id`,`min`,`max`,`unit`) 
		values 
			('$war_attribute_type_id_n','$soul_quality_id_n','$min_n','$max_n','$unit_n')
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
		$db->query("delete from soul_attribute where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置
function  SetSoul() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$soul_all_type_id = ReqArray('soul_all_type_id');
	$soul_quality_id = ReqArray('soul_quality_id');
	$name = ReqArray('name');
	$content = ReqArray('content');
	$saleprice = ReqArray('saleprice');
	$buyprice = ReqArray('buyprice');
	

	$soul_all_type_id_n = ReqStr('soul_all_type_id_n');
	$soul_quality_id_n = ReqStr('soul_quality_id_n');
	$name_n = ReqStr('name_n');
	$saleprice_n = ReqStr('saleprice_n');
	$saleprice_n = ReqStr('saleprice_n');
	$buyprice_n = ReqStr('buyprice_n');
	
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
					soul 
				set 
					`soul_all_type_id`='$soul_all_type_id[$i]',
					`soul_quality_id`='$soul_quality_id[$i]',
					`name`='$name[$i]',
					`content`='$content[$i]',
					`saleprice`='$saleprice[$i]',
					`buyprice`='$buyprice[$i]'					
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
			soul
			(`soul_all_type_id`,`soul_quality_id`,`name`,`saleprice`,`saleprice`,`buyprice`) 
		values 
			('$soul_all_type_id_n','$soul_quality_id_n','$name_n','$saleprice_n','$saleprice_n','$buyprice_n')
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
		$db->query("delete from soul where id in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量设置
function  SetTowerLayerSoul() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$layer = ReqArray('layer');
	$sequence = ReqArray('sequence');
	$soul_quality_id = ReqArray('soul_quality_id');
	$soul_location_id = ReqArray('soul_location_id');
	$probability = ReqArray('probability');
	$soul_quality_id_o = ReqArray('soul_quality_id_o');
	$soul_location_id_o = ReqArray('soul_location_id_o');
	
	

	$layer_n = ReqNum('layer_n');
	$sequence_n = ReqNum('sequence_n');
	$soul_quality_id_n = ReqNum('soul_quality_id_n');
	$soul_location_id_n = ReqNum('soul_location_id_n');
	$probability_n = ReqStr('probability_n');

	//-----------------更新-------------------------------------------
	if ($layer)
	{
	
		$id_num = count($layer);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($layer[$i] && $soul_quality_id_o[$i] && $soul_location_id_o[$i])
			{

				$db->query("
				update 
					tower_layer_soul 
				set 
					`soul_quality_id`='$soul_quality_id[$i]',
					`soul_location_id`='$soul_location_id[$i]',
					`probability`='$probability[$i]'
				where 
					layer = '$layer[$i]'
					and sequence = '$sequence[$i]'
					and soul_quality_id = '$soul_quality_id_o[$i]'
					and soul_location_id = '$soul_location_id_o[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($layer_n)
	{
	
		$query = $db->query("
		insert into 
			tower_layer_soul
			(`layer`,`sequence`,`soul_quality_id`,`soul_location_id`,`probability`) 
		values 
			('$layer_n','$sequence_n','$soul_quality_id_n','$soul_location_id_n','$probability_n')
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
			$db->query("delete from tower_layer_soul where layer = '$idArr[0]' and sequence = '$idArr[1]' and soul_quality_id = '$idArr[2]' and soul_location_id = '$idArr[3]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置
function  SetSoulQualityValue() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$soul_quality_id = ReqArray('soul_quality_id');
	$unit = ReqArray('unit');
	$colour = ReqArray('colour');
	$probability = ReqArray('probability');
	$min = ReqArray('min');
	$max = ReqArray('max');	
	

	$soul_quality_id_n = ReqNum('soul_quality_id_n');
	$unit_n = ReqStr('unit_n');
	$colour_n = ReqStr('colour_n');
	$probability_n = ReqStr('probability_n');
	$min_n = ReqStr('min_n');
	$max_n = ReqStr('max_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	//-----------------更新-------------------------------------------
	if ($soul_quality_id)
	{
	
		$id_num = count($soul_quality_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($soul_quality_id[$i] && $unit[$i] && $colour[$i])
			{

				$db->query("
				update 
					soul_quality_value 
				set 
					`probability`='$probability[$i]',
					`min`='$min[$i]',
					`max`='$max[$i]'
				where 
					soul_quality_id = '$soul_quality_id[$i]'
					and unit = '$unit[$i]'
					and colour = '$colour[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($soul_quality_id_n && $unit_n && $colour_n)
	{
	
		$query = $db->query("
		insert into 
			soul_quality_value
			(`soul_quality_id`,`unit`,`colour`,`probability`,`min`,`max`) 
		values 
			('$soul_quality_id_n','$unit_n','$colour_n','$probability_n','$min_n','$max_n')
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
			$db->query("delete from soul_quality_value where soul_quality_id = '$idArr[0]' and unit = '$idArr[1]' and colour = '$idArr[2]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}


//--------------------------------------------------------------------------------------------批量设置爬塔信息
function  SetTowerInfo() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$tower_layer = ReqArray('tower_layer');
	$swap_soul_id = ReqArray('swap_soul_id');
	$swap_count = ReqArray('swap_count');
	$day_stone_count = ReqArray('day_stone_count');
	

	$tower_layer_n = ReqNum('tower_layer_n');
	$swap_soul_id_n = ReqNum('swap_soul_id_n');
	$swap_count_n = ReqNum('swap_count_n');
	$day_stone_count_n = ReqStr('day_stone_count_n');

	//-----------------更新-------------------------------------------
	if ($tower_layer)
	{
	
		$id_num = count($tower_layer);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($tower_layer[$i] && $swap_soul_id[$i] && $swap_count[$i] && $day_stone_count[$i])
			{

				$db->query("
				update 
					tower_info 
				set 
					`swap_soul_id`='$swap_soul_id[$i]',
					`swap_count`='$swap_count[$i]',
					`day_stone_count`='$day_stone_count[$i]'
				where 
					tower_layer = '$tower_layer[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($tower_layer_n)
	{
	
		$query = $db->query("
		insert into 
			tower_info
			(`tower_layer`,`swap_soul_id`,`swap_count`,`day_stone_count`) 
		values 
			('$tower_layer_n','$swap_soul_id_n','$swap_count_n','$day_stone_count_n')
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
		$db->query("delete from tower_info where tower_layer in ($id_arr)");
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------灵件对应九空无界信息
function SoulToNineRegionsInfo()
{
	global $db,$page;
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$nine_regions_list = globalDataList('nine_regions');
	//$nine_regions_level_list = globalDataList('nine_regions_level');
	
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select
		count(*)
	from
		soul_to_nine_regions_info
	"),0);
	if($num)
	{
		$query = $db->query("
				select
				*
				from
				soul_to_nine_regions_info
				order by
				nine_regions_id asc
				limit
				$start_num,$pageNum
				");
				while($rs = $db->fetch_array($query))
				{
					$list_array[] =  $rs;
				}
				$list_array_pages = multi($num,$pageNum,$page,"t.php?in=tower");
	}
	include_once template('t_soul_to_nine_regions_info');
	
}
//--------------------------------------------------------------------------------------------批量设置灵件对应九空无界信息
function SetSoulToNineRegionsInfo()
{
	global $db;
	global $id_del,$nine_regions_id,$old_nine_regions_id,$nine_regions_level,$war_attr_1_soul_gold_num,$award_war_attr_type_id_1,$award_war_attr_value_1,$award_war_attr_type_id_2,$award_war_attr_value_2;
	global $nine_regions_id_n,$nine_regions_level_n,$war_attr_1_soul_gold_num_n,$award_war_attr_type_id_1_n,$award_war_attr_value_1_n,$award_war_attr_type_id_2_n,$award_war_attr_value_2_n;
	
	//-----------------更新-------------------------------------------
	if ($nine_regions_id && $old_nine_regions_id)
	{
	
		$id_num = count($nine_regions_id);//echo $id_num;
	
		for ($i=0;$i<=$id_num;$i++)
		{
		if ($nine_regions_id[$i])
		{
			
		$db->query("
				update
				soul_to_nine_regions_info
				set
				`nine_regions_id`='$nine_regions_id[$i]',
				`nine_regions_level`='$nine_regions_level[$i]',
				`war_attr_1_soul_gold_num`='$war_attr_1_soul_gold_num[$i]',
				`award_war_attr_type_id_1`='$award_war_attr_type_id_1[$i]',
				`award_war_attr_value_1`='$award_war_attr_value_1[$i]',
				`award_war_attr_type_id_2`='$award_war_attr_type_id_2[$i]',
				`award_war_attr_value_2`='$award_war_attr_value_2[$i]'
				where
				nine_regions_id = '$old_nine_regions_id[$i]'
				");
		}
		}
				$msg = "更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($nine_regions_id_n && $nine_regions_level_n)
	{
		$query = $db->query("
				insert into
				soul_to_nine_regions_info
				(`nine_regions_id`,`nine_regions_level`,`war_attr_1_soul_gold_num`,`award_war_attr_type_id_1`,`award_war_attr_value_1`,`award_war_attr_type_id_2`,`award_war_attr_value_2`)
				values
				('$nine_regions_id_n','$nine_regions_level_n','$war_attr_1_soul_gold_num_n','$award_war_attr_type_id_1_n','$award_war_attr_value_1_n','$award_war_attr_type_id_2_n','$award_war_attr_value_2_n')
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
			$db->query("delete from soul_to_nine_regions_info where nine_regions_id in ($id_arr)");
			$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');

}
?>