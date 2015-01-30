<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'BaXianReqiure': BaXianReqiure();break;
	case 'SetBaXianReqiure': SetBaXianReqiure();break;
	case 'SetBaXian': SetBaXian();break;
	default:  BaXian();
}

//---------------------------------------------------------------------八仙类型

function  BaXian()
{
	global $db;
	$table = 'ba_xian';
	$table_Columns_list = globalColumnsList($table);
	$table_info = globalTableInfo($table);
	$list_array = globalDataList($table);
	$action = "SetBaXian";
	$mod = KillBad('in');
	include_once template('t_'.$table);
}

//----------------------------------------------------批量八仙类型
function  SetBaXian()
{
	global $db;
	$table = 'ba_xian';
	$table_Columns_list = globalColumnsList($table);
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');



	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from $table where id in ($id_arr)");
		$msg = "删除成功！";
	}
	//-----------------更新-------------------------------------------
	if ($id_old)
	{

		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)
		{
			$update_field='';
			foreach ($table_Columns_list as $list){
				$$list['Field'] = ReqArray($list['Field']);
				$field = $list['Field']; //列名
				$field_value = $$list['Field'];
				$update_field .= "$field = '$field_value[$i]',";//更新字段
			}
			$update_field = substr($update_field,0,-1);
			if ($field_value[$i])
			{

				$db->query("
				update 
					$table 
				set 
					$update_field
				where 
					id = '$id_old[$i]'
				");
			}
		}
		$msg .= "<br />更新成功！";
	}

	//-----------------增加记录-------------------------------------------
	foreach ($table_Columns_list as $list){
		$type_array = explode('(',$list['Type']);
		$type = $type_array[0];
		$field_n = $list['Field'].'_n';
		if ($type == 'int'){
			$$field_n = ReqNum("$field_n");
		}
		if ($type == 'varchar'){
			$$field_n = ReqStr("$field_n");
		}
		$value_n =$$field_n;
		$value_n_str .= "'$value_n',"; //新增值
	}
	$value_n_str = substr($value_n_str,0,-1);
	if ($name_n)
	{

		$query = $db->query("
		insert into 
			$table
		values 
			($value_n_str)
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

//--------------------------------------------------------------------------------------------八仙试练需求

function  BaXianReqiure()
{
	global $db,$page;
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$table_Columns_list = globalColumnsList('ba_xian_reqiure');
	$ba_xian_list = globalDataList('ba_xian');//八仙
	//怪物团
	$mt_query = $db->query("SELECT b.`name`,a.id FROM `mission_monster_team` a,mission_scene b,mission c where a.mission_scene_id = b.id and b.mission_id=c.id and c.type=15 group by a.id");
	while($mt_rs = $db->fetch_array($mt_query))
	{
		$mt_rs_array[] =  $mt_rs;
	}
	//------------------------------------------------------------

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		ba_xian_reqiure
	where
		id <> 0		
	"),0);	
	if($num)
	{
		$query = $db->query("
		select 
			*
		from 
			ba_xian_reqiure
		where
			id <> 0		
		order by 
			id asc
		limit
			$start_num,$pageNum	
		");

		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=baxian");
	}
	include_once template('t_ba_xian_reqiure');
}

//---------------------------------------------------------------设置仙奇术等级
function  SetBaXianReqiure()
{
	global $db;
	$table_Columns_list = globalColumnsList('ba_xian_reqiure');
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	foreach ($table_Columns_list as $list){
		$$list['Field'] = ReqArray($list['Field']);
		$type_array = explode('(',$list['Type']);
		$type = $type_array[0];
		$field_n = $list['Field'].'_n';
		if ($type == 'int' or $type == 'tinyint'){
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
		$db->query("delete from ba_xian_reqiure where id in ($id_arr)");
		$msg = "删除成功！";

	}
	//-----------------更新-------------------------------------------
	if ($id_old)
	{

		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)
		{
			if ($ba_xian_id[$i])
			{

				$db->query("
				update 
					ba_xian_reqiure 
				set 
					`level`='$level[$i]',
					`ba_xian_id`='$ba_xian_id[$i]',
					`player_lv`='$player_lv[$i]',
					`monster_team_id`='$monster_team_id[$i]',
					`award_item`='$award_item[$i]',
					`close_pos1`='$close_pos1[$i]',
					`close_pos2`='$close_pos2[$i]',
					`close_pos3`='$close_pos3[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
		}

		$msg .= "<br />更新成功！";
	}

	//-----------------增加记录-------------------------------------------
	if ($ba_xian_id_n)
	{

		$query = $db->query("
		insert into 
			ba_xian_reqiure
			(`level`,`ba_xian_id`,`player_lv`,`monster_team_id`,`award_item`,`close_pos1`,`close_pos2`,`close_pos3`)
		values 
			('$level_n','$ba_xian_id_n','$player_lv_n','$monster_team_id_n','$award_item_n','$close_pos1_n','$close_pos2_n','$close_pos3_n')
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