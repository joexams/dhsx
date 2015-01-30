<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list = $dbh->query("SELECT id,`name`,require_level,bless_name,war_attribute_type_id, three_star, four_star, five_star, six_star FROM nine_regions");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["id"].",'".$item["name"]."',".$item["require_level"].",'".$item["bless_name"]."',".$item["war_attribute_type_id"].","
	                                .$item["three_star"].",".$item["four_star"].",".$item["five_star"].",".$item["six_star"]."]";
}

$list = $dbh->query("SELECT nine_regions_level.region_id AS n1,nine_regions_level.region_level AS n2,nine_regions_level.monster_team_id AS n3 ,
					monster.sign AS m1,monster.name AS m2,monster.resource_monster_id AS m4,
					nine_regions_level.award_fame AS n4,nine_regions_level.award_coin AS n5,nine_regions_level.talk_content AS n6 FROM nine_regions_level,mission_monster_team,monster 
					WHERE nine_regions_level.monster_team_id = mission_monster_team.mission_scene_id AND mission_monster_team.monster_id = monster.id GROUP BY n3");
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	$monsterId = $item["m4"];
	$monsterSign = $item["m1"];
	if($monsterId > 0)
	{
		$list1 = $dbh->query("SELECT monster.sign as m1 FROM monster WHERE id = ".$monsterId);
		if(count($list1))
		{
			$item1 = $list1[0];
			$monsterSign = $item1["m1"];
		}
	}
	$hash1 .= "			[".$item["n1"].",".$item["n2"].",".$item["n3"].",'".$monsterSign."','".$item["m2"]."',0,".$item["n4"].",".$item["n5"].",' ']";
}
$list = $dbh->query("SELECT id,`name`,award_fame,award_aura,tips,monster_team_id FROM nine_regions_hidden_level");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .="			[".$item["id"].",'".$item["name"]."',".$item["award_fame"].",".$item["award_aura"].",'".$item["tips"]."',".$item["monster_team_id"]."]";
}

$list = $dbh->query("
SELECT
  s.nine_regions_id,
  s.nine_regions_level,
  s.war_attr_1_soul_gold_num,
  s.award_war_attr_type_id_1,
  s.award_war_attr_value_1,
  s.award_war_attr_type_id_2,
  s.award_war_attr_value_2
FROM soul_to_nine_regions_info s;");
$hash3 = "";
for ($i = 0; $i < count($list); $i++)
{
	$item = $list[$i];
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	$hash3 .="			[".$item["nine_regions_id"].",".$item["nine_regions_level"].",".$item["war_attr_1_soul_gold_num"].",".$item["award_war_attr_type_id_1"].",".$item["award_war_attr_value_1"].",".$item["award_war_attr_type_id_2"].",".$item["award_war_attr_value_2"]."]";
}




### 类

$str = "package com.assist.server.source
{
	public class NineRegionsTypeData
	{
		//  [id，名称,等级要求,祝福名称，加成战斗属性， 3级 4级 5级 6级]
		public static const NineRegions : Array = [
".$hash."
		];
		
		//  [界id，关卡,怪物团id,怪标识，怪名字,奖励武魂值,奖励声望，奖励铜钱,说话内容]
		public static const NineRegionsMonster : Array = [
".$hash1."
		];
		
		//[神秘层id，名称，声望奖励，武魂奖励,提示内容，场景id]
		public static const HiddenLevel : Array = [
".$hash2."
		];
		
		//[九界ID，九界关卡，战争属性1的金色灵件数量，战争属性类型1，战争属性1的加值，战争属性类型2，战争属性2的加值]
		public static const NineRegionsSoul : Array = [
".$hash3."
		];
	}
}
";

file_put_contents($desc_dir."source/NineRegionsTypeData.as", addons().$str);

print repeat("[data] nine_regions_type_data", 75, ".")."DONE.\n";

$str = 'package com.assist.server
{
	import com.assist.server.source.NineRegionsTypeData;
	import com.lang.client.com.datas.NineRegionsDataLang;
	public class NineRegionsType
	{
		public static const MaxLevel:int = 10;//最大关卡数
		public static const openLevel:int = 101;//开放等级
		public static const TeamFull:int = 3;//队伍满人数
		private static var nrName:Array;
		
		/**
		 * 获取所有名称
		 * */
		
		public static function getNames():Array
		{
			if(nrName)
			{
				return nrName;
			}
			nrName = new Array();
			var arr:Array = NineRegionsTypeData.NineRegions;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				nrName.push(arr1[1]);
			}
			return nrName;
		}
		
		/**
		 * 通过Id获取名称
		 * */
		
		public static function getNameByIdx(idx:int):String
		{
			var arr:Array = NineRegionsTypeData.NineRegions;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == idx)
				{
					return arr1[1];
				}
			}
			return "";
		}
		
		/**
		 * 通过Id下一关开启等级
		 * */
		
		public static function getNextLvByIdx(idx:int):int
		{
			var arr:Array = NineRegionsTypeData.NineRegions;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] > idx)
				{
					return arr1[2];
				}
			}
			return 999;
		}
		
		/**
		 * 通过Id获取祝福名称
		 * */
		
		public static function getBlessNameByIdx(idx:int):String
		{
			var arr:Array = NineRegionsTypeData.NineRegions;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == idx)
				{
					return arr1[3];
				}
			}
			return "";
		}
		
		/**
		 * 通过战争属性和数量获取加值
		 * */
		
		public static function getBlessAddValue(warId : int, warNum : int):int
		{
			var arr:Array = NineRegionsTypeData.NineRegions;
			var addValue : int = 0;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[4] == warId)
				{
				    switch(warNum)
				    {
				        case 3:
					       addValue = arr1[5]
					       break;
					    case 4:
					       addValue = arr1[6]
					       break;
				        case 5:
					       addValue = arr1[7]
					       break;
				        case 6:
					       addValue = arr1[8]
					       break;
				    }
				}
			}
			return addValue;
		}
		
		/**
		 * 通过怪物界和关获得怪物id
		 * */
		
		public static function getMonsterId(jie:int,lv:int):int
		{
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == jie && arr1[1] == lv)
				{
					return arr1[2];
				}
			}
			return 0;
		}
		
		/**
		 * 通过怪物Id查怪物标识
		 * */
		
		public static function getMonsterSignById(id:int):String
		{
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[2] == id)
				{
					return arr1[3];
				}
			}
			return "";
		}
		
		/**
		 * 通过怪物Id查怪物名字
		 * */
		
		public static function getMonsterNameById(id:int):String
		{
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[2] == id)
				{
					return arr1[4];
				}
			}
			return "";
		}
		
		/**
		 * 通过界和关获取奖励
		 * */
		
		public static function getAward(jie:int,lv:int):Array
		{
			var award:Array = [0,0]
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == jie && arr1[1] == lv)
				{
					award = [arr1[6],arr1[7]];
				}
			}
			return award;
		}
		
		/**
		 * 通过怪物Id查说话内容
		 * */
		
		public static function getMonsterTalkById(id:int):Array
		{
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[2] == id)
				{
					var str:String = arr1[8];
				}
			}
			return str.split("|");
		}
		
		/**
		 * 通过 零件加成的属性 和 金色属性个数和 当前开启祝福 来获取祝福名称 加成值
		 * */
		
		public static function getSoulAdd(pId:int,num:int,limit:int = 0):Array
		{
			var retArr:Array = ["",0,0]
			var arr:Array = NineRegionsTypeData.NineRegions;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[4] == pId && limit >=arr1[0])
				{
					if(num >= 3)
					{
						var value:int = arr1[5+num-3];
						if(pId == 8 || pId == 9 || pId == 11 || pId == 13)
						{
							value *= 10;
						}
						return retArr = [arr1[3],value,arr1[0]];
					}
				}
			}
			return retArr;
		}
		
		/**
		 * 通过神秘层id获得信息
		 * 返回 [名称，声望奖励，武魂奖励,提示信息]
		 * */
		public static function getHiddenInfoById(id:int):Array
		{
			var arr:Array = NineRegionsTypeData.HiddenLevel;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == id)
				{
					return [arr1[1],arr1[2],arr1[3],arr1[4]]
				}
			}
			return ["",0,0,""];
		}
		
		public static function coinsString(coins:Number):String
		{			
			
			if(coins >= 1000000000)return int(coins/100000000) + NineRegionsDataLang.Yi; 
			if(coins >= 100000)return int(coins/10000) + NineRegionsDataLang.Wang; 
			
			return coins + "";
		}
		
		/**
		 * 获取九界附加在封灵的战争属性1类型
		 * @param id 九界id
		 * @return 战争属性1类型
		 */	
		public static function getSoulWarType1(id : int) : int
		{
			for each(var ary : Array in NineRegionsTypeData.NineRegionsSoul)
			{
				if(ary[0] == id)
				{
					return ary[3];
				}
			}
			return 0;
		}
		
		/**
		 * 获取九界附加在封灵的战争属性2类型
		 * @param id 九界id
		 * @return 战争属性2类型
		 */	
		public static function getSoulWarType2(id : int) : int
		{
			for each(var ary : Array in NineRegionsTypeData.NineRegionsSoul)
			{
				if(ary[0] == id && ary[1] >= 6)
				{
					return ary[5];
				}
			}
			return 0;
		}
		
		/**
		 * 获取九界附加在封灵的战争属性1的附加值
		 * @param id 九界id
		 * @param level 九界等级
		 * @param goldNum 战争属性1的金色封灵个数
		 * @return 战争属性1的附加值
		 */		
		public static function getSoulWarTypeValue1(id : int, level : int, goldNum : int) : Number
		{
			for each(var ary : Array in NineRegionsTypeData.NineRegionsSoul)
			{
				if(ary[0] == id && ary[1] == level && ary[2] == goldNum)
				{
					return ary[4];
				}
			}
			return 0;
		}
				
		/**
		 * 获取九界附加在封灵的战争属性2的附加值
		 * @param id 九界id
		 * @param level 九界等级
		 * @param goldNum 战争属性1的金色封灵个数
		 * @return 战争属性2的附加值
		 */		
		public static function getSoulWarTypeValue2(id : int, level : int, goldNum : int) : Number
		{
			for each(var ary : Array in NineRegionsTypeData.NineRegionsSoul)
			{
				if(ary[0] == id && ary[1] == level && ary[2] == goldNum)
				{
					return ary[6];
				}
			}
			return 0;
		}
		
		/**
		 * 根据关卡和界id获取怪物场景
		 * */
		public static function getSceneId(jie:int,lv:int):int
		{
			var arr:Array = NineRegionsTypeData.NineRegionsMonster;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == jie && arr1[1] == lv)
				{
					return arr1[2];
				}
			}
			return 1;
		}
		
		/**
		 * 根据关卡和界id获取隐藏层怪物场景
		 * */
		public static function getHiddenSceneId(jie:int):int
		{
			var arr:Array = NineRegionsTypeData.HiddenLevel;
			for(var i:int = 0; i < arr.length; i++)
			{
				var arr1:Array = arr[i];
				if(arr1[0] == jie)
				{
					return arr1[5];
				}
			}
			return 1;
		}
	}
	
}
';

file_put_contents($desc_dir."NineRegionsType.as", addons().$str);

print repeat("[data] nine_regions_type", 75, ".")."DONE.\n";
?>