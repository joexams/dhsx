<?php


if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}


function daddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}


function ajaxPage($num, $perpage, $curpage, $mpurl, $maxpages = 0,$page=10,$showID = '') {//AJAX分页
	if ($_SERVER['PHP_SELF'] == '/t_alpha.php'){
		$mpurl = str_replace('t.php','t_alpha.php',$mpurl);
	}

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		//$page = 10;
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page=1\',\''.$showID.'\');" class="p" >首页</a> ' : '').
			($curpage > 1 ? '<a  href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.($curpage - 1).'\',\''.$showID.'\');" class="p">上一页</a> ' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? ' <strong class="curpage">'.$i.'</strong> ' :
				'<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.$i.'\',\''.$showID.'\');" class="p">'.$i.'</a> ';
		}

		$multipage .= ($curpage < $pages ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.($curpage + 1).'\',\''.$showID.'\');" class="p" >下一页</a> ' : '').
			($to < $pages ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.$pages.'\',\''.$showID.'\');" class="p">尾页</a> ' : '');

		$multipage = $multipage ? '<span class="graytext">共有'.$num.'条数据&nbsp;&nbsp;当前<strong>'.$curpage.'</strong>页/共'.$realpages.'页</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$multipage.'' : '';

		//$multipage = $multipage ? '<div class="page">'.$multipage.'</div>' : '';
	}
	return $multipage;
}

function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0,$page = 10) {//普通分页
	if ($_SERVER['PHP_SELF'] == '/t_alpha.php'){
		$mpurl = str_replace('t.php','t_alpha.php',$mpurl);
	}
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		//$page = 10;
		$offset = 2;

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p" >首页</a> ' : '').
			($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p">上一页</a> ' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<a class="curpage">'.$i.'</a> ' :
				'<a href="'.$mpurl.'page='.$i.'" class="p">'.$i.'</a> ';
		}

		$multipage .= ($curpage < $pages ? '<a class="p" href="'.$mpurl.'page='.($curpage + 1).'">下一页</a> ' : '').
			($to < $pages ? '<a class="p" href="'.$mpurl.'page='.$pages.'">尾页</a>' : '').
			($pages > $page ? '<a style="padding: 0px"> <input size="2" type="text" name="custompage" onKeyDown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}"></a> ' : '');

		$multipage = $multipage ? '<span class="graytext">共有'.$num.'条数据&nbsp;&nbsp;当前<strong>'.$curpage.'</strong>页/共'.$realpages.'页</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$multipage.'' : '';
	}
	return $multipage;
}
//$num 记录数
//$perpage 每页记录数
//$curpage 当前页
//$mpurl  url地址
//$maxpages 可选项

function botTime() {
	global $stime;  
	$etime=microtime(true);//获取程序执行结束的时间
	$total=substr($etime-$stime,0,8);   //计算差值
	return "Processed in {$total} second(s)";
}


function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){//---字符号截取功能
      if(function_exists("mb_substr")){  
          if(mb_strlen($str, $charset) <= $length) return $str;
          $slice = mb_substr($str, $start, $length, $charset);
      }else { 
          $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]|[\xf0-\xff][\x80-\xbf]/";
          $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";  
          $re['gbk']          = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
          $re['big5']          = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/"; 
          preg_match_all($re[$charset], $str, $match); 
          if(count($match[0]) <= $length) return $str; 
          $slice = join("",array_slice($match[0], $start, $length));  
      }
      //if($suffix) return $slice."…"; 
      return $slice;
}  
//function msubstr($string, $from, $length = null){//---字符号截取功能
//	preg_match_all('/[\x80-\xff]?./', $string, $match);
//	if(is_null($length)){
//		$result = implode('', array_slice($match[0], $from));
//	}else{
//		$result = implode('', array_slice($match[0], $from, $length));
//	}
//	return $result;
//}


//---------接受的（防注入函数）-----------------------

function ReqNum($StrName) {
	$StrName=intval($_REQUEST[$StrName]);
	return $StrName;
}
function ReqArray($StrName,$html='') {
	$StrName=$_POST[$StrName];
	if($StrName!=''){
		foreach($StrName as $key => $val) {
			if($html=='htm') {//如果是支持HTML
				$StrName[$key] = get_magic_quotes_gpc()?$val:addslashes($val);
			}else{
				$StrName[$key] = get_magic_quotes_gpc()?htmlspecialchars($val):htmlspecialchars(addslashes($val));
			}
		}
	}
	return $StrName;
}

function ReqStr($StrName,$html='') {
	if(is_array($StrName)) {
		foreach($StrName as $key => $val) {
			$StrName[$key] = ReqStr($val);
		}
	} else {
		if($html=='htm') {//如果是支持HTML
			$StrName = get_magic_quotes_gpc()?$_REQUEST[$StrName]:addslashes($_REQUEST[$StrName]);
		}else{
			$StrName = get_magic_quotes_gpc()?htmlspecialchars($_REQUEST[$StrName]):htmlspecialchars(addslashes($_REQUEST[$StrName]));
		}
	}
	return $StrName;
}

function KillBad($string){//目录漏洞修补
	$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',str_replace(array('..','.','/',' '), array('','','',''), $_GET[$string]));
	$string = get_magic_quotes_gpc()?htmlspecialchars($string):htmlspecialchars(addslashes($string));
	return $string;
}

//-------------输出的（过滤函数）---------------------------------


function dehtml($str){//只支持换行，但不支持其它HTML语法/可用在除论坛贴子外其它需要换行的字段
	//$str = str_replace(' ', '&nbsp;', $str);//替换空格
	$str = nl2br(stripslashes($str));
	return $str;
}


//---------------------------------------------

function random($length) {//产生随机字符串函数 
	$hash = ''; 
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
	$max = strlen($chars) - 1; 
	mt_srand((double)microtime() * 1000000); 
		for($i = 0; $i < $length; $i++) { 
			$hash .= $chars[mt_rand(0, $max)]; 
		} 
		return $hash; 
}

// -------------------获取客户端IP---------------------------
function getIp() {
	if (isset($_SERVER)) {
		if (isset($_SERVER[HTTP_X_FORWARDED_FOR])) {
			$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
		} elseif (isset($_SERVER[HTTP_CLIENT_IP])) {
			$realip = $_SERVER[HTTP_CLIENT_IP];
		} else {
			$realip = $_SERVER[REMOTE_ADDR];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}
//使用:$user_ip=getIp(); 


/*
$info 提示信息内容
$url 转跳地址(留空则转到前一页)
$type 提示样式(web=独立页样式)
$color 提示信息内容颜色(取样式名如:greentext/redtext)留空为红色
$jump 是否转跳
$second 转跳等待时间以秒为单位
*/
function showMsg($info='',$url='',$type='',$color='',$winid='winmask',$jump='y',$second=1){
	if (!$url) $url = $_SERVER['HTTP_REFERER'];
	if (!$color) $color='redtext';
	if (!$info) {
		echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
		echo '<div class="showMsg2">';
		echo ' <span class="greentext"><img src="style/loading.gif" align="absmiddle"> Just a minute, please...</span><br/>';
		echo '</div>';		
	}else{
	
		if ($type=='web') {
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml">';
			echo '<head>';
			echo '<title>Info....</title>';
			if ($jump=='y') echo '<meta http-equiv="refresh" content="'.$second.';url='.$url.'">';
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<link href="style/style.css" rel="stylesheet" type="text/css" />';
			echo '</head><body>';
			echo '<div class="showMsgWeb">';
			echo ' <span class="'.$color.'">'.$info.'</span>';
			if ($jump=='y') echo ' <br/><span class="graytext">'.$second.' seconds return to...</span><br><br/>';
			echo ' <br><br/><a href="'.$url.'"> < BACK</a>';
			echo '</div>';
			echo '</body></html>';
		}elseif ($type=='ajax'){
			echo '<script language="javaScript" type="text/javascript">window.parent.selectAjax("'.$url.'","'.$winid.'");</script>';				
		}else{
			if ($jump=='y') echo '<meta http-equiv="refresh" content="'.$second.';url='.$url.'">';
			echo '<div class="showMsg2">';
			echo ' <span class="'.$color.'">'.$info.'</span>';
			if ($jump=='y') echo ' <br/><span class="graytext">'.$second.' seconds return to...</span>';
			echo ' <br><br/><a href="'.$url.'"><< BACK</a>';
			echo ' <br/>';
			echo '</div>';	
		}
	}
}



//模版调用
function template($file,$tpldir = '') {
	global $tplrefresh;
	
	$tplfile = UCTIME_ROOT.'/'.$tpldir.'/templates/'.$file.'.htm';
	$objfile = UCTIME_ROOT.'/'.$tpldir.'/templates.tpl/'.$file.'.tpl';
	
	if($tplrefresh == 1 || ($tplrefresh > 1 && substr($GLOBALS['timestamp'], -1) > $tplrefresh)) {
		if(@filemtime($tplfile) > @filemtime($objfile)) {
			require_once UCTIME_ROOT.'/include/template.func.php';
			parse_template($file,$tpldir);
		}
	}
	return $objfile;
}

//加密函数
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}



//导出SQL
function sqldumptable($table, $fp=0) {
	global $db;
	$tabledump  = "DROP TABLE IF EXISTS $table;\n";
	$tabledump .= "CREATE TABLE $table (\n";
	$firstfield = 1;
	$fields = $db->query("SHOW FIELDS FROM $table");
	while ($field = $db->fetch_array($fields)) {
		if (!$firstfield) {
			$tabledump .= ",\n";
		} else {
			$firstfield = 0;
		}
		$tabledump .= "   $field[Field] $field[Type]";
		if (!empty($field["Default"])) {
			$tabledump .= " DEFAULT '$field[Default]'";
		}
		if ($field['Null'] != "YES") {
			$tabledump .= " NOT NULL";
		}
		if ($field['Extra'] != "") {
			$tabledump .= " $field[Extra]";
		}
	}
	$db->free_result($fields);
	$keys = $db->query("SHOW KEYS FROM $table");
	while ($key = $db->fetch_array($keys)) {
		$kname = $key['Key_name'];
		if ($kname != "PRIMARY" and $key['Non_unique'] == 0) {
			$kname="UNIQUE|$kname";
		}
		if(!is_array($index[$kname])) {
			$index[$kname] = array();
		}
		$index[$kname][] = $key['Column_name'];
	}
	$db->free_result($keys);

	while(list($kname, $columns) = @each($index)) {
		$tabledump .= ",\n";
		$colnames=implode($columns,",");
		if ($kname == "PRIMARY") {
			$tabledump .= "   PRIMARY KEY ($colnames)";
		} else {
			if (substr($kname,0,6) == "UNIQUE") {
				$kname=substr($kname,7);
			}
			$tabledump .= "   KEY $kname ($colnames)";
		}
	}
	$tabledump .= "\n);\n\n";
	if ($fp) {
		fwrite($fp,$tabledump);
	} else {
		echo $tabledump;
	}
	$rows  = $db->query("SELECT * FROM $table");
	$numfields = $db->num_fields($rows);
	while ($row = $db->fetch_row($rows)) {
		$tabledump    = "INSERT INTO $table VALUES(";
		$fieldcounter = -1;
		$firstfield   = 1;
		while (++$fieldcounter<$numfields) {
			if (!$firstfield) {
				$tabledump.=", ";
			} else {
				$firstfield=0;
			}

			if (!isset($row[$fieldcounter])) {
				$tabledump .= "NULL";
			} else {
				$tabledump .= "'".mysql_escape_string($row[$fieldcounter])."'";
			}
		}
		$tabledump .= ");\n";
		if ($fp) {
			fwrite($fp,$tabledump);
		} else {
			echo $tabledump;
		}
	}
	$db->free_result($rows);
}
//将SQL分成单句
function splitsql($sql){
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return($ret);
}

 //写文件
function writetofile($file_name,$data,$method = "w" ,$dir='') {
		//mkpath($dir);
        if ( $filenum = fopen($dir.$file_name,$method )) { 
            flock( $filenum,LOCK_UN); 
            $file_data = fwrite( $filenum, $data ); 
            fclose($filenum); 
            //return $file_data; 
        }else{ 
            return false; 
        } 
    }
	
	
//创建多级目录 使用mkpath("aa/bb/cc/dd/ee/ff"); 
function mkpath($path,$mode=0777) { 
	if (!file_exists($path)) { 
		mkpath(dirname($path)); 
		mkdir($path,$mode); 
		chmod($path,$mode); 
	} 
} 


//文字首字母
function getLetter($input){

$letters=array(

'A'=>0xB0C4,

'B'=>0xB2C0,

'C'=>0xB4ED,

'D'=>0xB6E9,

'E'=>0xB7A1,

'F'=>0xB8C0,

'G'=>0xB9FD,

'H'=>0xBBF6,

'J'=>0xBFA5,

'K'=>0xC0AB,

'L'=>0xC2E7,

'M'=>0xC4C2,

'N'=>0xC5B5,

'O'=>0xC5BD,

'P'=>0xC6D9,

'Q'=>0xC8BA,

'R'=>0xC8F5,

'S'=>0xCBF9,

'T'=>0xCDD9,

'W'=>0xCEF3,

'X'=>0xD188,

'Y'=>0xD4D0,

'Z'=>0xD7F9,

);



    $input = iconv('UTF-8', 'GB18030', $input);



    $str = substr($input, 0, 1);

    if ($str >= chr(0x81) && $str <= chr(0xfe))    {

        $num = hexdec(bin2hex(substr($input, 0, 2)));

        foreach ($letters as $k=>$v){

            if($v>=$num)

            break;

        }

        return $k;

    }

    else{

        return $str;

    }

}

?>