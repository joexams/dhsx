<?php
	$pass = isset($argv[1]) ? trim($argv[1]) : null;
	if ($pass != 'cron') {
		exit('invalid args');
	}

	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	include_once(UCTIME_ROOT."/conn.php");
	if(!SXD_SYSTEM_TEST_GIFT)
	{
		echo 'CLOSE!';
		exit();
	}	
	
	$path_root = UCTIME_ROOT.'/cron/test_gift.php';
	$query = $db->query("
		select 
			*
		from 
			servers_address
		where 
			`type` = 2
		order by 
			`name` desc
		");	
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{
			print `php $path_root $rs[name]`;//执行不同版本
		}	
	}
	insertServersAdminData(0,0,0,'服务器','执行定期发放测试号奖励计划任务');//插入操作记录
	$db->close();


?> 