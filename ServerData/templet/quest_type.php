<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### quest

$list = $dbh->query("
	select
		/* 任务id */
		`id`,
		/* 类型：主干，分支，日常 */
		`type`,
		/* 锁 */
		`lock`,
		/* 需求玩家等级 */
		`level`,
		/* 标题 */
		`title`,
		/* 内容 */
		`content`,
		/* 条件描述 */
		`conditions`,
		/* 任务跟踪描述 */
		`town_text`,
		/* 接受任务的npc */
		`begin_npc_id`,
		/* 完成任务的npc */
		`end_npc_id`,
		/* 奖励的经验 */
		`award_experience`,
		/* 奖励的铜钱 */
		`award_coins`,
		/* 奖励的物品 */
		`award_item_id`,
		/* 是否为对话任务 */
		`is_talk_quest`,
		/* 接受任务前的npc对话 */
		`accept_talk`,
		/* 接受任务后的npc对话 */
		`accepted_talk`,
		/* 完成任务的npc对话 */
		`completed_talk`,
		/* 关联副本id */
		`mission_id`,
		/* 奖励物品数 */
		`award_item_count`
	from
		`quest`;
");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			"
	      .$item["id"]." : ["
		  .$item["type"].", "
		  .$item["lock"].", "
		  .$item["level"].", "
		  ."\"".clearBreak($item["title"])."\", "
		  ."\"".clearBreak($item["content"])."\", "
		  ."\"".clearBreak($item["conditions"])."\", "
		  ."\"".clearBreak($item["town_text"])."\", "
		  ."".$item["begin_npc_id"].", "
		  ."".$item["end_npc_id"].", "
		  ."".$item["award_experience"].", "
		  ."".$item["award_coins"].", "
		  ."".$item["award_item_id"].", "
		  ."".$item["is_talk_quest"].", "
		  ."\"".clearBreak($item["accept_talk"])."\", "
		  ."\"".clearBreak($item["accepted_talk"])."\", "
		  ."\"".clearBreak($item["completed_talk"])."\", "
		  ."".$item["mission_id"].", "
		  ."".$item["award_item_count"].""
		  ."]";
	
	#$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}


function clearBreak ($str) {
	return preg_replace("/\r\n|\r|\n/", "", $str);
}

### 类

$str = "package com.assist.server
{
	public class QuestType
	{
		// 特殊需求的任务id
		include \"./source/QuestTypeData0.as\";
		
		// id : [type, lock, level, title, content, conditions, town_text, begin_npc_id, end_npc_id, award_experience, award_coins, award_item_id, is_talk_quest, accept_talk, accepted_talk, completed_talk, mission_id, award_item_count]
		private static var _Quests : Object = null;
		
		public static function get Quests () : Object
		{
			if (_Quests == null) throw new Error(\"还未赋值！\");
			
			return _Quests;
		}
		
		public static function set Quests (value : Object) : void
		{
			if (_Quests != null) throw new Error(\"非法赋值\");
			
			_Quests = value;
		}
		
		// 主线任务
		public static const Master : int = 1;
		
		// 支线任务
		public static const Slave  : int = 2;
		
		// 日常任务
		public static const Daily  : int = 3;
		
		// 精英任务
		public static const Elite  : int = 4;
		
		// 等级限制
		public static const LevelLimit : int = -1;
		
		// 可接
		public static const Acceptable : int = 0;
		
		// 已接
		public static const Accepted   : int = 1;
		
		// 完成
		public static const Completed  : int = 2;
		
		//----------------------------------------------------------------------
		//
		//  方法
		//
		//----------------------------------------------------------------------
		
		/**
		 * 任务类型
		 *
		 * @param id int
		 */
		public static function getType (id : int) : int
		{
			return Quests[id] ? Quests[id][0] : 0;
		}
		
		/**
		 * 任务锁
		 *
		 * @param id int
		 */
		public static function getLock (id : int) : String
		{
			return Quests[id] ? Quests[id][1] : \"\";
		}
		
		/**
		 * 需求玩家等级
		 *
		 * @param id int
		 */
		public static function getLevel (id : int) : String
		{
			return Quests[id] ? Quests[id][2] : \"\";
		}
		
		/**
		 * 任务标题
		 *
		 * @param id int
		 */
		public static function getTitle (id : int) : String
		{
			return Quests[id] ? Quests[id][3] : \"\";
		}
		
		/**
		 * 任务描述内容
		 *
		 * @param id int
		 */
		public static function getContent (id : int) : String
		{
			return Quests[id] ? Quests[id][4] : \"\";
		}
		
		/**
		 * 任务条件描述
		 *
		 * @param id int
		 */
		public static function getConditions (id : int) : String
		{
			return Quests[id] ? Quests[id][5] : \"\";
		}
		
		/**
		 * 任务跟踪描述
		 *
		 * @param id int
		 */
		public static function getTownText (id : int) : String
		{
			return Quests[id] ? Quests[id][6] : \"\";
		}
		
		/**
		 * 接受任务npc
		 *
		 * @param id int
		 */
		public static function getBeginNPCId (id : int) : int
		{
			return Quests[id] ? Quests[id][7] : 0;
		}
		
		/**
		 * 完成任务npc
		 *
		 * @param id int
		 */
		public static function getEndNPCId (id : int) : int
		{
			return Quests[id] ? Quests[id][8] : 0;
		}
		
		/**
		 * 奖励经验
		 *
		 * @param id int
		 */
		public static function getAwardExperience (id : int) : int
		{
			return Quests[id] ? Quests[id][9] : 0;
		}
		
		/**
		 * 奖励铜钱
		 *
		 * @param id int
		 */
		public static function getAwardCoins (id : int) : int
		{
			return Quests[id] ? Quests[id][10] : 0;
		}
		
		/**
		 * 奖励物品
		 *
		 * @param id int
		 */
		public static function getAwardItemId (id : int) : int
		{
			return Quests[id] ? Quests[id][11] : 0;
		}
		
		/**
		 * 是否为对话型任务
		 *
		 * @param id int
		 */
		public static function getIsTalkQuest (id : int) : Boolean
		{
			return Quests[id] ? Quests[id][12] : false;
		}
		
		/**
		 * 接受任务前对话
		 *
		 * @param id int
		 */
		public static function getAcceptTalk (id : int) : String
		{
			return Quests[id] ? Quests[id][13] : \"\";
		}
		
		/**
		 * 接受任务后对话
		 *
		 * @param id int
		 */
		public static function getAcceptedTalk (id : int) : String
		{
			return Quests[id] ? Quests[id][14] : \"\";
		}
		
		/**
		 * 完成任务对话
		 *
		 * @param id int
		 */
		public static function getCompletedTalk (id : int) : String
		{
			return Quests[id] ? Quests[id][15] : \"\";
		}
		
		/**
		 * 任务关联的副本
		 *
		 * @param id int
		 */
		public static function getMissionId (id : int) : int
		{
			return Quests[id] ? Quests[id][16] : 0;
		}
		
		/**
		 * 奖励物品数目
		 *
		 * @param id int
		 */
		public static function getAwardItemCount (id : int) : int
		{
			return Quests[id] ? Quests[id][17] : 0;
		}
		
		/**
		 * 奖励物品
		 *
		 * @param id int
		 * @param roleId int
		 */
		public static function getAwardItemIdByRoleId (id : int, roleId : int) : int
		{
			var itemId : int = 0;
			var jobSign : String;
			
			if (17 == id)
			{
				jobSign = RoleType.getJobSignByRoleId(roleId);
				
				if (RoleType.FeiYu == jobSign)
				{
					itemId = 367;
				}
				else if (RoleType.JianLing == jobSign)
				{
					itemId = 366;
				}
				else if (RoleType.WuSheng == jobSign)
				{
					itemId = 365;
				}
			}
			else
			{
				itemId = getAwardItemId(id);
			}
			
			return itemId;
		}
	}
}
";

file_put_contents($desc_dir."QuestType.as", addons().$str);
file_put_contents($desc_dir."source/QuestTypeData.as", addons()."package com.assist.server.source
{
	public class QuestTypeData
	{
		// id : [type, lock, level, title, content, conditions, town_text, begin_npc_id, end_npc_id, award_experience, award_coins, award_item_id, is_talk_quest, accept_talk, accepted_talk, completed_talk, mission_id, award_item_count]
		public static const Quests : Object = {
".$hash."
		};
		
		/*
		public static function init () : void
		{
			QuestType.Quests = Quests;
		}
		*/
	}
}
");

echo "[data] quest_type [Done]\n";
?>