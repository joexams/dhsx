<?php
require('common.php');

$time = time();
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$zoneid = isset($_GET['zoneid']) ? intval($_GET['zoneid']) : 0;
$discountid = isset($_GET['discountid']) ? trim($_GET['discountid']) : '';
$sign   = isset($_GET['sign']) ? trim($_GET['sign']) : '';

if (empty($token) || empty($sign) || $zoneid <= 0 || empty($discountid)) exit('2');

if ($sign != md5($token.'_'.$zoneid.'_'.$discountid.'_{5ce06196-f92a-4310-bf90-8e196fdf76a6}')) exit('3');

$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);

$tablename = '';
switch ($discountid) {
	case 'UM130329142426527':
	case 'UM121012173440423':
	case 'UM121012173755347':
		//抽奖活动
		$tablename = 'pay_lottery_token';
		break;
	case 'UM120917133846681':
		//蓝钻大礼包活动
		$tablename = 'pay_gift_token';
		break;
}


$ret = 4;
$num = $dblink->count($tablename, array('token' => $token));
if ($num <= 0) {
	$res = $dblink->insert($tablename, array('token' => $token, 'zoneid' => $zoneid, 'time' => $time));
	if ($res) {
		$ret = 0;
	}else {
		$ret = 1;
	}
}

$dblink->close();

echo $ret;
