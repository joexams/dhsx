<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('admin');
switch (ReqStr('action'))
{
	case 'DelAdmin': DelAdmin();break;	
	case 'SaveAddAdmin': SaveAddAdmin();break;	
	case 'AddAdmin': AddAdmin();break;	
	case 'EditAdmin': EditAdmin();break;
	case 'AdminLogin': AdminLogin();break;
	case 'AdminLoginErr': AdminLoginErr();break;
	case 'SaveEditAdmin': SaveEditAdmin();break;
	case 'SetAdminCompany': SetAdminCompany();break;
	case 'DelAdminLoginErr': DelAdminLoginErr();break;
	default:  Admin();
}
	
function Admin() 
{
	global $db,$page; 
	$type = ReqStr('type');
	$cid = ReqNum('cid');
	$adminName = ReqStr('adminName');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商
	if (!$type && !$adminName)
	{
		$type = "s";
		$set_type = "and A.adminType = '$type'";
	}elseif($type){
		$set_type = "and A.adminType = '$type'";
	}
	if ($adminName)
	{
		$set_adminName = " and adminName like '%$adminName%'";
	}		
	if ($cid == 999999999)
	{
		$set_cid  = "and A.cid LIKE '%,%' and adminType = 'c'";
		//$set_cid_arr  = "and FIND_IN_SET('$cid',A.cid) <> 0";
	}elseif ($cid == 888888888)
	{
		$set_cid  = "and A.cid LIKE '%,%' and adminType = 'u'";
		//$set_cid_arr  = "and FIND_IN_SET('$cid',A.cid) <> 0";
	}elseif($cid){
		$set_cid  = "and A.cid = '$cid'";
	}
				
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		admin A
	where 
		A.adminID > 0
		$set_type
		$set_cid
		$set_adminName
	"),0);	
	if($num)
	{			
		
		$query = $db->query("
		select 
			A.*
		from 
			admin A 
		where 
			A.adminID > 0
			$set_type
			$set_cid
			$set_adminName
		order by 
			A.adminID asc
		limit 
			$start_num,$pageNum				
		");		

		while($rs = $db->fetch_array($query))
		{
/*			//---------------------------------------------------------------

			if ($rs['adminPower']) 
			{
				$rs['adminPowerA'] = explode(',',$rs['adminPower']);
				foreach($rs['adminPowerA'] as $ars => $val)
				{
					$rs['adminPowerList'] .= "'".$val."',";
				}
				$rs['adminPowerList'] =  substr($rs['adminPowerList'],0,strlen($rs['adminPowerList'])-1);
				$rs['admin_power_list'] = globalDataList('setup_power',"power in (".$rs['adminPowerList'].")",'ptype asc,porder asc');//服务器权限
				
			}
			
			//---------------------------------------------------------------

			if ($rs['servers']) 
			{
				$rs['servers_list'] = globalDataList('servers',"sid in (".$rs['servers'].")");//服务器权限
				
			}
			
			//---------------------------------------------------------------
			if ($rs['serversPower']) 
			{
				$rs['serversPowerA'] = explode(',',$rs['serversPower']);
				foreach($rs['serversPowerA'] as $prs => $val)
				{
					$rs['serversPowerList'] .= "'".$val."',";
				}
				$rs['serversPowerList'] =  substr($rs['serversPowerList'],0,strlen($rs['serversPowerList'])-1);
				$rs['servers_power_list'] = globalDataList('servers_power',"power in (".$rs['serversPowerList'].",'ptype asc,porder asc')");//服务器权限
				
			}
			//---------------------------------------------------------------
			$rs['serversPowerArr'] = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();
			$rs['adminPowerArr'] = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
*/			
			$rs['name_url'] = urlencode($rs['adminName']);
			$admin_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=admin&type=$type&cid=$cid");	
	}
	$db->close();
	include_once template('s_admin');
}	
function AddAdmin()
{
	global $db; 
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商
	
	
	$adminID = ReqNum('id');
	if($adminID)
	{
		$query = $db->query("
		select 
			adminPower,
			serversPower,
			adminType
		from 
			admin
		where 
			adminID = '$adminID'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$ptype = $rs['adminType'];
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();			
		}else{
			showMsg('NODATA');
			return;
		}	
	
	}else{
		$ptype = 's';
	}
	
	
	
	//------------------------------------远程权限----------------------------------------------------
	$query = $db->query("select * from servers_power order by ptype asc, porder asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
			$servers_power_array[] =  $prs;
			
		}
	}	
	
	//--------------------------------------系统权限--------------------------------------------------
	$query = $db->query("select * from setup_power where ptype = '$ptype' order by porder asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
			$power_array[] =  $prs;
			
		}
	}
	$db->close();
	include_once template('s_admin_add');
}
function EditAdmin()
{
	global $db,$adminWebID,$adminWebName; 
	$adminID = ReqNum('id');
	if (empty($adminID))
	{
		showMsg('错误参数！');
	}elseif ($adminID == $adminWebID && $adminWebName != 'admin')
	{
		showMsg('无法修改自己的资料！');
	}else{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商		
		$query = $db->query("
		select 
			A.*
		from 
			admin A 
		where 
			A.adminID = '$adminID'
	 ");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			$rs['adminAllowLoginIP'] = str_replace("|", "\n",$rs['adminAllowLoginIP']);	
			//$rs['adminSetPower'] = str_replace("|", "\n",$rs['adminSetPower']);	
			//$rs['adminSetServersPower'] = str_replace("|", "\n",$rs['adminSetServersPower']);	
			$adminType = $rs['adminType'];//类型
			$cid = $rs['cid'];
			$squery = $db->query("
			select 
				A.sid,
				A.name,
				A.o_name,
				A.server,
				B.name as company_name 
			from 
				servers A 
				left join company B on A.cid = B.cid 
			where 
				A.cid in ($cid) 
			order by 
				B.corder asc,A.sid desc
			");
			while($srs = $db->fetch_array($squery)){
				$servers_list[] = $srs;
			}	
					
			$adminServersArr =  $rs['servers'] ? explode(',',$rs['servers']) : array();
			$adminCidArr =  $rs['cid'] ? explode(',',$rs['cid']) : array();

			$servers_power_array = globalDataList('servers_power','','ptype asc, porder asc');//远程权限
			$power_array = globalDataList('setup_power',"ptype = '$adminType'",'porder asc');//系统权限

				
			$adminPowerArr = $rs['adminPower'] ? explode(',',$rs['adminPower']) : array();
			$serversPowerArr = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();
			include_once template('s_admin_edit');
		}else{
			showMsg('无此信息！');
		}
	}
	$db->close();
}

//------------------------------------------------------管理员登陆历史
function AdminLogin() 
{
	global $db,$page;
	include_once(UCTIME_ROOT."/include/ip.php");
	$pageNum = 30; 
	$start_num = ($page-1)*$pageNum;
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			servers_admin_login					
		"),0); //获得总条数
	if($num){			

		$query = $db->query("
		select 
			A.*,
			B.adminName,
			B.adminType
		from 
			servers_admin_login A
			left join admin B on A.adminID = B.adminID

		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=admin&action=AdminLogin");	
	}
	
	$db->close();
	include_once template('s_admin_login');

}

//------------------------------------------------------管理员错误登陆历史
function AdminLoginErr() 
{
	global $db,$page,$adminWebName;
	include_once(UCTIME_ROOT."/include/ip.php");
	$pageNum = 30; 
	$start_num = ($page-1)*$pageNum;
	$adminName = ReqStr('adminName');
	$adminLoingIP = ReqStr('adminLoingIP');
	
	if ($adminName) $set_admin = "and adminName = '$adminName'";
	if ($adminLoingIP) $set_ip = "and adminLoingIP = '$adminLoingIP'";
	
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			admin_login_err	
		where
			id > 0
			$set_admin
			$set_ip				
		"),0); //获得总条数
	if($num){			

		$query = $db->query("
		select 
			*
		from 
			admin_login_err
		where
			id > 0
			$set_admin
			$set_ip				
		order by 
			id desc
		limit 
			$start_num,$pageNum				
		");
				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=admin&action=AdminLoginErr&adminName=$adminName&adminLoingIP=$adminLoingIP");	
	}
	
	$db->close();
	include_once template('s_admin_login_err');

}
//--------------------------------------------------------------------------增加管理员
function SaveAddAdmin() {
	global $db; 
	$adminType = ReqStr('adminType');
	$adminName = trim(ReqStr('adminName'));
	$adminPassWord = trim(ReqStr('adminPassWord'));
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	$adminPower = ReqArray('adminPower');
	$servers=ReqArray('servers');
	$serversPower=ReqArray('serversPower');	
	$adminLang = ReqStr('adminLang');
	//$adminSetPower=ReqStr('adminSetPower');	
	//$adminSetServersPower=ReqStr('adminSetServersPower');	
	$cid = ReqArray('cid');
	$ip = getIp();	

	if(!empty($servers)) $servers = implode(",",$servers);//组合为字符串
	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$cid = !empty($cid) ? implode(",",$cid) : 0;//组合为字符串

	if (!$adminName || !$adminPassWord)
	{
		showMsg('错误参数！');
	}else{
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('管理员已存在！');
		}else{
			$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);
			//$adminSetPower = str_replace(array("\n","\r","\t"), array("|","",""),$adminSetPower);
			//$adminSetServersPower = str_replace(array("\n","\r","\t"), array("|","",""),$adminSetServersPower);
			
			$adminPassWord = md5($adminPassWord);
			$db->query("
			insert into 
				admin 
			(
				adminName,
				adminPassWord,
				adminPower,
				adminAllowLoginIP,
				adminType,
				cid,
				servers,
				serversPower,
				adminLoingIP,
				adminLang,
				adminLoingTime
				
			)values(
				'$adminName',
				'$adminPassWord',
				'$adminPower',
				'$adminAllowLoginIP',
				'$adminType',
				'$cid',
				'$servers',
				'$serversPower',
				'$ip',
				'$adminLang',
				now()
				
			)");
			insertServersAdminData(0,0,0,'管理员','管理员:增加管理员('.$adminName.')');//插入操作记录		
			showMsg('操作成功！','?in=admin','','greentext');
		}
	}
	$db->close();
}  

//--------------------------------------------------------------------------修改管理员
function SaveEditAdmin() 
{
	global $db,$adminWebName; 
	$adminID=ReqNum('id');
	$adminName=trim(ReqStr('adminName'));
	$adminPassWord=trim(ReqStr('adminPassWord'));
	$adminAllowLoginIP = ReqStr('adminAllowLoginIP');
	//$adminSetPower = ReqStr('adminSetPower');
	//$adminSetServersPower = ReqStr('adminSetServersPower');
	$adminPower=ReqArray('adminPower');
	$servers=ReqArray('servers');
	$serversPower=ReqArray('serversPower');
	$cid = ReqArray('cid');
	$adminLock = ReqNum('adminLock');
	$adminLang = ReqStr('adminLang');
	$adminLoginErr = ReqStr('adminLoginErr');
	if(!empty($servers)) $servers = implode(",",$servers);//组合为字符串
	if(!empty($serversPower)) $serversPower = implode(",",$serversPower);//组合为字符串
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	$cid = !empty($cid) ? implode(",",$cid) : 0;//组合为字符串
	$adminAllowLoginIP = str_replace(array("\n","\r","\t"), array("|","",""),$adminAllowLoginIP);
	//$adminSetPower = str_replace(array("\n","\r","\t"), array("|","",""),$adminSetPower);
//	$adminSetServersPower = str_replace(array("\n","\r","\t"), array("|","",""),$adminSetServersPower);
	
	if (empty($adminID)  || !$adminName)
	{
		showMsg('错误参数！');
	}elseif ($adminName == 'admin' && $adminWebName != 'admin')
	{
		showMsg('您没有权限操作顶级管理员资料！');
	}elseif ($adminName == $adminWebName && $adminWebName != 'admin')
	{
		showMsg('无法修改自己的资料！');
	}else{
	
		$num = $db->result($db->query("select count(*) from admin where adminName = '$adminName' and adminID <> '$adminID'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('管理员已存在！');
		}else{
			if ($adminPassWord != "")//判断是否修改密码
			{
				$adminPassWord = md5($adminPassWord);
				$setPassword=",adminPassWord = '$adminPassWord'";
			}else{
				$setPassword="";
			}
			$db->query("
			update 
				admin 
			set 
				cid = '$cid',
				adminName = '$adminName',
				adminPower = '$adminPower',
				adminAllowLoginIP = '$adminAllowLoginIP',
				servers = '$servers',
				serversPower = '$serversPower',
				adminLock = '$adminLock',
				adminLang = '$adminLang',
				adminLoginErr = '$adminLoginErr'
				$setPassword 
			where
				adminID = '$adminID' 
			");
			insertServersAdminData(0,0,0,'管理员','管理员:修改管理员(ID:'.$adminID.','.$adminName.')');//插入操作记录		
			showMsg('操作成功！','','','greentext');		
		}
	
	}
	$db->close();
}  

//--------------------------------------------------------------------------删除管理员
function DelAdmin()
{
	global $db; 
	$adminID=ReqNum('id');
	if (empty($adminID))
	{
		showMsg('错误参数！');
	}else{
		if ($adminID==1)
		{
			showMsg('顶级管理员无法删除！');
	
		}else{	
			$db->query("delete from admin where adminID = '$adminID'");
			$db->query("delete from servers_admin_login where adminID = '$adminID'");
			insertServersAdminData(0,0,0,'管理员','管理员:删除管理员(ID:'.$adminID.')');//插入操作记录		
			showMsg();
		}
	}
	$db->close();
} 
 //--------------------------------------------------------------------------设置多平台
function SetAdminCompany() 
{
	global $db,$adminWebName; 
	$id=ReqArray('id');
	$cid = ReqArray('cid');


	$id = !empty($id) ? implode(",",$id) : 0;//组合为字符串
	$cid = !empty($cid) ? implode(",",$cid) : 0;//组合为字符串
	
	if (empty($id) || !$cid)
	{
		showMsg('错误参数！');
	}else{
	
		$db->query("
		update 
			admin 
		set 
			cid = '$cid'
		where
			adminID in ($id)
		");
		insertServersAdminData(0,0,0,'管理员','管理员:设置多平台(ID:'.$id.',CID:'.$cid.')');//插入操作记录		
		showMsg('操作成功！','','','greentext');		
	
	}
	$db->close();
} 

//--------------------------------------------------------------------------------------------批量删除错误登陆记录
function  DelAdminLoginErr() 
{
	global $db; 
	$id_del = ReqArray('id_del');

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from admin_login_err where id in ($id_arr)");
		
		insertServersAdminData(0,0,0,'错误登陆记录','错误登陆记录:删除错误登陆的记录ID('.$id_arr.')');//插入操作记录		
		$db->close();
		showMsg('删除成功！','','','greentext');	
		
	}else{
		showMsg('未选择！');	
	}		

}
 
?> 