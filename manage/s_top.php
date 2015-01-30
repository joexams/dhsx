<?php 
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
//取运营商数据

	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	if($sid)
	{
		$set_n = ",B.name as servers_name";
		$set_left = "left join servers B on A.cid = B.cid and B.sid = '$sid'";
	}
	if($cid)
	{
		$compamy = $db->fetch_first("
		select 
			A.cid,
			A.name as company_name
			$set_n
		from 
			company  A
			$set_left
		where 
			A.cid = '$cid'
		");
		$title_conmpany_name = $compamy['company_name'];
		$title_servers_name = $compamy['servers_name'];
	}
	$timenow = date('Y-m-d H:i:s');
	
	
	//----------------------------------待充----------------------------------------------------
	if($adminWebName == 'admin'){
		$today_s = date('Y-m-d 00:00:00');
		$today_pay_count_0 = $db->fetch_first("select pid from pay_data where status = 0 and success = 0 and dtime >= '$today_s' limit 1");		
	}
?>