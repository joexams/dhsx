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
FROM award_type
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		".$item["id"].":"."[".$item["day_type_id"].","
		.'"'.$item["name"].'"'.",".$item["skill"].",".$item["fame"].","
		.$item["coins"].",".$item["item_id"].",".$item["item_number"].", "
		.$item["lv"].",".$item["probability"].",".$item["level_zone_id"].", "
		.$item["need_jifen"].", ".$item["fate"].", ".$item["ling_shi"]."]";
}

### 类

$str = "package com.assist.server
{
	public class AwardType
	{
		//id，活动id，名称，阅历，声望，铜钱，物品id，物品数量,lv，probability，区间id，需要的积分
		private static var AwardType : Object = {
			".$hash1."
		};
		
		/**
		*获取奖励列表
		*@param dayTypeId : 活动类型id
		*/
		public static function getAwardList(dayTypeId : int) : Array
		{
			var list : Array = []; 
			for each(var obj : Object in AwardType)
			{		
				
				if(obj[0] == dayTypeId)
					list.push(obj[1]);
			}
			return list;
		}

		/**
		*根据id获取奖励名称
		*/
		public static function getName(id : int) : String
		{
			return AwardType[id] ? AwardType[id][1] : \"\";
		}

		/**
		*根据id获取阅历
		*/
		public static function getSkill(id : int) : int
		{
			return AwardType[id] ? AwardType[id][2] : 0;
		}

		/**
		*根据id获取声望
		*/
		public static function getFame(id : int) : int
		{
			return AwardType[id] ? AwardType[id][3] : 0;
		}

		/**
		*根据id获取铜钱
		*/
		public static function getCoins(id : int) : int
		{
			return AwardType[id] ? AwardType[id][4] : 0;
		}

		/**
		*根据id获取物品id
		*/
		public static function getItemId(id : int) : int
		{
			return AwardType[id] ? AwardType[id][5] : 0;
		}

		/**
		*根据id获取物品数量
		*/
		public static function getItemNumber(id : int) : int
		{
			return AwardType[id] ? AwardType[id][6] : 0;
		}

		/**
		*根据id获取需要积分  
		*/
		public static function getScore(id : int) : int
		{
			return AwardType[id] ? AwardType[id][10] : 0;
		}

		/**
		*根据活动类型获取兑换所需最低积分
		*/
		public static function getMinScore(dayTypeId : int) : int
		{
			var list : Array = [];
			var minVal : int = 0;
			for each(var obj : Object in AwardType)
			{		
				if(obj[0] == dayTypeId)
					list.push(obj[10]);

			}
			//求数组最小值
			for each(var i : int in list)
			{
				if(i < minVal)
					minVal = i;
			}
			return minVal;
		}
	}
}
";

file_put_contents($desc_dir."AwardType.as", addons().$str);

echo("[data] AwardType DONE\n");
?>