<?php
defined('IN_CMD') or exit('No permission resources.');
@header("Content-Type: application/json; charset=utf-8");
$starttime = strtotime('2014-10-16 00:00:00');
$endtime = strtotime('2014-10-18 23:59:59');
$pubdb = common::load_model('public_model');
$i = 0;
$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where open=1 AND open_date<now() and combined_to=0 and cid=1 and sid between 6079 and 6084";
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
				$sql = "select player_id, sum(value+change_charge_value) as num from player_ingot_change_record where change_time>$starttime and change_time<$endtime and type=35 group by player_id";
				$list = $getdb->get_list($sql);
				$player_list = '';
				foreach ($list as $pkey => $pvalue){
					$player_id = $pvalue['player_id'];
					$num = $pvalue['num'];
					$ingot = $num*0.2;
					if ($ingot>0){
						$i++;
						$message = '恭喜你获得合服后三天内充值大返利礼包';
						$info = $api_admin::add_player_gift_data($player_id,16,$ingot,0,1252,$message,array());
					}
				}
			}
		}
		echo $i;