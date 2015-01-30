<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

$path_dev = dirname(dirname($path))."/";
$path_product_goods = dirname($path_dev)."/product/ͼ��/W - ��Ʒͼ��/";

if (is_dir($path_product_goods) == false) {
	print $path_product_goods." Ŀ¼�����ڡ�\n";
	exit;
}

$icons_db = get_all_icons_from_db();

$str = loop(glob($path_product_goods."*"));

print '<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<br>
<a href="#has_icon" name="top">��ͼ��û����</a>  <a href="#has_data">������ûͼ��</a>
<br><br>
';

print '<a name="has_icon">ͼ���ļ�</a>(<a href="#top">Top</a>)��<br>
<pre id="content">
'.$str.'
</pre>
<script type="text/javascript">
var obj = document.getElementById("content");

var cc = obj.innerHTML;
cc = cc.split(/\r\n|\r|\n/g);

var len = cc.length;
for (var i = 0; i < len; i++) {
	if (/\[N\]/.test(cc[i])) {
		cc[i] = cc[i] + " - <font color=\"#FF0000\">��̨����û�м�¼��ͼ��</font>";
	}
}

obj.innerHTML = cc.join("<br>");
</script>
<br><hr><br>
<font color="#FF0000"><a name="has_data">�����Ǻ�̨�����ݣ���û��ͼ���ļ�</a></font>(<a href="#top">Top</a>)��<br>
<pre>
'.from_dbs().'
</pre>
';

file_put_contents(dirname(__FILE__)."/a.txt", $str);

# ����

function loop ($urls, $depth = 0) {
	global $path_product_goods, $icons_db;
	
	$str = "";
	
	$len = count($urls);
	for ($i= 0; $i < $len; $i++) {
		$url = $urls[$i];
		
		$str .= str_repeat(" ", $depth * 4);
		
		$id = 0;
		
		if (! is_dir($url)) {
			$name = split("/", $url);
			$name = $name[count($name) - 1];
			$name = str_replace(".png", "", $name);
			
			if (array_key_exists($name, $icons_db)) {
				$str .= "<img src=\"http://ring:82/client/assets/icons/goods/".$icons_db[$name].".png\">"." [Y] ";
				
				$icons_db[$name] = 0;
			}
			else {
				$str .= "[N] ";
			}
		}
		
		$str .= str_replace($path_product_goods, "", $url)."\n";
		
		if (is_dir($url)) {
			$str .= loop(glob($url."/*"), $depth + 1);
		}
	}
	
	return $str;
}

function from_dbs () {
	global $icons_db;
	
	$str = "";
	
	foreach ($icons_db as $name => $value) {
		if ($value > 0) {
			$str .= "<u>".$name."</u>\n\n";
		}
	}
	
	return $str;
}

function get_all_icons_from_db () {
    $dbh = new DBI();
    $dbh->connect();
    
    $icons = array();

    $list = $dbh->query("select `id`, `name` from `item`;");
    foreach ($list as $item) {
        $item["name"] = iconv("utf-8", "gbk", $item["name"]);#mb_convert_encoding($item["name"], "gbk", "utf8");
        $icons[$item["name"]] = $item["id"];
    }
    
    $dbh->close();
    
    return $icons;
}
?>