<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}
$list = $dbh->query("
	SELECT e.id AS id ,e.sign AS sign ,e.name AS name ,e.head_story_id AS head_story_id ,e.role_id AS role_id
	FROM  partners_invite_story_main_line e
");
$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash != "") {
		$hash .= ",\n";
	}
	$hash .= "			{id:".$item["id"]
					   ."  ,sign: '".$item["sign"]
					   ."'  ,name: '".$item["name"]
					   ."' ,head_story_id:".$item["head_story_id"]
					   ." ,role_id:".$item["role_id"]
					   ." }";
}

$list = $dbh->query("SELECT	e.id AS id ,
							e.story_video_id AS story_video_id ,
							e.type AS type ,
							e.ending_id AS ending_id ,
							e.mission_monster_scene_id AS mission_monster_scene_id ,
							e.next_story_id AS next_story_id ,
							e.piece_id AS piece_id ,
							e.award_coins AS award_coins ,
							e.award_xianling AS award_xianling ,
							e.cd_times AS cd_times ,
							e.piece_count AS piece_count
							FROM partners_invite_story e");
$hash1 = "";
$hash4 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash1  != "") {
		$hash1  .= ",\n";
	}
	if ($hash4  != "") {
		$hash4  .= ",\n";
	}
	$hash1  .= "			{ story_id:".$item["id"]
						  ."  ,story_video_id:".$item["story_video_id"]
						  ."  ,type:".$item["type"]
						  ."  ,ending_id:".$item["ending_id"]
						  ."  ,scene_id:".$item["mission_monster_scene_id"]
						  ."}";
	$hash4  .= "			{ story_id:".$item["id"]
						  ."  ,piece_id:".$item["piece_id"]
						  ."  ,piece_count:".$item["piece_count"]
						  ."}";				  
}

$list = $dbh->query("SELECT e.id AS id, 
							e.sign AS sign,
							e.name AS name
							FROM partners_invite_story_type e");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item1 = $list[$i];
		if($hash2 != "")
		{
			$hash2 .= ",\n";
		}

		$hash2  .= "			{type_id:".$item1["id"]
								." ,sign:'".$item1["sign"]
								."' ,name: '".$item1["name"]
								."'}";
}

$list = $dbh->query("
	SELECT e.id AS id ,e.sign AS sign ,e.name AS name
	FROM  partners_invite_questions_ending e
");
$hash3 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	$hash3 .= "			{ending_id:".$item["id"]."  ,sign: '".$item["sign"]."' ,name: '".$item["name"]."'}";
}

$list = $dbh->query("
	SELECT e.id AS id ,e.story_id AS story_id ,e.choice AS choice
	FROM  partners_invite_choice e
");
$hash5 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash5 != "") {
		$hash5 .= ",\n";
	}
	$hash5 .= "			{id:".$item["id"]."  ,story_id:".$item["story_id"]."  ,choice: '".$item["choice"]."'}";
}

$list = $dbh->query("
	SELECT e.id AS id ,e.file_name AS file_name ,e.name AS name
	FROM  mission_video e
");
$hash6 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash6 != "") {
		$hash6 .= ",\n";
	}
	$hash6 .= "			{id:".$item["id"]."  ,file_name: '".$item["file_name"]."' ,name: '".$item["name"]."'}";
}

file_put_contents($desc_dir."source/TriplePartnersTypeData.as", addons()."package com.assist.server.source
{
	public class TriplePartnersTypeData
	{
		// [主线]
		public static const MainLineData : Array = [
".$hash."
		];
		
		//[剧情]
		public static const StoryData : Array = [
".$hash1."
		];
		
		//[剧情后接类型]
		public static const StoryTypeData : Array = [
".$hash2."
		];		
		
		//[答题结局]
		public static const QuestionEndingData : Array = [
".$hash3."	
		];
		
		//[喜好个数]
		public static const StoryPieceData : Array = [
".$hash4."		
		];
		
		//[剧情选择]
		public static const ChoiceData : Array = [
".$hash5."
		];
		
		//[剧情文件]
		public static const XMLData:Array = [
".$hash6."
		];
	}
}
");
$str = 'package com.assist.server
{
	import com.assist.server.source.TriplePartnersTypeData;
	public class TriplePartnersType
	{
		
		/**
		*获取主线ID
		* */
		public static function getMainLineId(intRoleId : int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.MainLineData)
			{
				if(intRoleId == obj["role_id"])
				{	
					return obj["id"]
				}
			}
			return 0;
		}
		/**
		*获取剧情后接类型
		* */
		public static function getStoryTypeId(intStoryId : int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryData)
			{
				if(intStoryId == obj["story_id"])
				{	
					return obj["type"]
				}
			}
			return 0;
		}
		
		/**
		 *获取剧情播放Id 
		 */
		public static function getVideoId(intStoryId : int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryData)
			{
				if(intStoryId == obj["story_id"])
				{	
					return obj["story_video_id"]
				}
			}
			
			return 0;
		}
		
		/**
		 *获取下一级类型 
		 */
		public static function getNextType(intStoryId : int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryData)
			{
				if(intStoryId == obj["story_id"])
				{	
					return obj["type"]
				}
			}
			
			return 0;
		}
		
		/**
		 *获取下一级类型标记
		 */
		public static function getNextTypeSign(intType : int) : String
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryTypeData)
			{
				if(intType == obj["type_id"])
				{	
					return obj["sign"]
				}
			}
			
			return null;
		}
		
		/**
		*获取选择Id
		* */
		public static function getVideoData(intVideoId : int) : Object
		{
			for each(var obj:Object in TriplePartnersTypeData.XMLData)
			{
				if( intVideoId == obj["id"])
				{
					return obj;
				}
			}
			
			return null;
		}
		
		/**
		 * 获取剧情数据
		 */
		public static function getVideoXML(intVideoId : int) : String
		{
			for each(var obj:Object in TriplePartnersTypeData.XMLData)
			{
				if( intVideoId == obj["id"])
				{
					return obj["file_name"];
				}
			}
			return null;
		}
		
		/**
		 * 获取剧情名称
		 */
		public static function getVideoName(intVideoId : int) : String
		{
			for each(var obj:Object in TriplePartnersTypeData.XMLData)
			{
				if( intVideoId == obj["id"])
				{
					return obj["name"];
				}
			}
			return null;
		}
		
		/**
		 * 获取碎片标记(暂无用)
		 */
		public static function getPiecesSign() : Array
		{
			var aryPieces:Array = [];
			for each(var obj:Object in TriplePartnersTypeData.StoryPieceData)
			{
				aryPieces.push(obj["sign"]);
			}
			return aryPieces;
		}
		
		/**
		 * 获取喜好个数
		 */
		public static function getPiecesName(intPieces:int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryPieceData)
			{
				if(intPieces == obj["piece_id"])
				{
					return obj["piece_count"];
				}
				
			}
			return 0;
		}
		
		/**
		*获取选项
		* */
		public static function getChoiceData(intStoryId : int) : Array
		{
			var aryChoices:Array = [];
			for each(var obj:Object in TriplePartnersTypeData.ChoiceData)
			{
				if(intStoryId == obj["story_id"])
				{	
					aryChoices.push(obj);
				}
			}
			
			return aryChoices;
		}
		
		/**
		*获取结局对应标签
		* */
		public static function getEndSign(intEndingId : int) : String
		{
			for each(var obj:Object in TriplePartnersTypeData.QuestionEndingData)
			{
				if(intEndingId == obj["ending_id"])
				{	
					return obj["sign"];
				}
			}
			
			return null;
		}
		
		/**
		*获取角色第一个视频Id
		* */
		public static function getRoleVideoId(intRoleId : int) : int
		{
			if(intRoleId == 100)
			{
				return 567;
			}
			return 0;
		}
		
		/**
		*获取怪物场景
		* */
		public static function getSceneId(intStoryId : int) : int
		{
			for each(var obj:Object in TriplePartnersTypeData.StoryData)
			{
				if(intStoryId == obj["story_id"])
				{	
					return obj["scene_id"];
				}
			}
			
			return 0;
		}
	}
}';
file_put_contents($desc_dir."TriplePartnersType.as", addons().$str);
?>