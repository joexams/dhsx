<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class getdb_model extends model {
	public $db_config, $db_setting, $table_name = '';
	public function __construct($db_config = array(), $db_setting = '', $ispre=0) {
		if (!$db_config) {
			$this->db_config = common::load_config('database');
		} else {
			$this->db_config = $db_config;
		}
		if (!$db_setting) {
			$this->db_setting = 'default';
		} else {
			$this->db_setting = $db_setting;
		}
		
		parent::__construct();
		if ($db_setting && $db_config[$db_setting]['db_tablepre'] && !$ispre) {
			$this->db_tablepre = $db_config[$db_setting]['db_tablepre'];
		}
	}
	
	public function sql_query($sql) {
		return parent::query($sql);
	}
	
	public function fetch_next() {
		return $this->db->fetch_next();
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