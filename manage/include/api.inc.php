<?php
if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

//加载API类库
class callapi{	

	public static function load_api_class($ver) {
		if (empty($ver)) return false;
		return callapi::load_class($ver);
	}

	public static function load_class($ver) {
		return self::_load_class($ver);
	}

	private static function _load_class($ver) {
		static $classes = array();
		if (empty($path)) $path = 'core';
		$key = md5($path.'api_admin');
		
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(UCTIME_ROOT.'/mod/'.$ver.'/api_admin.class.php')) {
			include UCTIME_ROOT.'/mod/'.$ver.'/api_admin.class.php';
			$name = 'api_admin';
			$classes[$key] = new $name;
			return $classes[$key];
		} else {
			return false;
		}
	}
}
