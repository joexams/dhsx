<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list1 = $dbh->query("
SELECT
   *
FROM immortal_art_type
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		".$item["id"]." : [\"".$item["name"]."\", \"".$item["sign"]."\"]";
}

$list2 = $dbh->query("
SELECT
   *
FROM immortal_art_level_data
");

$hash2 = "";
for($i = 0; $i < count($list2); $i++){
	$item = $list2[$i];
	if($hash2 != ""){
		$hash2 .= ",\n";
	}
	$hash2 .= "		[".$item["immortal_art_id"].", ".$item["level"].", ".
		$item["coins"].", ".$item["ba_xian_ling"].", ".$item["value"]."]";
}


### 类

$str = "package com.assist.server
{
	public class ImmortalArtType
	{
		//id，名称，标识
		private static var ImmortalTypeData : Object = {
			".$hash1."
		};
		
		//id,等级，需要铜钱，需要八仙令，增加值
		private static var ImmortalLevelData : Array = [
			".$hash2."
		];

		/**
		*根据id获取名称
		*@param id : 奇术id
		*/
		public static function getName(id : int) : String
		{
			return ImmortalTypeData[id] ? ImmortalTypeData[id][0] : \"\";
		}

		/**
		*根据id获取标识
		*@param id : 奇术id
		*/
		public static function getSing(id : int) : String
		{
			return ImmortalTypeData[id] ? ImmortalTypeData[id][1] : \"\";
		}

		/**
		*根据id和等级获取当前等级数据
		*@param id : 奇术id
		*@param level : 奇术等级
		*/
		public static function getLevelData(id : int, level : int) : Object
		{
			for each(var list : Array in ImmortalLevelData)
			{
				var obj : Object = {};
				obj.research_id = id;
				obj.name = getName(id);
				obj.content = \"每级增加\" + obj.name +getProperty(id);
				obj.level = level;
				obj.last_level = level + 1;
				obj.player_level = level + 1;
				if(id == list[0] && level + 1 == list[1])
				{
					obj.skill = 0;
					obj.coins = list[2];
					obj.bxl = list[3];
					return obj;
				}
			}
			return obj;
		}

		/**
		*获取奇术每级增加属性
		*@param id : 奇术id
		*/
		public static function getProperty(id : int) : int
		{
			for each(var list : Array in ImmortalLevelData)
			{
				if(id == list[0] && list[1] == 1)
				{
					return list[4];
				}
			}
			return 0;
		}
	}
}
";

file_put_contents($desc_dir."ImmortalArtType.as", addons().$str);

echo("[data] ImmortalArtType DONE\n");
?>