<?php

class controller{
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		common::load_class('param');
		$param = common::load_class('param');
		define('ROUTE_M', $param->route_m());
		define('ROUTE_C', $param->route_c());
		define('ROUTE_V', $param->route_v());
		$this->init();
	}
	
	/**
	 * 调用件事
	 */
	private function init() {
		$controller = $this->load();
		if (method_exists($controller, ROUTE_V)) {
			if (preg_match('/^[_]/i', ROUTE_V)) {
				exit('You are visiting the action is to protect the private view');
			} else {
				call_user_func(array($controller, ROUTE_V));
			}
		} else {
			exit('Views does not exist.');
		}
	}
	
	/**
	 * 加载控制器
	 * @param string $filename
	 * @param string $m
	 * @return obj
	 */
	private function load($filename = '', $m = '') {
		if (empty($filename)) $filename = ROUTE_C;
		if (empty($m)) $m = ROUTE_M;
		$filepath = CORE_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.$filename.'.php';
		if (file_exists($filepath)) {
			$classname = $filename;
			include $filepath;
			if(class_exists($classname)){
				return new $classname;
			}else{
				exit('Controller does not exist.');
 			}
		} else {
			exit('Controller does not exist.');
		}
	}
}
?>