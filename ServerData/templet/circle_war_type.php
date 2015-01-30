<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### circlewar_level

$list = $dbh->query("SELECT * FROM circlewar_level");
$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash != "") {
		$hash .= ",\n";
	}
	$hash .= "			{level:".$item["level"]."  ,name: '".$item["name"]."' ,level_limit:".$item["level_limit"]."}";			
}

$list = $dbh->query("SELECT * FROM circlewar_require");
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	$hash1 .= "			{id:".$item["id"]
					." ,circlewar_level:".$item["circlewar_level"]
					." ,barrier:".$item["barrier"]
					." ,monster_scene_id:".$item["monster_scene_id"]
					." ,describe:".$item["describe"]
					." ,rule_id:".$item["rule_id"]
					." ,award_neidan:".$item["award_neidan"]."}";			
}

$list = $dbh->query("SELECT * FROM special_rule");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .= "			{id:".$item["id"]
					." ,name:'".$item["name"]
					."' ,describe:'".$item["describe"]."'}";			
}

### 类

$str = "package com.assist.server
{
	
	public class CircleWarType
	{
		/**
		 * 轮回对应的等级限制
		 */		
		public static var CircleWarData : Array = [
".$hash."
		]
		
		/**
		 * 轮回对应的战场需求
			id ID
			circlewar_level 大关卡等级
			barrier 小关卡等级
			monster_scene_id 怪物场景id
			describe 轮回关卡描述
			rule_id 规则id
			award_neidan 奖励内丹
		 */		
		public static var CircleWarRequire : Array = [
".$hash1."
		]
		
		/**
		 * 轮回战场部分规则
		 *  id  规则ID
			circlewar_level 名称
			barrier 规则描述
		 */		
		public static var CircleWarRule : Array = [
".$hash2."
		]
		
		
		
		/**
		 *轮回战场限制等级
		 * */
		public static function getCircleWarLevel(circleLevel:int) : int
		{
			for each(var obj:Object in CircleWarData)
			{
				if(obj['level'] == circleLevel)
				{
					return obj['level_limit'];
				}
			}
			
			return 0;
		}
		
		/**
		 *轮回战场大关卡名称
		 * */
		 public static function getCircleWarName(circleLevel:int) : String
		{
			for each(var obj:Object in CircleWarData)
			{
				if(obj['level'] == circleLevel)
				{
					return obj['name'];
				}
			}
			
			return '';
		}
		
		/**
		 *轮回战场名称合集
		 * */
		public static function getAryNames() : Array
		{
			var ary:Array = [];
			for each(var obj:Object in CircleWarData)
			{
				ary.push(obj['name']);
			}
			return ary;
		}
		 
		 /**
		 *轮回战场已开启轮回个数
		 * */
		public static function getOpendCircleNum(playerLv:int):int
		{
			var num:int = 0;
			for each(var obj:Object in CircleWarData)
			{
				if(obj['level_limit'] <= playerLv)
				{
					num ++;
				}
			}
		 
			return num ;
		}
		/**
		 *获取轮回战场规则id
			circleLevel	大关卡等级
			barrierLevel	小关卡等级 
		 * */
		public static function getCircleWarRuleId(circleLevel:int, barrierLevel:int) : int
		{
			for each(var obj:Object in CircleWarRequire)
			{
				if(obj['circlewar_level'] == circleLevel && obj['barrier'] == barrierLevel)
				{
					return obj['rule_id'];
				}
			}
			
			return 0;
		}
		
		/**
		 *获取轮回战场股则描述
			ruleId	规则id
		 * */
		public static function getCircleWarDescribe(ruleId:int) : String
		{
			for each(var obj:Object in CircleWarRule)
			{
				if(obj['id'] == ruleId)
				{
					return obj['describe'];
				}
			}
			
			return '';
		}

		
	}
		
		 
		
}
";

file_put_contents($desc_dir."CircleWarType.as", addons().$str);

echo("[data] CircleWarType DONE\n");

?>
