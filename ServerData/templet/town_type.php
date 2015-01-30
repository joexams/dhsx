<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### town

$list = $dbh->query("select `id`, `sign`, `name`, `lock`, `description` from `town`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\", \"".$item["name"]."\", ";
	
	$str = "{";
	
	$list1 = $dbh->query("
		select
			`tn`.`id` as `town_npc_id`,
			`n`.`sign` as `sign`,
			`tn`.`position_x` as `position_x`,
			`tn`.`position_y` as `position_y`
		from
			`town_npc` `tn`,
			`npc` n
		where
			`town_id` = ".$item["id"]."
			and `tn`.`npc_id` = `n`.`id`
	");
	
	$temp = array();
	for ($j = 0; $j < count($list1); $j++) {
		$item0 = $list1[$j];
		$temp[$j] = "\n					".$item0["sign"]." : [".$item0["town_npc_id"].", ".$item0["position_x"].", ".$item0["position_y"]."]";
	}
	
	$str .= join(",", $temp)."\n				},";
	
	$hash .= $str.$item["lock"].", \"".$item["description"]."\"]";
	
	$constant .= "		// ".$item["name"]."\n";
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### 类

$str = "package com.assist.server
{
	import com.haloer.data.oObject;
	
	public class TownType
	{
		public static var MaxOldMap:int = 35;//人间城镇的最大值
		// town_id : [town_sign, town_name, {npc_sign : [town_npc_id, position_x, position_y], ...}, lock, description]
		private static var _Towns : Object;
		
		public static function get Towns () : Object
		{
			if (_Towns == null) throw new Error(\"还未赋值！\");
			
			return _Towns;
		}
		
		public static function set Towns (value : Object) : void
		{
			if (_Towns != null) throw new Error(\"非法赋值\");
			
			_Towns = value;
		}
		
".$constant."
		
		/// 数据库还没有的数据
		
		// 大理
		public static const DaLi : String = \"DaLi\";
		// 郑州
		public static const ZhengZhou : String = \"ZhengZhou\";
		
		//----------------------------------------------------------------------
		//
		//  活动
		//
		//----------------------------------------------------------------------
		
		// 多人副本
		public static const MultiMission : String = \"MultiMission\";
		
		// 赛神兽
		public static const HorseRace : String = \"HorseRace\";
		
		// 日常任务
		public static const DailyQuest : String = \"DailyQuest\";
		
		// 竞技场
		public static const Sport : String = \"Sport\";
		
		// 离线经验
		public static const OfflineExp : String = \"OfflineExp\";
		
		// 防沉迷系统
		public static const PreventIndulge : String = \"PreventIndulge\";
		
		// 在线奖励
		public static const OnlineGift : String = \"OnlineGift\";
		
		// 阵营站/国战
		public static const CampWar : String = \"CampWar\";
		
		// 送元宝
		public static const SendIngot : String = \"SendIngot\";
		
		// 门派俸禄
		public static const CampSalary : String = \"CampSalary\";
		
		// 全部活动
		public static const All : String = \"All\";
		
		//----------------------------------------------------------------------
		//
		//  定义
		//
		//----------------------------------------------------------------------
		
		// 已开放的townKey值
		private static var _lock : int = 0;
		
		public static function set lock (value : int) : void
		{
			_lock = value;
		}
		
		// 所属阵营
		private static var _campSign : String = \"\";
		
		public static function set campSign (value : String) : void
		{
			_campSign = value;
		}
		
		// 所有城镇
		public static const AllTown : Boolean = true;
		
		//----------------------------------------------------------------------
		//
		//  城镇id，城镇名称，城镇标识
		//
		//----------------------------------------------------------------------
		
		public static function getId (townSign : String) : int
		{
			var townId : int = 0;
			for (var id : Object in Towns)
			{
				if (Towns[id][0] == townSign)
				{
					townId = id as int;
					break;
				}
			}
			
			return townId;
		}
		
		public static function getIdByTownNPCId (id : int) : int
		{
			for (var townId : Object in Towns)
			{
				var npcs : Object = Towns[townId][2];
				for each (var obj : Object in npcs)
				{
					if (id == obj[0])
					{
						return townId as int;
					}
				}
			}
			
			return 0;
		}
		
		public static function getSign (townId : int) : String
		{
			return Towns[townId] ? Towns[townId][0] : \"\";
		}
		
		public static function getName (townId : int) : String
		{
			return Towns[townId] ? Towns[townId][1] : \"\";
		}
		
		public static function getNameBySign (townSign : String) : String
		{
			var id : int = getId(townSign);
			
			return getName(id);
		}
		
		public static function getLock (townId : int) : int
		{
			return Towns[townId] ? Towns[townId][3] : 0;
		}
		
		/**
		 * 已开启的城镇的标识列表
		 *
		 * @param all Boolean
		 * 是否返回所有城镇，即是否包含非自己阵营的城镇
		 */
		public static function getOpenedTownSigns (all : Boolean = false) : Array
		{
			var list : Array = getOpenedTownIds(all);
			
			var len : int = list.length;
			for (var i : int = 0; i < len; i++)
			{
				list[i] = Towns[list[i]][0];
			}
			
			return list;
		}
		
		/**
		 * 已开启的城镇的id列表
		 *
		 * @param all Boolean
		 * 是否返回所有城镇，即是否包含非自己阵营的城镇
		 */
		public static function getOpenedTownIds (all : Boolean = false) : Array
		{
			var list : Array = [];
			
			var tempLock : int = 0;
			var tempTownId : int = 0;
			
			var campTownId   : int = TownType.getId(_campSign);
			var campTownLock : int = TownType.getLock(campTownId);
			
			for (var id : Object in Towns)
			{
				var townId : int = id as int;
				var item : Object = Towns[townId];
				var itemLock : int = item[3];
				
				if (all == false && itemLock == campTownLock && campTownId != townId)
				{
					continue;
				}
				
				if (itemLock <= _lock && isTownById(townId))
				{
					list.push({id : townId, lock : itemLock});
				}
			}
			
			list.sortOn(\"lock\", Array.NUMERIC);
			
			var len : int = list.length;
			for (var i : int = 0; i < len; i++)
			{
				list[i] = list[i][\"id\"];
			}
			
			return list;
		}
		
		/**
		 * 已开启的新天界城镇的标识列表
		 *
		 */
		public static function getNewOpenedTownSigns () : Array
		{
			var arr:Array = new Array();
			var list : Array = getOpenedTownIds(true);
			
			var len : int = list.length;
			for (var i : int = 0; i < len; i++)
			{
				if(list[i] > MaxOldMap)
				{
					arr.push(Towns[list[i]][0]);
				}
			}
			
			return arr;
		}
		
		/**
		 * 已开启的新天界城镇的标识列表
		 *
		 */
		public static function getOldOpenedTownSigns () : Array
		{
			var arr:Array = new Array();
			var list : Array = getOpenedTownIds(true);
			
			var len : int = list.length;
			for (var i : int = 0; i < len; i++)
			{
				if(list[i] <= MaxOldMap)
				{
					arr.push(Towns[list[i]][0]);
				}
			}
			return arr;
		}
		
		/**
		 * 已开启的城镇列表
		 */
		public static function getOpenedTownInfo () : Array
		{
			var list : Array = [];
			
			for each (var item : Array in Towns)
			{
				if (item[3] > _lock || isTownBySign(item[0]) == false) continue;
				
				list.push({
					name : item[0],
					info : item[4],
					level : \"\"
				});
			}
			
			return list;
		}
		
		/**
		 * 通过lock获取玩家能进入的最高级别城镇id
		 */
		public static function getMaxTownId () : int
		{
			var tempLock : int = 0;
			var tempTownId : int = 0;
			
			var campTownId   : int = TownType.getId(_campSign);
			var campTownLock : int = TownType.getLock(campTownId);
			
			for (var id : Object in Towns)
			{
				var townId : int = id as int;
				var item : Object = Towns[townId];
				var itemLock : int = item[3];
				
				if (itemLock == campTownLock && campTownId != townId)
				{
					continue;
				}
				
				if (itemLock <= _lock && itemLock > tempLock)
				{
					tempLock = itemLock;
					tempTownId = townId;
				}
			}
			
			return tempTownId;
		}
		
		/**
		 * 获取上一个城镇id
		 * @param townId int
		 */
		public static function getPrevTownIdByTownId (_townId : int) : int
		{
			var lock : int = getLock(_townId);
			
			var tempLock : int = 0;
			var tempTownId : int = 0;
			
			var campTownId   : int = TownType.getId(_campSign);
			var campTownLock : int = TownType.getLock(campTownId);
			
			for (var id : Object in Towns)
			{
				var townId : int = id as int;
				var item : Object = Towns[townId];
				var itemLock : int = item[3];
				
				if (itemLock == campTownLock && campTownId != townId)
				{
					continue;
				}
				
				if (itemLock < lock && itemLock > tempLock)
				{
					tempLock = itemLock;
					tempTownId = townId;
				}
			}
			
			return tempTownId;
		}
		
		/**
		 * 获取下一个城镇id
		 * @param townId int
		 */
		public static function getNextTownIdByTownId (_townId : int) : int
		{
			var lock : int = getLock(_townId);
			
			// 设置一个任意极大值
			var tempLock : int = 10000000;
			var tempTownId : int = 0;
			
			var campTownId   : int = TownType.getId(_campSign);
			var campTownLock : int = TownType.getLock(campTownId);
			
			for (var id : Object in Towns)
			{
				var townId : int = id as int;
				var item : Object = Towns[townId];
				var itemLock : int = item[3];
				
				if (itemLock == campTownLock && campTownId != townId)
				{
					continue;
				}
				
				if (itemLock > lock && itemLock < tempLock)
				{
					tempLock = itemLock;
					tempTownId = townId;
				}
			}
			
			return tempTownId;
		}
		
		/**
		 * 是否为城镇
		 *
		 * @param sign String
		 */
		public static function isTownBySign (sign : String) : Boolean
		{
			return(
				sign != BossChiYanShou
				&& sign != BossQingTianMu
				&& sign != BossDaoBaTu
				//&& sign != BossBaiZe
				//&& sign != BossQingLong
				&& sign != JiHuiSuo
				&& sign != BaiHuFactionWar
				&& sign != XuanWuFactionWar
				&& sign != ZhuQueFactionWar
				&& sign != QingLongFactionWar
				&& sign != BangPaiJuDian
				&& sign != HuoLingTa
				&& sign != ShuiLingTa
				&& sign != MuLingTa
			);
		}
		
		/**
		 * 是否为城镇
		 *
		 * @param id int
		 */
		public static function isTownById (id : int) : Boolean
		{
			return isTownBySign(getSign(id));
		}
		
		/**
		 * 判断城镇是否开启
		 *
		 * @param townSign String
		 */
		public static function isOpened (townSign : String) : Boolean
		{
			var townId : int = getId(townSign);
			var lock : int = getLock(townId);
			
			return _lock >= lock;
		}
		
		/**
		 * 判断城镇是否开启
		 *
		 * @param id int
		 */
		public static function isOpenedByTownId (id : int) : Boolean
		{
			var lock : int = getLock(id);
			
			return _lock >= lock;
		}
		
		/**
		 * 获取城镇id
		 *
		 * @param townSign String
		 */
		public static function getTownIdByLock (lock : int) : int
		{
			var townId : int = 0;
			
			for (var id : Object in Towns)
			{
				var item : Object = Towns[id];
				var itemLock : int = item[3];
				
				if (itemLock <= lock && isTownById(id as int))
				{
					townId = Math.max(id as int, townId);
				}
			}
			
			return townId;
		}
		
		//----------------------------------------------------------------------
		//
		//  城镇npc功能
		//
		//----------------------------------------------------------------------
		
		private static function getTownInfo (townId : int) : Array
		{
			var arr : Array = Towns[townId];
			
			if (! arr)
			{
				throw new Error(\"不存在townId为的 \" + townId + \" 城镇！\");
			}
			
			return arr;
		}
		
		/**
		 * 获取npc标识
		 *
		 * @param id int
		 */
		public static function getNPCSignByTownNPCId (id : int) : String
		{
			for each (var arr : Object in Towns)
			{
				var hash : Object = arr[2];
				for (var sign : String in hash)
				{
					if (hash[sign][0] == id)
					{
						return sign;
					}
				}
			}
			
			return \"\";
		}
		
		/**
		 * 获取town_npc_id
		 *
		 * @param townId int
		 * @param npcSign : String
		 */
		public static function getTownNPCId (townId : int, npcSign : String) : int
		{
			var arr : Array = getTownInfo(townId);
			
			if (! arr[2][npcSign])
			{
				throw new Error(arr[1] + \"不存在npc:\" + npcSign);
			}
			
			return arr[2][npcSign][0];
		}
		
		/**
		 * 获取功能npc的town_npc_id
		 * 
		 * @param townId int
		 * 城镇id
		 * @param func : int
		 * 功能id
		 */
		public static function getTownNPCIdByFunc (townId : int, func : int) : int
		{
			var arr : Array = getTownInfo(townId);
			
			var townNPCId : int = 0;
			
			for (var npcSign : String in arr[2])
			{
				if (NPCType.getFunctionBySign(npcSign) == func)
				{
					townNPCId = arr[2][npcSign][0];
					break;
				}
			}
			
			return townNPCId;
		}
		
		/**
		 * 城镇内是否有指定的npc
		 * 
		 * @param townId int
		 * @param npcSign String
		 */
		public static function hasNPCInTown (townId : int, npcSign : String) : Boolean
		{
			var arr : Array = getTownInfo(townId);
			
			return !!arr[2][npcSign];
		}
		
		/**
		 * 获取npc标识
		 * 
		 * @param townId int
		 * @param townNPCId int
		 */
		public static function getNPCSign (townId : int, townNPCId : int) : String
		{
			var arr : Array = getTownInfo(townId);
			
			var sign : String = \"\";
			for (var item : String in arr[2])
			{
				if (arr[2][item][0] == townNPCId)
				{
					sign = item;
				}
			}
			
			if (sign == \"\")
			{
				throw new Error(arr[1] + \"不存在towNPCId:\" + townNPCId);
			}
			
			return sign;
		}
		
		/**
		 * 获取npc的id及坐标对象
		 * 
		 * @param townId int 
		 */
		public static function getNPCList (townId : int) : Object
		{
			var temp : Object = {};
			
			var arr : Array = getTownInfo(townId);
			var npcHash : Object = arr[2];
			
			for (var sign : String in npcHash)
			{
				if (temp[sign] == null)
				{
					temp[sign] = {};
				}
				
				oObject.list(npcHash[sign], temp[sign], [\"id\", \"x\", \"y\"]);
				
				temp[sign][\"name\"] = NPCType.getNameBySign(sign);
				temp[sign][\"type\"] = NPCType.getFunctionBySign(sign);
			}
			
			return temp;
		}
	}
}
";

file_put_contents($desc_dir."TownType.as", addons().$str);
file_put_contents($desc_dir."source/TownTypeData.as", addons()."package com.assist.server.source
{
	public class TownTypeData
	{
		// town_id : [town_sign, town_name, {npc_sign : [town_npc_id, position_x, position_y], ...}, lock, description]
		public static const Towns : Object = {
".$hash."
		};
		
		/*
		public static function init () : void
		{
			TownType.Towns = Towns;
		}
		*/
	}
}
");

echo "[data] town_type  [Done]\n";
?>