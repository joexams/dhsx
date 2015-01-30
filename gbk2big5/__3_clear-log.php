<?php

require_once(dirname(__FILE__)."/config.php");

$list = glob($current_path."log/*");

for ($i = 0; $i < count($list); $i++) {
	$content = substr(file_get_contents($list[$i]), 3);
	
	print $content."\n";
	if (strpos("0 个错误, 0 个警告", $content) > -1 || strpos("0 Error(s), 0 Warning(s)", $content) > -1) {
		unlink($list[$i]);
	}
}

?>