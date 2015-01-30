<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$all_monster = array();
$has_resource = array();

#print check_monster();

function check_monster () {
	global $dbh, $all_monster, $has_resource;
	
	# 有资源
	#$has_resource = array();
	
	# 无资源
	$no_resource = array();
	
	$list = $dbh->query("
		select
			`m1`.`id` as `id1`, `m1`.`sign` as `sign1`, `m1`.`name` as `name1`, `m1`.`resource_monster_id` as `resource_monster_id1`,
			`m2`.`id` as `id2`, `m2`.`sign` as `sign2`, `m2`.`name` as `name2`
		from
			`monster` `m1`
		left join `monster` `m2`
			on `m2`.`id` = `m2`.`resource_monster_id`
	");
	$len = count($list);
	
	$all_monster = array();
	$same_sign = array();
	
	for ($i = 0; $i < $len; $i++) {
		$item = $list[$i];
		$id1 = $item["id1"];
		
		$temp = array(
			"id1" => $item["id1"],
			"sign1" => $item["sign1"],
			"name1" => $item["name1"],
			"resource_monster_id1" => $item["resource_monster_id1"],
			"id2" => $item["id2"],
			"sign2" => $item["sign2"],
			"name2" => $item["name2"],
		);
		$all_monster[$id1] = $item;
		
		if (array_key_exists($item["sign1"], $same_sign) == false)
		{
			$same_sign[$item["sign1"]] = array();
		}
		array_push($same_sign[$item["sign1"]], $id1);
		
		if ($item["resource_monster_id1"] > 0) {
			array_push($no_resource, $id1);
		}
		else {
			array_push($has_resource, $id1);
		}
	}
	
	$content = "";
	
	#var_export($no_resource);
	#var_export($same_sign);
	
	### 使用相同标识的怪物
	
	$content .= "使用了相同标识的怪物：\n";
	$content .= "\t| id        | sign                     | resource_monster_id  | name\n";
	$content .= "\t---------------------------------------------------------------\n";
	foreach ($same_sign as $sign => $list) {
		$len = count($list);
		if ($len > 1) {
			$content .= "[".$sign."]\n";
			
			for ($i = 0; $i < $len; $i++) {
				$item = $all_monster[$list[$i]];
				$content .= (
					"\t| "
					.repeat($item["id1"], 10, " ")
					."| "
					.repeat($item["sign1"], 25, " ")
					."| "
					.repeat($item["resource_monster_id1"], 21, " ")
					."| "
					.$item["name1"]
					."\n"
				);
			}
		}
	}
	
	$content .= "\n".str_repeat("=", 80)."\n";
	
	### 使用相同资源的怪物
	
	$content .= "使用其他怪物的资源：\n";
	$content .= "\t| id        | sign                     | resource_monster_id  | name\n";
	$content .= "\t---------------------------------------------------------------\n";
	
	$len = count($no_resource);
	for ($i = 0; $i < $len; $i++) {
		$item = $all_monster[$no_resource[$i]];
		
		$content .= "[".$all_monster[$item["resource_monster_id1"]]["sign1"]."]\n";
		
		$content .= (
			"\t| "
			.repeat($item["id1"], 10, " ")
			."| "
			.repeat($item["sign1"], 25, " ")
			."| "
			.repeat($item["resource_monster_id1"], 21, " ")
			."| "
			.$item["name1"]
			."\n"
		);
	}
	
	return $content;
}

function all_monster () {
	global $all_monster, $has_resource;
	
	$content = "";
	$have = array();
	
	foreach ($has_resource as $value) {
		$item = $all_monster[$value];
		if (array_key_exists($item["sign1"], $have))
		{
			continue;
		}
		$have[$value["sign1"]] = 1;
		
		$content .= "<span type=\"sign\"><!--".repeat($item["id1"], 4)." -->".repeat($item["sign1"], 20)." : ".trim($item["name1"]).",</span>\n";
	}
	
	return $content;
}
?>