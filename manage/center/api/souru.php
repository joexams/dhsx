<?php
defined('IN_G') or exit('No permission resources.');

$ts = isset($_GET['time']) ? intval($_GET['time']) : 0;
$sig = isset($_GET['sign']) ? trim($_GET['sign']) : '';

if ($sig != md5($ts.'_{7f6f69f2-6e8b-47d4-bb92-210a9419a1e5}')) {
	echo 'error!';
	exit();
}

$pubdb = common::load_model('public_model');
$starttime = strtotime(date('Y-m-d'));
$endtime = $starttime + 24 *3600;
$wherestr = "dtime_unix>=$starttime AND dtime_unix<$endtime AND status<>1 AND success<>0";

$pubdb->table_name = 'pay_data';
$amount = $pubdb->get_one($wherestr, 'SUM(amount) AS amount');
$pay = $amount['amount'];

echo "document.write('".round($pay,2)."');";