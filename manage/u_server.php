<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'ServersAdminData': ServersAdminData();break;
	default:  Servers();
}
//--------------------------------------------------------------------------------------------服务器列表
	
function Servers() 
{
	global $db,$cid,$adminWebCid,$adminWebServers,$adminWebName,$page; 
	
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
	if(!in_array($cid,$adminCidArr))//如果服务器不属于此运营商
	{	
		showMsg('NOCOMPANYPOWERMSG');	
		exit();
	
	}	
	
	if ($adminWebServers) 
	{
		$set_sid = " and A.sid in ($adminWebServers)";
	}	
	//--------------------------------------------------------
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers A
	where 
		A.cid = '$cid'
		$set_sid
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.name2		
		from 
			servers A
			left join servers_address B on A.api_server = B.name
		where 
			A.cid = '$cid'
			$set_sid
		order by 
			A.sid desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			if ($rs["name2"]) $rs["name2"] = '('.languagevar('JIFANG').$rs["name2"].')';
			$list_array[] =  $rs;
		}

		$list_array_pages = multi($num,$pageNum,$page,"u.php?in=server&action=Servers&cid=$cid");	
	}	
	$db->close();
	include_once template('u_servers');
}	
 //--------------------------------------------------------------------------------------------操作记录

function ServersAdminData() {
	global $db,$cid,$adminWebName,$adminWebID,$adminWebCid,$adminWebServers,$page;
	$sid = ReqNum('sid');
	$text = trim(ReqStr('text'));
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
	if(!in_array($cid,$adminCidArr))//如果服务器不属于此运营商
	{	
		showMsg('NOSERVERPOWER');	
		exit();
	
	}	
	

	if ($text) $set_text = "and (A.username like '%$text%' or A.contents like '%$text%')";

	if($sid) {
		$set_sid = "and A.sid = '$sid'";
		
	}else{
		if ($adminWebServers)
		{
			$set_sid = "and A.sid in ($adminWebServers)";
		}
	}
	//----------------------------------------------------------------------------	
	
	if ($adminWebServers)
	{
		$set_sid_c = " and sid in ($adminWebServers)";
	}

	
	$servers_list = globalDataList('servers',"cid = '$cid' $set_sid_c");//服务器
		
	//-----------操作记录----------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_admin_data A 
	where 
		A.adminID = '$adminWebID'
		$set_sid
		$set_text
		or (A.type = 1 and A.cid = '$cid' $set_text $set_sid)
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*
		from 
			servers_admin_data A 
		where 
			A.adminID = '$adminWebID'
			$set_sid
			$set_text
			or (A.type = 1 and A.cid = '$cid' $set_text $set_sid)
		order by
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			$sidArr[] = $rs['sid'];
			$aidArr[] = $rs['adminID'];

			$list_array[] = $rs;
		}
		$text_url = urlencode($text);
		$list_array_pages = multi($num,$pageNum,$page,"u.php?in=server&action=ServersAdminData&text=$text_url&cid=$cid&sid=$sid");	
		
		
		//-------------------------------------------------------------------------------------------------
		if($aidArr)
		{
			$aidArr = array_unique($aidArr);
			$aid_arr = implode(",",$aidArr);
			$query = $db->query("
			select 
				adminID,
				adminName,
				adminType
			from 
				admin
			where 
				adminID in ($aid_arr)
			");
			if($db->num_rows($query))
			{
				while($ars = $db->fetch_array($query))
				{
					$a[$ars['adminID']] = $ars;
				}
			}
		}				
		
		if($sidArr)
		{
			$sidArr = array_unique($sidArr);
			$sid_arr = implode(",",$sidArr);
			$query = $db->query("
			select 
				A.sid,
				A.name as servers_name
			from 
				servers A
			where 
				A.sid in ($sid_arr)
			");
			if($db->num_rows($query))
			{
				while($srs = $db->fetch_array($query))
				{
					$s[$srs['sid']] = $srs;
				}
			}
		}			
				
		
		
	}	
	$db->close();
	include_once template('u_servers_admin_data');
}
?> 