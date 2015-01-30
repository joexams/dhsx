<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### faction_quest

$list = $dbh->query("
	select
		`id`, `sign`, `title`,`discribe`,`award_faction_con`,`need_gold_room_lv`,`use_item_id`,`init_coin`,`need_coin`,`type`,`npc_id`
	from
		`faction_quest`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\",\"".$item["title"]."\",\"".$item["discribe"]."\",".$item["award_faction_con"].",".$item["need_gold_room_lv"].",".$item["use_item_id"].",".$item["init_coin"].",".$item["need_coin"].",".$item["type"].",".$item["npc_id"]."]";
}

### 类

$str = "package com.assist.server
{
	public class FactionQuestType
	{
		// [帮派任务id : 英文标识 任务标题 任务描述 奖励帮贡 需要金库等级 给于的物品,帮助完成任务 初始铜钱 需要铜钱 0-跑商任务 发布任务npc]
		private static const FactionQuest : Object = {
".$hash."
		};
		 
		//可接任务
		public static const Acceptable : int = 0;
		
		//已接任务
		public static const Accepted : int = 1
		
		//已完成任务
		public static const Completed : int = 2;
		
		//不可接任务
		public static const UnAccepte : int = 3;
		
		/**
		 * 获取 帮派任务 英文标识
		 */
		public static function getQuestSign(id : int) : String
		{
			return FactionQuest[id] ? FactionQuest[id][0] : \"\";
		}
		
		/**
		 * 获取 帮派任务 任务标题
		 */
		public static function getQuestTitle(id : int) : String
		{
			return FactionQuest[id] ? FactionQuest[id][1] : \"\";
		}
		
		/**
		 * 获取 帮派任务 任务描述
		 */
		public static function getQuestDescribe(id : int) : String
		{
			return FactionQuest[id] ? FactionQuest[id][2] : \"\";
		}
		
		/**
		 * 获取 帮派任务 奖励帮贡
		 */
		public static function getQuestAwardCon(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][3] : 0;
		}
		
		/**
		 * 获取 帮派任务 需要金库等级
		 */
		public static function getQuestGoldRoomLv(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][4] : 0;
		}
		
		/**
		 * 获取 帮派任务 给于的物品,帮助完成任务
		 */
		public static function getQuestUseItemId(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][5] : 0;
		}
		
		/**
		 * 获取 帮派任务 初始铜钱
		 */
		public static function getQuestInitCoin(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][6] : 0;
		}
		
		/**
		 * 获取 帮派任务 需要铜钱
		 */
		public static function getQuestNeedCoin(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][7] : 0;
		}
		
		/**
		 * 获取 帮派任务 0-跑商任务
		 */
		public static function getQuestType(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][8] : 0;
		}
		
		/**
		 * 获取 帮派任务 发布任务npc
		 */
		public static function getQuestNPCId(id : int) : int
		{
			return FactionQuest[id] ? FactionQuest[id][9] : 0;
		}
	}
}
";

file_put_contents($desc_dir."FactionQuestType.as", addons().$str);

echo "[data] factionQuestType [Done]\n";
?>