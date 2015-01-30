<?php
$path = dirname(__FILE__)."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

require_once($path."__check/check_monster.php");
$content = check_monster();
$content = split(str_repeat("=", 80), $content);

$monster_signs = all_monster();

####

$monster_signs = "";
$has = array();

$list = $dbh->query("select `id`, `sign`, `name` from `monster`");
$count = 0;
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	$id   = $item["id"];
	$sign = $item["sign"];
	$name = $item["name"];
	
	if (array_key_exists($sign, $has)) {
		continue;
	}
	$has[$sign] = 1;
	
	$count++;
	
	$monster_signs .= "<span type=\"sign\"><!--".repeat($id, 4)." -->".repeat($sign, 20)." : ".trim($name).",</span>\n";
}

print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
print "
<style type=\"text/css\">
td {
	padding: 5px;
	font-size: 10.5pt;
	line-height: 150%;
	border: 5px solid #ccc;
}

span.sign {
	border-bottom:1px solid #ccc;
	padding:5px 0;
}
</style>";
print "
<script type=\"text/javascript\">
function onList () {
	var list = document.getElementById(\"list\");
	var sign = document.getElementById(\"sign\");
	
	list.style.display = \"\";
	sign.style.display = \"none\";
}
function onSign () {
	var list = document.getElementById(\"list\");
	var sign = document.getElementById(\"sign\");
	
	list.style.display = \"none\";
	sign.style.display = \"\";
}
</script>
";
print "<div><a href=\"#\" onclick=\"onList()\">怪物标识与资源使用列表</a>&nbsp;&nbsp;<a href=\"#\" onclick=\"onSign()\">怪物列表</a></div><br>";
print "<table cellspace=\"5\" align=\"left\">";
print "<tr id=\"list\">";
print "<td valign=\"top\"><pre>".$content[0]."</pre></td>";
print "<td valign=\"top\"><pre>".$content[1]."</pre></td>";
print "</tr>";
print "<tr id=\"sign\" style=\"display:none;\">";
print "<td>".$count."<pre>".$monster_signs."</pre></td>";
print "</tr>";
print "</table>";

$dbh->close();
?>