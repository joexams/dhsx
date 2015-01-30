<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class public_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config = common::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = '';
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
	/**
	 * 设置游戏服连接
	 */
	public function set_db($sid) {
		$server = $this->get_server($sid);

		if (empty($server['db_server']) || empty($server['db_root']) || empty($server['db_pwd']) || empty($server['db_name']))	return false;

		common::load_model('getdb_model', 0);
		$dbconfig = array(
			$server['db_name'] => array(
					'hostname' => $server['db_server'],
					'database' => $server['db_name'],
					'username' => $server['db_root'],
					'password' => $server['db_pwd'],
					'tablepre' => '',
					'charset' => 'utf8',
					'type' => 'mysql',
					'debug' => false,
					'pconnect' => 0,
					'autoconnect' => 0
				)
			);

		return new getdb_model($dbconfig, $server['db_name']);
	}

	/**
	 * 获取游戏服信息
	 * @param  [int] $sid [description]
	 * @return [array | object]      [description]
	 */
	public function get_server($sid, $is_obj = false)
	{
		$sid = intval($sid);
		$this->table_name = 'servers';
		$server = $this->get_one(array('sid' => $sid), 'name,db_server,db_root,db_pwd,db_name,server_ver,server,api_server,api_port,api_pwd');
		if ($is_obj) {
			$version = trim($server['server_ver']);
			$apiadmin = common::load_api_class('api_admin', $version);
			if ($apiadmin !== false) {
				$apiadmin::$SERVER    = $server['api_server'];
				$apiadmin::$PORT      = $server['api_port'];
				$apiadmin::$ADMIN_PWD = $server['api_pwd'];
			}
			return $apiadmin;
		}
		return $server;
	}
}