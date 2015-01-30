<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class version extends admin {
	private $versiondb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->versiondb = common::load_model('version_model');
		$this->pagesize = 20;
	}

	public function init(){

	}
	/**
	 * 版本更新日志列表
	 * 
	 */ 
	public function ajax_setting_list(){
		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$list    = $this->versiondb->get_list_page('', '*', 'id DESC', $page, $this->pagesize);
		if ($recordnum <= 0){
			$recordnum = $this->versiondb->count('', 'id');
		}
		$data['list']  = $list;
		$data['count'] = $recordnum;
		output_json(0, '', $data);
	}
	/**
	 * 版本更新录入
	 * 
	 */ 
	public function setting(){
		if (isset($_POST['doSubmit'])){
			$info['version']  = isset($_POST['version']) ? trim($_POST['version']) : '';
			$info['content']  = isset($_POST['content']) ? trim($_POST['content']) : '';
			$dateline = isset($_POST['dateline']) ? trim($_POST['dateline']) : '';
			$info['dateline'] = !empty($dateline) ? strtotime($dateline) : time();

			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

			if ($id > 0){
				$rtn = $this->versiondb->update($info, array('id'=>$id));
				$id  = $rtn ? $id : 0;
			}else {
				$id = $this->versiondb->insert($info, true);
			}
			
			$data = array();
			if ($id > 0){
				$info['id']       = $id;
				$info['dateline'] = $info['dateline'];
				$data['info']     = $info;

				output_json(0, Lang('success'), $data);
			}

			output_json(1, Lang('error'));
		}else {
			$data['today'] = date('Y-m-d');
			include template('develop', 'version');
		}
	}
	/**
	 * 删除
	 * 
	 */ 
	public function ajax_setting_delete(){
		$id   = intval($_POST['id']);
		if ($id > 0){
			$rtn = $this->versiondb->delete(array('id' => $id));
			if ($rtn){
				output_json(0, Lang('success'));
			}
		}

		output_json(1, Lang('error'));
	}
}
