<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### team_buying

$list = $dbh->query("
	select
		`id`, `name`
	from
		`team_buying_award_type`
");

$list1 = $dbh->query("
	select
		`id`, `name`,`price`,`discount`,`spare`,`jifen`,`limit_amount`
	from
		`team_buying_gift_type`
");

$list2 = $dbh->query("
	select
		`id`,`gift_id`, `award_id`,`item_id`,`amount`
	from
		`team_buying_info`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\"]"; 
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["name"]."\",".$item["price"].",".$item["discount"].",".$item["spare"].",".$item["jifen"].",".$item["limit_amount"]."]";
}

$hash2 = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [".$item["gift_id"].",".$item["award_id"].",".$item["item_id"].",".$item["amount"]."]";
}


### 类

$str = "package com.assist.server
{
	public class GroupBuyType
	{
		// 物品id : [奖励类型]
		private static const Award : Object = {
".$hash."
		};
		
		// id : [名字 积分 限制数量]
		private static const Gift : Object =
		{
".$hash1."
		}
		
		// 礼包ID : [奖励类型 物品ID 数量]
		private static const GiftAward : Object =
		{
".$hash2."
		}
		 
		/**
		 * 获取奖励类型
		 */
		public static function getAwardType(id : int) : String
		{
			return Award[id] ? Award[id][0] : \"\";
		}
		
		/**
		 * 获取id对应名称
		 */
		public static function getGiftName(id : int) : String
		{
			return Gift[id] ? Gift[id][0] : \"\";
		}
		
		/**
		 * 获取id对应原价
		 */
		public static function getGiftPrice(id : int) : int
		{
			return Gift[id] ? Gift[id][1] : 0;
		}
		
		/**
		 * 获取id对应折扣
		 */
		public static function getGiftDiscount(id : int) : Number
		{
			return Gift[id] ? Gift[id][2] : 0;
		}
		
		/**
		 * 获取id对应节省价格
		 */
		public static function getGiftSpare(id : int) : int
		{
			return Gift[id] ? Gift[id][3] : 0;
		}
		
		/**
		 * 获取id对应积分
		 */
		public static function getGiftJiFen(id : int) : int
		{
			return Gift[id] ? Gift[id][4] : 0;
		}
		
		/**
		 * 获取id对应限制数量 
		 */
		public static function getGiftLimitAmount(id : int) : int
		{
			return Gift[id] ? Gift[id][5] : 0;
		}
		
		/**
		 *获取礼包id对应的所有的奖励列表
		 */
		public static function getGiftAward(id : int) : Array
		{
			var rList : Array = [];
			for(var s : String in GiftAward)
			{
				var ary : Array = GiftAward[s];
				if(ary[0] == id)
				{
					rList.push(ary);
				}
			}
			return rList;
		}
	}
}
";

file_put_contents($desc_dir."GroupBuyType.as", addons().$str);

echo "[data] groupBuyType [Done]\n";
?>