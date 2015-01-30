<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `sign`, `name` from `super_gift_type`;");

$hash = "";
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$hash1 .= ",\n";
	}
	
	$hash .= "			public static const Gift".$item["id"]." : String = \"GiftType".$item["id"]."\"; //".$item["name"];
	$hash1 .= "          ".$item["id"]." : "."\"".$item["name"]."\"";
}


### 类

$str = "package com.assist.server
{
	public class SuperGiftType
	{
	
		public static const GiftStr : String = \"GiftType\";
	
".$hash."

		// id : [description, vip]
		private static const Data : Object = {
".$hash1."
		};

		/**
		*获取礼包名称
		@param id : 礼包id
		*/
		public static function getGiftName(id : int) : String
		{
			return Data[id] || \"大礼包\";
		}
		
	}
}
";

file_put_contents($desc_dir."SuperGiftType.as", addons().$str);

echo "[data] super_gift_type [Done]\n";
?>