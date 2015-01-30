<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'ImmortalArtType': ImmortalArtType();break;
	case 'SetImmortalArtType': SetImmortalArtType();break;
	case 'SetImmortal': SetImmortal();break;
	default:  Immortal();
}
//---------------------------------------------------------------------仙奇术类型

function  ImmortalArtType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		immortal_art_type
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
	//八仙
	$bx_query = $db->query("SELECT b.`name`,a.id FROM `ba_xian_reqiure` a,ba_xian b where a.ba_xian_id = b.id group by a.id");
	while($bx_rs = $db->fetch_array($bx_query))
		{	
			$bx_rs_array[] =  $bx_rs;
		}	
	include_once template('t_immortal_art_type');
}

//----------------------------------------------------批量仙奇术类型
function  SetImmortalArtType() 
{
	global $db;
	$table_Columns_list = globalColumnsList('immortal_art_type');
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	foreach ($table_Columns_list as $list){
		$$list['Field'] = ReqArray($list['Field']);
		$type_array = explode('(',$list['Type']);
		$type = $type_array[0];
		$field_n = $list['Field'].'_n';
		if ($type == 'int'){
			$$field_n = ReqNum("$field_n");
		}
		if ($type == 'varchar'){
			$$field_n = ReqStr("$field_n");
		}
	}
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from immortal_art_type where id in ($id_arr)");
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
					immortal_art_type 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`unlock_ba_xian_reqiure_id`='$unlock_ba_xian_reqiure_id[$i]'
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
			immortal_art_type
			(`name`,`sign`,`unlock_ba_xian_reqiure_id`) 
		values 
			('$name_n','$sign_n','$unlock_ba_xian_reqiure_id_n')
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

//--------------------------------------------------------------------------------------------仙奇术等级列表

function  Immortal() 
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$type_id = ReqNum('type_id');
	$immortal_art_type_list = globalDataList('immortal_art_type');//仙奇术类型
	$bx_query = $db->query("SELECT b.`name`,a.id FROM `ba_xian_reqiure` a,ba_xian b where a.ba_xian_id = b.id group by a.id");
	while($bx_rs = $db->fetch_array($bx_query))
		{	
			$bx_rs_array[] =  $bx_rs;
		}
	if($type_id)
	{
		$set_type = " and immortal_art_id = '$type_id'";
	}		
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		immortal_art_level_data
	where
		id <> 0		
		$set_type
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			immortal_art_level_data
		where
			id <> 0		
			$set_type
		order by 
			id asc
		limit
			$start_num,$pageNum	
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=immortal&type_id=$type_id");				
	}	
	include_once template('t_immortal_art');
}

//---------------------------------------------------------------设置仙奇术等级
function  SetImmortal() 
{
	global $db; 
	$table_Columns_list = globalColumnsList('immortal_art_level_data');
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	foreach ($table_Columns_list as $list){
		$$list['Field'] = ReqArray($list['Field']);
		$type_array = explode('(',$list['Type']);
		$type = $type_array[0];
		$field_n = $list['Field'].'_n';
		if ($type == 'int'){
			$$field_n = ReqNum("$field_n");
		}
		if ($type == 'varchar'){
			$$field_n = ReqStr("$field_n");
		}
	}
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from immortal_art_level_data where id in ($id_arr)");
		$msg = "删除成功！";
		
	}			
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($immortal_art_id[$i])
			{

				$db->query("
				update 
					immortal_art_level_data 
				set 
					`immortal_art_id`='$immortal_art_id[$i]',
					`level`='$level[$i]',
					`skill`='$skill[$i]',
					`coins`='$coins[$i]',
					`ba_xian_ling`='$ba_xian_ling[$i]',
					`value`='$value[$i]',
					`player_level`='$player_level[$i]',
					`ba_xian_reqiure_id`='$ba_xian_reqiure_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}			
		}
		
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($immortal_art_id_n && $level_n)
	{
	
		$query = $db->query("
		insert into 
			immortal_art_level_data
			(`immortal_art_id`,`level`,`skill`,`coins`,`ba_xian_ling`,`value`,`player_level`,`ba_xian_reqiure_id`) 
		values 
			('$immortal_art_id_n','$level_n','$skill_n','$coins_n','$ba_xian_ling_n','$value_n','$player_level_n','$ba_xian_reqiure_id_n')
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