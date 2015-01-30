<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class block extends admin {
	private $blockdb;
	function __construct(){
		parent::__construct();
		$this->blockdb = common::load_model('block_model');
	}

	public function init(){
		$list = $this->blockdb->select();
		$blocklist = array();
		foreach ($list as $key => $value) {
			$blocklist[$value['bid']]['id'] = $value['bid'];
			$blocklist[$value['bid']]['parentid'] = $value['parentid'];
			$blocklist[$value['bid']]['name'] = $value['bname'];
			$blocklist[$value['bid']]['level'] = $value['depth'];
			$blocklist[$value['bid']]['parentid_node'] = ($value['parentid'])? '  data-tt-parent-id="'.$value['parentid'].'"' : '';
		}

		$menu = common::load_class('tree');
		$menu->icon = array('','','');
		$menu->nbsp = '';
		$str  = "<tr data-tt-id='\$id' \$parentid_node>
					<td>\$spacer \$name</td>
					<td><a href='javascript:blockManage(0, \$id, &#39;\$name&#39;);'>添加子模块</a> | <a href='javascript:blockManage(\$id);'>修改</a> | <a href='javascript:noderemove(\$id, &#39;\$name&#39;, \$level);'>删除</a></td>
				</tr>";
		$menu->init($blocklist);
		$categorys = $menu->get_tree(0, $str);

		unset($list, $blocklist);
		include template('manage', 'block');
	}
	/**
	 * 添加模块
	 * 
	 */ 
	public function add(){
		if (isset($_POST['doSubmit'])){
			$bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
			$info = array();
			$editflag = 0;

			$info['bname'] = isset($_POST['bname']) ? trim($_POST['bname']) : '';
			$info['listorder'] = isset($_POST['listorder']) ? intval($_POST['listorder']) : '';
			$info['key']  = isset($_POST['key']) ? trim($_POST['key']) : '';

			$info['parentid'] = isset($_POST['parentid']) ? intval($_POST['parentid']) : 0;
			if ($info['parentid'] > 0){
				$pinfo = $this->blockdb->get_one(array('bid' => $info['parentid']), 'bid,depth,parentid,parentpath');
				$info['depth'] = $pinfo['depth'] + 1;
				$info['parentpath'] = !empty($pinfo['parentpath']) ? $pinfo['parentpath'].$pinfo['bid'].',': ','.$pinfo['bid'].',';
			}else {
				$info['parentpath'] = '';
				$info['depth'] = 0;
			}

			if ($bid > 0){
				$editflag = 1;
				$rtn = $this->blockdb->update($info, array('bid' => $bid));
				$bid = $rtn ? $bid : 0;
			}else {
				$bid = $this->blockdb->insert($info, true);
			}

			if ($bid > 0) {
				$msg      = $editflag ? Lang('edit_block_success') : Lang('add_block_success');
				$data['info']['id']       = $bid;
				$data['info']['pId']      = $info['parentid'];
				$data['info']['name']     = $info['bname'];
				$data['info']['editflag'] = $editflag;

				$content = ($editflag ? Lang('edit_block_success') : Lang('add_block_success')).'  模块ID：'.$bid.'  模块名：'.$info['bname'];
				parent::op_log($content);
				output_json(0, $msg, $data);
			}
			$msg	 = $editflag ? Lang('edit_block_error') : Lang('add_block_error');
			$content = ($editflag ? Lang('edit_block_error') : Lang('add_block_error')).'  模块ID：'.$bid.'  模块名：'.$info['bname'];
			parent::op_log($content);
			output_json(0, $msg);
		}else {
			
			$addressdb = common::load_model('public_model');
			$addressdb->table_name = 'servers_address';
			$arr_version = $addressdb->select('type=2');

			$tree_list = $list = array();
			$list = $this->blockdb->select('', 'bid as id, parentid, bname as name');
			foreach ($list as $key => $value) {
				$tree_list[$value['id']] = $value;
			}

			$bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
			$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
			$data['info'] = array();
			$parentid = $pid;
			if ($bid > 0) {
				$data['info'] = $this->blockdb->get_one(array('bid' => $bid));
				$parentid = $data['info']['parentid'];
			}

			$tree = common::load_class('tree');
			$tree->init($tree_list);
			$tree_str = $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer \$name</option>", $parentid);
			unset($tree_list, $list);

			include template('ajax', 'block_add');
		}
	}
	/**
	 * 模块删除
	 * @return [type] [description]
	 */
	public function delete() {
		$bid = isset($_POST['blockid']) && intval($_POST['blockid']) >0 ? intval($_POST['blockid']) : 0;
		$bname = isset($_POST['blockname']) && !empty($_POST['blockname']) ? trim($_POST['blockname']): '';

		if ($bid > 0){
			$block = $this->blockdb->get_one(array('bid' => $bid));
			// $childblock = $this->blockdb->select("parentid='$bid'", '*');
			if ($block){
				$this->blockdb->delete(array('bid' => $bid));
				$this->blockdb->delete(array('parentid' => $bid));

				$content = Lang('block_delete_success').'  模块ID：'.$bid.'  模块名：'.$block['bname'];
				parent::op_log($content);
				output_json(0, Lang('block_delete_success'));
			}

			$content = Lang('block_no_exist').'  模块ID：'.$bid;
			parent::op_log($content);
			output_json(1, Lang('block_no_exist'));
		}

		output_json(1, Lang('block_no_exist'));
	}
}