<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$list1 = var_export($dbh->query("select `id`, `sign`, `name` from `role_stunt`;"), true);

$current = dirname(__FILE__)."/";

file_put_contents($current."temp.php", '<'.'?php
require_once(dirname(__FILE__)."/pinyin/pinyin_table.php");

$temp = dirname(__FILE__)."/temp/";
if (!is_dir($temp)) {
	mkdir($temp);
}

$dir = $temp."/stunt/";
if (is_dir($dir) == false) mkdir($dir);
$list1 = '.str_replace("\n", "\r\n", $list1).';
product($dir, $list1);

function product (&$dir, &$list) {
	global $dbh;
	
	$len = count($list);
	for ($i = 0; $i < $len; $i++) {
		$item = $list[$i];
		
		$chars = get_pinyin_array(trim($item["name"]));
		
		if (count($chars) > 0) {
			$folder = $dir."/".$chars[0][0]." - ".trim($item["name"]);
			if (is_dir($folder) == false) {
				mkdir($folder);
			}
		}
	}
}
?>');

`c:/perl/bin/perl.exe ${current}transform.pl`;
`c:/php/php.exe ${current}temp.php`;

if (file_exists($current."temp.php")) {
	unlink($current."temp.php");
}

print "End.\n";
?>