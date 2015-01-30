<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### mission_failed_tips_typ

$list = $dbh->query("select `id`, `tips_sign`, `tips_name`, `description` from `mission_failed_tips_type`;");

$hash = "";
$constant = "";
$const_list = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$tips_sign = $item["tips_sign"];
	$hash .= "			".$item["id"]." : ["."\"".$tips_sign."\"".", \"".$item["description"]."\"] /* ".$item["tips_name"]." */";
	
	$const_list .= "        public static const " . $tips_sign. " : String = \"" . $tips_sign . "\";\n";
}

### mission_failed_tips

$list = $dbh->query("select `mission_id`, `mission_failed_tips_type_id` from `mission_failed_tips`;");

$max_mission_id = 0;
$arr = array();
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($max_mission_id < $item["mission_id"]) {
		$max_mission_id = $item["mission_id"];
	}
	
	if (! array_key_exists($item["mission_id"], $arr)) {
		$arr[$item["mission_id"]] = "";
	}
	
	if ($arr[$item["mission_id"]] != "") {
		$arr[$item["mission_id"]] .= ", ";
	}
	
	$arr[$item["mission_id"]] .= $item["mission_failed_tips_type_id"];
}

$hash1 = "";
for ($i = 0; $i <= $max_mission_id; $i++) {
	if (array_key_exists($i, $arr) == false) continue;
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$i." : [".$arr[$i]."]";
}

### 类

$str = "package com.assist.server
{
	public class MissionFailedTipsType
	{
         /**
		 * 功能指引标识
		 */
".$const_list."
		// id : sign
		private static const Types : Object = {
".$hash."
		};
		
		// mission_id : [id1, id2, ..., idN]
		private static const Tips : Object = {
".$hash1."
		};
		
		/**
		 * 副本是否有提示
		 * 
		 * @param missionId int
		 */
		public static function hasTips (missionId : int) : Boolean
		{
			return !!Tips[missionId];
		}
		
		/**
		 * 获取副本关联的提示
		 * 
		 * @param missionId int
		 */
		public static function getTipsByMissionId (missionId : int) : Array
		{
			var temp : Array = Tips[missionId] || [];
			var arr : Array = [];
			
			var len : int = temp.length;
			for (var i : int = 0; i < len; i++)
			{
				arr[i] = [Types[temp[i]][0], Types[temp[i]][1]];
			}
			
			return arr;
		}
	}
}
";

file_put_contents($desc_dir."MissionFailedTipsType.as", addons().$str);

echo "[data] mission_failed_tips_type  [Done]\n";
?>