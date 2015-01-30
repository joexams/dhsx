<?php
/**
 *  db_factory.class.php 数据库工厂类
 * @copyright			(C) 2010-2012
 * @author 				辰冰乐 <cybrobin@163.com>
 * @version 			V0.1
 * @create time 		2010-12-15
 */

final class DB_Factory {
	/**
	 * 当前数据库工厂类静态实例
	 */
	private static $db_factory;
	
	/**
	 * 数据库配置列表
	 */
	protected $db_config = array();
	
	/**
	 * 数据库操作实例化列表
	 */
	protected $db_list = array();
	
	/**
	 * 构造函数
	 */
	public function __construct() {
	}
	
	/**
	 * 返回当前终级类对象的实例
	 * @param $db_config 数据库配置
	 * @return object
	 */
	public static function get_instance($db_config = '') {
		if($db_config == '') {
			$db_config = common::load_config('database');
		}
		if(DB_Factory::$db_factory == '') {
			DB_Factory::$db_factory = new DB_Factory();
		}
		if($db_config != '' && $db_config != DB_Factory::$db_factory->db_config) DB_Factory::$db_factory->db_config = array_merge($db_config, DB_Factory::$db_factory->db_config);
		return DB_Factory::$db_factory;
	}
	
	/**
	 * 获取数据库操作实例
	 * @param $db_name 数据库配置名称
	 */
	public function get_database($db_name) {
		if(!isset($this->db_list[$db_name]) || !is_object($this->db_list[$db_name])) {
			$this->db_list[$db_name] = $this->connect($db_name);
		}
		return $this->db_list[$db_name];
	}
	
	/**
	 *  加载数据库驱动
	 * @param $db_name 	数据库配置名称
	 * @return object
	 */
	public function connect($db_name) {
		$object = null;
		switch($this->db_config[$db_name]['type']) {
			case 'mysql' :
				common::load_class('mysql', '', 0);
				$object = new mysql();
				break;
			case 'mysqli' :
				common::load_class('Sql', '', 0);
				$object = new Sql();
				break;
			default :
				common::load_class('mysql', '', 0);
				$object = new mysql();
		}
		$object->open($this->db_config[$db_name]);
		return $object;
	}

	/**
	 * 关闭数据库连接
	 * @return void
	 */
	protected function close() {
		foreach($this->db_list as $db) {
			$db->close();
		}
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		$this->close();
	}
}