<?php
require_once('pinyin_table.php');
$text = "����";
$flow = get_pinyin_array($text);
echo "<xmp>";
printf("ԭ�ִ�:\n%s\n",$text);
print_r($flow);
echo "</xmp>";
?>