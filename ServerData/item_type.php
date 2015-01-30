<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### item_type

$list = $dbh->query("select `id`, `sign`, `name`, `max_repeat_num` from `item_type`");

$hash0 = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($constant != "") {
		$constant .= "\n";
		
		$hash0 .= ",\n";
	}
	
	$constant .= "		// ".$item["name"]."\n";
	$constant .= "		public static const ".$item["sign"]." : int = ".$item["id"].";";
	
	$hash0 .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["max_repeat_num"]."]";
}

### item
$listPrice = $dbh->query("
	select
		`level`, `item_price`, `equip_price`
	from `item_price`;
");
$itemPriceHash = array();
for ($i = 0; $i < count($listPrice); $i++) {
	$item = $listPrice[$i];
	$itemPriceHash[$item["level"]] = $item;
}

$listIongt = $dbh->query("
	select
		`id`, `ingot`
	from `item_ingot`;
");
$itemIngotHash = array();
for ($i = 0; $i < count($listIongt); $i++) {
	$item = $listIongt[$i];
	$itemIngotHash[$item["id"]] = $item;
}

$listCard = $dbh->query("
	select
		`item_id`, `card_item_id`, `number`
	from `item_card_job`;
");
$itemCardHash = array();
for ($i = 0; $i < count($listCard); $i++) {
	$item = $listCard[$i];
	$itemCardHash[$item["item_id"]] = $item;
}

$list = $dbh->query("
	select
		`id`, `name`, `type_id`, `price_level`, `usage`,
		`description`, `quality`, `require_level`, `type_id`, `ingot_level`,
		
		-- 物品属性
		`attack`, `attack_up`,
		`defense`, `defense_up`,
		`stunt_attack`, `stunt_attack_up`,
		`stunt_defense`, `stunt_defense_up`,
		`magic_attack`, `magic_attack_up`,
		`magic_defense`, `magic_defense_up`,
		`health`, `health_up`,
		`speed`, `speed_up`,
		`strength`, `agile`, `intellect`
	from `item`;
");

$hash = "";
$OC_item = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$OC_item .= ",\n";
	}
		
	$desc = $item["description"];
	$desc = preg_replace("/\\r|\\n/", "", $desc);
	#$desc = preg_replace("/\\r/", "\\r", $desc);
	#$desc = preg_replace("/\\n/", "\\n", $desc);
	
	$type_id = $item["type_id"];
	$item_id = $item["id"];
	
	$item["item_card_id"] = 0;
	$item["item_card_num"] = 0;
	
	if(false != array_key_exists($item_id, $itemCardHash))
	{
		$item["item_card_id"] = $itemCardHash[$item_id]["card_item_id"];
		$item["item_card_num"] = $itemCardHash[$item_id]["number"];
	}
	
	if($type_id < 7 || $type_id==1001 || $type_id==1002)
	{
		$item["price_level"] = $itemPriceHash[$item["price_level"]]["equip_price"];
	}
	else
	{
		$item["price_level"] = $itemPriceHash[$item["price_level"]]["item_price"];
	}	
	
	if($item["ingot_level"] != 0)
	{
		$item["ingot_level"] = $itemIngotHash[$item["ingot_level"]]["ingot"];
	}
	
	$hash .= "			".$item_id." : [\"".$item["name"]."\", "
											.$type_id.", \""./*$item["usage"]*/""."\", "
											."\"".$desc."\", "
											.$item["quality"].", "
											.$item["require_level"].", "
											.$item["attack"].", "
											.$item["attack_up"].", "
											.$item["defense"].", "
											.$item["defense_up"].", "
											.$item["stunt_attack"].", "
											.$item["stunt_attack_up"].", "
											.$item["stunt_defense"].","
											.$item["stunt_defense_up"].", "
											.$item["magic_attack"].","
											.$item["magic_attack_up"].", "
											.$item["magic_defense"].", "
											.$item["magic_defense_up"].", "
											.$item["health"].", "
											.$item["health_up"].", "
											.$item["speed"].", "
											.$item["speed_up"].", "
											.$item["strength"].", "
											.$item["agile"].", "
											.$item["intellect"].", "
											.$item["price_level"].", "
											.$item["ingot_level"].", "
											.$item["item_card_id"].", "
											.$item["item_card_num"]
											."]";
											
	$OC_item .= "                [NSArray arrayWithObjects: @\"" . $item["name"] . "\", @\"" . $type_id. "\", @\"" . $desc. "\", nil], @\"" . $item_id . "\"";
}

# 装备品质
$list = $dbh->query("select `quality`, `name` from item_quality");
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["quality"]." : \"".$item["name"]."\"";
}

# 装备对应的职业
$list = $dbh->query("select `item_id`, `role_job_id` from `item_equip_job`");
$item_jobs = array();
foreach ($list as $value) {
	$item_id = $value["item_id"];
	if (! array_key_exists($item_id, $item_jobs)) {
		$item_jobs[$item_id] = array();
	}
	
	array_push($item_jobs[$item_id], $value["role_job_id"]);
}

$hash2 = "";
foreach ($item_jobs as $item_id => $list) {
	$str_list = join(",", $list);
	
	if ($str_list == "1,2,3,5,6,7") continue;
	
	if ($hash2 != "") {
		$hash2 .= ", \n			";
	}
	else {
		$hash2 = "			";
	}
	
	$hash2 .= "".$item_id." : [".$str_list."]";
}

### 怪物卡
$list = $dbh->query("select * from `avatar_item_monster`;");

$hash3 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$item["item_id"]." : ".$item["monster_id"];
}

# 装备强化等级
$list = $dbh->query("select `level`, `name` from `item_upgrade`");

$hash4 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash4 != "") {
		$hash4 .= ",\n";
	}
	
	$hash4 .= "			".$item["level"]." : \"".$item["name"]."\"";
}

### OC类
$ocStr =
"#import \"ItemTypeData.h\"

@implementation ItemTypeData

NSDictionary *ItemData;

/**
物品数据
*/
+ (void) dataInit
{
	NSMutableDictionary *data = [[NSMutableDictionary alloc] initWithObjectsAndKeys:
".$OC_item."
	                            ,nil];
				    
        ItemData = [[NSDictionary alloc] initWithDictionary: data];
}

/**
通过物品ID获取物品名字
*/
+ (NSString*) getItemNameForItemId: (NSInteger) itemId
{
	NSArray *list = [ItemData objectForKey: [NSString stringWithFormat: @\"%d\", itemId]];
	NSString *itemName = [list objectAtIndex: 0];
	return itemName;
}

/**
通过物品ID获取物品物品描述
*/
+ (NSString*) getDescriptionForItemId: (NSInteger) itemId
{
	NSArray *list = [ItemData objectForKey: [NSString stringWithFormat: @\"%d\", itemId]];
	NSString *description = [list objectAtIndex: 2];
	return description;
}

@end
";

$ocStrH =
"#import <Foundation/Foundation.h>

@interface ItemTypeData : NSObject

+ (void) dataInit;

+ (NSString*) getItemNameForItemId: (NSInteger) itemId;

+ (NSString*) getDescriptionForItemId: (NSInteger) itemId;

@end
";
file_put_contents($desc_dir."ItemTypeData.m", addons().$ocStr);
file_put_contents($desc_dir."ItemTypeData.h", addons().$ocStrH);
### 类

$str = "package com.assist.server
{
	public class ItemType
	{
		include \"./source/ItemTypeData0.as\";
		
		// item_type_sign : item_type_id
".$constant."

		// 元宝
		public static const Ingot : int = 99998;
		
		// 铜钱
		public static const Coin : int = 99999;
		
		// 由 com.assist.server.source.ItemTypeData.Types 设置
		// type_id : [name, max_repeat_num]
		private static var _Types : Object = null;
		
		public static function get Types () : Object
		{
			if (_Types == null) throw new Error(\"还未赋值！\");
			
			return _Types;
		}
		
		public static function set Types (value : Object) : void
		{
			if (_Types != null) throw new Error(\"非法赋值\");
			
			_Types = value;
		}
		
		// 由 com.assist.server.source.ItemTypeData.Items 设置
		/**
		 * {
		 * 	item_id : [
		 * 		name, type_id, usage, description, quality, require_level,
		 * 		attack, attack_up,
		 * 		defense, defense_up,
		 * 		stunt_attack, stunt_attack_up,
		 * 		stunt_defense, stunt_defense_up,
		 * 		magic_attack, magic_attack_up,
		 * 		magic_defense, magic_defense_up, 
		 * 		health, health_up,
		 * 		speed, speed_up,
		 * 		strength, agile, intellect, price, ingot, item_card_id, item_card_num
		 * 	],
		 * 	...
		 * }
		 */
		private static var _Items : Object = null;
		
		public static function get Items () : Object
		{
			if (_Items == null) throw new Error(\"还未赋值！\");
			
			return _Items;
		}
		
		public static function set Items (value : Object) : void
		{
			if (_Items != null) throw new Error(\"非法赋值\");
			
			_Items = value;
		}
		
		// 由 com.assist.server.source.ItemTypeData.Qualitys 设置
		// quality : name
		private static var _Qualitys : Object = null;
		
		public static function get Qualitys () : Object
		{
			if (_Qualitys == null) throw new Error(\"还未赋值！\");
			
			return _Qualitys;
		}
		
		public static function set Qualitys (value : Object) : void
		{
			if (_Qualitys != null) throw new Error(\"非法赋值\");
			
			_Qualitys = value;
		}
		
		// 由 com.assist.server.source.ItemTypeData.EquipJob 设置
		// item_id : role_job_id
		private static var _EquipJob : Object = null;
		
		public static function get EquipJob () : Object
		{
			if (_EquipJob == null) throw new Error(\"还未赋值！\");
			
			return _EquipJob;
		}
		
		public static function set EquipJob (value : Object) : void
		{
			if (_EquipJob != null) throw new Error(\"非法赋值\");
			
			_EquipJob = value;
		}
		
		// 由 com.assist.server.source.ItemTypeData.AvatarItemMonster 设置
		// item_id : monster_id
		private static var _AvatarItemMonster : Object = null;
		
		public static function get AvatarItemMonster () : Object
		{
			if (_AvatarItemMonster == null) throw new Error(\"还未赋值！\");
			
			return _AvatarItemMonster;
		}
		
		public static function set AvatarItemMonster (value : Object) : void
		{
			if (_AvatarItemMonster != null) throw new Error(\"非法赋值\");
			
			_AvatarItemMonster = value;
		}
		
		// 由 com.assist.server.source.ItemTypeData.ItemUpgrade 设置
		// level : name
		private static var _ItemUpgrade : Object = null;
		
		public static function get ItemUpgrade () : Object
		{
			if (_ItemUpgrade == null) throw new Error(\"还未赋值！\");
			
			return _ItemUpgrade;
		}
		
		public static function set ItemUpgrade (value : Object) : void
		{
			if (_ItemUpgrade != null) throw new Error(\"非法赋值\");
			
			_ItemUpgrade = value;
		}
		
		// 颜色值
		// quality : color
		private  static var colors : Object = {
			// 白色
			1 : 0xffffff,
			// 绿色
			2 : 0x22ac38,
			// 蓝色
			3 : 0x00aeef,
			// 紫色
			4 : 0xff00ff,
			// 金色/黄色
			5 : 0xfff100
		};
		
		//----------------------------------------------------------------------
		//
		//  方法
		//
		//-----------------------------------------------------------------------
		
		/**
		 * 通过物品名称获取物品id
		 *
		 * @param name String
		 */
		public static function getItemIdByName (name : String) : int
		{
			for (var id : String in Items)
			{
				if (Items[id][0] == name)
				{
					return parseInt(id);
				}
			}
			
			return 0;
		}
		
		/**
		 * 获取具体物品类型可重复个数
		 *
		 * @param typeId int
		 */
		public static function getMaxRepeatNum (typeId : int) : int
		{
			return Types[typeId] ? Types[typeId][1] : 0
		}
		
		/**
		 * 获取物品的品质名
		 *
		 * @param itemId int
		 * 物品id
		 */
		public static function getItemQualityById (itemId : int) : int
		{
			return (Items[itemId] ? Items[itemId][4] : 0) || 0;
		}
		
		/**
		 * 获取物品的品质名
		 *
		 * @param name int
		 * 物品名称
		 */
		public static function getItemQualityByName (itemName : String) : int
		{
			var id : int = 0;
			for (var index : String in Items)
			{
				if (Items[index][0] == itemName)
				{
					id = Items[index][4];
					break;
				}
			}
			
			return id;
		}
		
		/**
		 * 获取物品的品质名
		 *
		 * @param itemId int
		 * 物品id
		 */
		public static function getItemQualityNameById (itemId : int) : String
		{
			return (Items[itemId] ? Qualitys[getItemQualityById(itemId)] : \"\") || \"\";
		}
		
		/**
		 * 获取物品的品质名
		 *
		 * @param name int
		 * 物品名称
		 */
		public static function getItemQualityNameByName (itemName : String) : String
		{
			var id : int;
			for (var index : String in Items)
			{
				if (Items[index][0] == itemName)
				{
					id = parseInt(index);
					break;
				}
			}
			
			return getItemQualityNameById(id);
		}
		
		/**
		 * 获取物品需求等级
		 * 
		 * @param itemId int
		 */
		public static function getRequireLevel (itemId : int) : int
		{
			return Items[itemId] ? Items[itemId][5] : 0;
		}
		
		/**
		 * 获取物品类型
		 * 
		 * @param itemId int
		 */
		public static function getTypeId (itemId : int) : int
		{
			return Items[itemId] ? Items[itemId][1] : 0;
		}
		
		/**
		 * 获取物品属性
		 * @param itemId int
		 */
		public static function getItemAttr (itemId : int) : Object
		{
			var arr : Array = Items[itemId];
			
			return {
				attack         : arr[6]  || 0,
				attackUp       : arr[7]  || 0,
				defense        : arr[8]  || 0,
				defenseUp      : arr[9]  || 0,
				stuntAttack    : arr[10] || 0,
				stuntAttackUp  : arr[11] || 0,
				stuntDefense   : arr[12] || 0,
				stuntDefenseUp : arr[13] || 0,
				magicAttack    : arr[14] || 0,
				magicAttackUp  : arr[15] || 0,
				magicDefense   : arr[16] || 0,
				magicDefenseUp : arr[17] || 0,
				health         : arr[18] || 0,
				healthUp       : arr[19] || 0,
				speed          : arr[20] || 0,
				speedUp        : arr[21] || 0,
				strength       : arr[22] || 0,
				agile          : arr[23] || 0,
				intellect      : arr[24] || 0
			};
		}
		
		/**
		 * 获取物品/装备的职业限制
		 *
		 * @param itemId int
		 */
		public static function getEquipJob (itemId : int) : Array
		{
			return EquipJob[itemId] || [];
		}
		
		/**
		 * 获取物品/装备名称
		 *
		 * @param itemId int
		 */
		public static function getName (itemId : int) : String
		{
			return Items[itemId] ? Items[itemId][0] : \"\";
		}
		 
		/**
		 * 获取物品/装备描述
		 *
		 * @param itemId int
		 */
		public static function getDescription (itemId : int) : String
		{
			var str:String = (Items[itemId] ? Items[itemId][3] : \"\");
			if(str != \"\")
			{
				str = str.replace(reg, regMsg);
			}
			return str;
		}
		
		/**
		 * 获取物品对应的怪物id
		 *
		 * @param itemId int
		 */
		public static function getMonsterId (itemId : int) : int
		{
			return AvatarItemMonster[itemId] || 0;
		}
		
		/**
		 * 获取物品强化等级名称
		 *
		 * @param level int
		 */
		public static function getItemUpgrade (level : int) : String
		{
			return ItemUpgrade[level] || \"\";
		}
		
		/**
		 * 获取物品品质颜色
		 *
		 * @param itemId int
		 */
		public static function getItemColor (itemId : int) : uint
		{
			var quality : int = getItemQualityById(itemId);
			return quality ? colors[quality] : 0xFFFFFF;
		}
		
		/**
		 * 是否装备
		 * @param typeId int
		 */
		public static function isEquip (typeId : int) : Boolean
		{
			return TouKui == typeId || WuQi == typeId || HunQi == typeId || YiFu == typeId || HuFu == typeId || XieZi == typeId;
		}
		
		/**
		 * 是否是钱(包括元宝,铜钱)
		 */
		public static function isMoney (typeId : int) : Boolean
		{
			return (typeId == Coin) || (typeId == Ingot);
		}
		
		/**
		 * 是否是卷轴
		 */
		public static function isReel (typeId : int) : Boolean
		{
			return (typeId == LianDanJuanZhou) || (typeId == LianQiJuanZhou);
		}
		/**
		 * 是否是原石
		 */
		public static function isYuanShi (typeId : int) : Boolean
		{
			return typeId == YuanShi;
		}
		
		/**
		 * 是否可以堆叠 
		 */
		public static function isStack (typeId : int) : Boolean
		{
			return typeId > 10000;
		}
		
	}
}
";

file_put_contents($desc_dir."ItemType.as", addons().$str);
file_put_contents($desc_dir."source/ItemTypeData.as", addons()."package com.assist.server.source
{
	public class ItemTypeData
	{
		// type_id : [name, max_repeat_num]
		public static const Types : Object = {
".$hash0."
		};
		
		/**
		 * {
		 * 	item_id : [
		 * 		name, type_id, usage, description, quality, require_level,
		 * 		attack, attack_up,
		 * 		defense, defense_up,
		 * 		stunt_attack, stunt_attack_up,
		 * 		stunt_defense, stunt_defense_up,
		 * 		magic_attack, magic_attack_up,
		 * 		magic_defense, magic_defense_up, 
		 * 		health, health_up,
		 * 		speed, speed_up,
		 * 		strength, agile, intellect, price, ingot, item_card_id, item_card_num
		 * 	],
		 * 	...
		 * }
		 */
		public static const Items : Object = {
".$hash."
		};
		
		// quality : name
		public static const Qualitys : Object = {
".$hash1."
		};
		
		// item_id : role_job_id
		public static const EquipJob : Object = {
".$hash2."
		};
		
		// item_id : monster_id
		public static const AvatarItemMonster : Object = {
".$hash3."
		};
		
		// level : name
		public static const ItemUpgrade : Object = {
".$hash4."
		};
		
		/*
		public static function init () : void
		{
			ItemType.Types = Types;
			ItemType.Items = Items;
			ItemType.Qualitys = Qualitys;
			ItemType.EquipJob = EquipJob;
			ItemType.AvatarItemMonster = AvatarItemMonster;
			ItemType.ItemUpgrade = ItemUpgrade;
		}
		*/
	}
}
");

echo "[data]  item_type [Done]\n";
?>