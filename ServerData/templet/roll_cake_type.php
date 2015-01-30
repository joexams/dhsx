<?php

$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$roll_cake = array();

$list = $dbh->query("select * from `roll_cake`");
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$roll_cake[$item["id"]] = array(
		$item["name"],
		$item["nick_name"],
		$item["skill"],
		$item["state_point"],
		$item["coin"],
	);
}

$roll_count = array();

$list = $dbh->query("select * from `roll_count`");
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	$roll_count[$item["number"]] = array(
		$item["text"],
		$item["picture"],
	);
}

file_put_contents($client_dir."assets/templet/roll_cake/roll_cake.txt", json_encode(array($roll_cake, $roll_count)));

echo "[data] roll_cake_type [Done]\n";
?>