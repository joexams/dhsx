<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### online_shop

$list = $dbh->query("
	select
		`id`, `sign`, `name`
	from
		`world_war_type`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "	    public static const ".$item["sign"]." : int = ".$item["id"].";//".$item["name"]."";
}

### 类

$str = "package com.assist.server
{
	public class WorldWarType
	{
".$hash."
	}
}
";

file_put_contents($desc_dir."WorldWarType.as", addons().$str);

echo "[data]  world_war_type [Done].\n";
?>