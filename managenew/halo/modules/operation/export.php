<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class export extends admin {
	public function __construct(){
		parent::__construct();
	}

	public function init(){

	}
	/**
	 * 玩家资料
	 * @return [type] [description]
	 */
	public function player() {

		include template('operation', 'export_player');
	}
	/**
	 * 充值记录
	 * @return [type] [description]
	 */
	public function paylog() {

		include template('operation', 'export_paylog');
	}
}