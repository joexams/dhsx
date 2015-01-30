<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class quickguide_model extends model {
	public $table_name = '', $tree;
	public function __construct() {
		$this->db_config = common::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'quick_guide';
		parent::__construct();
	}

}