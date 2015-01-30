<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class consume extends admin {
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 消费统计
	 * @return [type] [description]
	 */
	public function total(){
		$serverdb  = common::load_model('public_model');
		$serverdb->table_name = 'servers';
		$server = $serverdb->get_one(array('sid' => 4745), 'db_server,db_root,db_pwd,db_name,server_ver');
		common::load_model('getdb_model', 0);
		$dbconfig = array(
			'game' => array(
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
		$getdb = new getdb_model($dbconfig, 'game');
		$getdb->table_name  = 'ingot_change_type';
		$typelist = $getdb->select('', 'id,name');	
		include template('report', 'consume_total');
	}
}
