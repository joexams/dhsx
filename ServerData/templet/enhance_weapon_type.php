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
   `id`,
   `name`,
   `description`,
   `type` ,
   `effect` ,
   `need_feats`
FROM enhance_weapon
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		[".$item["id"].",\"".$item["name"]."\",\"".$item["description"].
		"\",".$item["type"].",".$item["effect"].",".$item["need_feats"]."]";
}

$list2 = $dbh->query("
SELECT
   `id`,
   `description`,
   `sign`
FROM enhance_weapon_effect
");

$hash2 = "";
$constant = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "		[".$item["id"].",\"".$item["description"]."\",".$item["sign"]."]";
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";\n";
}

$list3 = $dbh->query("
SELECT
   `weapon_id`,
   `level`,
   `need_crystal`,
   `need_jade` ,
   `effect_id` ,
   `prob`,
   `value`,
   `value2`,
   `inc_health`,
   `inc_atk`,
   `inc_def`
FROM enhance_weapon_levelup
");

$hash3 = "";
for ($i = 0; $i < count($list3); $i++) {
	$item = $list3[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "		[".$item["weapon_id"].",".$item["level"].",".$item["need_crystal"].
		",".$item["need_jade"].",".$item["effect_id"].",".$item["prob"].",".$item["value"].
		",".$item["value2"].",".$item["inc_health"].",".$item["inc_atk"].",".$item["inc_def"]."]";					
}

### 类

$str = "package com.assist.server
{
	public class EnhanceWeaponType
	{
		//神兵种类[id，名字，描述，类型，附加效果，兑换需要功勋值，需要激活神兵，需要神兵等级]
		public static var WeaponType : Array = [
			".$hash1."
		];

		//神兵效果[id，描述，标识]
		public static var WeaponEffect : Array = [
			".$hash2."
		];

		//神兵强化[id，等级，升级需要天晶，升级需要玉牌，神兵效果id，触发概率，增加值，增加生命值，增加绝技攻击，增加绝技防御]
		public static var WeaponLevelUp : Array = [
			".$hash3."
		];

		".$constant."

		public static const MaxLevel : int = 80;
		
		//神兵技能名称
		public static const SkillName : Array = [
			\"金蛇狂舞\",
			\"玄武护盾\",
			\"凤凰琴音\",
			\"炎龙护甲\",
			\"火灵印记\",
			\"恢复旋律\",
			\"灵魂鼓点\",
			\"亡灵护盾\"
		];

		/**
		*根据id获取WeaponType的一条数据
		*/
		public static function getWeaponTypeData(id : int) : Array
		{
			for(var i : int = 0; i < WeaponType.length; i++)
			{
				if(WeaponType[i][0] == id)
					return WeaponType[i];
			}
			return null;
		}

		/**
		*根据id获取WeaponEffect的一条数据
		*/
		public static function getWeaponEffectData(id : int) : Array
		{
			for(var i : int = 0; i < WeaponEffect.length; i++)
			{
				if(WeaponEffect[i][0] == id)
					return WeaponEffect[i];
			}
			return null;
		}

		/**
		*根据等级和id获取升级数据
		*/
		public static function getLevelUpData(id : int, level : int) : Object
		{
			var obj : Object = {};
			for(var i : int = 0; i < WeaponLevelUp.length; i++)
			{
				if(WeaponLevelUp[i][0] == id && WeaponLevelUp[i][1] == level)
				{
					obj.name = getName(id);
					obj.weapon_id = WeaponLevelUp[i][0];
					obj.level = WeaponLevelUp[i][1];
					obj.need_crystal = WeaponLevelUp[i][2];
					obj.need_jade = WeaponLevelUp[i][3];
					obj.effect_id = WeaponLevelUp[i][4];
					obj.prob = WeaponLevelUp[i][5];
					obj.value = WeaponLevelUp[i][6];
					obj.value2 = WeaponLevelUp[i][7];
					obj.inc_health = WeaponLevelUp[i][8];
					obj.inc_atk = WeaponLevelUp[i][9];
					obj.inc_def = WeaponLevelUp[i][10];
				}
			}
			return obj;
		}

		/**
		*根据id获取名称
		*/
		public static function getName(id : int) : String
		{
			return getWeaponTypeData(id) ? getWeaponTypeData(id)[1] : \"\";
		}

		/**
		*根据id获取神兵类型
		*/
		public static function getType(id : int) : int
		{
			return getWeaponTypeData(id) ? getWeaponTypeData(id)[3] : 0;
		}

		/**
		*根据id获取描述
		*/
		public static function getDescription(id : int) : String
		{
			return getWeaponTypeData(id) ? getWeaponTypeData(id)[2] : \"\";
		}
		
		/**
		 * 根据id获取技能名称
		 */ 
		public static function getSkillName(id : int) : String
		{
			return SkillName[id - 1] ? SkillName[id - 1] : \"\";
		}
		
		/**
		 * 根据神兵等级获取技能等级
		 */ 
		public static function getSkillLevel(level : int) : int
		{
			var rl : int;
			if(level >= 70)
			{
				rl = 4;
			}else if(level >= 50)
			{
				rl = 3;
			}
			else if(level >= 30)
			{
				rl = 2;
			}
			else if(level >= 10)
			{
				rl = 1;
			}else
			{
				rl = 0;
			}
			return rl;
		}
		
		/**
		 * 根据技能等级获取最低神兵等级
		 */ 
		public static function getWeaponLevel(level : int) : int
		{
			var rl : int;
			switch(level)
			{
				case 0 :
					rl = 0;
					break;
				case 1 :
					rl = 10;
					break;
				case 2 :
					rl = 30;
					break;
				case 3 :
					rl = 50;
					break;
				case 4 :
					rl = 70;
					break;
			}
			return rl;
		}	
	}
}
";

file_put_contents($desc_dir."EnhanceWeaponType.as", addons().$str);

echo("[data] EnhanceWeaponType DONE\n");
?>