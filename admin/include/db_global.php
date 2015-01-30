<?php 
if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

//-------------------------------------------建筑类型列表
function globalDataList($data,$where='',$order='',$columns='*')
{
	global $db; 
	if ($where) $setWhere = "where ".$where;
	if ($order) $setOrder = "order by ".$order;
	$query = $db->query("select $columns from $data $setWhere $setOrder");
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

/**
 * 获取物品表信息
 *
 * @param $where 条件
 * @param $order 排序
 * @return 物品数组,name格式为'QXB气血包'
 */
function golbalItemList($where='',$order='',$columns='*'){
	global $db;
	if ($where) $setWhere = "where ".$where;
	if ($order) $setOrder = "order by ".$order;
	$query = $db->query("select $columns from item $setWhere $setOrder");
	if($db->num_rows($query))
	{		
		while($rs = $db->fetch_array($query))
		{
			$sign = $rs['sign'];
			$f_sign = strtoupper(intercept_str('_',$sign,1));
			$rs['name'] = $f_sign.$rs['name'];
			$array[] =  $rs;
		}
	}
	return $array;
}
/**
 * 获取字符串中指定字符后面固定长度的字符串
 *
 * @param 指定字符 $cstr
 * @param 要获取的字符串 $str
 * @param 获取的长度 $length
 * @return 符合条件的字符串
 */
function intercept_str($cstr,$str,$length)
{
	$strarr=explode($cstr,$str);
	foreach ($strarr as $arr){
		$rstr .= substr($arr,0,$length);
	}
	return $rstr;
}
?>