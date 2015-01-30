<?php
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);

//系统开始时间
define('HALO_START_TIME', microtime(true));
defined('HALO_START_MEM') or define('HALO_START_MEM', memory_get_usage());

require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');
common::load_class('controller');
// $bm = common::app_total();
// $total_exec_time = round($bm[0], 4);
// $total_exec_mem  = round($bm[1] / pow(1024, 2), 3);

// 性能监控，若执行时间过长，应记录日志
// echo 'Mem：'.$total_exec_mem;
// echo "<br>";
// echo 'Time：'.$total_exec_time;