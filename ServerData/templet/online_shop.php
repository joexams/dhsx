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
		`id`, `category`, `sell_type`,`item_id`,`item_amount`,`price`,`org_price`,`is_first_page`,is_on_sell
	from
		`online_shop_item`
");

$list1 = $dbh->query("
	select
		`id`, `name`
	from
		`online_shop_advertisement`
");

$list2 = $dbh->query("
	select
		`id`, `sign`,`name`
	from
		`online_shop_category`
");
$list3 = $dbh->query("
	select
		`id`, `sign`,`name`
	from
		`online_shop_sell_type`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["category"].",".$item["sell_type"].",".$item["item_id"].",".$item["item_amount"].",".$item["price"].",".$item["org_price"].",".$item["is_first_page"].",".$item["is_on_sell"]."]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["name"]."\"]";
}

$hash2 = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\"]";
}
$hash3 = "";
for ($i = 0; $i < count($list3); $i++) {
	$item = $list3[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\"]";
}


### 类

$str = "package com.assist.server
{
	public class OnlineShopType
	{
		// [物品id : 分类 出售类型 物品id 物品数量 单价 是否首页显示 是否上架 原价]
		private static const Item : Object = {
".$hash."
		};
		
		// 广告id : [广告名字]
		private static const Advertisement : Object =
		{
".$hash1."
		}
		
		// 商品类别 : [标识 名称]
		private static const Category : Object =
		{
".$hash2."
		}
		
		/**
		 * 促销名字
		 */
		 private static const SellType : Object =
		 {
".$hash3."			
		 }
		 
		/**
		 * 获取物品列表数量
		 */
		public static function getItemCount() : int
		{
			var count : int = 0;
			for(var i : String in Item)
			{
				count++;
			}
			return count;
		}
		
		/**
		 * 获取类别列表
		 */
		public static function getCategoryNum() : int
		{
			var count : int = 0;
			for(var i : String in Category)
			{
				count++;
			}
			return count;
		}
		
		/**
		 * 获取物品类别
		 * @param id int
		 */
		public static function getItemCategory (id : int) : int
		{
			
			return Item[id] ? Item[id][0] : 0;
		}
		
		/**
		* 获取物品出售类型
		* @param id int
		*/
		public static function getItemSellType(id : int) : int
		{
			return Item[id] ? Item[id][1] : 0;
		}
		
		/**
		 * 获取物品id
		 * @param id int
		 */
		public static function getItemId (id : int) : int
		{
			return Item[id] ? Item[id][2] : 0;
		}
		/**
		* 获取物品数量
		* @param id int
		*/
		public static function getItemNum(id : int) : int
		{
			return Item[id] ? Item[id][3] : 0;
		}
		
		/**
		* 获取物品单价
		* @param id int
		*/
		public static function getItemPrice(id : int) : int
		{
			return Item[id] ? Item[id][4] : 0;
		}
		
		/**
	         * 原价  是否有打折优惠
		 */
		public static function getOrgPrice(id : int) : int
		{
			return Item[id]? Item[id][5] : 0;
		}
		/**
		*该物品是否首页显示
		*0 否 1 是
		*/
		public static function isShowFirst(id : int) : int
		{
			return Item[id] ? Item[id][6] : 0;
		}
		
		/**
		 * 是否上架
		 * 返回 1 上架 0 下架
		 */
		public static function isSell(id : int) : int
		{
			return Item[id] ? Item[id][7] : 0;
		}
		
		
		/**
		* 获取广告名称
		* @param id int
		*/
		public static function getAdName(id : int) : String
		{
			return Advertisement[id] ? Advertisement[id][0] : \"\";
		}
		
		/**
		 * 类别标识
		 */
		 public static function getCategorySign(id : int) : String
		 {
			return Category[id] ? Category[id][0] : \"\";
		 }
		 
		 /**
		 * 类别名称
		 */
		 public static function getCategoryName(id : int) : String
		 {
			return Category[id] ? Category[id][1] : \"\";
		 }
		 
		  /**
		  * 促销标识
		  */
		public static function getSellSign(id : int) : String
		{
			return SellType[id] ? SellType[id][0] : \"\";
		}
		 /**
		  * 促销名字
		  */
		public static function getSellName(id : int) : String
		{
			return SellType[id] ? SellType[id][1] : \"\";
		}
	}
}
";

file_put_contents($desc_dir."OnlineShopType.as", addons().$str);

echo "[data] onlineShopType [Done]\n";
?>