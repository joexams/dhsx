<?php
header("content-Type: text/html; charset=utf-8");

set_time_limit(0);

require_once 'clear_conf.php';
require_once 'maps.php';

function clear($maps, $db, $server, $server_port, $user, $passwd, $clear_day, $clear_level, $clear_vip_level){
	//连接目标数据库
	$mysqli = new mysqli($server, $user, $passwd, $db, $server_port);
	$mysqli->query("SET NAMES utf8;");
	$mysqli->query("SET FOREIGN_KEY_CHECKS=0;");
	
	$Clear_Login_Time = time() - $clear_day * 24 * 3600;
	
	foreach($maps as $map){
		$db_table = $map[0];

		if (count($map) > 1) {
			echo "# Clearing $db_table...\n";
			
			if (count($map) > 2 && $map[2] == "clear") {
				$sql = "DELETE FROM `$db_table`;";
				$mysqli->query($sql);
			}
			else
			{
				$column_infos = $map[1]; 
			
				$has_player_id = false;
				
				$sql = "DELETE FROM `$db_table` WHERE ";
				
				if ($db_table == 'player')
					$sql .= "ISNULL(`main_role_id`) OR (`id` IN (SELECT `player_id` FROM `player_trace` WHERE `last_login_time` <= $Clear_Login_Time) AND `id` IN (SELECT `player_id` FROM `player_role` WHERE `level` < $clear_level) AND `vip_level` <= $clear_vip_level AND `id` NOT IN (SELECT `player_id` from `player_faction`))";
				else{
					$has_player_id = false;
					$ci=0;
					$has_player_id_sql = "";
					
					foreach($column_infos as $column_info) {
						$db_column = $column_info[0];
						$map_table = $column_info[1];
						$map_column = $column_info[2];

						if ($map_table == 'player' && $map_column == 'id'){
							$has_player_id = true;
							if ($ci > 0)
								$has_player_id_sql .= ' AND ';
							$has_player_id_sql .= "(`$db_column` <> 0 AND `$db_column` IN (SELECT `id` FROM `player` WHERE ISNULL(`main_role_id`))) OR (`$db_column` <> 0 AND `$db_column` IN (SELECT `player_id` FROM `player_trace` WHERE `last_login_time` <= $Clear_Login_Time) AND `$db_column` IN (SELECT `id` FROM `player` WHERE `vip_level` <= $clear_vip_level) AND `$db_column` IN (SELECT `player_id` FROM `player_role` WHERE `level` < $clear_level) AND `$db_column` NOT IN (SELECT `player_id` from `player_faction`))";
							$ci++;
						}
					}
						
					if ($has_player_id == true) {
						$sql .= $has_player_id_sql;
					}
				}
	
				$sql .= ";";
				//echo "### SQL:$sql\n";
				$mysqli->query($sql);
				
				foreach($column_infos as $column_info) {
					$db_column = $column_info[0];
					$map_table = $column_info[1];
					$map_column = $column_info[2];
					
					$c_sql = "DELETE FROM `$db_table` WHERE `$db_column` NOT IN (SELECT `$map_column` FROM `$map_table`);";
					//echo "### SQL:$c_sql\n";
					$mysqli->query($c_sql);
				}
			}			
		}
		
		echo "\n";
	}
	
	//检查清理后的数据
	echo ("Checking data...\n");
	foreach($maps as $map){
		$db_table = $map[0];
		if (count($map) > 1) {
			$column_infos = $map[1];
			foreach($column_infos as $column_info) {
				$db_column = $column_info[0];
				$map_table = $column_info[1];
				$map_column = $column_info[2];
				$chk_data_result = $mysqli->query("SELECT COUNT(*) FROM `$db_table` WHERE `$db_column` != 0 AND `$db_column` NOT IN (SELECT `$map_column` FROM `$map_table`) LIMIT 1;");
				if ($chk_data_result) {
					$chk_data_result_arr = mysqli_fetch_array($chk_data_result, MYSQLI_NUM);
					$BadDataCount = $chk_data_result_arr[0];
					if($BadDataCount > 0)
						echo ("!!Warning: $db_table.$db_column --- $map_table.$map_column has $BadDataCount bad datas!\n");
					
					$chk_data_result->close();
				}
			}
		}
	}
	
	//关闭连接
	$mysqli->close();
	echo "Done~\n";
}

clear($DATA_MAPS, $Db, $DbServer, $DbServerPort, $DbUser, $DbPwd, $Clear_Day, $Clear_Level, $Clear_VIP_Level);

?>