<?php
isset($argv) or exit('No permission resources.');
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);
define('IN_CMD', true);

require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');

$app = isset($argv[1]) ? $argv[1] : exit('App can not be empty');
if (!preg_match('/([^a-z_]+)/i',$app) && file_exists(ROOT_PATH.'cron/'.$app.'.php')) {
	include ROOT_PATH.'cron/'.$app.'.php';
} else {
	exit('API handler does not exist');
}