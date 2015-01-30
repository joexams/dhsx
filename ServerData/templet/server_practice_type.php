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
		`id`, `quality`, `normal_seat_count` , `normal_exp_addition` , `luxury_exp_addition` 
	from
		`st_practice_room_info`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["quality"].": [".$item["normal_seat_count"].", ".$item["normal_exp_addition"].", ".$item["luxury_exp_addition"]."] ";
}

$list = $dbh->query("
	select
		`id`, `level`, `exp_per_range`
	from
		`st_practice_room_level_exp`
");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["level"].": ".$item["exp_per_range"]." ";
}

### 类

$str = "package com.assist.server
{
	public class ServerPracticeType
	{
		// item_id : [name]
		private static const Qualities : Object = {
".$hash."
		};
		
		// item_id : [name]
		private static const Levels : Object = {
".$hash1."
		};
		
		/**
		 * 获取房间个数
		 * @param quality int
		 */
		public static function getRoomCountByQuality (quality : int) : int
		{
			var ary:Array = Qualities[quality] ? Qualities[quality] : [];
			return ary[0];
		}
		
		/**
		 * 获取普通房间加成
		 * @param quality int
		 */
		public static function getNormalByQuality (quality : int) : int
		{
			var ary:Array = Qualities[quality];
			return ary[1];
		}
		
		/**
		 * 获取至尊房间加成
		 * @param quality int
		 */
		public static function getEtrByQuality (quality : int) : int
		{
			var ary:Array = Qualities[quality];
			return ary[2];
		}
		
		/**
		 * 获取等级经验
		 * @param level int
		 */
		public static function getExpByLevel (level : int) : int
		{
			var exp:int = (Levels[level]) ? Levels[level] : 0;
			return exp;
		}
	}
}
";

file_put_contents($desc_dir."ServerPracticeType.as", addons().$str);

print repeat("[data] ServerPracticeType", 75, ".")."DONE.\n";
?>