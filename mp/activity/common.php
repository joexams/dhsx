<?php 
error_reporting(1);
date_default_timezone_set('Asia/Shanghai');
define('PATH_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

$appid  = '100616996';
$appkey = '12731d393543f86736b8d92654d3e6f1';

$dbsetting = array(
	'dbhost' => '10.142.81.14',
	'dbuser' => 'root',
	'dbpwd'  => '!e9K6aU[hkS$',
	'dbname' => 'game_manage',
	'dbport' => '8810',
	'dbmaster' => false,
);

common::load_class('Sql', '', 0);

class common{

	public static function load_api_class($classname, $version = '' ,$initialize = 0) {
		if (empty($version)) return false;
		return self::_load_class($classname, 'gameapi'.DIRECTORY_SEPARATOR.$version, $initialize);
	}

	public static function load_class($classname, $path = '', $initialize) {
		return self::_load_class($classname, $path, $initialize);
	}

	private static function _load_class($classname, $path = '', $initialize = 1) {
		static $classes = array();
		if (empty($path)) $path = 'lib';
		$key = md5($path.$classname);
		
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(PATH_ROOT.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
			include PATH_ROOT.$path.DIRECTORY_SEPARATOR.$classname.'.class.php';
			$name = $classname;
			if ($initialize) {
				$classes[$key] = new $name;
			} else {
				$classes[$key] = true;
			}
			return $classes[$key];
		} else {
			return false;
		}
	}

	public static function output($ret, $msg = '', $data=array(), $format = 'json') {
		$format = empty($format) ? 'json' : $format;
		$ret    = intval($ret);
		if ($format == 'json') {
			$output = array(
				'ret' => $ret,
				'msg' => $msg,
			);
			if (is_array($data) && !empty($data)) {
				$output = array_merge($output, $data);
			}
			echo json_encode($output);
		}
		exit;
	}

	public static function isOpenId($openid)
	{
		return (0 == preg_match('/^[0-9a-fA-F]{32}$/', $openid)) ? false : true;
	}
}

function getIp() {
	if (isset($_SERVER)) {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}
