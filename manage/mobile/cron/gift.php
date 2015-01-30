<?php
defined('IN_CMD') or exit('No permission resources.');
if (date('Y-m-d')>'2014-11-15') die();
$pubdb = common::load_model('public_model');
$i = 0;
$sersql = "select sid,name,api_server,api_port,api_pwd,server_ver from servers where sid=6193";
$serverlist = $pubdb->get_list($sersql);
foreach ($serverlist as $key => $value){
	$sid = $value['sid'];
	$getdb = $pubdb->set_db($sid);
	if ($getdb !== false){
		if (!empty($value['api_server']) && !empty($value['api_port']) && !empty($value['api_pwd']) && !empty($value['server_ver'])){
			$version = trim($value['server_ver']);
			$api_admin = common::load_api_class('api_admin', $version);
			if ($api_admin !== false && method_exists($api_admin, 'add_player_gift_data')){
				$api_admin::$SERVER    = $value['api_server'];
				$api_admin::$PORT      = $value['api_port'];
				$api_admin::$ADMIN_PWD = $value['api_pwd'];
			}
		}
		$sql = "select id from player where vip_level>4";
		$list = $getdb->get_list($sql);
		$item_list = array(array('item_id'=>2105,'number'=>10),array('item_id'=>2192,'number'=>1));
		foreach ($list as $pkey => $pvalue){
			$player_id = $pvalue['id'];
			$message = '恭喜你获得活动V587奖励';
			//$info = $api_admin::add_player_gift_data($player_id,16,0,0,1252,$message,$item_list);
			$i++;
		}
	}
}
echo $i;