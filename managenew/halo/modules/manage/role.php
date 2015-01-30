<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class role extends admin {
	private $roledb, $privdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->roledb  = common::load_model('role_model');
		$this->pagesize = 10;
	}

	public function init(){

		include template('manage', 'role');
	}

	/**
	 * 添加角色
	 * 
	 */ 
	public function add(){
		if (isset($_POST['doSubmit'])){

			$roleid = isset($_POST['roleid']) ? intval($_POST['roleid']) : 0;
			$info = array();
			$editflag = 0;

			$info['rolename'] = trim($_POST['rolename']);
			$info['description'] = trim($_POST['description']);
			$info['disabled'] = intval($_POST['disabled']);
			$info['listorder'] = intval($_POST['listorder']);

			if ($roleid > 0){
				$editflag = 1;
				$rtn = $this->roledb->update($info, array('roleid' => $roleid));
				$roleid = $rtn ? $roleid : 0;
			}else {
				$roleid = $this->roledb->insert($info, true);
			}
			
			if ($roleid > 0) {
				$msg	   = $editflag ? Lang('edit_role_success') : Lang('add_role_success');
				$data['editflag'] = $editflag;
				$data['info'] = $info;
				$data['info']['roleid']    = $roleid;

				$content = ($editflag ? Lang('edit_role_success') : Lang('add_role_success')).'  角色ID：'.$roleid.'  角色名：'.$info['rolename'];
				parent::op_log($content);
				output_json(0, $msg, $data);
			}

			$msg	 = $editflag ? Lang('edit_role_error') : Lang('add_role_error');
			$content = ($editflag ? Lang('edit_role_error') : Lang('add_role_error')).'  角色ID：'.$roleid.'  角色名：'.$info['rolename'];
			parent::op_log($content);
			output_json(1, $msg);
		}
	}

	/**
	 * 获取角色列表
	 *  
	 */ 
	public function ajax_list(){
		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$list = $this->roledb->get_list_page('', 'roleid,rolename,description,disabled,listorder', '', $page, $this->pagesize);
		if ($recordnum <= 0){
			$recordnum = $this->roledb->count('', 'roleid');
		}

		$data['count']  = $recordnum;
		$data['list']   = $list;
		output_json(0, '', $data);
	}

	/**
	 * 获取角色信息
	 * 
	 */ 
	public function ajax_info(){
		$roleid = intval($_GET['roleid']);
		$data['info'] = array();

		if ($roleid > 0){
			$data['info'] = $this->roledb->get_one(array('roleid'=>$roleid), 'roleid,rolename,description,disabled,listorder');			
		}
		if (empty($data['info'])){
			output_json(1, Lang('role_no_exits'));
		}

		output_json(0, '', $data);
	}

	/**
	 * 删除角色信息
	 * 
	 */ 
	public function delete(){
		/*$roleid   = intval($_POST['roleid']);
		$rolename = trim($_POST['rolename']);
		$status = $this->roledb->delete(array('roleid' => $roleid));

		if ($status){
			$content = Lang('delete_role_success').'  角色ID：'.$roleid.'  角色名：'.$rolename;
			parent::op_log($content);
			output_json(0, Lang('delete_role_success').$rolename);
		}
		
		$content = Lang('delete_role_error').'  角色ID：'.$roleid.'  角色名：'.$rolename;
		parent::op_log($content);
		output_json(1, Lang('delete_role_error'));
		*/
	}
}
