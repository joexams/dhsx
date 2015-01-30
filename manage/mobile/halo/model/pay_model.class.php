<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class pay_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config   = common::load_config('database');
		$this->db_setting  = 'default';
		$this->db_tablepre = '';
		$this->table_name  = 'pay_data';
		parent::__construct();
	}
	/**
	 * [get_list description]
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
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