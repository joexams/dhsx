<?php
/*
SELECT * FROM mission_section m;
SELECT * FROM mission m;
SELECT * FROM mission_scene m;
SELECT * FROM mission_monster_team m;
SELECT * FROM mission_monster m;
SELECT * FROM mission_video m;
*/

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### mission_section

$list = $dbh->query("select `id`, `lock`, `sign`, `name`, `town_id` from `mission_section`;");

$hash1 = "";
$constant1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
		$constant1 .= "\n";
	}
	
	$hash1 .= "			".$item["id"].": [\""
		.$item["sign"]."\", "
		.$item["lock"].", "
		.$item["town_id"].", "
		."\"".$item["name"]."\"]";
	
	$constant1 .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### mission

$missions = array();

$list = $dbh->query("
	select
		`id`, `mission_section_id`, `name`, `lock`,
		`require_power`, `award_coins`, `award_skill`,
		`award_experience`, `monster_id`, `type`, `mission_video_id`, `is_boss`
	from `mission`
	where
		`is_disable` = 0
");

$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	///
	
	$mission_id = $list[$i]["id"];
	
	if (false == array_key_exists($mission_id, $missions)) {
		list($video_file, $video_lock) = get_mission_video($item["mission_video_id"]);
		
		$missions[$mission_id] = array(
			"video_id"     => $item["mission_video_id"] ? intval($item["mission_video_id"]) : 0,
			"video_file"   => $video_file,
			"video_lock"   => $video_lock,
			"scenes"       => array(),
			"monster_list" => array(),
		);
	}
	
	///
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			"
		.$item["id"]." : ["
		.$item["mission_section_id"].", "
		.$item["lock"].", "
		.$item["require_power"].", "
		.$item["award_coins"].", "
		.$item["award_skill"].", "
		.$item["award_experience"].", "
		.$item["monster_id"]
		.", \"".$item["name"]."\","
		.$item["type"].","
		.$item["is_boss"]
		."]";
}

### mission_item

$list = $dbh->query("select `mission_id`, `item_id` from `mission_item`;");

$items = array();
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$mission_id = $item["mission_id"];
	
	if (array_key_exists($mission_id, $missions) == false) continue;
	
	if (false == array_key_exists($mission_id, $items)) {
		$items[$mission_id] = array();
	}
	
	array_push($items[$mission_id], $item["item_id"]);
}

$hash3 = "";
foreach ($items as $mission_id => $list) {
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			".$mission_id." : [".join(", ", $list)."]";
}
					
### mission_scene

$list = $dbh->query("select `id`, `mission_id`, `lock`, `map` from `mission_scene`");
$len = count($list);
for ($i = 0; $i < $len; $i++) {
	$mission_id = $list[$i]["mission_id"];
	$scene_id = $list[$i]["id"];
	
	if (array_key_exists($mission_id, $missions) == false) continue;
	
	$arr = array(
		"map" => intval($list[$i]["map"]),
		"monsters" => array()
	);
	
	$list1 = $dbh->query("
		select
			`id`, `monster_id`, `position_x`, `position_y`,
			`start_mission_video_id`, `end_mission_video_id`,
			`max_bout_number`, `request_bout_number`,
			`fuhuo_mission_monster_id`, `fuhuo_bout_number` ,`attack_can_not_dead_number`
		from
			`mission_monster_team`
		where
			`mission_scene_id` = ".$scene_id."
			
		order by `lock`
	");
	
	$teams = array();
	
	$len1 = count($list1);
	for ($j = 0; $j < $len1; $j++) {
		$item = $list1[$j];
		
		list($start_video_file, $start_video_lock) = get_mission_video($item["start_mission_video_id"]);
		list($end_video_file, $end_video_lock) = get_mission_video($item["end_mission_video_id"]);
		
		$teams[$j] = array(
			"id" => intval($item["id"]),
			"monster_id" => intval($item["monster_id"]),
			"position_x" => intval($item["position_x"]),
			"position_y" => intval($item["position_y"]),
			"start_video_id"   => intval($item["start_mission_video_id"]),
			"start_video_file" => $start_video_file,
			"start_video_lock" => intval($start_video_lock),
			"end_video_id"     => intval($item["end_mission_video_id"]),
			"end_video_file"   => $end_video_file,
			"end_video_lock" => intval($end_video_lock),
			"max_bout_number" => intval($item["max_bout_number"]),
			"request_bout_number" => intval($item["request_bout_number"]),
			"fuhuo_mission_monster_id" => intval($item["fuhuo_mission_monster_id"]),
			"fuhuo_bout_number" => intval($item["fuhuo_bout_number"]),
			"attack_can_not_dead_number" => intval($item["attack_can_not_dead_number"]),
		);
		
		$monsters = $dbh->query("select `monster_id` from `mission_monster` where `mission_monster_team_id` = ".$item["id"]);
		foreach ($monsters as $value) {
			$missions[$mission_id]["monster_list"][$value["monster_id"]] = 1;
		}
	}
	
	$arr["monsters"] = $teams;
	
	array_push(
		$missions[$mission_id]["scenes"],
		$arr
	);
}


/// 生成外部副本数据

#var_export($missions);

foreach ($missions as $mission_id => $list) {
	file_put_contents($client_dir."assets/templet/mission/".$mission_id.".txt", /*var_export($list, true)*/json_encode($list));
}


### mission_monster_team

/*
$list = $dbh->query("
	select
		`id`, `missioin_scene_id`, `monster_id`,
		`position_x`, `position_y`,
		`start_video_id`, `end_video_id`
	from `mission_monster_team`
");
*/

### mission_monster

$list = $dbh->query("
		select distinct
			`id`, `monster_id`, `position_x`, `position_y`,
			`start_mission_video_id`, `end_mission_video_id`,
			`max_bout_number`, `request_bout_number`,
			`fuhuo_mission_monster_id`, `fuhuo_bout_number` ,`attack_can_not_dead_number`
		from
			`mission_monster_team`
			
		order by `lock`
	");

mkdir("{$client_dir}assets/templet/mision_monster/");

$len = count($list);
for ($j = 0; $j < $len; $j++) {
		$item = $list[$j];
		
		list($start_video_file, $start_video_lock) = get_mission_video($item["start_mission_video_id"]);
		list($end_video_file, $end_video_lock) = get_mission_video($item["end_mission_video_id"]);
		
		$monsterList = array(
			"id" => intval($item["id"]),
			"monster_id" => intval($item["monster_id"]),
			"position_x" => intval($item["position_x"]),
			"position_y" => intval($item["position_y"]),
			"start_video_id"   => intval($item["start_mission_video_id"]),
			"start_video_file" => $start_video_file,
			"start_video_lock" => intval($start_video_lock),
			"end_video_id"     => intval($item["end_mission_video_id"]),
			"end_video_file"   => $end_video_file,
			"end_video_lock" => intval($end_video_lock),
			"max_bout_number" => intval($item["max_bout_number"]),
			"request_bout_number" => intval($item["request_bout_number"]),
			"fuhuo_mission_monster_id" => intval($item["fuhuo_mission_monster_id"]),
			"fuhuo_bout_number" => intval($item["fuhuo_bout_number"]),
			"attack_can_not_dead_number" => intval($item["attack_can_not_dead_number"]),
		);
		
		file_put_contents($client_dir."assets/templet/mision_monster/".$item["id"].".txt", json_encode($monsterList));
	}
	
/// 生成外部副本数据

#var_export($missions);

foreach ($missions as $mission_id => $list) {
	file_put_contents($client_dir."assets/templet/mission/".$mission_id.".txt", /*var_export($list, true)*/json_encode($list));
}

### mission_monster_team

/*
$list = $dbh->query("
	select
		`id`, `missioin_scene_id`, `monster_id`,
		`position_x`, `position_y`,
		`start_video_id`, `end_video_id`
	from `mission_monster_team`
");
*/

### mission_monster

$list = $dbh->query("select `id`, `mission_monster_team_id`, `monster_id` from `mission_monster`");

### mission_video

$list = $dbh->query("select `id`, `file_name`, `name`, `lock` from `mission_video`");

### 类

$str = "package com.assist.server
{
	import flash.geom.Point;
	
	public class MissionType
	{
".$constant1."
		
		// 由 package com.assist.server.source.MissionTypeData.Sections 设置
		// mission_section_id : [sign, lock, town_id, name]
		private static var _Sections : Object = null;
		
		public static function get Sections () : Object
		{
			if (_Sections == null) throw new Error(\"还未赋值！\");
			
			return _Sections;
		}
		
		public static function set Sections (value : Object) : void
		{
			if (_Sections != null) throw new Error(\"非法赋值\");
			
			_Sections = value;
		}
		
		// 由 package com.assist.server.source.MissionTypeData.Missions 设置
		//	mission_id : {mission_section_id, lock, require_power, award_coins, award_skill, award_experience, monster_id, name}
		private static var _Missions : Object = null;
		
		public static function get Missions () : Object
		{
			if (_Missions == null) throw new Error(\"还未赋值！\");
			
			return _Missions;
		}
		
		public static function set Missions (value : Object) : void
		{
			if (_Missions != null) throw new Error(\"非法赋值\");
			
			_Missions = value;
		}
		
		private static var _missionTeamCount : Object = null;
		
		public static function get MissionTeamCount () : Object
		{
			if (_missionTeamCount == null) throw new Error(\"还未赋值！\");
			
			return _missionTeamCount;
		}
		
		public static function set MissionTeamCount (value : Object) : void
		{
			if (_missionTeamCount != null) throw new Error(\"非法赋值\");
			
			_missionTeamCount = value;
		}
		
		// 暂无用
		private static const Scenes : Object = {};
		
		// 由 package com.assist.server.source.MissionTypeData.MissionItems 设置
		// mission_id : [item_id1, item_id2, ..., item_idN]
		private static var _MissionItems : Object = null;
		
		public static function get MissionItems () : Object
		{
			if (_MissionItems == null) throw new Error(\"还未赋值！\");
			
			return _MissionItems;
		}
		
		public static function set MissionItems (value : Object) : void
		{
			if (_MissionItems != null) throw new Error(\"非法赋值\");
			
			_MissionItems = value;
		}
		
		// 副本名称序列
		private static var Indexes : Object = {
			1 : \"一\",
			2 : \"二\",
			3 : \"三\",
			4 : \"四\",
			5 : \"五\",
			6 : \"六\",
			7 : \"七\",
			8 : \"八\",
			9 : \"九\",
			10 : \"十\"
		};
		
		private static const NORMAL : int = 0;
		private static const HERO : int = 1;
		private static const BOSS : int = 2;
		
		//----------------------------------------------------------------------
		//
		//  方法
		//
		//----------------------------------------------------------------------
		
		// 已开放的missionKey值
		private static var _lock : int = 0;
		
		public static function set lock (value : int) : void
		{
			_lock = value;
		}
		
		/**
		 * 获取剧情标识
		 *
		 * @param id int
		 */
		public static function getSectionSign (id : int) : String
		{
			return Sections[id] ? Sections[id][0] : \"\";
		}
		
		/**
		 * 获取剧情id
		 *
		 * @param id int
		 */
		public static function getSectionIdByMissionId (id : int) : int
		{
			return Missions[id] ? Missions[id][0] : 0;
		}
		
		/**
		 * 获取剧情标识
		 *
		 * @param id int
		 */
		public static function getSectionSignByMissionId (id : int) : String
		{
			var sectionId : int = getSectionIdByMissionId(id);
			
			return getSectionSign(sectionId).replace(/(_\d+)+$/, \"\");
		}
		
		/**
		 * 获取副本剧情所在城镇id
		 *
		 * @param id int
		 */
		public static function getTownIdBySectionId (id : int) : int
		{
			return Sections[id] ? Sections[id][2] : 0;
		}
		
		/**
		 * 获取副本所在城镇id
		 *
		 * @param id int
		 */
		public static function getTownIdByMissionId (id : int) : int
		{
			var sectionId : int = getSectionIdByMissionId(id);
			return getTownIdBySectionId(sectionId);
		}
		
		/**
		 * 获取副本剧情名称
		 *
		 * @param id int
		 */
		public static function getSectionName (id : int) : String
		{
			return Sections[id] ? Sections[id][3] : \"\";
		}
		
		/**
		 * 获取副本剧情名称
		 *
		 * @param sign String
		 */
		public static function getSectionNameBySign (sign : String) : String
		{
			for each (var item : Object in Sections)
			{
				if (item[0] == sign)
				{
					return item[3];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 获取副本标识
		 * 
		 * @param id int
		 */
		public static function getMissionSign (id : int) : String
		{
			return getSectionSignByMissionId(id);
		}
		
		public static function getMissionLock (id : int) : int
		{
			return Missions[id] ? Missions[id][1] : 0;
		}
		
		public static function getMissionPower (id : int) : int
		{
			return Missions[id] ? Missions[id][2] : 0;
		}
		
		/**
		 * 获取副本奖励铜钱
		 *
		 * @param townId int
		 */
		public static function getMissionAwardCoins (id : int) : int
		{
			return Missions[id] ? Missions[id][3] : 0;
		}
		
		/**
		 * 获取副本奖励体力
		 *
		 * @param townId int
		 */
		public static function getMissionAwardSkill (id : int) : int
		{
			return Missions[id] ? Missions[id][4] : 0;
		}
		
		/**
		 * 获取副本奖励经验
		 *
		 * @param townId int
		 */
		public static function getMissionAwardExp (id : int) : int
		{
			return Missions[id] ? Missions[id][5] : 0;
		}
		
		/**
		 * 获取副本怪物id
		 * 
		 * @param id int
		 */
		public static function getMissionMonsterId (id : int) : int
		{
			return Missions[id] ? Missions[id][6] : 0;
		}
		
		/**
		 * 获取副本名称
		 * 
		 * @param id int
		 */
		public static function getMissionName (id : int) : String
		{
			return Missions[id] ? Missions[id][7] : \"\";
		}
		
		/**
		 * 判断副本是否开启
		 *
		 * @param id int
		 */
		public static function isOpenedByMissionId (id : int) : Boolean
		{
			var lock : int = getMissionLock(id);
			
			return _lock >= lock;
		}
		
		/**
		 * 获取本地副本名称
		 * 
		 * @param id int
		 */
		public static function getLocalMissionName (id : int) : String
		{
			var name : String = Missions[id] ? Missions[id][7] : \"\";
			
			var nums : Array = /(\d+)/.exec(name);
			if (nums && nums.length > 1)
			{
				name = name.replace(nums[1], numberToChs(nums[1]));
			}
			
			return name;
		}
		
		/**
		 * 获取副本类型
		 * 
		 * @param id int
		 */
		public static function getMissionType (id : int) : int
		{
			return Missions[id] ? Missions[id][8] : 0;
		}
		
		/**
		 * 是不是普通副本
		 *
		 * @param id int
		 */
		public static function isNormalMission (id : int) : Boolean
		{
			return getMissionType(id) == NORMAL;
		}
		
		/**
		 * 是不是英雄副本
		 *
		 * @param id int
		 */
		public static function isHeroMission (id : int) : Boolean
		{
			return getMissionType(id) == HERO;
		}
		
		/**
		 * 是不是Boss副本
		 *
		 * @param id int
		 */
		public static function isBossMission (id : int) : Boolean
		{
			return Missions[id] ? Missions[id][9] == 1 : false;
		}
		
		/**
		 * 获取副本奖励物品
		 *
		 * @param townId int
		 */
		public static function getMissionAwardItems (id : int) : Array
		{
			return MissionItems[id] ? MissionItems[id] : [];
		}
		
		/**
		 * 剧情中的副本顺序
		 * 
		 * @param id int
		 * mission_id
		 */
		public static function getMissionOrder (id : int) : int
		{
			var name : String = getMissionName(id);
			
			return parseInt(name.replace(/[^\d]/g, \"\"));
		}
		
		/**
		 * 获取城镇的所有副本剧情id列表（与剧情动画不一样）
		 * 
		 * @param townId int
		 */
		public static function getSectionIdsByTownId (townId : int) : Array
		{
			var arr : Array = [];
			
			for (var sectionId : String in Sections)
			{
				var tempId : int = Sections[sectionId][2];
				
				if (tempId == townId)
				{
					arr.push(parseInt(sectionId));
				}
			}
			
			return arr;
		}
		
		/**
		 * 获取城镇的所有副本
		 *
		 * @param townId int
		 */
		public static function getMissionIdsByTownId (townId : int) : Array
		{
			var arr : Array = [];
			
			var sectionIds : Array = getSectionIdsByTownId(townId);
			
			for (var id : Object in Missions)
			{
				var item : Array = Missions[id];
				
				if (sectionIds.indexOf(item[0]) > -1)
				{
					arr.push(id);
				}
			}
			
			return arr;
		}
		
		/**
		 * 获取副本怪物波数
		 */
		 public static function getMissionTeamNum (missionId : int) : int
		 {
			return MissionTeamCount[missionId] || 0;
		 }
		 
		/**
		 * 获取副本数据
		 *
		 * @param id : int
		 * @param hero Boolean
		 */
		public static function getMissionDataByMissionId (id : int) : Object
		{
			var type : int = getMissionType(id);
			
			var townId : int = getTownIdByMissionId(id);
			var temp : Array = getMissionListByTownId(townId, type);
			
			var obj : Object = {};
			
			for each (var item : Object in temp)
			{
				if (item[\"id\"] == id)
				{
					obj = item;
					break;
				}
			}
			
			return obj;
		}
		
		// 缓存城镇的副本数据
		private static var _missionList : Object = {};
		
		public static function getNormalMissionListByTownId (townId : int) : Array
		{
			return getMissionListByTownId(townId, NORMAL);
		}
		
		public static function getHeroMissionListByTownId (townId : int) : Array
		{
			return getMissionListByTownId(townId, HERO);
		}
		
		public static function getBossMissionListByTownId (townId : int) : Array
		{
			return getMissionListByTownId(townId, BOSS);
		}
		
		/**
		 * 获取城镇的所有副本
		 *
		 * @param townId int
		 * @param type int
		 * 副本类型
		 */
		public static function getMissionListByTownId (townId : int, type : int) : Array
		{
			if (! _missionList[townId])
			{
				_missionList[townId] = {};
			}
			
			var obj : Array = _missionList[townId][type];
			if (obj)
			{
				return obj;
			}
			
			var arr : Array = [];
			
			var sectionIds : Array = getSectionIdsByTownId(townId);
			
			var i : int, len : int;
			
			for (var id : Object in Missions)
			{
				var missionId : int = id as int;
				var item : Array = Missions[missionId];
				
				if (getMissionType(missionId) != type) {
					continue;
				}
				
				var awardItems : Array = getMissionAwardItems(missionId);
				var itemNames : Array = [];
				var itemColors : Array = [];
				
				len = awardItems.length;
				for (i = 0; i < len; i++)
				{
					itemNames.push(ItemType.getName(awardItems[i]));
					itemColors.push(ItemType.getItemColor(awardItems[i]));
				}
				
				if (sectionIds.indexOf(item[0]) > -1)
				{
					var temp : Object = {
						award : itemNames,
						color : itemColors,
						coin : getMissionAwardCoins(missionId),
						id : missionId,
						lock : getMissionLock(missionId),
						mainName : getMissionName(missionId).replace(/\(.+?\)/, \"\"),
						minTownNum : 0,
						missionNum : numberToChs(getMissionOrder(missionId)),
						name : getLocalMissionName(missionId),
						power : getMissionPower(missionId),
						skill : getMissionAwardSkill(missionId),
						townMissionNum : \"\",
						townName : TownType.getName(townId)
					};
					
					arr.push(temp);
				}
			}
			
			arr.sortOn(\"lock\", Array.NUMERIC);
			
			len = arr.length;
			for (i = 0; i < len; i++)
			{
				arr[i][\"minTownNum\"] = i + 1;
				arr[i][\"townMissionNum\"] = numberToChs(i + 1);
			}
			
			_missionList[townId][type] = arr;
			
			return arr;
		}
		
		private static function numberToChs (num : int) : String
		{
			return num + \"\";
			
			
			
			var str : String = num + \"\";
			var len : int = str.length;
			
			if (num == 10)
			{
				return Indexes[10];
			}
			
			if (num > 10 && num < 20)
			{
				return Indexes[10] + \"\" + Indexes[num % 10];
			}
			
			if (num == 20 
				|| num == 30 || num == 40
				|| num == 50 || num == 60
				|| num == 70 || num == 80
				|| num == 90) {
				return Indexes[num / 10] + \"\" + Indexes[10];
			}
			
			var temp : Array = [];
			
			for (var i : int = len - 1; i > -1; i--)
			{
				num = parseInt(str.substr(i, 1));
				temp.unshift(Indexes[num]);
			}
			
			return temp.join(\"十\");
		}
		
		/**
		 * 副本是否开启
		 * @param sectionSign 副本标识
		 * @param index 副本顺序
		 */
		/*
		public function isOpended (sectionSign : String, index : int) : Boolean
		{
			var sectionId : int = 0;
			for (var id : String in Sections)
			{
				if (Sections[id][0] == sectionSign)
				{
					sectionId = id as int;
					break;
				}
			}
			
			var list : Array = [];
			for (var id : String in Missions)
			{
				if (Missions[id][0] == sectionId)
				{
					list.push({id : id, lock : Missions[id][1]});
				}
			}
			list.sortOn(\"lock\", Array.NUMERIC);
			
			var missionId : int = list[index - 1] ? list[index - 1][0] : 0;
			if (missionId == 0) return false;
			
			var townId : int = getTownIdBySectionId(sectionId);
			var townSign : String = TownType.getSign(townId);
			if (TownType.isOpened(townSign) == false) return false;
			
			
		}
		*/
		
		/**
		 * 已开启的副本的id列表
		 */
		/*
		public static function getOpenedMissionIds () : Array
		{
			var list : Array = [];
			for (var id : Object in Missions)
			{
				if (Missions[id][1] <= _lock)
				{
					list.push(id as int);
				}
			}
			
			return list;
		}
		*/
		
		//----------------------------------------------------------------------
		//
		//  传送点
		//
		//----------------------------------------------------------------------
		
		/**
		 * 入口传送点
		 */
		public static function get startTeleport () : Point
		{
			return new Point(150, 450);
		}
		
		/**
		 * 出口传送点
		 */
		public static function get endTeleport () : Point
		{
			return new Point(2250, 450);
		}
		
		// 怪站位迁移至服务端保存
		// 副本场景内的传送点击怪站位坐标
		/*private static var _coordList : Array = [
			// 入口传送点
			new Point(150, 450),
			// 第一只怪
			new Point(750, 450),
			// 第二只怪
			new Point(1350, 450),
			// 第三只怪
			new Point(1950, 450),
			// 出口传送点
			new Point(2250, 450)
		];
		
		/**
		 * 返回怪物坐标
		 * 
		 * @param index int
		 */
		/*
		public static function coord (index : int) : Point
		{
			return _coordList[index];
		}
		*/
		
		include \"./source/MissionTypeData0.as\";
	}
}
";

file_put_contents($desc_dir."MissionType.as", addons().$str);
file_put_contents($desc_dir."source/MissionTypeData.as", addons()."package com.assist.server.source
{
	public class MissionTypeData
	{
		// mission_section_id : [sign, lock, town_id, name]
		public static const Sections : Object = {
".$hash1."
		};
		
		//	mission_id : {mission_section_id, lock, require_power, award_coins, award_skill, award_experience, monster_id, name, type, is_boss}
		public static const Missions : Object = {
".$hash2."
		};
		
		// 暂无用
		public static const Scenes : Object = {};
		
		// mission_id : [item_id1, item_id2, ..., item_idN]
		public static const MissionItems : Object = {
".$hash3."
		};
		
		// mission_id : team_num
		public static const MissionTeamCount : Object = {
".$hash4."
		};
		
		/*
		public static function init () : void
		{
			MissionType.Sections = Sections;
			MissionType.Missions = Missions;
			MissionType.MissionItems = MissionItems;
		}
		*/
	}
}
");

function get_mission_video ($video_id) {
	global $dbh;
	
	if (! $video_id) return array("", 0);
	
	$list = $dbh->query("select `file_name`, `lock` from `mission_video` where `id` = ".$video_id);
	
	if (count($list) > 0) {
		return array($list[0]["file_name"], intval($list[0]["lock"]));
	}
	
	return array("", 0);
}

echo "[data] mission_type  [Done]\n";

?>