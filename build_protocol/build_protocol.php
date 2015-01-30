<?php
$not_build_file = array(
    '00_player.txt',
    '99_admin.txt',
    '100_mobile.txt'
);

$mod_list = array();
$action_list = array();

for ($i = 0; $i < 256; $i++) {
    //120,156用于压缩包头识别
    if (in_array($i, array(0, 99, 100, 120, 156))) {
        continue;
    }
    array_push($mod_list, $i);
    array_push($action_list, $i);
}

$protocol_dir = dirname(dirname(dirname(__FILE__))) . '/server-new/doc/通讯协议/';
$dir_fp = opendir($protocol_dir);

$match = array();
$file_list = array();
while ($file_name = readdir($dir_fp)) {
    if (preg_match_all("/\d{1,}/", $file_name, $match, PREG_SET_ORDER)) {
        array_push($file_list, $file_name);
    }
}


foreach ($file_list as $file_name) {
    if (in_array($file_name, $not_build_file)) {
        continue;
    }
    $the_action_list = $action_list;

    $file_path = "{$protocol_dir}{$file_name}";
    $file_content = '';

    $fp = fopen($file_path, 'r');
    $is_mod_line = false;
    while (!feof($fp)) {
        $match_list = array();
        $line = fgets($fp);
        if (preg_match_all('/=\s*\d{1,}/', $line, $match_list)) {
            $match_value_list = $match_list[0];
            foreach ($match_value_list as $match_value) {
                $n = 0;
                if (!$is_mod_line) {
                    list($n, $mod_list) = rand_value($mod_list);
                    $is_mod_line = true;
                }
                else {
                    list($n, $the_action_list) = rand_value($the_action_list);
                }
                $new_match_value = preg_replace('/\d{1,}/', $n, $match_value);
                $line = str_replace($match_value, $new_match_value, $line);
            }
        } 

        $file_content .= $line;
    }
    fclose($fp);

    file_put_contents($file_path, $file_content);
}


function rand_value ($arr) {
    $i = rand(0, count($arr) - 1);
    $value = $arr[$i];
    
    unset($arr[$i]);

    return array(
        $value,
        array_values($arr)
    );
}
?>
