<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### feats_exchange

$list = $dbh->query("
	select
		`id`, `item_id`, `value`,`feats`,`feats_lv`
	from
		`feats_item`
");

$list1 = $dbh->query("
	select
		`feats_lv`, `feats`
	from
		`feats_lv`
");



$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["id"].",".$item["item_id"].",".$item["value"].",".$item["feats"].",".$item["feats_lv"]."]";
}

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["feats_lv"]." : [".$item["feats"]."]";
}



### 类

$str = "package com.assist.server
{
	public class FeatsExchangeType
	{
		// 功勋  id ：[物品id 物品数量 功勋值 功勋等级]
		private static const Feats : Array = [
".$hash."
		];
	
		// 功勋等级 ：[功勋值]
		private static const FeatsLv : Object = {
		".$hash1."	
		}
		
		/**
		 * 根据功勋等级获取 该等级对应的所有物品
		 */
		public static function featsList() : Array
		{
			var rList : Array = [];
			var len : int = Feats.length;
			for(var i : int = 0;i < len;i++)
			{
				var obj : Object = featsInfo(Feats[i]);
				rList.push(obj);
			}
			rList.sortOn(\"needFeatsLevel\",Array.NUMERIC);
			return rList;
		}
		
		/**
		 * 根据功勋等级获取 该等级对应的第一个物品 信息
		 */
		public static function featsInfo(list : Array) : Object
		{
			var obj : Object = {};
			obj.id = list[0];
			obj.itemId = list[1];
			obj.itemNum = list[2];
			obj.needFeats = list[3];
			obj.needFeatsLevel = list[4];
			return obj;
		}
		
		/**
		 *根据功勋等级获取功勋值
		*/
		public static function getFeatsFormLv(lv : int) : int
		{
			return FeatsLv[lv] ? FeatsLv[lv][0] : 0;
		}
		
	}
}
";

file_put_contents($desc_dir."FeatsExchangeType.as", addons().$str);

echo "[data] featsExchangeType [Done]\n";
?>