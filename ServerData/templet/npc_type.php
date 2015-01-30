<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### npc_function

$list = $dbh->query("select `id`, `sign`, `name` from `npc_function`");

$npc_function = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($npc_function != "") {
		$npc_function .= "\n";
	}
	
	$npc_function .= "		public static const ".repeat($item["sign"], 13, " ")." : int = ".$item["id"]."; // ".$item["name"];
}

### role

$list = $dbh->query("select `id`, sign, name, dialog, shop_name, npc_func_id from `npc`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["sign"].", \"".$item["name"]."\", \"".$item["dialog"]."\", \"".$item["shop_name"]."\", ".$item["npc_func_id"]."]";
	
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}


### 类

$str = "package com.assist.server
{
	import com.assist.Helper;
	
	public class NPCType
	{
".$npc_function."
		
		// id : [sign, name, dialog, shop_name, npc_func_id]
		private static const List : Object = {
".$hash."
		};
		
".$constant."
		
		//----------------------------------------------------------------------
		//
		//  npc id，npc名称
		//
		//----------------------------------------------------------------------
		
		/**
		 * 获取npc id
		 * @param sign String
		 */
		public static function getId (sign : String) : int
		{
			var npcId : int = 0;
			
			for (var id : Object in List)
			{
				if (List[id][0] == sign)
				{
					npcId = id as int;
					break;
				}
			}
			
			return npcId;
		}
		
		/**
		 * 获取npc名称
		 *
		 * @param id int
		 * npcId
		 */
		public static function getName (id : int) : String
		{
			return List[id] ? List[id][1] : \"\";
		}
		
		/**
		 * 获取npc名称
		 *
		 * @param sign String
		 * npc标识
		 */
		public static function getNameBySign (sign : String) : String
		{
			var arr : Array = getListBySign(sign);
			
			return arr[1] || \"\";
		}
		
		/**
		 * 获取npc对话内容
		 *
		 * @param id int
		 * npcId
		 */
		private static function getDialog (id : int) : String
		{
			return List[id] ? List[id][2] : \"\";
		}
		
		/**
		 * 获取npc对话内容
		 *
		 * @param sign String
		 * npc标识
		 */
		public static function getDialogBySign (sign : String) : String
		{
			var arr : Array = getListBySign(sign);
			
			return arr[2] || \"\";
		}
		
		/**
		 * 获取npc功能名称
		 *
		 * @param id int
		 * npcId
		 */
		private static function getFeature (id : int) : String
		{
			return List[id] ? List[id][3] : \"\";
		}
		
		/**
		 * 获取npc功能名称
		 *
		 * @param sign String
		 * npc标识
		 */
		public static function getFeatureBySign (sign : String) : String
		{
			var arr : Array = getListBySign(sign);
			
			return arr[3] || \"\";
		}
		
		/**
		 * 获取npc功能id
		 *
		 * @param id int
		 * npcId
		 */
		public static function getFunction (id : int) : int
		{
			return List[id] ? List[id][4] : 0;
		}
		
		/**
		 * 获取npc功能id
		 *
		 * @param sign String
		 * npc标识
		 */
		public static function getFunctionBySign (sign : String) : int
		{
			var arr : Array = getListBySign(sign);
			
			return arr[4] || 0;
		}
		
		private static function getListBySign (sign : String) : Array
		{
			for each (var arr : Object in List)
			{
				if (arr[0] == sign)
				{
					return arr as Array;
				}
			}
			
			return [];
		}
	}
}
";

file_put_contents($desc_dir."NPCType.as", addons().$str);

echo "[data] npc_type  [Done]\n";
?>