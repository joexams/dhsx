<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class template extends admin {
	private $tmpldb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->tmpldb = common::load_model('template_model');
		$this->pagesize = 15;
	}

	public function init(){
		include template('manage', 'template');
	}
    /**
     * 获取列表
     * 
     */ 
	public function ajax_init_list(){
		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$list = $this->tmpldb->get_list_page('', '*', '', $page, $this->pagesize);
		if ($recordnum <= 0){
			$recordnum = $this->tmpldb->count('', 'tid');
		}
		if (count($list) > 0){
			foreach ($list as $key => $value) {
				if (!empty($value['args'])){
					$list[$key]['args'] = unserialize($value['args']); 
				}
				if (!empty($value['rtns'])){
					$list[$key]['rtns'] = unserialize($value['rtns']); 
				}
			}
		}

		$data['count']  = $recordnum;
		$data['list']   = $list;
		output_json(0, '', $data);
	}
	/**
	 * 添加
	 * 
	 */ 
	public function add(){
		if (isset($_POST['doSubmit'])){
			$tid      = isset($_POST['tid']) ? intval($_POST['tid']) : 0;
			$info     = array();
			$editflag = 0;
			
			$arg      = $_POST['arg'];
			$arg_tips = $_POST['arg_tips'];
			$rtn      = $_POST['rtn'];
			$rtn_tips = $_POST['rtn_tips'];

			$args = $rtns = array();
			if (is_array($arg) && count($arg) > 0){
				$i = 0;
				foreach ($arg as $key => $value) {
					if (!empty($value)){
						$args[$i]['arg']  = $value;
						$args[$i]['tips'] = $arg_tips[$key]; 
						$i += 1;
					}
				}
			}
			if (is_array($rtn) && count($rtn) > 0){
				$i = 0;
				foreach ($rtn as $key => $value) {
					if (!empty($value)){
						$rtns[$i]['rtn']  = $value;
						$rtns[$i]['tips'] = $rtn_tips[$key]; 
						$i += 1;
					}
				}
			}
			$info['args'] = serialize($args);
			$info['rtns'] = serialize($rtns);
			$info['key'] = trim($_POST['key']);
			$info['title'] = trim($_POST['title']);
			$info['version'] = trim($_POST['version']);
			$info['content'] = trim($_POST['content']);

			if ($tid > 0){
				$editflag = 1;
				$rtn = $this->tmpldb->update($info, array('tid' => $tid));
				$tid = $rtn ? $tid : 0;
			}else {
				$tid = $this->tmpldb->insert($info, true);
			}
			
			if ($tid > 0){
				$data['editflag'] = $editflag;
				$data['info']   = array(
						'tid'     => $tid, 
						'key'     => $info['key'], 
						'title'   => $info['title'], 
						'version' => $info['version'], 
						'args'    => $args,
						'rtns'    => $rtns,
 					);

				unset($args,$arg, $arg_tips, $rtns, $rtn, $rtn_tips);
				output_json(0, Lang('success'), $data);
			}

			unset($args,$arg, $arg_tips, $rtns, $rtn, $rtn_tips);
			output_json(1, Lang('error'));
		}else {
			$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
			$data['info'] = array();
			if ($tid > 0) {
				$data['info'] = $this->tmpldb->get_one(array('tid' => $tid));
				if (!empty($data['info']['args'])){
					$data['info']['args'] = unserialize($data['info']['args']); 
				}
				if (!empty($data['info']['rtns'])){
					$data['info']['rtns'] = unserialize($data['info']['rtns']); 
				}
			}

			$addressdb = common::load_model('public_model');
			$addressdb->table_name = 'servers_address';
			$arr_version = $addressdb->select('type=2');
			include template('ajax', 'template_add');
		}
	}
	/**
	 * 获取模板
	 * 
	 */ 
	public function public_info(){
		$key = isset($_GET['key']) ? trim($_GET['key']) : '';
		$sid = intval($_GET['sid']) > 0 ? intval($_GET['sid']) : 0;
		$version = isset($_GET['version']) ? trim($_GET['version']) : '';
		if (!empty($key) && $sid > 0){
			$tmpllist = $this->tmpldb->select(array('key' => $key), 'tid, version, content');
			$data['info'] = array();
			if (!empty($version) && count($tmpllist) > 1){
				foreach ($tmpllist as $key => $value) {
					if (!empty($value['version']) && $value['version'] >= $version){
						$data['info'] = $value;
						break;
					}else {
						$data['info'] = $value;
					}
				}
			}else {
				$data['info'] = $tmpllist[0];
			}
			if (!empty($data['info'])){
				output_json(0, '', $data);
			}

			output_json(1, Lang('template_no_exist'));
		}
		
		output_json(1, Lang('args_no_enough'));
	}
	/**
	 * 删除模版
	 * 
	 */ 
	public function delete(){
		$tid = intval($_POST['tid']);
		if ($tid > 0){
			$template = $this->tmpldb->get_one(array('tid' => $tid));
			if ($template){
				$this->tmpldb->delete(array('tid' => $tid));
				$content = Lang('template_delete_success').'  '.Lang('template_id').$tid.'  '.Lang('template_name').$template['title'];
				parent::op_log($content);
				output_json(0, Lang('template_delete_success'));
			}

			$content = Lang('template_no_exist').'  '.Lang('template_id').$tid;
			parent::op_log($content);
			output_json(1, Lang('template_no_exist'));
		}
		
		$content = Lang('template_no_exist').'  '.Lang('template_id').$tid;
		parent::op_log($content);
		output_json(1, Lang('template_no_exist'));
	}
}