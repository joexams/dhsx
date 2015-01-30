<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list = $dbh->query("
SELECT
   `id`,
   `award_coin`,
   `award_skill`,
   `award_power` ,
   `award_fame` ,
   `award_xian_ling` ,
   `award_state_point`,
   `award_stone`,
   `award_item`,
   `award_item_count`,
   `need_flower`,
   `need_ingot`,
   `award_fate_scrap`,
   `framerate`,
   `game_function`
FROM flower_swap
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "		[".$item["id"].",".$item["award_coin"].",".$item["award_skill"].",".$item["award_power"].
		",".$item["award_fame"].",".$item["award_xian_ling"].",".$item["award_state_point"].",".$item["award_stone"].
		",".$item["award_item"].",".$item["award_item_count"].",".$item["need_flower"].",".$item["need_ingot"].
		",".$item["award_fate_scrap"].",".$item["framerate"].",".$item["game_function"]."]";					
}

### 类

$str = "package com.assist.server
{
	public class FlowerSwapType
	{
		//兑换列表 每期更新
		public static const exchangeList : Array = [11,4,7,9,10,12];
		//[id,铜钱，阅历，体力，声望，仙令，境界点，灵石，物品id，物品数量，需要印花，需要元宝,命格碎片，帧数,功能id]
		public static const item : Array = [
		".$hash."
		];
		
		/**
		*根据id获取该条数据
		*/
		private static function getData(id : int) : Array
		{
			for each(var arr : Array in item)
			{
				if(arr[0] == id)
					return arr;
			}
			return null;
		}

		/**
		*根据id返回帧数
		*/
		public static function getFrame(id : int) : int
		{
			return getData(id)[13];
		}

		/**
		*根据id返回功能id
		*/
		public static function getFunctionId(id : int) : int
		{
			return getData(id)[14];
		}
		
		/**
		*根据id返回物品id
		*/
		public static function getItemId(id : int) : int
		{
			return getData(id)[8];
		}

		/**
		*根据id返回印花数量
		*/
		public static function getFlower(id : int) : int
		{
			return getData(id)[10];
		}
	}
}
";

file_put_contents($desc_dir."FlowerSwapType.as", addons().$str);

echo("[data] FlowerSwap DONE\n");
?>