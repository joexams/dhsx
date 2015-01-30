<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

//-----------------------------------转移订单
function TransferOrder() {
	global $db,$adminWebID,$adminWebType,$adminWebName; 
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
				
	}

	$db->close();
	include_once template('setting_pay_transfer');
}	

//-----------------------------------补单
function PayAdd() {
	global $db,$adminWebID,$adminWebType,$adminWebName; 
	//$company_list = globalDataList('company');//运营商


	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
				
	}


	
	$amount = rand(1,99);
	$coins = $amount*10;
	$oid = random(20);
	$dtime = date('Y-m-d H:i:s');
	$db->close();
	include_once template('setting_pay_add');
}	

//-----------------------------------保存补单
function SavePayAdd() {
	global $db,$adminWebID,$adminWebType,$adminWebName; 
	$server = ReqStr('server');
	$username = trim(ReqStr('username'));
	$coins = ReqStr('coins');//将获得的元宝
	$amount = ReqStr('amount');//充值金额
	$oid = ReqStr('oid');//订单号
	
	$dtime = ReqStr('dtime');//时间
	$dtime = strtotime($dtime);//时间戳
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}	
	
	if (!$sid && !$username && !$oid && !$dtime && !$coins)
	{
		showMsg("ERROR");
		return;	
	}
	$query = $db->query("select A.sid,B.key from servers A left join company B on A.cid = B.cid  where FIND_IN_SET('$server',A.server) <> 0");		
	if($db->num_rows($query))
	{
		$rs = $db->fetch_array($query);	
		$key = $rs['key']; //密钥
		$sid = $rs['sid']; //SID
			
	}else{
		showMsg("NOSERVER");
		return;	
	}	
	//------------------------------------------------------------
	$sign = md5($username.'_'.$coins.'_'.$oid.'_'.$server.'_'.$key);//组合MD5
	$url = SXD_SYSTEM_PAY_ADD.'?user='.urlencode($username).'&gold='.$coins.'&amount='.$amount.'&order='.$oid.'&domain='.$server.'&time='.$dtime.'&sign='.$sign;//服务器充值
	$msg = @file_get_contents($url);
	//------------------------------------------------------------
	if ($msg == 1)
	{
		$contents = '补单:充值'.$coins.'元宝,充值金额'.$amount.'元,订单号'.$oid.'';
		insertServersAdminData($cid,$sid,0,$username,$contents);//插入操作记录
		showMsg("PAYADDYOK",'','','greentext');
		return;
	}elseif ($msg == 2)
	{
		showMsg("NOSERVER");
		return;
	}elseif ($msg == 3)
	{
		showMsg("PAYADDYBERR");
		return;
	}elseif ($msg == 4)
	{
		showMsg("PAYADDFFLL");
		return;
	}elseif ($msg == 5)
	{
		showMsg("PAYADDMD5ERR");
		return;
	}elseif ($msg == 6)
	{
		showMsg("PAYADDORDEROK");
		return;
	}elseif ($msg == 7)
	{
		showMsg("NOUSER");
		return;
	}elseif ($msg == 8)
	{
		showMsg("PAYADDCLOSE");
		return;
	}elseif ($msg == 0)
	{
		showMsg("PAYADDDCERR");
		return;	
	}else{
		showMsg("PAYADDERR");
		return;	
	}
	$db->close();
	
}
 //--------------------------------------------------------------------------------------------转移订单
function SetTransferOrder() 
{
	global $db,$adminWebID,$adminWebType; 
	$cid = ReqNum('cid');
	$fsid = ReqNum('fsid');
	$tsid = ReqNum('tsid');
	$order = ReqStr('order');
	$username = trim(ReqStr('username'));

	if (!$username || !$cid || !$order) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}	
	if (!$fsid || !$tsid) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}	
	///-----------来源服
	$query = $db->query("
	select
		 A.oid,
		 A.coins,
		 A.player_id,
		 A.username,
		 B.server_ver,
		 B.api_server,
		 B.api_pwd,
		 B.api_port,
		 B.name,
		 B.is_combined
	from 
		pay_data A,
		servers B
	where 
		A.sid = B.sid
		and A.cid = '$cid' 
		and A.sid = '$fsid' 
		and A.oid = '$order'
	");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
		$f_username = $server['username'];
		$f_is_combined = $server['is_combined'];
	}else{
		showMsg(languagevar('ORDERERR'));	
		return;	
	}
	
//-------------------------------------------------------------------------------------------------------------------------


	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];



		$cmsg = api_admin::cancel_order($server['player_id'], $order);//取消该笔订单

		if (!$cmsg['result']) {	
			showMsg(languagevar('SETERR'));	
			return;	
		}	

		
		//===================================执行下一步将记录转移到目标玩家=====================================================
		
		///-----------目标服
		$query = $db->query("
		select
			`server_ver`,
			`api_server`,
			`api_pwd`,
			`api_port`,
			`name`,
			`is_combined`
			 
		from 
			servers
		where 
			cid = '$cid' 
			and sid = '$tsid'
		");		
		if($db->num_rows($query))
		{
			$trs = $db->fetch_array($query);	
			$t_is_combined = $trs['is_combined'];
		}else{
			showMsg(languagevar('NOSERVER'));	
			return;	
		}	
		if($trs['api_server'] && $trs['api_pwd'] && $trs['api_port']) 
		{
		
			
			require_once callApiVer($trs['server_ver']);
			api_base::$SERVER = $trs['api_server'];
			api_base::$PORT   = $trs['api_port'];
			api_base::$ADMIN_PWD   = $trs['api_pwd'];
		
			//----------------------帐号不存在--------------------------------------
			
/*			$player = api_admin::find_player_by_username($username);
			if (!$player['result']) {	
				showMsg(languagevar('NOUSER'));	
				return;	
			}else{
				$player_id = $player['player_id'];
			}*/
			$nick = api_admin::get_nickname_by_username($username);
			if ($nick['result']) {	
				$nickname = $nick['nickname'][1];
				$player_id = $nick['player_id'];
			}else{
				showMsg(languagevar('NOUSER'));	
				return;	
			}
							
			$vmsg = api_admin::charge($player_id,$order,$server['coins']);//充值累积用于VIP等级提升
			if (!$vmsg['result']) {	
				showMsg(languagevar('SETERR'));	
				return;	
			}else{
				$msgingot = api_admin::increase_player_ingot($player_id,$server['coins']);//加元宝
				if (!$msgingot['result']) {	
					showMsg(languagevar('SETERR'));	
					return;	
				}else{
					$db->query("update pay_data set sid = '$tsid',username = '$username',nickname = '$nickname',player_id = '$player_id' where cid= '$cid' and oid = '$order'");//更新订单记录所属服务器
					showMsg(languagevar('SETOK'),"",'','greentext');	
					$contents = languagevar('ZYDD').' '.languagevar('PAYORDER').':'.$order.' From:'.$server['username'].' ('.$server['name'].'-->'.$trs['name'].')  ('.languagevar('USERNAME').')';
					insertServersAdminData($cid,$tsid,$player_id,$username,$contents);//插入操作记录
					SetReplyPayPlayer($f_username,$cid,$f_is_combined);//更新来源用户充值
					SetReplyPayPlayer($username,$cid,$t_is_combined);//更新目标用户充值
				}
			}
		}else{
			showMsg(languagevar('ERROR'));	
			return;		
		}		
	
	}else{
		showMsg(languagevar('ERROR'));	
		return;		
	}
	

	
	$db->close();			
	
	
}
?> 