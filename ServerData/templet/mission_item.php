<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `mission_id`, `item_id`, `number` from `mission_item`;");

$hash = ""; 
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["mission_id"].",".$item["item_id"].",".$item["number"]."]";
}

### 类

$str = "package com.assist.server
{
	public class MissionItemType
	{
	
	// 强化等级， 物品类型ID, 物品品质, 强化价格
        private static const _missionItem : Array =
        [
". $hash ."
        ]
	
        /**
	 * 通过副本ID查询掉落的物品
	 * {missionId, itemId, number}
	 */
	 public static function getItemIdForMissionId (missionId : int) : Array
	 {
		var list : Array = [];
		var len : int = _missionItem.length;
		for(var i : int = 0; i < len; i++)
		{
			var missionList : Array = _missionItem[i];
			if(missionList[0] == missionId)
			{
			        var obj : Object = {};
				obj.missionId = missionId;
				obj.itemId = missionList[1];
				obj.number = missionList[2];
				list.push(obj);
			}
		}
		
		return list;
	 }
	 
	 /**
	 * 通过物品ID查询掉落的副本
	 * {missionId, itemId, number}
	 */
	 public static function getMissionIdForItemId (itemId : int) : Array
	 {
		var list : Array = [];
		var len : int = _missionItem.length;
		for(var i : int = 0; i < len; i++)
		{
			var missionList : Array = _missionItem[i];
			if(missionList[1] == itemId)
			{
			        var obj : Object = {};
				obj.missionId = missionList[0];
				obj.itemId = missionList[1];
				obj.number = missionList[2];
				list.push(obj);
			}
		}
		
		return list;
	 }
	 
    }
}
";

file_put_contents($desc_dir."MissionItemType.as", addons().$str);

echo "[data]  mission_item [Done]\n";
?>