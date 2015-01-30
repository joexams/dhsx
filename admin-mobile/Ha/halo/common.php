<?php
define('IN_G', true);
define('CORE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
if(!defined('ROOT_PATH')) define('ROOT_PATH', CORE_PATH.'..'.DIRECTORY_SEPARATOR);

define('WEB_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
define('STYLE', 'default');
define('TIME', time());
define('WEB_URL',common::load_config('system', 'web_url'));
define('INDEX',  common::load_config('system', 'dispatch'));
define('TABLE_DB',  common::load_config('system', 'table_db'));

common::load_func('global');
common::load_config('system','errorlog') ? set_error_handler('my_error_handler') : error_reporting(E_ERROR | E_WARNING | E_PARSE);

//设置本地时差
function_exists('date_default_timezone_set') && date_default_timezone_set(common::load_config('system','timezone'));

@header('Content-Type: text/html; charset=utf-8');

if(common::load_config('system','gzip') && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
}else {
	ob_start();
}

class common {
	/**
	 * 加载系统类方法
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	public static function load_class($classname, $path = '', $initialize = 1) {
		$database = common::load_config('database', 'default');
		
		if ($database["type"] == 'mysqli' and $classname == 'model'){
			$classname = 'modeli';
		}
		return self::_load_class($classname, $path, $initialize);
	}
	/**
	 * 加载应用类方法
	 * @param string $classname 类名
	 * @param string $m 模块
	 * @param intger $initialize 是否初始化
	 */
	public static function load_app_class($classname, $m = '', $initialize = 1) {
		$m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_class($classname, 'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'class', $initialize);
	}
	/**
	 * 加载数据模型
	 * @param string $classname 类名
	 */
	public static function load_model($classname, $initialize = 1) {
		return self::_load_class($classname,'model', $initialize);
	}
	/**
	 * 加载系统的函数库
	 * @param string $func 函数库名
	 */
	public static function load_func($func) {
		return self::_load_func($func);
	}
	/**
	 * 加载类文件函数
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	private static function _load_class($classname, $path = '', $initialize = 1) {
		static $classes = array();
		
		
		if (empty($path)) $path = 'core';
		$key = md5($path.$classname);
		
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(CORE_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
			include CORE_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php';
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
	/**
	 * 加载函数库
	 * @param string $func 函数库名
	 * @param string $path 地址
	 */
	private static function _load_func($func, $path = '') {
		static $funcs = array();
		if (empty($path)) $path = 'core'.DIRECTORY_SEPARATOR.'func';
		$path .= DIRECTORY_SEPARATOR.$func.'.inc.php';
		$key = md5($path);
		if (isset($funcs[$key])) return true;
		if (file_exists(CORE_PATH.$path)) {
			include CORE_PATH.$path;
		} else {
			$funcs[$key] = false;
			return false;
		}
		$funcs[$key] = true;
		return true;
	}
	/**
	 * 加载API类库
	 */
	public static function load_api_class($classname, $version = '' ,$initialize = 1) {
		if (empty($version)) return false;
		return common::load_class($classname, 'api'.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR, $initialize);
	}
	/**
	 * 加载API数据模型
	 * @param string $classname 类名
	 */
	public static function load_api_model($classname,$version) {
		if (empty($version)) return false;
		$path = 'api'.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'model';
		return self::_load_class($classname,$path);
	}
	/**
	 * 加载API模版
	 * 
	 */ 
	public static function load_api_template($block, $version){
		if (empty($version)) return false;
		$path = 'api'.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.$block.'.php';
		$key = md5($path);
		if (isset($blocks[$key])) return true;
		if (file_exists(CORE_PATH.$path)) {
			include CORE_PATH.$path;
		}else {
			$blocks[$key] = false;
			return false;
		}
		$blocks[$key] = true;
		return true;
	}
	/**
	 * 加载配置文件
	 * @param string $file 配置文件
	 * @param string $key  要获取的配置荐
	 * @param string $default  默认配置。当获取配置项目失败时该值发生作用。
	 * @param boolean $reload 强制重新加载。
	 */
	public static function load_config($file, $key = '', $default = '', $reload = false) {
		static $configs = array();
		if (!$reload && isset($configs[$file])) {
			if (empty($key)) {
				return $configs[$file];
			} elseif (isset($configs[$file][$key])) {
				return $configs[$file][$key];
			} else {
				return $default;
			}
		}
		$path = ROOT_PATH.'caches'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$file.'.php';
		if (file_exists($path)) {
			$configs[$file] = include $path;
		}
		if (empty($key)) {
			return $configs[$file];
		} elseif (isset($configs[$file][$key])) {
			return $configs[$file][$key];
		} else {
			return $default;
		}
	}
	/**
	 * 判断HTTP头是否为Ajax
	 */
	public static function is_ajax()
	{
		$index = 'HTTP_X_REQUESTED_WITH';
		if ( $_SERVER[strtoupper($index)] !== null && strtolower($_SERVER[strtoupper($index)]) === 'xmlhttprequest' ) {
			return true;
		}
		return false;
	}	
	/**
	 * 统计开销时间和开销内存
	 */
	public static function app_total(){
		return array(
			microtime(true) - HALO_START_TIME,
			memory_get_peak_usage() - HALO_START_MEM
		);
	}
}
