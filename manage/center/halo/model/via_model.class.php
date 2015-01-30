<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class via_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config  = common::load_config('database');
		$this->db_setting = 'platform';
		$this->table_name = 'via_data';
		parent::__construct();
	}
}
