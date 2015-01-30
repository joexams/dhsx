<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class account extends admin {
	private $menudb, $privdb, $platformdb, $pagesize;
	function __construct() {
		parent::__construct();
	}

	public function customer() {
		$roledb = common::load_model('role_model');
		
		$roleid = intval($_SESSION['roleid']);
		$data['rolelist'] = $roledb->select('roleid=5', 'roleid,rolename', '', 'roleid DESC');
		$data['jsonrole'] = json_encode($data['rolelist']);
		$data['roleid']   = 5;
		$data['title']    = Lang('customer_manage');
		$data['hiddenId'] = 1;
		include template('manage', 'user');
	}

	public function intermodal() {
		$roledb = common::load_model('role_model');
		$roleid = intval($_SESSION['roleid']) >= 3 ? intval($_SESSION['roleid']) : 3;
		$data['rolelist'] = $roledb->select("roleid>='$roleid'", 'roleid,rolename', '', 'roleid DESC');
		$data['jsonrole'] = json_encode($data['rolelist']);
		$data['roleid']   = $roleid;
		$data['title']    = Lang('intermodal_manage');
		$data['hiddenId'] = 1;
		$data['more'] = 1;
		include template('manage', 'user');
	}
}