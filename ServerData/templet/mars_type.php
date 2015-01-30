<?php

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### mars_offerings

$list = $dbh->query("select * from `mars_offerings`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["exp"].", ".$item["blessing_count"].", ".$item["fame"].", ".$item["ingot"].", ".$item["skill"].", ".$item["vip_level"]."]";
}

$str = "package com.assist.server
{
	public class MarsType
	{
		// id : [name, exp, blessing_count, fame, ingot, skill, vip_level]
		private static const Offerings : Object = {
".$hash."
		};
		
		/**
		 * 获取香的名称
		 * @param id int
		 */
		public static function getOfferingName (id : int) : String
		{
			return Offerings[id] ? Offerings[id][0] : \"\";
		}
		
		/**
		 * 获取经验倍数
		 * @param id int
		 */
		public static function getOfferingExp (id : int) : int
		{
			return Offerings[id] ? Offerings[id][1] : 0;
		}
		
		/**
		 * 获取祝福/打副本次数
		 * @param id int
		 */
		public static function getOfferingBlessingCount (id : int) : int
		{
			return Offerings[id] ? Offerings[id][2] : 0;
		}
		
		/**
		 * 获取奖励声望
		 * @param id int
		 */
		public static function getOfferingFame (id : int) : int
		{
			return Offerings[id] ? Offerings[id][3] : 0;
		}
		
		/**
		 * 获取消费元宝
		 * @param id int
		 */
		public static function getOfferingIngot (id : int) : int
		{
			return Offerings[id] ? Offerings[id][4] : 0;
		}
		
		/**
		 * 获取消费阅历
		 * @param id int
		 */
		public static function getOfferingSkill (id : int) : int
		{
			return Offerings[id] ? Offerings[id][5] : 0;
		}
		
		/**
		 * 获取需求vip等级
		 * @param id int
		 */
		public static function getOfferingVipLevel (id : int) : int
		{
			return Offerings[id] ? Offerings[id][6] : 0;
		}
	}
}
";

file_put_contents($desc_dir."MarsType.as", addons().$str);

echo "[data]  mars_type [Done]\n";
?>