<?php
require('common.php');

$ts = time();
$openid = isset($_GET['openid']) ? trim($_GET['openid']) : '';
$zoneid = isset($_GET['zoneid']) ? intval($_GET['zoneid']) : 0;
$sign   = isset($_GET['sign']) ? trim($_GET['sign']) : '';
$contractid   = isset($_GET['contractid']) ? trim($_GET['contractid']) : '';
if (empty($openid) || empty($sign) || $zoneid <= 0 || empty($contractid)) exit('2');
// if ($contractid != '100616996T2201211130001') exit(5);
if ($sign != md5($openid.'_'.$zoneid.'_'.$contractid.'_{5ce06196-f92a-4310-bf90-8e196fdf76a6}')) exit('3');

$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);

$ret = 4;
$num = $dblink->count('player_task', array('openid' => $openid, 'contractid' => $contractid, 'zoneid' => $zoneid));
if ($num <= 0) {
	$res = $dblink->insert('player_task', array('openid' => $openid, 'contractid' => $contractid, 'zoneid' => $zoneid, 'status' => 0, 'ts' => $ts));
	if ($res) {
		$ret = 0;
	}else {
		$ret = 1;
	}
}

$dblink->close();

echo $ret;
