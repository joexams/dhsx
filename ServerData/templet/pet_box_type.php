<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### online_shop

$list = $dbh->query("
	select
		`id`, `name`, `description`
	from
		`pet`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\",\"".$item["description"]."\"]";
}

### 类

$str = "package com.assist.server
{
	public class PetBoxType
	{
		// [id : 名字， 说明]
		private static const petInfo : Object = {
".$hash."
		};
		
		public static const cat : String = \"GenSuiCat\";
		public static const chicken : String = \"GenSuiChicken\";
		public static const death : String = \"GenSuiDeath\";
		public static const ghost : String = \"GenSuiGhost\";
		public static const heiheipumpkin : String = \"GenSuiHeiHeiPumpkin\";
		public static const oopumpkin : String = \"GenSuiOOPumpkin\";
		public static const bimeng : String = \"GenSuiBiMeng\";
		public static const milu : String = \"MiLu\";
		public static const nianShou : String = \"nianshou\";
		
		/**
		 * 宠物标识
		 */
		private static const petSignData : Object = 
			{
				2 : cat,
				3 : chicken,
				5 : death,
				6 : ghost,
				7 : heiheipumpkin,
				8 : oopumpkin,
				9 : bimeng,
				10 : milu,
				11 : nianShou
			}
			
		/**
		 * 宠物列表
		 */
		private static var _owdPetList : Array = [];
		
		/**
		 * 获取自己宠物列表
		 */
		public static function set owdPetList (list : Array) : void
		{
			_owdPetList = list;
		}
		
		/**
		 * 获取自己宠物列表
		 */
		public static function get owdPetList () :Array
		{
			return _owdPetList;
		}
		
		/**
		 * 是否拥有该宠物
		 */
		public static function isOwdPet (petSign : String) : Object
		{
			var len : int = _owdPetList.lenght;
			for(var i : int = 0; i < len; i++)
			{
				var obj : Object = _owdPetList[i];
				if(obj.petSign == petSign)
				{
					return obj;
				}
			}
			return null;
		}
		
		/**
		 * 获取物品列表数量
		 */
		public static function getPetName(petId : int) : String
		{
			return petInfo[petId] ? petInfo[petId][0] : \"\";
		}
		
		/**
		 * 获取类别列表
		 */
		public static function getPetDescription (petId : int) : String
		{
			return petInfo[petId] ? petInfo[petId][1] : \"\";
		}
		
		/**
		 * 获取类别列表
		 */
		public static function getPetSign (petId : int) : String
		{
			return petSignData[petId] ? petSignData[petId] : \"\";
		}
	}
}
";

file_put_contents($desc_dir."PetBoxType.as", addons().$str);

echo "[data]  PetBoxType [Done].\n";
?>