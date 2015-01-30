<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class priv_platform_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config  = common::load_config('database');
		$this->db_setting = 'platform';
		$this->table_name = 'platform_priv';
		parent::__construct();
	}

	public function set_model($modelid){
		if ($modelid > 0){
			$this->table_name = $this->db_tablepre.'platform_group';
		}else {
			$this->table_name = $this->db_tablepre.'platform_priv';
		}
	}
	/**
	 * 获取 表在使用前设定
	 */
	public function get_list($sql){
		$sql = trim($sql);
		if (empty($sql)) {
			return array();
		}
		$this->db->query($sql);
		$reslist = $this->fetch_array();
		$this->db->free_result();
		return $reslist;
	}	
}