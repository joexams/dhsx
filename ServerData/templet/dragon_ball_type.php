<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$list = $dbh->query("SELECT * FROM dragonball_buff");

$hash1 = "";
$property1 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	if ($property1 != "") {
		$property1 .= "\n";
	}
	$hash1 .= "			".$item["id"]." : ['".$item["sign"]."', '".$item["name"]."']";
	$property1 .= "		public static const ".$item["sign"].":String = '".$item["sign"]."';";
}

$list = $dbh->query("SELECT * FROM dragonball_effect");

$hash2 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .= "			".$item["id"]." : ['".$item["name"]."']";
}

$hash21 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash21 != "") {
		$hash21 .= ",\n";
	}
	$hash21 .= "			".$item["id"]." : ['".$item["sign"]."']";
}

$hash22 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash22 != "") {
		$hash22 .= "\n";
	}
	$hash22 .= "			static public const ".$item["sign"] . ": String = ". "\"". $item["sign"] . "\";";
}

$list = $dbh->query("SELECT * FROM dragonball_quality");

$hash3 = "";
$property3 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	if ($property3 != "") {
		$property3 .= "\n";
	}
	$hash3 .= "			".$item["id"]." : ['".$item["sign"]."', '".$item["name"]."',".$item["dragonball_init_exp"].",".$item["price"]."]";
	$property3 .= "		public static const ".$item["sign"].":String = '".$item["sign"]."';";
}

$list = $dbh->query("SELECT * FROM dragonball_upgrade_info");
$hash4 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash4 != "") {
		$hash4 .= ",\n";
	}
	$hash4 .= "			[".$item["quality_id"].",".$item["level2_exp"].", ".$item["level3_exp"].",".$item["level4_exp"].",".$item["level5_exp"].",".$item["level6_exp"].",".$item["level7_exp"].",".$item["level8_exp"].
	",".$item["level9_exp"].",".$item["level10_exp"].",".$item["mergelevel1_costs"].",".$item["mergelevel2_costs"].",".$item["mergelevel3_costs"].",".$item["mergelevel4_costs"].",".$item["mergelevel5_costs"].
	",".$item["mergelevel6_costs"].",".$item["mergelevel7_costs"].",".$item["mergelevel8_costs"].",".$item["mergelevel9_costs"].",".$item["mergelevel10_costs"]."]";
}

$list = $dbh->query("SELECT * FROM dragonball_breakthrough");
$hash5 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash5 != "") {
		$hash5 .= ",\n";
	}
	$hash5 .= "			[".$item["quality_id"].",".$item["green_count"].", ".$item["blue_count"].",".$item["purple_count"].",".$item["gold_count"]."]";
}

$list = $dbh->query("SELECT * FROM dragonball");
$hash6 = "";
$property6 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash6 != "") {
		$hash6 .= ",\n";
	}
	$hash6 .= "			[".$item["id"].",".$item["effect_id"].", ".$item["quality_id"].",'".$item["sign"]."','".$item["name"]."', ".$item["star_class"]." ]";
	
	if ($property6 != "") {
		$property6 .= "\n";
	}
	$property6 .= "		public static const ".$item["sign"].":String = '".$item["sign"]."';";
}


$hash7 = "";
$ignot= 50;
$coin= 1000000;
for($i = 1; $i<= 60 ; $i++)
{
	if ($hash7 != "") {
		$hash7 .= ",\n";
	}
	if($i <= 5)
	{
		$ignot = 50;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
	else if($i > 5 &&$i <= 20)
	{
		$ignot = 60;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
	else if($i > 20 &&$i <= 30)
	{
		$ignot = 70;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
	else if($i > 30 &&$i <= 40)
	{
		$ignot = 80;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
	else if($i > 40 &&$i <= 50)
	{
		$ignot = 90;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
	else if($i > 50 &&$i <= 60)
	{
		$ignot = 100;
		$hash7.= "			[".$i.",".$ignot.", ".$coin."]";
	}
}

$list = $dbh->query("SELECT * FROM fire_dragonball_exp");
$hash8 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash8 != "") {
		$hash8 .= ",\n";
	}
	$hash8 .= "			{ id: ".$item["id"].", XiaoHuoLongZhu: ".$item["xiao"].", ZhongHuoLongZhu: ".$item["zhong"].", DaHuoLongZhu: ".$item["da"].", LieHuoLongZhu: ".$item["lie"]." }";
}

$list = $dbh->query("SELECT * FROM dragonball_level_description");
$hash9 = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash9 != "") {
		$hash9 .= ",\n";
	}
	$hash9 .= "			".$item["dragonball_id"].": ['".$item["level_1"]."','".$item["level_2"]."','".$item["level_3"]."','"
	.$item["level_4"]."','".$item["level_5"]."','".$item["level_6"]."','".$item["level_7"]."','"
	.$item["level_8"]."','".$item["level_9"]."','".$item["level_10"]."']";
}


$str = "package com.assist.server
{
	public class DragonBallType
	{
		import com.assist.view.HtmlText;
		import com.haloer.data.oObject;
		// id : [sign, name]
		private static const DragonBallBuffs : Object = {
".$hash1."
		};
		
".$property1."

		private static const DragonBallEffect : Object = {
".$hash2."
		};
		
		private static const DragonBallEffectForSign : Object =
		{
".$hash21."
		}
		
".$hash22."

		private static const DragonBallQuality : Object = {
".$hash3."
		};
		
".$property3."
        
		
        private static const DragonBallUpgradeInfo : Array = [
".$hash4."
		];
		
		private static const DragonBallBreakThrough : Array = [
".$hash5."
		];
		
		private static const DragonBall : Array = [
".$hash6."
		]
		
".$property6."
		
		private static const vipCostData : Array =[
".$hash7."
		]
		
		private static const EsExp : Array = [
".$hash8."
		]
		
		private static const DragonBallDescription : Object = {
".$hash9."
		}
		
		public static function getDragonBallDescription(dragonBallId : int, level : int) : String
		{
			return DragonBallDescription[dragonBallId][level -1];
		}
		
		public static function getEsExp() : Object
		{
			return EsExp[0];
		}
		
		public static function getDragonBallBuffSign(id : int) : String
		{
			return DragonBallBuffs[id][0];
		}
		
		public static function getDragonBallBuffName(id : int) : String
		{
			return DragonBallBuffs[id][1];
		}
		
		
		public static function getDragonBallEffectName(id : int) : String
		{
			return DragonBallEffect[id][0];
		}
		
		public static function getDragonBallEffectForSign(id : int) : String
		{
			return DragonBallEffectForSign[id][0];
		}
		
		/**
		 *  
		 * @param qualityId
		 * @return {id,sign,name,dragonball_init_exp,price}
		 * 
		 */		
		public static function getDragonBallQuality(qualityId : int) : Object
		{
			var cout : Object ;
			var tempArr : Array = DragonBallQuality[qualityId];

			cout ={};
			oObject.list(tempArr,cout,[
				
				'sign',
				'name',
				'dragonball_init_exp',
				'price'
			]);
			cout.id = qualityId;

			return cout;
		}
		
		/**
		 *  
		 * @param qualityId
		 * @return {id,sign,name,dragonball_init_exp,price}
		 * 
		 */		
		public static function getDragonBallQualityBySign(qualitySign : String) : Object
		{
			var cout : Object ;
			for (var qualityIdStr : String in DragonBallQuality)
			{
				var tempArr : Array = DragonBallQuality[qualityIdStr];
				if(tempArr[0] == qualitySign)
				{
					cout ={};
					oObject.list(tempArr,cout,[
						
						'sign',
						'name',
						'dragonball_init_exp',
						'price'
					]);
					cout.id = parseInt(qualityIdStr);
					break;
				}
			}
			return cout;
		}
		
		
		
		/**
		 * 
		 * @param qualityId
		 * @return {'quality_id','level2_exp','level3_exp','level4_exp','level5_exp','level6_exp','level7_exp','level8_exp','level9_exp','level10_exp',
						'mergelevel1_costs','mergelevel2_costs','mergelevel3_costs','mergelevel4_costs',
						'mergelevel5_costs','mergelevel6_costs','mergelevel7_costs','mergelevel8_costs','mergelevel9_costs','mergelevel10_costs'}
		 * 
		 */		
		public static function getDragonBallUpgradeInfo(qualityId : int) : Object
		{
			var cout : Object;
			for each(var tempArr : Array in DragonBallUpgradeInfo)
			{
				if(tempArr[0] == qualityId)
				{
					cout = {};
					oObject.list(tempArr,cout,[
						'quality_id',
						'level2_exp',
						'level3_exp',
						'level4_exp',
						'level5_exp',
						'level6_exp',
						'level7_exp',
						'level8_exp',
						'level9_exp',
						'level10_exp',
						'mergelevel1_costs',
						'mergelevel2_costs',
						'mergelevel3_costs',
						'mergelevel4_costs',
						'mergelevel5_costs',
						'mergelevel6_costs',
						'mergelevel7_costs',
						'mergelevel8_costs',
						'mergelevel9_costs',
						'mergelevel10_costs'
					]);
					break;
				}
			}
			return cout;
		}
		
		/**
		 * 获取升级需要的经验值 
		 * @param qualityId
		 * @param level
		 * @return 
		 * 
		 */		
		public static function getNextLevelExp(qualityId : int, level : int) : int
		{
			var upgradeInfo : Object = getDragonBallUpgradeInfo(qualityId);
			if(upgradeInfo == null)
			{
				return int.MAX_VALUE;
			}
			var nextLevelExp : int = int.MAX_VALUE;
			switch(level)
			{
				case 1:
					return upgradeInfo.level2_exp;
				case 2:
					return upgradeInfo.level3_exp;
				case 3:
					return upgradeInfo.level4_exp;
				case 4:
					return upgradeInfo.level5_exp;
				case 5:
					return upgradeInfo.level6_exp;
				case 6:
					return upgradeInfo.level7_exp;
				case 7:
					return upgradeInfo.level8_exp;
				case 8:
					return upgradeInfo.level9_exp;
				case 9:
					return upgradeInfo.level10_exp;
			}
			return int.MAX_VALUE;
		}
		
		/**
		 *  
		 * @param qualityId
		 * @return {quality_id,green_count,blue_count,purple_count,gold_count}
		 * 
		 */		
		public static function getDragonBallBreakThrough(qualityId : int) : Object
		{
			var cout : Object;
			for each(var tempArr : Array in DragonBallBreakThrough)
			{
				if(tempArr[0] == qualityId)
				{
					cout = {};
					oObject.list(tempArr,cout,[
						'quality_id',
						'green_count',
						'blue_count',
						'purple_count',
						'gold_count'
					]);
					break;
				}
			}
			return cout;
		}
		
		/**
		 *  
		 * @param id
		 * @return {id,effect_id,quality_id,sign}
		 * 
		 */		
		public static function getDragonBall(id : int) : Object
		{
			var cout : Object;
			for each(var tempArr : Array in DragonBall)
			{
				if(tempArr[0] == id)
				{
					cout = {};
					oObject.list(tempArr,cout,['id','effect_id','quality_id','sign','name','star']);
					break;
				}
			}
			return cout;
		}
		
		public static function getDragonBallByEffectAndQuality(effectId : int, qualityId : int) : Object
		{
			var cout : Object = null ;
			for each(var tempArr : Array in DragonBall)
			{
				if(tempArr[1] == effectId && tempArr[2] == qualityId)
				{
					cout = {};
					oObject.list(tempArr,cout,['id','effect_id','quality_id','sign','name']);
					break;
				}
			}
			return cout;
		}
		
		
		/**
		 * 升级到指定级数需要的铜钱 
		 * @param qualityId
		 * @param level 指定级数
		 * @return 
		 * 
		 */		
		public static function getBeMergeCost(qualityId : int, level : int) : int
		{
			var upgradeInfo : Object = getDragonBallUpgradeInfo(qualityId);
			var cout : int = 0;
			switch(level)
			{
				case 1:
					cout = upgradeInfo.mergelevel1_costs;
					break;
				case 2:
					cout = upgradeInfo.mergelevel2_costs;
					break;
				case 3:
					cout = upgradeInfo.mergelevel3_costs;
					break;
				case 4:
					cout = upgradeInfo.mergelevel4_costs;
					break;
				case 5:
					cout = upgradeInfo.mergelevel5_costs;
					break;
				case 6:
					cout = upgradeInfo.mergelevel6_costs;
					break;
				case 7:
					cout = upgradeInfo.mergelevel7_costs;
					break;
				case 8:
					cout = upgradeInfo.mergelevel8_costs;
					break;
				case 9:
					cout = upgradeInfo.mergelevel9_costs;
					break;
				case 109:
					cout = upgradeInfo.mergelevel10_costs;
					break;
			}
			return cout;
		}
		
		/**
		 * 获取品质颜色 
		 * @param qualitySign
		 * @return 
		 * 
		 */		
		public static function getColor(qualitySign : String) : int
		{
			switch(qualitySign)
			{
				case HeiSe:
					return HtmlText.Gray2;
				case LvSe:
					return HtmlText.Green;
				case LanSe:
					return HtmlText.Blue2;
				case ZiSe:
					return HtmlText.Purple;
				case JinSe:
					return HtmlText.Yellow;
				case HongSe:
					return HtmlText.Red;
			}
			
			return 0xFFF;
		}

		/**
		 *获取对应花费
		 * @param intVip
		 * @return 
		 * 
		 */
		public static function getCostData(intAlartCount : int) : Array
		{
			for each(var ary:Array in vipCostData)
			{
				if(ary[0] == intAlartCount)
				{
					return ary;
				}
			}
			
			return [];
		}	
		
		/**
		 * 根据id获取龙珠标示
		 * */
		public static function getDragonBallSignById(id : int) : String
		{
			for(var i:int = 0; i < DragonBall.length; i++)
			{
				var arr:Array = DragonBall[i];
				if(arr[0] == id)
				{
					return arr[3];
				}
			}
			return '';
		}
		
		/**
		 * 根据id获取龙珠名字
		 * */
		public static function getDragonBallNameById(id : int) : String
		{
			for(var i:int = 0; i < DragonBall.length; i++)
			{
				var arr:Array = DragonBall[i];
				if(arr[0] == id)
				{
					return arr[4];
				}
			}
			return '';
		}
		
		/**
		 * 根据标示获取龙珠名字
		 * */
		public static function getDragonBallNameBySign(sign : String) : String
		{
			for(var i:int = 0; i < DragonBall.length; i++)
			{
				var arr:Array = DragonBall[i];
				if(arr[3] == sign)
				{
					return arr[4];
				}
				
			}
			return '';
		}
		
		/**
		 * 根据标示获取龙珠id
		 * */
		public static function getDragonBallIdBySign(sign : String) : int
		{
			for(var i:int = 0; i < DragonBall.length; i++)
			{
				var arr:Array = DragonBall[i];
				if(arr[3] == sign)
				{
					return arr[0];
				}
				
			}
			return 0;
		}
	}
}
";

file_put_contents($desc_dir."DragonBallType.as", addons().$str);

print repeat("[data] DragonBallType", 75, ".")."DONE.\n";
?>