<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
$domain = ReqStr('domain');
$order = ReqStr('order');//plt=等级；plt=副本
$mt = ReqNum('mt');//0:普通副本排行,1:英雄副本排行
if(!$domain || !$order)
{
	echo 0;//域名和排行类型为空
	exit();
}
if($order != 'plt' && $order != 'mt')
{
	echo 0;//域名和排行类型为空
	exit();
}
/*
$filename = $domain."_sxd_".$order."_".$mt.".php";//文件名
$dir = UCTIME_ROOT."/data/";//目录
$flie = $dir.$filename;//全地址
$filetime  = @filemtime($flie);//文件创建时间
@include_once($flie);
if(!$filetime || time() - $filetime >= 3600)	$is_update = 1;	//如果调用的缓存文件不存在或过期
$updatetime = setTime($filetime);


if ($is_update) 
{
*/
	$query = $db->query("
		select 
			sid,
			name,
			api_server
		from 
			servers
		where 
			FIND_IN_SET('$domain',server) <> 0
		");	
	if($db->num_rows($query))
	{
	
		
		$rs = $db->fetch_array($query);
		
/*		require_once callApiVer($rs['server_ver']);
		api_base::$SERVER = $rs['api_server'];
		api_base::$PORT   = $rs['api_port'];
		api_base::$ADMIN_PWD   = $rs['api_pwd'];*/
		$time = time();
		$key = '@admin2_SHEN0_XIAN1_DAO1_^^';
		$chksum= md5("".$time."_".$key."");
		$name= strtolower($rs['name']);
		$url = 'http://'.$rs['api_server'].'/'.$name.'/route.php?m='.$order.'&tn=10&t='.$time.'&chksum='.$chksum.'&mt='.$mt.'';	////&is_tester=0 不包含测试=1包含.不传默认包含
		echo $url;
		echo '<br />';
		$o = file_get_contents($url);
		//echo $o;
		//echo '<br />';
		if(!$o)
		{
			$o = serialize(array());
		}elseif($o >= 1 && $o <= 6)
		{
			$o = serialize(array());
		}	
	}else{
	
		$o = serialize(array());
	}
	$db->close();
//}
//-------------------------------------生成缓存文件------------------------------------------------------	
/*if ($is_update) 
{
	$str = '$o='.var_export($o, TRUE).";\n";//存入数组 
	writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
}*/

/*if($order == 'mt')
{
	$list = unserialize($o); 
	foreach($list as $rs) { 
		$rs['mission_name'] = str_replace(array('(',')'), array('',''), $rs['mission_name']);
		$oo[] = $rs;
	}
	$o = serialize($oo);
}*/

	//echo $o;



echo '<pre>';
print_r(unserialize($o));
echo '</pre>';

?>