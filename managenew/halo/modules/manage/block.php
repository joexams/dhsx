<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class block extends admin {
	private $blockdb;
	function __construct(){
		parent::__construct();
		$this->blockdb = common::load_model('block_model');
	}

	public function init(){
		$list = $this->blockdb->select('');
		$this->blockdb->tree = $list;
		$blocklist = array();
		foreach ($list as $key => $value) {
			$blocklist[$key]['id'] = $value['bid'];
			$blocklist[$key]['pId'] = $value['parentid'];
			$blocklist[$key]['name'] = $value['bname'];
			if ($this->blockdb->get_child($value['bid'])){
				$blocklist[$key]['open'] = 'true';
			}
		}
		$addressdb = common::load_model('public_model');
		$addressdb->table_name = 'servers_address';
		$arr_version = $addressdb->select('type=2');
		$str_version = '';
		foreach ($arr_version as $key => $value) {
			$str_version .= '<option value="'.$value['name'].'">'.$value['name'].'</option>';
		}
		$data['blocklist'] = $blocklist;
		$data['treelist'] = json_encode($blocklist);
		unset($list);
		include template('manage', 'block');
	}	
	/**
	 * 获取模块信息
	 * 
	 */ 
	public function ajax_info(){
		$bid = isset($_GET['blockid']) ? intval($_GET['blockid']) : 0;
		$data['info'] = $this->blockdb->get_one(array('bid' => $bid));
		if ($data['info']){
			output_json(0, '', $data);
 		}
 		output_json(1, Lang('get_info_no_exist'));
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