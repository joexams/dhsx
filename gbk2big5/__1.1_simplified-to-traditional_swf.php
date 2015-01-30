<?php
require_once(dirname(__FILE__)."/config.php");

## 生成log文件夹
if (! is_dir($current_path."log")) {
	mkdir($current_path."log");
}

## 生成jsfl
$jsfl = $current_path."inc/simplified-to-traditional.jsfl.templet";
$jsfl_content = file_get_contents($jsfl);
$jsfl_content = str_replace("{{dirname}}", "file:///".str_replace(":", "|/", $dev_path), $jsfl_content);
$jsfl_content = str_replace("{{logpath}}", "file:///".str_replace(":", "|/", $current_path)."log/", $jsfl_content);
file_put_contents($current_path."__2_simplified-to-traditional.jsfl", $jsfl_content);

## 生成svn导出命令
file_put_contents($current_path."__4_svn-export.bat", "svn export --force ${dev_path}client ${dev_path}client-tw\r\npause");

?>