<?php
$pass = isset($argv[1]) ? trim($argv[1]) : null;
if ($pass != 'cron') {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(dirname(dirname(__FILE__))."/o.config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
$crontime = date('Y-m-d H:i:s');
$yesterday = date('Y-m-d',time()-86400);//昨天数据
$yesterday2 = date('Y-m-d',strtotime($yesterday)-86400);//前天数据
$yesterday2_s = strtotime(date('Y-m-d 00:00:00',strtotime($yesterday)-86400));//前天数据
$yesterday2_e = strtotime(date('Y-m-d 23:59:59',strtotime($yesterday)-86400));//前天数据
$yesterday_s = strtotime(date('Y-m-d 00:00:00',time()-86400));//昨天数据
$yesterday_e = strtotime(date('Y-m-d 23:59:59',time()-86400));//昨天数据
$yesterday_ee = date('Y-m-d 23:59:59',time()-86400);//昨天数据
$adminWebType = 's';//用于打印MYSQL错误
//$db->query("update servers set private = 1 where date_format(open_date, '%Y-%m-%d') = '$yesterday' and private = 0");//设置昨日开服为公开

$pdb = new mysql();
	
	$query = $db->query("
	SELECT 
		A.cid,
		A.sid,
		A.name,
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name,
		A.open_date,
		B.coins_rate
	FROM 
		servers A
		LEFT JOIN company B ON A.cid = B.cid
	WHERE
		A.test = 0
		AND A.open = 1
		AND A.combined_to = 0
		AND A.open_date <= '$yesterday_ee'
	ORDER BY
		A.sid asc		
	");
	if($db->num_rows($query))
	{
		while($server = $db->fetch_array($query))
		{	
			
			$cid =  $server['cid'];
			$sid =  $server['sid'];
			$name =  $server['name'];
			$coins_rate =  $server['coins_rate'];
			$open_date =  strtotime($server['open_date']);
			echo $server['cid'].'|'.$server['sid'].'<br />';
			//------------------------------------------连远程数据库-----------------------------------------------------
			$pdbhost = SetToDB($server['db_server']);//数据库服务器
			$pdbuser = $server['db_root'];//数据库用户名
			$pdbpw = $server['db_pwd'];//数据库密码
			$pdbname = $server['db_name'];//数据库名	
			$pdbcharset = 'utf8';//数据库编码,不建议修改.
			$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
			
			$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
			unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			//-------------------------------------------最高在线----------------------------------------------------
			$max = $pdb->fetch_first("
				SELECT 
					`time` AS max_online_time,
					MAX(online_count) AS max_online_count
				FROM 
					server_state
				WHERE 
					`time` >= '$yesterday_s'
					AND `time` <= '$yesterday_e'
				GROUP BY
					online_count		
				ORDER BY 
					max_online_count desc
			");
			if($max)
			{
				$max_online_count = $max['max_online_count'];
				$max_online_time = $max['max_online_time'];		
			}
			if($max)
			{	
				//-------------------------------------------平均在线----------------------------------------------------
				$avg = $pdb->fetch_first("
				SELECT
					SUM(online_count) AS online_count,
					COUNT(*) AS hour_count
				FROM 
					server_state
				WHERE 
					`time` >= '$yesterday_s'
					AND `time` <= '$yesterday_e'	
				");

				if($max)
				{
					$avg_online_count = round($avg['online_count']/$avg['hour_count']);
				}				
			
				//-------------------------------------------注册/创建/登陆数据----------------------------------------------------
				
				$reg = $pdb->fetch_first("
				SELECT
					SUM(register_count) AS register_count,
					SUM(login_count) AS login_count
				FROM 
					server_state
				WHERE 
					`time` >= '$yesterday_s'
					AND `time` <= '$yesterday_e'
			
				");
				if($reg)
				{
					$register_count = $reg['register_count'];
					$login_count = $reg['login_count'];		
				}	
				
				$create_count = $pdb->result($pdb->query("SELECT COUNT(*) FROM player a LEFT JOIN player_trace b ON a.id=b.player_id WHERE nickname<>'' AND first_login_time>='$yesterday_s' AND first_login_time<=$yesterday_e"), 0);
				//-------------------------------------------新手流失----------------------------------------------------
				$out_count = $pdb->result_first("
					SELECT 
						COUNT(B.player_id) AS out_count
					FROM 
						player A
						LEFT JOIN player_trace B ON A.id = B.player_id
						LEFT JOIN player_role C ON A.id = C.player_id and A.main_role_id = C.id
			
					WHERE 
						C.level < 10
						AND B.first_login_time >= '$yesterday2_s'
						AND B.first_login_time <= '$yesterday2_e'		
				");	
				
			
				//-------------------------------------------消费----------------------------------------------------
				$con = $pdb->fetch_first("
				
				select 
					SUM(if(change_charge_value < 0,change_charge_value,0)) AS consume
				from 
					player_ingot_change_record
				where 
					change_time >= '$yesterday_s'
					AND change_time <= '$yesterday_e'	
				"); 		
				if($con){
					$consume = round(($con['consume']/$coins_rate),2);				
					
				}			
				
				
			}
			//-----------------------------------执行插入------------------------------------------------------------
			$d = $db->result($db->query("select count(*) from game_data where cid = '$cid' and sid = '$sid' and gdate = '$yesterday'"),0);
			if (!$d)
			{
				$db->query("
				insert IGNORE into 
				game_data
				(
					cid,
					sid,
					max_online_time,
					max_online_count,
					avg_online_count,
					register_count,
					create_count,
					login_count,
					out_count,
					consume,
					gdate
				) 
				values 
				(
					'$cid',
					'$sid',
					'$max_online_time',
					'$max_online_count',
					'$avg_online_count',
					'$register_count',
					'$create_count',
					'$login_count',
					'$out_count',
					'$consume',
					'$yesterday'
				) 
				");
			}else{
				$db->query("
				update 
					game_data 
				set 
					max_online_time = '$max_online_time',
					max_online_count = '$max_online_count',
					avg_online_count = '$avg_online_count',
					register_count = '$register_count',
					create_count = '$create_count',
					login_count = '$login_count',
					out_count = '$out_count',
					consume = '$consume'
				where 
					cid = '$cid' 
					and sid = '$sid' 
					and gdate = '$yesterday'
				");
			}
				

			//-----------------------------------------------------------------------------------------------
			unset($e, $s,$name, $cid, $sid, $max_online_time, $max_online_count, $avg_online_count, $register_count,$create_count,$login_count,$out_count,$consume);
			//-----------------------------------------------------------------------------------------------
			
		}
		insertServersAdminData(0,0,0,'服务器','执行获取远程数据计划任务');//插入操作记录

		$odb->query("INSERT INTO ho_sys_log_cron(`key`,content,crontime,dateline) VALUES('GameData', '执行获取远程数据计划任务', '$crontime',".time().");");
	}

$pdb->close();
$db->close();
$odb->close();
?>