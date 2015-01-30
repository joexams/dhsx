<?php
header("content-Type: text/html; charset=utf-8");

set_time_limit(0);

require_once 'conf.php';
require_once 'maps.php';
echo "$DbServer, $DbUser, $DbPwd, $Db, $DbServerPort";
	$mysqli = new mysqli($DbServer, $DbUser, $DbPwd, $Db, $DbServerPort);
	$mysqli->query("SET NAMES utf8;");
	$mysqli->query("SET FOREIGN_KEY_CHECKS=0;");
	//初始化合服信息列表
	$mysqli_list = array(); //合服数据库的连接集
	$server_list = array(); //合服数据库的服务器地址集
	$port_list = array(); //合服数据库的服务器端口集
	$db_name_list = array(); //合服数据库的名称集
	$suffix_list = array(); //合服的后缀集
	$server_day_list = array(); //相对于合服第一个服的间隔时间集
	$server_number	= array();	//合服的player_id基数
	foreach($DbList as $dbinfo){
		$db_server = $dbinfo[0];
		$db_server_port = $dbinfo[1];
		$db_user = $dbinfo[2];
		$db_passwd = $dbinfo[3];
		$db_name = $dbinfo[4];
		$db_suffix = $dbinfo[5];
		$srv_day = $dbinfo[6];
		$db_server_number = $dbinfo[7];
		$db_mysqli = new mysqli($db_server, $db_user, $db_passwd, $db_name, $db_server_port);
		$db_mysqli->query("SET NAMES utf8;");
		
		array_push($mysqli_list, $db_mysqli);
		array_push($server_list, $db_server);
		array_push($port_list, $db_server_port);
		array_push($db_name_list, $db_name);
		array_push($suffix_list, $db_suffix);
		array_push($server_day_list, $srv_day);
		array_push($server_number, $db_server_number);
	}
	//数据库版本检查，必须要一致，才可以进行合服****************************************
	echo ("Checking db data...\n");
	foreach($DATA_MAPS as $map){
		$number_sub = 0;
		$db_table = $map[0];
		$v_result = $mysqli->query("SELECT count(*) FROM $db_table;");
		if ($v_result){
			$result_array = $v_result->fetch_array(MYSQLI_NUM);
			$number = $result_array[0];
			$v_result->close();
		}
		else{
			echo ("!!!Error : Unknown db data!\n");
			return;
		}
		foreach($mysqli_list as $db_mysqli){
			$v_result = $db_mysqli->query("SELECT count(*) FROM $db_table;");
			if ($v_result){
				$result_array = $v_result->fetch_array(MYSQLI_NUM);
				$number1 = $result_array[0];
				$number_sub += $number1;
				$v_result->close();
			}
		}
		if ($number != $number_sub){
			echo ("!!!!!!!!!!!!!!!!!$db_table ERR: combined:$number   sub:$number_sub!!!!!!!!!!!!!!!!!!!!!!!");
		}
	}