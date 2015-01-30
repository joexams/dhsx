<?php
require_once(dirname(__FILE__)."/config.php");

$str = loop(glob($dev_path."client-resources/*"));
file_put_contents($current_path."flas.txt", "var list = [\n".$str.", ''];");

function loop ($list) {
	$str = "";
	
	for ($i = 0; $i < count($list); $i++) {
		$file = $list[$i];
		
		if (is_dir($file)) {
			$str .= loop(glob($file."/*"));
		}
		elseif (is_file($file) && preg_match("/\.fla$/", $file)) {
			$file = preg_replace("/\\\/", "/", $file);
			$file = preg_replace("/:\\//", "|/", $file);
			$str .=  "'file:///".$file."',\r\n";
		}
	}
	
	return $str;
}
?>