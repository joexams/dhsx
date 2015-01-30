<?php
###
### 检查是否有一原尺寸和缩小版尺寸的资源
###

$path = dirname(dirname(__FILE__))."/";

# /client/
$path_client = dirname(dirname($path))."/client/";
# /client/assets/roles/war/
$path_assets_war_roles_res = $path_client."assets/roles/war/";
$path_assets_war_roles_player_as = $path_client."com/assist/view/war/roles/players/";
$path_assets_war_roles_monster_as = $path_client."com/assist/view/war/roles/monsters/";

## 检查资源

$hash = check_file($path_assets_war_roles_res, ".swf", "资源");
var_export($hash);

## 检查AS类

#$hash1 = check_file($path_assets_war_roles_player_as, ".as", "类");
#$hash2 = check_file($path_assets_war_roles_monster_as, ".as", "类");
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
			$hash[$filename] = false == $has_mini ? $filename.",没有缩小版$desc" : "没有原尺寸$desc";
		}
	}
	
	return $hash;
}

?>