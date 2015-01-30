<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role_stunt

$list = $dbh->query("select `id`, `sign`, `name`, `ripe_time`, `experience`, `star_level`, `lock`, `herbs_type`, `coin` from `herbs`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\", \"".$item["name"]."\", ".$item["ripe_time"].", ".$item["experience"].", ".$item["star_level"].", ".$item["lock"].", ".$item["herbs_type"].", ".$item["coin"]."]";
}

### 类

$str = "package com.assist.server
{
	public class HerbsType
	{
		// id : [sign, name, ripe_time, experience, start_level, lock, herbs_type, coin]
		private static var Herbs : Object = {
".$hash."
		};
		
		public static function getSign (herbId : int) : String
		{
			return Herbs[herbId] ? Herbs[herbId][0] : \"\";
		}
		
		public static function getName (herbId : int) : String
		{
			return Herbs[herbId] ? Herbs[herbId][1] : \"\";
		}
		
		public static function getRipeTime (herbId : int) : int
		{
			return Herbs[herbId] ? Herbs[herbId][2] : 0;
		}
		
		public static function getExperience (herbId : int) : Number
		{
			return Herbs[herbId] ? Herbs[herbId][3] : 0;
		}
		
		public static function getStarLevel (herbId : int) : int
		{
			return Herbs[herbId] ? Herbs[herbId][4] : 0;
		}
		
		public static function getLock (herbId : int) : int
		{
			return Herbs[herbId] ? Herbs[herbId][5] : 0;
		}
		
		public static function getType (herbId : int) : int
		{
			return Herbs[herbId] ? Herbs[herbId][6] : 0;
		}
		
		public static function getCoin (herbId : int) : int
		{
			return Herbs[herbId] ? Herbs[herbId][7] : 0;
		}
		
		public static function getIdsByLock (lock : int) : Array
		{
			var list : Array = [];
			for (var id : Object in Herbs)
			{
				if (Herbs[id][5] == lock)
				{
					list.push(id as int);
				}
			}
			
			list.sort();
			
			return list;
		}
	}
}
";

file_put_contents($desc_dir."HerbsType.as", addons().$str);

echo "[data]  herbs [Done].\n";
?>