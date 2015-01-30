<?php
	$ver = $argv[1];
	$sidArr = $argv[2];
	$type = $argv[3];
	$val = $argv[4];
	if (!$ver || !$sidArr || !$type || !$val ) {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	require_once callApiVer($ver);
	$query = $db->query("
	select 
		api_server,
		api_port,
		api_pwd,
		db_server,
		db_root,
		db_pwd,
		db_name
	from 
		servers
	where 
		sid in ($sidArr)
		and server_ver = '$ver'
		and open = 1
	order by 
		sid desc
	");
	while($srs = $db->fetch_array($query)){
		api_base::$SERVER = $srs['api_server'];
		api_base::$PORT   = $srs['api_port'];
		api_base::$ADMIN_PWD   = $srs['api_pwd'];
		
		$pdbhost = SetToDB($srs['db_server']);//数据库服务器
		$pdbuser = $srs['db_root'];//数据库用户名
		$pdbpw = $srs['db_pwd'];//数据库密码
		$pdbname = $srs['db_name'];//数据库名	
		$pdbcharset = 'utf8';//数据库编码,不建议修改.
		$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
		//-----------------------------------------------------------------------------------------------
		$pdb = new mysql();
		$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
		unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			
		$pquery = $pdb->query("
		select 		
			id,
			nickname				
		from 
			player
		where
			is_tester = 0
		order by 
			id desc
		");
		if($pdb->num_rows($pquery)){
			while($prs = $pdb->fetch_array($pquery)){
				if($type == 'ingot')
				{
					//if($prs['nickname']) echo $prs['nickname'].'<br />';
					if($prs['nickname']) {
						if($ver >= 2011072101){
							api_admin::system_send_ingot($prs['id'],$val);
						}else{
							api_admin::increase_player_ingot($prs['id'],$val);
						}					
					}
					
					
					
				}elseif($type == 'coins'){
					if($prs['nickname']) api_admin::increase_player_coins($prs['id'],$val);//送铜钱
				}elseif($type == 'thew'){
					if($prs['nickname']) api_admin::increase_player_power($prs['id'],$val);//送体力
				}			
			}
		}
		$pdb->close();
	}
	$db->close();

?> 