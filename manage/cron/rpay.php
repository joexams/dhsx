<?php
$pass = isset($argv[1]) ? trim($argv[1]) : null;
if ($pass != 'cron') {
	exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
$path_root = UCTIME_ROOT.'/cron/rpay_ver.php';
$query = $db->query("
	select 
		server_ver
	from 
		servers
	where 
		private = 1
	group by 
		server_ver
	");	
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		print `php $path_root $rs[server_ver]`;//执行不同版本
	}	
}

$db->close();

?>