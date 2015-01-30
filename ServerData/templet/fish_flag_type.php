<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### fish_flag

$list = $dbh->query("
	select
		`id`, `sign`, `name`
	from
		`fish_flag`
");

$list1 = $dbh->query("
	select
		`id`, `flag_id`,`flag_level`,`blow_state`,`skill`,`fame`,`state_point`,`xian_ling`,`pearl`,`ling_ye`,`coins`
	from
		`fish_flag_award`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\"]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [".$item["flag_id"].",".$item["flag_level"].",".$item["blow_state"].",".$item["skill"].",".$item["fame"].",".$item["state_point"].",".$item["xian_ling"].",".$item["pearl"].",".$item["ling_ye"].",".$item["coins"]."]";
}


### 类

$str = "package com.assist.server
{
	public class FindImmortalType
	{
		// [id : 标识 名字 
		private static const Fish : Object = {
".$hash."
		};
		
		// id : [鱼旗id 鱼旗等级  状态 0-未吹龙鱼, 1-吹龙鱼失败 阅历 声望 境界点 仙令 元神珠]
		private static const FishData : Object =
		{
".$hash1."
		}
		
		/**
		 * 获取龙鱼标识
		 */
		public static function getFishSign(id:String) : String
		{
			return Fish[id] ? Fish[id][0] : \"\";
		}
		
		/**
		 * 获取龙鱼名字
		 */
		public static function getFishName(id : int) : String
		{
			return Fish[id] ? Fish[id][1] : \"\";
		}
		
		/**
		 * 获取鱼旗id
		 */
		public static function getFishId (id : int) : int
		{
			
			return FishData[id] ? FishData[id][0] : 0;
		}
		
		/**
		* 获取鱼旗等级
		* @param id int
		*/
		public static function getFishLevel(id : int) : int
		{
			return FishData[id] ? FishData[id][1] : 0;
		}
		
		/**
		 * 获取鱼旗状态
		 * @param id int
		 */
		public static function getFishState (id : int) : int
		{
			return FishData[id] ? FishData[id][2] : 0;
		}
		/**
		* 获取奖励的阅历
		* @param id int
		*/
		public static function getSkill(id : int) : int
		{
			return FishData[id] ? FishData[id][3] : 0;
		}
		
		/**
		* 获取奖励的声望
		* @param id int
		*/
		public static function getFame(id : int) : int
		{
			return FishData[id] ? FishData[id][4] : 0;
		}
		
		/**
	         * 获取奖励的境界点
		 */
		public static function getPoint(id : int) : int
		{
			return FishData[id] ? FishData[id][5] : 0;
		}
		/**
		*获取奖励的仙令
		*/
		public static function getLing(id : int) : int
		{
			return FishData[id] ? FishData[id][6] : 0;
		}
		
		/**
		 * 获取元神珠
		*/
		public static function getPearl(id : int) : int
		{
			return FishData[id] ? FishData[id][7] : 0;
		}
		
		/**
		* 获得灵液
		*/
		public static function getLingYe(id : int) : int
		{
			return FishData[id] ? FishData[id][8] : 0;
		}
		
		/**
		* 获得铜钱
		*/
		public static function getCoins(id : int) : int
		{
			return FishData[id] ? FishData[id][9] : 0;
		}
	}
}
";

file_put_contents($desc_dir."FindImmortalType.as", addons().$str);

echo "[data] findImmortalType [Done]\n";
?>