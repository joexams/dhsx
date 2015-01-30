<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class cacheclear extends admin {
	function __construct(){
		parent::__construct();
	}
	public function init() {
		
	}

	public function memcache_flush() {
		common::load_class('cache_factory','',0);
		$cacheconfig = common::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache('memcache');
		
		$rtn = $cache->flush();
		if ($rtn) {
			output_json(0, 'Memcache缓存清除成功！');
		}else {
			output_json(1, 'Memcache缓存清除失败！');
		}

	}
}
