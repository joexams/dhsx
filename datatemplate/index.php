<?php
// defined('FY_START_TIME') or define('FY_START_TIME', microtime(true));
// defined('FY_START_MEM') or define('FY_START_MEM', memory_get_usage());
$f3 = require('./lib/base.php');

$f3->config('config.ini');

$f3->set('AUTOLOAD', 'app/');

$f3->run();
// echo round((memory_get_peak_usage() - FY_START_MEM)/pow(1024, 2), 3);
// echo '<br>';
// echo round(microtime(true)-FY_START_TIME, 3);