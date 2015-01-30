<?php
header("content-Type: text/html; charset=utf-8");

set_time_limit(0);

require_once 'conf.php';
require_once 'maps.php';


//获取当前秒数
function get_microtime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//输出SQL语句
function printx($sql, $show_sql) {
	if ($show_sql){
		echo ("###### SQL Query: $sql \n");
	}
}

/****************************************
//获取指定编号的服的表字段合并后要叠加最大值
//$index:服编号
//$table:表, $column:字段
//$mysqli_list:要合并的各个服的mysqli对象
//$max_id_list:已经存储的叠加值列表
******************************************/
function get_max_id($index, $table, $column, $mysqli_list, &$max_id_list, $server_number) {
	$key = "s$index.$table.$column";
	if (array_key_exists($key, $max_id_list)) //如果已经获取过此值,直接返回
		return $max_id_list[$key];	
	if ($table == 'player' && $column == 'id'){
		$Count = count($server_number);
		if($index >= $Count){//其实是最大值
			$new_index = $Count - 1;
			$mysqli = $mysqli_list[$new_index];
			$result = $mysqli->query("SELECT MAX(`$column`) FROM `$table` LIMIT 1;", MYSQLI_USE_RESULT);
			$top_id = 0;
			if ($result) {
				$result_array = $result->fetch_array(MYSQLI_NUM);
				$top_id = $result_array[0]; //最大ID
				$result->close();
			}

			$max_id_list[$key] = $server_number["$new_index"] + 1 + $top_id;
		}else{
			$max_id_list[$key] = $server_number["$index"];
		}
		return $max_id_list["$key"];
	}
	if ($index < 1) //为0则为第一个服,不需要叠加值
		return 0;


	for($d = 0; $d < $index; $d++) {
		$mysqli = $mysqli_list[$d];
		$result = $mysqli->query("SELECT MAX(`$column`) FROM `$table` LIMIT 1;", MYSQLI_USE_RESULT);
		if ($result) {
			$result_array = $result->fetch_array(MYSQLI_NUM);
			$top_id = $result_array[0]; //最大ID
			if (is_null($top_id))
				continue;
			$result->close();
			
			$add_id += ($top_id + 1); //叠加
		}
	}
	
	$max_id_list[$key] = $add_id; //存储进列表
	return $add_id;
	
}

/****************************************
//获取合服的数据中指定表指定字段的最大值
//$query查询某个字段值的语句
//$mysqli_list:要合并的各个服的mysqli对象
******************************************/
function get_column_max_value($query, $mysqli_list) {
	$value = 0;
	foreach($mysqli_list as $mysqli){
		$result = $mysqli->query($query, MYSQLI_USE_RESULT);
		if ($result) {
			$result_array = $result->fetch_array(MYSQLI_NUM);
			$result_value = $result_array[0]; 
			if ($result_value > $value){
				$value = $result_value;
			}
			
			$result->close();
		}
	}
	return $value;
}

//按照一定的算法处理各服竞技排名
function get_sport_rank_sql_string($index, $f, $db_count){
	$w = $db_count - ($index + 1);
	$sql = "#$f# * $db_count - $w";
	return $sql;
}

//合服
function merge(
	$db, $server, $server_port, $user, $passwd, //合并的目标数据库连接信息
	$dblist, //要合并的数据连接信息列表
	$ignore_log, //是否忽略日志表
	$maps, $rank_tables, //合服映射表, 竞技场排名字段表
	$show_sql, //是否输出执行的SQL语句
	$copy_number, //要合并的数据量, 0:合并全部, >0:仅方便测试用
	$clear_day, $clear_level, //要清除的玩家,多少天未登陆且少于多少级别(VIP玩家不清除)
	$redeem_coins, $redeem_power, $max_redeem_days, $redeem_register_day, $redeem_must_coins, $redeem_must_power, //补偿内容
	$is_need_redeem //是否需要补偿
) {

	$db_count = count($dblist); //合服数量
	if($db_count < 2) { //要合并的服必须大于一个
		echo ("Error : Target databases number must more than 2!\n");
		return;
	}
	
	$now_time = get_microtime(); //开始时间

	//连接目标数据库
	echo "$server, $user, $passwd, $db, $server_port";
	$mysqli = new mysqli($server, $user, $passwd, $db, $server_port);
	$mysqli->query("SET NAMES utf8;");
	$mysqli->query("SET FOREIGN_KEY_CHECKS=0;");
	
	$Clear_Login_Time = time() - $clear_day * 24 * 3600; //最后登陆时间在这个时间之前的且小于指定级别的要清除
	$MAX_ID_LIST = array(); //字段叠加值存储列表
	
	//初始化合服信息列表
	$mysqli_list = array(); //合服数据库的连接集
	$server_list = array(); //合服数据库的服务器地址集
	$port_list = array(); //合服数据库的服务器端口集
	$db_name_list = array(); //合服数据库的名称集
	$suffix_list = array(); //合服的后缀集
	$server_day_list = array(); //相对于合服第一个服的间隔时间集
	$server_number	= array();	//合服的player_id基数
	foreach($dblist as $dbinfo){
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
	echo ("Checking db version...\n");
	$version = 0;
	$v_result = $mysqli->query("SELECT MAX(`version`) FROM `db_version` LIMIT 1;");
	if ($v_result){
		$result_array = $v_result->fetch_array(MYSQLI_NUM);
		$version = $result_array[0];
		$v_result->close();
	}
	else{
		echo ("!!!Error : Unknown db version!\n");
		return;
	}
	foreach($mysqli_list as $db_mysqli){
		$db_version = 0;
		$v_result = $db_mysqli->query("SELECT MAX(`version`) FROM `db_version` LIMIT 1;");
		if ($v_result){
			$result_array = $v_result->fetch_array(MYSQLI_NUM);
			$db_version = $result_array[0];
			$v_result->close();
			if ($version != $db_version){
				echo ("!!!Error : db version are diffirent!\n");
				return;
			}
		}
	}
	//数据库版本检查END******************************************************************
	
	//检查映射表是否有异常*********************************
	echo ("Checking Maps...\n");
	$checked_map_list = array();
	foreach($maps as $map){
		$db_table = $map[0];
		if (in_array($db_table, $checked_map_list)) {
			echo ("!!!Error: dunplicate map table - $db_table!\n");
			return;
		}
		
		$chk_db_result = $mysqli->query("SELECT * FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$db_table';");
		if ($chk_db_result) {
			$chk_db_result_array = $chk_db_result->fetch_array(MYSQLI_NUM);
			if(count($chk_db_result_array) > 0){
				if (count($map) > 1) {
					$column_infos = $map[1];
					foreach($column_infos as $column_info) {
						$db_column = $column_info[0];
						$map_table = $column_info[1];
						$map_column = $column_info[2];
						
						$chk_db_column_result = $mysqli->query("SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$db_table' AND `COLUMN_NAME` = '$db_column';");
						if ($chk_db_column_result) {
							$chk_db_column_result_array = $chk_db_column_result->fetch_array(MYSQLI_NUM);
							if(count($chk_db_column_result_array) > 0){
								$chk_map_column_result = $mysqli->query("SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$map_table' AND `COLUMN_NAME` = '$map_column';");
								if ($chk_map_column_result) {
									$chk_map_column_result_array = $chk_map_column_result->fetch_array(MYSQLI_NUM);
									if(count($chk_map_column_result_array) > 0){
										//nothing
									}
									else {
										echo ("!!!Error: Column - $map_table.$map_column is not existing!\n");
									}
								}
								else{
									echo ("!!!Error: Column - $map_table.$map_column is not existing!\n");
								}
							}
							else {
								echo ("!!!Error: Column - $db_table.$db_column is not existing!\n");
							}
							$chk_db_column_result->close();
						}
						else{
							echo ("!!!Error: Column - $db_table.$db_column is not existing!\n");
						}
					}
				}
			}
			else{
				echo ("!!!Error: Table - $db_table is not existing!\n");
				return;
			}
			$chk_db_result->close();
		}
		else{
			echo ("!!!Error: Table - $db_table is not existing!\n");
			return;
		}
		
		array_push($checked_map_list, $db_table);
	}
	foreach($rank_tables as $rank_table) {
		$db_table = $rank_table[0];
		$rank_column = $rank_table[1];
		$where_cols = $rank_table[2];
		$chk_db_result = $mysqli->query("SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$db_table' AND `COLUMN_NAME` = '$rank_column';");
		if ($chk_db_result) {
			$chk_db_result_array = $chk_db_result->fetch_array(MYSQLI_NUM);
			if (count($chk_db_result_array) > 0){
				foreach($where_cols as $where_col){
					$chk_col_result = $mysqli->query("SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$db_table' AND `COLUMN_NAME` = '$rank_column';");
					if ($chk_col_result){
						$chk_col_result_array = $chk_col_result->fetch_array(MYSQLI_NUM);
						if (count($chk_col_result_array) > 0){
							//nothing
						}
						else{
							echo ("!!!Error: Column - $db_table.$where_col is not existing!\n");
							return;
						}
					}
					else{
						echo ("!!!Error: Column - $db_table.$where_col is not existing!\n");
						return;
					}
				}
			}
			else{
				echo ("!!!Error: Column - $db_table.$rank_column is not existing!\n");
				return;
			}
			$chk_db_result->close();
		}
		else
		{
			echo ("!!!Error: Column - $db_table.$rank_column is not existing!\n");
			return;
		}
	}
	//检查映射表是否有异常END******************************************************************
	
	//根据映射表复制数据
	foreach($maps as $map){
		$db_table = $map[0];

		echo "# Merging $db_table...\n";
		
		if (count($map) > 1) {
			if (count($map) > 2 && $map[2] == "log" && $ignore_log == true) //日志表是否需要合并
				continue;
			if (count($map) > 2 && $map[2] == "clear") //部分表不需要合服
				continue;
			
			$column_infos = $map[1]; //要更新ID的字段 
			$is_need_clear = false; //是否清理多余数据(间接引用player表的会复制到废数据)
			
			echo ("### Initializing sql templates of $db_table...\n");
			
			$field_result = $mysqli->query("SELECT * FROM `$db_table` LIMIT 1;");
			$sql_templates = array(); //复制数据的SQL语句模板
			
			//初始化SQL语句模板
			for($s = 0; $s < $db_count; $s++){
				array_push($sql_templates, "INSERT INTO `$db_table` VALUES(");
			}	
			$f = 0;
			//处理SQL语句模板(对需要叠加的ID字段以及竞技场排名字段进行处理)
			while ($field = $field_result->fetch_field()) {
				if ($f > 0) {
					for($s = 0; $s < $db_count; $s++){
						$sql_templates[$s] .= ', ';
					}
				}
				
				$fname = $field->name;
				$is_do = 0;
				
				foreach($column_infos as $column_info) {
					$db_column = $column_info[0];
					$map_table = $column_info[1];
					$map_column = $column_info[2];
					
					if ($db_column == $fname) {				
						for($s = 0; $s < $db_count; $s++){
							$max_id = get_max_id($s, $map_table, $map_column, $mysqli_list, $MAX_ID_LIST, $server_number);
							if ($max_id < 1)
								$sql_templates[$s] .= "%$f%";
							else
								$sql_templates[$s] .= "IF(#$f# = 0, #$f#, #$f# + $max_id)";
						}
						$is_do = 1;
					}
				}
				
				foreach($rank_tables as $rank_table) {
					$r_table = $rank_table[0];
			
					if ($r_table == $db_table){
						$r_column = $rank_table[1];

						if($r_column == $fname){		
							for($s = 0; $s < $db_count; $s++){
								$rank = get_sport_rank_sql_string($s, $f, $db_count);
								$sql_templates[$s] .= $rank;
							}
							$is_do = 1;
						}
					}
				}
				
				if ($is_do == 0){
					for($s = 0; $s < $db_count; $s++){
						$sql_templates[$s] .= "%$f%";
					}
				}
				
				$f++;
			}	
			for($s = 0; $s < $db_count; $s++){
				$sql_templates[$s] .= ');';
			}
			
			//复制数据
			for($t = 0; $t < $db_count; $t++){
				$dti = $server_list[$t] . ":" . $port_list[$t] . " - " . $db_name_list[$t];
				echo ("### Copying $db_table from database $dti...\n");
			
				$sql_template = $sql_templates[$t];

				$db_mysqli = $mysqli_list[$t];
				$db__sql = "SELECT * FROM `$db_table`";
					
				if ($db_table == 'player')
					$db__sql .= " WHERE `vip_level` > 0 OR `id` IN (SELECT `player_id` from `player_faction`) OR (NOT ISNULL(`main_role_id`) AND (`id` IN (SELECT `player_id` FROM `player_role` WHERE `level` >= $clear_level) OR `id` IN (SELECT `player_id` FROM `player_trace` WHERE `last_login_time` > $Clear_Login_Time)))";
				else{
					$has_player_id_sql = " WHERE ";
					$has_player_id = false;
					$ci=0;
					foreach($column_infos as $column_info) {
						$db_column = $column_info[0];
						$map_table = $column_info[1];
						$map_column = $column_info[2];

						if ($map_table == 'player' && $map_column == 'id'){
							$has_player_id = true;
							if ($ci > 0)
								$has_player_id_sql .= ' AND ';
							$has_player_id_sql .= "(`$db_column` = 0 OR `$db_column` IN (SELECT `id` FROM `player`  WHERE `vip_level` > 0 OR `id` IN (SELECT `player_id` from `player_faction`) OR (NOT ISNULL(`main_role_id`) AND (`id` IN (SELECT `player_id` FROM `player_role` WHERE `level` >= $clear_level) OR `id` IN (SELECT `player_id` FROM `player_trace` WHERE `last_login_time` > $Clear_Login_Time)))))";
							$ci++;
						}
					}
						
					if ($has_player_id == true) {
						$db__sql .= $has_player_id_sql;
					}
					else {
						$is_need_clear = true;
					}
				}
				if ($copy_number == 0)
					$db__sql .= ';';
				else
					 $db__sql .= " LIMIT $copy_number;";
				printx($db__sql, true);
				echo("\n");
					
				$db_result = $db_mysqli->query($db__sql, MYSQLI_USE_RESULT);
				if (!$db_mysqli) {
				    die('Could not connect: ' . mysql_error());
				}
				if ($db_result) {
					while($db_row = mysqli_fetch_array($db_result, MYSQLI_NUM)){	
						$sql = $sql_template;
						for($n = 0; $n < count($db_row); $n++) {
							$db_column = $db_row[$n];
							if (is_null($db_column)){ 
								$sql = str_replace("%$n%", "NULL", $sql);
								$sql = str_replace("#$n#", "NULL", $sql);
							}
							else{
								if (is_string($db_column)){
									$db_column = $mysqli->real_escape_string($db_column);
								}
								$sql = str_replace("%$n%", "'$db_column'", $sql);
								$sql = str_replace("#$n#", "$db_column", $sql);
							}
						}
						printx($sql, $show_sql);
						$mysqli->query($sql);
					}
					$db_result->close();
				}
			}
			if ($is_need_clear == true) {
				foreach($column_infos as $column_info) {
					$db_column = $column_info[0];
					$map_table = $column_info[1];
					$map_column = $column_info[2];

					$mysqli->query("DELETE FROM `$db_table` WHERE `$db_column` NOT IN (SELECT `$map_column` FROM `$map_table`);");
				}
			}
			echo ("\n");
			
			
			//有名称需要特殊处理
			if (count($map) > 2){ 
				$new_field_infos = $map[2];
				$where_info = $new_field_infos[0];
				$where_column = $where_info[0];
				$where_tab = $where_info[1];
				$where_col = $where_info[2];
				
				$name_sqls = array();
				for($s = 0; $s < $db_count; $s++){
					array_push($name_sqls, "UPDATE `$db_table` SET ");
				}	

				//处理名称
				echo "### Updating Names of `$db_table`...\n";
				$f_i = 0;
				$f_s = 0;	
				foreach($new_field_infos as $new_field_info) {
					if ($f_i > 0) {
						$field_name = $new_field_info[0];
						$field_size = $new_field_info[1];
						$field_null = $new_field_info[2];
						$field_comment = $new_field_info[3];
						
						//更新表结构
						$modify_sql = "ALTER TABLE `$db_table` MODIFY `$field_name` VARCHAR($field_size) $field_null COMMENT '$field_comment';";	
						echo "### Modifying `$db_table`.`$field_name`: $modify_sql \n";
						$mysqli->query($modify_sql);
						echo "\n";
						
						$is_must_suffix = false;
						if (count($new_field_info) > 4)
							$is_must_suffix = true;
							
						if ($is_must_suffix == false) {
							$table_index_name = 'idx_' . $db_table . '_' . $field_name;
							//检查是否有名称字段索引与创建索引
							$chk_index_sql = "SELECT * FROM `information_schema`.`STATISTICS` WHERE `TABLE_SCHEMA` = '$db' AND `TABLE_NAME` = '$db_table' AND `INDEX_SCHEMA` = '$db' AND `INDEX_NAME` = '$table_index_name';";
							$chk_index_result = $mysqli->query($chk_index_sql);
							if ($chk_index_result) {
								$chk_index_arr = mysqli_fetch_array($chk_index_result, MYSQLI_NUM);
								if (count($chk_index_arr) > 0) {
									//nothing
								}
								else{
									$create_index_sql = "CREATE INDEX `$table_index_name` ON `$db_table`(`$field_name`);";
									echo "### Creating Index : $create_index_sql\n";
									$mysqli->query($create_index_sql);
								}
								$chk_index_result->close();
							}
							
							//仅重复名称才要添加后缀的字段处理
							$same_name_sql = "SELECT `t1`.`$where_column`, `t1`.`$field_name` FROM `$db_table` AS `t1`, `$db_table` AS `t2` WHERE `t1`.`$where_column` != `t2`.`$where_column` AND `t1`.`$field_name` = `t2`.`$field_name` AND NOT ISNULL(`t1`.`$field_name`) AND `t1`.`$field_name` != '';";

							$same_name_result = $mysqli->query($same_name_sql);
							if ($same_name_result){
								while($same_name_row = mysqli_fetch_array($same_name_result, MYSQLI_ASSOC)){
									for($s = 0; $s < $db_count; $s++){
										$where_max_id_start = get_max_id($s, $where_tab, $where_col, $mysqli_list, $MAX_ID_LIST, $server_number);
										$where_max_id_end = get_max_id($s + 1, $where_tab, $where_col, $mysqli_list, $MAX_ID_LIST, $server_number);
										$unique_id = $same_name_row[$where_column];
										
										if ($unique_id >= $where_max_id_start && $unique_id < $where_max_id_end) {
											$db_suffix = $suffix_list[$s]; 
											$new_name = $same_name_row[$field_name] . $db_suffix;
											$update_name_sql = "UPDATE `$db_table` SET `$field_name` = '$new_name' WHERE `$where_column` = $unique_id;";
											printx($update_name_sql, $show_sql);
											$mysqli->query($update_name_sql);
										}
										else
											continue;
									}
								}
								$same_name_result->close();
							}
						}
						else { //一定要添加后缀的名称字段处理
							if ($f_s > 0) {
								for($s = 0; $s < $db_count; $s++){
									$name_sqls[$s] .= ', ';
								}
							}
							for($s = 0; $s < $db_count; $s++){
								$db_suffix = $suffix_list[$s];
								$name_sqls[$s] .= "`$field_name` = CONCAT(`$field_name`, '$db_suffix')";
							}
							$f_s++;
						}
					}
					
					$f_i++;
				}
	
				for($s = 0; $s < $db_count; $s++){
					$where_max_id_start = get_max_id($s, $where_tab, $where_col, $mysqli_list, $MAX_ID_LIST, $server_number);
					$where_max_id_end = get_max_id($s + 1, $where_tab, $where_col, $mysqli_list, $MAX_ID_LIST, $server_number);
					$name_sqls[$s] .= " WHERE `$where_column` >= $where_max_id_start AND `$where_column` < $where_max_id_end;";
					
					printx($name_sqls[$s], $show_sql);
					
					$mysqli->query($name_sqls[$s]);
				}
					
				echo "\n";
			}
			
		}
		
		else{ 
			//只需要复制第一个服的数据的表********************************
			echo ("### Copying $db_table...\n");
			$db1st_mysqli = $mysqli_list[0];
			$db1st_sql = "";
			if ($copy_number == 0)
				$db1st_sql = "SELECT * FROM `$db_table`;";
			else
				$db1st_sql = "SELECT * FROM `$db_table` LIMIT $copy_number;";
			$db1st_result = $db1st_mysqli->query($db1st_sql, MYSQLI_USE_RESULT);
			if ($db1st_result){
				while($db1st_row = mysqli_fetch_array($db1st_result, MYSQLI_NUM)){
					$data_sql = "INSERT INTO `$db_table` VALUES(";
		
					for($i = 0; $i < count($db1st_row); $i++) {
						if ($i > 0)
							$data_sql .= ", ";
						
						$db1st_column =	$db1st_row[$i];
						if (is_null($db1st_column)){
							$data_sql .= "NULL";
						}
						else{
							$data_sql .= "'";
							$data_sql .= $db1st_column;
							$data_sql .= "'";
						}
					}
					$data_sql .= ");";
					printx($data_sql, $show_sql);
					$mysqli->query($data_sql);
				}
				$db1st_result->close();
			}
			echo ("\n");
			//只需要复制第一个服的数据的表END********************************
		}
		
		echo ("\n");
	}
	
	//竞技场排名修正(合并的各个服玩家人数不一样，这里需要修正排名中的间隙)********************
	echo "### Fixing Super Sport Ranking...\n";
	foreach($rank_tables as $rank_table) {
		$r_table = $rank_table[0];
		$r_column = $rank_table[1];
		$where_cols = $rank_table[2];

		$ranking_sql = "SELECT * FROM `$r_table` ORDER BY `$r_column` ASC;";
		$ranking_result = $mysqli->query($ranking_sql);
		if ($ranking_result) {
			$previous_rank = 0;
			while($ranking_row = mysqli_fetch_array($ranking_result, MYSQLI_ASSOC)){
				$the_rank = $ranking_row[$r_column];
				$chk_rank = $previous_rank + 1;
				if ($chk_rank < $the_rank){
					$update_sql = "UPDATE `$r_table` SET `$r_column` = '$chk_rank' WHERE ";
					for($w = 0; $w < count($where_cols); $w++){
						$where_col = $where_cols[$w];
						if (array_key_exists($where_col, $ranking_row)) {
							if ($w > 0)
								$update_sql .= ' AND ';
							$where_value = $ranking_row[$where_col];
							$update_sql .= "`$where_col` = '$where_value'";
						}
					}
					printx($update_sql, $show_sql);
					$mysqli->query($update_sql);
					$previous_rank = $chk_rank;
				}
				else{
					$previous_rank = $the_rank;
				}
			}
			$ranking_result->close();
		}
	}
	//竞技场排名修正END******************************************************************************************
	
	
	//字段字符串玩家ID/特殊玩家ID修正*********************************
	$ids_sql1 = "UPDATE `player_listener_count` SET `last_contact_list` = '';";
	$ids_sql2 = "UPDATE `player_faction_war_gift` SET `award_player_ids` = '';";
	printx($ids_sql1, $show_sql);
	$mysqli->query($ids_sql1);
	printx($ids_sql2, $show_sql);
	$mysqli->query($ids_sql2);
	$ids_sql3 = "UPDATE `player_server_data` SET `data` = 0 WHERE `id` in (3, 4, 5, 6, 7);";
	printx($ids_sql3, $show_sql);
	$mysqli->query($ids_sql3);
	//字段字符串玩家ID/特殊玩家ID修正END******************************************************************
	
	//冗余总数修正*********************************
	$listener_count_sql = "UPDATE `player_listener_count` AS pl SET `count` = (SELECT COUNT(1) FROM `player_friends` AS pf WHERE pf.`friend_id` = pl.`player_id` AND pf.`group_type` = 1);";
	printx($listener_count_sql, $show_sql);
	$mysqli->query($listener_count_sql);
	$faction_member_count_sql = "UPDATE `player_faction` AS f SET `member_count` = (SELECT COUNT(1) FROM `player_faction_member` AS m WHERE m.`faction_id` = f.`id`);";
	printx($faction_member_count_sql, $show_sql);
	$mysqli->query($faction_member_count_sql);
	$message_count_sql = "UPDATE `player_friends` SET `message_count` = 0;";
	printx($message_count_sql, $show_sql);
	$mysqli->query($message_count_sql);
	//冗余总数修正END***************************************************************************************************
	
	//player_server_data表特殊处理***********************************
	echo "### Handing `player_server_data`...\n";
	$mysqli->query("UPDATE `player_server_data` SET `data` = 0 WHERE `id` in(8);"); //处理ID=8,20,25的数据
	
	//player_send_flower_data表特殊处理***********************************
	echo "### Handing `player_send_flower_data`...\n";
	$mysqli->query("UPDATE `player_send_flower_data` SET `max_send_flower_player_id` = 0;");
    //    $mysqli->query("UPDATE `player_server_data` SET `data` = 1 WHERE `id` in(51);");      //处理ID=51 
	//处理ID=22与23的数据
	/*$second_server_player_id = get_max_id(1, 'player', 'id', $mysqli_list, $MAX_ID_LIST);
	$second_mysqli = $mysqli_list[1];
	$second_player_server_data_result = $second_mysqli->query("SELECT * FROM `player_server_data` WHERE `id` = 22;");
	$second_player_server_data_id22 = 0;
	$second_player_server_data_str_id22 = "";
	if ($second_player_server_data_result){
		$second_player_server_data_array = $second_player_server_data_result->fetch_array(MYSQLI_NUM);
		$second_player_server_data_id22 = $second_player_server_data_array[1];
		$second_player_server_data_str_id22 = $second_player_server_data_array[2];

		$second_player_server_data_result->close();
	}
	$second_player_server_data_id22_for_save = $second_player_server_data_id22;
	if ($second_player_server_data_id22 > 0){
		$second_player_server_data_id22_for_save = $second_player_server_data_id22 + $second_server_player_id;
	}
	$mysqli->query("DELETE FROM `player_server_data` WHERE `id` = 23;");
	$mysqli->query("INSERT INTO `player_server_data`(`id`, `data`, `string_data`) VALUES(23, $second_player_server_data_id22_for_save, '$second_player_server_data_str_id22');");
	
	//记录最大玩家ID的player_server_data数据
	$max_player_level_value = get_column_max_value("SELECT `data` FROM `player_server_data` WHERE `id` = 24;", $mysqli_list);
	$mysqli->query("DELETE FROM `player_server_data` WHERE `id` = 24;");
	$mysqli->query("INSERT INTO `player_server_data`(`id`, `data`, `string_data`) VALUES(24, $max_player_level_value, NULL);");
	*/
	//player_server_data表特殊处理END**********************************************************************
	
	//补偿***********************************
	if ($is_need_redeem == true){
		echo "### Redeeming...\n";
		for($t = 0; $t < $db_count; $t++){
			$server_day = $server_day_list[$t];
			$srv_redeem_coins = $server_day * $redeem_coins;
			$srv_redeem_power = $server_day * $redeem_power;
			$max_redeem_coins = $max_redeem_days * $redeem_coins;
			$max_redeem_power = $max_redeem_days * $redeem_power;
			if ($srv_redeem_coins > $max_redeem_coins)
				$srv_redeem_coins = $max_redeem_coins;
			if ($srv_redeem_power > $max_redeem_power)
				$srv_redeem_power = $max_redeem_power;
			$redeem_register_time = time() - $redeem_register_day * 24 * 3600;
			$max_player_id_start = get_max_id($t, "player", "id", $mysqli_list, $MAX_ID_LIST, $server_number);
			$max_player_id_end = get_max_id($t + 1, "player", "id", $mysqli_list, $MAX_ID_LIST, $server_number);
			$redeem_sql = "UPDATE `player_data` SET `coins` = `coins` + $srv_redeem_coins, `power` = `power` + $srv_redeem_power WHERE `player_id` >= $max_player_id_start AND `player_id` < $max_player_id_end AND `player_id` IN (SELECT `player_id` FROM `player_trace` WHERE `first_login_time` <= $redeem_register_time);";
			
			printx($redeem_sql, $show_sql);
			$mysqli->query($redeem_sql);
		}
		
		$must_redeem_sql = "UPDATE `player_data` SET `coins` = `coins` + $redeem_must_coins, `power` = `power` + $redeem_must_power WHERE `player_id` IN (SELECT `player_id` FROM `player_trace` WHERE `first_login_time` <= $redeem_register_time);";
		printx($must_redeem_sql, $show_sql);
		$mysqli->query($must_redeem_sql);
	}
	//补偿END*********************************************************************************************************
	
	//检查合并后的数据***********************************
	echo ("Checking data...\n");
	foreach($maps as $map){
		$db_table = $map[0];
		if (count($map) > 1) {
			$column_infos = $map[1];
			if (count($map) > 2 && $map[2] == "log" && $ignore_log == true) //日志表是否需要合并
				continue;
			foreach($column_infos as $column_info) {
				$db_column = $column_info[0];
				$map_table = $column_info[1];
				$map_column = $column_info[2];
				$chk_data_result = $mysqli->query("SELECT * FROM `$db_table` WHERE `$db_column` != 0 AND `$db_column` NOT IN (SELECT `$map_column` FROM `$map_table`) LIMIT 1;");
				if ($chk_data_result) {
					$chk_data_result_arr = mysqli_fetch_array($chk_data_result, MYSQLI_NUM);
					if (count($chk_data_result_arr) > 0) {
						echo ("!!Warning: db_table - $db_table, db_column - $db_column --- map_table - $map_table, map_column - $map_column has bad data!\n");
					}
					$chk_data_result->close();
				}
			}
		}
	}
	//检查合并后的数据END**********************************************************************
	
	//关闭连接
	foreach($mysqli_list as $the_mysqli){
		$the_mysqli->close();
	}
	$mysqli->close();
	
	$over_time = get_microtime(); //结束时间
	$process_time = $over_time - $now_time;
	
	echo "\nServer ";
	for($t = 0; $t < $db_count; $t++){
		$spdb = $server_list[$t] . ":" . $port_list[$t] . " - " . $db_name_list[$t];
		if($t > 0)
			echo ', ';
		echo $spdb;
	}
	echo " has been merged!\n";
	echo "Player last login before $Clear_Login_Time seconds has been cleared!\n";
	echo "%% Done~\n%% Processed in $process_time second(s)~\n";
}


merge($Db, $DbServer, $DbServerPort, $DbUser, $DbPwd, $DbList, $IgnoreLog, $DATA_MAPS, $RANK_MAPS, $ShowSql, $CopyNumber, $Clear_Day, $Clear_Level, $Redeem_Coins, $Redeem_Power, $Max_Redeem_Days, $Redeem_Register_Day, $Redeem_Must_Coins, $Redeem_Must_Power, $Is_Need_Redeem);
?>
