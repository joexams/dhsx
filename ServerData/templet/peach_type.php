<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### peach_data

$list = $dbh->query("select `peach_lv`, `exp` from `peach_data`;");

$max_level = 1;

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$max_level = max($max_level, $item["peach_lv"]);
	
	$hash .= "			".$item["peach_lv"]." : [".$item["exp"]."]";
}
### 类

$str = "package com.assist.server
{
	public class PeachType
	{
		// peach_lv : [exp]
		private static const PeachData : Object = {
".$hash."
		};
		
		/**
		 * 仙桃最高等级
		 */
		public static const MaxLevel : int = $max_level;
		
		/**
		 * 获取仙桃等级的经验值
		 * @param peachLevel int
		 */
		public static function getExp (peachLevel : int) : int
		{
			return PeachData[peachLevel] ? PeachData[peachLevel][0] : 0;
		}
	}
}
";

file_put_contents($desc_dir."PeachType.as", addons().$str);

echo "[data] peach_type  [Done]\n";
?>