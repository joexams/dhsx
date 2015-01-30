<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("SELECT m.id, m.name, g.deploy_grid_type_id FROM deploy_mode m, deploy_grid g WHERE m.id = g.deploy_mode_id AND g.type = 1;");

$list1 = $dbh->query("SELECT m.id,g.deploy_grid_type_id FROM deploy_mode m, deploy_grid g WHERE m.id = g.deploy_mode_id AND g.type = 2;");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : ['".$item["name"]."', ".$item["deploy_grid_type_id"]."]";
}

$hash1 = "";
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["id"]." : [".$item["deploy_grid_type_id"]."]";
}

### 类

$str = "package com.assist.server
{
	public class DeployType
	{
		// id : [name, deploy_grid_type_id]
		private static const Deploys : Object = {
".$hash."
		};
		//第二阵眼位置
		private static const SecondEye : Object = {
		".$hash1."	
		};
		/**
		 * 获取阵型名称
		 * @param id int
		 */
		public static function getDeployName (id : int) : String
		{
			return Deploys[id] ? Deploys[id][0] : '';
		}
		
		/**
		 * 获取阵型阵眼位置
		 * @param id int
		 */
		public static function getDeployEye (id : int) : int
		{
			return Deploys[id] ? Deploys[id][1] : 0;
		}
		
		/**
		 * 获取第二阵眼位置
		 * @param id int
		 */
		public static function getSecondEye(id:int) : int
		{
			return SecondEye[id]?SecondEye[id][0] : 0;
		}
	}
}
";

file_put_contents($desc_dir."DeployType.as", addons().$str);

echo "[data] deploy_type  [Done]\n";
?>