<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

file_put_contents(dirname(__FILE__)."/monsters.php", "<"."?php
\$monsters = ".str_replace("\n", "\r\n", iconv("UTF-8", "gbk", var_export(monster_signs(), true))).";
?".">
");

function monster_signs () {
	global $dbh;
	
	$list = $dbh->query("select `id`, `sign`, `name`, `talk`, `resource_monster_id` from `monster`;");
	
	$signs = array();
	
	$len = count($list);
	for ($i = 0; $i < $len; $i++) {
		$item = $list[$i];
		
		$name = trim($item["name"]);
		if (! array_key_exists($name, $signs)) {
			$signs[$name] = array();
		}
		
		array_push($signs[$name], $item["sign"]);
	}
	
	$temp = array();
	foreach ($signs as $name => $sign_list) {
		if (count($sign_list) == 1) {
			$temp[$name] = $sign_list[0];
		}
		else {
			for ($i = 0; $i < count($sign_list); $i++) {
				if (preg_match("/^[a-zA-Z]+$/", $sign_list[$i])) {
					$temp[$name] = $sign_list[$i];
				}
			}
		}
	}
	
	return $temp;
}
?>