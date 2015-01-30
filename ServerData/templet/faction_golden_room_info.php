<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### online_shop

$list = $dbh->query("
	select
		`level`, `upper_limit_money`, `levelup_need_cost`,`maintain_cost`,`lower_limit_money`,`levelup_need_exp`,`need_day`
	from
		`faction_golden_room_info`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["level"]." : [".$item["upper_limit_money"].",".$item["levelup_need_cost"].",".$item["maintain_cost"].",".$item["lower_limit_money"].",".$item["levelup_need_exp"].",".$item["need_day"]."]";
}

### 类

$str = "package com.assist.server
{
	public class FactionGoldenRoomInfo
	{
		// [建筑等级 : 基金上限 升级所需花费 每小时维护费用 资金下限 升级所需经验 所需天数]
		private static const Item : Object = {
".$hash."
		};
		 
		
		/**
		 * 获取基金上限
		 * @param levle:金库等级
		 */
		public static function getUpperLimit (level : int) : int
		{
			
			return Item[level] ? Item[level][0] : 0;
		}
		
		/**
		* 获取升级所需花费
		* @param level:金库等级
		*/
		public static function getLevelUpNeed(level : int) : int
		{
			return Item[level] ? Item[level][1] : 0;
		}
		
		/**
		 * 获取每小时维护费用
		 * @param level:金库等级
		 */
		public static function getMaintainCost (level : int) : int
		{
			return Item[level] ? Item[level][2] : 0;
		}
		/**
		* 获取资金下限
		* @param level:金库等级
		*/
		public static function getLowerLimit(level : int) : int
		{
			return Item[level] ? Item[level][3] : 0;
		}
		
		/**
		* 获取升级所需经验
		* @param level:金库等级
		*/
		public static function getLevelUpNeedExp(level : int) : int
		{
			return Item[level] ? Item[level][4] : 0;
		}

		/**
		* 获取升级所需时间
		* @param level:金库等级
		*/
		public static function getLevelUpNeedDay(level : int) : int
		{
			return Item[level] ? Item[level][5] : 0;
		}

	}
}
";

file_put_contents($desc_dir."FactionGoldenRoomInfo.as", addons().$str);

echo "[data] FactionGoldenRoomInfo [Done]\n";
?>