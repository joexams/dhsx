<?php
defined('IN_G') or exit('No permission resources.');

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today.' 00:00:00')));
$filename = CACHE_PATH . 'log/favor_total_'.$yesterday.'.json';
if (!file_exists($filename)) exit('');

echo file_get_contents($filename);
