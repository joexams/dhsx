<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class setting extends admin {
	function __construct(){
		parent::__construct();
		$this->pagesize = 15;
	}

	public function init() {
		$config = common::load_config('system');
		$database = common::load_config('database');

		include template('manage', 'setting');
	}
}