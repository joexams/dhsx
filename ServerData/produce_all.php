<?php
$path = dirname(__FILE__)."/";
require_once($path."config.php");

$dbh = new DBI();

$args = args();
if (count($args) == 6) {
	$dbh->db_host = $args[1];
	$dbh->db_port = $args[2];
	$dbh->db_name = $args[3];
	$dbh->db_user = $args[4];
	$dbh->db_pwd  = $args[5];
}

$dbh->connect();

$_templets = glob($path."templet/*.php");
for ($_i = 0; $_i < count($_templets); $_i++) {
	$_basename = basename($_templets[$_i]);
	require_once($path."templet/".$_basename);
}

print "---\n";

### 生成 class_list.as 由

$list = glob($desc_dir."*.as");

$str = "private var _list : Array = [\r\n";
for ($i = 0; $i < count($list); $i++) {
	$basename = basename($list[$i], ".as");
	if ($basename != "classList") {
		$str .= "	".$basename.",\r\n";
	}
}

file_put_contents($desc_dir."classList.as", $str."	0\r\n];");

print repeat("[data] class_list", 75, ".")."DONE.\n";

### 生成 Template.as

$list = glob($client_dir."com/assist/server/source/*.as");

$str = "";
for ($i = 0; $i < count($list); $i++) {
	$basename = basename($list[$i], ".as");
	
	if (false == preg_match("/0$|1$/", $basename)) {
		if ($str != "") $str .= "\r\n";
		$str .= "			".$basename.";";
	}
}

file_put_contents($client_dir."Templet.as", "package
{
	import com.assist.server.source.*;
	
	import flash.display.Sprite;
	
	public class Templet extends Sprite
	{
		public function Templet ()
		{
".$str."
		}
	}
}");

repeat("[data] template", 75, ".")."DONE.\n";

$dbh->close();
?>