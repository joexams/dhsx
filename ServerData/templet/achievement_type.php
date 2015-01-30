<?php

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$achievement = array();

$list = $dbh->query("select * from `achievement`");
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$achievement[$item["id"]] = array(
		$item["sign"],
		$item["name"],
		$item["content"],
		$item["total"],
		$item["points"],
		$item["special_award"],
		$item["sort_order"],
		$item["tag"],
	);
}

$tag = array();

$list = $dbh->query("select * from `achievement_tag`");
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$tag[$item["id"]] = array(
		$item["type"],
		$item["name"],
		$item["parent_id"],
	);
}

file_put_contents($client_dir."assets/templet/achievement/achievement.txt", json_encode(array($achievement, $tag)));

echo "[data] achievement_type  [Done]\n";
?>