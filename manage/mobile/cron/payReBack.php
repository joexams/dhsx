<?php
defined('IN_CMD') or exit('No permission resources.');
if (date('Y-m-d')>'2014-11-17') die();
$starttime = strtotime("2014-11-15");
$endtime = strtotime("2014-11-16");
@header("Content-Type: application/json; charset=utf-8");
$pubdb = common::load_model('public_model');
$i = 0;
$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where open=1 AND open_date<now() and combined_to=0 and cid=1 and test=0 and sid>4745";
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
		$sql = "select player_id,sum(value+change_charge_value) num from player_ingot_change_record where type=35 and change_time>$starttime and change_time<$endtime group by player_id";
		$list = $getdb->get_list($sql);
		foreach ($list as $pkey => $pvalue){
			$item_list = array();
			$player_id = $pvalue['player_id'];
			$num = $pvalue['num'];
			if ($num>=10000){
				$item_list = array(array('item_id'=>2182,'number'=>30),array('item_id'=>2048,'number'=>120),array('item_id'=>2202,'number'=>3),array('item_id'=>347,'number'=>300),array('item_id'=>2109,'number'=>10));
			}
			if ($num >= 10000){
				$message = '恭喜你获得充值回馈礼包';
				$info = $api_admin::add_player_gift_data($player_id,16,0,0,1252,$message,$item_list);
				$i++;
			}
		}
	}
}
echo $i;