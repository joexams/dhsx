<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

file_put_contents(dirname(__FILE__)."/roles.php", "<"."?php
\$roles = ".str_replace("\n", "\r\n", iconv("UTF-8", "gbk", var_export(role_signs(), true))).";
?".">
");

function role_signs () {
	global $dbh;
	
	$list = $dbh->query("select `id`, `sign`, `name` from `role`;");
	
	$signs = array();
	
	$len = count($list);
	for ($i = 0; $i < $len; $i++) {
		$item = $list[$i];
		$signs[trim($item["name"])] = $item["sign"];
	}
	
	return $signs;
}
?>