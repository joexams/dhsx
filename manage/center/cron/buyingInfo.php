<?php
defined('IN_CMD') or exit('No permission resources.');
if (date('Y-m-d')>'2015-01-26') die();
@header("Content-Type: application/json; charset=utf-8");
$host = "10.190.237.135";
$port = "8810";
$root = "root";
$pwd = "YuUkD<%PsB(0u]!x";
$db_name = "gamedb_lucky_wheel_server";
$gamelink = new mysqli($host, $root, $pwd, $db_name, $port);
//$gamelink = new mysqli("192.168.6.241", 'root', 'ybybyb', 'server_lottery', '3306');
if ($gamelink->connect_error) continue;
$gamelink->query('SET NAMES "utf8"');
$yesterday = strtotime(date('Y-m-d',strtotime("-1 day")));
//$yesterday = strtotime(date('Y-m-d'));
$pubdb = common::load_model('public_model');
$sql = "select a.tb_id,a.number,b.`price`,b.`name` from player_server_team_buying a,server_team_buying b where a.`time`='$yesterday' and a.tb_id=b.id";
$result = $gamelink->query($sql);
$tblist = array();$tbname_list = array();
while ($row = $result->fetch_assoc()){
    $tb_id = $row['tb_id'];
    $number = $row['number'];
    $price = $row['price'];
    $tbname_list[$tb_id] = $row['name'];
	if ($number>=1000){
		$tblist[$tb_id] = (0.8-0.5)*$price;
	}elseif ($number>=800 and $number<1000){
		$tblist[$tb_id] = (0.8-0.55)*$price;
	}elseif ($number>=500 and $number<800){
		$tblist[$tb_id] = (0.8-0.6)*$price;
	}elseif ($number>=300 and $number<500){
		$tblist[$tb_id] = (0.8-0.65)*$price;
	}elseif ($number>=100 and $number<300){
		$tblist[$tb_id] = (0.8-0.7)*$price;
	}else{
		$tblist[$tb_id] = (0.8-0.8)*$price;
	}
}
$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where open=1 AND open_date<now() and combined_to=0 and cid=1";
//$sersql = "select sid,name,o_name, is_combined,api_server,api_port,api_pwd,server_ver from servers where sid=4745";
		$serverlist = $pubdb->get_list($sersql);
		foreach ($serverlist as $key => $value){
			$sid = $value['sid'];
			$getdb = $pubdb->set_db($sid);
			if ($getdb !== false){
				if (!empty($value['api_server']) && !empty($value['api_port']) && !empty($value['api_pwd']) && !empty($value['server_ver'])){
					$version = trim($value['server_ver']);
					$api_admin = common::load_api_class('api_admin', $version);
					if ($api_admin !== false && method_exists($api_admin, 'team_buying_activity_award')){
						$api_admin::$SERVER    = $value['api_server'];
						$api_admin::$PORT      = $value['api_port'];
						$api_admin::$ADMIN_PWD = $value['api_pwd'];
					}
			}
				$sql = "select id,tb_id,player_id from player_server_team_buying_data where `time`='$yesterday' and is_back=0";
//				$sql = "select id,tb_id,player_id from player_server_team_buying_data where `time`='$yesterday' and is_back=0 and player_id=134474";
				$list = $getdb->get_list($sql);
				$player_list = '';
				foreach ($list as $pkey => $pvalue){
					$player_id = $pvalue['player_id'];
					$id = $pvalue['id'];
					$ingot = $tblist[$pvalue['tb_id']];
					if ($ingot>0){
						$message = '恭喜你获得团购['.$tbname_list[$pvalue["tb_id"]].']差价回馈';
						$info = $api_admin::team_buying_activity_award($player_id,$id,$ingot,$message);
					}
				}
			}
		}