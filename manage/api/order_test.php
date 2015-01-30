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

	$query = $db->query("
		select 
			cid,
			sid,
			name,
			server,
			server_ver,
			api_server
		from 
			servers
		where 
			FIND_IN_SET('$domain',server) <> 0
		");	
	if($db->num_rows($query))
	{
	
		
		$rs = $db->fetch_array($query);
		
		$time = time();
		$key = '@admin2_SHEN0_XIAN1_DAO1_^^';
		$chksum= md5("".$time."_".$key."");
		$name= strtolower($rs['name']);
		$url = 'http://'.$rs['server'].'/'.$name.'/route.php?m='.$order.'&tn=3&t='.$time.'&chksum='.$chksum.'&mt='.$mt.'';	////&is_tester=0 不包含测试=1包含.不传默认包含
		echo $url;
		echo '<br />';
		$o = file_get_contents($url);
		if(!$o)
		{
			echo 'ERR1';
			exit();
		}elseif($o >= 1 && $o <= 6)
		{
			echo 'ERR2';
			exit();
		}	


	}else{
	
		echo 'ERR3';
		exit();
	}
	$db->close();

echo '<pre>';
print_r(unserialize($o));
echo '</pre>';

?>