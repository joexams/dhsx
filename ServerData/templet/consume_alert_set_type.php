<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `name`, `description`, `vip` from `consume_alert_set_type`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : ["."\"".$item["description"]."\", ".$item["vip"]."]";
	
	$constant .= "		public static const ".$item["name"]." : int = ".$item["id"].";";
}

### 类

$str = "package com.assist.server
{
	public class ConsumeAlertSetType
	{
		// id : [description, vip]
		private static const Data : Object = {
".$hash."
		};
		
".$constant."
		
		/**
		 * 获得描述
		 * @param id int
		 */
		public static function getDescription (id : int) : String
		{
			return Data[id] ? Data[id][0] : \"\";
		}
		
		/**
		 * 获得vip数值
		 * @param id int
		 */
		public static function getVIP (id : int) : int
		{
			return Data[id] ? Data[id][1] : 0;
		}
	}
}
";

file_put_contents($desc_dir."ConsumeAlertSetType.as", addons().$str);

echo "[data] consume_alert_set_type  [Done]\n";
?>