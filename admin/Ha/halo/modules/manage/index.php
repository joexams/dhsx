<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class index extends admin {
	private $menudb;
	function __construct(){
		parent::__construct();
		$this->menudb = common::load_model('index_model');
		$this->menusub = common::load_model('menu_model');
		$this->table = common::load_model('table_model');
	}
	public function init(){
		$father_list = $this->menudb->select('father_id=0','*','','id desc');
		$name = $_GET['dname'];
		$father_id = $_GET['father_id'];
		$menu_list = $this->menudb->select('level in (1,2)','`id`,`father_id` as parentid,`describe` as name','','level,id');
		array_unshift($menu_list, Array ( 'id' => -1, 'parentid' => 0, 'name' => '根目录' ) );
		$this->tree = common::load_class('tree');
		$this->tree->arr = $menu_list;
		$select_tree = $this->tree->get_tree(0,"<option value=\$id \$selected>\$spacer\$name</option>");
		$menu1_list = $this->menudb->select('','*,father_id as parentid','','id');
		foreach ($menu1_list as $arr){
			$f_desc = $this->menudb->select('id='.$arr["father_id"],'`describe`','','id');
			$f_desc = $f_desc[0]['describe'];
			$arr['f_desc'] = $f_desc;
			if ($arr["status"] == '1'){
				$arr["status_k"] = '启用';
			}
			if ($arr["status"] == '0'){
				$arr["status_k"] = '禁用';
			}
			if ($arr["is_link"] == '1'){
				$arr["is_link_k"] = '是';
			}
			if ($arr["is_link"] == '0'){
				$arr["is_link_k"] = '否';
			}
			$list[] = $arr;
		}
		$this->tree->ret = '';
		$this->tree->arr = $list;
		$this->tree->icon = array('','','');
		$tree = $this->tree->get_tree(0,"<tr data-tt-id='\$id' data-tt-parent-id='\$father_id'>
											<td>\$describe</td>
			<td>\$func_file</td>
			<td>\$table_name</td>
			<td>\$params</td>
			<td>\$url</td>
			<td>\$is_link_k</td>
			<td>\$f_desc</td>
			<td>\$status_k</td>
			<td><a href=javascript:; onclick=show_add('\$id','\$describe')>增加</a>&nbsp;&nbsp;<a href=javascript:; class='m_modify'>修改</a>&nbsp;&nbsp;<a href=javascript:; onclick=del('\$id','\$table_name')>删除</a>
			</td>
			</tr>"
		,0,' ',
		"<tr data-tt-id='\$id'>
		<td>\$describe</td>
		<td>\$func_file</td>
		<td>\$table_name</td>
		<td>\$params</td>
		<td>\$url</td>
		<td>\$is_link_k</td>
		<td>\$f_desc</td>
		<td>\$status_k</td>
		<td><a href=javascript:; onclick=show_add('\$id','\$describe')>增加</a>&nbsp;&nbsp;<a href=javascript:; class='m_modify'>修改</a>&nbsp;&nbsp;<a href=javascript:; onclick=del('\$id','\$table_name')>删除</a>
		</td></tr>",
		"<tr data-tt-id='\$id' data-tt-parent-id='\$father_id'>
											<td>\$describe</td>
			<td>\$func_file</td>
			<td>\$table_name</td>
			<td>\$params</td>
			<td>\$url</td>
			<td>\$is_link_k</td>
			<td>\$f_desc</td>
			<td>\$status_k</td>
			<td><a href=javascript:; onclick=upd_furl('\$id')>默认</a>&nbsp;&nbsp;<a href=javascript:; class='m_modify'>修改</a>&nbsp;&nbsp;<a href=javascript:; onclick=del('\$id','\$table_name')>删除</a>
			</td>
			</tr>");
		include template('manage', 'index');
	}
	/**
     * 添加菜单
     * 
     */ 
	public function add()
	{
		$describe = $_GET['describe'];
		$func_file = $_GET['func_file'];
		$table_name = $_GET['table_name'];
		$params = $_GET['params'];
		$url = $_GET['url'];
		$father_id = $_GET['father_id'];
		$status = $_GET['status'];
		$level = $_GET['level'];
		$is_link = $_GET['is_link'];
		//根目录
		if ($father_id == -1)
		{
			$level = 1;
			$father_id = 0;
		}else{
			$level = $this->menudb->select('id='.$father_id,'`level`','','id');
			$level = $level[0]['level']+1;
		}
		//存在方法文件并且非外链并且非第一二级
		if ($func_file and $is_link==0 and $level>2)
		{
			$url = '?in='.$func_file;
			if ($table_name)
			{
				$url = $url.'&table='.$table_name; //url参数
				$table_column_list = $this->table->get_list("SHOW FULL COLUMNS FROM $table_name");
					foreach ($table_column_list as $rs){
						$column_key = $rs['Extra'] == 'auto_increment'?$rs['Extra']:$rs['Key'];
						$sub_data = array('table_name'=>$table_name,'column_name'=>$rs['Field'],'c_type'=>$rs['Type'],'column_desc'=>$rs['Comment'],'column_type'=>'text','column_key'=>$column_key);
						$sub_insert_id = $this->menusub->insert($sub_data);
					}
			}
			if ($params)
			{
				$url = $url.'&'.$params;
			}
		}
		
		if ($describe){
		$data = array('describe'=>$describe,'url'=>$url,'father_id'=>$father_id,'status'=>$status,'func_file'=>$func_file,'table_name'=>$table_name,'params'=>$params,'level'=>$level,'is_link'=>$is_link);
		$insert_id = $this->menudb->insert($data);
		}
		if ($insert_id)
		{
			echo json_encode(array('status'=>0,'msg'=>'新增记录成功'));
		}
	}
	/**
	 * 删除菜单
	 * 
	 */ 
	public function delete(){
		$id = $_GET['id'];
		$table_name = trim($_GET['table_name']);
		$delete = $this->menudb->delete('id='."$id".'');
		if ($table_name){
			$delete_sub = $this->menusub->delete("table_name='".$table_name."'");
		}
		if ($delete){
			echo json_encode(array('status'=>0,'msg'=>'删除记录成功'));
		}
	}
	/**
     * 修改菜单
     * 
     */ 
	public function edit(){
		$fid = $_GET['fid'];
		$menu_list = $this->menudb->select('level in (1,2)','`id`,`father_id` as parentid,`describe` as name','','level,id');
		array_unshift($menu_list, Array ( 'id' => -1, 'parentid' => 0, 'name' => '根目录' ) );
//		$sid = array_search($fid,$menu_list);
		$this->tree = common::load_class('tree');
		$this->tree->arr = $menu_list;
		$select_tree = $this->tree->get_tree(0,"<option value=\$id \$selected>\$spacer\$name</option>",$fid);
		echo json_encode($select_tree);
	}
	/**
	 * 更新菜单
	 */
	public function update(){
		$id = $_GET['nid'];
		$describe = $_GET['ndesc'];
		$func_file = $_GET['nfunc_file'];
		$table_name = $_GET['ntable_name'];
		$params = $_GET['nparams'];
		$url = $_GET['nurl'];
		$father_id = $_GET['nfather_id'];
		$status = $_GET['nstatus'];
		$is_link = $_GET['nis_link'];
		$modify_id_str = $_GET['modify_id'];
		if ($father_id == -1)
		{
			$father_id = 0;
			$level = 1;
		}else{
			$level = $this->menudb->select('id='.$father_id,'`level`','','id');
			$level = $level[0]['level']+1;
		}
		if ($func_file and $is_link==0)
		{
			$url = '?in='.$func_file;
			if ($table_name)
			{
				$url = $url.'&table='.$table_name; //url参数
			}
			if ($params)
			{
				$url = $url.'&'.$params;
			}
		}
		$query = $this->menudb->update(array('describe'=>$describe,'func_file'=>$func_file,'table_name'=>$table_name,'params'=>$params,'url'=>$url,'father_id'=>$father_id,'status'=>$status,'level'=>$level,'is_link'=>$is_link),'id='.$id.'');
		$modify_id_arr = array_filter(array_unique(explode('|',$modify_id_str)));
		foreach ($modify_id_arr as $modify_id){
			$query = $this->menusub->update(array('column_desc'=>$_GET['column_desc'.$modify_id],'column_type'=>$_GET['column_type'.$modify_id],'column_type_length'=>$_GET['column_type_length'.$modify_id],'default_val'=>$_GET['default_val'.$modify_id],'type_sql'=>$_GET['type_sql'.$modify_id],'type_val'=>$_GET['type_val'.$modify_id],'column_sort'=>$_GET['column_sort'.$modify_id],'column_status'=>$_GET['column_status'.$modify_id],'column_key'=>$_GET['column_key'.$modify_id],'column_search'=>$_GET['column_search'.$modify_id]),'id='.$modify_id.'');
		}
		if ($query){
			echo json_encode(array('status'=>0,'msg'=>'操作成功'));
		}
	}
	
	/**
	 * 检验表的正确性
	 */
	public  function check_table(){
		$table_name = $_GET['table_name'];
		$table_exist = $this->menudb->select('`table_name`="'.$table_name.'"','id','','id');
		if ($table_exist){
			echo json_encode(array('status'=>0,'msg'=>'该表已经存在了'));
		}else{
			if ($this->table->table_exists($table_name)){
				echo json_encode(array('status'=>1,'msg'=>''));
			}else{
				echo json_encode(array('status'=>0,'msg'=>'输入的表有错误'));
			}
		}
	}
	
	public function upd_furl(){
		$id = $_GET['id'];
		$info = $this->menudb->select('id='.$id,'`level`,`url`,`father_id`','','id');
		$level = $info[0]['level'];
		$url = $info[0]['url'];
		$father_id = $info[0]['father_id'];
		if ($level==3){
			$finfo = $this->menudb->select('id='.$father_id,'`father_id`','','id');
			$fid = $finfo[0]['father_id'];
		}
		if ($level==2){
			$fid = $father_id;
		}
		$query = $this->menudb->update(array("url"=>"$url"),'id='.$fid.'');
		if ($query){
			echo json_encode(array('status'=>0,'msg'=>'操作成功'));
		}
	}
	public function menu_modify(){
		$mid = $_GET['mid'];
		//菜单基本信息
		$info = $this->menudb->select('id='.$mid,'*','','id');
		$info = $info[0];
		//父目录下拉框
		$fid = $info['father_id'];
		$menu_list = $this->menudb->select('level in (1,2)','`id`,`father_id` as parentid,`describe` as name','','level,id');
		array_unshift($menu_list, Array ( 'id' => -1, 'parentid' => 0, 'name' => '根目录' ) );
		$this->tree = common::load_class('tree');
		$this->tree->arr = $menu_list;
		$select_tree = $this->tree->get_tree(0,"<option value=\$id \$selected>\$spacer\$name</option>",$fid);
		//通用模版,获取表字段信息
		if ($info["table_name"]){
			$table_name = $info["table_name"];
			//系统表字段
			$table_column_list = $this->table->get_list("SHOW FULL COLUMNS FROM $table_name");
			foreach ($table_column_list as $rs){
				$table_list[] = $rs["Field"];
			}
			//配置表字段
			$menu_column_list = $this->menusub->select('`table_name`="'.$table_name.'"','column_name','','id');
			foreach ($menu_column_list as $value){
				$menu_sub_list[] = $value["column_name"];
			}
			//增加的字段
			$add_column_list = array_diff($table_list,$menu_sub_list);
			if ($add_column_list){
			foreach ($add_column_list as $key=>$add_column){
				foreach ($table_column_list as $table_rs){
					if ($add_column == $table_rs["Field"]){
						$column_key = $table_rs['Extra'] == 'auto_increment'?$table_rs['Extra']:$table_rs['Key'];
						$add_data = array('table_name'=>$table_name,'column_name'=>$add_column,'c_type'=>$table_rs['Type'],'column_desc'=>$table_rs['Comment'],'column_type'=>'text','column_key'=>$column_key,'column_type_length'=>5);
						$add_id = $this->menusub->insert($add_data);
					}
				}
//				$add_list = $this->table->get_list("SELECT column_type,column_key,column_comment 
//	FROM information_schema.COLUMNS where TABLE_SCHEMA='".TABLE_DB."' and TABLE_NAME='$table_name' and column_name='$add_column'");
//				foreach ($add_list as $add_rs){
//					$add_data = array('table_name'=>$table_name,'column_name'=>$add_column,'c_type'=>$add_rs['column_type'],'column_desc'=>$add_rs['column_comment'],'column_type'=>'text','column_key'=>$add_rs['column_key']);
//					$add_id = $this->menusub->insert($add_data);
//				}
			}
			}
			//删除字段
			$del_column_list = array_diff($menu_sub_list,$table_list);
			if ($del_column_list){
			foreach ($del_column_list as $key=>$del_column){
				$this->menusub->delete("column_name='$del_column' and `table_name` ='$table_name'");			
			}
			}
			$column_list = $this->menusub->select('`table_name`="'.$table_name.'"','*','','id');
			//字段类型
			$type_value = array('text','select','textarea','a','radio','checkbox');
		}
		include template('manage', 'menu_info');
	}
}