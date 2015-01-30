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
$yesterday_s = date('Y-m-d 00:00:00',time()-86400);//昨天数据
$yesterday_e = date('Y-m-d 23:59:59',time()-86400);//昨天数据
/*$p = ReqStr('p');//密码
if ($p != '2208755') {
	echo 'ERROR!';
	exit();
}*/
	// $db->query("update servers set private = 1 where open_date >= '$yesterday_s' and open_date <= '$yesterday_e' and private = 0");//设置昨日开服为公开

	
	$query = $db->query("
	SELECT 
		A.cid,
		A.sid,
		A.name,
		B.coins_rate
	FROM 
		servers A
		LEFT JOIN company B ON A.cid = B.cid
	WHERE
		A.open_date <= '$yesterday_e'
	ORDER BY
		A.cid asc,		
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

			//-------------------------------------------充值----------------------------------------------------
			$pay = $db->fetch_first("
			SELECT 		
				COUNT(DISTINCT(player_id)) AS pay_player_count,
				SUM(amount) AS pay_amount,
				COUNT(*) AS pay_num
			FROM 
				pay_data 
			WHERE 
				cid = '$cid'
				AND sid = '$sid'
				AND status <> 1	
				AND success <> 0	
				AND `dtime` >= '$yesterday_s'
				AND `dtime` <= '$yesterday_e'
			");
			if($pay){
				$pay_player_count = $pay['pay_player_count'];
				$pay_amount = round($pay['pay_amount'],2);
				$pay_num = $pay['pay_num'];		
				
			}
			
			//-------------------------------------------首充----------------------------------------------------
	
			$new_player = $db->result_first("
			SELECT 
				new_player
			FROM 
				pay_day_new
			WHERE 
				cid = '$cid'
				AND sid = '$sid'
				AND pdate = '$yesterday'				
				
			");
		
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
					pay_player_count,
					pay_amount,
					pay_num,
					new_player,
					gdate
				) 
				values 
				(
					'$cid',
					'$sid',
					'$pay_player_count',
					'$pay_amount',
					'$pay_num',
					'$new_player',
					'$yesterday'
				) 
				");
			}else{
				$db->query("
				update 
					game_data 
				set 
					pay_player_count = '$pay_player_count',
					pay_amount = '$pay_amount',
					pay_num = '$pay_num',
					new_player = '$new_player'
				where 
					cid = '$cid' 
					and sid = '$sid' 
					and gdate = '$yesterday'
				");
			}
			
		//-----------------------------------------------------------------------------------------------
		unset($e, $s,$name, $cid, $sid, $pay_player_count,$pay_amount,$pay_num,$new_player);
		//-----------------------------------------------------------------------------------------------
			
		}
		insertServersAdminData(0,0,0,'服务器','执行每日充值数据汇总计划任务');//插入操作记录

		$odb->query("INSERT INTO ho_sys_log_cron(`key`,content,crontime,dateline) VALUES('PayData', '执行每日充值数据汇总计划任务', '$crontime',".time().");");
	}

$db->close();
$odb->close();
?>