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
   `state_point`,
   `coin`,
   `fame`,
   `skill`,
   `items_id`,
   `items_count`,
   `frame`
FROM consumption_draw_items
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		[".$item["id"].",".$item["state_point"].",".$item["coin"].",".$item["fame"].
		",".$item["skill"].",".$item["items_id"].",".$item["items_count"].",".$item["frame"]."]";
}

### 类

$str = "package com.assist.server
{
	public class ConsumptionDrawType
	{
		//id，境界点,铜钱,声望，阅历，物品id，物品数量，帧数
		public static var List : Array = [
			".$hash1."
		];

		/**
		*根据id获取数据
		*/
		public static function getData(id : int) : Array
		{
			for each(var list : Array in List)
			{
				if(list[0] == id)
					return list;
			}
			return null;
		}

		/**
		*获取帧
		*/
		public static function getFrame(id : int) : int
		{
			if(id == 0)
			{
				return 0;
			}

			return getData(id)[7];
		}

		/**
		*获取名字和数量
		*/
		public static function getNameAndNum(id : int) : Object
		{
			var obj : Object = {};
			if(id == 0)
			{
				obj.name = \"\";
				obj.num = 0;
				return obj;
			}
			if(getData(id)[1] != 0)
			{
				obj.name = \"境界点\";
				obj.num = getData(id)[1];
			}else if(getData(id)[2] != 0)
			{
				obj.name = \"铜钱\";
				obj.num = getData(id)[2];
			}else if(getData(id)[3] != 0)
			{
				obj.name = \"声望\";
				obj.num = getData(id)[3];
			}else if(getData(id)[4] != 0)
			{
				obj.name = \"阅历\";
				obj.num = getData(id)[4];
			}else if(getData(id)[5] != 0)
			{
				obj.name = ItemType.getName(getData(id)[5]);
				obj.num = getData(id)[6];
			}
			return obj;
		}
	}
}";

file_put_contents($desc_dir."ConsumptionDrawType.as", addons().$str);

echo("[data] ConsumptionDrawType DONE\n");
?>