<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### fate

$list = $dbh->query("select `id`, `type`,`name`, `sign`, `request_level`, `fate_quality_id`, `description`, `war_attribute_type_id`, `war_attribute_type_id2`, `actived_fate_id`, `actived_fate_id2`, `need_actived`, `exchange_require` from `fate`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\", \"".$item["sign"]."\", ".$item["fate_quality_id"].",\"".$item["description"]."\", ".$item["war_attribute_type_id"].",".$item["war_attribute_type_id2"].",".$item["actived_fate_id"].",".
	$item["actived_fate_id2"].",".$item["need_actived"].",".$item["exchange_require"].",".$item["type"]."]";
}

$hash4 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash4 != "") {
		$hash4 .= ",\n";
	}
	$request_level = $item["request_level"];
	
	if($request_level > 0)
	{
		$hash4 .= "			".$item["id"]." : ".$item["request_level"];
	}
}

### fate_npc

$list = $dbh->query("select `id`, `name`, `fees` from `fate_npc`");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["fees"]."]";
}

### fate_level

$list = $dbh->query("select `fate_id`, `level`, `addon_value`, `addon_value2` from `fate_level`");

$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			[".$item["fate_id"].", ".$item["level"].", ".$item["addon_value"].", ".$item["addon_value2"]."]";
}

### fate_quality

$list = $dbh->query("select `id`, `name`, `sale_price`, `experience` from `fate_quality`");

$hash3 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["sale_price"].", ".$item["experience"]."]";
}

### war_attribute_type

$list = $dbh->query("select `id`, `sign`, `name` from `war_attribute_type`");

$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($constant != "") {
		$constant .= "\n";
	}
	
	$signs = explode("_", $item["sign"]);
	$str = "";
	for ($j = 0; $j < count($signs); $j++) {
		$str .= strtoupper(substr($signs[$j], 0, 1)).substr($signs[$j], 1);
	}
	
	$constant .= "		// ".$item["name"]."\n";
	$constant .= "		public static const ".$str." : int = ".$item["id"].";";
}

### 类

$str = "package com.assist.server
{
	public class FateType
	{
		// fate_id : [name, sign, fate_quality_id, description]
		private static var _Fates : Object = null;
		
		public static function get Fates () : Object
		{
			if (_Fates == null) throw new Error(\"还未赋值！\");
			
			return _Fates;
		}
		
		public static function set Fates (value : Object) : void
		{
			if (_Fates != null) throw new Error(\"非法赋值\");
			
			_Fates = value;
		}
		
		// fate_npc_id : [name, fees]
		private static const FateNPCs : Object = {
".$hash1."
		};
		
		// [fate_id, level, addon_value]
		private static var _FateLevel : Object = null;
		
		public static function get FateLevel () : Object
		{
			if (_FateLevel == null) throw new Error(\"还未赋值！\");
			
			return _FateLevel;
		}
		
		public static function set FateLevel (value : Object) : void
		{
			if (_FateLevel != null) throw new Error(\"非法赋值\");
			
			_FateLevel = value;
		}
		
		// fate_quality_id : [name, sale_price, experience]
		private static const FateQuality : Object = {
".$hash3."
		};
		
".$constant."
		// 命格兑换等级
		private static const FateRequestLevel : Object =
		{
".$hash4."
		}
		
		/**
		 * 获取猎命名称
		 *
		 * @param id int
		 */
		public static function getFateName (fateId : int) : String
		{
			if(fateId == 9999) return \"金龙蛋\";
			return Fates[fateId] ? Fates[fateId][0] : \"\";
		}
		
		/**
		 * 获取猎命标识
		 *
		 * @param id int
		 */
		public static function getFateSign (fateId : int) : String
		{
			return Fates[fateId] ? Fates[fateId][1] : \"\";
		}
		
		/**
		 * 获取猎命品质
		 *
		 * @param fate_id int
		 */
		public static function getFateQuality (fateId : int) : int
		{
			return Fates[fateId] ? Fates[fateId][2] : 0;
		}
		
		/**
		 * 获取猎命描述
		 *
		 * @param fateId int
		 */
		public static function getFateDescription (fateId : int) : String
		{
			return Fates[fateId] ? Fates[fateId][3] : \"\";
		}
		
		/**
		 * 获取属性类型id
		 *
		 * @param fateId int
		 */
		public static function getWarAttributeType (fateId : int) : int
		{
			return Fates[fateId] ? Fates[fateId][4] : 0;
		}
        
		/**
		 * 是否是战争属性
		 *
		 * @param fateId int
		 */
		public static function isWarAttribute(fateId : int) : Boolean
		{
			var warType : int = FateType.getWarAttributeType(fateId);
			if(isPercentage(warType))
			{
			    return true;
			}
			return false;
		}
		
		/**
		 * 百分比属性
		 *
		 * @param fateId int
		 */
		public static function isPercentage(warType : int) : Boolean
		{
			if(warType == FateType.Hit || 
				warType == FateType.Block ||
				warType == FateType.Dodge ||
				warType == FateType.Critical ||
				warType == FateType.BreakBlock ||
				warType == FateType.BreakCritical ||
				warType == FateType.Kill ||
				warType == FateType.DecKill)
			{
				return true;
			}
			
			return false;
		}
        
		/**
		 * 需要激活的列表
		 *
		 * @param fateId int
		 */
		public static function getActivedList (fateId : int) : Array
		{
		       var list : Array = [];
		       if(Fates[fateId] != null)
		       {
		           var value : int = Fates[fateId][6];
			       if(value > 0)
			       {
				        list.push(value);
			       }
			   
			       value = Fates[fateId][7];
			       if(value > 0)
			       {
				        list.push(value);
			       }
		       }
		        
		       return list;
		}
		
		/**
		 * 是否需要激活的命格id
		 *
		 * @param fateId int
		 */
		public static function getNeedActived (fateId : int) : int
		{
			return Fates[fateId] ? Fates[fateId][8] : 0;
		}
		
		/**
		 * 命格类型 1 命格 2 暗命格
		 *
		 * @param fateId int
		 */
		public static function getFateType (fateId : int) : int
		{
			if(fateId == 9999) return 1;
			return Fates[fateId] ? Fates[fateId][10] : 0;
		}
		
		/**
		 * 获取npc名称
		 *
		 * @param id int
		 */
		public static function getNPCName (id : int) : String
		{
			return FateNPCs[id] ? FateNPCs[id][0] : \"\";
		}
		
		/**
		 * 获取npc费用
		 *
		 * @param id int
		 */
		public static function getNPCFee (id : int) : int
		{
			return FateNPCs[id] ? FateNPCs[id][1] : 0;
		}
		
		/**
		 * 获取猎命最高等级
		 *
		 * @param fateId int
		 */
		public static function getMaxFateLevel (fateId : int) : int
		{
			var len : int = FateLevel.length;
			var maxLevel : int = 0;
			
			for (var i : int = 0; i < len; i++)
			{
				var item : Array = FateLevel[i];
				if (fateId == item[0] && maxLevel < item[1])
				{
					maxLevel = item[1];
				}
			}
			
			return maxLevel;
		}
		
		/**
		 * 获取附加值
		 *
		 * @param fateId int
		 * @param fateLevel int
		 */
		public static function getFateAddonValue (fateId : int, fateLevel : int) : int
		{
			var len : int = FateLevel.length;
			var value : int = 0;
			
			for (var i : int = 0; i < len; i++)
			{
				var item : Array = FateLevel[i];
				if (fateId == item[0] && fateLevel == item[1] && value < item[2])
				{
					value = FateLevel[i][2];
				}
			}
			
			return value;
		}
		
		/**
		 * 获取附加值2
		 *
		 * @param fateId int
		 * @param fateLevel int
		 */
		public static function getFateAddonValue2 (fateId : int, fateLevel : int) : int
		{
			var len : int = FateLevel.length;
			var value : int = 0;
			
			for (var i : int = 0; i < len; i++)
			{
				var item : Array = FateLevel[i];
				if (fateId == item[0] && fateLevel == item[1] && value < item[3])
				{
					value = FateLevel[i][3];
				}
			}
			
			return value;
		}
		
		/**
		 * 获取价格
		 *
		 * @param fateId int
		 */
		public static function getSalePriceByFateId (fateId : int) : int
		{
			var qualityId : int = getFateQuality(fateId);
			return getSalePriceByQualityId(qualityId);
		}
		
		/**
		 * 获取价格
		 *
		 * @param qualityId int
		 */
		public static function getSalePriceByQualityId (qualityId : int) : int
		{
			return FateQuality[qualityId] ? FateQuality[qualityId][1] : 0;
		}
		
		/**
		 * 获取经验
		 *
		 * @param fateId int
		 */
		public static function getExperienceByFateId (fateId : int) : int
		{
			var qualityId : int = getFateQuality(fateId);
			return getExperienceByQualityId(qualityId);
		}
		
		/**
		 * 获取经验
		 *
		 * @param qualityId int
		 */
		public static function getExperienceByQualityId (qualityId : int) : int
		{
			return FateQuality[qualityId] ? FateQuality[qualityId][2] : 0;
		}
		
		/**
		 * 获取命格兑换等级
		 *
		 * @param fateId int
		 */
		public static function fateChengeLevel (fateId : int) : int
		{
			return FateRequestLevel[fateId] || 0
		}

		/**
		 * 获取兑换所需命格碎片
		 *
		 * @param fateId int
		 */
		public static function exchangeRequire (fateId : int) : int
		{
			if(fateId == 9999) return 12;
			return Fates[fateId][9];
		}

		/**
		 * 颜色值
		 */
		private static var colorObj : Object = {
			1 : 0x999999,
			2 : 0x22ac38,
			3 : 0x00aeef,
			4 : 0xff00ff,
			5 : 0xfff100,
			6 : 0xFF0000,
			7 : 0xfff100,
			8 : 0xfff100
		};
		
		/**
		 * 颜色转换
		 */
		public static function getColor (fateId : uint) : uint
		{
			var num : uint = colorObj[fateId];
			return num;
		}
	}
}
";

file_put_contents($desc_dir."FateType.as", addons().$str);
file_put_contents($desc_dir."source/FateTypeData.as", addons()."package com.assist.server.source
{
	public class FateTypeData
	{
		// fate_id : [name, sign, fate_quality_id, description, war_attribute_type_id,
		//            war_attribute_type_id2, actived_fate_id, actived_fate_id2, `need_actived,exchangeRequire, type]
		public static const Fates : Object = {
".$hash."
		};
		
		// [fate_id, level, addon_value]
		public static const FateLevel : Array = [
".$hash2."
		];
		
		/*
		public static function init () : void
		{
			FateType.Fates = Fates;
			FateType.FateLevel = FateLevel;
		}
		*/
	}
}
");
echo "[data] fate_type  [Done]\n";
?>