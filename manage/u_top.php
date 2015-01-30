<?php 
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
//取客服数据
	//----------------运营商----------------------
	if (!$adminWebCid)
	{
		showMsg(languagevar('NOCOMPANYMSG'),'login.php','web','','','n');
		exit();		
	}
/*	if (!$adminWebServers)
	{
		showMsg("您还没有负责的服务器记录，请联系管理员！",'login.php','web','','','n');
		exit();		
	}	*/


	$cid=ReqNum('cid');
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();

	if(!$cid)
	{
		$cid = $adminCidArr[0];
	}elseif($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
		showMsg(languagevar('NOSERVERPOWER'),'login.php','web','','','n');	
		exit();
	
	}		
		
	$company_list = globalDataList('company',"cid in ($adminWebCid)");//运营商
	if ($company_list) $crows = array_chunk($company_list,5); 	
	

	if(!empty($cid)) 
	{
		$query = $db->query("
		select 
			A.*
		from 
			company A
		where 
			A.cid = '$cid'
		");
		if($db->num_rows($query))
		{
			$company = $db->fetch_array($query);
		}
	}
	
	$timeoffset = isset($company['timeoffset']) && $company['timeoffset'] != 9999 ? $company['timeoffset'] : SXD_SYSTEM_TIMEOFFSET;//判断是否与系统默认时差不同
	date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	$timenow = date('Y-m-d H:i:s');
?>