<?php

/**
 * ��ѯ�Ӱ汾$revision��������ӻ�ɾ����flA
 */

$current_path = dirname(__FILE__)."/";
require_once($current_path."config.php");
include_once($current_path."inc/version-log.php");

$repository = "https://192.168.1.110/svn/dev/trunk/client-resources/";

$log = `svn log -v -r $client_resources_revision:HEAD $repository`;
$log = format_log($log);

file_put_contents($current_path."__0.2_svn-log-fla.txt", $log);


## ���������ļ�����ϷĿ¼

$templet_path = $current_path."templet/";
copy($templet_path."assets.txt", $dev_path."client/assets.txt");
copy($templet_path."Main.bat", $dev_path."client/Main.bat");
copy($templet_path."��Ŀ����.config", $dev_path."��Ŀ����.config");

file_put_contents($current_path."svn-update.bat", "
svn cleanup ${dev_path}
svn update --accept theirs-full ${dev_path}
svn cleanup ${dev_path}client-resources
svn update ${dev_path}client-resources
svn cleanup ${dev_chs_path}
svn update --accept theirs-full ${dev_chs_path}
svn cleanup ${dev_chs_path}client-resources
svn update ${dev_chs_path}client-resources
pause;
");

print $log;
print "End.\r\n";

function format_log ($log) {
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
			if (preg_match("/^ {3}(?:A|D) /", $item_lines[$j]) && preg_match("/\\.fla/", $item_lines[$j])) {
				$info2 .= $item_lines[$j]."\r\n";
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