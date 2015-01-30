<?php
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}

	$sid=ReqNum('sid');
	if(!$sid) 
	{
		showMsg('未选择服务器！','','web');	
		exit();	
	}
	$query = $db->query("
	select 
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name,
		A.server,
		A.cid,
		A.name,
		A.o_name,
		A.private,
		A.open_date,
		A.combined_to,
		A.is_combined,
		B.name as company_name,
		B.money_type,
		B.coins_rate,
		B.slug,
		B.key,
		C.name2
	from 
		servers A
		left join company B on A.cid = B.cid
		left join servers_address C on A.db_server = C.name and C.type = 1
	where 
		A.sid = '$sid'
	");
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
						
	}else{
		showMsg('无此服务器！','','web');	
		exit();	
	}


?>