<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class priv_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config  = common::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'user_role_priv';
		parent::__construct();
	}

	public function set_model($modelid){
		if ($modelid > 0){
			$this->table_name = $this->db_tablepre.'user_priv';
		}else {
			$this->table_name = $this->db_tablepre.'user_role_priv';
		}
	}
	
}