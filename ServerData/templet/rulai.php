<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### rulai

$list = $dbh->query("
	select
		`level`, `name`, `experience`,`lift_fame_prob_1`,`lift_fame_prob_2`,`lift_fame_prob_3`
	from
		`rulai_attr`
");

$list1 = $dbh->query("
	select
		`id`, `name`, `skill`,`ingot`,`fame`,`incense`,`vip_level`
	from
		`rulai_incense_attr`
");



$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["level"]." : [\"".$item["name"]."\",".$item["experience"].",".$item["lift_fame_prob_1"].",".$item["lift_fame_prob_2"].",".$item["lift_fame_prob_3"]."]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["name"]."\",".$item["skill"].",".$item["ingot"].",".$item["fame"].",".$item["incense"].",".$item["vip_level"]."]";
}



### 类

$str = "package com.assist.server
{
	public class RuLaiType
	{
		// 如来等级对应声望百分比 如来等级 ：[名称 每个等级升级需要的经验 上香类型1声望提升百分比 上香类型2声望提升百分比 上香类型3声望提升百分比]
		private static const Percent : Object = {
".$hash."
		};
		
		// 相应的香类型 相应信息 id : [名称 需要消耗的阅历数量 需要消耗的元宝数量 奖励的声望值 增加的香火值 需要的VIP等级]
		private static const Incense : Object =
		{
".$hash1."
		}
		
		/**
		 * 如来等级获取对应名字
		 */
		public static function percentName(level : int) : String
		{
			return Percent[level] ? Percent[level][0] : \"\";
		}
		
		/**
		 * 每个等级升级需要的经验
		 */
		public static function percentExperience(level : int) : int
		{
			return Percent[level] ? Percent[level][1] : 0;
		}
		
		/**
		 * 上香类型 声望提升百分比
		 */
		public static function percentFame(level : int,id : int) : int
		{
			return Percent[level] ? Percent[level][1 + id] : 0;
		} 
		
		
		/**
		 * 香的id获取香的名字
		 */
		public static function incenseName(id:int) : String
		{
			return Incense[id] ? Incense[id][0] : \"\";
		}
		
		/**
		 * 香的id需要消耗的阅历数量
		 */
		public static function skillNeed(id:int) : int
		{
			return Incense[id] ? Incense[id][1] : 0;
		}
		
		/**
		 * 香的id需要消耗的元宝数量
		 */
		public static function ingotNeed(id:int) : int
		{
			return Incense[id] ? Incense[id][2] : 0;
		}
		
		/**
		 * 香的id奖励的声望值
		 */
		public static function fameAward(id:int) : int
		{
			return Incense[id] ? Incense[id][3] : 0;
		}
		
		/**
		 * 香的id增加的香火值
		 */
		public static function incenseAward(id:int) : int
		{
			return Incense[id] ? Incense[id][4] : 0;
		}
		
		/**
		 * 香的id需要的vip等级
		 */
		public static function vipNeed(id:int) : int
		{
			return Incense[id] ? Incense[id][5] : 0;
		}
		
	}
}
";

file_put_contents($desc_dir."RuLaiType.as", addons().$str);

echo "[data] ruLaiType [Done]\n";
?>