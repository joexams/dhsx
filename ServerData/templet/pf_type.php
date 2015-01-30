<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select
		    `id`,
		    `sign`,
		    `name`
		     from `pf`;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= "\n";
	}
	
	$hash .= "		public static const ".$item["sign"]." : int = ".$item["id"];
}

### 类

$str = "package com.assist.server
{
	public class PfType
	{
	
".$hash."
	   
	}
}
";

file_put_contents($desc_dir."PfType.as", addons().$str);

echo "[data] pf_type [Done]\n";
?>