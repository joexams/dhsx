<?php

$current_path = dirname(__FILE__)."/";
require_once($current_path."config.php");
include_once($current_path."inc/version-log.php");

$client_com = "https://192.168.1.110/svn/dev/tags/$repository/client/com/";
$client_assets = "https://192.168.1.110/svn/dev/tags/$repository/client/assets/";

$com_log = `svn log -v -r $client_com_version:HEAD $client_com`;
$assets_log = `svn log -v -r $client_assets_version:HEAD $client_assets`;

# 版本更新
print `svn cleanup ${dev_path}`;
print "svn cleanup\r\n";
print str_repeat("-", 80)."\r\n";
print "svn update\r\n";
print `svn update --accept theirs-full ${dev_path}`;
print str_repeat("-", 80)."\r\n";

# 简体转繁体
print "gbk2big5...\r\n";
print `php ${current_path}__1_simplified-to-traditional.php`;
print str_repeat("-", 80)."\r\n";

print "svn log\r\n\r\n";

$as_log = format_log($com_log, ".as");
$swf_log = format_log($assets_log, ".swf");

file_put_contents($current_path."__5_svn-log-as.txt", $as_log);
file_put_contents($current_path."__5_svn-log-swf.txt", $swf_log);

print "as log\r\n";
print $as_log;
print "\r\n";
print "swf log\r\n";
print $swf_log;

#`${current_path}__2_simplified-to-traditional.jsfl`;

function format_log ($log, $attr) {
	global $dev_path, $repository;
	
	$info = "";
	
	$list = preg_split("/--+/", $log);
	
	$len = count($list);
	for ($i = 0; $i < $len; $i++) {
		$item = $list[$i];
		if ($item == "") continue;
		
		$item_lines = preg_split("/\\r\\n|\\r|\\n/", $item);
		
		$info1 = $item_lines[1]."\r\n";
		$info2 = "";
		
		for ($j = 3; $j < count($item_lines); $j++) {
			if (preg_match("/^ {3}(?:A|M) /", $item_lines[$j]) && preg_match("/\\".$attr."/", $item_lines[$j])) {
				$l = explode(" ", preg_replace("/^ {3}(?:A|M) /", "", $item_lines[$j]));
				$l = $dev_path.str_replace("/tags/".$repository."/", "", $l[0]);
				$info2 .= $l."\r\n";
				
				if (preg_match("/\.as$/", $l)) {
					copy($l, str_replace("/client/", "/client-tw/", $l));
				}
			}
		}
		
		if ($info2 != "") {
			$info .= $info1.$info2."\r\n".str_repeat("-", 80)."\r\n";
		}
		
		#var_export($item_lines);return;
	}
	
	#print $log;
	#var_export($list);
	
	return $info;
}
?>