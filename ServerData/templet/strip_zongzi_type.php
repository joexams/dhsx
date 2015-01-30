<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### 

$list1 = $dbh->query("
SELECT
   `id`,
   `name`
FROM bo_zong_zi
");

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "		[".$item["id"].",".'"'.$item["name"].'"'."]";
}

### 类

$str = "package com.assist.server
{
	public class StripZongziType
	{
		//id，name
		public static var List : Array = [
			".$hash1."
		];

		/**
		*根据id获取名称
		*/
		public static function getName(id : int) : String
		{
			for each(var list : Array in List)
			{
				if(list[0] == id)
					return list[1];
			}
			return \"\"
		}
	}
}
";

file_put_contents($desc_dir."StripZongziType.as", addons().$str);

echo("[data] StripZongzi DONE\n");
?>