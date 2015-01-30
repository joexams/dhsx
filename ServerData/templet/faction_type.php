<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### camp

$list = $dbh->query("select `id`, `sign`, `name` from `camp`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["sign"].", \"".$item["name"]."\"]";
	
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### faction_job

$list = $dbh->query("select `id`, `sign`, `name` from `faction_job`");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["sign"]."\", \"".$item["name"]."\"]";
}

### faction_level

$hash2 = "";
/*
$list = $dbh->query("select `id`, `sign`, `faction_level_name` from `faction_level`");

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [\"".$item["sign"]."\", \"".$item["faction_level_name"]."\"]";
}
*/


### faction_god_offerings

$hash3 = "";

$list = $dbh->query("
	select
		`id`, `name`, `ingot`, `exp`, `fame`, `blessing_count`,
		`vip_level`, `skill`
	from
		`faction_god_offerings`
");

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["ingot"].", ".$item["exp"].", ".$item["fame"].", ".$item["blessing_count"].", ".$item["vip_level"].", ".$item["skill"]."]";
}

### 类

$str = "package com.assist.server
{
	public class FactionType
	{
        // 格式 => id : [sign, name]
		private static const Camps : Object = {
".$hash."
		};
		
".$constant."
		
		// id : [sign, name]
		private static const FactionJobs : Object = {
".$hash1."
		};
		
		// id : [sign, faction_level_name]
		private static const FactionLevels : Object = {
".$hash2."
		};
		
		// id : [name, ingot, exp, fame, blessing_count, vip_level, skill]
		private static const GodOfferings : Object = {
".$hash3."
		};
		
		/**
		 * 阵营id
		 * 
		 * @param sign String
		 */
		public static function campId (sign : String) : int
		{
			var tempId : int;
			
			for (var id : Object in Camps)
			{
				var item : Object = Camps[id];
				if (item[0] == sign)
				{
					tempId = id as int;
					break;
				}
			}
			
			return tempId;
		}
		
		/**
		 * 阵营标识
		 * 
		 * @param campId int
		 */
		public static function campSign (campId : int) : String
		{
			var sign : String;
			for (var id : Object in Camps)
			{
				if (id == campId)
				{
					sign = Camps[id][0];
					break;
				}
			}
			
			return sign;
		}
		
		/**
		 * 阵营名称
		 * 
		 * @param campId int
		 */
		public static function campName (campId : int) : String
		{
			var name : String = \"\";
			
			if (hasCamp(campId))
			{
				name = Camps[campId][1];
			}
			
			return name;
		}
		
		/**
		 * 已选择阵营
		 * 
		 * @param campId int
		 */
		public static function hasCamp (campId : int) : Boolean
		{
			var sign : String = campSign(campId);
			
			return sign && sign != WeiXuanZe && sign != WeiKaiFang;
		}
		
		//----------------------------------------------------------------------
		//
		//  帮派祭神相关
		//
		//----------------------------------------------------------------------
		
		/**
		 * 获取祭神香名称
		 *
		 * @param offerId int
		 */
		public static function getOfferName (offerId : int) : String
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][0] : \"\";
		}
		
		/**
		 * 获取祭神元宝数
		 *
		 * @param offerId int
		 */
		public static function getOfferIngot (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][1] : 0;
		}
		
		/**
		 * 获取祭神经验值
		 *
		 * @param offerId int
		 */
		public static function getOfferExp (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][2] : 0;
		}
		
		/**
		 * 获取祭神声望
		 *
		 * @param offerId int
		 */
		public static function getOfferFame (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][3] : 0;
		}
		
		/**
		 * 获取祭神次数
		 *
		 * @param offerId int
		 */
		public static function getOfferBlessingCount (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][4] : 0;
		}
		
		/**
		 * 获取祭神需求vip等级
		 *
		 * @param offerId int
		 */
		public static function getOffsetVIPLevel (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][5] : 0;
		}
		
		/**
		 * 获取祭神消耗的阅历
		 *
		 * @param offerId int
		 */
		public static function getOffsetSkill (offerId : int) : int
		{
			return GodOfferings[offerId] ? GodOfferings[offerId][6] : 0;
		}
	}
}
";

file_put_contents($desc_dir."FactionType.as", addons().$str);

echo "[data] faction_type  [Done]\n";
?>