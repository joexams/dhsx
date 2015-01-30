<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list = $dbh->query("SELECT
   `role_job_id`,
   `library_level`,
   `need_xian_ling`,
   `need_player_lavel` ,
   `need_wusheng_lib_lv` ,
   `need_jianling_lib_lv` ,
   `need_feiyu_lib_lv` ,
   `role_stunt_id`,
   `strength`,
   `agile`,
   `intellect`,
   `health`,
   `attack`,
   `defense`,
   `magic_attack`,
   `magic_defense`,
   `stunt_attack`,
   `stunt_defense`,
   `hit`,
   `block`,
   `dodge`,
   `critical`,
   `break_block`,
   `break_critical`,
   `kill`,
   `protect`
FROM library_level_war_attr");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["role_job_id"].",".$item["library_level"].",".$item["need_xian_ling"].",".$item["need_player_lavel"].",".$item["need_wusheng_lib_lv"].","
	                                .$item["need_jianling_lib_lv"].",".$item["need_feiyu_lib_lv"].",".$item["role_stunt_id"].
									",".$item["strength"].",".$item["agile"].",".$item["intellect"].",".$item["health"].
									",".$item["attack"].",".$item["defense"].",".$item["magic_attack"].",".$item["magic_defense"].
									",".$item["stunt_attack"].",".$item["stunt_defense"].",".$item["hit"].",".$item["block"].
									",".$item["dodge"].",".$item["critical"].",".$item["break_block"].",".$item["break_critical"].
									",".$item["kill"].",".$item["protect"]."]";
}

$list = $dbh->query("SELECT
  role_stunt_id,
  next_role_stunt_id
FROM library_stunt_relation");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .= "			[".$item["role_stunt_id"].",".$item["next_role_stunt_id"]."]";
}


$list = $dbh->query("SELECT * FROM passivity_level_war_attr");
$hash3= "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	$hash3 .= "			[".$item["type"].",".$item["level"].",".$item["need_xian_ling"].",".$item["need_player_level"].","
	                                .$item["need_zhanshen_lv"].",".$item["need_jingang_lv"].
									",".$item["need_xianfa_lv"].",".$item["role_stunt_id"].
									",".$item["strength"].",".$item["agile"].",".$item["intellect"].",".$item["health"].
									",".$item["attack"].",".$item["defense"].",".$item["magic_attack"].",".$item["magic_defense"].
									",".$item["stunt_attack"].",".$item["stunt_defense"].",".$item["hit"].",".$item["block"].
									",".$item["dodge"].",".$item["critical"].",".$item["break_block"].",".$item["break_critical"].
									",".$item["kill"].","."]";
}	
$str = "package com.assist.server.source
{
	public class LibraryTypeData
	{
//		role_job_id,library_level,need_xian_ling,need_player_lave l,need_wusheng_lib_lv,need_jianling_lib_lv,need_feiyu_lib_lv,role_stunt_id,strength,agile,intellect,health,attack,defense,magic_attack,magic_defense,stunt_attack,stunt_defense,hit,block,dodge ,critical,break_block,break_critical,kill,protect
		public static const Library_Level_War_Arr : Array = [
		".$hash."
		];
//      role_stunt_id,next_role_stunt_id
		public static const Library_Stunt_Relation : Array = [
		".$hash2."
		];
		
		//		supernatural_job_id,library_level,need_xian_ling,need_wusheng_lib_lv,need_jianling_lib_lv,need_feiyu_lib_lv,need_zhanshen_lib_lv,need_jingang_lib_lv,need_xianfa_lib_lv,role_stunt_id,strength,agile,intellect,health,attack,defense,magic_attack,magic_defense,stunt_attack,stunt_defense,hit,block,dodge ,critical,break_block,break_critical,kill,protect
		public static const Super_Nature_Library_Level_War_Arr : Array = [
		".$hash3."
		];
	}
}
";


file_put_contents($desc_dir."source/LibraryTypeData.as", addons().$str);

print repeat("[data] library_type_data", 75, ".")."DONE.\n";

$str = 'package com.assist.server
{
	import com.assist.server.source.LibraryTypeData;

	public class LibraryType
	{
		private static var _libraryDataCache1 : Object = {};
		private static var _libraryDataCache2:Object = {};
		private static var _libraryMaxLevelData : Object = {};
		
		private static var _superNatureLibMaxLevelData : Object = {};
		private static var _superNatureLibCache1 : Object = {};
		private static var _superNatureLibCache2 : Object ={};
		
		public static var maxLevelAllow : int = 180;
		
		public static function getLibraryMaxLevel(jobId : int) : int
		{
			if(_libraryMaxLevelData[jobId] != null )
			{
				return _libraryMaxLevelData[jobId];
			}
			var cout : int = 0;
			var arr:Array = LibraryTypeData.Library_Level_War_Arr;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[0] == jobId)
				{
					if(tempItem[1] > cout && tempItem[1]<= maxLevelAllow)
					{
						cout = tempItem[1];
					}
				}
			}
			_libraryMaxLevelData[jobId] = cout;
			return cout;
		}
		
		/**
		 * 获取对应角色的藏经阁数据
		 * 返回数据为技能二维数组
		 * */
		
		public static function getLibraryData2(jobId : int):Array
		{
			
			if(_libraryDataCache2[jobId])
			{
				return _libraryDataCache2[jobId] as Array;
			}
			var cout : Array = new Array();
			var arr:Array = LibraryTypeData.Library_Level_War_Arr;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[7]<=0)
				{
					continue;
				}
				if(tempItem[0] == jobId)
				{
					var obj : Object ={
						role_job_id:tempItem[0],
						library_level:tempItem[1],
						need_xian_ling :tempItem[2],
						need_player_lavel : tempItem[3],
						need_wusheng_lib_lv:tempItem[4],
						need_jianling_lib_lv:tempItem[5],
						need_feiyu_lib_lv:tempItem[6],
						role_stunt_id : tempItem[7],
						strength : tempItem[8],
						agile : tempItem[9],
						intellect : tempItem[10],
						health : tempItem[11],
						attack : tempItem[12],
						defense : tempItem[13],
						magic_attack : tempItem[14],
						magic_defense : tempItem[15],
						stunt_attack : tempItem[16],
						stunt_defense : tempItem[17],
						hit : tempItem[18],
						block : tempItem[19],
						dodge : tempItem[20],
						critical : tempItem[21],
						break_block : tempItem[22],
						break_critical : tempItem[23],
						kill : tempItem[24],
						protect : tempItem[25]
						
					};
					var isAdd : Boolean = false;
					for each(var tempArr: Array in cout )
					{
						var targetObj : Object = tempArr[0];
						if(isSameJobStunt(targetObj.role_stunt_id,obj.role_stunt_id))
						{
							tempArr.push(obj);
							isAdd = true;
							break;
						}
					}
					if(isAdd == false)
					{
						cout.push([obj]);
					}
				}
			}
			_libraryDataCache2[jobId] = cout;
			return cout;
		}
		public static var JingMaiPerLength : int = 90;
		public static var JingMaiPerLength2 : int = 80;
		/**
		 *  
		 * @param jobId
		 * @param level >0
		 * @return 
		 * 
		 */		
		public static function getLibraryDataByReasearchLv(jobId : int, level : int) : Array
		{
			if(level < 1)
			{
				level =1;
			}
			if(level > getLibraryMaxLevel(jobId))
			{
				level = getLibraryMaxLevel(jobId);
			}
			var key : String = jobId.toString() + "_" + level.toString();
			if(_libraryDataCache1[key])
			{
				return _libraryDataCache1[key] as Array;
			}
			var cout : Array = new Array();
			var arr:Array = LibraryTypeData.Library_Level_War_Arr;
			var startIndex : int = Math.floor((level -1)/JingMaiPerLength)* JingMaiPerLength + 1;
			var endindex : int = startIndex + JingMaiPerLength;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[0] == jobId)
				{
					if(tempItem[1] < startIndex)
					{
						continue;
					}
					if(tempItem[1] >= endindex)
					{
						continue;
					}
					var obj : Object ={
						role_job_id:tempItem[0],
						library_level:tempItem[1],
						need_xian_ling :tempItem[2],
						need_player_lavel : tempItem[3],
						need_wusheng_lib_lv:tempItem[4],
						need_jianling_lib_lv:tempItem[5],
						need_feiyu_lib_lv:tempItem[6],
						role_stunt_id : tempItem[7],
						strength : tempItem[8],
						agile : tempItem[9],
						intellect : tempItem[10],
						health : tempItem[11],
						attack : tempItem[12],
						defense : tempItem[13],
						magic_attack : tempItem[14],
						magic_defense : tempItem[15],
						stunt_attack : tempItem[16],
						stunt_defense : tempItem[17],
						hit : tempItem[18],
						block : tempItem[19],
						dodge : tempItem[20],
						critical : tempItem[21],
						break_block : tempItem[22],
						break_critical : tempItem[23],
						kill : tempItem[24],
						protect : tempItem[25]
						
					};
					cout.push(obj);
				}
			}
			_libraryDataCache1[key] = cout;
			return cout;
		}
		/**
		 * 根据技能id获取藏经阁限制信息 
		 * @param stuntId
		 * @return 二维数组
		 * 
		 */		
		public static function getLibraryDataByStuntId(stuntId : int, jobId : int) : Object
		{
			for each(var tempArr : Array in _libraryDataCache2[jobId])
			{
				for each(var tempObj: Object in  tempArr)
				{
					if(tempObj.role_stunt_id == stuntId)
					{
						return tempObj;
					}
				}
			}
			return null;
		}
		
		public static function getJobIdFromStuntId(stuntId : int) : int
		{
			for each(var tempObj : Array in LibraryTypeData.Library_Level_War_Arr)
			{
				if(tempObj[7]== stuntId)
				{
					return tempObj[0];
				}
			}
			return 0;
		}
		
		private static var _jobStuntRelationsCache : Array;
		/**
		 * 初始化技能联系缓存 
		 * 
		 */		
		private static function initJobStuntRelations() : void		
		{
			_jobStuntRelationsCache = new Array();
			for each(var tempArr : Array in  LibraryTypeData.Library_Stunt_Relation)
			{
				addStuntRelation(tempArr[0],tempArr[1]);
			}
		}
		/**
		 * 添加一个关系到缓存
		 * 
		 */		
		private static function addStuntRelation(stuntId :int,nextStuntId : int) : void
		{
			for each(var tempArr : Array in _jobStuntRelationsCache)
			{
				if(tempArr.indexOf(stuntId) != -1)
				{
					if(tempArr.indexOf(nextStuntId ) != -1)
					{
						return;
					}
					tempArr.push(nextStuntId);
					return;
				}
			}
			_jobStuntRelationsCache.push([stuntId,nextStuntId]);
		}
		
		/**
		 *  判断两个技能是不是同一个技能
		 * 
		 */		
		public static function isSameJobStunt(stuntId1 : int ,stuntId2 : int) : Boolean
		{
			if(stuntId1 == stuntId2)
			{
				return true;
			}
			if(_jobStuntRelationsCache == null )
			{
				initJobStuntRelations();
			}
			for each(var tempArr : Array in _jobStuntRelationsCache)
			{
				if(tempArr.indexOf(stuntId1) != -1)
				{
					if(tempArr.indexOf(stuntId2 ) != -1)
					{
						return true;
					}
					return false;
				}
			}
			return false;
		}
		
				/**
		 * 根据当前技能id，获取当前职业联系信息 
		 * @param stuntId
		 * @return 
		 * 
		 */		
		public static function getJobStuntRelationObj (stuntId : int, researchingLv : int) : Object
		{
			if(_jobStuntRelationsCache == null )
			{
				initJobStuntRelations();
			}
			var index : int = 0;
			var obj : Object= null;
			for each(var tempArr : Array in _jobStuntRelationsCache )
			{
				index = 0;
				for(;index < tempArr.length ; index ++ )
				{
					var tempInt : int  = tempArr[index];
					if(tempInt == stuntId)
					{
						obj = {
							role_stunt_id : tempInt,
							index : index +1,
							max : tempArr.length
						};
						if(researchingLv >= JingMaiPerLength + 1)
						{
							obj.next_role_stunt_id = tempArr[index + 1];
						}
						break;
					}
				}
				if(obj  != null )
				{
					break;
				}
			}
			var arr:Array = LibraryTypeData.Library_Level_War_Arr;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[7] == obj.role_stunt_id)
				{
					obj.role_job_id=tempItem[0];
					obj.library_level=tempItem[1];
					obj.need_xian_ling =tempItem[2];
					obj.need_player_lavel = tempItem[3];
					obj.need_wusheng_lib_lv=tempItem[4];
					obj.need_jianling_lib_lv=tempItem[5];
					obj.need_feiyu_lib_lv=tempItem[6];
					obj.role_stunt_id = tempItem[7];
					obj.strength = tempItem[8];
					obj.agile = tempItem[9];
					obj.intellect = tempItem[10];
					obj.health = tempItem[11];
					obj.attack = tempItem[12];
					obj.defense = tempItem[13];
					obj.magic_attack = tempItem[14];
					obj.magic_defense = tempItem[15];
					obj.stunt_attack = tempItem[16];
					obj.stunt_defense = tempItem[17];
					obj.hit = tempItem[18];
					obj.block = tempItem[19];
					obj.dodge = tempItem[20];
					obj.critical = tempItem[21];
					obj.break_block = tempItem[22];
					obj.break_critical = tempItem[23];
					obj.kill = tempItem[24];
					obj.protect = tempItem[25];
					break;
				}
			}
			return obj;
		}

		
		public static function getSuperNatureLibraryMaxLevel(jobId : int) : int
		{
			if(_superNatureLibMaxLevelData[jobId] != null )
			{
				return _superNatureLibMaxLevelData[jobId];
			}
			var cout : int = 0;
			var arr:Array = LibraryTypeData.Super_Nature_Library_Level_War_Arr;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[0] == jobId)
				{
					if(tempItem[1] > cout && tempItem[1]<= maxLevelAllow)
					{
						cout = tempItem[1];
					}
				}
			}
			_superNatureLibMaxLevelData[jobId] = cout;
			return cout;
		}
		
		/**
		 *  
		 * @param jobId
		 * @param level >0
		 * @return 
		 * 
		 */		
		public static function getSuperNatureLibraryDataByReasearchLv(jobId : int, level : int) : Array
		{
			if(level < 1)
			{
				level =1;
			}
			if(level > getSuperNatureLibraryMaxLevel(jobId))
			{
				level = getSuperNatureLibraryMaxLevel(jobId);
			}
			var key : String = jobId.toString() + "_" + level.toString();
			if(_superNatureLibCache1[key])
			{
				return _superNatureLibCache1[key] as Array;
			}
			var cout : Array = new Array();
			var arr:Array = LibraryTypeData.Super_Nature_Library_Level_War_Arr;
			var startIndex : int = Math.floor((level -1)/JingMaiPerLength2)* JingMaiPerLength2 + 1;
			var endindex : int = startIndex + JingMaiPerLength2;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[0] == jobId)
				{
					if(tempItem[1] < startIndex)
					{
						continue;
					}
					if(tempItem[1] >= endindex)
					{
						continue;
					}
					var obj : Object ={
						supernatural_job_id:tempItem[0],
						library_level:tempItem[1],
						need_xian_ling :tempItem[2],
						need_player_lavel :tempItem[3],
						need_zhanshen_lib_lv:tempItem[4],
						need_jingang_lib_lv:tempItem[5],
						need_xianfa_lib_lv:tempItem[6],
						supernatural_id : tempItem[7],
						strength : tempItem[8],
						agile : tempItem[9],
						intellect : tempItem[10],
						health : tempItem[11],
						attack : tempItem[12],
						defense : tempItem[13],
						magic_attack : tempItem[14],
						magic_defense : tempItem[15],
						stunt_attack : tempItem[16],
						stunt_defense : tempItem[17],
						hit : tempItem[18],
						block : tempItem[19],
						dodge : tempItem[20],
						critical : tempItem[21],
						break_block : tempItem[22],
						break_critical : tempItem[23],
						kill : tempItem[24]						
					};
					cout.push(obj);
				}
			}
			_superNatureLibCache1[key] = cout;
			return cout;
		}
		
		/**
		 * 获取对应角色神通的藏经阁数据
		 * 返回数据为技能二维数组 {supernatural_job_id,library_level,need_xian_ling,need_wusheng_lib_lv,need_jianling_lib_lv,need_feiyu_lib_lv,need_zhanshen_lib_lv,need_jingang_lib_lv,need_xianfa_lib_lv,role_stunt_id,strength,agile,intellect,health,attack,defense,magic_attack,magic_defense,stunt_attack,stunt_defense,hit,block,dodge ,critical,break_block,break_critical,kill,protect}
		 * */
		
		public static function getSuperNatureLibraryData2(jobId : int):Array
		{
			
			if(_superNatureLibCache2[jobId])
			{
				return _superNatureLibCache2[jobId] as Array;
			}
			var cout : Array = new Array();
			var arr:Array = LibraryTypeData.Super_Nature_Library_Level_War_Arr;
			for(var i:int = 0; i < arr.length; i++)
			{
				var tempItem : Array = arr[i];
				if(tempItem[7]<=0)
				{
					continue;
				}
				if(tempItem[0] == jobId)
				{
					var obj : Object ={
						supernatural_job_id:tempItem[0],
						library_level:tempItem[1],
						need_xian_ling :tempItem[2],
						need_player_lavel :tempItem[3],
						need_zhanshen_lib_lv:tempItem[4],
						need_jingang_lib_lv:tempItem[5],
						need_xianfa_lib_lv:tempItem[6],
						supernatural_id : tempItem[7],
						strength : tempItem[8],
						agile : tempItem[9],
						intellect : tempItem[10],
						health : tempItem[11],
						attack : tempItem[12],
						defense : tempItem[13],
						magic_attack : tempItem[14],
						magic_defense : tempItem[15],
						stunt_attack : tempItem[16],
						stunt_defense : tempItem[17],
						hit : tempItem[18],
						block : tempItem[19],
						dodge : tempItem[20],
						critical : tempItem[21],
						break_block : tempItem[22],
						break_critical : tempItem[23],
						kill : tempItem[24]						
					};
					var isAdd : Boolean = false;
					for each(var tempArr: Array in cout )
					{
						var targetObj : Object = tempArr[0];
						if(isSameSuperNature(targetObj,obj))
						{
							tempArr.push(obj);
							isAdd = true;
							break;
						}
					}
					if(isAdd == false)
					{
						cout.push([obj]);
					}
				}
			}
			_superNatureLibCache2[jobId] = cout;
			return cout;
		}
		
		
		/**
		 *  判断两个神通是不是同一个神通
		 * 
		 */		
		public static function isSameSuperNature(data1 : Object ,data2 : Object) : Boolean
		{
			return SuperNaturalType.getTypeIdByKey(data1.supernatural_id) == SuperNaturalType.getTypeIdByKey(data2.supernatural_id);
		}
		
		/**
		 * 根据技能id获取藏经阁限制信息 
		 * @param stuntId
		 * @return 二维数组
		 * 
		 */		
		public static function getSuperNaturalDataBySuperNaturalId(superNaturalId : int, jobId : int) : Object
		{
			for each(var tempArr : Array in _superNatureLibCache2[jobId])
			{
				for each(var tempObj: Object in  tempArr)
				{
					if(tempObj.supernatural_id == superNaturalId)
					{
						return tempObj;
					}
				}
			}
			return null;
		}
	}
}

';

file_put_contents($desc_dir."LibraryType.as", addons().$str);

print repeat("[data] library_type", 75, ".")."DONE.\n";
?>
