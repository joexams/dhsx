<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class company extends admin {
	public function __construct(){
		parent::__construct();
	}

	public function init(){

		include template('operation', 'company');
	}
}