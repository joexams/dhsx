<?php
defined('IN_CMD') or exit('No permission resources.');
if (date('Y-m-d')<>'2014-12-01') die();
$starttime = strtotime("2014-11-29");
$endtime = strtotime("2014-11-30");
@header("Content-Type: application/json; charset=utf-8");
$pubdb = common::load_model('public_model');
$i = 0;
//$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where open=1 AND open_date<now() and combined_to=0 and cid=1 and test=0 and sid>4744 and is_use=1";
$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where open=1 AND open_date<now() and combined_to=0 and cid=1 and test=0 and sid=5889 and is_use=1";
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
		$sql = "select player_id from player_wish_tree where wish_time<1417276800 and red_bless_bag>9 and player_id=3262 order by red_bless_bag desc";
		$list = $getdb->get_list($sql);
		foreach ($list as $pkey => $pvalue){
			$item_list = array();
			$player_id = $pvalue['player_id'];
			if ($player_id>0){
				$item_list = array(array('item_id'=>347,'number'=>300));
				$message = '恭喜你获得许愿树礼包';
				$info = $api_admin::add_player_gift_data($player_id,16,0,30000000,1252,$message,$item_list);
				$i++;
			}
		}
	}
}
echo $i;