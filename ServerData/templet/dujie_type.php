<?php

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### spirit_state

$list = $dbh->query("select * from `spirit_state`");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : \"".$item["name"]."\"";
}

### spirit_state_require

$list = $dbh->query("select `spirit_state_id`, `level`, `role_level`, `health` from `spirit_state_require` order by spirit_state_id, level");

$hash1 = "";
$health = 0;
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			[".$item["spirit_state_id"].",".$item["level"].", ".$item["role_level"].", ".($item["health"] - $health)."]";
	$health = $item["health"];
}


$str = "package com.assist.server
{
	public class DuJieType
	{
		include \"./source/DuJieTypeData0.as\";
		
		// id : name
		private static const SpiritState : Object = {
".$hash."
		};
		
		// [spirit_state_id, level, role_level, health]
		private static const Require : Array = [
".$hash1."
		];
		
		/**
		 * 获取境界名称
		 *
		 * @param id int
		 */
		public static function getSpiritName (id : int) : String
		{
			return SpiritState[id] || \"\";
		}
		
		/**
		 * 获取境界的颜色值
		 *
		 * @param id int
		 */
		public static function getSpiritColor (id : int) : uint
		{
			if (id >= 1 && id <= 3)
			{
				return 0x00a0e9;
			}
			else if (id >=4 && id <= 6)
			{
				return 0xff00ff;
			}
			else
			{
				return 0xffff11;
			}
		}
		
		/**
		 * 获取渡劫的角色等级
		 *
		 * @param id int
		 * 境界id
		 * @param level int
		 * 境界阶段
		 */
		public static function getRequireRoleLevel (id : int, level : int) : int
		{
			var len : int = Require.length;
			for (var i : int = 0; i < len; i++)
			{
				var item : Array = Require[i];
				if (item[0] == id && item[1] == level)
				{
					return item[2];
				}
			}
			
			return 0;
		}
		
		/**
		 * 获取渡劫增加的生命点
		 *
		 * @param id int
		 * 境界id
		 * @param level int
		 * 境界阶段
		 */
		public static function getRequireHealth (id : int, level : int) : int
		{
			var len : int = Require.length;
			for (var i : int = 0; i < len; i++)
			{
				var item : Array = Require[i];
				if (item[0] == id && item[1] == level)
				{
					return item[3];
				}
			}
			
			return 0;
		}
	}
}
";

file_put_contents($desc_dir."DuJieType.as", addons().$str);

echo "[data] dujie_type  [Done]\n";
?>