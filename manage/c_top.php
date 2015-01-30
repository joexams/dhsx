<?php 
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
//取运营商数据
	if (!$adminWebCid)
	{
		showMsg(languagevar('NOCOMPANYMSG'),'login.php','web','','','n');
		exit();		
	}

	$cid = ReqNum('cid');
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();

	if(!$cid || $cid == 999999999)
	{
		$cid = $adminCidArr[0];
	}elseif($cid && !in_array($cid,$adminCidArr) && !($cid == 999999999)){//如果服务器不属于此运营商
		showMsg(languagevar('NOSERVERPOWER'),'login.php','web','','','n');	
		exit();
	
	}		
	
	//$company_list = globalDataList('company',"cid in ($adminWebCid)","corder asc");//运营商
	
	$query = $db->query("
	select 
		A.cid,
		A.name,
		A.link,
		A.game_text,
		sum(if(B.gdate = DATE_SUB(curdate(), INTERVAL 1 DAY),B.pay_amount,0)) as yesterday_amount
	from 
		company A
		left join game_data B on A.cid = B.cid
	where 
		A.cid in ($adminWebCid)
	group by
		A.cid
	order by 
		yesterday_amount desc,
		A.corder asc,
		A.cid asc		
	");
	while($rs = $db->fetch_array($query))
	{
		$rs['yesterday_amount'] = round($rs['yesterday_amount'],2);
		$company_list[] =  $rs;
	}	
	
	
	
	if ($company_list) $crows = array_chunk($company_list,5); 	
	if(!empty($cid)) 
	{
		$company = $db->fetch_first("select * from company where cid = '$cid'");

	}
	
	$timeoffset = isset($company['timeoffset']) && $company['timeoffset'] != 9999 ? $company['timeoffset'] : SXD_SYSTEM_TIMEOFFSET;//判断是否与系统默认时差不同
	date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
	$timenow = date('Y-m-d H:i:s');
	$tomorrow = date("Y-m-d",strtotime("+1 day"));
	//$tomorrow_server_count = $db->result($db->query("select count(*) from servers where cid in ($adminWebCid) and (DATE_FORMAT(open_date, '%Y-%m-%d') = '$tomorrow' or DATE_FORMAT(open_date, '%Y-%m-%d') = curdate())"),0);		
	
?>