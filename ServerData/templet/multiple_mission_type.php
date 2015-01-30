<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `mission_id`, `name`, `award_skill`, `award_experience`, `award_fame` from `multiple_mission`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["mission_id"].", \"".$item["name"]."\", ".$item["award_skill"].", ".$item["award_experience"].", ".$item["award_fame"]."]";
}

### 类

$str = "package com.assist.server
{
	public class MultipleMissionType
	{
		// id : [mission_id, name, award_skill, award_experience, award_fame]
		private static const Datas : Object = {
".$hash."
		};
		
		/**
		 * 获取副本名称
		 * @param id int
		 */
		public static function getMissionName (id : int) : String
		{
			var item : Object = Datas[id];
			return item ? item[1] : \"\";
		}
		
		/**
		 * 获取奖励阅历
		 * @param id int
		 */
		public static function getAwardSkill (id : int) : int
		{
			var item : Object = Datas[id];
			return item ? item[2] : 0;
		}
		
		/**
		 * 获取奖励经验
		 * @param id int
		 */
		public static function getAwardExp (id : int) : int
		{
			var item : Object = Datas[id];
			return item ? item[3] : 0;
		}
		
		/**
		 * 获取奖励物品id
		 * @param id int
		 */
		public static function getAwardItemId (id : int) : int
		{
			var item : Object = Datas[id];
			return item ? item[4] : 0;
		}
	}
}
";

file_put_contents($desc_dir."MultipleMissionType.as", addons().$str);

echo "[data] multiple_mission_type [Done]\n";
?>