<?php
	//执行提取今日合服的测试号
	$pass = isset($argv[1]) ? trim($argv[1]) : null;
	if ($pass != 'cron') {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");

	$query = $db->query("
	select 
		cid,
		combined_to
	from 
		servers_merger
	where 
		date_format(open_date, '%Y-%m-%d') = curdate()
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			ReServerTest($rs['cid'],$rs['combined_to']);
			//echo $rs['cid'].'|'.$rs['combined_to'].'<br />';
	
		}
	}
	insertServersAdminData(0,0,0,'服务器','执行提取合服后测试号计划任务');//插入操作记录

?> 