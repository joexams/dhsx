<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class server_merge_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config  = common::load_config('database');
		$this->db_setting = 'platform';
		$this->table_name = 'servers_merge';
		parent::__construct();
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
	/**
	 * 获取数目,表在使用前设定
	 */
	public function get_count($sql){
		$sql = trim($sql);
		if (empty($sql)) {
			return 0;
		}
		$this->db->query($sql);
		$res = $this->db->fetch_next();
		$this->db->free_result();
		return $res['num'];
	}
}