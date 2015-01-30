<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class log_model extends model {
	public $table_name = '', $tree;
	public function __construct() {
		$this->db_config = common::load_config('database');
		$this->db_setting = 'default_extend';
		$this->db_tablepre = 'ho_sys_';
		$this->table_name = $this->db_tablepre.'log_operation';
		parent::__construct();
	}
	/**
	 * 设置日志表
	 * 
	 */ 
	public function set_model($tablename){
		switch ($tablename) {
			case 'login':
				$this->table_name = 'ho_sys_log_login';
				break;
			case 'source':
				$this->table_name = 'ho_sys_log_source';
				break;
			case 'cron':
				$this->table_name = 'ho_sys_log_cron';
				break;
			case 'activity':
				$this->table_name = 'ho_sys_log_activity';
				break;
			default:
				$this->table_name = 'ho_sys_log_operation';
				break;
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
	/**
	 * 增加新记录
	 */
	public function add($tablename, $insertarr) {
		$this->set_model($tablename);
		return $this->insert($insertarr);
	}
}