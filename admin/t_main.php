<?php 
if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}


switch (ReqStr('action')){
	case 'CdTimeType': CdTimeType();break;
	case 'WarAttributeType': WarAttributeType();break;
	case 'SetCdTimeType': SetCdTimeType();break;
	case 'SetWarAttributeType': SetWarAttributeType();break;
	case 'AssistantAward': AssistantAward(); break;
	case 'SetAssistantAward': SetAssistantAward();break;

	case 'FishAward' : FishAward(); break;
	case 'SetFishAward' : SetFishAward(); break;

	case 'DayType': DayType();break;
	case 'SetDayType': SetDayType();break;

	default:  CdTimeType();
}
function  Main() {
	global $db; 
	include template('t_main');
}

//--------------------------------------------------------------------------------------------喜从天降奖励

function FishAward() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		fish_flag_award
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			fish_flag_award
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=main&action=FishAward");	

	}	
	include_once template('t_fish_award');
}
//--------------------------------------------------------------------------------------------保存喜从天降奖励
function SetFishAward() {
	global $db; 
	global $id_old,$id_del,$flag_id,$flag_level,$blow_state,$skill,$fame,$state_point,$xian_ling,$pearl,$ling_ye,$coins; 
	global $flag_id_n,$flag_level_n,$blow_state_n,$skill_n,$fame_n,$state_point_n,$xian_ling_n,$pearl_n,$ling_ye_n,$coins_n; 
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
		$id_num = count($id_old);
		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					fish_flag_award 
				set 
					`flag_id`='$flag_id[$i]',
					`flag_level`='$flag_level[$i]',
					`blow_state`='$blow_state[$i]',
					`skill`='$skill[$i]',
					`fame`='$fame[$i]',
					`state_point`='$state_point[$i]',
					`pearl`='$pearl[$i]',
					`xian_ling`='$xian_ling[$i]',
					`ling_ye`='$ling_ye[$i]',
					`coins`='$coins[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($flag_id_n)
	{
	
		$query = $db->query("
		insert into 
			fish_flag_award
			(`flag_id`,`flag_level`,`blow_state`,`skill`,`fame`,`state_point`,`xian_ling`,`pearl`,`ling_ye`,`coins`) 
		values 
			('$flag_id_n','$flag_level_n','$blow_state_n','$skill_n','$fame_n','$state_point_n','$xian_ling_n','$pearl_n','$ling_ye_n','$coins_n')
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
		$db->query("delete from fish_flag_award where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

//--------------------------------------------------------------------------------------------属性

function  WarAttributeType() 
{
	global $db; 		
	$query = $db->query("
	select 
		*
	from 
		war_attribute_type
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
	include_once template('t_war_attribute_type');
}
//--------------------------------------------------------------------------------------------冷却时间类别表
function  CdTimeType() {
	global $db; 
	
	$query = $db->query("
	select 
		*
	from 
		cd_time_type
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
	
	include template('t_cd_time_type');
}


//--------------------------------------------------------------------------------------------小助手奖励

function  AssistantAward() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		assistant_award
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			assistant_award
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=main&action=AssistantAward");	

	}	
	include_once template('t_assistant_award');
}


//--------------------------------------------------------------------------------------------批量设置小助手奖励
function  SetAssistantAward() 
{
	global $db; 
	
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$vip = ReqArray('vip');
	$times = ReqArray('times');
	$name = ReqArray('name');
	$skill = ReqArray('skill');
	$long_yu_ling = ReqArray('long_yu_ling');
	$card_num = ReqArray('card_num');

	$name_n = ReqStr('name_n');
	$vip_n = ReqNum('vip_n');
	$times_n = ReqNum('times_n');
	$skill_n = ReqStr('skill_n');
	$long_yu_ling_n = ReqStr('long_yu_ling_n');
	$card_num_n = ReqNum('card_num_n');

	//-----------------更新------------------------------------------
	if ($id){
		$id_num = count($id);
		if ($id_num > 0){
			foreach ($id as $key => $value) {
					$db->query("
					update 
						assistant_award 
					set 
						`vip`='$vip[$value]',
						`times`='$times[$value]',
						`name`='$name[$value]',
						`skill`='$skill[$value]',
						`long_yu_ling`='$long_yu_ling[$value]',
						`card_num`='$card_num[$value]'
					where 
						id = '$value'
					");
			}
			$msg = "更新成功！";
		}
	}
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			assistant_award
			(`vip`,`times`,`name`,`skill`,`long_yu_ling`,`card_num`) 
		values 
			('$vip_n','$times_n','$name_n','$skill_n','$long_yu_ling_n','$card_num_n')
		") ;	
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from assistant_award where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置冷却时间类别表
function  SetCdTimeType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$cd_type_name = ReqArray('cd_type_name');
	$ingot_time_ratio = ReqArray('ingot_time_ratio');
	
	$cd_type_name_n = ReqStr('cd_type_name_n');
	$ingot_time_ratio_n = ReqNum('ingot_time_ratio_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $cd_type_name[$i])
			{

				$db->query("
				update 
					cd_time_type 
				set 
					`cd_type_name`='$cd_type_name[$i]',
					`ingot_time_ratio`='$ingot_time_ratio[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($cd_type_name_n && $ingot_time_ratio_n)
	{
	
		$query = $db->query("
		insert into 
			cd_time_type
			(`cd_type_name`,`ingot_time_ratio`) 
		values 
			('$cd_type_name_n','$ingot_time_ratio_n')
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
		$db->query("delete from cd_time_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置属性
function  SetWarAttributeType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$sign = ReqArray('sign');
	$name = ReqArray('name');
	
	$sign_n = ReqStr('sign_n');
	$name_n = ReqNum('name_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $sign[$i] && $name[$i])
			{

				$db->query("
				update 
					war_attribute_type 
				set 
					`sign`='$sign[$i]',
					`name`='$name[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($sign_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			war_attribute_type
			(`sign`,`name`) 
		values 
			('$sign_n','$name_n')
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
		$db->query("delete from war_attribute_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
?>