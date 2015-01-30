<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SetCommon': SetCommon();break;
	default:  Common();
}

//---------------------------------------------------------------------

function  Common()
{
	global $db,$page,$menu_db;
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$table = KillBad('table'); //表名
	
	//获取标题
	$table_info = globalTableInfo($table);
	//系统表字段
	$column_list = globalColumnsList($table);
	foreach ($column_list as $clrs){
		$table_list[] = $clrs['Field'];
	}
	//表配置字段
	$t_Columns_list = globalTableColumnsList($table,1);
	foreach ($t_Columns_list as $tclrs){
		$menu_list[] = $tclrs['column_name'];
	}
	//增加的字段
	$add_column_list = array_diff($table_list,$menu_list);
	if ($add_column_list){
		foreach ($add_column_list as $key=>$add_column){
			foreach ($column_list as $clrs2){
				if ($clrs2['Field'] == $add_column){
					$c_type = $clrs2['Type'];
					$column_desc = $clrs2['Comment'];
					$column_key = $clrs2['Extra'] == 'auto_increment'?$clrs2['Extra']:$clrs2['Key'];
					$query = $menu_db->query("insert into ho_sys_menu_sub (`table_name`,`column_name`,`c_type`,`column_desc`,`column_type`,`column_key`,`column_type_length`) values ('$table','$add_column','$c_type','$column_desc','text','$column_key','5')") ;
				}
			}
		}
	}
	//删除字段
	$del_column_list = array_diff($menu_list,$table_list);
	if ($del_column_list){
		foreach ($del_column_list as $key=>$del_column){
			$menu_db->query("delete from ho_sys_menu_sub where `table_name` = '$table' and column_name = '$del_column'");
		}
	}
	//获取表配置字段信息列表
	$table_Columns_list = globalTableColumnsList($table);
	//字段数
	$columns_num = count($table_Columns_list)+2;
	foreach ($table_Columns_list as $rs){
		//如果类型为select,radio,checkbox
		if ($rs['column_type'] == 'select' or $rs['column_type'] == 'radio' or $rs['column_type'] == 'checkbox'){
			$abx = '';
			//如果存在固定值,则取固定值,否则使用SQL
			if ($rs['type_val']){
				$arr = explode("\n",$rs['type_val']);
				foreach ($arr as $str){
					$array = explode('|',$str);
					$array['id'] = $array[0];
					$array['name'] = $array[1];
					$abx[] = $array;
				}
			}else{
				$type_sql = stripslashes($rs['type_sql']);
				$query = $db->query("$type_sql");
				while($type_rs = $db->fetch_array($query))
				{
					$abx[] = $type_rs;
				}
			}
			$$rs['column_name']=$abx;
			//为顶部搜索,则获取字段以及搜索列表
			if ($rs['column_search'] == 1){
				$column_search = $abx;
				$column_search_key = $rs['column_name'];
			}
			//默认值
			$default = $rs['default_val']?explode('|',$rs['default_val']):'';
		}
		//PRIMARY KEY 更新删除时用到
		if ($rs['column_key'] == 'PRI' or $rs['column_key'] == 'auto_increment'){
			$primary_key .= $rs['column_name'].',';
		}
	}
	$primary_key = $primary_key?substr($primary_key,0,-1):'';
	$primary_arr = explode(",",$primary_key);
	//-------------------------------获取表资料列表
	//存在搜索条件
	if (ReqStr('type'))
	{
		$col_type=ReqStr('type'); //类型
		$where = "where $column_search_key = '$col_type'";
		$page_param = "&type=$col_type";
	}
	//获取总数量
	$num = $db->result($db->query("select count(*) from $table $where "),0);
	if($num)
	{
		$query = $db->query("select * from $table $where limit $start_num,$pageNum");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=common&table=$table$page_param");
	}

	if ($list_array){
		//将主键信息写入到表资料列表中
		foreach ($list_array as $list_arr){
			$primary_val = '';
			foreach ($primary_arr as $each_primary){
				$primary_val .= $list_arr["$each_primary"].',';
			}
			$primary_val = substr($primary_val,0,-1);
			$list_arr["primary_val"] = $primary_val;
			$list[] = $list_arr;
		}
	}
	include_once template('t_common');
}

//----------------------------------------------------批量设置
function  SetCommon()
{
	global $db;
	$table = ReqStr('table');
	$primary_key = ReqStr('primary_key');
	$primary_arr = explode(",",$primary_key);
	$table_Columns_list = globalTableColumnsList($table); //获取配置信息
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$column_str = trim(ReqStr('column_str')); //有更新的列串
	if ($column_str)
	{
		$column_arr = explode('|',substr($column_str,0,-1));
		$column_arr = array_unique($column_arr);
	}

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		foreach ($id_del as $del_id){
			$del_id_arr = explode(",",$del_id);
			$del_id_num = count($del_id_arr);
			$where = '';
			for ($j=0;$j<$del_id_num;$j++){
				$where .= " $primary_arr[$j] = '$del_id_arr[$j]' and ";
			}
			$where = substr($where,0,-4);
			$db->query("delete from $table where $where");
			$msg = "删除成功！";
		}
		//		$id_arr = implode(",",$id_del);
		//		$db->query("delete from $table where id in ($id_arr)");
		//		$msg = "删除成功！";
	}
	//-----------------更新-------------------------------------------
	//存在需要更新的字段
	if ($column_arr)
	{
		$id_num = count($id_old);
		for ($i=0;$i<=$id_num;$i++)
		//循环所有的列
		{
			//需要更新的字段中包含此ID
			if (in_array("$id_old[$i]",$column_arr)){
				$update_field='';
				//循环每一个字段
				foreach ($table_Columns_list as $list)
				{
					$field = $list['column_name']; //列名
					switch ($list['column_type']) //字段类型
					{
						case 'radio':
							$$list['column_name'] = ReqStr($list['column_name'].$id_old[$i]);
							$field_value = $$list['column_name'];
							$update_field .= "`$field` = '$field_value',";//更新字段
							break;
						case 'checkbox':
							$$list['column_name'] = ReqArray($list['column_name'].$id_old[$i]);
							$field_value = $$list['column_name']?implode(",",$$list['column_name']):'';
							$update_field .= "`$field` = '$field_value',";//更新字段
							break;
						default:
							$$list['column_name'] = ReqArray($list['column_name']);
							$field_value = $$list['column_name'];
							$update_field .= "`$field` = '$field_value[$i]',";//更新字段
					}
				}
//							print_r($update_field);die();
				//存在更新的字段则执行SQL
				if ($update_field)
				{
					$upd_id_arr = explode(",",$id_old[$i]);
					$upd_id_num = count($upd_id_arr);
					$where = '';
					for ($m=0;$m<$upd_id_num;$m++){
						$where .= " $primary_arr[$m] = '$upd_id_arr[$m]' and ";
					}
					$where = substr($where,0,-4);
					$update_field = substr($update_field,0,-1);
					$affected = $db->query("update $table set $update_field	where $where");
				}
			}
		}
		if ($affected){
			$msg .= "<br />更新成功！";
		}
	}

	//-----------------增加记录-------------------------------------------
	$add_status = true;
	foreach ($table_Columns_list as $list)
	{
		//获取字段类型
		$type_array = explode('(',$list['c_type']);
		$c_type = $type_array[0];

		//获取变量名
		$field_n = $list['column_name'].'_n';
		switch ($c_type)
		{
			case 'int':
				$$field_n = ReqNum("$field_n");
				break;
			default:
				$$field_n = ReqStr("$field_n");
		}
		if ($list['column_type'] == 'checkbox')
		{
			$$field_n = ReqArray("$field_n")?implode(",",ReqArray("$field_n")):'';
		}
		
		//变量值
		$value_n =$$field_n;

		if ($list['column_key'] != 'auto_increment' and $list['column_key'] and !$value_n){
			$add_status = false;
		}
		//		$add = $add_status?'1':$add;
		$value_n_str .= "'$value_n',"; //新增值
		$column_n = $list['column_name'];
		$column_n_str .= "`$column_n`,";
		//excel
		if ($list['column_type'] == 'text' && $list['column_key'] != 'auto_increment'){
			$column_n_str_excel .= "`$column_n`,";
		}
	}
	$value_n_str = substr($value_n_str,0,-1);
	$column_n_str = substr($column_n_str,0,-1);
	//如果存在新增变量则执行SQL
	if ($add_status)
	{
		$query = $db->query("insert into $table ($column_n_str) values ($value_n_str)") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}
	if ($_FILES["fileField"]["name"]){
		require_once 'include/Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('UTF-8');
		$data->read($_FILES["fileField"]["tmp_name"]);
		error_reporting(E_ALL ^ E_NOTICE);
		$column_n_str_excel = substr($column_n_str_excel,0,-1);
		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
			$value_n_str_excel = '';
			for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
				$value_excel = $data->sheets[0]['cells'][$i][$j];
				$value_n_str_excel .="'$value_excel',";
			}
			$value_n_str_excel = substr($value_n_str_excel,0,-1);
			$query = $db->query("insert into $table ($column_n_str_excel) values ($value_n_str_excel)") ;
			if($query)
			{
				$msg .= "<br />Excel".$data->sheets[0]['cells'][$i][1]."增加成功！";
			}
			else
			{
				$msg .= '<br /><strong class="redtext">'.$data->sheets[0]['cells'][$i][1].'增加失败，可能因为您输入了重复的数据！</strong>';
			}
		}
	}
	showMsg($msg,'','','greentext');
}

?>