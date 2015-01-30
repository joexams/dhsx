<?php
require('common.php');
require('..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'SnsSigCheck.php');

$openid = isset($argv[1]) ? $argv[1] : '';
$contractid = isset($argv[2]) ? $argv[2] : '';
if (common::isOpenId($openid) === false)	common::output(1000, 'openid参数错误');
//if ('' != $contractid)	common::output(1000, 'contractid参数错误');


$dblink = new Sql($dbsetting['dbhost'], $dbsetting['dbuser'], $dbsetting['dbpwd'], $dbsetting['dbname'], $dbsetting['dbport']);

$sql = "SELECT * FROM player_task WHERE openid='$openid' AND contractid='$contractid' AND status=1 LIMIT 1";
$row = $dblink->getRow($sql);

$ret = 4;
$msg = 'Failed';
if ($row) {
    $zoneid = $row['zoneid'];
    $domain = 's'.$zoneid.'.app100616996.qqopenapp.com';
    if ($dbsetting['dbmaster']) {
        $sql = "SELECT b.name2 AS db_host,a.name,combined_to,db_root,db_pwd,db_name,api_server,api_port,api_pwd,server_ver FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$domain',server)<>0 AND b.type=1 LIMIT 1";
    }else {
        $sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name,name,api_server,api_port,api_pwd,server_ver FROM servers WHERE FIND_IN_SET('$domain',server) <> 0 LIMIT 1";
    }

    $serv = $dblink->getRow($sql);
    if ($serv) {
        $servername = '';

        if ($serv['combined_to'] > 0) {
            $servername = $serv['name'];
            
            $sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name,api_server,api_port,api_pwd,server_ver FROM servers WHERE sid='".$serv['combined_to']."' LIMIT 1";
            $serv = $dblink->getRow($sql);
        }

        if (strpos($serv['db_host'], ':') !== false){
            $db_host = explode(':', $serv['db_host']);
            $serv['db_host'] = $db_host[0];
            $serv['db_port'] = $db_host[1];
        }

        if (!empty($servername)) {
            $servername = explode('_', $servername);
            $openid = $openid.'.'.$servername[1];
        }

	$api_admin = common::load_api_class('api_admin', $serv['server_ver'], 1);
        if ($api_admin !== false) {
            $api_admin::$SERVER    = $serv['api_server'];
            $api_admin::$PORT      = $serv['api_port'];
            $api_admin::$ADMIN_PWD = $serv['api_pwd'];

            $callback = $api_admin::find_player_by_username($openid);
            if ($callback['result'] == 1) {

                $items = array(
                    array('item_id'=>1131, 'level'=>1, 'number'=>1),
                    array('item_id'=>1369, 'level'=>1, 'number'=>1),
                    array('item_id'=>1215, 'level'=>1, 'number'=>1),
                );
                $player_id = $callback['player_id'];
                $rtn = api_admin::add_player_super_gift($player_id, 33, 0, 100000, 0, 0, 1493, '恭喜您获得应用中心任务集市588大礼包！', $items, array(), array());
		if ($rtn['result'] == 1) {
                    $ret = 0;
                    $msg = 'OK';
                }else {
		    $msg = 'player no exists';
		}
            }else {
                $ret = 1000;
                $msg = '玩家不存在';
            }
        }
    }
}

$dblink->close();

common::output($ret, $msg);
