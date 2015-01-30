<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class menu extends admin {
	private $menudb;
	function __construct(){
		parent::__construct();
		$this->menudb = common::load_model('menu_model');
	}

	public function init(){
		
		$list = $this->menudb->select();
		$menulist = array();
		foreach ($list as $key => $value) {
			$menulist[$value['mid']]['id'] = $value['mid'];
			$menulist[$value['mid']]['parentid'] = $value['parentid'];
			$menulist[$value['mid']]['name'] = $value['mname'];
			$menulist[$value['mid']]['level'] = $value['depth'];
			$menulist[$value['mid']]['parentid_node'] = ($value['parentid'])? '  data-tt-parent-id="'.$value['parentid'].'"' : '';
		}

		$menu = common::load_class('tree');
		$menu->icon = array('','','');
		$menu->nbsp = '';
		$str  = "<tr data-tt-id='\$id' \$parentid_node>
					<td>\$spacer \$name</td>
					<td><a href='javascript:menuManage(0, \$id, &#39;\$name&#39;);'>添加子菜单</a> | <a href='javascript:menuManage(\$id);'>修改</a> | <a href='javascript:noderemove(\$id, &#39;\$name&#39;, \$level);'>删除</a></td>
				</tr>";
		$menu->init($menulist);
		$categorys = $menu->get_tree(0, $str);
		unset($list, $menulist);

		include template('manage', 'menu');
	}
    /**
     * 添加菜单
     * 
     */ 
	public function add(){
		if (isset($_POST['doSubmit'])){
			$mid = isset($_POST['mid']) ? intval($_POST['mid']) : 0;
			$info = array();
			$editflag = 0;

			$info['mname'] = isset($_POST['mname']) ? trim($_POST['mname']) : '';
			$info['m'] = isset($_POST['m']) ? trim($_POST['m']) : '';
			$info['v'] = isset($_POST['v']) ? trim($_POST['v']) : '';
			$info['c'] = isset($_POST['c']) ? trim($_POST['c']) : '';
			$info['data'] = isset($_POST['data']) ? trim($_POST['data']) : '';
			$info['display'] = isset($_POST['m']) ? intval($_POST['display']) : 1;
			$info['listorder'] = isset($_POST['listorder']) ? intval($_POST['listorder']) : '';
			$info['islink'] = isset($_POST['islink']) ? intval($_POST['islink']) : 0;
			$info['urllink'] = isset($_POST['urllink']) ? trim($_POST['urllink']) : '';
			$info['isdistrib'] = isset($_POST['isdistrib']) ? intval($_POST['isdistrib']) : 0;

			$info['parentid'] = isset($_POST['parentid']) ? intval($_POST['parentid']) : 0;
			if ($info['parentid'] > 0){
				$pinfo = $this->menudb->get_one(array('mid' => $info['parentid']), 'mid,depth,parentid,parentpath');
				$info['depth'] = $pinfo['depth'] + 1;
				$info['parentpath'] = !empty($pinfo['parentpath']) ? $pinfo['parentpath'].$pinfo['mid'].',': ','.$pinfo['mid'].',';
			}else {
				$info['parentpath'] = '';
				$info['depth'] = 0;
			}

			if ($mid > 0){
				$editflag = 1;
				$rtn = $this->menudb->update($info, array('mid' => $mid));
				$mid = $rtn ? $mid : 0;
			}else {
				$mid = $this->menudb->insert($info, true);

				// if ($mid > 0){
				// 	$parentpath = explode(',', trim($info['parentpath'], ','));
				// 	if (count($parentpath) > 0){
				// 		$parentlist = $this->menudb->select("mid in (".trim($info['parentpath'], ',').")", 'mid,depth,childpath,parentpath');
				// 		$new_parentlist = array();
				// 		foreach ($parentlist as $key => $value) {
				// 			$new_parentlist[$value['mid']] = $value;
				// 		}
				// 		unset($parentlist);
				// 		foreach ($parentpath as $key => $pid) {
				// 			$childpath = empty($new_parentlist[$pid]['childpath']) ? ','.$mid.',': $new_parentlist[$pid]['childpath'].$mid.',';
				// 			$this->menudb->update(array('childpath'=>$childpath), array('mid'=>$pid));
				// 		}
				// 	}
				// }
			}

			if ($mid > 0) {
				$status   = 0;
				$msg      = $editflag ? Lang('edit_menu_success') : Lang('add_menu_success');

				$data['info']['id']   = $mid;
				$data['info']['pId']  = $info['parentid'];
				$data['info']['name'] = $info['mname'];
				$data['editflag']     = $editflag;

				$content = ($editflag ? Lang('edit_menu_success') : Lang('add_menu_success')).'  菜单ID：'.$mid.'  菜单名：'.$info['mname'];
				parent::op_log($content);
				output_json(0, $msg, $data);
			}

			$msg      = $editflag ? Lang('edit_menu_error') : Lang('add_menu_error');
			$content = ($editflag ? Lang('edit_menu_error') : Lang('add_menu_error')).'  菜单ID：'.$mid.'  菜单名：'.$info['mname'];
			parent::op_log($content);
			output_json(1, $msg);
		}else {
			$mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;
			$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
			$data['info'] = array();
			$parentid = $pid;
			if ($mid > 0) {
				$data['info'] = $this->menudb->get_one(array('mid' => $mid));
				$parentid = $data['info']['parentid'];
			}

			$tree_list = $list = array();
			$list = $this->menudb->select('', 'mid as id, parentid, mname as name');
			foreach ($list as $key => $value) {
				$tree_list[$value['id']] = $value;
			}
			$tree = common::load_class('tree');
			$tree->init($tree_list);
			$tree_str = $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer \$name</option>", $parentid);
			unset($tree_list, $list);

			include template('ajax', 'menu_add');
		}
	}

	/**
	 * 删除菜单
	 * 
	 */ 
	public function delete(){
		$mid = intval($_POST['menuid']);
		if ($mid > 0){
			$menu = $this->menudb->get_one(array('mid' => $mid));
			$childmenu = $this->menudb->select("parentid='$mid'", '*');
			if ($menu){
				$this->menudb->delete(array('mid' => $mid));
				$this->menudb->delete(array('parentid' => $mid));
				$privdb = common::load_model('priv_model');
				$privdb->delete(array('m'=>$menu['m'], 'v'=>$menu['v'], 'c'=>$menu['c']));
				if (!empty($childmenu)){
					foreach ($childmenu as $key => $child) {
						$privdb->delete(array('m'=>$child['m'], 'v'=>$child['v'], 'c'=>$child['c']));
					}
				}

				$content = Lang('menu_delete_success').'  菜单ID：'.$mid.'  菜单名：'.$menu['mname'];
				parent::op_log($content);
				output_json(0, Lang('menu_delete_success'));
			}

			$content = Lang('menu_no_exist').'  菜单ID：'.$mid;
			parent::op_log($content);
			output_json(1, Lang('menu_no_exist'));
		}
		
		$content = Lang('menu_no_exist').'  菜单ID：'.$mid;
		parent::op_log($content);
		output_json(1, Lang('menu_no_exist'));
	}
}