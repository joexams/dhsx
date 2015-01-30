<?php
date_default_timezone_set("Asia/Shanghai");

$path = dirname(__FILE__).'/';
$list = file_get_contents($path.'collection.ini');
$list = preg_split('/\r\n|\r|\n/', $list);

$client_path = dirname(dirname($path)).'/';
$client_assets = $client_path.'client/assets/';

$version_date = strtotime(preg_replace('/\r\n|\r|\n/', "", file_get_contents($path."version-date.ini")));

$version = "";
$version_list = array();

recursive(glob($client_assets."*"), "");

foreach ($version_list as $sign => $time) {
	if ($version != "") $version .= "\n";
	$version .= $sign."|".$time;
}

file_put_contents($client_path."client/assets.txt", $version);
#print $version;

print "End.\n";

# 循环拷贝
function recursive ($urls, $prev) {
	global $client_path, $client_assets, $version, $version_date, $version_list;
	
	#var_export($urls);
	
	foreach ($urls as $url) {
		if (is_dir($url)) {
			$list = explode("/", $url);
			$first = substr($list[count($list) - 1], 0, 1);
			recursive(glob($url.'/*'), $prev."/".$first);
		}
		else {
			$dest = $client_assets.str_replace($client_path, '', $url);
			
			# 文件最后修改时间
			$time = filemtime($url);
			
			if ($time > $version_date) {
				
				$sign = $prev."/".basename($url);
				$time = date("YmdHi", $time);
				
				if (array_key_exists($sign, $version_list)) {
					if ($time > $version_list[$sign]) {
						$version_list[$sign] = $time;
					}
				}
				else {
					$version_list[$sign] = $time;
				}
			}
		}
	}
}
?>