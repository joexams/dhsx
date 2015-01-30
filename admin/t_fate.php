<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'FateQuality': FateQuality();break;

	case 'FateNpc': FateNpc();break;

	case 'SetFate': SetFate();break;
	case 'SetFateQuality': SetFateQuality();break;
	case 'SetFateQualityLevel': SetFateQualityLevel();break;
	case 'SetFateNpc': SetFateNpc();break;
	case 'SetFateLevel': SetFateLevel();break;

	default:  Fate();
}

//--------------------------------------------------------------------------------------------命格

function  Fate() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$war_attribute_type_list = globalDataList('war_attribute_type');//影响战争属性
	$fate_quality_list = globalDataList('fate_quality');//品质
	$fate_list = globalDataList('fate');//品质
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		fate
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			fate
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
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=fate");	
	}	
	include_once template('t_fate');
}
//--------------------------------------------------------------------------------------------命格品质

function  FateQuality() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		fate_quality
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			fate_quality
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
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=fate&action=FateQuality");	
	}	
	include_once template('t_fate_quality');
}


//--------------------------------------------------------------------------------------------命格NPC

function  FateNpc() 
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		fate_npc
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			fate_npc
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=fate&action=FateNpc");	
	}	
	include_once template('t_fate_npc');
}
//--------------------------------------------------------------------------------------------批量设置命格
function  SetFate() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$type = ReqArray('type');
	$sign = ReqArray('sign');
	$fate_quality_id = ReqArray('fate_quality_id');
	$war_attribute_type_id = ReqArray('war_attribute_type_id');
	$description = ReqArray('description');
	$request_level = ReqArray('request_level');
	$exchange_require = ReqArray('exchange_require');
	$actived_fate_id = ReqArray('actived_fate_id');
	$war_attribute_type_id2 = ReqArray('war_attribute_type_id2');
	$actived_fate_id2 = ReqArray('actived_fate_id2');
	$need_actived = ReqArray('need_actived');
	$wear_level = ReqArray('wear_level');


	$name_n = ReqStr('name_n');
	$type_n = ReqStr('type_n');
	$sign_n = ReqStr('sign_n');
	$fate_quality_id_n = ReqNum('fate_quality_id_n');
	$war_attribute_type_id_n = ReqNum('war_attribute_type_id_n');
	$description_n = ReqStr('description_n');
	$request_level_n = ReqNum('request_level_n');
	$exchange_require_n = ReqNum('exchange_require_n');
	$actived_fate_id_n = ReqNum('actived_fate_id_n');
	$war_attribute_type_id2_n = ReqNum('war_attribute_type_id2_n');
	$actived_fate_id2_n = ReqNum('actived_fate_id2_n');
	$need_actived_n = ReqNum('need_actived_n');
	$wear_level_n = ReqNum('wear_level_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($name[$i] && $sign[$i] && $description[$i])
			{
				$db->query("
				update 
					fate 
				set 
					`name`='$name[$i]',
					`type`='$type[$i]',
					`sign`='$sign[$i]',
					`fate_quality_id`='$fate_quality_id[$i]',
					`war_attribute_type_id`='$war_attribute_type_id[$i]',
					`description`='$description[$i]',
					`request_level`='$request_level[$i]',
					`exchange_require`='$exchange_require[$i]',
					`actived_fate_id`='$actived_fate_id[$i]',
					`war_attribute_type_id2`='$war_attribute_type_id2[$i]',
					`actived_fate_id2`='$actived_fate_id2[$i]',
					`need_actived`='$need_actived[$i]',
					`wear_level`='$wear_level[$i]'		
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n && $description_n)
	{
		$query = $db->query("
		insert into 
			fate
			(`name`, `type`,`sign`,`fate_quality_id`,`war_attribute_type_id`,`description`,`request_level`,`exchange_require`,`actived_fate_id`,`war_attribute_type_id2`,`actived_fate_id2`,`need_actived`,`wear_level`) 
		values 
			('$name_n', '$type_n','$sign_n','$fate_quality_id_n','$war_attribute_type_id_n','$description_n','$request_level_n','$exchange_require_n','$actived_fate_id_n','$war_attribute_type_id2_n','$actived_fate_id2_n','$need_actived_n','$wear_level_n')
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
		$db->query("delete from fate where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置命格品质
function  SetFateQuality() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$sale_price = ReqArray('sale_price');
	$experience = ReqArray('experience');

	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$sale_price_n = ReqNum('sale_price_n');
	$experience_n = ReqNum('experience_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($name[$i] && $sign[$i])
			{
				$db->query("
				update 
					fate_quality 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`sale_price`='$sale_price[$i]',
					`experience`='$experience[$i]'
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
			fate_quality
			(`name`,`sign`,`sale_price`,`experience`) 
		values 
			('$name_n','$sign_n','$sale_price_n','$experience_n')
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
		$db->query("delete from fate_quality where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}
//--------------------------------------------------------------------------------------------批量设置命格NPC
function  SetFateNpc() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$fees = ReqArray('fees');
	$zawu_probability = ReqArray('zawu_probability');
	//$general_probability = ReqArray('general_probability');
	$excellent_probability = ReqArray('excellent_probability');
	$well_probability = ReqArray('well_probability');
	$legend_probability = ReqArray('legend_probability');
	$artifact_probability = ReqArray('artifact_probability');
	$scrap_probability = ReqArray('scrap_probability');

	$name_n = ReqStr('name_n');
	$fees_n = ReqNum('fees_n');
	$zawu_probability_n = ReqNum('zawu_probability_n');
	//$general_probability_n = ReqNum('general_probability_n');
	$excellent_probability_n = ReqNum('excellent_probability_n');
	$well_probability_n = ReqNum('well_probability_n');
	$legend_probability_n = ReqNum('legend_probability_n');
	$artifact_probability_n = ReqNum('artifact_probability_n');
	$scrap_probability_n = ReqNum('scrap_probability_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($name[$i] && $fees[$i])
			{
				$db->query("
				update 
					fate_npc 
				set 
					`name`='$name[$i]',
					`fees`='$fees[$i]',
					`zawu_probability`='$zawu_probability[$i]',
					`excellent_probability`='$excellent_probability[$i]',
					`well_probability`='$well_probability[$i]',
					`legend_probability`='$legend_probability[$i]',
					`artifact_probability`='$artifact_probability[$i]',
					`scrap_probability`='$scrap_probability[$i]'				
					
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $fees_n )
	{
		$query = $db->query("
		insert into 
			fate_npc
			(`name`,`fees`,`zawu_probability`,`excellent_probability`,`well_probability`,`legend_probability`,`artifact_probability`,`scrap_probability`) 
		values 
			('$name_n','$fees_n','$zawu_probability_n','$excellent_probability_n','$well_probability_n','$legend_probability_n','$artifact_probability_n','$scrap_probability_n')
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
		$db->query("delete from fate_npc where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置命格等级
function  SetFateLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$level = ReqArray('level');
	$fateid = ReqArray('fateid');
	$addon_value = ReqArray('addon_value');
	$addon_value2 = ReqArray('addon_value2');
	$fate_id = ReqNum('fate_id');
	$level_n = ReqNum('level_n');
	$addon_value_n = ReqNum('addon_value_n');
	$addon_value2_n = ReqNum('addon_value2_n');
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');

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
					fate_level
				set 
					`addon_value`='$addon_value[$i]',
					`addon_value2`='$addon_value2[$i]'
				where 
					level = '$id[$i]'
					and fate_id = '$fateid[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($fate_id && $level_n)
	{
		$query = $db->query("
		insert into 
			fate_level
			(`fate_id`,`level`,`addon_value`,`addon_value2`) 
		values 
			('$fate_id','$level_n','$addon_value_n','$addon_value2_n')
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
			$db->query("delete from fate_level where level = '$idArr[0]' and fate_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}
	$msg = urlencode($msg);	
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);


}
//--------------------------------------------------------------------------------------------批量设置命品质格等级
function  SetFateQualityLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$request_experience = ReqArray('request_experience');
	
	$fate_quality_id = ReqNum('fate_quality_id');
	$level_n = ReqNum('level_n');
	$request_experience_n = ReqNum('request_experience_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	
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
					fate_quality_level 
				set
					`request_experience` = '$request_experience[$i]'
				where 
					level = '$id[$i]'
					and fate_quality_id = '$fate_quality_id'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $fate_quality_id)
	{
	
		$query = $db->query("
		insert into 
			fate_quality_level
			(`fate_quality_id`,`level`,`request_experience`) 
		values 
			('$fate_quality_id','$level_n','$request_experience_n')
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
			$db->query("delete from fate_quality_level where level = '$idArr[0]' and fate_quality_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		

	}	
	$msg = urlencode($msg);	
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

?>