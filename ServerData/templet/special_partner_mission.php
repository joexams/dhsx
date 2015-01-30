<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### special_partner_mission

$list = $dbh->query("
SELECT
  id,
  role_id,
  mission_id,
  monster_id,
  require_level,
  require_item_id,
  require_item_amount,
  award_power,
  award_fame,
  award_item_id,
  award_item_amount,
  award_experience,
  item_price
FROM special_partner_mission;");

$hash = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["role_id"].", ".$item["mission_id"].", ".$item["monster_id"].", ".$item["require_level"].", "
    .$item["require_item_id"].", ".$item["require_item_amount"].", ".$item["award_power"].", ".$item["award_fame"].", ".$item["award_item_id"].", "
    .$item["award_item_amount"].", ".$item["award_experience"].", ".$item["item_price"]."]";
	

}

$list1 = $dbh->query("
		     SELECT
		     role_id,
		     req_level
		     FROM special_partner;");

$hash1 = "";

for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	$hash1 .= "			".$item["role_id"]." : [".$item["req_level"]."]";

}

### 类

$str = "package com.assist.server
{
	public class SpecialPartnerType
	{
		// id : [role_id, mission_id, monster_id, require_level, require_item_id, require_item_amount, award_power, award_fame, award_item_id, award_item_amount, award_experience, item_price]
		private static const Data : Object = {
".$hash."
		};

		//对应role_id需要的玩家等级
		private static const RequireLevel : Object =
		{
".$hash1."
		}
        /**
		 * 获取副本ID
		 * @param id int
		 */
		public static function getMissionId (id : int) : int
		{
			return Data[id] ? Data[id][1] : 0;
		}
        
		/**
		 * 获取怪物ID
		 * @param id int
		 */
		public static function getMonsterId (id : int) : int
		{
			return Data[id] ? Data[id][2] : 0;
		}
		
		/**
		 * 获取需求等级
		 * @param id int
		 */
		public static function getRequireLevel (id : int) : int
		{
			return Data[id] ? Data[id][3] : 0;
		}
		
		/**
		 * 获取需求物品ID
		 * @param id int
		 */
		public static function getRequireItemId (id : int) : int
		{
			return Data[id] ? Data[id][4] : 0;
		}
		
		/**
		 * 获取需求物品数量
		 * @param id int
		 */
		public static function getRequireItemAmount (id : int) : int
		{
			return Data[id] ? Data[id][5] : 0;
		}
		
		/**
		 * 获取奖励的体力
		 * @param id int
		 */
		public static function getAwardPower (id : int) : int
		{
			return Data[id] ? Data[id][6] : 0;
		}
		
		/**
		 * 获取奖励的声望
		 * @param id int
		 */
		public static function getAwardFame (id : int) : int
		{
			return Data[id] ? Data[id][7] : 0;
		}
		
		/**
		 * 获取奖励的物品ID
		 * @param id int
		 */
		public static function getAwardItemId (id : int) : int
		{
			return Data[id] ? Data[id][8] : 0;
		}
		
		/**
		 * 获取奖励的物品数量
		 * @param id int
		 */
		public static function getAwardItemAmount (id : int) : int
		{
			return Data[id] ? Data[id][9] : 0;
		}
		
		/**
		 * 获取奖励的经验
		 * @param id int
		 */
		public static function getAwardExperience (id : int) : int
		{
			return Data[id] ? Data[id][10] : 0;
		}
        
		/**
		 * 获取需求物品对应的元宝单价
		 * @param id int
		 */
		public static function getRequireItemPrice (id : int) : int
		{
			return Data[id] ? Data[id][11] : 0;
		}
		
		/**
		 * 根据roleid 获取对应的玩家邀请需求等级
		 */
		public static function getInviteLevel(id:int) : int
		{
			return RequireLevel[id] ? RequireLevel[id][0] : 0;
		}
	}
}
";

file_put_contents($desc_dir."SpecialPartnerType.as", addons().$str);

echo "[data] special_partner_mission [Done]\n";
?>