<?php

namespace Controllers;
use \Models\TableModel;
use \Models\TableFieldsModel;
use \Models\RemoteModel;
use \Excel\Reader;

class TableController extends Controller
{
	private $tablemodel;
	function beforeroute($f3)
	{
		$this->tablemodel = new TableModel();
	}
	/**
	 * 菜单管理
	 * @return [type] [description]
	 */
	function view()
	{
		$list = $this->tablemodel->getTableList();
		$tree = \Tree::instance();
		$tree->nbsp = '';
		$str  = "<tr data-tt-id='\$id' \$parentid_node>
					<td>\$spacer \$name</td>
					<td>\$tablename</td>
					<td>\$querystring</td>
					<td><a href='javascript:Ga.table.create(\$id, &#39;\$name&#39;);'>添加子菜单</a> | <a href='javascript:Ga.table.modify(\$id, &#39;\$name&#39;);'>修改</a> | <a href='javascript:Ga.table.remove(\$id, &#39;\$name&#39;, \$level);'>删除</a></td>
				</tr>";
		$tree->init($list);
		$tableTree = $tree->get_tree(0, $str);

		$navbar = $sidebar = $breadcrumb = array();
		foreach ($list as $key => $value) {
			if ($value['parentid'] === 0) {
				$navbar[$key] = $value;
			}
		}
		$this->base->set('navbar', $navbar);
		$this->base->set('currentid', 0);

		$this->base->set('tableTree', $tableTree);
		echo $this->view->render('table_view.htm');
	}
	/**
	 * 创建
	 * @return [type] [description]
	 */
	function create()
	{
		if ($this->base->get('COOKIE.sent') && $this->base->get('POST.comment')) {

			$table_id = $this->tablemodel->create();

			$this->base->clear('COOKIE.sent');
			if ($table_id) {
				$this->showmessage(1, 'success', '/table/view');
			}

			$this->showmessage(0, 'error');
		}else {
			$this->base->set('COOKIE.sent',TRUE);
			$parentid = $this->base->get('GET.parentid') ?: 0;

			$list = $this->tablemodel->getTableList();
			$tree = \Tree::instance();
			$tree->init($list);
			$tableTree = $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer \$name</option>", $parentid);

			$this->base->set('tableTree', $tableTree);
			$this->base->set('parentid', $parentid);
			echo $this->view->render('table_create.htm');
		}
	}
	/**
	 * 修改
	 * @return [type] [description]
	 */
	function modify()
	{
		if ($this->base->get('COOKIE.sent') && $this->base->get('POST.comment')) {

			$table_id = $this->tablemodel->modify();

			$this->base->clear('COOKIE.sent');
			if ($table_id) {
				$this->showmessage(1, 'success', '/table/view');
			}

			$this->showmessage(0, 'error');
		}else {
			$this->base->set('COOKIE.sent',TRUE);
			$parentid = 0;
			$id = $this->base->get('GET.id') ?: 0;

			$tableInfo = $id > 0 ? $this->tablemodel->load(array('id=?', $id)) : '';
			if ($tableInfo) {
				$parentid = $tableInfo->parentid;
			}

			$table_name = $tableInfo['name'];
			$fields = $fields_saved = array();
			if ($table_name) {
				$fieldsmodel = new TableFieldsModel();
				$fields_saved = $fieldsmodel->getFields($table_name);

				$remotemodel = new RemoteModel();
				$fields = $remotemodel->getFields($table_name);
			}
			$this->base->set('fields', $fields);
			$this->base->set('fields_saved', $fields_saved);

			$list = $this->tablemodel->getTableList();
			$tree = \Tree::instance();
			$tree->init($list);
			$tableTree = $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer \$name</option>", $parentid);

			$this->base->set('tableTree', $tableTree);
			$this->base->set('tableInfo', $tableInfo);
			echo $this->view->render('table_create.htm');
		}
	}
	/**
	 * 删除
	 * @return [type] [description]
	 */
	function delete(){
		$rtn = $this->tablemodel->delete();

		if ($rtn) {
			$this->showmessage(1, 'success', '/table/view');
		}

		$this->showmessage(0, 'error');
	}
	/**
	 * 数据字段配置
	 * @return [type] [description]
	 */
	function fields()
	{
		$fieldsmodel = new TableFieldsModel();
		if ($this->base->get('COOKIE.fsent') && $this->base->get('POST.table_name')) {
			$table_id = $fieldsmodel->setting();
			$this->base->clear('COOKIE.fsent');
			if ($table_id) {
				$this->showmessage(1, 'success');
			}
			$this->showmessage(0, 'error');
		}else {
			$this->base->set('COOKIE.fsent',TRUE);
			$table_name = $this->base->get('GET.table_name');
			if (!$table_name) {
				exit('您输入的数据表名不存在');
			}

			$remotemodel = new RemoteModel();
			$fields = $remotemodel->getFields($table_name);
			if (!$fields) {
				exit('您输入的数据表名不存在');
			}
			$fields_saved = $fieldsmodel->getFields($table_name);
			$tableInfo = $this->tablemodel->findone("name='$table_name'");

			$this->base->set('tableInfo', $tableInfo);
			$this->base->set('fields', $fields);
			$this->base->set('fields_saved', $fields_saved);
			$this->base->set('table_name', $this->base->get('GET.table_name'));
			echo $this->view->render('table_fields.htm');
		}
	}
	/**
	 * 导入模板数据
	 * @return [type] [description]
	 */
	function upload()
	{
		$tablename = $this->base->get('PARAMS.tablename');
		$fieldsmodel = new TableFieldsModel();
		$fields_saved = $fieldsmodel->getFields($tablename);
		if (!$fields_saved) {
			exit('访问的数据表名不存在');
		}

		$remotemodel = new RemoteModel();
		$fields = $remotemodel->getFields($tablename);
		if (!$fields) {
			exit('访问的数据表名不存在');
		}

		$tableInfo = $this->tablemodel->findone("name='$tablename'");
		$required_keys = array();
		foreach ($fields_saved as $key => $value) {
			$fields_arr[$value['field_name']]['field_comment'] = $value['field_comment'];
			$fields_arr[$value['field_name']]['input_type'] = $value['input_type'];
			$fields_arr[$value['field_name']]['default_value'] = $value['default_value'];
			$fields_arr[$value['field_name']]['display'] = $value['display'];

			if ($value['required'] == 1)
				$required_keys[] = $value['field_name'];
		}

		$insert_keys = array();

		foreach ($fields as $key => $field) {
			if (isset($fields_arr[$field['Field']]) && !$fields_arr[$field['Field']]['display']) continue;

			$insert_keys[] = $field['Field'];
			$field_keys[] = stristr($field['Type'], 'int') !== false ? 'int' : $field['Type'];
		}

		$web=\Web::instance();
		$files = $web->receive(null, true);
		if ($files && $insert_keys){
			$excelreader = new Reader();
			$excelreader->setOutputEncoding('UTF-8');

			$file_path_arr = array_keys($files);
			$excelreader->read($file_path_arr[0]);
			error_reporting(E_ALL ^ E_NOTICE);

			$insert_arr = array();
			if ($excelreader->sheets[0]['numRows'] < 2) {
				$this->showMessage(0, '导入的Excel表中无数据');
			}

			for ($i = 2; $i <= $excelreader->sheets[0]['numRows']; $i++) {
				$singe_arr = array();
				$insert_flag = true;
				for ($j = 1; $j <= $excelreader->sheets[0]['numCols']; $j++) {
					$value_excel = $excelreader->sheets[0]['cells'][$i][$j];
					$value_excel = trim($value_excel);
					if (in_array($insert_keys[$j-1], $required_keys) && !$value_excel) {
						$insert_flag = false;
						break;
					}
					$singe_arr[] = $field_keys[$j-1] == 'int' ? intval($value_excel) : $value_excel;
				}
				if (!$insert_flag)	continue;
				$insert_arr[] = "('".implode("','", $singe_arr)."')";
			}
			if ($insert_arr) {
				$insert_sql = "INSERT INTO $tablename(`".implode("`,`", $insert_keys)."`) VALUES ".implode(',', $insert_arr)."";

				$rst = $remotemodel->db->exec($insert_sql);
				if ($rst) {
					$this->handleLog(array('title' => '导入表数据', 'content' => '数据导入成功，表名：'.$tablename));
					$this->showmessage(1, '数据导入成功', '/table/show/'.$tablename);
				}
			}else {
				$this->showMessage(0, '导入的文件无数据');
			}
		}
		$this->showMessage(0, '导入失败');
	}
	/**
	 * 下载Excel模板
	 * @return [type] [description]
	 */
	function download()
	{
		$tablename = $this->base->get('PARAMS.tablename');
		$fieldsmodel = new TableFieldsModel();
		$fields_saved = $fieldsmodel->getFields($tablename);
		if (!$fields_saved) {
			exit('访问的数据表名不存在');
		}

		$remotemodel = new RemoteModel();
		$fields = $remotemodel->getFields($tablename);
		if (!$fields) {
			exit('访问的数据表名不存在');
		}

		$tableInfo = $this->tablemodel->findone("name='$tablename'");

		foreach ($fields_saved as $key => $value) {
			$fields_arr[$value['field_name']]['field_comment'] = $value['field_comment'];
			$fields_arr[$value['field_name']]['input_type'] = $value['input_type'];
			$fields_arr[$value['field_name']]['default_value'] = $value['default_value'];
			$fields_arr[$value['field_name']]['display'] = $value['display'];
		}
		$i = 1;
		foreach ($fields as $key => $field){
			if (isset($fields_arr[$field['Field']]) && !$fields_arr[$field['Field']]['display']) continue;
			if (isset($fields_arr[$field['Field']])) {
			 	$data[$i][] = $fields_arr[$field['Field']]['field_comment'] ?: (strtoupper($field['Comment']?: $field['Field'])) ;
			}else {
				$data[$i][] = strtoupper($field['Comment']?: $field['Field']) ;
			}
		}

		$list = $remotemodel->db->exec("SELECT * FROM $tablename");
		if ($list) {
			foreach ($list as $key => $value) {
				$i++;
				foreach ($value as $k=>$row) {
					if (isset($fields_arr[$k]) && !$fields_arr[$k]['display']) continue;
					$data[$i][] = $row;
				}
			}
		}


		$xls = new \Excel\CreateXML('UTF-8', false, 'Sheet1');
		$xls->addArray($data);
		$xls->generateXML($tablename);

		// header("Content-type: application/vnd.ms-excel");
		// header("Accept-Ranges: bytes");
  //       header("Content-Disposition: attachment; filename={$tablename}.xls");

		// $this->base->set('fields', $fields);
		// $this->base->set('fields_saved', $fields_saved);
		// $this->base->set('fields_arr', $fields_arr);
		// echo $this->view->render('table_excel.htm');
	}
	/**
	 * 数据表显示
	 * @return [type] [description]
	 */
	function show()
	{
		$tablename = $this->base->get('PARAMS.tablename');

		$fieldsmodel = new TableFieldsModel();
		$fields_saved = $fieldsmodel->getFields($tablename);
		if (!$fields_saved) {
			exit('访问的数据表名不存在');
		}

		$remotemodel = new RemoteModel();
		$fields = $remotemodel->getFields($tablename);
		if (!$fields) {
			exit('访问的数据表名不存在');
		}
		$tableInfo = $this->tablemodel->findone("name='$tablename'");

		$fields_arr = array();
		$search_arr = array();
		$search_id = array();
		$filter = $search_col = '';
		foreach ($fields_saved as $key => $value) {
			if ($value['input_type'] == 'select') {
			//下拉框
				if ($value['fixed_value']) {
					$fixed_value = explode(PHP_EOL, $value['fixed_value']);
					foreach ($fixed_value as $fk => $fv) {
						list($id, $name) = explode('|', $fv);
						$fields_arr[$value['field_name']]['select'][$fk] = array('id'=>$id, 'name' => $name);
					}
				}elseif (!empty($value['target_sql'])) {
					$fields_arr[$value['field_name']]['select'] = $remotemodel->db->exec($value['target_sql']);
				}

				if ($value['default_value']) {
					$default_value = explode(PHP_EOL, $value['default_value']);
					foreach ($default_value as $fk => $fv) {
						list($id, $name) = explode('|', $fv);
						$fields_arr[$value['field_name']]['default'][$fk] = array('id'=>$id, 'name' => $name);
					}
				}

				if ($value['search']) {
					$search_arr[$value['field_name']] = $fields_arr[$value['field_name']]['select'];

					$searchid = $this->base->get('GET.'.$value['field_name']);
					$search_id[$value['field_name']] = $searchid;
					if ($searchid) {
						$filter .= ($filter ? ' AND ' : '')."`".$value['field_name']."`='".$searchid."'";
					}
				}
			}else if ($value['input_type'] == 'checkbox') {
			//选择框
				if ($value['fixed_value']) {
					$fixed_value = explode(PHP_EOL, $value['fixed_value']);
					foreach ($fixed_value as $fk => $fv) {
						$fields_arr[$value['field_name']]['checkbox'][$fk] = $fv;
					}
				}
			}elseif ($value['input_type'] == 'input' && $value['search']){
				$search_col = $value['field_name'];
				$searchtext = $this->base->get('GET.'.$value['field_name']);
				if ($searchtext) {
					
					$filter .= ($filter ? ' AND ' : '')."`".$value['field_name']."` like '%".$searchtext."%'";
				}
			}
			$fields_arr[$value['field_name']]['field_comment'] = $value['field_comment'];
			$fields_arr[$value['field_name']]['input_type'] = $value['input_type'];
			$fields_arr[$value['field_name']]['default_value'] = $value['default_value'];
			$fields_arr[$value['field_name']]['display'] = $value['display'];
			$fields_arr[$value['field_name']]['tips'] = $value['tips'];
		}
		$data = $remotemodel->getPageRows($tablename, $this->base->get('GET.page'), $filter, $tableInfo->orderby);
		foreach ($fields_saved as $key => $value){
			if ($value['input_type'] == 'input' && trim($value['target_sql'])){
				$fields_input = $remotemodel->db->exec($value['target_sql']);
				foreach ($fields_input as $fivalue){
					$fields_input_arr[$fivalue['id']] = $fivalue['name'];
				}
				foreach ($data['list'] as $dk => $dv){
					$data['list'][$dk][$value['field_name']] = $fields_input_arr[$dv[$value['field_name']]];
				}
			}
		}
		
		$this->base->set('tablename', $tablename);
		$this->base->set('fields', $fields);
		$this->base->set('fields_saved', $fields_saved);
		$this->base->set('fields_arr', $fields_arr);
		$this->base->set('search_arr', $search_arr);
		$this->base->set('search_id', $search_id);
		$this->base->set('search_col', $search_col);
		$this->base->set('data', $data);
		echo $this->view->render('table_show.htm');
	}
	/**
	 * 保存模板数据
	 * @return [type] [description]
	 */
	function rowsSave()
	{
		$tablename = $this->base->get('POST.tablename');
		$remotemodel = new RemoteModel($tablename);

		//获取当前表字段
		$fields = $remotemodel->getFields($tablename);
		if (!$fields) {
			$this->showMessage(1, '访问的数据表名不存在');
		}
		$field_keys = array();
		foreach ($fields as $key => $field) {
			if (strtolower($field['Extra']) == 'auto_increment' || strtolower($field['Key']) == 'pri') {
				$condition_keys[] = $field['Field'];
			}
			//填写值类型强制转换
			$field_keys[$field['Field']] = stristr($field['Type'], 'int') !== false ? 'int' : $field['Type'];
		}
		//获取当前表配置字段
		$fieldsmodel = new TableFieldsModel();
		$fields_saved = $fieldsmodel->getFields($tablename);
		foreach ($fields_saved as $key => $value) {
			if ($value['required'] == 1)
				$required_keys[] = $value['field_name'];
			if ($value['input_type'] == 'input' && trim($value['target_sql'])){
				$input_sql_keys[] = $value['field_name'];
				$input_sql_keys[$value['field_name']] = $value['connect_table'];
			}
			if ($value['input_type'] == 'calculate'){
				$calculated_value_keys[] = $value['field_name'];
				$input_sql_keys[$value['field_name']] = $value['target_sql'];
			}
		}

		//删除
		$delids = $this->base->get('POST.delids');
		$del_key = '';
		$ids = array();
		if ($delids) {
			foreach ($delids as $key => $row) {
				$delete_sql = "DELETE FROM $tablename WHERE ";
				$del_key = $key;
				foreach ($row as $id) {
					$ids[] = $id;
				}
				if ($ids) {
					$delete_sql .= "`$key` IN ('".implode("','", $ids)."')";
					$remotemodel->db->exec($delete_sql);
				}
			}
			$this->handleLog(array('title' => '删除表数据', 'content' => '删除成功，表名：'.$tablename.'，ID串（'.implode("','", $ids).'）'));
		}

		//修改信息
		$list = $this->base->get('POST.list');
		$change = $this->base->get('POST.change');
		if ($list) {
			foreach ($list as $k=>$rows) {
				$where = '';
				$update_arr = array();
				$update_sql = "UPDATE $tablename SET ";
				$update_flag = true;
				//没修改不需要更新
				if (!isset($change[$k]) || !$change[$k])	continue;

				foreach ($rows as $key => $value) {
					//存在删除时不更新
					if ($delids && $key == $del_key && in_array($value, $delids)) {
						break;
					}
					//必填项为空时不更新
					if (in_array($key, $required_keys) && !$value) {
						$update_flag = false;
						break;
					}
					//主键或自增为条件
					if (in_array($key, $condition_keys)) {
						$where .= ($where ? ' AND ': ''). " `$key`='$value' ";
					}
					if (in_array($key,$input_sql_keys)){
						$value = self::input_sql_value("$value","$input_sql_keys[$key]");
					}
					$value = $field_keys[$key] == 'int' ? intval($value) : $value;					
					$update_arr[] = "`$key`='$value'";
					if (in_array($key, $calculated_value_keys)){
						
					}
				}
				//必填项判断
				if ($update_flag && $where) {
					$update_sql .= implode(',', $update_arr).' WHERE '.$where;
					$remotemodel->db->exec($update_sql);
				}
			}
		}

		//添加信息
		$info = $this->base->get('POST.info');
		$insert_flag = true;
		if ($required_keys) {
			foreach ($info as $key => $value) {
				if (in_array($key,$input_sql_keys)){
					$value = self::input_sql_value("$value","$input_sql_keys[$key]");
					$remotemodel->$key = $value;
				}
				$value = $field_keys[$key] == 'int' ? intval($value) : $value;
				if (in_array($key, $required_keys) && empty($value)) {
					$insert_flag = false;
					break;
				}
			}
		}
		if ($insert_flag) {
			$remotemodel->copyFrom('POST.info');
			if ($input_sql_keys){
				foreach ($info as $key => $value) {
					if (in_array($key,$input_sql_keys)){
						$remotemodel->$key = self::input_sql_value("$value","$input_sql_keys[$key]");
					}
					if (in_array($key,$calculated_value_keys)){
						$remotemodel->$key = self::calculated_value_keys("$value","$input_sql_keys[$key]");
					}
				}
			}
			$rst = $remotemodel->save();
		}
		$this->showmessage(1, '编辑成功', '/table/show/'.$tablename);
	}
	/**
	 * 清空数据库
	 * @return [type] [description]
	 */
	function truncate()
	{
		$tablename = $this->base->get('PARAMS.tablename');
		$remotemodel = new RemoteModel($tablename);

		//获取当前表字段
		$fields = $remotemodel->getFields($tablename);
		if (!$fields) {
			$this->showMessage(0, '访问的数据表名不存在');
		}
		$rst = $remotemodel->db->exec("TRUNCATE TABLE $tablename");

		$this->handleLog(array('title' => '清空表数据', 'content' => '表数据已清空，表名：'.$tablename));

		$this->showmessage(1, '表数据已清空', '/table/show/'.$tablename);
	}
	
	function input_sql_value($text,$table){
		$tableinfo = explode("|",$table);
		$remotemodel = new RemoteModel($tableinfo[0]);
		$result = $remotemodel->db->exec("select $tableinfo[2] from ".$tableinfo[0]." where $tableinfo[1]='$text'");
		if ($result){
			return $result[0][$tableinfo[2]]; 
		}else{
			$remotemodel->db->exec("insert into $tableinfo[0] (`$tableinfo[1]`) values ('$text')");
			return $remotemodel->db->lastInsertId();
		}
	}
	
	function search_menu(){
    	$param = $_GET['term'];
    	$fieldsmodel = new TableFieldsModel();
		$fieldsmodel->table_name = 'table';
		$query = $fieldsmodel->db->exec("select a.`comment` label,a.`name`,b.`parentid` from dt_table a,dt_table b where a.depth=2 and a.parentid=b.id and concat(a.`comment`,a.name) like '%$param%'");
		$list_array=array();
		foreach ($query as $key => $value) {
			$list_array[] = $value;
		}
		echo json_encode($list_array);
	}
}