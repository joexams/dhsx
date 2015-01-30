<?php
$pass = isset($argv[1]) ? trim($argv[1]) : null;
if ($pass != 'cron') {
    exit('invalid args');
}

	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	$pdb = new mysql();
	$query = $db->query("
	select 
		cid,
		sid,
		db_server,
		db_root,
		db_pwd,
		db_name
	from 
		servers A
	where 
		A.open_date < now()
		AND A.combined_to = 0
		and A.test = 0 
		and A.open = 1
	order by
		A.open_date desc,
		A.sid desc
	");	
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{
			//------------------------------------------连远程数据库-----------------------------------------------------
			$pdbhost = SetToDB($rs['db_server']);//数据库服务器
			$pdbuser = $rs['db_root'];//数据库用户名
			$pdbpw = $rs['db_pwd'];//数据库密码
			$pdbname = $rs['db_name'];//数据库名	
			$pdbcharset = 'utf8';//数据库编码,不建议修改.
			$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
			
			$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
			unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			//-----------------------------------------------等级----------------------------------------------------------------------------------------------------------------------------------------------------------
			$max_level = $pdb->result_first("SELECT MAX(level) FROM player_role");				
			$max_level = $max_level ? $max_level : 0;
			SetServerMaxLevel($max_level,$rs['cid'],$rs['sid']);//更新最高等级
			//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------		
			unset($max_level);
			
		}
	
	}	
	$db->close();
	$pdb->close();
?>