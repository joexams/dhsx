<?php
require('common.php');
require('..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'SnsNetwork.php');
$usrl  = 'http://union.tencentlog.com/cgi-bin/Register.cgi';
$appid = '100616996';
$svrip = '10.190.233.245';
$svrip = sprintf("%u", ip2long($svrip));

$serverid = intval($_GET['serverid']);
$openid   = intval($_GET['openid']);
if (common::isOpenId($openid) === false)	common::output(1000, 'openid invalid');

$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);

$domain = 's'.$serverid.'.app100616996.qqopenapp.com';
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

    $sql = "SELECT id,username FROM player WHERE username='$openid' LIMIT 1";
    $player = $gamelink->getRow($sql);
    if (!$player)	common::output(1001, 'Player No exists');

    $opuid = $player['id'];
    if (empty($servername)) {
    	$opuid = $opuid * 1000000 + $opuid;
    }
}else {
	common::output(1002, 'Player No exists');
}


$userip= getIp();
$userip= sprintf("%u", ip2long($userip));

$params	= array();
$params['appid']	= '100616996';
$params['version']	= '1';
$params['userip']	= $userip;
$params['svrip']	= $svrip;
$params['time']		= time();
$params['worldid']	= $serverid;
$params['opuid']	= $opuid;
$params['opopenid'] = $_GET['openid'];
$params['pf'] 		= $_GET['pf'];
$params['openkey']	= $_GET['openkey'];
$params['pfkey']	= $_GET['pfkey'];

$ret = SnsNetwork::makeRequest($url, $params, array(), 'get');
$result = json_decode($ret['msg'], true);
if (intval($result['iRet']) != 0) {
	$filepath = './log/';
	$filename = date('Ymd').'.php';
	$current  = '<?php exit;?>'."\t".date('Y-m-d H:i:s')."\t".$result['iRet']."\t".$result['sMsg']."\t".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').PHP_EOL;
	error_log($current, 3, $filepath.$filename);
}

common::output($result['iRet'], $result['sMsg']);


function getIp() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}