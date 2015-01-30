<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
	
function Admin() 
{
	global $db,$cid,$adminWebID,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;		
	if (!webAdmin('c_admin_m','y')){
		$set_cid  = "and FIND_IN_SET('$cid',cid) <> 0";
	}else{
		$set_cid  = "and cid = '$cid'";

	}		
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		admin
	where 
		adminType = 'u'
		$set_cid
	"),0);	
	if($num)
	{			
		
		$query = $db->query("
		select 
			*
		from 
			admin
		where 
			adminType = 'u'
			$set_cid
		order by 
			adminID asc
		limit 
			$start_num,$pageNum				
		");		

		while($rs = $db->fetch_array($query))
		{			
			$admin_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=setting&action=Admin&cid=$cid");	
	}
	$db->close();
	include_once template('c_admin');
}	
function AdminPj() 
{
	global $db,$cid,$adminWebID,$page; 
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');

	if ($stime && $etime) 
	{
		$stime_s = strtotime($stime.' 00:00:00');
		$etime_e = strtotime($etime.' 23:59:59');
		$set_time = "where `date` >= '$stime_s' AND `date` <= '$etime_e'";
	}

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;		
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		admin
	where 
		adminType = 'u'
		and FIND_IN_SET('$cid',cid) <> 0
	"),0);	
	if($num)
	{			
		
		$query = $db->query("
		select 
			A.*
		from 
			admin A
		where 
			A.adminType = 'u'
			and FIND_IN_SET('$cid',A.cid) <> 0
		order by 
			A.adminID asc
		limit 
			$start_num,$pageNum				
		");		

		while($rs = $db->fetch_array($query))
		{			
			$admin_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=setting&action=AdminPj&cid=$cid&stime=$stime&etime=$etime");	
		
		
		//-----------------------------------------------------------------------------------------------------
		$query = $db->query("
		select 
			A.adminID,
			count(case when A.pj = 1 then id end) as p_1,
			count(case when A.pj = 2 then id end) as p_2,
			count(case when A.pj = 3 then id end) as p_3,
			count(case when A.pj = 4 then id end) as p_4,
			count(case when A.pj = 5 then id end) as p_5
		from 
			admin_pj_d A
			$set_time
		group by 
			A.adminID
		");		
		if($db->num_rows($query))
		{
			while($rs = $db->fetch_array($query))
			{
				$pj[$rs['adminID']] = $rs;
			}
			//print_r($pj);
		}
		
		
		
		
	}
	$db->close();
	include_once template('c_admin_pj');
}	
function AdminC() 
{
	global $db,$cid,$adminWebID,$page,$adminWebCid; 
	$cid = ReqNum('cid');
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$adminWebCidArr =  explode(',',$adminWebCid);
	if ($cid == 999999999 && !webAdmin('c_admin_m','y')){
		$set_cid  = "and A.cid LIKE '%,%'";
	}else{
		if ($cid)
		{
			$set_cid  = "and A.cid = '$cid'";
		}else{
			$cid = $adminWebCidArr[0];
			$set_cid  = "and A.cid = '$cid'";
		}
	}		
				
	$company_list = globalDataList('company',"cid in (".$adminWebCid.")",'corder asc,cid asc');//运营商
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		admin A
		left join company B on A.cid = B.cid
	where 
		A.adminType = 'c'
		and A.adminID <> '$adminWebID'
		$set_cid
	"),0);	
	if($num)
	{			
		
		$query = $db->query("
		select 
			A.*,
			B.name as company_name
		from 
			admin A
			left join company B on A.cid = B.cid
		where 
			A.adminType = 'c'
			and A.adminID <> '$adminWebID'
			$set_cid
		order by 
			A.adminID asc
		limit 
			$start_num,$pageNum				
		");		

		while($rs = $db->fetch_array($query))
		{			
			$admin_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=admin_c&action=AdminC&cid=$cid");	
	}
	$db->close();
	include_once template('c_admin_c');
}	
function AddAdmin()
{
	global $db,$cid,$adminWebPower,$adminWebServersPower,$adminWebLang;
	$adminID = ReqNum('id');
	if($adminID)
	{
		$query = $db->query("
		select 
			adminPower,
			serversPower
		from 
			admin
		where 
			adminID = '$adminID'
			and adminType = 'u'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();			
		}else{
			showMsg('NODATA');
			return;
		}	
	
	}
	$sxd_system_company_kf = "'".str_replace(array("c_",","), array("u_","','"),$adminWebPower)."'";
	$sxd_system_company_s_kf = "'".str_replace(array("data_key",","), array("","','"),$adminWebServersPower)."'";	
	$servers_power_array = globalDataList('servers_power',"power in (".$sxd_system_company_s_kf.")",'porder asc,pid asc');//远程权限	
	$power_array = globalDataList('setup_power',"power in (".$sxd_system_company_kf.") and ptype = 'u'",'porder asc,pid asc');//系统权限
	
	$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");	
	$db->close();
	include_once template('c_admin_add');
}
function AddAdminC()
{
	global $db,$cid,$adminWebPower,$adminWebServersPower,$adminWebCid,$adminWebLang;
	$adminID = ReqNum('id');
	if($adminID)
	{
		$query = $db->query("
		select 
			adminPower,
			serversPower
		from 
			admin
		where 
			adminID = '$adminID'
			and adminType = 'c'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();			

		}else{
			showMsg('NODATA');
			return;
		}		
	}else{
	}
	
	$sxd_system_company_kf = "'".str_replace(array(",","server_open","key_data_set","c_admin_m","logs","key_power","game_data"), array("','","","","","","",""),$adminWebPower)."'";
	$sxd_system_company_s_kf = "'".str_replace(array("data_key",","), array("","','"),$adminWebServersPower)."'";
	
	$servers_power_array = globalDataList('servers_power',"power in (".$sxd_system_company_s_kf.")",'porder asc,pid asc');//远程权限	
	$power_array = globalDataList('setup_power',"power in (".$sxd_system_company_kf.") and ptype = 'c'",'porder asc,pid asc');//系统权限	
	$company_list = globalDataList('company',"cid in (".$adminWebCid.")",'corder asc,cid asc');//运营商
	$db->close();
	include_once template('c_admin_add_c');
}

function EditAdmin()
{
	global $db,$adminWebID,$cid,$adminWebPower,$adminWebServersPower,$adminWebLang; 
	$adminID = ReqNum('id');
	if (empty($adminID))
	{
		showMsg('ERROR');
	}else{
		$query = $db->query("
		select 
			*
		from 
			admin
		where 
			adminID = '$adminID'
			and adminType = 'u'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$rs['adminAllowLoginIP'] = str_replace("|", "\n",$rs['adminAllowLoginIP']);	
			$adminServersArr =  $rs['servers'] ? explode(',',$rs['servers']) : array();		
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();			
			
			$sxd_system_company_kf = "'".str_replace(array("c_",","), array("u_","','"),$adminWebPower)."'";
			$sxd_system_company_s_kf = "'".str_replace(array("data_key",","), array("","','"),$adminWebServersPower)."'";
				
			$servers_power_array = globalDataList('servers_power',"power in (".$sxd_system_company_s_kf.")",'porder asc,pid asc');//远程权限	
			$power_array = globalDataList('setup_power',"power in (".$sxd_system_company_kf.") and ptype = 'u'",'porder asc,pid asc');//系统权限	
			$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");	

			include_once template('c_admin_edit');
		}else{
			showMsg('NODATA');
		}
	}
	$db->close();
}

function EditAdminC()
{
	global $db,$adminWebID,$cid,$adminWebPower,$adminWebServersPower,$adminWebCid,$adminWebLang; 
	$cid = ReqNum('cid');
	$adminID = ReqNum('id');
	if (empty($adminID))
	{
		showMsg('ERROR');
		
	}else{
		if ($cid == 999999999 && webAdmin('c_admin_m','y'))
		{
			showMsg('NOPOWER');
			return;
		}
	
	
	
		$query = $db->query("
		select 
			A.*,
			B.name as company_name
		from 
			admin A
			left join company B on A.cid = B.cid
		where 
			A.adminID = '$adminID'
			and adminType = 'c'
			and A.cid in ($adminWebCid)
			and A.adminID <> '$adminWebID'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$rs['adminAllowLoginIP'] = str_replace("|", "\n",$rs['adminAllowLoginIP']);	
			$adminServersArr =  $rs['servers'] ? explode(',',$rs['servers']) : array();		
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();			
			
			if ($cid == 999999999 && !webAdmin('c_admin_m','y') && strrpos($rs['cid'],","))
			{
				$sxd_system_company_kf = "'".str_replace(array(","), array("','"),$adminWebPower)."'";
				$sxd_system_company_s_kf = "'".str_replace(array(","), array("','"),$adminWebServersPower)."'";
			}else{
				$sxd_system_company_kf = "'".str_replace(array(",","server_open","key_data_set","c_admin_m","logs","key_power","game_data"), array("','","","","","","",""),$adminWebPower)."'";
				$sxd_system_company_s_kf = "'".str_replace(array("data_key",","), array("","','"),$adminWebServersPower)."'";
			}
		
			
				
			$servers_power_array = globalDataList('servers_power',"power in (".$sxd_system_company_s_kf.")",'porder asc,pid asc');//远程权限	
			$power_array = globalDataList('setup_power',"power in (".$sxd_system_company_kf.") and ptype = 'c'",'porder asc,pid asc');//系统权限	
			$company_list = globalDataList('company',"cid in (".$adminWebCid.")",'corder asc,cid asc');//运营商
			include_once template('c_admin_edit_c');
		}else{
			showMsg('NODATA');
		}
	}
	$db->close();
}
//--------------------------------------------------------------------------增加管理员
function SaveAddAdmin() {
	global $db,$adminWebID,$cid; 
	$adminName = trim(ReqStr('adminName'));
	$adminPassWord = trim(ReqStr('adminPassWord'));
	$adminPower = ReqArray('adminPower');
	$servers=ReqArray('servers');
	$serversPower=ReqArray('serversPower');	
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	$adminLang = ReqStr('adminLang');
	$all = ReqStr('all');
	$ip = getIp();	

	if(!empty($servers)) $servers = implode(",",$servers);//组合为字符串
	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);
	if($all == 'on') $servers = '';//如果选择所有
	if (!$adminName || !$adminPassWord)
	{
		showMsg('ERROR');
	}else{
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('ADMINADDMSG');
		}else{
		
			CheckPwd($adminPassWord);
		
			$adminPassWord = md5($adminPassWord);
			$db->query("
			insert into 
				admin 
			(
				adminName,
				adminPassWord,
				adminPower,
				adminType,
				cid,
				servers,
				serversPower,
				adminLoingIP,
				adminCreateID,
				adminAllowLoginIP,
				adminLang,
				adminLoingTime
				
			)values(
				'$adminName',
				'$adminPassWord',
				'$adminPower',
				'u',
				'$cid',
				'$servers',
				'$serversPower',
				'$ip',
				'$adminWebID',
				'$adminAllowLoginIP',
				'$adminLang',
				now()
				
			)");
			insertServersAdminData(0,0,0,'客服管理','客服管理:增加客服('.$adminName.')');//插入操作记录		
			showMsg('SETOK',"?in=setting&action=Admin&cid=$cid",'','greentext');
		}
	}
	$db->close();
}  
//--------------------------------------------------------------------------增加联运号
function SaveAddAdminC() {
	global $db,$adminWebID,$cid; 
	$adminName = trim(ReqStr('adminName'));
	$adminPassWord = trim(ReqStr('adminPassWord'));
	$adminPower = ReqArray('adminPower');
	$serversPower=ReqArray('serversPower');	
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	$adminLang = ReqStr('adminLang');
	$cid = ReqNum('cid');
	$ip = getIp();	

	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);

	if (!$adminName || !$adminPassWord)
	{
		showMsg('ERROR');
	}else{
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('ADMINADDMSG');
		}else{
		
			CheckPwd($adminPassWord);
		
			$adminPassWord = md5($adminPassWord);
			$db->query("
			insert into 
				admin 
			(
				adminName,
				adminPassWord,
				adminPower,
				adminType,
				cid,
				servers,
				serversPower,
				adminLoingIP,
				adminCreateID,
				adminAllowLoginIP,
				adminLang,
				adminLoingTime
				
			)values(
				'$adminName',
				'$adminPassWord',
				'$adminPower',
				'c',
				'$cid',
				'$servers',
				'$serversPower',
				'$ip',
				'$adminWebID',
				'$adminAllowLoginIP',
				'$adminLang',
				now()
				
			)");
			insertServersAdminData(0,0,0,'联运号管理','联运号管理:增加联运号('.$adminName.')');//插入操作记录		
			showMsg('SETOK',"?in=admin_c&action=AdminC",'','greentext');
		}
	}
	$db->close();
}  

//--------------------------------------------------------------------------修改管理员
function SaveEditAdmin() 
{
	global $db,$cid,$adminWebID,$adminWebName; 
	$adminID=ReqNum('id');
	//$adminName=trim(ReqStr('adminName'));
	$adminPassWord=trim(ReqStr('adminPassWord'));
	$adminPower=ReqArray('adminPower');
	$servers=ReqArray('servers');
	$serversPower=ReqArray('serversPower');
	$adminLock = ReqNum('adminLock');
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	$all = ReqStr('all');

	if(!empty($servers)) $servers = implode(",",$servers);//组合为字符串
	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);
	if($all == 'on') $servers = '';//如果选择所有
	if (empty($adminID))
	{
		showMsg('ERROR');
	}else{
	
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName' and adminID <> '$adminID'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('ADMINADDMSG');
		}else{
			if ($adminPassWord != "")//判断是否修改密码
			{
			
				CheckPwd($adminPassWord);
				$adminPassWord = md5($adminPassWord);
				$setPassword=",adminPassWord = '$adminPassWord'";
			}else{
				$setPassword="";
			}



			$db->query("
			update 
				admin 
			set 
				adminPower = '$adminPower',
				servers = '$servers',
				serversPower = '$serversPower',
				adminLock = '$adminLock',
				adminAllowLoginIP = '$adminAllowLoginIP'
				$setPassword 
			where
				adminID = '$adminID' 
				and adminType = 'u'
			");
			insertServersAdminData(0,0,0,'客服管理','客服管理:修改客服(ID:'.$adminID.','.$adminName.')');//插入操作记录		
			showMsg('SETOK','','','greentext');		
		}
	
	}
	$db->close();
}   


//--------------------------------------------------------------------------修改联运号
function SaveEditAdminC() 
{
	global $db,$cid,$adminWebID,$adminWebName,$adminWebCid; 
	$adminID=ReqNum('id');
	//$adminName=trim(ReqStr('adminName'));
	$adminPassWord=trim(ReqStr('adminPassWord'));
	$adminPower=ReqArray('adminPower');
	$serversPower=ReqArray('serversPower');
	$adminLock = ReqNum('adminLock');
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	$cid = ReqNum('cid');

	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);


	if (empty($adminID)  || !$cid)
	{
		showMsg('ERROR');
	}else{
	
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName' and adminID <> '$adminID'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('ADMINADDMSG');
		}else{
			if ($adminPassWord != "")//判断是否修改密码
			{
				CheckPwd($adminPassWord);
				$adminPassWord = md5($adminPassWord);
				$setPassword=",adminPassWord = '$adminPassWord'";
			}else{
				$setPassword="";
			}



			$db->query("
			update 
				admin 
			set 
				adminPower = '$adminPower',
				serversPower = '$serversPower',
				adminLock = '$adminLock',
				adminAllowLoginIP = '$adminAllowLoginIP'
				$setPassword 
			where
				adminID = '$adminID' 
				and adminType = 'c'
				and cid in ($adminWebCid)
				and adminID <> '$adminWebID'
			");
			insertServersAdminData(0,0,0,'联运号管理','联运号管理:修改联运号(ID:'.$adminID.','.$adminName.')');//插入操作记录		
			showMsg('SETOK','','','greentext');		
		}
	
	}
	$db->close();
}   
?> 