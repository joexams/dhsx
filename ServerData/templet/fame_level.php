<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role_stunt

$list = $dbh->query("select `id`, `level`, `name` from `fame_level_data`;");
$roleList = $dbh->query("select `level`, `require_fame` from `fame_level_for_role`;");

$hash = "";
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["level"].", \"".$item["name"]."\"]";
}

for ($i = 0; $i < count($roleList); $i++) {
	$item1 = $roleList[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item1["level"]." : ".$item1["require_fame"];
}

### 类

$str = "package com.assist.server
{
	public class FameLevel
	{
		// id : [level, name]
		public static const Levels : Object = {
".$hash."
		};
		
	        // level : [EXP]
		public static const roleLevels : Object = {
".$hash1."
		};
		
		/**
		 * 声望等级
		 *
		 * @param level int
		 */
		public static function getFameName (level : int) : String
		{
			for each (var arr : Array in Levels)
			{
				if (arr[0] == level)
				{
					return arr[1];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 声望等级获取声望经验
		 *
		 * @param level int
		 */
		public static function getFameExpForFameLevel (level : int) :int
		{
			return roleLevels[level] || 0;
		}
	}
}";

file_put_contents($desc_dir."FameLevel.as", addons().$str);

echo "[data] fame_level  [Done]\n";
?>