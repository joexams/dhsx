<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### marry

$list = $dbh->query("
	select
		`id`, `favor_value`, `description`
	from
		`marry_description`
");

$list1 = $dbh->query("
	select
		`id`, `desc`,`sign`
	from
		`marry_skill`
");

$list2 = $dbh->query("
	select
		`id`, `picture_id`,`panel_id`,`skill_id`,`effect_value`,`is_percent`,`need_favor`
	from
		`marry_skill_effect`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["favor_value"].",\"".$item["description"]."\"]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [\"".$item["desc"]."\",\"".$item["sign"]."\"]";
}

$hash2 = "";
for ($i = 0; $i < count($list2); $i++) {
	$item = $list2[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			".$item["id"]." : [".$item["picture_id"].",".$item["panel_id"].",".$item["skill_id"].",".$item["effect_value"].",".$item["is_percent"].",".$item["need_favor"]."]";
}


### 类

$str = "package com.assist.server
{
	public class MarryType
	{
		// id : [亲密度上限 类型描述]
		private static const MarryDescribe : Object = {
".$hash."
		};
		
		// id : [描述 标识]
		private static const MarrySkill : Object =
		{
".$hash1."
		}
		
		// id : [图片id 区块id 结婚技能id 效果值 消耗亲密度]
		private static const SkillEffect : Object =
		{
".$hash2."
		}
	
		/**
		* 亲密度上限
		*/
		public static function favorTop(id : int) : int
		{
			return MarryDescribe[id] ? MarryDescribe[id][0] : 0;
		}
	
		/**
		* 婚姻状态描述
		*/
		public static function describe(id : int) : String
		{
			return MarryDescribe[id] ? MarryDescribe[id][1] : \"\";
		}
		
		/**
		 * 结婚id 
		 * @param favor
		 * @return 
		 */
		public static function marryId(favor : int) : int
		{
			var list : Array = [];
			for(var s : String in MarryDescribe)
			{
				var obj : Object = {};
				obj.id = s;
				obj.favorValue = MarryDescribe[s][0];
				list.push(obj);
			}
			list.sortOn(\"id\",Array.NUMERIC);
			for(var i : int = 0;i < list.length;i++)
			{
				obj = list[i];
				if(favor < obj.favorValue)
				{
					return obj.id;
				}
			}
			obj = list[list.length -1];
			if(favor >= obj.favorValue)
			{
				return obj.id;
			}
			return 1;
		}
		
		/**
		* 婚姻技能描述
		*/
		public static function skillDescribe(id : int) : String
		{
			return MarrySkill[id] ? MarrySkill[id][0] : \"\";
		}
		
		/**
		* 婚姻技能标识
		*/
		public static function skillSign(id : int) : String
		{
			return MarrySkill[id] ? MarrySkill[id][1] : \"\";
		}
		
		/**
		* 百合图图片id
		*/
		public static function effectPictureId(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][0] : 0;
		}
		
		/**
		* 百合图区块id
		*/
		public static function effectPanelId(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][1] : 0;
		}
		
		/**
		* 百合图技能id
		*/
		public static function effectSkillId(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][2] : 0;
		}
		
		/**
		* 百合图技能 加成数值
		*/
		public static function effectValue(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][3] : 0;
		}
		
		/**
		 * 是否属性等级
		 */
		public static function isAttribute(id : int) : Boolean
		{
			if(id >= 8 && id <= 12)
			{
				return true;
			}
			return false;
		}
		
		/**
		* 百合图技能 加成数值 是否百分比
		*/
		public static function isPercent(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][4] : 0;
		}
		/**
		* 百合图技能 需消耗的亲密度 
		*/
		public static function effectNeedFavor(id : int) : int
		{
			return SkillEffect[id] ? SkillEffect[id][5] : 0;
		}
	}
}
";

file_put_contents($desc_dir."MarryType.as", addons().$str);

echo "[data] marryType [Done]\n";
?>