<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `town_id` from `world_boss`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["town_id"]."]";
}

### 类

$str = "package com.assist.server
{
	public class WorldBossType
	{
		// id : [town_id]
		private static const Bosses : Object = {
".$hash."
		};
		
		/**
		 * 获取Boss城镇id
		 *
		 * @param id int
		 */
		public static function getTownIdByBossId (id : int) : int
		{
			return Bosses[id] ? Bosses[id][0] : 0;
		}
		
		/**
		 * 获取Boss城镇id
		 *
		 * @param sign String
		 */
		public static function getTownIdByBossSign (sign : String) : int
		{
			return TownType.getId(sign);
		}
	}
}
";

file_put_contents($desc_dir."WorldBossType.as", addons().$str);

echo "[data] world_boss_type  [Done]\n";
?>