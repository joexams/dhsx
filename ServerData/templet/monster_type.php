<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### monster

$list = $dbh->query("select `id`, `sign`, `name`, `talk`, `resource_monster_id` from `monster`;");

$ids = array();
$keys = array();
$the_same = array();

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if (array_key_exists($item["sign"], $keys)) {
		print "id: ".$item["id"].", 『".$item["name"]."』的标识(".$item["sign"].")重复输入。\n";
		print "	已存在的 id: ".$keys[$item["sign"]]."\n";
		continue;
	}
	
	$ids[$item["id"]] = $item["sign"];
	$keys[$item["sign"]] = $item["id"];
	
	$the_same[$item["sign"]] = $item["resource_monster_id"];
	
	if ($hash != "") {
		$hash .= ",\n";
		if (! $item["resource_monster_id"]) {
			$constant .= "\n";
		}
	}
	
	$hash .= "			".$item["sign"]." : [\"".$item["id"]."\", \"".$item["name"]."\", \"".$item["talk"]."\"]";
	
	if (! $item["resource_monster_id"]) {
		$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
	}
}

$same = "";
foreach ($the_same as $sign => $id)
{
	if ($id == 0 || $id == "0" || $id == "null" || !$id) continue;
	
	if (array_key_exists($id, $ids) == false) {
		print "	not exists ".$id."\n";
		continue;
	}
	
	if ($sign == $ids[$id]) continue;
	
	if ($same != "") {
		$same .= ",\n";
	}
	
	$same .= "			".$sign." : \"".$ids[$id]."\"";
}

### 类

$str = "package com.assist.server
{
	public class MonsterType
	{
		// sign : [id, name, talk]
		private static var _Monsters : Object = null;
		
		public static function get Monsters () : Object
		{
			if (_Monsters == null) throw new Error(\"还未赋值！\");
			
			return _Monsters;
		}
		
		public static function set Monsters (value : Object) : void
		{
			if (_Monsters != null) throw new Error(\"非法赋值\");
			
			_Monsters = value;
		}
		
".$constant."
		
		public static const BossNianShou : String = \"BossNianShou\";
		
		// 与其他怪使用相同的资源
		// sourceSign : targetSign
		private static var _Same : Object = null;
		
		public static function get Same () : Object
		{
			if (_Same == null) throw new Error(\"还未赋值！\");
			
			return _Same;
		}
		
		public static function set Same (value : Object) : void
		{
			if (_Same != null) throw new Error(\"非法赋值\");
			
			_Same = value;
		}
		
		public static function getMonsterSign (id : int) : String
		{
			var sign : String = \"\";
			
			for (var item : String in Monsters)
			{
				if (Monsters[item][0] == id)
				{
					sign = item;
					break;
				}
			}
			
			return sign;
		}
		
		public static function getMonsterName (sign : String) : String
		{
			return Monsters[sign] ? Monsters[sign][1] : \"\";
		}
		
		public static function getMonsterNameById (id : int) : String
		{
			return getMonsterName(getMonsterSign(id));
		}
		
		public static function getMonsterIdByName (name : String) : int
		{
			for (var sign : String in Monsters)
			{
				if (Monsters[sign][1] == name)
				{
					return Monsters[sign][0];
				}
			}
			
			return 0;
		}
		
		public static function words (sign : String) : String
		{
			var list : Array = Monsters[sign];
			
			if (list == null)
			{
				return \"\";
			}
			
			return list[2];
		}
		
		/**
		 * 获取sign使用想相同的资源的怪物的sign
		 *
		 * @param sign String
		 */
		public static function sameResource (sign : String) : String
		{
			return Same[sign] || sign;
		}
	}
}
";

file_put_contents($desc_dir."MonsterType.as", addons().$str);
file_put_contents($desc_dir."source/MonsterTypeData.as", addons()."package com.assist.server.source
{
	public class MonsterTypeData
	{
		// sign : [id, name, talk]
		public static const Monsters : Object = {
".$hash."
		};
		
		// 与其他怪使用相同的资源
		// sourceSign : targetSign
		public static const Same : Object = {
".$same."
		};
		
		/*
		public static function init () : void
		{
			MonsterType.Monsters = Monsters;
			MonsterType.Same = Same;
		}
		*/
	}
}
");

echo "[data] monster_type  [Done]\n";
?>