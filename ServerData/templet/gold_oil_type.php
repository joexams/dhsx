<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### gold_oil

$list = $dbh->query("
	select
		`item_id`, `name`, `item_lv`, `oil_lv`, `need_item_id`,
		`use_coin`, `use_state_point`, `get_state_point`, `use_ingot`
	from
		`gold_oil`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["item_id"]." : [\"".$item["name"]."\", ".$item["item_lv"].", ".$item["oil_lv"].", ".$item["need_item_id"].", ".$item["use_coin"].", ".$item["use_state_point"].", ".$item["get_state_point"].", ".$item["use_ingot"]."]";
}

### gold_oil_data

$item_id_hash = array();

$list = $dbh->query("
	select
		`gold_oil_item_id`, `item_type`,
		`item_attack_up`, `item_defense_up`,
		`item_stunt_attack_up`, `item_stunt_defense_up`,
		`item_magic_attack_up`, `item_magic_defense_up`,
		`item_health_up`,`item_speed_up`,`attack_type`
	from
		`gold_oil_data`
");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			["
			.$item["gold_oil_item_id"].", "
			.$item["item_type"].", "
			.$item["item_attack_up"].", "
			.$item["item_defense_up"].", "
			.$item["item_stunt_attack_up"].", "
			.$item["item_stunt_defense_up"].", "
			.$item["item_magic_attack_up"].", "
			.$item["item_magic_defense_up"].", "
			.$item["item_health_up"].", "
		        .$item["item_speed_up"].", "
			.$item["attack_type"]
			."]";
	
	$item_id_hash[$item["gold_oil_item_id"]] = 1;
}

### 类

$str = "package com.assist.server
{
	public class GoldOilType
	{
		/**
		 * item_id : [, , , , , , ]
		 * 索引 item_id
		 * [
		 * 	[
		 * 		0.name,
		 * 		1.item_lv,
		 * 		2.oil_lv,
		 * 		3.need_item_id,
		 * 		4.use_coin,
		 * 		5.use_state_point,
		 * 		6.get_state_point,
		 * 	],
		 * 	...
		 * ]
		 */
		private static const Oils : Object = {
".$hash."
		};
		
		/**
		 * [
		 * 	[
		 * 		0.item_id,
		 * 		1.item_type,
		 * 		2.item_attack_up,
		 * 		3.item_defense_up,
		 * 		4.item_stunt_attack_up,
		 * 		5.item_stunt_defense_up,
		 * 		6.item_magic_attack_up,
		 * 		7.item_magic_defense_up, 
		 * 		8.item_health_up
		 *              9.item_speed_up
 		 *		10.attack_type
		 * 	],
		 * 	...
		 * ]
		 */
		private static const Data : Array = [
".$hash1."
		];
		
		/**
		 * 获取所有金油物品id
		 */
		public static function getAllOilItemIds () : Array
		{
			var arr : Array = [];
			for (var itemId : String in Oils)
			{
				arr[Oils[itemId][2] - 1] = parseInt(itemId);
			}
			
			return arr;
		}
		
		/**
		 * 获取金油名称
		 */
		public static function getOilName (oilItemId : int) : String
		{
			return ItemType.getName(oilItemId);
		}
		
		/**
		 * 获取金油可以镀金的物品等级
		 */
		public static function getItemLevel (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[1] : 0;
		}
		
		/**
		 * 获取金油等级
		 */
		public static function getOilLevel (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[2] : 0;
		}
		
		/**
		 * 获取上一级的金油id
		 */
		public static function getPrevOilItemId (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[3] : 0;
		}
		
		/**
		 * 获取使用铜钱数
		 */
		public static function getUseCoin (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[4] : 0;
		}
		
		/**
		 * 获取使用消耗境界点
		 */
		public static function getUseStatePoint (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[5] : 0;
		}
		
		/**
		 * 获取兑换消耗境界点
		 */
		public static function getExchangeStatePoint (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[6] : 0;
		}
		
		/**
		 * 获取立即使用金油需要多少元宝
		 */
		public static function getUserGoldOilIngot (oilItemId : int) : int
		{
			var arr : Array = Oils[oilItemId];
			return arr ? arr[7] : 0;
		}
		
		/**
		 * 获取金油附加属性
		 */
		public static function getOilAttr (oilItemId : int, itemTypeId : int, jobId : int = 0) : Object
		{
			var arr : Array = [];			
			var len : int = Data.length;
			
			if(itemTypeId != 2)
			{
				jobId = 0;
			}
			
			for (var i : int = 0; i < len; i++)
			{
				if (Data[i][0] == oilItemId && Data[i][1] == itemTypeId && Data[i][10] == jobId)
				{
					arr = Data[i];
					break;
				}
			}
			
			return {
				attackUp       : arr[2] || 0,
				defenseUp      : arr[3] || 0,
				stuntAttackUp  : arr[4] || 0,
				stuntDefenseUp : arr[5] || 0,
				magicAttackUp  : arr[6] || 0,
				magicDefenseUp : arr[7] || 0,
				healthUp       : arr[8] || 0,
				speedUp        : arr[9] || 0
			};
		}
	}
}
";

file_put_contents($desc_dir."GoldOilType.as", addons().$str);

echo "[data] gold_oil_type  [Done]\n";
?>