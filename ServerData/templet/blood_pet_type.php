<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### blood_pet

$list = $dbh->query("SELECT * FROM blood_pet_to_chip");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	$hash .= "			[".$item["pet_item_id"].",".$item["chip_item_id"].",".$item["need_chip"]."]";
}

$list = $dbh->query("SELECT * FROM blood_pet_attr");
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";      
	}
	$hash1 .= "			[".$item["id"].",".$item["item_id"].",".$item["level"].",".$item["blood_stunt_id"].",".$item["need_curiosity"].",".'"'.$item["grow_up"].'"'.",".$item["strength"].",".$item["agile"].",".
					$item["intellect"].",".$item["attack"].",".$item["defense"].",".$item["stunt_attack"].",".$item["stunt_defense"].",".$item["magic_attack"].",".$item["magic_defense"].",".$item["hit"].",".
					$item["block"].",".$item["dodge"].",".$item["critical"].",".$item["break_block"].",".$item["break_critical"].",".$item["kill"].",".$item["health"]."]";
}

$list = $dbh->query("SELECT * FROM blood_pet_stunt");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .= "			{id:".$item["id"]
					." ,name:'".$item["name"]
					."' ,description:'".$item["description"]."'}";			
}

### 类

$str = "package com.assist.server.source
{
	public class BloodPetTypeData
	{
		/**
		 *  id ID
			pet_item_id 	宠物物品ID
			chip_item_id 	碎片物品ID
			need_chip
		 */		
		public static var BloodPetChipArr : Array = [
".$hash."
		];
		
		/**
		 *  id ID
			item_id 血契灵兽物品ID
			level 等级
			blood_stunt_id 技能id
			need_curiosity 所需奇物
			grow_up 成长阶段
			strength 武力
			agile 绝技
			intellect 法术
			attack 普攻
			defense 普防
			stunt_attack 绝攻
			stunt_defense 绝防
			magic_attack 法攻
			magic_defense 法防
			hit 命中
			block 格挡
			dodge 闪避
			critical 暴击
			break_block 破击
			break_critical 韧性
			kill 必杀
			health 生命
		 */		
		public static var BloodPetAttr : Array = [
".$hash1."
		];
		
		/**
		 * 异兽技能
		 *  id  stuntID
			circlewar_level 名称
			barrier 规则描述
		 */		
		public static var BloodPetStuntList : Array = [
".$hash2."
		]
	}	
}
";

file_put_contents($desc_dir."source/BloodPetTypeData.as", addons().$str);

print repeat("[data] BloodPetTypeData", 75, ".")."DONE.\n";

$str = "package com.assist.server
{
	import com.assist.server.source.BloodPetTypeData;
	import com.haloer.data.oObject;
	
	public class BloodPetType
	{
	
		private static var PetProperty:Object = {strength:'武力', agile:'绝技', intellect:'法术', attack:'普攻', defense:'普防', stunt_attack:'绝攻',
							stunt_defense:'绝防',magic_attack:'法攻', magic_defense:'法防', hit:'命中', block:'格挡', dodge:'闪避',
							critical:'暴击', break_block:'破击', break_critical:'韧性', kill:'必杀', health:'生命'};
		
		  /**
		 * 获取碎片物品ID
		 * @param	petItemId 	宠物物品ID
		 * @return chipItemId	碎片物品ID
		 */
		 public static function getPetChipItemId(petItemId : int) : int
		 {
			for each(var tempArr : Array in BloodPetTypeData.BloodPetChipArr)
			{
				if(tempArr[0] == petItemId)
				{
					return tempArr[1];
				}
			}
			return 0;
		 }
		 
		  /**
		 * 获取宠物详细
		 * @param	petItemId 	宠物物品ID
		 * @param	level 		宠物等级
		 * @return list	属性列表
		 */
		 public static function getPetInfo(petItemId : int, level:int) : Object
		 {
			var obj:Object = null
			for each(var tempArr : Array in BloodPetTypeData.BloodPetAttr)
			{
				
				if(tempArr[1] == petItemId && tempArr[2] == level)
				{
					obj = {};
					oObject.list(tempArr,obj,[
						'id',
						'item_id',
						'level', 
						'blood_stunt_id',
						'need_curiosity',
						'grow_up',
						'strength',
						'agile',
						'intellect',
						'attack',
						'defense',
						'stunt_attack',
						'stunt_defense',
						'magic_attack',
						'magic_defense',
						'hit',
						'block',
						'dodge',
						'critical',
						'break_block',
						'break_critical',
						'kill',
						'health'
					]);
					break;
				}	
			}
			return obj;
		 }
		 
		  /**
		 * 获取宠物当前成长阶段最后一等级信息
		 * @param	petItemId 	宠物物品ID
		 * @param	level 		宠物等级
		 * @return list	属性列表
		 */
		 public static function getPetJiuJiInfo(petItemId : int, level:int) : Object
		 {
			var maxLevel:int;
			if(level%20 == 0)
			{
				maxLevel = int(level/20)*20;
			}
			else
			{
				maxLevel = int(level/20)*20 + 20;
			}
			return getPetInfo(petItemId, maxLevel);
		 }
		 
		 
		  /**
		 * 获取宠物属性中文名称
		 * @param str 	英文键值 
		 * @return 名称
		 */
		 public static function getPetPropertyName(str:String) : String
		 {
			var name:String = '';
			if(PetProperty[str])
			{
				name = PetProperty[str];
			}
			return name;
		 }
		 
		 /**
		 * 获取宠物所有技能id列表
		 * @param petItemId	宠物id 
		 */
		 public static function getPetAllStuntId(petItemId : int) : Array
		 {
			var list:Array = [];
			var obj:Object = null;
			for each(var tempArr : Array in BloodPetTypeData.BloodPetAttr)
			{
				if(tempArr[1] == petItemId && tempArr[3] > 0)
				{
					var hasKey:Boolean = false;
					for(var i:int=0; i< list.length; i++)
					{
						obj = list[i] as Object;
						if(obj.stuntId == tempArr[3])
						{
							hasKey = true;
							if(obj.leve > tempArr[2])
							{
								obj.leve = tempArr[2] ;
								break;
							}
						}
					}
					if(!hasKey)
					{
						obj = {};
						obj.stuntId = tempArr[3];
						obj.leve = tempArr[2];
						list.push(obj);
					}
				}	
			}
			return list;
		 }
		 
		 
		  /**
		 * 获取宠物技能描述
		 * @param stuntId 	技能Id
		 */
		 public static function getStuntDes(stuntId:int) : String
		 {
			for each(var obj:Object in BloodPetTypeData.BloodPetStuntList)
			{
				if(obj['id'] == stuntId)
				{
					return obj['description'];
				}
			}
			
			return '';
		 }
	}
		
}
";

file_put_contents($desc_dir."BloodPetType.as", addons().$str);

print repeat("[data] BloodPetType", 75, ".")."DONE.\n";

?>