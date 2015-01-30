<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
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
		include template('operation', 'account_customer');
	}

	public function intermodal() {
		$roledb = common::load_model('role_model');

		$roleid = intval($_SESSION['roleid']);
		$data['rolelist'] = $roledb->select("roleid>='$roleid'", 'roleid,rolename', '', 'roleid DESC');
		$data['jsonrole'] = json_encode($data['rolelist']);

		include template('operation', 'account_intermodal');
	}
}