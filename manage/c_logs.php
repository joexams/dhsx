<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('logs');
switch (ReqStr('action'))
{
	default:  Logs();
}
 //--------------------------------------------------------------------------------------------操作记录

function Logs() {
	global $db,$adminWebName,$adminWebCid,$page;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$adminame = trim(ReqStr('adminame'));
	$text = trim(ReqStr('text'));
	if ($text) 
	{
		$set_text = "and (A.username like '%$text%' or A.contents like '%$text%')";
	}		
	if($cid)
	{
		$set_cid = "and B.cid = '$cid'";
	}else{
		$set_cid = "and B.cid in ($adminWebCid)";
	}
	if($sid)
	{
		$set_sid = "and B.sid = '$sid'";
	}	
	if($adminame)
	{
		$set_adminame = "and D.adminName = '$adminame'";
	}
	//----------------------------------------------------------------------------	
	
	$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
	$servers_list = globalDataList('servers',"cid = '$cid'");//服务器
	//---------------------------------所有客服ID-------------------------------------------	
/*	if (strstr($adminWebCid,','))
	{
		$set_cid_admin  = "and FIND_IN_SET('$cid',cid) <> 0";
	}else{
		$set_cid_admin  = "and cid = '$cid'";
	}
	$query = $db->query("
	select 
		adminID,
		adminName,
		adminType
	from 
		admin
	where 
		adminType <> 's'
		$set_cid_admin
	order by 
		adminType asc,			
		adminID asc			
	");		
	if($db->num_rows($query))
	{	
		while($ars = $db->fetch_array($query))
		{			
			$admin_id_array[] = $ars['adminID'];
		}	
	}else{
		$admin_id_array = array();;
	}		
	$admin_id_arr = implode(",",$admin_id_array);//组合为字符串	*/	
	//-----------操作记录----------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_admin_data A 
		left join servers B on A.sid = B.sid
		left join company C on B.cid = C.cid
		left join admin D on A.adminID = D.adminID
	where 
		A.adminID > 0
		and D.adminType <> 's'
		$set_adminame
		$set_sid
		$set_cid
		$set_text
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*,
			B.name as servers_name,
			C.name as company_name,
			D.adminName,
			D.adminType
		from 
			servers_admin_data A 
			left join servers B on A.sid = B.sid
			left join company C on B.cid = C.cid
			left join admin D on A.adminID = D.adminID
		where 
			A.adminID > 0
			and D.adminType <> 's'
			$set_adminame
			$set_sid
			$set_cid
			$set_text
		order by
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			$list_array[] = $rs;
		}
		$text_url = urlencode($text);
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=logs&text=$text_url&cid=$cid&sid=$sid&adminame=$adminame");	
	}	
	$db->close();
	include_once template('c_logs');
}
?> 