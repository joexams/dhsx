<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### NewComerTarget

$list = $dbh->query("
	select
		`id`, `day`, `name`,`sign`,`description`,`type`,`total`,`sort_order`,`coin`,`fame`,`skill`,`power`,`stone`,`item_id`,`item_amount`
	from
		`target_info`
");



$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["day"].",\"".$item["name"]."\",\"".$item["sign"]."\",\"".$item["description"]."\",".$item["type"].",".$item["total"].",".$item["sort_order"].",".$item["coin"].",".$item["fame"].",".$item["skill"].",".$item["power"].",".$item["stone"].",".$item["item_id"].",".$item["item_amount"]."]";
}



### 类

$str = "package com.assist.server
{
	public class NewComerTargetType
	{
		//目标id : 【天数 名字 标识 描述 奖励类型0-普通1-额外 总值 排序 奖励铜钱 奖励声望 奖励阅历 奖励体力 奖励灵山 物品id 物品数量】
		private static const Target : Object = {
".$hash."
		};
		
		
		 
		/**
		 * 获取目标所在天数
		 */
		public static function getDay(id : int) : int
		{
			return Target[id] ? Target[id][0] : 0;
		}
		
		/**
		 * 获取目标名字
		 */
		public static function getName(id : int) : String
		{
			return Target[id] ? Target[id][1] : \"\";
		}
		/**
		 * 获取目标标识
		 */
		public static function getSign(id : int) : String
		{
			return Target[id] ? Target[id][2] : \"\";
		}
		/**
		 * 获取目标描述
		 */
		public static function getDescription(id : int) : String
		{
			return Target[id] ? Target[id][3] : \"\";
		}
		
		/**
		 * 获取目标奖励类型
		 */
		public static function getType(id : int) : int
		{
			return Target[id] ? Target[id][4] : 0;
		}
		
		/**
		 * 获取目标总值
		 */
		public static function getTotal(id : int) : int
		{
			return Target[id] ? Target[id][5] : 0;
		}
		
		/**
		 * 获取目标排序
		 */
		public static function getSort(id : int) : int
		{
			return Target[id] ? Target[id][6] : 0;
		}
		
		/**
		 * 获取目标奖励铜钱
		 */
		public static function getCoin(id : int) : int
		{
			return Target[id] ? Target[id][7] : 0;
		}
		
		/**
		 * 获取目标奖励声望
		 */
		public static function getFame(id : int) : int
		{
			return Target[id] ? Target[id][8] : 0;
		}
		
		/**
		 * 获取目标奖励阅历
		 */
		public static function getSkill(id : int) : int
		{
			return Target[id] ? Target[id][9] : 0;
		}
		
		/**
		 * 获取目标体力
		 */
		public static function getPower(id : int) : int
		{
			return Target[id] ? Target[id][10] : 0;
		}
		
		/**
		 * 获取目标奖励灵石
		 */
		public static function getStone(id : int) : int
		{
			return Target[id] ? Target[id][11] : 0;
		}
		
		/**
		 * 获取目标奖励物品id
		 */
		public static function getItemId(id : int) : int
		{
			return Target[id] ? Target[id][12] : 0;
		}
		
		/**
		 * 获取目标奖励物品数量
		 */
		public static function getItemAmount(id : int) : int
		{
			return Target[id] ? Target[id][13] : 0;
		}
		
		/**
		* 汇总
		*/
		public static function getAll(id : int) : Object
		{
			var obj : Object = {};
			var list : Array = Target[id] ? Target[id] : [];
			var nameList : Array = [\"day\",\"name\",\"sign\",\"description\",\"type\",\"total\",\"sort_order\",\"coin\",\"fame\",\"skill\",\"power\",\"stone\",\"item_id\",\"item_amount\"];
			var len : int = list.length;
			for(var i : int = 0;i < len;i++)
			{
				obj[nameList[i]] = list[i];
			}
			return obj;
		}
	}
}
";

file_put_contents($desc_dir."NewComerTargetType.as", addons().$str);

echo "[data] newComerTargetType [Done]\n";
?>