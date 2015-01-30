<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### mount

$list = $dbh->query("
	select
		`id`, `name`, `describe`,`come_from`,`item_id`,`item_spirit`,`display`
	from
		`mounts`
");

$list1 = $dbh->query("
	select
		`id`, `mounts_id`, `lv`,`next_lv_exp`,`health`,`attack`,`defense`,`magic_attack`,`magic_defense`,`stunt_attack`,`stunt_defense`,`hit`,`block`,`dodge`,`critical`,`momentum`,`break_block`,`break_critical`,`kill`,`speed`
	from
		`mounts_attribute`
");



$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\",\"".$item["describe"]."\",\"".$item["come_from"]."\",".$item["item_id"].",".$item["item_spirit"].",".$item["display"]."]";
}



$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [".$item["mounts_id"].",".$item["lv"].",".$item["next_lv_exp"].",".$item["health"].",
	".$item["attack"].",".$item["defense"].",".$item["magic_attack"].",".$item["magic_defense"].",".$item["stunt_attack"].",
	".$item["stunt_defense"].",".$item["hit"].",".$item["block"].",".$item["dodge"].",".$item["critical"].",
	".$item["momentum"].",".$item["break_block"].",".$item["break_critical"].",".$item["kill"].",".$item["speed"]."]";
}



### 类

$str = "package com.assist.server
{
	public class MountType
	{
		// 坐骑信息 索引id ：[名称 描述 获得方式 需要的物品id 物品产生的精魄数量 是否显示在详细列表]
		private static const MountInfo : Object = {
".$hash."
		};
		
		// 坐骑属性 索引id : [坐骑唯一id 等级 下级所需经验,0表示升不了级 生命值 攻击 防御 法术攻击 法术防御 绝技攻击 绝技防御 命中 格挡 闪避 暴击 气势 破击 韧性 必杀 速度]
		private static const MountData : Object =
		{
".$hash1."
		}
		
			/**
		 * 获取所有坐骑信息 
		 * @return 
		 */
		public static function allMounts() : Array
		{
			var rList : Array = [];
			for(var id : String in MountInfo)
			{
				var obj : Object = {};
				var ary : Array = MountInfo[id];
				obj.mountId = id;
				obj.mountName = ary[0];
				obj.mountDescribe = ary[1];
				obj.mountFrom = ary[2];
				obj.itemId = ary[3];
				obj.spirit = ary[4];
				obj.isDisplay = ary[5];
				rList.push(obj);
			}
			rList.sortOn(\"mountId\",Array.NUMERIC);
			return rList;
		}
		
		/**
		 * 索引id 获取 坐骑名字
		 */
		public static function mountName(id : int) : String
		{
			return MountInfo[id] ? MountInfo[id][0] : \"\";
		}
		
		/**
		 * 索引id 获取 坐骑描述
		 */
		public static function mountDescribe(id : int) : String
		{
			return MountInfo[id] ? MountInfo[id][1] : \"\";
		}
		
		/**
		 * 索引id 获取 坐骑获得方式
		 */
		public static function mountComeFrom(id : int) : String
		{
			return MountInfo[id] ? MountInfo[id][2] : \"\";
		}
		
		/**
		 * 索引id 获取 坐骑需要的物品id
		 */
		public static function mountItemId(id : int) : int
		{
			return MountInfo[id] ? MountInfo[id][3] : 0;
		}
		
		/**
		 * 索引id 获取 坐骑产生的精魄数量
		 */
		public static function mountItemSpirit(id : int) : int
		{
			return MountInfo[id] ? MountInfo[id][4] : 0;
		}
		
		/**
		 * 索引id 获取 是否显示在详细列表 0 显示 1 不显示
		 */
		public static function mountDisplay(id : int) : int
		{
			return MountInfo[id] ? MountInfo[id][5] : 1;
		}
		/**
		 * 坐骑id 获得坐骑的10级所有属性数值
		 */
		public static function mountAttribute(mountId : int) : Array
		{
			var tempList : Array = [];
			for each(var ary : Array in MountData)
			{
				if(ary[0] == mountId)
				{
					tempList.push(ary)
				}
			}
			var len : int = tempList.length;
			for(var i : int = 0;i < len-1;i++)
			{
				for(var j : int = i+1;j < len;j++)
				{
					if(tempList[i][1] > tempList[j][1])
					{
						var temp : Array = tempList[i];
						tempList[i] = tempList[j];
						tempList[j] = temp;
					}
				}
			}
			return tempList;
		}
		
		/**
		 * 坐骑id 获得坐骑的某级所有属性数值
		*	obj.mountId = ary[0]; //id
		*	obj.lv = ary[1]; //等级
		*	obj.next_lv_exp = ary[2]; //下一等级所需经验
		*	obj.health = ary[3];//生命
		*	obj.attack = ary[4];//攻击
		*	obj.defense = ary[5];//防御
		*	obj.magic_attack = ary[6];//法术攻击
		*	obj.magic_defense = ary[7];//法术防御
		*	obj.stunt_attack = ary[8];//绝技攻击
		*	obj.stunt_defense = ary[9];//绝技防御
		*	obj.hit = ary[10];//命中
		*	obj.block = ary[11];//格挡
		*	obj.dodge = ary[12];//闪避
		*	obj.critical = ary[13];//暴击
		*	obj.momentum = ary[14];//气势
		*	obj.break_block = ary[15];//破击
		*	obj.break_critical = ary[16];//韧性
		*	obj.kill = ary[17];//必杀
		*	obj.speed = ary[18];//速度
		 */
		public static function mountAttributeLevel(mountId:int,level:int) : Object
		{
			var ary : Array = mountAttribute(mountId)[level - 1];
			var obj : Object = {};
			obj.mountId = ary[0];
			obj.lv = ary[1];
			obj.next_lv_exp = ary[2];
			obj.health = ary[3];
			obj.attack = ary[4];
			obj.defense = ary[5];
			obj.magic_attack = ary[6];
			obj.magic_defense = ary[7];
			obj.stunt_attack = ary[8];
			obj.stunt_defense = ary[9];
			obj.hit = ary[10] * 10;
			obj.block = ary[11] * 10;
			obj.dodge = ary[12] * 10;
			obj.critical = ary[13] * 10;
			obj.momentum = ary[14] * 10;
			obj.break_block = ary[15] * 10;
			obj.break_critical = ary[16] * 10;
			obj.kill = ary[17] * 10;
			obj.speed = ary[18];
			return obj;
		}
		
		
		
	}
}
";

file_put_contents($desc_dir."MountType.as", addons().$str);

echo "[data] mountType [Done]\n";
?>