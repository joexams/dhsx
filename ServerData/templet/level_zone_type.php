<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list1 = $dbh->query("
SELECT
   `id`,
   `min_lv`,
   `max_lv`
FROM award_level_zone
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		[".$item["id"].", ".$item["min_lv"].", ".$item["max_lv"]."]";
}

### 类

$str = "package com.assist.server
{
	public class LevelZoneType
	{
		//id，最低等级，最高等级
		private static var LevelZoneData : Array = [
			".$hash1."
		];
		
		/**
		*根据等级获取区间id
		*@param level : 玩家等级
		*/
		public static function getZoneId(level : int) : int
		{
			var list : Array = []; 
			for each(var arr : Array in LevelZoneData)
			{		
				if(level >= arr[1] && level < arr[2])
				{
					return arr[0];
				}
			}
			return 0;
		}
	}
}
";

file_put_contents($desc_dir."LevelZoneType.as", addons().$str);

echo("[data] LevelZoneType DONE\n");
?>