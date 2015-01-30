<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### baxian

$list = $dbh->query("
	select
		`id`, `level`, `ba_xian_id`,`player_lv`,`monster_team_id`,`award_item`,`close_pos1`,`close_pos2`,`close_pos3`
	from
		`ba_xian_reqiure`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["level"].",".$item["ba_xian_id"].",".$item["player_lv"].",".$item["monster_team_id"].",".$item["award_item"].",".$item["close_pos1"].",".$item["close_pos2"].",".$item["close_pos3"]."]";
}




### 类

$str = "package com.assist.server
{
	public class BaXianType
	{
		// [id : 轮数 八仙id 需求玩家等级 怪物团 奖励八仙令数量 关闭位置1 关闭位置2 关闭位置3]
		private static const BaXian : Object = {
".$hash."
		};
		
		
		 /**
		  * 根据轮数、八仙id获取3个位置的锁id
		  */
		public static function getLock(lv : int,id : int) : Object
		{
			var obj : Object = {};
			for each(var ary : Array in BaXian)
			{
				if(lv == ary[0] && id == ary[1])
				{
					obj.lock1 = ary[5];
					obj.lock2 = ary[6];
					obj.lock3 = ary[7];
				}
			}
			return obj;
		}
		
		 /**
		  * 根据轮数、八仙id获取该仙的需求等级
		  */
		public static function getLevel(lv : int,id : int) : int
		{
			for each(var ary : Array in BaXian)
			{
				if(lv == ary[0] && id == ary[1])
				{
					return ary[2]
				}
			} 
			return 100;
		}
		
			/**
		 * 根据轮数、八仙id获取该仙的需求等级
		 */
		public static function getAward(lv : int,id : int) : int
		{
			for each(var ary : Array in BaXian)
			{
				if(lv == ary[0] && id == ary[1])
				{
					return ary[4]
				}
			} 
			return 0;
		}
	}
}
";

file_put_contents($desc_dir."BaXianType.as", addons().$str);

echo "[data] baXianType [Done]\n";
?>