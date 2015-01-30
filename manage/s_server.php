<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'WarZone':webAdmin('war_zone'); WarZone();break;
	case 'WarZoneSelect':webAdmin('war_zone'); WarZoneSelect();break;
	case 'ServersPost':webAdmin('s_post'); ServersPost();break;
	case 'ServersOpen':webAdmin('s_post'); ServersOpen();break;
	case 'ServersMerger':webAdmin('s_post');ServersMerger();break;
	case 'DelServersMerger':webAdmin('server');DelServersMerger();break;
	case 'SetCombinedTo':webAdmin('server');SetCombinedTo();break;
	case 'SqlLog':webAdmin('server');SqlLog();break;
	case 'RenewLog':RenewLog();break;
	case 'DelSqlLog':webAdmin('server');DelSqlLog();break;
	case 'SetRenewLog': webAdmin('renew_log');SetRenewLog();break;

	case 'Company': webAdmin('company');Company();break;
	case 'CompanyView':webAdmin('company');CompanyView();break;
	
	case 'Servers':webAdmin('server');Servers();break;
	case 'ServersDbTransfer':webAdmin('server');ServersDbTransfer();break;
	case 'ServersAddress': webAdmin('server');ServersAddress();break;
	case 'ServersAdminData': webAdmin('log');ServersAdminData();break;
	case 'SetServersDbTransfer':webAdmin('server'); SetServersDbTransfer();break;
	case 'SetServers': webAdmin('server');SetServers();break;
	case 'SetServersAddress': webAdmin('server');SetServersAddress();break;
	case 'SaveServers': webAdmin('server');SaveServers();break;
	case 'SaveServersAdd': webAdmin('server');SaveServersAdd();break;
	case 'SetUpdateMerger':webAdmin('server'); SetUpdateMerger();break;
	case 'SetCompany': webAdmin('company');SetCompany();break;
	case 'SaveCompany': webAdmin('company');SaveCompany();break;
	case 'DelServersAdminData': webAdmin('log');DelServersAdminData();break;
	case 'SetWarZone':webAdmin('war_zone'); SetWarZone();break;
	case 'SetWarZoneSelect':webAdmin('war_zone'); SetWarZoneSelect();break;
	default:  Main();
}
function GetPurMonth($date){//获取指定日期上个月的第一天和最后一天
	$time=strtotime($date);
	$firstday=date('Y-m-01',strtotime(date('Y',$time).'-'.(date('m',$time)-1).'-01'));
	$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
	return array($firstday,$lastday);
}
function  Main() {
	global $db,$adminWebName; 
	@include_once(UCTIME_ROOT."/online_data.php");
	$today = date('Y-m-d');;
	$last_day = date("Y-m-d",strtotime("-1 day"));
	$day = date("Y-m-d 00:00:00",strtotime("-7 day"));
	$dayunix = strtotime($day);
	$dayy = date("Y-m-d 23:59:59",strtotime("-1 day"));
	$now = date("H:i:s");
	$sdate = date('Y-m-d 00:00:00');
	$filename = "sxd_data_count.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$mobile = 164;//手机平台CID

	if ($is_update) 
	{
	
		///----------------------------------最高在线----------------------------------------------------
		$max = $db->fetch_first("select * from max_online");
		///----------------------------------总注册----------------------------------------------------
		$player_reg = $db->fetch_first("
		select 
			sum(A.register_count) as register_count,
			sum(A.create_count) as create_count
		from 
			game_data A
			left join company B on A.cid = B.cid
		where 
			B.slug not in ('1wan','test')
			and B.cid <> '$mobile'
		");
	
		///----------------------------------当月总收入----------------------------------------------------
		
		
		$yesterday = date('Y-m-d',time()-86400);//昨天
		$thismonth = date('Y-m-01');//月头
		$lm = GetPurMonth($thismonth);
		$lastmonths = $lm[0];//上个月头
		$lastmonthe = $lm[1];//上个月尾
		$paylog = $db->fetch_first("
		select 
			sum(if(gdate <= '$yesterday',pay_amount,0)) as amount_all,
			sum(if(gdate = '$yesterday',pay_amount,0)) as amount_yesterday,
			sum(if(gdate >= '$thismonth' and gdate <= '$yesterday' ,pay_amount,0)) as amount_thismonth,
			sum(if(gdate >= '$lastmonths' and gdate <= '$lastmonthe' ,pay_amount,0)) as amount_lastmonth
		from 
			game_data
		where 
			cid <> '$mobile'
		");	
		
			
		///----------------------------------服务器----------------------------------------------------
		$company_count = $db->result($db->query("select count(*) from company where slug not in ('1wan','test') and cid <> '$mobile'"),0);
		
		///----------------------------------昨日登陆----------------------------------------------------
		$last_login = $db->result_first("
		select 
			sum(A.login_count)
		from 
			game_data A
			left join company B on A.cid = B.cid
		where 
			B.slug not in ('1wan','test')
			and A.gdate = '$last_day'
			and B.cid <> '$mobile'
		");
		$last_login = $last_login ? $last_login : 0;
		///----------------------------------服务器----------------------------------------------------
		$s = $db->fetch_first("
		select 		
			count(case when is_combined = 0 and date_format(open_date, '%Y-%m-%d') = curdate() then sid end) as open_today_count,
			count(case when is_combined = 0 and open = 1 and test = 0 and open_date <= now() then sid end) as open_count,
			count(case when is_combined = 1 and date_format(open_date, '%Y-%m-%d') = curdate() then sid end) as merger_today_count,
			count(case when is_combined = 1 and open_date <= now() then sid end) as merger_count
		from 
			servers
		where 
			cid <> '$mobile'		
		");
		
/*		///----------------------------------合服的服务器----------------------------------------------------
		$m = $db->fetch_first("
		select 		
			count(case when date_format(open_date, '%Y-%m-%d') = curdate() then id end) as open_today_count,
			count(case when open_date <= now() then id end) as open_count
		from 
			servers_merger
		where 
			cid <> '$mobile'
		");
*/		
		///----------------------------------全日充值----------------------------------------------------
		$query = $db->query("
		select 
			gdate,
			sum(pay_amount) as pay_amount
		from 
			game_data
		where 
			gdate >= '$day' 
			and gdate < curdate()
			and cid <> '$mobile'
		group by 
			gdate				
		");
		while($grs = $db->fetch_array($query))
		{
			$grs['pay_amount'] = round($grs['pay_amount'],2);
			$payall[$grs['gdate']] = $grs;
		}
		///----------------------------------全日抵扣券----------------------------------------------------
		$query = $db->query("
		select 
			from_unixtime(dateline,'%Y-%m-%d') as pdate,
			sum(payamt_coins+pubacct_payamt_coins+goldcoupon+slivercoupon+coppercoupon) as payamt_amount
		from 
			pay_data_detail
		where 
			dateline >= '$dayunix' 
			and from_unixtime(dateline,'%Y-%m-%d') < curdate()
		group by 
			pdate				
		");
		while($prs = $db->fetch_array($query))
		{
			$prs['payamt_amount'] = round($prs['payamt_amount']/10,2);
			$payamt[$prs['pdate']] = $prs;
		}		
		
		
		
		
		///----------------------------------今日单笔充值----------------------------------------------------
		$today_order_pay = 0;
		$today_order_array = array();
		$query = $db->query("
		select 
			A.cid,
			A.sid,
			A.player_id,
			sum(A.amount) as amount,
			count(A.player_id) as pay_num,
			A.username,
			A.nickname,
			B.name
		from 
			pay_data A
			left join servers B on A.sid = B.sid
		where 
			A.dtime >= '$sdate'
			and A.success = 1
			and A.status = 0				
		group by 
			sid,player_id
		order by 
			amount desc

		limit 20			
		");
		if($db->num_rows($query))
		{
			$i=1;
			
			while($prs = $db->fetch_array($query))
			{
				$prs['i'] = $i++;
				$today_order_pay += $prs['amount'];
				$prs['amount'] = round($prs['amount'],2);
				$today_order_array[] = $prs;
			}
			$today_order_pay = round($today_order_pay,2);
		}
		
		
		///----------------------------------今日单服充值排行----------------------------------------------------
		$servers_order_array = array();
		$query = $db->query("
		select 
			A.cid,
			A.sid,
			sum(A.amount) as pay_amount,
			count(distinct(A.player_id)) as pay_user,
			B.name,
			B.o_name
		from 
			pay_data A
			left join servers B on A.sid = B.sid
		where 
			A.dtime >= '$sdate'
			and A.success = 1
			and A.status = 0				
		group by
			sid
		order by 
			pay_amount desc
		limit 20			
		");
		if($db->num_rows($query))
		{
			$i=1;
			while($srs = $db->fetch_array($query))
			{
				
				$srs['i'] = $i++;
				$srs['pay_amount'] = round($srs['pay_amount'],2);
				$servers_order_array[] = $srs;
			}
		}
		
					
	}
	
	
	
	///----------------------------------当日总收入----------------------------------------------------
	$sdate_unix = strtotime(date('Y-m-d'));
	$t = $db->fetch_first("
	select 
		sum(amount) as amount_today,
		count(distinct(username)) as amount_today_user
	from 
		pay_data
	where 
		dtime_unix >= '$sdate_unix'
		and status = 0	
		and success = 1	
		and cid <> '$mobile'
	");		
	
	///----------------------------------7日时段充值数据----------------------------------------------------

	$datesArr = prDates(date("Y-m-d",strtotime("-7 day")),date("Y-m-d",strtotime("-1 day")));
	foreach ($datesArr as $drs => $day){
		$now_s = strtotime($day." 00:00:00");
		$now_e = strtotime($day." ".$now);
		$rs	= $db->fetch_first("select sum(amount) as pay_hour_amount,count(distinct(username)) as pay_hour_user from pay_data where dtime_unix >= '$now_s' and dtime_unix <= '$now_e' and status = 0 and success = 1 and cid <> '$mobile'");	
		$rs['pay_hour_amount'] = round($rs['pay_hour_amount'],2);
		$rs['pay_hour_user'] = round($rs['pay_hour_user'],2);
		//$rs['pay_hour_user']	= round($db->result_first("select count(distinct(username)) from pay_data where dtime_unix >= '$now_s' and dtime_unix <= '$now_e' and status = 0 and success = 1"),2);
		$dayhis[$day]  = $rs;
	}
	//-------------------------------------------------------------------------------------------		

	if($max){
		$max_online = $max['max_online'];
		$max_online_time = $max['max_online_time'];
	}
	if($s){
		$open_today_count = $s['open_today_count'];//今日
		$open_count = $s['open_count'];//已开
		$merger_today_count = $s['merger_today_count'];//今日合服
		$merger_count = $s['merger_count'];//已开合服
		
	}	

	if($t){
		$amount_today = round($t['amount_today'],1);//当天
		$amount_today_user = $t['amount_today_user'];//当天充值人数
	}else{
		$amount_today = 0;//当天
		$amount_today_user = 0;//当天
	}
	if($paylog){
		$amount_all = round($paylog['amount_all']+$amount_today,1);//所有收入不包括今日
		$amount_yesterday = round($paylog['amount_yesterday'],1);//昨天
		$amount_thismonth = round($paylog['amount_thismonth']+$amount_today,1);//当月不包括今日
		$amount_lastmonth = round($paylog['amount_lastmonth'],1);//上月		

	}else{
		$amount_all = 0;
		$amount_yesterday = 0;
		$amount_thismonth = 0;
		$amount_lastmonth = 0;		
	}
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
	
		$str = '$company_count='.$company_count.";\n"; 
		$str .= '$last_login='.$last_login.";\n"; 
		$str .= '$today_order_pay='.$today_order_pay.";\n"; 
		$str .= '$player_reg='.var_export($player_reg, TRUE).";\n"; 
		$str .= '$paylog='.var_export($paylog, TRUE).";\n"; 
		$str .= '$max='.var_export($max, TRUE).";\n"; 
		$str .= '$s='.var_export($s, TRUE).";\n";
		$str .= '$payall='.var_export($payall, TRUE).";\n";
		$str .= '$payamt='.var_export($payamt, TRUE).";\n";
		$str .= '$today_order_array='.var_export($today_order_array, TRUE).";\n";
		$str .= '$servers_order_array='.var_export($servers_order_array, TRUE).";\n";
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		

	
	$db->close();
	include template('s_main');
}
//--------------------------------------------------------------------------------------------运营商列表
	
function Company() 
{
	global $db; 
	$name=ReqStr('name');
	$web=ReqStr('web');
	$type=ReqNum('type');

	if($name)
	{
		$set_name = " and `name` like '%$name%'";
	}
	if($web)
	{
		$set_web = " and `web` like '%$web%'";
	}
	if($type)
	{
		$set_type = " and `type` = '$type'";
	}	
	
	$query = $db->query("select * from company where cid > 0 $set_name $set_web $set_type order by corder asc,cid asc");		
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$list_array[] = $rs;
		}
	}
	$db->close();
	include_once template('s_company');
}
//--------------------------------------------------------------------------------------------运营商查看
	
function CompanyView() 
{
	global $db; 
	$cid=ReqNum('cid');

	$query = $db->query("select * from company where cid = '$cid'");		
	if($db->num_rows($query))
	{
		$rs = $db->fetch_array($query);
		
		//-------------------------------------------------------------------------------------------------
		
		$query = $db->query("
		select 
			A.*,
			A.name as company_name
		from 
			servers A
		where 
			A.cid = '$cid' 
		order by 
			A.sid asc
		");		
		if($db->num_rows($query))
		{
			while($srs = $db->fetch_array($query))
			{
				$servers_list_array[] = $srs;
			}
			if ($servers_list_array) $srows = array_chunk($servers_list_array,5); 	
			
		}
		
		//-------------------------------------------------------------------------------------------------
		
		$query = $db->query("select * from admin where cid in ($cid) order by adminID asc");		
		if($db->num_rows($query))
		{
			while($ars = $db->fetch_array($query))
			{
				$admin_list_array[] = $ars;
			}
			if ($admin_list_array) $arows = array_chunk($admin_list_array,5); 	
		}		
		//-------------------------------------------------------------------------------------------------
		
		
	}else{
		showMsg('无此运营商！');	
		return;		
	}
	$db->close();
	include_once template('s_company_view');
}
//--------------------------------------------------------------------------------------------服务器数据库变更
	
function ServersDbTransfer() 
{
	global $db; 
	//$servers_address_db_list = globalDataList('servers_address','type = 1','name asc');//DB地址
	$query = $db->query("select A.name,(select count(*)  from servers B where A.name = B.db_server) as num from servers_address A where A.type = 1");
	if($db->num_rows($query))
	{
		while($ars = $db->fetch_array($query))
		{
			$servers_address_db_list[] = $ars;
		}
	}		

	$db->close();
	include_once template('s_servers_db_transfer');
	

}	
//--------------------------------------------------------------------------------------------服务器设置
	
function Servers() 
{
	global $db,$adminWebName,$page; 
	$apis=ReqStr('apis');
	$dbs=ReqStr('dbs');
	$vers=ReqStr('vers');
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$combined_to=ReqNum('combined_to');
	if(!$cid && !$sid && !$apis && !$dbs && !$vers && !$combined_to)
	{
		$cid = $db->result_first("select cid from company order by corder asc limit 1");
		$set_cid = " and cid = '$cid'";
	}
	if($cid && !$sid && !$apis && !$dbs && !$vers && !$combined_to)
	{
		$set_cid = " and cid = '$cid'";
	}
	if($apis)
	{
		$set_api = " and api_server = '$apis'";
	}	
	if($dbs)
	{
		$set_db = " and db_server = '$dbs'";
	}	
	if($vers)
	{
		$set_ver = " and server_ver = '$vers'";
	}
	if($sid)
	{
		$set_sid = " and sid = '$sid'";
	}
	if($combined_to)
	{
		$set_combined_to = " and combined_to > 0";
	}			
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商
	$servers_address_api_list = globalDataList('servers_address','type = 0','name asc');//API地址
	$servers_address_db_list = globalDataList('servers_address','type = 1','name asc');//DB地址
	$servers_address_ver_list = globalDataList('servers_address','type = 2','name asc');//VER地址
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers
	where 
		sid > 0
		$set_cid
		$set_sid
		$set_api
		$set_db	
		$set_ver
		$set_combined_to	
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			servers
		where 
			sid > 0
			$set_cid
			$set_sid
			$set_api
			$set_db
			$set_ver
			$set_combined_to
		order by 
			open_date desc,
			sid desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['server'] = str_replace(",", "\n",$rs['server']);	
			$rs['db_pwd'] = htmlspecialchars($rs['db_pwd']);
			$rs['db_pwd2'] = htmlspecialchars($rs['db_pwd2']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=Servers&cid=$cid&apis=$apis&dbs=$dbs&vers=$vers&combined_to=$combined_to");	
	}	
	$db->close();
	include_once template('s_servers');
}	

//--------------------------------------------------------------------------------------------机器地址设置
	
function ServersAddress() 
{
	global $db,$adminWebName,$page; 
	$type=ReqNum('type');

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_address
	where 
		type = '$type'
	"),0);	
	if($num)
	{			


		$query = $db->query("
		select 
			*
		from 
			servers_address
		where 
			type = '$type'
		order by 
			name asc
		limit 
			$start_num,$pageNum			
					
		");
		if($db->num_rows($query))
		{				
		
			while($rs = $db->fetch_array($query))
			{	
				if($type == 2){
					
					if($rs['name2'] == 1 && !file_exists(UCTIME_ROOT.'/mod/'.$rs['name'].'/api_admin.class.php')) $rs['no_ver'] = '<strong class="redtext">版本不存在！</strong>';
				}		
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=ServersAddress&type=$type");	
		}
	}
	if($type == 0)
	{				
		$add_name = "api_server";
	}elseif($type == 1){
		$add_name = "db_server";
	}elseif($type == 2){
		$add_name = "server_ver";
	}
	$query = $db->query("
	select 
		$add_name,
		count(*) as s_count	
	from 
		servers
	group by
		$add_name
	");
	while($srs = $db->fetch_array($query))
	{	
		$s[$srs[$add_name]] =  $srs;
	}
		
	$db->close();
	include_once template('s_servers_address');
}	

//--------------------------------------------------------------------------------------------战区设置
	
function WarZone() 
{
	global $db,$adminWebName; 
	$query = $db->query("
	select 
		*
	from 
		war_zone
	order by 
		id asc		
	");
	if($db->num_rows($query))
	{				
	
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}
		
	$db->close();
	include_once template('s_war_zone');
}	
//--------------------------------------------------------------------------------------------战区设置
	
function WarZoneSelect() 
{
	global $db; 
	$zone=ReqNum('zone');
	if($zone == 999999)
	{
		$set_zone = " and A.zone = ''";
	}elseif($zone)
	{
		$set_zone = " and A.zone = '$zone'";
	}	
	$war_zone_list = globalDataList('war_zone');
	
	$query = $db->query("
	select 
		A.*,
		B.name as zone_name 
	from 
		company A 
		left join war_zone B ON A.zone = B.id
	where 
		A.cid > 0 
		$set_zone 
	order by 
		A.corder asc,
		A.cid asc
	");		
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$list_array[] = $rs;
		}
	}
	$db->close();
	include_once template('s_war_zone_select');
}
//--------------------------------------------------------------------------------------------服务器列表
	
function ServersPost() 
{
	global $db,$adminWebName,$page; 
	include_once(UCTIME_ROOT."/online_data.php");
	$apis=ReqStr('apis');
	$dbs=ReqStr('dbs');

	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$sname=trim(ReqStr('sname'));
	//$company_list = globalDataList('company','','corder asc,cid asc');//运营商
	$servers_address_api_list = globalDataList('servers_address','type = 0','name asc');//API地址
	$servers_address_db_list = globalDataList('servers_address','type = 1','name asc');//DB地址	
	
	
	$query = $db->query("
	select 
		A.cid,
		A.name,
		sum(if(B.gdate = DATE_SUB(curdate(), INTERVAL 1 DAY),B.pay_amount,0)) as yesterday_amount
	from 
		company A
		left join game_data B on A.cid = B.cid
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
	
	
	if($apis)
	{
		$set_api = " and A.api_server = '$apis'";
	}
	if($dbs)
	{
		$set_db = " and A.db_server = '$dbs'";
	}
	if($sid)
	{
		$set_sid = " and A.sid = '$sid'";
	}
	if(!$apis && !$dbs && !$sid)
	{
		$open_today_count = $db->result($db->query("select count(*) from  servers where date_format(open_date, '%Y-%m-%d') = curdate()"),0);
	
	}	

	if($sname)
	{
		$ssname = 'qq_s'.$sname;
		$set_sname = " and A.name = '$ssname'";
	}	

	if(!$cid && !$sid && !$sname && !$apis && !$dbs && $open_today_count)
	{
		$set_cid = "and date_format(A.open_date, '%Y-%m-%d') = curdate()";
		$set_order = "A.open_date desc,";
	}elseif(!$cid && !$sid && !$sname && !$apis && !$dbs){
		$cid = $db->result_first("select cid from company order by corder asc limit 1");
		$set_cid = "and A.cid = '$cid'";
	}elseif(!$sid && !$sname && !$apis && !$dbs){
		$set_cid = "and A.cid = '$cid'";
	}
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers A
	where 
		A.sid > 0
		$set_cid
		$set_sid
		$set_api
		$set_db
		$set_sname
	"),0);	
	if($num)
	{		
		$i = 0;	
		$query = $db->query("
		select 
			A.*,
			B.name2		
		from 
			servers A
			left join servers_address B on A.db_server = B.name and B.type = 1
		where 
			A.sid > 0
			$set_cid
			$set_sid
			$set_api
			$set_db	
			$set_sname		
		order by 
			$set_order
			A.open_date desc,
			A.sid desc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['i'] = $i++;
			
			if ($rs['test'] == 0 && $rs['open'] && $rs['open_date'] <= date('Y-m-d H:i:s') && $rs['combined_to'] == 0) {
				$sidarr[] = $rs['sid'];
			}
			//if ($rs['test'] == 0 && $rs['open'] && $rs['open_date'] <= date('Y-m-d H:i:s')) $sidarr[] = $rs['sid'];
			$rs['server'] = str_replace(",", "<br />",$rs['server']);	
			
			//$api_server = explode('.',$rs['api_server']);
			//$rs['api_server'] = $api_server[0];
			
			//$db_server = explode('.',$rs['db_server']);
			//$rs['db_server'] = $db_server[0];			
			
			//$name2 = explode('.',$rs['name2']);
			//$rs['name2'] = $name2[0];			

			if ($rs['is_combined']) {
				$server = $db->fetch_first("select * from servers where combined_to='".$rs['sid']."' order by open_date asc");
				$rs['logserver'] = str_replace(",", "<br />",$server['server']);	
			}
			
			
			$list_array[] =  $rs;
		}
		$sid_arr = $sidarr ? implode(",",$sidarr): '';
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=ServersPost&cid=$cid&apis=$apis&dbs=$dbs");	
	}		
	//-----------------------------------------------------------------------------
	if ($sid_arr)
	{
		$today = date('Y-m-d 00:00:00');//昨天数据
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
	include_once template('s_servers_post');
}	


//--------------------------------------------------------------------------------------------合服计划
	
function ServersMerger() 
{
	global $db,$adminWebName,$adminWebCid,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;	
	$cid=ReqStr('cid');
	$odate=ReqStr('odate');
	$combined_to=ReqNum('combined_to');
	if($odate)
	{
		$set_date = " and date_format(A.open_date, '%Y-%m-%d') = '$odate'";
	}	
	if($cid)
	{
		$set_cid = " and A.cid = '$cid'";
	}	
	if($combined_to == 1)
	{
		$set_combined = " and D.name <> ''";
	}elseif($combined_to == 2)
	{
		$set_combined = " and (A.combined_to = '' or D.name = '')";
	}
	//------------------------------------------------------------------------------------------------	
	$company_list = globalDataList('company',"",'corder asc,cid asc');//运营商	

	//------------------------------------------------------------------------------------------------	
	$c = $db->fetch_first("
	select 
		count(case when B.name <> '' then id end) as combined_count,
		count(case when A.combined_to = '' or B.name = '' then id end) as combinedn_count
	from 
		servers_merger A
		left join servers B on A.combined_to = B.sid

	");	
	//------------------------------------------------------------------------------------------------	
	$query = $db->query("
	select 
		distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
		count(*) as s_num
	from 
		servers_merger
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
		left join company B on A.cid = B.cid
		left join admin C on A.adminID = C.adminID
		left join servers D on A.combined_to = D.sid
	where
		id > 0
		$set_date
		$set_cid
		$set_combined
		
	"),0);	
	if($num)
	{		
		$i = 0;	
		$query = $db->query("
		select 
			A.*,
			B.name as company_name,
			C.adminName,
			D.name as sname,
			D.open_date_old,
			D.api_port as newserver_port
		from 
			servers_merger A
			left join company B on A.cid = B.cid
			left join admin C on A.adminID = C.adminID
			left join servers D on A.combined_to = D.sid
		where
			id > 0
			$set_date
			$set_cid
			$set_combined
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

		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=ServersMerger&cid=$cid&odate=$odate");	
	}	
	
	//---------------------------------------------------------------------
	if($sid_m)
	{
		//---------------------找服务器名-----------------------------
		$query = $db->query("
		select 
			A.sid,	
			A.name,	
			A.o_name,	
			A.combined_to,
			A.is_combined,
			A.open_date,	
			A.open_date_old,
			A.server,	
			A.api_server,	
			A.db_server,
			B.name2
		from 
			servers A
			left join servers_address B on A.db_server = B.name and B.type = 1
		where 
			A.sid in ($sid_m)	
		order by
			A.open_date asc
		");
		if($db->num_rows($query))
		{
			while($srs = $db->fetch_array($query))
			{	
				$api_server = explode('.',$srs['api_server']);
				$srs['api_server'] = $api_server[0];
				
				$db_server = explode('.',$srs['db_server']);
				$srs['db_server'] = $db_server[0];				
				
				$name2 = explode('.',$srs['name2']);
				$srs['name2'] = $name2[0];				
			
			
				$s[$srs['sid']] =  $srs;
			}
		}

	}	
		
	$db->close();
	include_once template('servers_merger');
}	

//--------------------------------------------------------------------------------------------版本更新日志

function RenewLog() {
	global $odb,$adminWebType,$adminWebCid,$page;
	
				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $odb->result($odb->query("
	select 
		count(*) 
	from 
		renew_log
	"),0); //获得总条数
	if($num){	
		$query = $odb->query("
		select 
			*
		from 
			renew_log 
		order by
			`date` desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $odb->fetch_array($query)){	
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=RenewLog");	
	}	


	$odb->close();

		
	include_once template('renew_log');
}

//--------------------------------------------------------------------------------------------SQL错误日志

function SqlLog() {
	global $odb,$adminWebType,$adminWebCid,$page;
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	
	
	if($stime && $etime)
	{
		$stime_s = strtotime($stime.' 00:00:00');
		$etime_s = strtotime($etime.' 23:59:59');

		$set_time = "and `time` >= '$stime_s' and `time` <= '$etime_s'";
	}

	
				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $odb->result($odb->query("
	select 
		count(*) 
	from 
		sql_log
	where 
		id > 0 
		$set_time
	"),0); //获得总条数
	if($num){	
		$query = $odb->query("
		select 
			*
		from 
			sql_log 
		where 
			id > 0
			$set_time
		order by
			id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $odb->fetch_array($query)){	
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=SqlLog&stime=$stime&etime=$etime");	
	}	


	$odb->close();

		
	include_once template('sql_log');
}
 //--------------------------------------------------------------------------------------------删除SQL错误日志
function  DelSqlLog() 
{
	global $odb,$adminWebID; 
	$id_del = ReqArray('id_del');
	if (!$id_del)
	{
		showMsg('错误参数！');
		return;		
	}else{
		$id_arr = implode(",",$id_del);
		$odb->query("delete from sql_log where id in ($id_arr) ");
		showMsg('删除成功！',"",'','greentext');	
		return;		
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------删除物品申请
function  DelServersMerger() 
{
	global $db,$adminWebID; 
	$id = ReqNum('id');
	if (empty($id))
	{
		showMsg('错误参数！');
		return;		
	}else{

		$db->query("delete from servers_merger where id = '$id' ");
		showMsg('删除成功！',"",'','greentext');	
		return;		
	}
	$db->close();			
		
}

//--------------------------------------------------------------------------------------------开服时间表
	
function ServersOpen() 
{
	global $db,$adminWebName,$page; 

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$t=ReqStr('t');
	$odate=ReqStr('odate');
	if($odate)
	{
		$set_date = " and date_format(A.open_date, '%Y-%m-%d') = '$odate'";
	}	
	if($t == 'woc')
	{
		$set_t = "and A.open_date > now() and A.open = 1";
	}elseif($t == 'yc'){
		$set_t = "and A.open_date > now() and A.open = 0";
	}elseif($t == 'otc'){
		$set_t = "and date_format(A.open_date, '%Y-%m-%d') = curdate()";
	}elseif($t == 'oc'){
		$set_t = "and A.open = 1 and A.test = 0 and A.open_date <= now()";
	}			
	//------------------------------------------------------------------------------------------------	
	$query = $db->query("select combined_to from servers_merger where combined_to > 0");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$sid_c .= $sid_c ? ','.$rs['combined_to'] : $rs['combined_to'];	
		}
		$set_c = " where sid not in ($sid_c)";
		$set_c2 = " and  A.sid not in ($sid_c)";
	}
	
	//------------------------------------------------------------------------------------------------	
	$query = $db->query("
	select 
		distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
		count(sid) as s_num
	from 
		servers
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
	
	$s = $db->fetch_first("
	select 		
		count(case when open_date > now() and open = 1 then sid end) as wait_open_count,
		count(case when open_date > now() and open = 0 then sid end) as yd_count,
		count(case when open = 1 and test = 0 and open_date <= now() then sid end) as open_count,
		count(case when date_format(open_date, '%Y-%m-%d') = curdate() then sid end) as open_today_count
	from 
		servers
		$set_c
	");
	if($s){
		$wait_open_count = $s['wait_open_count'];//等待开启
		$yd_count = $s['yd_count'];//预定
		$open_count = $s['open_count'];//已开
		$open_today_count = $s['open_today_count'];//今日
		
	}
	///--------------------------------------------------------------------------------------
	
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers A
	where
		A.sid > 0
		$set_t
		$set_date
		$set_c2
	"),0);	
	if($num)
	{		
		$i = 0;	
		$query = $db->query("
		select 
			A.*,
			B.name company_name,
			C.name2	as db_server1
					
		from 
			servers A
			left join company B on A.cid = B.cid
			left join servers_address C on A.db_server = C.name and C.type = 1
		where
			A.sid > 0
			$set_t
			$set_date
			$set_c2
		order by 
			A.open_date desc
		limit 
			$start_num,$pageNum	
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['server'] = str_replace(",", "<br />",$rs['server']);	
			//$api_server = explode('.',$rs['api_server']);
			//$rs['api_server'] = $api_server[0];
			
			//$db_server2 = explode('.',$rs['db_server']);
			//$rs['db_server2'] = $rs['db_server'];			
			$list_array[] =  $rs;
		}

		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=ServersOpen&t=$t&odate=$odate");	
	}		
	$db->close();
	include_once template('servers_open');
}	
  //--------------------------------------------------------------------------------------------操作记录

function ServersAdminData() {
	global $db,$adminWebName,$page;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$aid = ReqNum('aid');
	$player_id = ReqNum('player_id');
	$text = trim(ReqStr('text'));
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商
	$servers_list = globalDataList('servers',"cid = '$cid'");//服务器
	
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
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if($aid)
	{
		$set_aid = "and A.adminID = '$aid'";
	}
		
	//-----------操作记录----------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		servers_admin_data A
	where 
		A.id > 0
		$set_text 
		$set_player
		$set_aid
		$set_sid
		$set_cid
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*
		from 
			servers_admin_data A 
		where 
			A.id > 0
			$set_text 
			$set_player
			$set_aid
			$set_sid
			$set_cid
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
		$list_array_pages = multi($num,$pageNum,$page,"s.php?in=server&action=ServersAdminData&text=$text_url&cid=$cid&sid=$sid&aid=$aid");	
		
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
	include_once template('s_servers_admin_data');
}
//--------------------------------------------------------------------------------------------批量版本更新日志
function  SetRenewLog() 
{
	global $odb; 
	$id_del = ReqArray('id_del');

	$date_n = ReqStr('date_n');
	$ver_n = ReqStr('ver_n');
	$contents_n = ReqStr('contents_n');
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$date_arr = "'".str_replace(",", "','",$id_arr)."'";
		
		$odb->query("delete from renew_log where date in ($date_arr)");
		$msg = "删除成功！";

		
	}		

	//-----------------增加记录-------------------------------------------
	if ($date_n && $ver_n && $contents_n)
	{
		$query = $odb->query("
		insert into 
			renew_log
			(`date`,`ver`,`contents`) 
		values 
			('$date_n','$ver_n','$contents_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}

	}	
	$odb->close();
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设运营商列表
function  SetCompany() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$web = ReqArray('web');
	$name = ReqArray('name');
	$slug = ReqArray('slug');
	$game_name = ReqArray('game_name');
	$corder = ReqArray('corder');
	$type = ReqArray('type');

	$web_n = ReqStr('web_n');
	$name_n = ReqStr('name_n');
	$slug_n = ReqStr('slug_n');
	$game_name_n = ReqStr('game_name_n');
	$corder_n = ReqStr('corder_n');
	$type_n = ReqNum('type_n');
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		if (SXD_SYSTEM_COMPANY_DEL)
		{
			$id_arr = implode(",",$id_del);
			$db->query("delete from servers where cid in ($id_arr)");
			$db->query("delete from company where cid in ($id_arr)");
			$msg = "删除成功！";
		}else{
			$msg = "未开启直接删除！";
		}
		
	}		
	
/*	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $web[$i] && $name[$i] && $slug[$i] && $game_name[$i])
			{
				$db->query("
				update 
					company 
				set 
					`web`='$web[$i]',
					`name`='$name[$i]',
					`slug`='$slug[$i]',
					`game_name`='$game_name[$i]',
					`corder`='$corder[$i]',
					`type`='$type[$i]'
				where 
					cid = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
*/		
	//-----------------增加记录-------------------------------------------
	if ($web_n && $name_n && $slug_n && $game_name_n)
	{
		$num = $db->result($db->query("select count(*) from company where `slug` = '$slug_n'"),0); //获得总条数
		if(!$num){
			$query = $db->query("
			insert into 
				company
				(`web`,`corder`,`name`,`slug`,`game_name`,`locale`,`money_type`,`type`) 
			values 
				('$web_n','$corder_n','$name_n','$slug_n','$game_name_n','zh_CN','人民币','$type_n')
			") ;
			if($query)
			{
				$msg .= "<br />增加成功！";
				$add_company = '增加运营商('.$name_n.')';
			}
			else
			{
				$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
			}
		}else{
			$msg .= '<br /><strong class="redtext">增加失败，平台短名已存在！</strong>';
		}
	}	
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_company = '删除的运营商ID('.$id_arr.')';
	$contents = '运营商设置:更新的运营商ID('.$id_up_arr.')'.$del_company.$add_company;
	insertServersAdminData(0,0,0,'运营商',$contents);//插入操作记录		
	$db->close();
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------保存修改运营商
function  SaveCompany() 
{
	global $db; 
	$cid = ReqNum('cid');
	$coins_rate = ReqStr('coins_rate');
	$rmb_rate = ReqStr('rmb_rate');
	$web = ReqStr('web');
	$name = ReqStr('name');
	$slug = ReqStr('slug');
	$key = trim(ReqStr('key'));
	$charge_ips = ReqStr('charge_ips');
	$game_name = ReqStr('game_name');
	$game_text = ReqStr('game_text');
	$locale = ReqStr('locale');
	$timeoffset = ReqStr('timeoffset');
	$money_type = ReqStr('money_type');
	$cdn = ReqStr('cdn');
	$link = ReqArray('link');
	$t_player = ReqNum('t_player');
	$type = ReqNum('type');
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	

	if (empty($cid) || !$coins_rate || !$rmb_rate || !$web || !$name || !$slug || !$key || !$game_name || !$locale || !$money_type)
	{
		$msg = '<strong class=redtext>错误参数，有项目未填写！</strong>';		
	}else{
		$num = $db->result($db->query("select count(*) from company where `slug` = '$slug' and cid <> '$cid'"),0); //获得总条数
		if(!$num){
			$charge_ips = str_replace(array("\n","\r","\t"), array("|","",""),$charge_ips);
			$cdn = str_replace(array("\n","\r","\t"), array("|","",""),$cdn);
			//$link = str_replace(array("\n","\r","\t"), array("|","",""),$link);
			$link = implode("|",$link);
			$query = $db->query("
			update 
				company 
			set 
				`coins_rate`='$coins_rate',
				`rmb_rate`='$rmb_rate',
				`web`='$web',
				`name`='$name',
				`slug`='$slug',
				`key`='$key',
				`charge_ips`='$charge_ips',
				`game_name`='$game_name',
				`game_text`='$game_text',
				`locale`='$locale',
				`timeoffset`='$timeoffset',
				`money_type`='$money_type',
				`cdn`='$cdn',
				`link`='$link',
				`t_player`='$t_player',
				`type`='$type'
			where 
				cid = '$cid'
			");
			if ($query)
			{
				$contents = '修改运营商设置:运营商(ID:'.$cid.'/名称:'.$name.')';
				insertServersAdminData(0,0,0,'运营商',$contents);//插入操作记录		
				$msg = "修改成功！";		
			}else{
				$msg = '<strong class=redtext>修改失败，出现异常错误！</strong>';		
			
			}
		}else{
			$msg = '<strong class=redtext>修改失败，[短名称]已被其它平台使用！</strong>';
		}
	}
	$db->close();
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
	
}
 
//--------------------------------------------------------------------------------------------批量设服务器列表
function  SetServers() 
{
	global $db,$adminWebName; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	
	$name = ReqArray('name');
	$o_name = ReqArray('o_name');
	$api_server = ReqArray('api_server');
	$api_port = ReqArray('api_port');
	$api_pwd = ReqArray('api_pwd');
	$db_server = ReqArray('db_server');
	$db_name = ReqArray('db_name');
	$db_root = ReqArray('db_root');
	$db_pwd = ReqArray('db_pwd','htm');
	
	$db_server_2 = ReqArray('db_server_2');
	$db_name_2 = ReqArray('db_name_2');
	$db_root_2 = ReqArray('db_root_2');
	$db_pwd_2 = ReqArray('db_pwd_2','htm');	
	
	$server = ReqArray('server');
	$open_date = ReqArray('open_date');
	$server_ver = ReqArray('server_ver');
	$client_ver = ReqArray('client_ver');
	$open = ReqArray('open');
	$private = ReqArray('private');
	$test = ReqArray('test');
	

	$cid = ReqNum('cid');
	$name_n = trim(ReqStr('name_n'));
	$o_name_n = trim(ReqStr('o_name_n'));
	$api_server_n = trim(ReqStr('api_server_n'));
	$api_port_n = trim(ReqStr('api_port_n'));
	$api_pwd_n = trim(ReqStr('api_pwd_n'));
	$db_server_n = trim(ReqStr('db_server_n'));
	$db_name_n = trim(ReqStr('db_name_n'));
	$db_root_n = trim(ReqStr('db_root_n'));
	$db_pwd_n = trim(ReqStr('db_pwd_n'));
	$db_server_2_n = trim(ReqStr('db_server_2_n'));
	$db_name_2_n = trim(ReqStr('db_name_2_n'));
	$db_root_2_n = trim(ReqStr('db_root_2_n'));
	$db_pwd_2_n = trim(ReqStr('db_pwd_2_n'));	
	
	$server_n = trim(ReqStr('server_n'));
	$open_date_n = trim(ReqStr('open_date_n'));
	$server_ver_n = trim(ReqStr('server_ver_n'));
	$client_ver_n = trim(ReqStr('client_ver_n'));	
	$open_n = ReqNum('open_n');	
	$private_n = ReqNum('private_n');	
	$test_n = ReqNum('test_n');		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		if ($adminWebName == 'admin')
		{
			$id_arr = implode(",",$id_del);
			$db->query("delete from servers where sid in ($id_arr)");
			$msg = "删除成功！";
		}else{
			$msg = "删除失败，您没有删除服务器权限！";
		}
		
	}		
/*	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<$id_num;$i++)	
		{
			if ($id[$i] && $api_server[$i] && $name[$i] && $db_server[$i] && $db_name[$i] && $db_root[$i] && $db_pwd[$i] && $server[$i] && $open_date[$i] && $server_ver[$i])
			{
				$server[$i] = str_replace(array("\n","\r","\t"), array(",","",""),$server[$i]);
				$num1 = $db->result($db->query("select count(*) from servers where `name` = '$name[$i]' and sid <> '$id[$i]'"),0); //获得总条数
				//$num2 = $db->result($db->query("select count(*) from servers where `api_port` = '$api_port[$i]' and api_server = '$api_server[$i]' and sid <> '$id[$i]'"),0); //获得总条数
				if(!$num1 && !$num2){
					$db->query("
					update 
						servers 
					set 
						`name`='$name[$i]',
						`o_name`='$o_name[$i]',
						`api_server`='$api_server[$i]',
						`api_port`='$api_port[$i]',
						`api_pwd`='$api_pwd[$i]',
						`db_server`='$db_server[$i]',
						`db_name`='$db_name[$i]',
						`db_root`='$db_root[$i]',
						`db_pwd`='$db_pwd[$i]',
						`db_server_2`='$db_server_2[$i]',
						`db_name_2`='$db_name_2[$i]',
						`db_root_2`='$db_root_2[$i]',
						`db_pwd_2`='$db_pwd_2[$i]',
						`server`='$server[$i]',
						`open_date`='$open_date[$i]',
						`server_ver`='$server_ver[$i]',
						`client_ver`='$client_ver[$i]',
						`open`='$open[$i]',
						`private`='$private[$i]',
						`test`='$test[$i]'
					where 
						sid = '$id[$i]'
					");
					$msg .= "<br />".$o_name[$i]."更新成功！";
				}else{
					$msg .= "<br /><strong class=\"redtext\">".$o_name[$i]."更新失败，代号及API地址端口配置已存在！</strong>";
				}
			}else{
				$msg .= "<br /><strong class=\"redtext\">".$o_name[$i]."更新失败，有项目未填写！</strong>";
			}
			
		}
	}*/
		
	//-----------------增加记录-------------------------------------------
	if ($cid && $api_server_n  && $name_n && $db_server_n && $db_name_n && $db_root_n && $db_pwd_n && $server_n && $open_date_n && $server_ver_n)
	{
		$num_n_1 = $db->result($db->query("select count(*) from servers where `name` = '$name_n'"),0); //获得总条数
		$num_n_1 = $db->result($db->query("select count(*) from servers where `api_port` = '$api_port_n' and api_server = '$api_server_n'"),0); //获得总条数
		if(!$num_n_1 && !$num_n_2){
			$query = $db->query("
			insert into 
				servers
				(`cid`,`api_server`,`api_port`,`api_pwd`,`name`,`o_name`,`db_server`,`db_name`,`db_root`,`db_pwd`,`db_server_2`,`db_name_2`,`db_root_2`,`db_pwd_2`,`server`,`open_date`,`server_ver`,`client_ver`,`open`,`private`,`test`) 
			values 
				('$cid','$api_server_n','$api_port_n','$api_pwd_n','$name_n','$o_name_n','$db_server_n','$db_name_n','$db_root_n','$db_pwd_n','$db_server_2_n','$db_name_2_n','$db_root_2_n','$db_pwd_2_n','$server_n','$open_date_n','$server_ver_n','$client_ver_n','$open_n','$private_n','$test_n')
			") ;
		}
		if($query)
		{
			$msg .= "<br />增加成功！";
			$add_server = '增加服务器('.$name_n.')';
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_server = '删除的服务器ID('.$id_arr.')';
	$contents = '服务器设置:更新的服务器ID('.$id_up_arr.')'.$del_server.$add_server;
	insertServersAdminData(0,0,0,'服务器',$contents);//插入操作记录
	$db->close();
	showMsg($msg,'','','greentext','','','n');	
}

//--------------------------------------------------------------------------------------------批量机器地址
function  SetServersAddress() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$name2 = ReqArray('name2');
	$name3 = ReqArray('name3');
	$name_n = trim(ReqStr('name_n'));
	$name2_n = trim(ReqStr('name2_n'));
	$name3_n = trim(ReqStr('name3_n'));
	$type = ReqNum('type');

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from servers_address where id in ($id_arr)");
		$msg = "删除成功！";
	}		
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{
				$db->query("
				update 
					servers_address 
				set 
					`name`='$name[$i]',
					`name2`='$name2[$i]',
					`name3`='$name3[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			servers_address
			(`type`,`name`,`name2`,`name3`) 
		values 
			('$type','$name_n','$name2_n','$name3_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
			$add_s = '增加机器地址('.$name_n.')';
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}	
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_s = '删除的机器地址ID('.$id_arr.')';
	$contents = '更新的机器地址ID('.$id_up_arr.')'.$del_s.$add_s;
	insertServersAdminData(0,0,0,'机器地址',$contents);//插入操作记录		
	$db->close();
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量战区设置
function  SetWarZone() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	
	$name_n = trim(ReqStr('name_n'));
	$sign_n = trim(ReqStr('sign_n'));

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from war_zone where id in ($id_arr)");
		$msg = "删除成功！";
	}		
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{
				$db->query("
				update 
					war_zone 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			war_zone
			(`name`,`sign`) 
		values 
			('$name_n','$sign_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}	
	$db->close();
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量战区选择设置
function  SetWarZoneSelect() 
{
	global $db; 
	$cid = ReqArray('cid');
	$zone = ReqNum('zone');

	//-----------------更新-------------------------------------------
	if ($cid)
	{
		$cid_arr = implode(",",$cid);
		$msg = $db->query("
		update 
			company 
		set 
			`zone`='$zone'
		where 
			cid in ($cid_arr)
		");
		$db->close();
		showMsg('操作成功','','','greentext');	
		
	}else{
		showMsg('错误参数！');	
	}

}

//--------------------------------------------------------------------------------------------保存修改服务器
function  SaveServers() 
{
	global $db,$adminWebID; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$name = trim(ReqStr('name'));
	$o_name = trim(ReqStr('o_name'));
	$api_server = trim(ReqStr('api_server'));
	$api_port = trim(ReqStr('api_port'));
	$api_pwd = trim(ReqStr('api_pwd'));
	$db_server = trim(ReqStr('db_server'));
	$db_name = trim(ReqStr('db_name'));
	$db_root = trim(ReqStr('db_root'));
	$db_pwd = trim(ReqStr('db_pwd','htm'));
	$db_server_2 = trim(ReqStr('db_server_2'));
	$db_name_2 = trim(ReqStr('db_name_2'));
	$db_root_2 = trim(ReqStr('db_root_2'));
	$db_pwd_2 = trim(ReqStr('db_pwd_2','htm'));	
	$pay_item = trim(ReqStr('pay_item'));	

	$server = trim(ReqStr('server'));
	$open_date = trim(ReqStr('open_date'));
	$server_ver = trim(ReqStr('server_ver'));
	$client_ver = trim(ReqStr('client_ver'));	
	$open = ReqNum('open');	
	$private = ReqNum('private');
	$test = ReqNum('test');
	$combined_to = ReqNum('combined_to');	
	$slug = ReqStr('slug');	
	
	$first_pay_act = ReqNum('first_pay_act');	
	$level_act = ReqNum('level_act');	
	$mission_act = ReqNum('mission_act');
	$new_card_act = ReqNum('new_card_act');
	$test_player = ReqNum('test_player');
	$combined_server = trim(ReqStr('combined_server'));	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	$num1 = $db->result($db->query("select count(*) from servers where `name` = '$name' and sid <> '$sid'"),0); //获得总条数
	//$num2 = $db->result($db->query("select count(*) from servers where `api_port` = '$api_port' and api_server = '$api_server' and sid <> '$sid'"),0); //获得总条数
	
	
/*	if ($slug != 'verycd' && $slug != 'txwy' && $slug != 'test' && $slug != '1wan')
	{
		$nameArr = explode('_',$name);
		if ($slug != $nameArr[0] && !($slug == 'sina'))
		{
			$msg = "<strong class=redtext>代号输入错误，不符合平台规则！</strong>";
			$msg = urlencode($msg);		
			showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
			return;
		}
	}*/
	
	if (empty($sid) || !$api_server || !name || !$db_server || !$db_name || !$db_root || !$db_pwd || !$server || !$open_date || !$server_ver)
	{
		$msg = "<strong class=redtext>错误参数，有项目未填写！</strong>";
	}elseif ($num1 || $num2)
	{
		$msg = '<strong class=redtext>代号及API地址端口配置已存在，请检查！</strong>';
	}else{
		$server = str_replace(array("\n","\r","\t"), array(",","",""),$server);
	
		$query = $db->query("
		update 
			servers 
		set 
			`name`='$name',
			`o_name`='$o_name',
			`api_server`='$api_server',
			`api_port`='$api_port',
			`api_pwd`='$api_pwd',
			`db_server`='$db_server',
			`db_name`='$db_name',
			`db_root`='$db_root',
			`db_pwd`='$db_pwd',
			`db_server_2`='$db_server_2',
			`db_name_2`='$db_name_2',
			`db_root_2`='$db_root_2',
			`db_pwd_2`='$db_pwd_2',			
			`pay_item`='$pay_item',
			`server`='$server',
			`open_date`='$open_date',
			`server_ver`='$server_ver',
			`client_ver`='$client_ver',
			`open`='$open',
			`private`='$private',
			`test`='$test',
			`combined_to`='$combined_to',
			`first_pay_act`='$first_pay_act',
			`level_act`='$level_act',
			`mission_act`='$mission_act',
			`new_card_act`='$new_card_act',
			`test_player`='$test_player',
			`combined_server`='$combined_server'
		where 
			sid = '$sid'
		");
		if ($query)
		{
		
		
			if ($new_card_act)//顺便开新手卡活动
			{
				$batch = $db->result($db->query("select count(*) from code_batch where sid = '$sid' and juche = 1"),0); //获得总条数
				if(!$batch)
				{
					$query = $db->query("
					insert into 
						code_batch
						(`cid`,`sid`,`name`,`ingot`,`coins`,`num`,`item_id`,`item_name`,`item_val`,`juche`,`adminID`,`edate`,`ctime`) 
					values 
						('$cid','$sid','新手卡大放送',0,0,0,520,'神秘礼包',1,1,'$adminWebID','9999-01-01',now())
					");
				}
			}
			
			
			$contents = '修改服务器设置:服务器(ID:'.$sid.'/名称:'.$name.')';
			insertServersAdminData(0,0,0,'服务器',$contents);//插入操作记录		
			$msg = "修改成功！";		
		}else{
			$msg = "修改失败，出现异常错误！";		
		
		}
	}
	$db->close();
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
	
}




//--------------------------------------------------------------------------------------------保存添加服务器
function  SaveServersAdd() 
{
	global $db,$adminWebName; 
	$id = ReqNum('id');
	$cid = ReqNum('cid');
	$name = trim(ReqStr('name'));
	$o_name = trim(ReqStr('o_name'));
	$api_server = trim(ReqStr('api_server'));
	$api_port = trim(ReqStr('api_port'));
	$api_pwd = trim(ReqStr('api_pwd'));
	$db_server = trim(ReqStr('db_server'));
	$db_name = trim(ReqStr('db_name'));
	$db_root = trim(ReqStr('db_root'));
	$db_pwd = trim(ReqStr('db_pwd','htm'));

	$server = trim(ReqStr('server'));
	$open_date = trim(ReqStr('open_date'));
	$server_ver = trim(ReqStr('server_ver'));
	$open = ReqNum('open');	
	$private = ReqNum('private');
	$test = ReqNum('test');
	$slug = ReqStr('slug');	
	
	$first_pay_act = ReqNum('first_pay_act');	
	$level_act = ReqNum('level_act');	
	$mission_act = ReqNum('mission_act');
	$new_card_act = ReqNum('new_card_act');
	$test_player = ReqNum('test_player');
	
	if ($slug != 'verycd' && $slug != 'txwy' && $slug != 'test' && $slug != '1wan')
	{
		$nameArr = explode('_',$name);
		if ($slug != $nameArr[0] && !($slug == 'sina'))
		{
			$msg = "<strong class=redtext>代号输入错误，不符合平台规则！</strong>";
			showMsg($msg,'','','greentext','','','n');	
			return;
		}
	}
	
	
	
	//-----------------增加记录-------------------------------------------
	if ($cid && $name && $server && $open_date && $server_ver && $api_port && $api_server)
	{
		$num_n_1 = $db->result($db->query("select count(*) from servers where `name` = '$name'"),0); //获得总条数
		$num_n_1 = $db->result($db->query("select count(*) from servers where `api_port` = '$api_port' and api_server = '$api_server'"),0); //获得总条数
		if(!$num_n_1 && !$num_n_2){
			$query = $db->query("
			insert into 
				servers(
				`cid`,
				`api_server`,
				`api_port`,
				`api_pwd`,
				`name`,
				`o_name`,
				`db_server`,
				`db_name`,
				`db_root`,
				`db_pwd`,
				`server`,
				`open_date`,
				`server_ver`,
				`open`,
				`private`,
				`test`,
				`first_pay_act`,
				`level_act`,
				`mission_act`,
				`new_card_act`,
				`test_player`
			)values (
				'$cid',
				'$api_server',
				'$api_port',
				'$api_pwd',
				'$name',
				'$o_name',
				'$db_server',
				'$db_name',
				'$db_root',
				'$db_pwd',
				'$server',
				'$open_date',
				'$server_ver',
				'$open',
				'$private',
				'$test',
				'$first_pay_act',
				'$level_act',
				'$mission_act',
				'$new_card_act',
				'$test_player'		
			)
			") ;
			
			if($query)
			{
				$sid = $db->insert_id();
				$db->query("update servers_merger set `combined_to` = '$sid' where id = '$id' and combined_to = 0");//更新
				showMsg("增加成功！",'','','greentext','','','n');	
			}else{
				showMsg("<strong class=redtext>增加失败，可能因为您输入了重复的数据！</strong>");	
			}
			
		}else{
			showMsg("<strong class=redtext>代号及API地址端口配置已存在，请检查！</strong>");	
		}
	}else{
		showMsg("错误参数！");	
	}
	$db->close();
}



//--------------------------------------------------------------------------------------------批量删除操作记录
function  DelServersAdminData() 
{
	global $db; 
	$id_del = ReqArray('id_del');

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from servers_admin_data where id in ($id_arr)");
		
		insertServersAdminData(0,0,0,'操作记录','操作记录:删除的操作记录ID('.$id_arr.')');//插入操作记录		
		$db->close();
		showMsg('删除成功！','','','greentext');	
		
	}else{
		showMsg('未选择！');	
	}		

}

//--------------------------------------------------------------------------------------------设置合服
function  SetCombinedTo() 
{
	global $db; 
	$id = ReqNum('id');
	if ($id)
	{
		$c = $db->fetch_first("
		select 		
			sid_m,
			combined_to,
			cid
		from 
			servers_merger A
		where 
			id = '$id'
		
		");	
		if ($c)
		{
			//-------查被合服的服之前是否是被其他服合并成的
			$query = $db->query("
			select 
				`sid`
			from 
				servers
			where 
				combined_to in ($c[sid_m])
			");
			if($db->num_rows($query)){
				while($rs = $db->fetch_array($query))
				{	
					$sidArr[] = $rs['sid'];
				}
			}
			//---------------------------------------------
			$sid_arr = $sidArr ? implode(",",$sidArr).','.$c['sid_m'] : $c['sid_m'];//有则合并
			//---------------------------------------------
			$query = $db->query("
			update 
				servers 
			set 
				`combined_to`='$c[combined_to]'
			where 
				sid in ($sid_arr)
			");
			//--------------------取旧的合服标题格式-------------------------
			$query = $db->query("
			select 
				`name`
			from 
				servers
			where 
				sid in ($c[sid_m])
			");
			if($db->num_rows($query)){
				while($rs = $db->fetch_array($query))
				{	
					if(strpos($rs['name'],'_',0)){
						$s = explode('_',$rs['name']);
						$ss = strtoupper($s[1]);
					}else{
						$ss = strtoupper($rs['name']);
					}
					$o_name_old .= $o_name_old ? ' + '.$ss : $ss ;

				}
			}			
			
			//---------------------------------------------
			$query = $db->query("
			select 
				`name`
			from 
				servers
			where 
				combined_to in ($c[combined_to])
				and is_combined = 0
			");
			if($db->num_rows($query)){
				while($rs = $db->fetch_array($query))
				{	
					if (strpos($rs['name'],'_',0)) 
					{
						$s = explode("_",trim($rs['name']));
						$sname = $s[1];
					}else{
						$sname = $rs['name'];
					}
					$snameArr[] = $sname;
				}
			}
			$sname_arr = $snameArr ? ' ('.implode(",",$snameArr).')' : '';//是被哪写服合的
			$o_name = $o_name_old . $sname_arr;
			//---------------------------------------------
			
			$query = $db->query("
			update 
				servers 
			set 
				`open`=1,
				`o_name`= '$o_name'
				
			where 
				sid = '$c[combined_to]'
			");			
			
		}
		
		ReServerTest($c['cid'],$c['combined_to']);
		
		$db->close();
		showMsg('合服指向成功！','','','greentext');	
		
	}else{
		showMsg('未选择！');	
	}		

}//--------------------------------------------------------------------------------------------批量修复合服服务器合服前开服时间
function  SetUpdateMerger() 
{
	global $db; 
	$sid = ReqArray('sid');
	$open_date_old = ReqArray('open_date_old');
	//----------------------删除--------------------------------------
	if ($sid)
	{
	
		$sid_num = count($sid);

		for ($i=0;$i<=$sid_num;$i++)	
		{
			if ($sid[$i] && $open_date_old[$i])
			{
				$db->query("
				update 
					servers 
				set 
					`open_date_old`='$open_date_old[$i]'
				where 
					sid = '$sid[$i]'
				");
			}
			
		}
		showMsg('成功！','','','greentext');	
	}else{
		showMsg('错误参数！');	
	}

}

//--------------------------------------------------------------------------------------------执行服务器数据库变更
function  SetServersDbTransfer() 
{
	global $db; 
	$f_db = ReqStr('f_db');
	$t_db = ReqStr('t_db');
	//----------------------删除--------------------------------------
	if ($f_db && $t_db)
	{
		//$num = $db->result($db->query("select count(*) from servers where db_server = '$f_db'"),0);	
		
		$query = $db->query("
		select 
			sid
		from 
			servers A
		where 
			db_server = '$f_db'
		order by
			sid asc
		");
		$num = $db->num_rows($query);
		if($num){
			while($rs = $db->fetch_array($query))
			{	
				$sidArr[] = $rs['sid'];
			}
			$sid_arr = implode(",",$sidArr);
			
			$db->query("
			update 
				servers 
			set 
				db_server = '$t_db'
			where 
				db_server = '$f_db'
			");
			insertServersAdminData(0,0,0,'变更数据库','变更'.$num.'台服务器数据库，由['.$f_db.']变更为['.$t_db.']；SID('.$sid_arr.')');//插入操作记录		
	
			showMsg('变更'.$num.'台服务器成功！','','','greentext');	
		}else{
			showMsg('没有需要变更的服务器！');	
		}
	}else{
		showMsg('错误参数！');	
	}

}
?> 