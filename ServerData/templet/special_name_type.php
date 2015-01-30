<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### special_name

$list = $dbh->query("
	select
		`id`, `sign`,`name`, `content`,`type`,`health`,`award_coins`,`award_fame`
	from
		`title`
");




$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["name"]."\",\"".$item["content"]."\",\"".$item["type"]."\",".$item["health"].",".$item["award_coins"].",".$item["award_fame"]."]";
}

### 类

$str = "package com.assist.server
{
	public class SpecialNameType
	{
		// 称号 id ：[标识 名字 说明 类型 生命加成 铜钱奖励 声望奖励]
		private static const SpecialName : Object = {
".$hash."
		};
		
		/**
		 * 称号id获取对应标识
		 */
		public static function titleSign(id : int) : String
		{
			return SpecialName[id] ? SpecialName[id][0] : \"\";
		}
		
		/**
		 * 称号id获取对应名字
		 */
		public static function titleName(id : int) : String
		{
			return SpecialName[id] ? SpecialName[id][1] : \"\";
		}
		
		/**
		 * 称号id获取对应获取说明
		 */
		public static function titleContent(id : int) : String
		{
			return SpecialName[id] ? SpecialName[id][2] : \"\";
		} 
		
		
		/**
		 * 称号id获取对应类型
		 */
		public static function titleType(id:int) : String
		{
			return SpecialName[id] ? SpecialName[id][3] : \"\";
		}
		
		/**
		 * 称号id获取对应生命加成
		 */
		public static function titleAdd(id:int) : int
		{
			return SpecialName[id] ? SpecialName[id][4] : 0;
		}
		
		/**
		 * 称号id获取对应铜钱奖励
		 */
		public static function awardCoins(id:int) : int
		{
			return SpecialName[id] ? SpecialName[id][5] : 0;
		}
		
		/**
		 * 称号id获取对应声望奖励
		 */
		public static function awardFame(id:int) : int
		{
			return SpecialName[id] ? SpecialName[id][6] : 0;
		}
	}
}
";

file_put_contents($desc_dir."SpecialNameType.as", addons().$str);

echo "[data] SpecialNameType [Done]\n";
?>