<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### zodiac_level

$list = $dbh->query("
	select
		`level`, `name`, `gold_oil_id`, `require_level`
	from
		`zodiac_level`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["level"]." : [\"".$item["name"]."\"]";
}

### 类

$str = "package com.assist.server
{
	public class ZodiacType
	{
		// item_id : [name]
		private static const Levels : Object = {
".$hash."
		};
		
		/**
		 * 获取名字
		 * @param level int
		 */
		public static function getZodiaName (level : int) : String
		{
			return Levels[level] ? Levels[level][0] : \"\";
		}
	}
}
";

file_put_contents($desc_dir."ZodiacType.as", addons().$str);

echo "[data] zodiac_type  [Done]\n";
?>