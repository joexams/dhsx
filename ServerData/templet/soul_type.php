<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### soul_type

$list = $dbh->query("select `id`, `name` from `soul_type`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\"]";
}

### soul_location

$list = $dbh->query("select `id`, `describe` from `soul_location`;");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["describe"]."\"]";
}

### soul_all_type

$list = $dbh->query("select `id`, `soul_type_id`, `soul_location_id`, `name` from `soul_all_type`");

$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [".$item["soul_type_id"].", ".$item["soul_location_id"].", \"".$item["name"]."\"]";
}

### soul_quality

$list = $dbh->query("select `id`, `name` from `soul_quality`;");

$hash3 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$item["id"]." : [\"".$item["name"]."\"]";
}

### soul

$list = $dbh->query("select `id`, `soul_all_type_id`, `soul_quality_id`, `name`, `content`, `saleprice` from `soul`");

$hash4 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash4 != "") {
		$hash4 .= ",\n";
	}
	
	$hash4 .= "			".$item["id"]." : [".$item["soul_all_type_id"].", ".$item["soul_quality_id"].", \"".$item["name"]."\", \"".$item["content"]."\", ".$item["saleprice"]."]";
}

### soul_attribute

$list = $dbh->query("select `id`, `war_attribute_type_id`, `soul_quality_id` from `soul_attribute`");

$hash5 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash5 != "") {
		$hash5 .= ",\n";
	}
	
	$hash5 .= "			".$item["id"]." : [".$item["war_attribute_type_id"].", ".$item["soul_quality_id"]."]";
}

### item_to_soul

$list = $dbh->query("select `item_num`, `soul_id` from `item_to_soul`");

$hash6 = "";
for ($i = 0; $i < count($list); $i++) {
	if ($hash6 != "") {
		$hash6 .= ",\n";
	}
	
	$hash6 .= "			".$list[$i]["soul_id"]." : ".$list[$i]["item_num"];
}

### 类

$str = "package com.assist.server
{
	public class SoulType
	{
		include \"./source/SoulTypeData0.as\";
		
		// id : [name]
		private static const Type : Object = {
".$hash."
		};
		
		// id : [describe]
		private static const Location : Object = {
".$hash1."
		};
		
		// id : [soul_type_id, soul_location_id, name]
		private static const AllType : Object = {
".$hash2."
		};
		
		// id : [name]
		private static const Quality : Object = {
".$hash3."
		};
		
		// id : [soul_all_type_id, soul_quality_id, name, content, saleprice]
		private static var _Soul : Object = null;
		
		public static function get Soul () : Object
		{
			if (_Soul == null) throw new Error(\"还未赋值！\");
			
			return _Soul;
		}
		
		public static function set Soul (value : Object) : void
		{
			if (_Soul != null) throw new Error(\"非法赋值\");
			
			_Soul = value;
		}
		
		// id : [war_attribute_type_id, soul_quality_id]
		private static const Attribute : Object = {
".$hash5."
		};
		
		// soul_id : item_num
		public static var ItemToSoul : Object = {
".$hash6."
		};
		
		//----------------------------------------------------------------------
		//
		//  方法
		//
		//----------------------------------------------------------------------
		
		/**
		 * 灵件类型名
		 * @param typeId int
		 */
		public static function getTypeName (typeId : int) : String
		{
			return Type[typeId] ? Type[typeId][0] : \"\";
		}
		
		/**
		 * 灵件类型id
		 * @param id int
		 */
		public static function getSoulTypeIdByAllTypeId (id : int) : int
		{
			return AllType[id] ? AllType[id][0] : 0;
		}
		
		/**
		 * 灵件子类型名
		 * @param id int
		 */
		public static function getSubTypeNameByAllTypeId (id : int) : String
		{
			return AllType[id] ? AllType[id][2] : \"\";
		}
		
		/**
		 * 灵件子类型名
		 * @param typeId int
		 * 类型id
		 * @param locationId int
		 * 指定位置
		 */
		public static function getSubTypeName (typeId : int, locationId : int) : String
		{
			for (var key : String in AllType)
			{
				if (AllType[key][0] == typeId && AllType[key][1] == locationId)
				{
					return AllType[key][2];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 获取品质名称
		 * @param qualityId int
		 */
		public static function getQualityName (qualityId : int) : String
		{
			return Quality[qualityId] ? Quality[qualityId][0] : \"\";
		}
		
		/**
		 * 获取灵件子类型id
		 * @param soulId int
		 */
		public static function getSoulSubTypeId (soulId : int) : int
		{
			return Soul[soulId] ? Soul[soulId][0] : 0;
		}
		
		/**
		 * 获取灵件品质id
		 * @param soulId int
		 */
		public static function getSoulQualityId (soulId : int) : int
		{
			return Soul[soulId] ? Soul[soulId][1] : 0;
		}
		
		/**
		 * 获取灵件名称
		 * @param soulId int
		 */
		public static function getSoulName (soulId : int) : String
		{
			var str:String = (Soul[soulId] ? Soul[soulId][2] : \"\");
			str = str.split(\"_\")[0];
			return str;
		}
		
		/**
		 * 获取灵件描述内容
		 * @param soulId int
		 */
		public static function getSoulContent (soulId : int) : String
		{
			return Soul[soulId] ? Soul[soulId][3] : \"\";
		}
		
		/**
		 * 获取灵件出售价格
		 * @param soulId int
		 */
		public static function getSoulSalePrice (soulId : int) : int
		{
			return Soul[soulId] ? Soul[soulId][4] : 0;
		}
		
		/**
		 * 获取灵件是属性类型id
		 * @param attributeId int
		 */
		public static function getWarAttributeTypeId (attributeId : int) : int
		{
			return Attribute[attributeId] ? Attribute[attributeId][0] : 0;
		}
		
		/**
		 * 获取灵件属性品质id
		 * @param attributeId int
		 */
		public static function getAttrQualityId (attributeId : int) : int
		{
			return Attribute[attributeId] ? Attribute[attributeId][1] : 0;
		}
		
		/**
		 * 获取指定位置的所有灵件类型
		 *
		 * @param id int
		 * 灵件位置
		 */
		public static function getSubTypeIdListByLocationId (id : int) : Array
		{
			var list : Array = [];
			
			for (var key : String in AllType)
			{
				if (AllType[key][1] == id)
				{
					list.push(key);
				}
			}
			
			return list;
		}
		
		/**
		 * 获取灵件id列表
		 *
		 * @param qualityId int
		 * 品质id
		 * 
		 * @param locationId int
		 * 灵件位置
		 */
		public static function getSoulIdList (qualityId : int, locationId : int) : Array
		{
			var allTypeIdList : Array = getSubTypeIdListByLocationId(locationId);
			var len : int = allTypeIdList.length;
			
			var list : Array = [];
			
			var i : int;
			for (var key : String in Soul)
			{
				for (i = 0; i < len; i++)
				{
					if (Soul[key][0] == allTypeIdList[i] && Soul[key][1] == qualityId)
					{
						list.push({soulId : key, qualityId : qualityId});
					}
				}
			}
			
			return list;
		}
		
		/**
		 * 灵件需要多少物品兑换
		 * @param soulId int
		 */
		public static function getItemNumBySoulId (soulId : int) : int
		{
			return ItemToSoul[soulId] || 0;
		}
	}
}
";

file_put_contents($desc_dir."SoulType.as", addons().$str);
file_put_contents($desc_dir."source/SoulTypeData.as", addons()."package com.assist.server.source
{
	public class SoulTypeData
	{
		// id : [soul_all_type_id, soul_quality_id, name, content, saleprice]
		public static const Soul : Object = {
".$hash4."
		};
	}
}
");

echo "[data] soul_type  [Done]\n";
?>