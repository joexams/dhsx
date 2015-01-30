<?php
require_once('pinyin_table.php');
$text = "Àý×Ó";
$flow = get_pinyin_array($text);
echo "<xmp>";
printf("Ô­×Ö´®:\n%s\n",$text);
print_r($flow);
echo "</xmp>";
?>