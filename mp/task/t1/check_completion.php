<?php
require('..'.DIRECTORY_SEPARATOR.'common.php');
require('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'SnsSigCheck.php');

$openid = isset($_GET['openid']) ? trim($_GET['openid']) : 'R21';
$appid = isset($_GET['appid']) ? trim($_GET['appid']) : '';
$pf = isset($_GET['pf']) ? trim($_GET['pf']) : '';
$ts = isset($_GET['ts']) ? intval($_GET['ts']) : 0;
$contractid = isset($_GET['contractid']) ? trim($_GET['contractid']) : 'R21';
$sig = isset($_GET['sig']) ? trim($_GET['sig']) : '';

if (empty($openid) || empty($appid) || empty($pf) || $ts <= 0 || empty($contractid) || empty($sig))	common::output(1000, '参数错误');
if (common::isOpenId($openid) === false)	common::output(1000, 'openid参数错误');
if (!in_array($contractid, array('100616996T2201211130001')))	common::output(1000, 'contractid参数错误');

$verify = SnsSigCheck::verifySig('get', '/task/t1/check_completion.php', $_GET, $appkey.'&', $sig);
if (!$verify)	common::output(1, 'sig验证失败');

$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);

$sql = "SELECT * FROM player_task WHERE openid='$openid' AND contractid='$contractid' LIMIT 1";
$row = $dblink->getRow($sql);

$ret = 4;
$msg = 'Failed';
$zoneid = 0;

if ($row) {
    $zoneid = $row['zoneid'];
    $domain = 's'.$zoneid.'.app100616996.qqopenapp.com';

	if ($dbsetting['dbmaster']) {
		$sql = "SELECT b.name2 AS db_host,a.name,combined_to,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$domain',server)<>0 AND b.type=1 LIMIT 1";
	}else {
		$sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name,name FROM servers WHERE FIND_IN_SET('$domain',server) <> 0 LIMIT 1";
	}

    $serv = $dblink->getRow($sql);

    if ($serv) {
        $servername = '';

        if ($serv['combined_to'] > 0) {
            $servername = $serv['name'];

            $sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name FROM servers WHERE sid='".$serv['combined_to']."' LIMIT 1";
            $serv = $dblink->getRow($sql);
        }

        if (strpos($serv['db_host'], ':') !== false){
            $db_host = explode(':', $serv['db_host']);
            $serv['db_host'] = $db_host[0];
            $serv['db_port'] = $db_host[1];
        }

        $gamelink = new Sql($serv['db_host'], $serv['db_root'], $serv['db_pwd'], $serv['db_name'], $serv['db_port']);
        $oldopenid = $openid;
        if (!empty($servername)) {
            $servername = explode('_', $servername);
            $openid = $openid.'.'.$servername[1];
        }

        $sql = "SELECT a.id, username, level FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND main_role_id=b.id WHERE username='$openid' LIMIT 1";
        $player = $gamelink->getRow($sql);
        if ($player['level'] >= 15) {
            $ret = 0;
            $msg = 'OK';

            if ($row['status'] != 1) {
                $sql = "UPDATE player_task SET status=1 WHERE openid='$oldopenid' AND contractid='$contractid' AND zoneid='$zoneid' LIMIT 1";
                $dblink->query($sql);
            }
        }

        $gamelink->close();
    }
}

$dblink->close();

common::output($ret, $msg, array('zoneid' => 0));
