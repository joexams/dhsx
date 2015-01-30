<?php
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'caches'.DIRECTORY_SEPARATOR);

require(ROOT_PATH.DIRECTORY_SEPARATOR.'halo/common.php');

$app = isset($_GET['app']) && trim($_GET['app']) ? trim($_GET['app']) : exit('App can not be empty');
if (!preg_match('/([^a-z_]+)/i',$app) && file_exists(ROOT_PATH.'api/'.$app.'.php')) {
	include ROOT_PATH.'api/'.$app.'.php';
} else {
	exit('API handler does not exist');
}