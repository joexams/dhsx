<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### mysteriousShop

$list = $dbh->query("
	select
		`id`, `type`, `categoty`,`item_id`,`amount`,`ingot`,`coin`,`lv_min`,`lv_max`
	from
		`mysterious_shop_item`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["type"].",".$item["categoty"].",".$item["item_id"].",".$item["amount"].",".$item["ingot"].",".$item["coin"].",".$item["lv_min"].",".$item["lv_max"]."]";
}


### 类

$str = "package com.assist.server
{
	public class MysteriousShopType
	{
		// 商品id ：[类型 1-资源;2-材料 类别 1-物品;2-声望;3-铜钱;4-阅历;5-灵石;6-境界点 物品id 数量 元宝价格 铜钱价格 等级下限 等级上限]
		private static const Item : Object = {
".$hash."
		};
		
		/**
		 * 类型 1-资源;2-材料
		 */
		public static function type(id : int) : int
		{
			return Item[id] ? Item[id][0] : 0;
		}
		
		/**
		 * 类别 1-物品;2-声望;3-铜钱;4-阅历;5-灵石;6-境界点
		 */
		public static function categoty(id : int) : int
		{
			return Item[id] ? Item[id][1] : 0;
		}
		
		/**
		 * 物品id
		 */
		public static function itemId(id : int) : int
		{
			return Item[id] ? Item[id][2] : 0;
		} 
		
		
		/**
		 * 数量
		 */
		public static function amount(id:int) : int
		{
			return Item[id] ? Item[id][3] : 0;
		}
		
		/**
		 * 元宝价格
		 */
		public static function ingot(id:int) : int
		{
			return Item[id] ? Item[id][4] : 0;
		}
		
		/**
		 * 铜钱价格
		 */
		public static function coin(id:int) : int
		{
			return Item[id] ? Item[id][5] : 0;
		}
		
		/**
		 * 等级下限
		 */
		public static function lvMin(id:int) : int
		{
			return Item[id] ? Item[id][6] : 0;
		}
		
		/**
		 * 等级上限
		 */
		public static function lvMax(id:int) : int
		{
			return Item[id] ? Item[id][7] : 0;
		}
	
		
	}
}
";

file_put_contents($desc_dir."MysteriousShopType.as", addons().$str);

echo "[data] mysteriousShopType [Done]\n";
?>