<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `message_sign`, `template_message`, `type` from `delay_notify_message_template`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($constant != "") {
		$hash .= ",\n";
		$constant .= "\n		\n";
	}
	
	$signs = explode("_", $item["message_sign"]);
	$sign = "";
	for ($j = 0; $j < count($signs); $j++) {
		$sign .= strtoupper(substr($signs[$j], 0, 1)).substr($signs[$j], 1);
	}
	
	$hash .= "			".$item["id"]." : [\"\", \"\", ".$item["type"]."]";
	
	$constant .= "		// ".preg_replace("/\r|\n/", " ", $item["template_message"])."\n";
	$constant .= "		public static const ".$sign." : int = ".$item["id"].";";
}

### 类

$str = "package com.assist.server
{
	public class DelayNotifyType
	{
		// 系统
		public static var System : int = 0;
		// 互动
		public static var Email  : int = 1;
		// pk
		public static var PK     : int = 2;
		
		// id : [sign, template_message, type]
		private static const List : Object = {
".$hash."
		};
		
".$constant."
		
		/**
		 * 获取类型
		 *
		 * @param id int
		 */
		public static function getType (id : int) : int
		{
			return List[id] ? List[id][2] : 0;
		}
	}
}
";

file_put_contents($desc_dir."DelayNotifyType.as", addons().$str);

echo "[data] delay_notify_type  [Done]\n";
?>