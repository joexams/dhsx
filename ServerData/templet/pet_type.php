<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$list = $dbh->query('SELECT * FROM `pet_animal` order by lv');

$pet_data = "";
$stunt_attack = 0;
$critical = 0;
$dodge = 0;
$block = 0;
$break_critical = 0;
$break_block = 0;
$hit = 0;
$kill = 0;
$attack_base = 0;

$health_base = 0;
$defense_base = 0;

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	$item["attack"] = $item["attack"]/1000;
	$item["stunt_attack"] = $item["stunt_attack"]/1000;
	$item["health"] = $item["health"]/1000;
	$item["defense"] = $item["defense"]/1000;
	
	$item["critical"] = $item["critical"]/10;
	$item["dodge"] = $item["dodge"]/10;
	$item["block"] = $item["block"]/10;
	$item["break_critical"] = $item["break_critical"]/10;
	$item["break_block"] = $item["break_block"]/10;
	$item["hit"] = $item["hit"]/10;
	$item["kill"] = $item["kill"]/10;
	
	$stunt_attack = $stunt_attack + $item["stunt_attack"];
	$critical = $critical + $item["critical"];
	$dodge = $dodge + $item["dodge"];
	$block = $block + $item["block"];
	$break_critical = $break_critical + $item["break_critical"];
	$break_block = $break_block + $item["break_block"];
	$hit = $hit + $item["hit"];
	$kill = $kill + $item["kill"];

	
	if($pet_data != "")
	{
		$pet_data = $pet_data.",\n			";
	}
	$pet_data = $pet_data.$item["lv"].":['".$item["name"]."', ".$item["player_lv"].", ".$item["attack"].", ".$attack_base.", ".$item["defense"].", ".$defense_base.", ".$stunt_attack.", ".$critical.", ".$dodge.", ".$block.", ".$break_critical.", ".$break_block.", ".$hit.", ".$kill.", ".$item["health"].", ".$health_base."]";
	

	$attack_base = $attack_base + 10*($item["attack"]);	
	$defense_base = $defense_base + 10*($item["defense"]);	
	$health_base = $health_base + 10*($item["health"]);	
}

$pet_data = 
"		/***
		0.name           char(30)    宠物阶段名字		
		1.player_lv      int(11)    需要的玩家等级
		2.attack         int(11)    每个小阶段攻击加值
		3.attack_base    		//本级0阶攻击加值	
		4.defense   每个小阶段防御加值
		5.defense_base   //本级0阶防御加值	
		
		6.stunt_attack   int(11)    绝技攻击(每个等级获取)
		7.critical       int(11)    暴击(每个等级获取)   %    
		8.dodge          int(11)    闪避(每个等级获取)   %    
		9.block          int(11)    档格(每个等级获取)   %   
		10.break_critical int(11)    韧性(每个等级获取)   %   
		11.break_block    int(11)    破击(每个等级获取)   %   
		12.hit            int(11)    命中(每个等级获取)   %   
		13.kill           int(11)    必杀(每个等级获取)   %   
		
		14.defense   每个小阶段生命加值
		15.defense_base   //本级0阶生命加值	
		***/	
		public static const PetData : Object ={
			".$pet_data."
		}";

//---------------------------------------------------------------
$pet_Ingot = "";
$pet_Coin = "";
$list = $dbh->query('SELECT * FROM `pet_animal_award` order by id');
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if($item["ingot"] > 0)
	{
		if($pet_Ingot != "")
		{
			$pet_Ingot = $pet_Ingot.",\n			";
		}
		$pet_Ingot = $pet_Ingot.$item["lv"].":[".$item["ingot"].", ".$item["exp"]."]";
	}
	if($item["coin"] > 0)
	{
		if($pet_Coin != "")
		{
			$pet_Coin = $pet_Coin.",\n			";
		}
		$pet_Coin = $pet_Coin.$item["lv"].":[".$item["coin"].", ".$item["exp"]."]";
	}
}

$pet_Ingot = 
"		/** 喂养元宝价格  lv:[ingot, exp(增加经验)] */
		public static const PetIngot : Object ={
			".$pet_Ingot."
		}";

$pet_Coin = 
"		/** 喂养铜钱价格  lv:[coin, exp(增加经验)] */
		public static const PetCoin : Object ={
			".$pet_Coin."
		}";

//---------------------------------------------------------------
$pet_Exp = "";		
$list = $dbh->query('SELECT * FROM `pet_animal_stage` ORDER BY `pet_animal_lv`, `stage`');
$arrlist = array();
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$arrlist[$item["pet_animal_lv"]."_".$item["stage"]] = $item["exp"];
}
$i = 0;
while($i < 10)
{
	$i++;
	if($pet_Exp != "")
	{
		$pet_Exp = $pet_Exp.",\n			";
	}
	
	$pet_Exp = $pet_Exp.$i.":[0, ";
	
	$k = 0;
	while($k < 10)
	{
		$k++;
		if(array_key_exists($i."_".$k, $arrlist) == false)
		{
			$pet_Exp = $pet_Exp."0, ";
		}
		else
		{
			$pet_Exp = $pet_Exp.$arrlist[$i."_".$k].", ";
		}
	}
	
	$pet_Exp = $pet_Exp."0]";
}

$pet_Exp = 
"
		public static const PetExp : Object ={
			".$pet_Exp."
		}";
		
		
$str = "package com.assist.server
{
	public class PetType
	{
".$pet_data."

".$pet_Ingot."

".$pet_Coin."

".$pet_Exp."


			
		
		/**
		 * 根据龙的等级获取龙的名字
		 */
		public static function getName(petLv:int) : String
		{
			var arr:Array = PetData[petLv]||[];			
			return String(arr[0]);
		}
		
		/**
		 * 根据龙的等级获取需求的玩家等级
		 */
		public static function getReqLv(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[1]);
		}

		/**
		 * 根据龙的等级和阶级获取龙的攻击加值(普通/魔法)
		 */
		public static function getAttack(petLv:int, petStage:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[3]) + int(arr[2])*petStage;
		}
		
		/**
		 * 根据龙的等级和阶级获取龙的防御加值
		 */
		public static function getDefense(petLv:int, petStage:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[5]) + int(arr[4])*petStage;
		}
		
		/**
		 * 根据龙的等级和阶级获取龙的生命加值
		 */
		public static function getLife(petLv:int, petStage:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[15]) + int(arr[14])*petStage;
		}
		
		/**
		 * 根据龙的等级获取龙的绝技攻击加值
		 */
		public static function getStuntAttack(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[6]);
		}
		
		/**
		 * 根据龙的等级获取龙的暴击加值%
		 */
		public static function getCritical(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[7]);
		}
		
		/**
		 * 根据龙的等级获取龙的闪避加值%
		 */
		public static function getDodge(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[8]);
		}
		
		/**
		 * 根据龙的等级获取龙的档格加值%
		 */
		public static function getBlock(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[9]);
		}
		
		/**
		 * 根据龙的等级获取龙的韧性加值%
		 */
		public static function getBreakCritical(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[10]);
		}
		
		/**
		 * 根据龙的等级获取龙的破击加值%
		 */
		public static function getBreakBlock(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[11]);
		}
		
		/**
		 * 根据龙的等级获取龙的命中加值%
		 */
		public static function getHit(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[12]);
		}
		
		/**
		 * 根据龙的等级获取龙的必杀加值%
		 */
		public static function getKill(petLv:int) : int
		{
			var arr:Array = PetData[petLv]||[];			
			return int(arr[13]);
		}
		  
		
		/**
		 * 根据龙的等级获和阶段取龙升级所需要的Exp
		 */
		public static function getExp(petLv:int, petStage:int) : int
		{
			var arr:Array = PetExp[petLv]||[];			
			return int(arr[petStage]);
		}
		
		
		/**
		 * 根据玩家等级获取铜钱喂养价格
		 */
		public static function getCoin(petLv:int) : int
		{
			var arr:Array = PetCoin[petLv]||[];			
			return int(arr[0]);
		}
		/**
		 * 根据玩家等级获取铜钱喂养经验
		 */
		public static function getCoinExp(petLv:int) : int
		{
			var arr:Array = PetCoin[petLv]||[];			
			return int(arr[1]);
		}
		
		/**
		 * 根据玩家等级获取元宝喂养价格
		 */
		public static function getIngot(petLv:int) : int
		{
			var arr:Array = PetIngot[petLv]||[];			
			return int(arr[0]);
		}
		/**
		 * 根据玩家等级获取元宝喂养经验
		 */
		public static function getIngotExp(petLv:int) : int
		{
			var arr:Array = PetIngot[petLv]||[];			
			return int(arr[1]);
		}
		
		/**
		 * 获取宠物的标识
		 * @param level int
		 */
		public static function getSignByLevel (level : int) : String
		{
			var sign : String = \"\";
			switch (level)
			{
				case 1:
				{
					sign = \"YanLong\";
					break;
				}
				case 2:
				{
					sign = \"MuLong\";
					break;
				}
				case 3:
				{
					sign = \"ShuiLong\";
					break;
				}
				case 4:
				{
					sign = \"HuoLong\";
					break;
				}
				case 5:
				{
					sign = \"FengLong\";
					break;
				}
				case 6:
				{
					sign = \"LeiLong\";
					break;
				}
				case 7:
				{
					sign = \"BingLong\";
					break;
				}
				case 8:
				{
					sign = \"FeiLong\";
					break;
				}
				case 9:
				{
					sign = \"HeiLong\";
					break;
				}
				case 10:
				{
					sign = \"JinLong\";
					break;
				}
			}
			
			return sign;
		}
		
		
		
		/**
		 *  根据龙的等级获取龙名字的颜色.
		 */
		/**
		 *  根据龙的等级获取龙名字的颜色.
		 */
		public static function getColor(petLv:int) : int
		{
			var color:int = 0xffffff;
			switch(petLv)
			{
				case 1:
				case 2:
				case 3:
					color = 0x00a0e9;
					break;
				
				case 4:
				case 5:
				case 6:
					color = 0xff00ff;
					break;
				
				case 7:
				case 8:
				case 9:
				case 10:
					color = 0xfff200;
					break;
				
			}
			return color;
		}
		
		public static function isMaxLv(petLv:int, petStar:int):Boolean
		{
			if(petLv==0 && petStar==10)return true;
			petStar++;
			if(petStar > 10)
			{
				petStar = 1;
				petLv++;
			}
			
			var exp:int = getExp(petLv, petStar);
			return exp==0;
		}
		
	}
}




";

file_put_contents($desc_dir."PetType.as", addons().$str);

echo "[data] pet_type [Done]\n";
?>