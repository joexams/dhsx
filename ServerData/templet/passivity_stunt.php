<?php

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### spirit_state

$list = $dbh->query("select * from `passivity_stunt`");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "             [".$item["id"]. ", \"" . $item["name"] ."\", \"". $item["description"] ."\", \"". $item["sign"] ."\" , ". $item["type"] ."]";
}

$str = "package com.assist.server
{
	public class PassivityStuntType
	{
		// id , name,  description, sign, type
		private static const passivityStuntList : Array =
		[
".$hash."	
		]
		
		/**
		 * 获取名称
		 *
		 * @param id int
		 */
		public static function getNameForId (id : int) : String
		{
			var len : int = passivityStuntList.length;
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = passivityStuntList[i];
				
				if (list[0] == id)
				{
					return list[1];
				}
			}
			
			return \"\";
		}
		
	        /**
		 * 获取描述
		 *
		 * @param id int
		 */
		public static function getDescriptionForId (id : int) : String
		{
			var len : int = passivityStuntList.length;
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = passivityStuntList[i];
				
				if (list[0] == id)
				{
					return list[2];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 获取标识
		 *
		 * @param id int
		 */
		public static function getSignForId (id : int) : String
		{
			var len : int = passivityStuntList.length;
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = passivityStuntList[i];
				
				if (list[0] == id)
				{
					return list[3];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 获取类型
		 *
		 * @param id int
		 */
		public static function getTypeForId (id : int) : int
		{
			var len : int = passivityStuntList.length;
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = passivityStuntList[i];
				
				if (list[0] == id)
				{
					return list[4];
				}
			}
			
			return 0;
		}
		
		/**
		 * 获取数据
		 *
		 * @param id int
		 */
		public static function getStuntDataForId (id : int) : Object
		{
			var len : int = passivityStuntList.length;
			var obj : Object = {};
			
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = passivityStuntList[i];
				
				if (list[0] == id)
				{
					obj.id = id;
					obj.name = list[1];
					obj.description = list[2];
					obj.sign = list[3];
					obj.type = list[4];
				}
			}
			
			return obj;
		}
	}
}
";

file_put_contents($desc_dir."PassivityStuntType.as", addons().$str);

echo "[data] PassivityStunt_type  [Done]\n";
?>