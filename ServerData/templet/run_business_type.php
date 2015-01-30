<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### runBusinessType
$list1 = $dbh->query("
	select
		`id`, `type`, `name`, `min_buy_price`, `max_buy_price`, `pic_sine`
	from
		`run_business_new_item`
");

$list2 = $dbh->query("
	select
		`id`, `sign`,`name`,`description`
	from
		`run_business_town`
");

$list3 = $dbh->query("
	select
		`item_type`, `item_name`
	from run_business_item_type
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"].": [".$item["type"].", \"".$item["name"]."\", ".$item["min_buy_price"].", "
	.$item["max_buy_price"].", \"".$item["pic_sine"]."\"]";
}

$hash2 = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\",\"".$item["description"]."\"]";
}

$hash3 = "";
for($i = 0; $i < count($list3); $i++){
	$item = $list3[$i];
	if($hash3 != "")
	{
		$hash3 .= ",\n";
	}
	$hash3 .= "			[".$item["item_type"].", \"".$item["item_name"]."\"]";
}



### 类

$str = "package com.assist.server
{
	public class RunBusinessType
	{	
		// 物品id : [物品类型 最低价  最高价 物品标识]
		private static const TownItem : Object =
		{
".$hash1."
		};
		
		// 城镇id : [标识 名称]
		private static const Town : Object =
		{
".$hash2."
		};

		//物品类型:类型名称
		public static const ItemType : Array =
		[
			".$hash3."
		];

		/**
		*根据物品id获取名称
		*/
		public static function getItemName(id : int) : String
		{
			return TownItem[id] ? TownItem[id][1] : \"\";
		}
		
		/**
		 * 根据物品id获取物品类型
		 */
		public static function getItemType(id : int) : int
		{
			return  TownItem[id] ? TownItem[id][0] : 0;
		}
		
	
		  /**
		  * 根据城镇id 标识
		  */
		public static function getTownSign(id : int) : String
		{
			return Town[id] ? Town[id][0] : \"\";
		}
		 /**
		  * 根据城镇id 跑商城镇的名称
		  */
		public static function getTownName(id : int) : String
		{
			return Town[id] ? Town[id][1] : \"\";
		}
		
		 /**
		  * 根据城镇id 跑商城镇的描述
		  */
		public static function getTownDescribe(id : int) : String
		{
			return Town[id] ? Town[id][2] : \"\";
		}
		
	}
}
";

file_put_contents($desc_dir."RunBusinessType.as", addons().$str);

echo "[data] runBusinessType [Done]\n";
?>