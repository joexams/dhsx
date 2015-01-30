<?php
define("IP", "192.168.1.120");
define("PORT", 3306);
define("DB", "gamedb_tengyun");
define("USER", "root");
define("PWD", "ybybyb");

require_once($path."dbi.php");

$client_dir = dirname(dirname(dirname(__FILE__)))."/client/";
$desc_dir = $client_dir."com/assist/server/";

function repeat ($str, $len, $chr = " ") {
	return $str;
}

function args () {
	return isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
}

function addons () {
	return "/**
 * 此类由脚本生成，不要手动修改
 * 此类由脚本生成，不要手动修改
 * 此类由脚本生成，不要手动修改
 */\n";
}
?>