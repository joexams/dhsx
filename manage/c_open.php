<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('server_open');
switch (ReqStr('action'))
{
	case 'ServersMerger':ServersMerger();break;
	case 'SetServersMerger':SetServersMerger();break;
	case 'SaveEditServersMerger':SaveEditServersMerger();break;
	
	case 'SetServersOpen':SetServersOpen();break;
	case 'SaveEditServersOpen':SaveEditServersOpen();break;
	default:  ServersOpen();
}
//--------------------------------------------------------------------------------------------合服计划
	
function ServersMerger() 
{
	global $db,$adminWebName,$adminWebCid,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;	
	$cid=ReqStr('cid');
	$odate=ReqStr('odate');
	if($odate)
	{
		$set_date = " and date_format(A.open_date, '%Y-%m-%d') = '$odate'";
	}	
	if($cid)
	{
		$set_cid = " and A.cid = '$cid'";
	}	
	//------------------------------------------------------------------------------------------------	
	$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
	//------------------------------------------------------------------------------------------------	

	$query = $db->query("
	select 
		distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
		count(*) as s_num
	from 
		servers_merger
	where
		cid in ($adminWebCid)
	group by 
		odate	
	order by 
		odate desc
	");
	while($drs = $db->fetch_array($query))
	{
		if($drs['odate'] == date('Y-m-d'))
		{
			$merger_today_count = $drs['s_num'] ? $drs['s_num'] : 0;
		}
		$day_list[]=$drs;
	}	


	//------------------------------------------------------------------------------------------------	


	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_merger A
	where
		A.cid in ($adminWebCid)	
		$set_date	
		$set_cid
	"),0);	
	if($num)
	{		
		$i = 0;	
		$query = $db->query("
		select 
			A.*,
			B.name as company_name,
			C.name as sname
		from 
			servers_merger A
			left join company B on A.cid = B.cid
			left join servers C on A.combined_to = C.sid
		where
			A.cid in ($adminWebCid)
			$set_date
			$set_cid
		order by 
			A.open_date desc,
			A.id desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$sid_m .= $sid_m ? ','.$rs['sid_m'] : $rs['sid_m'];	
			$list_array[] =  $rs;
		}

		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=open&action=ServersMerger&cid=$cid&odate=$odate");	
	}
	
	//---------------------------------------------------------------------
	if($sid_m)
	{
		//---------------------找服务器名-----------------------------
		$query = $db->query("
		select 
			`sid`,	
			`name`,
			`o_name`,
			`combined_to`,
			`is_combined`,
			`open_date`,
			`open_date_old`
		from 
			servers
		where 
			sid in ($sid_m)	
		order by
			`open_date` asc
		");
		if($db->num_rows($query))
		{
			while($srs = $db->fetch_array($query))
			{	
				$s[$srs['sid']] =  $srs;
			}
		}

	}	
		
	$db->close();
	include_once template('c_servers_merger');
}	


//--------------------------------------------------------------------------------------------录入新服
	
function ServersOpen() 
{
	global $db,$adminWebName,$adminWebCid,$page; 

	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$odate=ReqStr('odate');
	$apis=ReqStr('apis');
	$cid=ReqNum('cid');
	if($cid)
	{
		$set_cid = " and A.cid = '$cid'";
	}	
	//------------------------------------------------------------------------------------------------	
	$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	

	//------------------------------------------------------------------------------------------------	
	
	if (!webAdmin('key_power','y')) 
	{
		$query = $db->query("
		select 
			api_server,
			count(sid) as s_num		
		from 
			servers
		where 
			cid in ($adminWebCid)
			and api_server <> ''
			and api_server <> '000' 
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
	//------------------------------------------------------------------------------------------------	
	
	if($odate)
	{
		$set_date = " and date_format(A.open_date, '%Y-%m-%d') = '$odate'";
	}	
	//------------------------------------------------------------------------------------------------	

	$query = $db->query("select combined_to from servers_merger where combined_to > 0");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$sid_c .= $sid_c ? ','.$rs['combined_to'] : $rs['combined_to'];	
		}
		if(!$apis)//不在查询机器配置才显示
		{
			$set_c = " and A.sid not in ($sid_c)";
		}
	}

	
	//--------------------------------------------------------------------------------------------

	$query = $db->query("
	select 
		distinct(date_format(A.open_date, '%Y-%m-%d')) AS odate ,
		count(A.sid) as s_num
	from 
		servers A
	where
		A.cid in ($adminWebCid)
		$set_cid
		$set_c
	group by 
		odate	
	order by 
		odate desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_list[]=$drs;
	}	
	//------------------------------------------------------------------------------------------------	
	$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
	
	$s = $db->fetch_first("
	select 		
		count(case when A.open_date > now() and A.open = 1 then sid end) as wait_open_count,
		count(case when A.open_date > now() and A.open = 0 then sid end) as yd_count,
		count(case when A.open = 1 and A.private = 1 and A.test = 0 and A.open_date <= now() then sid end) as open_count,
		count(case when date_format(A.open_date, '%Y-%m-%d') = curdate() then sid end) as open_today_count
	from 
		servers A
	where
		A.cid in ($adminWebCid)
		$set_c
	
	");
	if($s){
		$wait_open_count = $s['wait_open_count'];//等待开启
		$yd_count = $s['yd_count'];//预定
		$open_count = $s['open_count'];//已开
		$open_today_count = $s['open_today_count'];//今日
		
	}	
	
	
	//------------------------------------------------------------------------------------------------	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers A
	where
		A.cid in ($adminWebCid)	
		$set_cid
		$set_date	
		$set_c
		$set_api
	"),0);	
	if($num)
	{		
		$i = 0;	
		$query = $db->query("
		select 
			A.*,
			B.name company_name
					
		from 
			servers A
			left join company B on A.cid = B.cid
		where
			A.cid in ($adminWebCid)
			$set_date
			$set_c
			$set_api
		order by 
			A.open_date desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['server'] = str_replace(",", "<br />",$rs['server']);	
			$list_array[] =  $rs;
		}

		$list_array_pages = multi($num,$pageNum,$page,"c.php?in=open&apis=$apis");	
	}		
	$db->close();
	include_once template('c_servers_open');
}	



 
//--------------------------------------------------------------------------------------------添加新服务器
function  SetServersOpen() 
{
	global $db,$cid; 
	$cid = trim(ReqNum('cid'));
	$o_name_n = trim(ReqStr('o_name_n'));
	$server_n = trim(ReqStr('server_n'));
	$open_date_n = trim(ReqStr('open_date_n'));
	//-----------------增加记录-------------------------------------------
	if (!$cid || !$o_name_n || !$server_n || !$open_date_n )
	{
		showMsg('ERROR');	
		return;
	}
	$query = $db->query("select server from servers");
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{	
			$serverArrAll = $rs['server'] ? explode(",",$rs['server']) : array();
			for ($i=0;$i<=count($serverArr);$i++){
				if (in_array($server_n,$serverArrAll))
				{
					showMsg($server_n.languagevar('EXIST'));	
					return;
				}
			}
		
		}
	}
	$query = $db->query("
	insert into 
		servers
		(`cid`,`o_name`,`server`,`open_date`,`open`,`private`,`test`,`first_pay_act`,`level_act`,`mission_act`,`repute_act`,`new_card_act`) 
	values 
		('$cid','$o_name_n','$server_n','$open_date_n',0,0,0,0,1,1,0,0)
	") ;
	if($query)
	{
		$msg .= "<br />".languagevar('SETOK');
	}
	else
	{
		$msg .= '<br /><strong class="redtext">'.languagevar('ZJSBMSG').'</strong>';
	}

	insertServersAdminData(0,0,0,'服务器','增加服务器('.$o_name_n.')');//插入操作记录
	$db->close();
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------保存修改服务器
function  SaveEditServersOpen() 
{
	global $db,$cid; 
	$sid = ReqNum('sid');
	$o_name = ReqStr('o_name');
	$server = ReqStr('server');
	$open_date = ReqStr('open_date');
	$first_pay_act = ReqNum('first_pay_act');	
	$level_act = ReqNum('level_act');	
	$mission_act = ReqNum('mission_act');
	$new_card_act = ReqNum('new_card_act');
	

	if (empty($sid) || !$o_name || !$server || !$open_date)
	{
		showMsg("ERROR");		
	}else{
		$rs = $db->fetch_first("select open,open_date from servers where sid = '$sid'");
		if ($rs['open'] == 1 && $rs['open_date'] < date('Y-m-d H:i:s'))
		{
			showMsg("YPZKFWFXG");
			return;	
		}
		$query = $db->query("select server from servers where sid <> '$sid'");
		if($db->num_rows($query))
		{
		
			while($rs = $db->fetch_array($query))
			{	
				$serverArrAll = $rs['server'] ? explode(",",$rs['server']) : array();
				for ($i=0;$i<=count($serverArr);$i++){
					if (in_array($server,$serverArrAll))
					{
						showMsg($server.languagevar('EXIST'));	
						return;
					}
				}
			
			}
		}
		
		$query = $db->query("
		update 
			servers 
		set 
			`o_name`='$o_name',
			`server`='$server',
			`open_date`='$open_date',
			`first_pay_act`='$first_pay_act',
			`level_act`='$level_act',
			`mission_act`='$mission_act',
			`new_card_act`='$new_card_act'		
		where 
			sid = '$sid'
		");
		if ($query)
		{
			$contents = '修改开服列表:服务器(ID:'.$sid.'/名称:'.$o_name.')';
			insertServersAdminData(0,0,0,'服务器',$contents);//插入操作记录		
			showMsg("SETOK",'','','greentext');		
		}else{
			showMsg("SETERR");		
		
		}
	}
	$db->close();
	
}


//--------------------------------------------------------------------------------------------添加合服计划
function  SetServersMerger() 
{
	global $db,$cid,$adminWebID; 
	$cid = trim(ReqNum('cid'));
	$contents = trim(ReqStr('contents'));
	$sid_m = ReqArray('sid_m');
	$open_date = trim(ReqStr('open_date'));
	//-----------------增加记录-------------------------------------------
	if (!$cid || !$sid_m || !$open_date )
	{
		showMsg('ERROR');	
		return;
	}
	$sid_m_arr = implode(",",$sid_m);//组合为字符串
	$query = $db->query("
	insert into 
		servers_merger
		(`cid`,`sid_m`,`contents`,`open_date`,`adminID`,`mdate`) 
	values 
		('$cid','$sid_m_arr','$contents','$open_date','$adminWebID',now())
	") ;

	if($query)
	{
		$msg .= "<br />".languagevar('SETOK');
		//-------------------------------------插入新的合服数据
		
		$query = $db->query("select `name`,`open_date`,`open_date_old` from servers where sid in ($sid_m_arr) order by open_date asc");		
		if($db->num_rows($query))
		{
			while($srs = $db->fetch_array($query))
			{
				$odate[] = $srs['open_date_old'] != '0000-00-00 00:00:00' ? $srs['open_date_old'] : $srs['open_date'];//如果查旧的开服时间判断是否之前合过
				if(strpos($srs['name'],'_',0)){
					$s = explode('_',$srs['name']);
					$ss = strtoupper($s[1]);
				}else{
					$ss = strtoupper($srs['name']);
				}
				$sname .= $sname ? ' + '.$ss : $ss ;
			}
			$open_date_old = min($odate);
		}
		
		
		$mid = $db->insert_id();
		$test_player = count($sid_m)*5;
		$o_name = $sname;
		$query = $db->query("
		insert into 
			servers(
			`cid`,
			`o_name`,
			`open_date`,
			`open`,
			`private`,
			`test`,
			`first_pay_act`,
			`level_act`,
			`mission_act`,
			`new_card_act`,
			`test_player`,
			`is_combined`,
			`open_date_old`
		)values (
			'$cid',
			'$o_name',
			'$open_date',
			'0',
			'0',
			'0',
			'1',
			'0',
			'0',
			'0',
			'$test_player',
			'1',
			'$open_date_old'
		)
		") ;		
		$sid = $db->insert_id();
		$db->query("update servers_merger set `combined_to` = '$sid' where id = '$mid' and combined_to = 0");//更新
	}
	else
	{
		$msg .= '<br /><strong class="redtext">'.languagevar('ZJSBMSG').'</strong>';
	}

	$db->close();
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------保存修改合服计划
function  SaveEditServersMerger() 
{
	global $db,$cid; 
	$id = ReqNum('id');
	$combined_to = ReqNum('combined_to');
	$contents = trim(ReqStr('contents'));
	$sid_m = ReqArray('sid_m');
	$open_date = trim(ReqStr('open_date'));
	

	if (empty($id) || !$sid_m || !$open_date)
	{
		showMsg('ERROR');		
	}else{

		
/*		$rs = $db->fetch_first("select open,open_date from servers where sid = '$combined_to'");
		if ($rs['open'] == 1 && $rs['open_date'] < date('Y-m-d H:i:s'))
		{
			showMsg("YPZKFWFXG");
			return;	
		}*/
		
		//-------------------------------------------------------------------------------------------
		
		$sid_m_arr = implode(",",$sid_m);//组合为字符串
		$query = $db->query("select combined_to,`name`,`open_date`,`open_date_old` from servers where sid in ($sid_m_arr) order by open_date asc");		
		if($db->num_rows($query))
		{
			while($srs = $db->fetch_array($query))
			{
				$odate[] =  $srs['open_date_old'] != '0000-00-00 00:00:00' ? $srs['open_date_old'] : $srs['open_date'];
				if(strpos($srs['name'],'_',0)){
					$s = explode('_',$srs['name']);
					$ss = strtoupper($s[1]);
				}else{
					$ss = strtoupper($srs['name']);
				}
				$sname .= $sname ? ' + '.$ss : $ss ;
				if($srs['combined_to'] > 0) $ism = 1;
			}
			$open_date_old = min($odate);
		}
		
		$test_player = count($sid_m)*5;
		$o_name = $sname;
		//-------------------------------------------------------------------------------------------
		if ($ism)
		{
			showMsg("YPZKFWFXG");
			return;	
		}			
		//-------------------------------------------------------------------------------------------
		
		
		$query = $db->query("
		update 
			servers_merger 
		set 
			`contents`='$contents',
			`open_date`='$open_date',
			`sid_m`='$sid_m_arr'
		where 
			id = '$id'
		");
		if ($query)
		{
			if($combined_to) {
				$query = $db->query("
				update 
					servers 
				set 
					`open_date`='$open_date',
					`o_name`='$o_name',
					`test_player`='$test_player',
					`open_date_old`='$open_date_old',
					`is_combined` = 1
				where 
					sid = '$combined_to'
				");
			}
			showMsg("SETOK",'','','greentext');		
		}else{
			showMsg("SETERR");		
		
		}
	}
	$db->close();
	
}

?> 