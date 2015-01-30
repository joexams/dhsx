<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `need_player_level`, `need_pearl`, `level`, `ball`,
		    `name` from `refined_array`;");

$hash = "";
$hash1 = "              ";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= "\n";
	}
	
	$hash .= "			[".$item["id"].", ".$item["need_player_level"].", ".$item["need_pearl"].", ".
	                                   $item["level"].", ".$item["ball"].", \"".$item["name"]."\"],";
}

### 类

$str = "package com.assist.server
{
	public class RefinedArrayType
	{
	
	    // ID， 需要人物等级， 需要元神珠， 等级， 球（0-小球，1-打球）， 属性加成描述
	    static public const RefinedList : Array =
	    [	
".$hash."
	    ]
		
	}
}
";

file_put_contents($desc_dir."RefinedArrayType.as", addons().$str);

echo "[data] refined_array [Done]\n";
?>