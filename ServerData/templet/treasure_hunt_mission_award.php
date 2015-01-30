<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### treasure_hunt

$list = $dbh->query("
	select
		`th_mission_id`, `th_award_id`, `award_prob`
	from
		`treasure_hunt_mission_award`
");

$list1 = $dbh->query("
	select
		`id`, `name`, `monster`,`ingot_cost`,`health`, `need_exp`
	from
		`treasure_hunt_mission`
");

$list2 = $dbh->query("
	select
		`id`, `name`, `coins`,`power`,`skill`,`fame`,`item_id`,`item_amount`, `exp`
	from
		`treasure_hunt_award`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["th_mission_id"].",".$item["th_award_id"].",".$item["award_prob"]."]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["name"]."\",".$item["monster"].",".$item["ingot_cost"].",".$item["health"].",".$item["need_exp"]."]";
}

$hash2 = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [\"".$item["name"]."\",".$item["coins"].",".$item["power"].",".$item["skill"].",".$item["fame"].",".$item["item_id"].",".$item["item_amount"].",".$item["exp"]."]";
}
### 类

$str = "package com.assist.server
{
	public class TreasureHuntType
	{
		// [关卡id 奖励id 剩余次数]
		private static const MissionAward : Array = [
".$hash."
		];
		// 关卡id : [关卡名称 关卡怪物id 传送所需元宝数 生命值 开启所需经验]
		private static const Mission : Object =
		{
".$hash1."
		}
		
		// 奖励id: [奖励信息 奖励铜钱数 奖励体力 奖励阅历 奖励声望 奖励物品id 物品数量 奖励经验]
		private static const Award : Object = {
".$hash2."
		};
		
		/**
		 * 获取关卡奖励id数组
		 * @param id int
		 */
		public static function getAwardId (id : int) : Array
		{
			var ra : Array = [];
			var len : int = MissionAward.length;
			for(var i : int = 0;i < len;i++)
			{
				if(MissionAward[i][0] == id)
				{
					ra.push(MissionAward[i][1])
				}
			}
			return ra ? ra : [];
		}
		/**
		* 获取关卡奖励次数
		* @param id int
		*/
		public static function getAwardTimes(id : int) : int
		{
			return MissionAward[id] ? MissionAward[id][1] : 0;
		}
		
		/**
		 * 获取关卡名称
		 * @param id int
		 */
		public static function getMissionName (id : int) : String
		{
			return Mission[id] ? Mission[id][0] : \"\";
		}
		/**
		* 获取怪物id
		* @param id int
		*/
		public static function getMonsterId(id : int) : int
		{
			return Mission[id] ? Mission[id][1] : 0;
		}
		
		/**
		* 获取传送所需元宝
		* @param id int
		*/
		public static function getIngot(id : int) : int
		{
			return Mission[id] ? Mission[id][2] : 0;
		}
		
		/**
		* 该关卡boss血量
		* @param id int
		*/
		public static function getHealth(id : int) : int
		{
			return Mission[id] ? Mission[id][3] : 0;
		}
		
		/**
		* 开启需要经验
		* @param id int
		*/
		public static function getNeedExp(id : int) : int
		{
			return Mission[id] ? Mission[id][4] : 0;
		}
		
		/**
		 * 获取奖励名称
		 * @param id int
		 */
		public static function getAwardName (id : int) : String
		{
			return Award[id] ? Award[id][0] : \"\";
		}
		/**
		* 获取铜钱
		* @param id int
		*/
		public static function getCoin(id : int) : int
		{
			return Award[id] ? Award[id][1] : 0;
		}
		/**
		* 获取体力
		* @param id int
		*/
		public static function  getPower(id : int) : int
		{
			return Award[id] ? Award[id][2] : 0;
		}
		/**
		* 获取阅历
		* @param id int
		*/
		public static function getSkill(id : int) : int
		{
			return Award[id] ? Award[id][3] : 0;
		}
		/**
		* 获取声望 
		* @param id int
		*/
		public static function getFame(id : int) : int
		{
			return Award[id] ? Award[id][4] : 0;
		}
		
		/**
		* 奖励物品id
		* @param id int
		*/
		public static function getItemId(id : int) : int
		{
			return Award[id] ? Award[id][5] : 0;
		}
		
		/**
		* 奖励物品数量
		* @param id int
		*/
		public static function getItemCount(id : int) : int
		{
			return Award[id] ? Award[id][6] : 0;
		}
		
		/**
		* 奖励寻宝经验
		* @param id int
		*/
		public static function getExp(id : int) : int
		{
			return Award[id] ? Award[id][7] : 0;
		}
	}
	
	
}
";
file_put_contents($desc_dir."TreasureHuntType.as", addons().$str);
print repeat("[data] treasure_hunt_type", 75, ".")."[Done].\n";
?>