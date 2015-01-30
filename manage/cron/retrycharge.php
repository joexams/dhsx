<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
//$sid = ReqNum('sid');
$p = ReqStr('p');//密码
$e = ReqNum('e');//是否执行
if ($p != '2208755') {
	echo 'ERROR!';
	exit();
}
$path_root = UCTIME_ROOT.'/cron/retrycharge_ver.php';
$query = $db->query("
	select 
		*
	from 
		servers_address
	where 
		`type` = 2
		and name2 = 1
	order by 
		`name` desc
		
	");	
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		print `php $path_root $rs[name] $e`;//执行不同版本
		echo '<br /><br />';
	}	
}

$db->close();
echo botTime();
?>