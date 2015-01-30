<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	
	case 'ServersAdminData': ServersAdminData();break;
	case 'SaveServers': SaveServers();break;
	default:  Servers();
}

//--------------------------------------------------------------------------------------------服务器列表
	
function Servers() 
{
	global $db,$cid,$adminWebCid,$adminWebName,$page; 
	include_once(UCTIME_ROOT."/online_data.php");
	$apis=ReqStr('apis');
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	if (!webAdmin('key_power','y')) 
	{
		//$servers_address_api_list = globalDataList('servers_address','type = 0','name asc');//API地址
		$query = $db->query("
		select 
			api_server,
			count(*) as s_num		
		from 
			servers
		where 
			cid = '$cid'
			and  api_server <> ''
		group by 
			api_server asc		
		");
		while($rs = $db->fetch_array($query))
		{	
			$servers_address_api_list[] =  $rs;
		}	
		if($apis)
		{
			$set_api = " and A.api_server = '$apis'";
		}		
	}

	//--------------------------------------------------------
	$query = $db->query("
	select 
		distinct(name) as name,
		name2	
	from 
		servers_address
	where
		type = 0
	group by 
		name asc		
	");
	while($rs = $db->fetch_array($query))
	{	
		$apiArr[$rs['name']] =  $rs['name2'];
	}		
	//--------------------------------------------------------
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers A
	where 
		A.cid = '$cid'
		$set_api
	"),0);	
	if($num)
	{	
		$i = 0;	
		$query = $db->query("
		select 
			A.*
		from 
			servers A
		where 
			A.cid = '$cid'
			$set_api
		order by 
			A.open_date desc,
			A.sid desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['i'] = $i++;
			if ($rs['open']) $sidarr[] = $rs['sid'];
			//if ($rs["name2"]) $rs["name2"] = '('.languagevar('JIFANG').$rs["name2"].')';
			$list_array[] =  $rs;
		}
		$sid_arr = $sidarr ? implode(",",$sidarr): '';
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=server&action=Servers&cid=$cid&apis=$apis");	
	}	
	//-----------------------------------------------------------------------------
	if ($sid_arr)
	{
		$yesterday = date('Y-m-d',time()-86400);//昨天数据
		$today = date('Y-m-d 00:00:00');//昨天数据
	
		$query = $db->query("
		select 
			sid,
			pay_amount	as yesterday_amount	
		from 
			game_data
		where 
			sid in ($sid_arr)
			AND gdate = '$yesterday'
		");
		while($prs = $db->fetch_array($query))
		{	
			$pay[$prs['sid']] =  $prs;
		}
		//------------------------------------------------------------------
		$query = $db->query("
		select 
			sid,
			sum(amount)	as today_amount	
		from 
			pay_data
		where 
			sid in ($sid_arr)
			and dtime >= '$today'
			and status = 0	
			and success = 1
		group by
			sid
		");
		while($prs = $db->fetch_array($query))
		{	
			$prs['today_amount'] = round($prs['today_amount'],2);
			$pat[$prs['sid']] =  $prs;
		}
		
		
	}

	
	
	
	$db->close();
	include_once template('c_servers');
}	

 //--------------------------------------------------------------------------------------------操作记录

function ServersAdminData() {
	global $db,$cid,$adminWebName,$adminWebCid,$page;
	$sid = ReqNum('sid');
	$aid = ReqNum('aid');
	$player_id = ReqNum('player_id');
	$text = trim(ReqStr('text'));
	if (strstr($adminWebCid,','))
	{
		$set_cid_admin  = "and FIND_IN_SET('$cid',cid) <> 0";
	}else{
		$set_cid_admin  = "and cid = '$cid'";
	}

	if ($text) 
	{
		$set_text = "and (A.username like '%$text%' or A.contents like '%$text%')";
	}	
	if ($player_id) 
	{
		$set_player = "and A.player_id ='$player_id'";
	}	
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}else{
		$set_cid = "and A.cid in ($adminWebCid)";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if($aid)
	{
		$set_aid = "and A.adminID = '$aid'";
	}
	//---------------------------------所有客服ID-------------------------------------------	
	$query = $db->query("
	select 
		adminID,
		adminName,
		adminType
	from 
		admin
	where 
		(adminType = 'u'
		or adminType = 'c')
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
			$admin_array[] = $ars;
		}	
	}else{
		$admin_id_array = array();;
	}
	if($aid && !in_array($aid,$admin_id_array)){//如果不属于此运营商
		showMsg('NOPOWER','','web','','','n');	
		exit();
	
	}		
	$admin_id_arr = implode(",",$admin_id_array);//组合为字符串
	//----------------------------------------------------------------------------	
	
	$servers_list = globalDataList('servers',"cid = '$cid'");//服务器
		
	//-----------操作记录----------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_admin_data A
	where 
		A.adminID in ($admin_id_arr)
		$set_player
		$set_aid
		$set_sid
		$set_cid
		$set_text
		or (A.type = 1 $set_cid $set_text $set_aid $set_player $set_sid)
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*
		from 
			servers_admin_data A 
		where 
			A.adminID in ($admin_id_arr)
			$set_player
			$set_aid
			$set_sid
			$set_cid
			$set_text
			or (A.type = 1 $set_cid $set_text $set_aid $set_player $set_sid)
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
		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=server&action=ServersAdminData&text=$text_url&cid=$cid&sid=$sid&aid=$aid");	
		
		
		
		
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
				A.name as servers_name,
				B.name as company_name
			from 
				servers A
				left join company B on A.cid = B.cid
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
	include_once template('c_servers_admin_data');
}

?> 