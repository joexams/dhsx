<?php


if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

function ajaxPage($num, $perpage, $curpage, $mpurl, $maxpages = 0,$page=10,$showID = '') {//AJAX分页

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

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page=1\',\''.$showID.'\');" class="p" >'.languagevar('PAGEINDEX').'</a> ' : '').
			($curpage > 1 ? '<a  href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.($curpage - 1).'\',\''.$showID.'\');" class="p">'.languagevar('PAGEON').'</a> ' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? ' <strong class="curpage">'.$i.'</strong> ' :
				'<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.$i.'\',\''.$showID.'\');" class="p">'.$i.'</a> ';
		}
		
		$multipage .= ($curpage < $pages ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.($curpage + 1).'\',\''.$showID.'\');" class="p" >'.languagevar('PAGENEXT').'</a> ' : '').
			($to < $pages ? '<a href="javascript:void(0)" onclick="selectAjax(\''.$mpurl.'page='.$pages.'\',\''.$showID.'\');" class="p">'.languagevar('PAGELAST').'</a> ' : '');
		$GLOBALS['num']=$num;
		$GLOBALS['curpage']=$curpage;
		$GLOBALS['realpages']=$realpages;

		$multipage = $multipage ? '<span class="graytext">'.langmsg('PAGEMSG').'</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$multipage.'' : '';

		//$multipage = $multipage ? '<div class="page">'.$multipage.'</div>' : '';
	}
	return $multipage;
}

function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0,$page = 10) {//普通分页
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

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p" >'.languagevar('PAGEINDEX').'</a> ' : '').
			($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p">'.languagevar('PAGEON').'</a> ' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<a class="curpage">'.$i.'</a> ' :
				'<a href="'.$mpurl.'page='.$i.'" class="p">'.$i.'</a> ';
		}

		$multipage .= ($curpage < $pages ? '<a class="p" href="'.$mpurl.'page='.($curpage + 1).'">'.languagevar('PAGENEXT').'</a> ' : '').
			($to < $pages ? '<a class="p" href="'.$mpurl.'page='.$pages.'">'.languagevar('PAGELAST').'</a>' : '').
			($pages > $page ? '<a style="padding: 0px"> <input size="2" type="text" name="custompage" onKeyDown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}"></a> ' : '');
		$GLOBALS['num']=$num;
		$GLOBALS['curpage']=$curpage;
		$GLOBALS['realpages']=$realpages;
		$multipage = $multipage ? '<span class="graytext">'.langmsg('PAGEMSG').'</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$multipage.'' : '';
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
				$StrName[$key] = get_magic_quotes_gpc()?trim($val):addslashes(trim($val));
			}else{
				$StrName[$key] = get_magic_quotes_gpc()?htmlspecialchars(trim($val)):htmlspecialchars(addslashes(trim($val)));
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
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
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
	extract($GLOBALS, EXTR_SKIP);
	@include language($GLOBALS['adminWebLang']);
	if (!$url) $url = $_SERVER['HTTP_REFERER'];
	if (!$color) $color='redtext';
	
	//print_r($lang);
	if(isset($lang[$info])) {
		//eval("\$info = \"".$lang[$info]."\";");
		eval("\$info = addslashes(\"".$lang[$info]."\");");
		//$info = str_replace("'", "\'", $info);
		//echo $info.$GLOBALS['vip'];
		
	}
	
	
	
	if (!$info) {
		//include template('showmsg_noinfo');
		echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
		echo '<div class="showMsg2">';
		echo ' <span class="greentext"><img src="style/loading.gif" align="absmiddle"> Just a minute, please...</span><br/>';
		echo '</div>';		
	}else{
	
		if ($type=='web') {
			include template('showmsg_web');
			/*echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
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
			echo '</body></html>';*/
		}elseif ($type=='ajax'){
			echo '<script language="javaScript" type="text/javascript">window.parent.selectAjax("'.$url.'","'.$winid.'");</script>';				
		}else{
			include template('showmsg');
			/*if ($jump=='y') echo '<meta http-equiv="refresh" content="'.$second.';url='.$url.'">';
			echo '<div class="showMsg2">';
			echo ' <span class="'.$color.'">'.$info.'</span>';
			if ($jump=='y') echo ' <br/><span class="graytext">'.$second.' seconds return to...</span>';
			echo ' <br><br/><a href="'.$url.'"><< BACK</a>';
			echo ' <br/>';
			echo '</div>';*/	
		}
	}
}


function GetWeekDay($date) {  //计算出给出的期是星期几
   if (date("w", strtotime($date))==0){
	   return 'Sun.';
   }else{
	   return ''.date("w", strtotime($date)).'';
   }
}


//模版调用
function template($file,$tpldir = '') {
	global $tplrefresh;
	if($GLOBALS['adminWebLang'])  $lang = '.'.$GLOBALS['adminWebLang'];
	$tplfile = UCTIME_ROOT.'/'.$tpldir.'/templates/'.$file.'.htm';
	$objfile = UCTIME_ROOT.'/'.$tpldir.'/templates.tpl/'.$file.$lang.'.php';
	
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



 //写文件
function writetofile($file_name,$data,$method = "w" ,$dir='') {
		//mkpath($dir);
		//if(file_exists($dir.$file_name)) unlink ($dir.$file_name);
        if ( $filenum = fopen($dir.$file_name,$method )) { 
            flock($filenum,LOCK_UN); 
            $file_data = fwrite( $filenum, $data ); 
			chmod($dir.$file_name,0777);
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
//数据调用
function globalDataList($data,$where='',$order='',$columns='*')
{
	global $db; 
	if ($where) $setWhere = "where ".$where;
	if ($order) $setOrder = "order by ".$order;
	$query = $db->query("select $columns from $data $setWhere $setOrder");
	if($db->num_rows($query))
	{		
		while($rs = $db->fetch_array($query))
		{
			 $array[] =  $rs;			
		}
	}
	return $array;
}
//数据调用
function globalDataListPlayer($data,$where='',$order='')
{
	global $pdb; 
	if ($where) $setWhere = "where ".$where;
	if ($order) $setOrder = "order by ".$order;
	$query = $pdb->query("select * from $data $setWhere $setOrder");
	if($pdb->num_rows($query))
	{		
		while($rs = $pdb->fetch_array($query))
		{
			 $array[] =  $rs;			
		}
	}
	return $array;
}



function url_exists($url){//判断网页是否存在
	$headeraar = @get_headers($url);
	if(strpos($headeraar[0],'HTTP/1.1 200') === 0){
		return 0;
	}else{
		return 1;
	}
}
function setTime($filetime){//倒数时间计算

	$lastTime = SXD_SYSTEM_FILETIME_OUT - (time() - $filetime);
	$str = $lastTime.'(S)';		
	return $str;
}

function callApiVer($ver){//调用版本接口
	$str = UCTIME_ROOT.'/mod/'.$ver.'/api_admin.class.php';//数据操作接口
	if(file_exists($str))
	{
		return $str;
	}else{
		return UCTIME_ROOT.'/mod/api_admin.class.php';//数据操作接口
	}
}
function callPlayerVer($ver){//调用版本游戏
	$str = UCTIME_ROOT.'/mod/'.$ver.'/player.php';
	if(file_exists($str))
	{
		return $str;
	}else{
		return UCTIME_ROOT.'/mod/player.php';
	}
}

//---------------------------------------------------------------------------------------------------------------------------------
//$type = 弹出信息类型
//$show = 留空为直接输出 y为return
function webAdmin($power,$show='',$type=''){//判断是否网站管理员
	global $db,$adminWebID,$adminWebName,$adminWebPower;
	$adminWebPowerArr = explode(',',$adminWebPower);	
	if (!$adminWebID || !$adminWebName)
	{
		if ($show=='y')
		{
			return 1;
		}
		else
		{
			showMsg("NOPOWER",'login.php',$type);
			exit();		
		}
	}
	if (!in_array($power,$adminWebPowerArr) && !($adminWebName=='admin'))
	{
		if ($show=='y')
		{
			return 1;
		}	
		else
		{
			showMsg("NOPOWER",'',$type);
			exit();	
		}
	}	
}

//---------------------------------------------------------------------------------------------------------------------------------
//$look = 是否是可见栏目权限判断 0为直接输出 1为return
//$type = 弹出信息类型
//$show = 0为直接输出 1为return
function serverAdmin($power,$show='',$look='',$type=''){//判断权限
	global $db,$adminWebName,$adminWebServersPower;
	
	$adminWebServersPowerArr = explode(',',$adminWebServersPower);	
	if ((!$adminWebName || !$adminWebServersPower) && !($adminWebName=='admin'))
	{
		if ($show=='y')
		{
			return 1;
		}
		else
		{
			showMsg('NOPOWER','',$type);
			exit();		
		}
	}
	if ($look=='y')//如果该栏目涉及到查看
	{
		if (in_array($power.'-look',$adminWebServersPowerArr) && !($adminWebName=='admin'))
		{
			if ($show=='y')
			{
				return 1;
			}
			else
			{
				showMsg("NOPOWER",'',$type);
				exit();				
			}
		}	
	}		
	if (!in_array($power,$adminWebServersPowerArr) && !($adminWebName=='admin'))
	{
		if ($show=='y')
		{
			return 1;
		}	
		else
		{
			showMsg("NOPOWER",'',$type);
			exit();	
		}
	}	
}

function insertServersAdminData($cid,$sid,$player_id,$username,$contents,$type=0){//操作记录
	global $db,$adminWebID;
	$timenow = $GLOBALS['timenow'] ? $GLOBALS['timenow'] : date('Y-m-d H:i:s');
	$type = $type ? $type : 0;
	$adminWebID = $adminWebID ? $adminWebID : '0';
	//if (SXD_SYSTEM_ADMIN_DATA == 1) 
	//{	
		$db->query("insert into servers_admin_data (cid,sid,adminID,player_id,username,contents,stime,`type`) values ('$cid','$sid','$adminWebID','$player_id','$username','$contents','$timenow','$type')");//插入操作记录
	//}
}

function systemDefine($set = 0){//系统常量
	global $db;
	//if (@file_exists(UCTIME_ROOT.'/include/sxd_system.php') && !$set)
	//{
	//	include_once(UCTIME_ROOT.'/include/sxd_system.php');
	//}else{
	$setupArr = '';
	$query = $db->query("select skey,sval from setup order by sid desc");
	while($setup = $db->fetch_array($query))
	{
		$setupArr .= "define('".$setup['skey']."', '".$setup['sval']."');\r\n";
	}
	$funcdefine = create_function("",$setupArr);
	$funcdefine();	
	/*	writetofile('sxd_system.php',"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$setupArr."\r\n?>",'w',UCTIME_ROOT.'/include/');//写入
		
	}*/
}
//--------------------------------------------------------------------------------------------保存送
function  SetIncrease($cid,$sid,$type,$usertype,$username,$apply,$val,$cause,$api_server,$api_pwd,$api_port,$player_role_id=0) 
{

	$apply = $apply ? json_decode($apply, true) : array();
	if(!$sid)
	{
		return '<strong class="redtext">'.$username.' '.languagevar('NOCHOOSESERVER').'</strong>';	
	}
	if(!$type)
	{
		return '<strong class="redtext">'.$username.' '.languagevar('NOCHOOSETYPE').'</strong>';	
	}		
	$nowpowermsg = '<strong class="redtext">'.$username.' '.languagevar('NOPOWER').'</strong>';
	
	if($type == 'ingot')
	{
		if (webAdmin('increase_ingot','y')) return $nowpowermsg;	
		$typename = languagevar('SYB');
	}elseif($type == 'delingot'){
		if (webAdmin('del_ingot','y')) return $nowpowermsg;	
		$typename = languagevar('KYB');
	}elseif($type == 'coins'){
		if (webAdmin('increase_coins','y')) return $nowpowermsg;	
		$typename = languagevar('STQ');
	}elseif($type == 'delcoins'){
		if (webAdmin('del_coins','y')) return $nowpowermsg;	
		$typename = languagevar('KTQ');
	}elseif($type == 'item'){
		if (webAdmin('increase_item','y')) return $nowpowermsg;		
		$typename = languagevar('SZB');
	}elseif($type == 'exp'){
		if (webAdmin('increase_exp','y')) return $nowpowermsg;	
		$typename = languagevar('SJY');
	}elseif($type == 'mg'){
		if (webAdmin('increase_mg','y')) return $nowpowermsg;	
		$typename = languagevar('SMG');
	}elseif($type == 'delmg'){
		if (webAdmin('del_mg','y')) return $nowpowermsg;	
		$typename = languagevar('KMG');
	}elseif($type == 'repute'){
		if (webAdmin('increase_repute','y')) return $nowpowermsg;	
		$typename = languagevar('SSW');
	}elseif($type == 'thew'){
		if (webAdmin('increase_thew','y')) return $nowpowermsg;	
		$typename = languagevar('STL');
	}elseif($type == 'soul'){
		if (webAdmin('increase_soul','y')) return $nowpowermsg;	
		$typename = languagevar('SLJ');
	}elseif($type == 'skill'){
		if (webAdmin('increase_skill','y')) return $nowpowermsg;	
		$typename = languagevar('SYL');
	}elseif($type == 'point'){
		if (webAdmin('increase_point','y')) return $nowpowermsg;	
		$typename = languagevar('SJJD');
	}elseif($type == 'roll'){
		if (webAdmin('increase_roll','y')) return $nowpowermsg;	
		$typename = languagevar('SBBCS');
	}elseif($type == 'achievement'){
		if (webAdmin('increase_achievement','y')) return $nowpowermsg;	
		$typename = languagevar('WJCJ');
	}elseif($type == 'rura'){
		if (webAdmin('increase_rura','y')) return $nowpowermsg;	
		$typename = '赠送武魂';
	}elseif($type == 'times'){
		if (webAdmin('increase_times','y')) return $nowpowermsg;	
		$typename = '送喂养次数';
	}elseif($type == 'stone'){
		if (webAdmin('increase_stone','y')) return $nowpowermsg;	
		$typename = '赠送灵石';
	}

	$GLOBALS['typename']=$typename;
	$GLOBALS['val']=$val;

	if($api_server && $api_pwd && $api_port) 
	{

		//require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $api_server;
		api_base::$PORT   = $api_port;
		api_base::$ADMIN_PWD   = $api_pwd;

		//------------------------检查数量------------------------------------
		
		if ($val <= 0) {//获得的数量有错
			return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
		}
		
		//----------------------帐号不存在--------------------------------------
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}	
		
		if (!$player['result']) {
			return '<strong class="redtext">'.$username.' '.languagevar('NOUSER').'</strong>';	
		}
		if($type == 'ingot')
		{
			//------------------------送元宝------------------------------------
			$msgval = api_admin::system_send_ingot($player['player_id'],$val);
			
			if ($msgval['result'] == 1) {
				$msg = 1;
				//$show = $typename.$val;
			}else{
				$msg = 0;
			}
			$show = $typename.$val;
		}elseif($type == 'delingot'){
			//------------------------扣元宝------------------------------------
			$msgval = api_admin::decrease_player_ingot($player['player_id'],$val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}
			$show = $typename.$val;
		}elseif($type == 'coins'){
			//------------------------送铜钱------------------------------------
			$msgval = api_admin::increase_player_coins($player['player_id'],$val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}
			$show = $typename.$val;		
		}elseif($type == 'delcoins'){
			//------------------------扣铜钱------------------------------------
			$msgval = api_admin::decrease_player_coins($player['player_id'],$val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}
			$show = $typename.$val;
		}elseif($type == 'item'){
			//------------------------送装备------------------------------------
			$item_level = $apply['level'] ? $apply['level'] : 1;//若有赠送装备的时候
			$item_name = urldecode($apply['name']);
			$item_id = $apply['id'];
			if(!$item_id || !$item_name || !$item_level)
			{
				return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
			}	
			$msgval = api_admin::give_item($player['player_id'],$item_id, $val, $item_level);
			$GLOBALS['msgval']=$msgval;
			$GLOBALS['item_name']=$item_name;
			$GLOBALS['item_level']=$item_level;
			if ($msgval['success_number']) {
				$msg = 1;
				$show = langmsg('SZBOKMSG');
			}else{
				$msg = 0;
				$show = langmsg('SZBERRMSG');
			}		
		}elseif($type == 'exp'){
			//------------------------送经验------------------------------------
			$msgval = api_admin::give_exp($player['player_id'], $player_role_id, $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}	
			$show = $typename.$val;	
		}elseif($type == 'mg'){
			//------------------------送命格------------------------------------

			$fate_level = $apply['level'] ? $apply['level'] : 1;//若有赠送命格的时
			$fate_name = urldecode($apply['name']);
			$fate_id = $apply['id'];
			
			if(!$fate_id || !$fate_name || !$fate_level)
			{
				return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
			}				
			
			$msgval = api_admin::give_fate($player['player_id'], $fate_id, $fate_level, $val);
			$GLOBALS['msgval']=$msgval;
			$GLOBALS['fate_name']=$fate_name;
			$GLOBALS['fate_level']=$fate_level;
			if ($msgval['result'] > 0) {
				$msg = 1;
				$show = langmsg('SMGOKMSG');
			}else{
				$msg = 0;
				$show = langmsg('SMGERRMSG');
			}	
		}elseif($type == 'delmg'){
			//------------------------清除命格------------------------------------

			$ids = $apply['ids'];//若有赠送命格的时
			$names = $apply['names'];
			
			if(!$ids || !$names )
			{
				return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
			}				
			
			$msgval = api_admin::drop_player_fates ($player['player_id'], $ids);
			$GLOBALS['names']=$names;
			//$GLOBALS['fate_name']=$fate_name;
			//$GLOBALS['fate_level']=$fate_level;
			if ($msgval['result'] > 0) {
				$msg = 1;
				$show = langmsg('KMGOKMSG');
			}else{
				$msg = 0;
				$show = langmsg('KMGERRMSG');
			}	
		}elseif($type == 'repute'){
			//------------------------送声望------------------------------------

			$msgval = api_admin::increase_player_fame($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}elseif($type == 'thew'){
			//------------------------送体力------------------------------------

			$msgval = api_admin::increase_player_power($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}
			$show = $typename.$val;		
		}elseif($type == 'soul'){
			//------------------------送灵件------------------------------------
			$a1 =  $apply['a1'] ? $apply['a1'] : 0;
			$av1 =  $apply['av1'] ? $apply['av1'] : 0;
			$a2 =  $apply['a2'] ? $apply['a2'] : 0;
			$av2 =  $apply['av2'] ? $apply['av2'] : 0;
			$a3 =  $apply['a3'] ? $apply['a3'] : 0;
			$av3 =  $apply['av3'] ? $apply['av3'] : 0;
			$a4 =  $apply['a4'] ? $apply['a4'] : 0;
			$av4 =  $apply['av4'] ? $apply['av4'] : 0;
			$skey =  $apply['skey'] ? $apply['skey'] : 0;
			$soul_name = urldecode($apply['name']);
			$soul_id = $apply['id'];

			$msgval = api_admin::give_soul($player['player_id'], $soul_id,$a1,$av1,$a2,$av2,$a3,$av3,$a4,$av4,$skey);
			$GLOBALS['msgval']=$msgval;
			$GLOBALS['soul_name']=$soul_name;
			if ($msgval['result'] == 1) {
				$msg = 1;
				$show = langmsg('SLJOKMSG');
			}else{
				$msg = 0;
				$show = langmsg('SLJERRMSG');
			}
		}elseif($type == 'skill'){
			//------------------------送声望------------------------------------

			$msgval = api_admin::increase_player_skill($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}elseif($type == 'point'){
			//------------------------送境界点------------------------------------

			$msgval = api_admin::increase_player_state_point($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}elseif($type == 'roll'){
			//------------------------送博饼次数------------------------------------
			if($val > 100) $val = 100; //不允许超过100
			$msgval = api_admin::give_roll_count($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}elseif($type == 'achievement'){
			//------------------------设置玩家成就------------------------------------
			$achievement_name = urldecode($apply['name']);
			$achievement_id = $apply['id'];
			if(!$achievement_id || !$achievement_name)
			{
				return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
			}	
			$msgval = api_admin::complete_player_achievement($player['player_id'], $achievement_id);	
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.':'.$achievement_name;
		}elseif($type == 'rura'){
			//------------------------赠送灵气------------------------------------
			$msgval = api_admin::add_player_rura($player['player_id'], $val);	
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val.'('.$msgval['new_rura'].')';
		}elseif($type == 'times'){
			//------------------------赠送喂养次数------------------------------------
			$msgval = api_admin::add_feed_times($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}elseif($type == 'stone'){
			//------------------------赠送灵石------------------------------------
			$msgval = api_admin::increase_stone($player['player_id'], $val);
			if ($msgval['result'] == 1) {
				$msg = 1;
				
			}else{
				$msg = 0;
			}		
			$show = $typename.$val;
		}
		
		//------------------------------------------------------------
		if ($msg == 1)
		{		
			$cause = $cause ? $cause : languagevar('WTX');
			$contents = $show.','.languagevar('REASON').':'.$cause.$n;
			insertServersAdminData($cid,$sid,$player['player_id'],$username,$contents);//插入操作记录
			return '[OK] <strong>'.$username.'</strong> '.languagevar('SETOK').$show;//开头输出[OK]是程序逻辑判断要用，不能删除
		}elseif ($msg == 0){
			return '<strong class="redtext">'.$username.' '.languagevar('SETERR').$show.'</strong>';	
		}else{
			return '<strong class="redtext">'.$username.' '.languagevar('SETERR').$show.'</strong>';	
		}
	}else{
		return '<strong class="redtext">'.$username.' '.languagevar('SETERR').$show.'</strong>';
	}
}


function SetToDB($db1){//主从库切换//兼容国服和台服
	global $db;
/*	if($db1 == '4x067.xd.com:8810' || $db1 == '4x066.xd.com:8810')
	{
		return $db1;
	}*/

	if(SXD_SYSTEM_DB)
	{
		$db2 = $db->result_first("select name2 from servers_address where `type` = 1 and `name` = '$db1'");
		
		if($db2)
		{
			return $db2;
		}else{
			return $db1;
		}

	}else{
		return $db1;
		
	}
}
/*function SetToDB($db){//主从库切换//兼容国服和台服
	if($db == '4x067.xd.com:8810')
	{
		return $db;
	}

	if(SXD_SYSTEM_DB)
	{
		$pdbhost2 = $db;
		$list = explode(".", $db);
		//$index = preg_replace("/4[x|X]/", "", $list[0]);
		$index = str_replace(array("4x","sxd-"), array("",""), $list[0]);
		$index2 = str_pad(intval($index-1),3,'0',STR_PAD_LEFT);
		$domain2 = str_replace($list[0], "", $pdbhost2);
		//$pdbhost = '4x'.$index2.$domain2;
		
		if (strstr($pdbhost2, '4x')){
			$pdbhost = '4x'.$index2.$domain2;
		}elseif (strstr($pdbhost2, 'sxd-')){
			$pdbhost = 'sxd-'.$index2.$domain2;
		}else{
			$pdbhost = $db;
		}
		
		
		return $pdbhost;

	}else{
		return $db;
	}
}
*/

function SetToDB2($db){//主从库切换//兼容国服和台服
	$pdbhost2 = $db;
	$list = explode(".", $db);
	$index = str_replace(array("4x","sxd-"), array("",""), $list[0]);
	$index2 = str_pad(intval($index-1),3,'0',STR_PAD_LEFT);
	
	if (strstr($pdbhost2, '4x')){
		$pdbhost = '4x'.$index2;
	}elseif (strstr($pdbhost2, 'sxd-')){
		$pdbhost = 'sxd-'.$index2;
	}else{
		$pdbhost = $db;
	}		
	return $pdbhost;


}
function array_add($a,$b){
	$arr=array_intersect_key($a, $b);
	foreach($b as $key=>$value){
		if(!array_key_exists($key, $a)){
			$a[$key]=$value;
		}
	}
	
	foreach($arr as $key=>$value){
		$a[$key]=$a[$key]+$b[$key];
	}
	
	return $a;
}
function array_add2($a,$b){
	foreach ($b as $nodeIndex => $oneNode)
	{
		
		foreach ($oneNode as $key => $value)
		{
			if(is_numeric($value)) 
			{
				$a[$nodeIndex][$key] += $value; 
			}else{
				$a[$nodeIndex][$key] = $value; 
			}
		}

	}
	return $a;
}
function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_ASC",$SortType1 = "SORT_REGULAR")
{
    if(!is_array($ArrayData))
    {
        return $ArrayData;
    }

    // Get args number.
    $ArgCount = func_num_args();

    // Get keys to sort by and put them to SortRule array.
    for($I = 1;$I < $ArgCount;$I ++)
    {
        $Arg = func_get_arg($I);
        if(!eregi("SORT",$Arg))
        {
            $KeyNameList[] = $Arg;
            $SortRule[]    = '$'.$Arg;
        }
        else
        {
            $SortRule[]    = $Arg;
        }
    }

    // Get the values according to the keys and put them to array.
    foreach($ArrayData AS $Key => $Info)
    {
        foreach($KeyNameList AS $KeyName)
        {
            ${$KeyName}[$Key] = $Info[$KeyName];
        }
    }

    // Create the eval string and eval it.
    $EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);';
    eval ($EvalString);
    return $ArrayData;
}


function prDates($start,$end){
    $dt_start = strtotime($start);
    $dt_end = strtotime($end);
    while ($dt_start<=$dt_end){
        $date[] =  date('Y-m-d',$dt_start);
        $dt_start = strtotime('+1 day',$dt_start);
    }
	return $date;
}



function CombinedUser($user,$name,$combined_to=0)//合服帐号判断，如果是合服则加后缀来标识来源于哪台
{
	if($combined_to)
	{
		if (strpos($name,'_',0)) 
		{
			$s = explode("_",trim($name));
			$sname = $s[1];
			
		}else{
			$sname = $name;
		}
		$user = $user.'.'.$sname;
	}	
	return $user;
}
function language($file) {//调用语言包

	$languagepack = UCTIME_ROOT.'/include/'.$file.'_lang.php';
	if(file_exists($languagepack)) {
		return $languagepack;
	} else {
		return UCTIME_ROOT.'/include/'.SXD_SYSTEM_LANG.'_lang.php';
	}
}
function languagevar($var) {
	if(isset($GLOBALS['lang'][$var])) {
		return $GLOBALS['lang'][$var];
	} else {
		return "<span class=\"redtext\">!$var!</span>";
	}
}

function langmsg($var) {
	
	extract($GLOBALS, EXTR_SKIP);
	if(isset($GLOBALS['lang'][$var])) {
		eval("\$show_message = \"".$GLOBALS['lang'][$var]."\";");
		$show_message = str_replace("'", "\'", $show_message);
		return $show_message;
		//include template('showmsg');
	} else {
		return "<span class=\"redtext\">!$var!</span>";
	}
}


function CheckPwd($adminPassWord) {
	
	$nopwd = array(
		'123456',
		'654321',
		'642531',
		'asdfgh',
		'qwerty',
		'zxcvbn',
		'123qwe',
		'123asd',
		'111111',
		'112233',
	);
	
	if(strlen($adminPassWord) <= 6)
	{
		showMsg('XGMERRMSM');	
		exit();
	}	
	if(in_array($adminPassWord,$nopwd))
	{
		showMsg('XGMERRMSM');	
		exit();
	}			

}
//--------------------------------------------------------------------------------------------重计测试号

function ReServerTest($cid,$sid) {
	global $db;
	if($cid && $sid)
	{
		$query = $db->query("select * from servers where cid = '$cid' and sid = '$sid'");
		if($db->num_rows($query))
		{
			$server = $db->fetch_array($query);
		
			$pdbhost = SetToDB($server['db_server']);//数据库服务器
			$pdbuser = $server['db_root'];//数据库用户名
			$pdbpw = $server['db_pwd'];//数据库密码
			$pdbname = $server['db_name'];//数据库名	
			$pdbcharset = 'utf8';//数据库编码,不建议修改.
			$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
			//-----------------------------------------------------------------------------------------------
			$pdb = new mysql();
			$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
			unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			$query = $pdb->query("
			select 		
				username,
				nickname				
			from 
				player
			where 
				nickname <> ''
				and (is_tester = 1 OR is_tester = 2)

			");
			if($pdb->num_rows($query)){
				while($rs = $pdb->fetch_array($query)){	
					$testNameArr[] = $rs['nickname'];
					$testUserArr[] = $rs['username'];


				}
				$test_name_arr = implode("%",$testNameArr);
				$test_user_arr = implode("%",$testUserArr);
			}
			$test = $db->fetch_first("select * from servers_data where cid = '$cid' and sid = '$sid'");
			if (!$test)
			{
				if ($test_name_arr) $db->query("insert into servers_data(cid,sid,test_name_arr,test_user_arr) values ('$cid','$sid','$test_name_arr','$test_user_arr')");
			}else{
				$db->query("update servers_data set test_name_arr = '$test_name_arr',test_user_arr = '$test_user_arr' where cid = '$cid' and sid = '$sid'");
			}
		}
	}
}


//--------------------------------------------------------------------------------------------插入服务器最高等级

function SetServerMaxLevel($level,$cid,$sid) {
	global $db;
	if ($cid && $sid)
	{
		$max_player_level = $level ? $level : 0;
		$num = $db->result($db->query("select count(*)  from servers_data where cid = '$cid' and sid = '$sid'"),0); //获得总条数
		if (!$num)
		{
			$db->query("insert into servers_data(cid,sid,max_player_level) values ('$cid','$sid','$max_player_level')");
		}else{
			$db->query("update servers_data set max_player_level = '$max_player_level' where cid = '$cid' and sid = '$sid'");
		}
	}

}

//--------------------------------------------------------------------------------------------插入错误SQL日志

function SetSqlLog($sql,$page,$state) {
	global $odb;
	$time = time();
	$adminWebName = $GLOBALS['adminWebName'] ? "[".$GLOBALS['adminWebName']."]" : '';
	$val = addslashes($state." [".$page."]".$adminWebName."<br />".$sql);
	if(SXD_SYSTEM_SQLLOG)
	{	
		$odb->query("insert into sql_log(time,val) values ('$time','$val')");
	}
}

//--------------------------------------------------------------------------------------------重新计算玩家充值
function  SetReplyPayPlayer($username,$cid,$is_combined) {
	global $db;
	if($is_combined){
		$u = explode(".",$username);
		$hz = '.'.end($u);//后缀
		$username = str_replace($hz, "", $username);
	}
	$c_username = $username.'.s';
	
	$all_amount = $db->result($db->query("select sum(amount) from pay_data where cid = '$cid' and (username = '$username'  or username like '$c_username%') and success <> 0 and status <> 1"),0); //统计个人充值总额
	$pay_num = $db->result($db->query("select COUNT(*) from pay_data where cid = '$cid' and (username = '$username'  or username like '$c_username%') and success <> 0 and status <> 1"),0); //统计个人充值次数
	$last = $db->fetch_first("select sid,dtime_unix,nickname,amount from pay_data where cid = '$cid' and (username = '$username'  or username like '$c_username%') and success <> 0 and status <> 1 order by dtime desc limit 1");
	$query = $db->query("select distinct(sid) as sid  from pay_data where cid = '$cid' and (username = '$username'  or username like '$c_username%') and success <> 0 and status <> 1");
	$sid_arr = '';
	while($srs = $db->fetch_array($query))
	{	
		$sid_arr = $sid_arr ? $sid_arr.','.$srs['sid'] : $srs['sid'];
	}

	//-------------------------------------------------------------------------------------------------------------------------------------
	$nickname = addslashes($last['nickname']);

	$db->query("
	update 
		pay_player 
	set 
		sid = '$last[sid]',
		sid_arr = '$sid_arr',
		amount = '$all_amount',
		pay_num = '$pay_num',
		last_pay_amount = '$last[amount]',
		last_pay_time = '$last[dtime_unix]',
		nickname = '$nickname'
	where 
		cid = '$cid' 
		and username = '$username'
	");	
	
}

?>