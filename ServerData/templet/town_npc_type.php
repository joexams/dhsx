<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `town_id`, `npc_id` from `town_npc`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["town_id"].", ".$item["npc_id"]."]";
}

### town_npc_soul

$list = $dbh->query("select `town_npc_id`, `soul_id` from `town_npc_soul`");
$obj = array();

for ($i = 0; $i < count($list); $i++) {
	$town_npc_id = $list[$i]["town_npc_id"];
	if (false == array_key_exists($town_npc_id, $obj)) {
		$obj[$town_npc_id] = array();
	}
	array_push($obj[$town_npc_id], $list[$i]["soul_id"]);
}

$hash1 = "";
foreach ($obj as $town_npc_id => $list) {
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	sort($list);
	$hash1 .= "			".$town_npc_id." : [".join(",", $list)."]";
}

### 类

$str = "package com.assist.server
{
	public class TownNPCType
	{
		// town_npc_id : [town_id, npc_id]
		private static const List : Object = {
".$hash."
		};
		
		// town_npc_id : [soul_id, soul_id, ...]
		private static const TownNPCSoul : Object = {
".$hash1."
		};
		
		/**
		 * 获取城镇id
		 * 
		 * @param townNPCId int
		 */
		public static function getTownId (townNPCId : int) : int
		{
			return List[townNPCId] ? List[townNPCId][0] : 0;
		}
		
		/**
		 * 获取npcid
		 * 
		 * @param townNPCId int
		 */
		public static function getNPCId (townNPCId : int) : int
		{
			return List[townNPCId] ? List[townNPCId][1] : 0;
		}
		
		/**
		 * 获取npc可兑换灵件id列表
		 * @param townNPCId int
		 */
		public static function getNPCSoulIdList (townNPCId : int) : Array
		{
			return TownNPCSoul[townNPCId] || [];
		}
	}
}
";

file_put_contents($desc_dir."TownNPCType.as", addons().$str);

echo "[data] town_npc_type  [Done]\n";
?>