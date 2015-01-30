<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");

	define('MAX_SLEEP_TIME',120);

	$query = $db->query("SHOW PROCESSLIST");	
	while($proc = $db->fetch_array($query)){
		if($proc["Command"] == "Sleep" && $proc["Time"] > MAX_SLEEP_TIME){
			@$db->query("KILL ".$proc["Id"]);
		}
	}
	//exit();
	//mysql_close($connect);

?>