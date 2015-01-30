<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### war_attribute_type

$list = $dbh->query("SELECT `id`, `sign`, `name` FROM `war_attribute_type`");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\"]";
}

### 类

$str = "package com.assist.server
{
	public class WarAttributeType
	{
		// id : [sign, name]
		private static const Data : Object = {
".$hash."
		};
		
		/**
		 * 获取标识
		 * @param id int
		 */
		public static function getSign (id : int) : String
		{
			return Data[id] ? Data[id][0] : \"\";
		}
        
        /**
		 * 获取名字
		 * @param id int
		 */
		public static function getName (id : int) : String
		{
			return Data[id] ? Data[id][1] : \"\";
		}
	}
}
";

file_put_contents($desc_dir."WarAttributeType.as", addons().$str);

echo "[data] war_attribute_type [Done]\n";
?>