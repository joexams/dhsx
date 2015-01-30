<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### research

$list = $dbh->query("select `id`, `name`, `research_type_id`, `player_level`, `content` from `research`");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["name"]."\", ".$item["research_type_id"].", ".$item["player_level"].", \"".$item["content"]."\"]";
}

### 类

$str = "package com.assist.server
{
	public class ResearchType
	{
		// research_id : [name, sign, research_type_id, player_level, content]
		private static var List : Object = {
".$hash."
		};
		
		/**
		 * 全部
		 */ 
		public static const All : int = 0;
		
		/**
		 * 功法
		 */ 
		public static const Magic : int = 1;
		
		/**
		 * 阵法
		 */ 
		public static const Deploy : int = 2;

		/**
		* 仙法
		*/
		public static const Immortal : int = 3;
		
		/**
		 * 获取奇术名称
		 * 
		 * @param id int
		 */
		public static function getName (id : int) : String
		{
			return List[id] ? List[id][0] : \"\";
		}
		
		/**
		 * 获取奇术名称
		 * 
		 * @param id int
		 */
		public static function getContent (id : int) : String
		{
			return List[id] ? List[id][3] : \"\";
		}
	}
}
";

file_put_contents($desc_dir."ResearchType.as", addons().$str);

'
file_put_contents($desc_dir."source/ResearchTypeData.as", addons()."package com.assist.server.source
{
	public class ResearchTypeData
	{
	}
}
");
';

echo "[data] research_type [Done]\n";
?>