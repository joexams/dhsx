<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role_stunt

$list = $dbh->query("select `vip_level`, `money` from `vip_require`;");

$constant = "";
$hash = "";

$len = count($list);
for ($i = 0; $i < $len; $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$constant .= ",\n";
		$hash .= "\n";
	}
	
	$constant .= "			".$item["vip_level"].": ".$item["money"];
	$hash .= "		public static const Level".$item["vip_level"]." : int = ".$item["vip_level"].";";
}

$hash .= "\n		public static const MaxLevel : int = ".$list[$len - 1]["vip_level"].";";

### 类

$str = "package com.assist.server
{
	public class VIPType
	{
		// level : money
		private static const Levels : Object = {
".$constant."
		};
		
		public static const Level0 : int = 0;
".$hash."
		
		private static var _level : int = -1;
		
		public static function set level (value : int) : void
		{
			_level = value;
		}
		
		/**
		 * 检查玩家是否达到指定的等级
		 * 
		 * @param level int
		 */
		public static function check (level : int) : Boolean
		{
			if (_level >= level)
			{
				return true;
			}
			
			return false;
		}
		
		/**
		 * vip是否有效
		 */
		public static function get enabled () : Boolean
		{
			return check(Level0);
		}
		
		//----------------------------------------------------------------------
		//
		//  VIP等级信息
		//
		//----------------------------------------------------------------------
		
		public static function getVIPInfo (level : int, limit : int = 100) : String
		{
			var info : String = \"\";
			
			if (infos[level])
			{
				var len : int = infos[level].length;
				for (var i : int = 0; i < len && i < limit; i++)
				{
					if (info != \"\")
					{
						info += \"\\n\";
					}
					
					info += (i + 1) + \".\" + infos[level][i];
				}
			}
			
			return info;
		}
		
		/**
		 * 获取等级需求充值额
		 *
		 * @param level int
		 */
		public static function getRequireMoney (level : int) : int
		{
			return Levels[level] || 0;
		}
		
		include \"source/VIPTypeData1.as\";
	}
}

include \"source/VIPTypeData0.as\";
";

file_put_contents($desc_dir."VIPType.as", addons().$str);

echo "[data] vip_type [Done]\n";
?>