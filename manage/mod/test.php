<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

  //--------------------------------------------------------------------------------------------测试号

function Test() {

	global $db,$adminWebID,$adminWebType,$adminWebName,$page;
	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		
		$set_cid_arr = "and A.cid in ($adminWebCid)";
				
	}
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}
	//---------------------------------------------------------------------
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_data A 	
		left join servers B on A.sid = B.sid
	where 
		A.cid > 0
		and B.combined_to = 0
		$set_cid
		$set_cid_arr
		
	"),0); //获得总条数
	if($num){	
		
		$query = $db->query("
		select 
			A.*,
			B.name as servers_name,
			C.name as company_name
		from 
			servers_data A
			left join servers B on A.sid = B.sid
			left join company C on A.cid = C.cid
		where 
			A.cid > 0
			and B.combined_to = 0
			$set_cid
			$set_cid_arr
		order by
			A.sid desc 
		limit
			$start_num,$pageNum
		");
		while($rs = $db->fetch_array($query)){	
			//$rs['test_name_arr'] = str_replace("%", "&nbsp;&nbsp;&nbsp;",$rs['test_name_arr']);	
			$test_name_arr = explode('%',$rs['test_name_arr']);	
			foreach($test_name_arr as $crs => $val) {
				$rs['testNameArr'] .= '<a href="?in=setting&action=SetPlayerTest&is_tester=0&usertype=2&cid='.$rs['cid'].'&sid='.$rs['sid'].'&username='.$val.'" onclick=\'javascript: return confirm("['.$val.']'.languagevar('QXCSHQX').'");\'>'.$val.'</a>&nbsp;&nbsp;&nbsp;';
			}
			
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=test&action=Test&cid=$cid");	
	}	
	$db->close();
	include_once template('test');
}
//--------------------------------------------------------------------------------------------重计测试号

function ReTest() {
	global $db,$adminWebID,$adminWebType,$adminWebCid;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	if(!$cid || !$sid )
	{
		showMsg('NOCHOOSESERVER');	
		return;
	}	
	if($adminWebType != 's')//如果不是开发
	{
		$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
		if(!in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			showMsg('NOSERVERPOWER');	
			return;
		
		}
	}		
	ReServerTest($cid,$sid);
	insertServersAdminData(0,0,0,'重计测试号','服务器ID:'.$sid);//插入操作记录		
	showMsg('SETOK','','','greentext');		
	$db->close();

}
//--------------------------------------------------------------------------------------------清空测试号

function ClearTest() {
	global $db,$adminWebID,$adminWebType,$adminWebCid;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	if(!$cid || !$sid )
	{
		showMsg('NOCHOOSESERVER');	
		return;
	}
	
	if($adminWebType != 's')//如果不是开发
	{
		$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
		if(!in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			showMsg('NOSERVERPOWER');	
			return;
		
		}
	}		
	DelServerTest($sid);
	insertServersAdminData(0,0,0,'服务器','清空测试号，服务器ID:'.$sid);//插入操作记录		
	showMsg('');		
	$db->close();

}
//--------------------------------------------------------------------------------------------清空测试号

function DelServerTest($sid) {
	global $db;
	if($sid)
	{
		$query = $db->query("select * from servers where sid = '$sid'");
		if($db->num_rows($query))
		{
			$server = $db->fetch_array($query);
		
			require_once callApiVer($server['server_ver']);
			api_base::$SERVER = $server['api_server'];
			api_base::$PORT   = $server['api_port'];
			api_base::$ADMIN_PWD   = $server['api_pwd'];
			
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
				id				
			from 
				player
			where 
				is_tester = 1 OR is_tester = 2
			");
			if($pdb->num_rows($query)){
				while($rs = $pdb->fetch_array($query)){	
					$msg = api_admin::set_tester($rs['id'], 0);
				}
			}		
			$db->query("update servers_data set test_name_arr = '' where sid = '$sid'");

		}
	}	
}
//--------------------------------------------------------------------------------------------赠送

function SetTestGift() {
	global $db,$adminWebID,$adminWebType;
	$type = ReqStr('type');
	$ingot = ReqNum('ingot') ? ReqNum('ingot') : 'n';
	$coins = ReqNum('coins') ? ReqNum('coins') : 'n';
	$vip = ReqStr('vip');
	$sid = ReqArray('sid');
	$path_root = UCTIME_ROOT.'/mod/test_gift.php';
	if(!$sid)
	{
		showMsg('NOCHOOSESERVER');	
		return;
	}	
	$sidArr =  $sid ? implode(",",$sid) : '';
	
	if($type == 'add')
	{
	
	
		$query = $db->query("
			select 
				server_ver
			from 
				servers
			where
				sid in ($sidArr)
			group by 
				server_ver
			");	
		if($db->num_rows($query))
		{
		
			while($rs = $db->fetch_array($query))
			{
				print `php $path_root $rs[server_ver] $sidArr $ingot $coins $vip`;//执行不同版本
			}	
		}
		$GLOBALS['vip']=$vip;
		if ($vip != 'n') $showvip = langmsg('TESTVIPOK');
		insertServersAdminData(0,0,0,'赠送测试号','服务器ID:'.$sidArr);//插入操作记录		
		showMsg(languagevar('TESTZSOK').$showvip,'','','greentext','','n');
		
		
	}elseif($type == 'del'){
	
		for ($i=0;$i<count($sid);$i++){
			DelServerTest($sid[$i]);
		}
		insertServersAdminData(0,0,0,'清空测试号','服务器ID:'.$sidArr);//插入操作记录		
		showMsg(languagevar('SETOK'),'','','greentext','','n');
		
	}
	$db->close();

}
?> 