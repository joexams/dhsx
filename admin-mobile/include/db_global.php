<?php 
if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

//-------------------------------------------建筑类型列表
function globalDataList($data,$where='',$order='')
{
	global $db; 
	if ($where) $setWhere = "where ".$where;
	if ($order) $setOrder = "order by ".$order;
	$query = $db->query("select * from $data $setWhere $setOrder");
	if($db->num_rows($query))
	{		
		while($rs = $db->fetch_array($query))
		{
			 $array[] =  $rs;
		}
	}
	return $array;
}

//------------------------------------------获取表字段信息列表-------------------------------
function globalColumnsList($table) 
{
	global $db;
	$query = $db->query("SHOW FULL COLUMNS FROM $table");
	if($db->num_rows($query)) 
	{
		while($rs = $db->fetch_array($query))
		{
			$array[] = $rs;
		}
	}
	return $array;
}

//------------------------------------------获取表信息-------------------------------
function globalTableInfo($table)
{
	global $menu_db;
	$table_info = $menu_db->result_first("select `describe` from ho_sys_menu where table_name = '$table'");
	return $table_info;
}

//------------------------------------------获取表配置字段信息列表-------------------------------
function globalTableColumnsList($table,$all=0) 
{
	global $menu_db;
	$where =  " and column_status=1 ";
	if ($all == 1){
		$where = '';
	}
	$query = $menu_db->query("select * from ho_sys_menu_sub where table_name='$table' $where order by column_sort,id");
	
	if($menu_db->num_rows($query)) 
	{
		while($rs = $menu_db->fetch_array($query))
		{
			$array[] = $rs;
		}
	}
	return $array;
}

//-------------------------------------------中文模版
function globalChineseList()
{
	global $db; 
	$query = $db->query("select id,text from chinese_text");
	if($db->num_rows($query))
	{		
		while($rs = $db->fetch_array($query))
		{
			 $array[$rs['id']] =  $rs['text'];
		}
	}
	return $array;
}
function chinese_text($text=''){
	global $db;
	if ($text == '') return '';
	$query = $db->query("select id from chinese_text where text='$text'");
	if($db->num_rows($query)){
		$result = $db->result($query);
		return $result;
	}else{
		$query = $db->query("insert into chinese_text (`id`,`text`) values ('','$text')");
		return $db->insert_id();
	}
}
?>