<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$list = $dbh->query("select `id`, `name`, `sign` from `game_function`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\"]";
	
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\" // ".$item["name"];
}

$str = "package com.assist.server
{
	public class FunctionType
	{
		private static var _List : Object = null;
		
		public static function get List () : Object
		{
			if (_List == null) throw new Error(\"还未赋值！\");
			
			return _List;
		}
		
		public static function set List (value : Object) : void
		{
			if (_List != null) throw new Error(\"非法赋值\");
			
			_List = value;
		}
		
".$constant."

		public static const Body : String = \"Body\";
		public static const Pack : String = \"Pack\";
		
		public static const DefaultList : Array = [
			Body,
			Pack,
			OnlineShop
		];
		
		// 功能按钮列表
		public static const FullList : Array = [
			/*[0]*/ Body,
			/*[1]*/ Pack,
			/*[2]*/ Upgrade,
			/*[4]*/ Deploy,
			/*[5]*/ Research,
			/*[6]*/ Fate,
			/*[7]*/ SealSoul,
			/*[8]*/ LongZhu,
			/*[9]*/ Library,
			/*[10]*/ ShenBing,
			/*[11]*/ Friend,
			/*[12]*/ Faction,
			/*[13]*/ OnlineShop
		];
		
		/**
		 * 功能所在索引
		 * 
		 * @param sign String 
		 * 功能标识
		 */
		public static function functionIndex (sign : String) : int
		{
			var index : int = -1;
			var len : int = FullList.length;
			for (var i : int = 0; i < len; i++)
			{
				if (FullList[i] == sign)
				{
					index = i;
					break;
				}
			}
			
			return index;
		}
		
		public static function sign (id : int) : String
		{
			if (List[id] == null)
			{
				trace(\"找不到id:\" + id + \"的功能！\");
				return \"\";
			}
			
			return List[id][0];
		}
		
		//----------------------------------------------------------------------
		//
		//  开启的功能
		//
		//----------------------------------------------------------------------
		
		private static var _openedList : Object = {};
		private static var _playedList : Object = {};
		
		/**
		 * 指定的功能是否已经开启
		 *
		 * @param sign String
		 */
		public static function isOpened (sign : String) : Boolean
		{
			return _openedList[sign] != null;
		}
		
		/**
		 * 指定的功能是否已经播放
		 * 
		 * @param sign String
		 */
		public static function isPlayed (sign : String) : Boolean
		{
			return _playedList[sign] != null;
		}
		
		/**
		 * 是否忽略功能提示
		 *
		 * @param sign String
		 */
		public static function isIgnoreTip (sign : String) : Boolean
		{
			return (
				Partners == sign
				|| OnlineGift == sign
				|| ExtraPower == sign
				|| CampSalary == sign
				|| World == sign
				//|| Tower == sign
			);
		}
		
		/**
		 * 更新开启的功能列表
		 *
		 * @param list Array
		 */
		public static function updateOpenedList (list : Array) : void
		{
			for (var index : Object in list)
			{
				var id : int = list[index][\"id\"];
				
				if (List[id] == null)
				{
					continue;
				}
				
				var sign : String = List[id][0];
				
				_openedList[sign] = id;
				
				if (list[index][\"isPlayed\"] == 1)
				{
					_playedList[sign] = id;
				}
			}
		}
	}
}
";

file_put_contents($desc_dir."FunctionType.as", addons().$str);
file_put_contents($desc_dir."source/FunctionTypeData.as", addons()."package com.assist.server.source
{
	public class FunctionTypeData
	{
		public static const List : Object = {
".$hash."
		};
	}
}
");

echo "[data] game_function  [Done]\n";
?>