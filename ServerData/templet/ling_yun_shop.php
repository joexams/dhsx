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
   *
FROM ling_yun_shop
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		".$item["id"].":"."[".$item["type"].",".'"'.$item["item_id"].
		'"'.",".$item["value"].",".$item["ling_yun"]."]";
}

### 类

$str = "package com.assist.server
{
	public class LingYunShopType
	{
		//id，类型，物品id，数量，灵蕴
		private static var AwardType : Object = {
			".$hash1."
		};
		
		/**
		*根据id获取类型
		*/
		public static function getType(id : int) : int
		{
			return AwardType[id] ? AwardType[id][0] : 0;
		}

		/**
		*根据id获取奖励名称
		*/
		public static function getName(id : int) : String
		{
			var name : String = \"\";
			switch(getType(id))
			{
				case 1 :
					name = \"铜钱\";
					break;
				case 2 :
					name = \"声望\";
					break;
				case 3 :
					name = \"阅历\"
					break;
			}
			return getItemNumber(id) + name;
		}

		/**
		*根据id获取物品id
		*/
		public static function getItemId(id : int) : int
		{
			return AwardType[id] ? AwardType[id][1] : 0;
		}

		/**
		*根据id获取物品数量
		*/
		public static function getItemNumber(id : int) : int
		{
			return AwardType[id] ? AwardType[id][2] : 0;
		}

		/**
		*根据id获取需要灵蕴
		*/
		public static function getScore(id : int) : int
		{
			return AwardType[id] ? AwardType[id][3] : 0;
		}
	}
}
";

file_put_contents($desc_dir."LingYunShopType.as", addons().$str);

echo("[data] LingYunShopType DONE\n");
?>