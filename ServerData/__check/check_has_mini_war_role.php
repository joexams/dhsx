<?php
###
### ����Ƿ���һԭ�ߴ����С��ߴ����Դ
###

$path = dirname(dirname(__FILE__))."/";

# /client/
$path_client = dirname(dirname($path))."/client/";
# /client/assets/roles/war/
$path_assets_war_roles_res = $path_client."assets/roles/war/";
$path_assets_war_roles_player_as = $path_client."com/assist/view/war/roles/players/";
$path_assets_war_roles_monster_as = $path_client."com/assist/view/war/roles/monsters/";

## �����Դ

$hash = check_file($path_assets_war_roles_res, ".swf", "��Դ");
var_export($hash);

## ���AS��

#$hash1 = check_file($path_assets_war_roles_player_as, ".as", "��");
#$hash2 = check_file($path_assets_war_roles_monster_as, ".as", "��");
#$hash = array_merge($hash1, $hash2);
#ksort($hash);

#var_export($hash);
###

function check_file ($path, $ext, $desc) {	
	$list = glob($path."*".$ext);
	$len = count($list);
	
	$hash = array();
	for ($i = 0; $i < $len; $i++) {
		$filename = basename($list[$i], $ext);
		
		$is_mini = preg_match("/Mini$/", $filename);
		$orgin_name = preg_replace("/Mini$/", "", $filename);
		
		$has_origin = file_exists($path.$orgin_name.$ext);
		$has_mini   = file_exists($path.$orgin_name."Mini".$ext);
		
		if (false == $has_origin || false == $has_mini) {
			$hash[$filename] = false == $has_mini ? $filename.",û����С��$desc" : "û��ԭ�ߴ�$desc";
		}
	}
	
	return $hash;
}

?>