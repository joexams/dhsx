<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
function GetPurMonth($date){//获取指定日期上个月的第一天和最后一天
	$time=strtotime($date);
	$firstday=date('Y-m-01',strtotime(date('Y',$time).'-'.(date('m',$time)-1).'-01'));
	$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
	return array($firstday,$lastday);
}

function DataCompanyOrder() {
	global $db,$cid,$adminWebType,$adminWebName,$adminWebID,$adminWebCid; 
	global $cookiepath,$cookiedomain;
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	if ($stime && $etime) 
	{

		$stime_s = strtotime($stime.' 00:00:00');
		$etime_e = strtotime($etime.' 23:59:59');
	}else{
		$stime_s = strtotime(date('Y-m-d').' 00:00:00');
		$etime_e = strtotime(date('Y-m-d').' 23:59:59');
		
	}
	///--------------------------------------------------------------------------------------
	
	if($adminWebType == 's')//如果是开发
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商

	}	
	///--------------------------------------------------------------------------------------
	$order_array = array();
	$query = $db->query("
	select 
		A.cid,
		A.sid,
		A.player_id,
		sum(A.amount) as amount,
		count(A.player_id) as pay_num,
		A.username,
		A.nickname
	from 
		pay_data A
		left join servers B on A.sid = B.sid
	where 
		dtime_unix >= '$stime_s'
		and dtime_unix <= '$etime_e'
		and A.cid = '$cid'
		and A.success = 1
		and A.status = 0	
	group by 
		A.username
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
			$prs['amount'] = round($prs['amount'],2);
			$list_array[] = $prs;
		}
	}
	$db->close();
	include_once template('data_company_order');
		
}
//--------------------------------------------------------------------------------------------数据汇总首页

function DataData() {
	global $db,$adminWebType,$adminWebName,$adminWebID,$adminWebCid; 
	global $cookiepath,$cookiedomain;
	$sdate = date('Y-m-d 00:00:00');
	$day = date("Y-m-d 00:00:00",strtotime("-7 day"));
	$dayunix = strtotime($day);
	$dayy = date("Y-m-d 23:59:59",strtotime("-1 day"));
	$now = date("H:i:s");
	if (!webAdmin('key_data_set','y'))
	{
		include_once(dirname(dirname(__FILE__))."/online_data.php");
		$filename = "sxd_data_count_key.php";//文件名
		$dir = UCTIME_ROOT."/data/";//目录
		$flie = $dir.$filename;//全地址
		$filetime  = @filemtime($flie);//文件创建时间
		@include_once($flie);
		if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	
		if ($is_update) 
		{
			///----------------------------------总注册----------------------------------------------------
			$player_reg = $db->fetch_first("
			select 
				sum(A.register_count) as register_count,
				sum(A.create_count) as create_count
			from 
				game_data A
				left join company B on A.cid = B.cid
			");
	
			///----------------------------------服务器----------------------------------------------------
			$company_count = $db->result($db->query("select count(*) from company where cid in ($adminWebCid)"),0);
			
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
				cid in ($adminWebCid)
			");
	
			///----------------------------------最高在线----------------------------------------------------
			$max = $db->fetch_first("select * from max_online");
			
			///----------------------------------今日充值排行----------------------------------------------------
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
				A.cid in ($adminWebCid)
				and A.dtime >= '$sdate'
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
				A.cid in ($adminWebCid)
				and A.dtime >= '$sdate'
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
		$today_order_pay = $today_order_pay ? $today_order_pay : 0;
		//-------------------------------------生成缓存文件------------------------------------------------------	
		if ($is_update) 
		{
			$str = '$company_count='.$company_count.";\n"; 
			$str .= '$today_order_pay='.$today_order_pay.";\n"; 
			$str .= '$player_reg='.var_export($player_reg, TRUE).";\n"; 
			$str .= '$max='.var_export($max, TRUE).";\n"; 
			$str .= '$s='.var_export($s, TRUE).";\n";
			$str .= '$today_order_array='.var_export($today_order_array, TRUE).";\n";
			$str .= '$servers_order_array='.var_export($servers_order_array, TRUE).";\n";
			writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
		}
		//-------------------------------------------------------------------------------------------		
		
					
	}

	///----------------------------------历史总收入---------------------------------------------------
	$paylog=array();
	$payall=array();
	$payamt=array();
	if($_COOKIE['sxd_data_'.$adminWebID])
	{
		list($paylog,$payall,$payamt) = explode("\t", authcode($_COOKIE['sxd_data_'.$adminWebID], 'DECODE'));
	}
	if (!$paylog || !payall || !payamt) {
	
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
			cid in ($adminWebCid)
		");	
	
		///----------------------------------全日充值----------------------------------------------------
		$query = $db->query("
		select 
			gdate,
			sum(pay_amount) as pay_amount
		from 
			game_data
		where 
			cid in ($adminWebCid)
			and gdate >= '$day' and gdate < curdate()
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
		setcookie('sxd_data_'.$adminWebID, authcode(serialize($paylog)."\t".serialize($payall)."\t".serialize($payamt), 'ENCODE'),time()+SXD_SYSTEM_DATATIME_OUT,$cookiepath,$cookiedomain);	
			
	}else{
		$paylog=unserialize($paylog);
		$payall=unserialize($payall);	
		$payamt=unserialize($payamt);	
	}

	$set_ss = " and status <> 1 and success <> 0 ";
	
	$sdate_unix = strtotime(date('Y-m-d'));
	$t = $db->fetch_first("
	select 
		sum(amount) as amount_today,
		count(distinct(username)) as amount_today_user
	from 
		pay_data
	where 	
		dtime_unix >= '$sdate_unix'
		and cid in ($adminWebCid)
		$set_ss
	");	
	

	///----------------------------------7日时段充值数据----------------------------------------------------
	$datesArr = prDates(date("Y-m-d",strtotime("-7 day")),date("Y-m-d",strtotime("-1 day")));
	foreach ($datesArr as $drs => $day){
		$now_s = strtotime($day." 00:00:00");		
		$now_e = strtotime($day." ".$now);	
		$rs	= $db->fetch_first("select sum(amount) as pay_hour_amount,count(distinct(username)) as pay_hour_user from pay_data where dtime_unix >= '$now_s' and dtime_unix <= '$now_e' and cid in ($adminWebCid) $set_ss");	
		$rs['pay_hour_amount'] = round($rs['pay_hour_amount'],2);
		$rs['pay_hour_user'] = round($rs['pay_hour_user'],2);
		$dayhis[$day]  = $rs;
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
	
	
	//-------------------------------------------------------------------------------------------		

	
	$db->close();
	include_once template('data_data');
}

//--------------------------------------------------------------------------------------------消费统计

function DataConsume() {
	global $db,$adminWebType,$adminWebCid; 
	//$cid = ReqNum('cid');
	$ok = ReqNum('ok');
	$sstime = ReqStr('sstime');
	$eetime = ReqStr('eetime');
	$vip_s = ReqNum('vip_s');
	$vip_e = ReqNum('vip_e');
	$level_s = ReqNum('level_s');
	$level_e = ReqNum('level_e');
	$vip = ReqNum('vip');
	if($adminWebType == 'c'){
		$set_cid = " cid in ($adminWebCid)"	;
		//$servers_list = globalDataList('servers',$set_cid,'sid desc');
		
	}
	$company_list = globalDataList('company',$set_cid,'corder asc,cid asc');
	
	
	
	if ($ok)
	{
		$sid = ReqArray('sid');
		if (!$sid) 
		{
			showMsg(languagevar('NOCHOOSESERVER'));	
			return;	
		}		
		$dir = UCTIME_ROOT."/data/";//目录
		$sidArr =  $sid ? implode(",",$sid) : '';
		
		$query = $db->query("
		select 
			cid,
			sid,
			`name`
		from 
			servers
		where 
			sid in ($sidArr)
		order by 
			sid desc			
		");
		if($db->num_rows($query))
		{
			//$crs = array();
			//$consume_list = array();
			while($srs = $db->fetch_array($query))
			{	
				$flie = $dir.$srs['sid']."_sxd_data_consume_0_".$sstime.$eetime."_".$vip_s.$vip_e."_".$level_s.$level_e.".php";//文件名
				$srs['filetime']  = @filemtime($flie);//文件创建时间
				@include_once($flie);
				$crs = array_add($crs,$rs);
				//natsort($consume);
				//natsort($consume_list);
				$consume_list = array_add2($consume_list,sysSortArray($consume,'type'));
				$list_array[] = $srs;
			}
			$consume_list = sysSortArray($consume_list,'value_count','SORT_DESC');
		}
		//print_r($consume_list);
	}
	//$data = array("US" => "United States", "IN" => "India", "DE" => "Germany", "ES" => "Spain");
	//asort($data); 
	//print_r($data);

	$db->close();
	include_once template('data_consume');
}



//--------------------------------------------------------------------------------------------分级流失统计

function DataPlayerOut() {
	global $db,$adminWebType,$adminWebCid; 
	//$cid = ReqNum('cid');
	$ok = ReqNum('ok');
	$day = ReqNum('day');
	$slevel = ReqNum('slevel');
	$elevel = ReqNum('elevel');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	if($adminWebType == 'c'){
		$set_cid = " cid in ($adminWebCid)"	;		
	}
	$company_list = globalDataList('company',$set_cid,'corder asc,cid asc');
	
	if (!$day) {
		$day = 5;
	}
	if (!$slevel) {
		$slevel = 1;
	}
	if (!$elevel) {
		$elevel = SXD_SYSTEM_ITEM_LEVEL;
	}
	
	
	if ($ok)
	{
		$sid = ReqArray('sid');
		if (!$sid) 
		{
			showMsg(languagevar('NOCHOOSESERVER'));	
			return;	
		}		
		$dir = UCTIME_ROOT."/data/";//目录
		$sidArr =  $sid ? implode(",",$sid) : '';


		for ($i=$slevel;$i<=$elevel;$i++)
		{
			$level_list[$i] = $i;
			
		}


		
		$query = $db->query("
		select 
			cid,
			sid,
			`name`
		from 
			servers
		where 
			sid in ($sidArr)
		order by 
			sid desc			
		");
		if($db->num_rows($query))
		{
			//$crs = array();
			//$consume_list = array();
			while($srs = $db->fetch_array($query))
			{	
				$flie = $dir.$srs['sid']."_sxd_data_playerout_".$day."_".$slevel."_".$elevel."_".$stime."_".$etime.".php";//文件名
				$srs['filetime']  = @filemtime($flie);//文件创建时间
				@include_once($flie);
				$pall = array_add($pall,$all);
				$vall = array_add($vall,$vipall);
				$prs = array_add2($prs,$player);
				$vrs = array_add2($vrs,$vip);
				$list_array[] = $srs;
			}
			//$prs = sysSortArray($prs,'player_count','SORT_DESC');
		}
		//print_r($prs);
	}

	$db->close();
	include_once template('data_player_out');
}


//--------------------------------------------------------------------------------------------VIP等级统计

function DataVipLevel() {
	global $db,$adminWebType,$adminWebCid; 
	//$cid = ReqNum('cid');
	$ok = ReqNum('ok');
	if($adminWebType == 'c'){
		$set_cid = " cid in ($adminWebCid)"	;
		//$servers_list = globalDataList('servers',$set_cid,'sid desc');
		
	}
	$company_list = globalDataList('company',$set_cid,'corder asc,cid asc');
	
	
	
	if ($ok)
	{
		$sid = ReqArray('sid');
		if (!$sid) 
		{
			showMsg(languagevar('NOCHOOSESERVER'));	
			return;	
		}		
		$dir = UCTIME_ROOT."/data/";//目录
		$sidArr =  $sid ? implode(",",$sid) : '';
		
		$query = $db->query("
		select 
			cid,
			sid,
			`name`
		from 
			servers
		where 
			sid in ($sidArr)
		order by 
			sid desc			
		");
		if($db->num_rows($query))
		{
			//$crs = array();
			//$consume_list = array();
			$player_num = 0;
			while($srs = $db->fetch_array($query))
			{	
				$flie = $dir.$srs['sid']."_sxd_data_player_level_vip.php";//文件名
				$srs['filetime']  = @filemtime($flie);//文件创建时间
				@include_once($flie);
				$player_num += $player_vip_num;
				//natsort($consume);
				//natsort($consume_list);
				$p = array_add2($p,$player);
				//if (time()-$srs['filetime'] > 3600) $sidArr[] = $srs;
				$list_array[] = $srs;
			}
			//$player = sysSortArray($player,'value_count','SORT_DESC');
		}
		//print_r($level_list);
	}
	for ($i=1;$i<=12;$i++)
	{
		$level_list[$i] = $i;
		
	}	

	//$data = array("US" => "United States", "IN" => "India", "DE" => "Germany", "ES" => "Spain");
	//asort($data); 
	//print_r($data);

	$db->close();
	include_once template('data_vip_level');
}
//--------------------------------------------------------------------------------------------最高等级统计

function DataMaxLevel() {
	global $db,$adminWebType; 
	$order = ReqStr('order');
	if(!$order) $order = 'max_player_level';
	$query = $db->query("
	select 
		count(*) as s_num,
		A.max_player_level as level
		
	from 
		servers_data A
		left join company B on A.cid = B.cid
	where 
		B.slug not in ('1wan','test')
		and A.max_player_level > 0
	group by 
		level
	order by 
		$order desc,
		level desc
	");
	if($db->num_rows($query))
	{
		$all_num = 0;
		while($rs = $db->fetch_array($query))
		{	
			$all_num += $rs['s_num'];
			$max[] = $rs['s_num'];
			$list_array[] =  $rs;
		}
		$max_num = max($max);
	}	
	$db->close();
	include_once template('data_max_level');
}
//--------------------------------------------------------------------------------------------汇总

function DataServers() {
	global $db,$adminWebType; 
	
	if($adminWebType != 's')//如果不是开发
	{
		global $cid,$company;
	}else{
		$cid = ReqNum('cid');
		if (!$cid) 
		{
			$cid = $db->result_first("select cid from company order by corder asc limit 1");
		}	
		$company = $db->fetch_first("select money_type from company where cid = '$cid'");
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商				
	}

	
//------------------------------数据--------------------------------------------------------

	$query = $db->query("
	select 
		sum(pay_amount) as pay_amount,
		sum(new_player) as  pay_player_count,
		sum(pay_num) as  pay_num,
		sum(register_count) as  register_count,
		sum(create_count) as  create_count,
		sum(avg_online_count) as  online_count,
		max(max_online_count) as  max_online_count,
		count(*) as data_count,
		sid
	from 
		game_data
	where 
		cid = '$cid'			
	group by 
		sid
	");
	if($db->num_rows($query))
	{
		while($drs = $db->fetch_array($query))
		{	
			$drs['pay_amount'] = round($drs['pay_amount'],2);
			$data[$drs['sid']] =  $drs;
		}
	}
	
	
//--------------------------------------------------------------------------------------
	$query = $db->query("
	select 
		A.sid,
		A.name,
		A.o_name,
		A.open,
		A.test,
		A.open_date,
		B.max_player_level
	from 
		servers A
		left join servers_data B on A.sid = B.sid
	where 
		A.cid = '$cid'
		and A.open_date <= now()
	order by 
		A.open_date desc,
		A.sid desc			
	");
	if($db->num_rows($query))
	{
		
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	$db->close();
	include_once template('data_servers');
}
//--------------------------------------------------------------------------------------------平台每日汇总

function DataDayData() {
	global $db,$adminWebType; 
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$pay_amount = 0;
	$pay_user = 0;
	$reg_user = 0;
	if ($stime && $etime) 
	{

		$set_time = " and gdate >= '$stime' and gdate <= '$etime' ";
	}
	
	if($adminWebType != 's')//如果不是开发
	{
		global $cid,$company;
	}else{
		$cid = ReqNum('cid');
		if (!$cid) 
		{
			$cid = $db->result_first("select cid from company order by corder asc limit 1");
		}	
		$company = $db->fetch_first("select money_type from company where cid = '$cid'");
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商				
	}

	
//------------------------------数据--------------------------------------------------------

	$query = $db->query("
	select 
		gdate,
		sum(pay_amount) as pay_amount,
		sum(pay_player_count) as  pay_player_count,
		sum(new_player) as  new_player,
		sum(pay_num) as  pay_num,
		sum(consume) as  consume,
		sum(register_count) as  register_count,
		sum(create_count) as  create_count,
		sum(login_count) as  login_count,
		sum(avg_online_count) as  avg_online_count,
		count(*) as servers_count
	from 
		game_data
	where 
		cid = '$cid'	
		and gdate < curdate()	
		$set_time
	group by 
		gdate
	order by
		gdate desc
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$rs['pay_amount'] = round($rs['pay_amount'],2);
			$rs['consume'] = round($rs['consume'],2);
			$pay_amount += $rs['pay_amount'];
			$pay_new_user += $rs['new_player'];
			$pay_user += $rs['pay_player_count'];
			$reg_user += $rs['register_count'];
			$list_array[$rs['gdate']] =  $rs;
		}
	}
	//print_r($list_array);
	
	$db->close();
	include_once template('data_day_data');
}

//--------------------------------------------------------------------------------------------平台服务器每日汇总

function DataDayServersData() {
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$day = ReqStr('day');
	
	$query = $db->query("
	select 
		A.*,
		B.name,
		B.o_name,
		B.open,
		B.test,
		B.open_date,
		B.combined_to
	from 
		game_data A
		left join servers B on A.sid = B.sid
	where 
		A.cid = '$cid'
		and A.gdate = '$day'
	order by
		open_date desc,
		sid desc
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$rs['pay_amount'] = round($rs['pay_amount'],2);
			$rs['consume'] = round($rs['consume'],2);
			$list_array[$rs['sid']] =  $rs;
		}
	}
	
	$db->close();
	include_once template('data_day_servers_data');
}

//--------------------------------------------------------------------------------------------充值汇总服务器明细

function DataPayData() {
	global $db,$adminWebType,$adminWebName; 
	//$companyArr = ReqStr('companyArr');
	$pay = ReqNum('pay');
	$servers = ReqArray('servers');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$all = ReqStr('all');
	$allcid = ReqStr('allcid');
	$allsid = ReqStr('allsid');
	$ccid = ReqArray('ccid') ? ReqArray('ccid') : array();
	$s_hits = ReqStr('s_hits');
	$s_people = ReqStr('s_people');
	$s_arpu = ReqStr('s_arpu');
	
	
	$ccidArr =  $ccid ? implode(",",$ccid) : '';
	$companyArr =  $ccidArr ? $ccidArr.',' : '';
	$cid =  $ccidArr;
	$stime1 = date("Y-m-01",strtotime($month.'-01'));
	$etime1 = date("Y-m-d");
	
	
	//--------------------------------------------------------------------------------------------
	if ($stime && $etime && $etime >= $stime && !$all) 
	{
		$dt_start = strtotime($stime);
		$dt_end = strtotime($etime);
		while ($dt_start<=$dt_end){
			$day_list[] =  date('Y-m-d',$dt_start);
			$dt_start = strtotime('+1 day',$dt_start);
		}
		$set_time = "AND gdate >= '$stime' AND gdate <= '$etime'";
		$stime_s = strtotime($stime.' 00:00:00');
		$etime_e = strtotime($etime.' 23:59:59');
		$set_time2 = "AND dtime_unix >= '$stime_s' AND dtime_unix <= '$etime_e'";
	}elseif (!$all) {
		$stime = date("Y-m-01",strtotime($month.'-01'));
		$etime = date("Y-m-d");
	}
	
	//--------------------------------------------------------------------------------------------
	if($adminWebType != 's')//如果不是开发
	{
		global $adminWebCid;	
		$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
		$cidArr =  $cid ? explode(',',$cid) : array();
		$set_c = "and cid in ($adminWebCid)";

		for ($i = 0;$i<count($cidArr);$i++) 
		{
			if(!in_array($cidArr[$i],$adminCidArr)){//如果服务器不属于此运营商
				showMsg(languagevar('NOCOMPANYPOWERMSG'),'login.php','','','','n');	
				return;
			
			}	
		}
		
	}	
	
	
	//--------------------------------------------------------------------------------------------
	//$company_list = globalDataList('company',$set_c,'corder asc,cid asc');//运营商			
	$cquery = $db->query("
	select 
		cid,
		name,
		slug
	from 
		company
	where 
		cid > 0 
		$set_c
	order by 
		corder asc,cid asc
	");
	while($crs = $db->fetch_array($cquery)){
		$cidArray[] = $crs['cid'];
		$company_list[] = $crs;
	}	
	$cidArr = implode(",",$cidArray);
	if ($company_list) $crows = array_chunk($company_list,6); 	
	
	//--------------------------------------------------------------------------------------------
	if ($companyArr)
	{
	
		if ($allsid)
		{
			$set_group = " group by sid,gdate ";
			$set_group_c = " group by sid ";
			$s_val = "sid";
		}else{
			$set_group = " group by cid,gdate ";
			$set_group_c = " group by cid ";
			$s_val = "cid";
			
			
			//------------------------------开服数量--------------------------------------------------------
				
	
			$osquery = $db->query("
			select 
				A.cid,
				count(case when A.open = 1 and A.private = 1 and A.test = 0 and A.open_date <= now() then sid end) as open_count
			from 
				servers A

			group by 
				A.cid
			");		
			if($db->num_rows($osquery))
			{
				while($osrs = $db->fetch_array($osquery))
				{
					$os[$osrs['cid']] = $osrs;
				}
			}
					
			
			
			
			
		}
	

		$serversArr =  $servers ? $servers : array();

		if ($allsid)
		{

			$squery = $db->query("
			select 
				A.cid,
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
				B.corder asc,
				A.sid desc
			");
		}else{
			$squery = $db->query("
			select 
				cid,
				name as company_name 
			from 
				company
			where 
				cid in ($cid) 	
			order by 
				corder asc,
				cid desc
			");		
		
		}
			
			
		while($srs = $db->fetch_array($squery)){
			if ($servers) {
				if (in_array($srs['sid'],$serversArr)) $servers_select_list[$srs[$s_val]] = $srs;
			}else{
				$servers_select_list[$srs[$s_val]] = $srs;
				
			}
			if ($allsid){
				$servers_list[$srs['sid']] = $srs;
			}				
			
			
		}	
			//if ($servers_list) $srows = array_chunk($servers_list,6); 
			
		//------------------------------平台服务器充值人数--------------------------------------------------------
		
/*		$query = $db->query("
		select 
			cid,
			sid,		
			COUNT(DISTINCT(username)) AS pay_player_count
		from 
			pay_data 
		where 
			cid in ($cid)
			and status = 0
			and success = 1
			$set_time2
			$set_group_c
		");
		while($prs = $db->fetch_array($query))
		{
			$p[$prs[$s_val]] = $prs;
		}	*/			
		//	print_r($p);
		//------------------------------平台服务器明细数据--------------------------------------------------------
		if ($s_hits) $set_hits = ",sum(pay_num) as pay_num";
		if ($s_people) $set_people = ",sum(pay_player_count) as pay_player_count";
		
		$query = $db->query("
		select 
			cid,
			sid,
			sum(pay_amount) as pay_amount
			$set_hits
		from 
			game_data
		where 
			cid in ($cid)
			$set_cid
			$set_time
			$set_group_c
		");
		while($crs = $db->fetch_array($query))
		{
			$crs['pay_amount'] = round($crs['pay_amount'],2);
			$c[$crs[$s_val]] = $crs;
		}				
		//------------------------------日期明细数据--------------------------------------------------------
		$query = $db->query("
		select 
			cid,
			sid,
			gdate,
			sum(pay_amount) as pay_amount
			$set_hits
			$set_people
		from 
			game_data
		where 
			cid in ($cid)
			$set_cid
			$set_time
			$set_group			
		order by 
			gdate asc
		");
		while($srs = $db->fetch_array($query))
		{
			$srs['pay_amount'] = round($srs['pay_amount'],2);
			$pay_amount += $srs['pay_amount'];	//总计
			$pay_num += $srs['pay_num'];	//总计
			$s[$srs[$s_val]][$srs['gdate']] = $srs;
		}

		//------------------------------总计--------------------------------------------------------
		if ($servers)
		{
			$sid =  implode(',',$servers);
			$set_sid_a = " and sid in ($sid)";
		}
		//-------------------------------------------总充值人数----------------------------------------------------
		if ($s_people)
		{
			$pquery = $db->query("
			SELECT 		
				COUNT(DISTINCT(player_id)) AS pay_player_count
			FROM 
				pay_data 
			WHERE 
				cid in ($cid)
				and status = 0
				and success = 1			
				$set_sid_a
				$set_time2	
			GROUP BY
				sid
			");
			while($prs = $db->fetch_array($pquery)){
				$pay_player_count += $prs['pay_player_count'];
			}
		}		
		
		//------------------------------日期数据--------------------------------------------------------
		
		$query = $db->query("
		select 
			gdate,
			sum(pay_amount) as pay_amount,
			sum(pay_player_count) as pay_player_count,
			sum(pay_num) as pay_num
		from 
			game_data
		where 
			cid in ($cid)
			$set_sid_a
			$set_time
		group by 
			gdate				
		order by 
			gdate asc
		");
		while($drs = $db->fetch_array($query))
		{
			$d[$drs['gdate']]=$drs;
		}

		//print_r($s);
	}

	$db->close();
	include_once template('data_pay_data_s');
}

//--------------------------------------------------------------------------------------------充值排行

function DataPayOrder() {

	global $db,$adminWebType,$page; 
	$cid = ReqNum('cid');
	$type = ReqStr('type');
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	if($adminWebType != 's')//如果不是开发
	{
		global $cid,$company;
		$set_cid = " and cid = '$cid'";
	}else{
		if($type != 'all')
		{
			if (!$cid) $cid = $db->result_first("select cid from company order by corder asc limit 1");
			$set_cid = " and cid = '$cid'";
			
			//$company = $db->fetch_first("select money_type from company where cid > 0 $set_cid");	
			$company_list = globalDataList('company','','corder asc,cid asc');//运营商
			
			
		}
	}	
	
	if($type != 'all')
	{
		$game = $db->fetch_first("
		select 		
			sum(amount) as pay_amount,
			count(*) as pay_player_count
		from 
			pay_player 
		where
			cid = '$cid'			
		");
		if($game){
			$pay_player_count = $game['pay_player_count'];//-----------充值人数
			$pay_amount = $game['pay_amount'];//-----------收入
		}	
		
				
		$pay_count = $db->result($db->query("
		select 
			sum(pay_num)
		from 
			game_data
		where
			cid = '$cid'			
		"),0);		
	


		///----------------------------------充值排行----------------------------------------------------		
		if($pay_player_count)
		{
			$i=1*$start_num+1;
			$query = $db->query("
			select 
				*
			from 
				pay_player
			where 
				cid = '$cid'		
			order by 
				amount desc
			limit 
				$start_num,$pageNum	
			");
			
	
			
			while($rs = $db->fetch_array($query))
			{	
				$rs['i'] = $i++;
				$cidArr[] = $rs['cid'];
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($pay_player_count,$pageNum,$page,$adminWebType.".php?in=data&action=DataPayOrder&cid=$cid");	
		}
		

			
	}else{
		$game = $db->fetch_first("
		select 		
			sum(amount) as pay_amount,
			count(*) as pay_player_count
		from 
			pay_player 
		");
		if($game){
			$pay_player_count = $game['pay_player_count'];//-----------充值人数
			$pay_amount = $game['pay_amount'];//-----------收入
		}	
		
				
		$pay_count = $db->result($db->query("
		select 
			sum(pay_num)
		from 
			game_data
		"),0);	
		//--------------------------------------------------------------------------------
		$i=1;
		$i=1*$start_num+1;
		$query = $db->query("
		select 
			*
		from 
			pay_player		
		order by 
			amount desc
		limit 
			200
		");			
		while($rs = $db->fetch_array($query))
		{	
			$rs['i'] = $i++;
			$cidArr[] = $rs['cid'];
			$list_array[] =  $rs;
		}	
	}



	///----------------------------------服务器列表----------------------------------------------------
	
	if($cidArr)
	{
		$cidArr = array_unique($cidArr);
		$cid_arr = implode(",",$cidArr);
		
		$query = $db->query("
		select 
			cid,
			name
		from 
			company
		where 
			cid in ($cid_arr)
		");
	
	
		while($crs = $db->fetch_array($query))
		{	
			$c[$crs['cid']] =  $crs;
		}	
	}
	
	
	$db->close();
	include_once template('data_pay_order');
	
}
//--------------------------------------------------------------------------------------------充值记录

function DataPay() {
	global $db,$adminWebType,$adminWebName,$page;
	$sid = ReqNum('sid');
	$username = ReqStr('username');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$oid = ReqStr('oid');
	$s = ReqNum('s');
	$t = ReqNum('t');
	$liquan = ReqNum('liquan');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		if (!$cid) $cid = $db->result_first("select cid from company order by corder asc limit 1");	
		$company = $db->fetch_first("select money_type,slug from company where cid = '$cid'");
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","open_date desc");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				showMsg(languagevar('NOSERVERPOWER'));	
				return;	
			
			}			
			
		}		
		
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 $set_sid_arr");//服务器
	
	}

	
	
//--------------------------------------------------------------------------------------
	if($s)
	{	
		$set_s = "and A.success = 0";
	}
	if($t)
	{	
		$set_t = "and A.status = 1";
	}	
	if ($username) 
	{
		$set_user = "and (A.username ='$username' or A.nickname ='$username')";
	}	
	if ($oid) 
	{
		$set_oid = "and A.oid ='$oid'";
	}
	
	
	if ($stime && $etime) 
	{
	
		$stime_s = $stime." 00:00:00";
		$etime_e = $etime." 23:59:59";
		$set_day = "and dtime >= '$stime_s' and dtime <= '$etime_e'";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if (($stime && $etime) || $username) 
	{	
	
		//-----------收入/待充/测试------------------------------------------------------------
		$query = $db->query("
		select 		
			sum(if(A.success = 1 and A.status = 0,amount,0)) as amount ,
			sum(if(A.success = 0,amount,0)) as amount_no ,
			sum(if(A.status = 1,amount,0)) as amount_test
		from 
			pay_data A
		where 
			A.cid = '$cid'	
			$set_sid
			$set_day
			$set_user 
		");
		if($db->num_rows($query)){
			$mrs = $db->fetch_array($query);
			$amount = $mrs['amount'];//-----------收入
			$amount_no = $mrs['amount_no'];//-----------待充
			$amount_test = $mrs['amount_test'];//-----------测试
		}	
	}
		
	//-----------充值记录----------------------------------------------------------
	if($company['slug'] == 'verycd')
	{
		$set_left_vc = "left join pay_data_vc B on A.oid = B.oid and A.sid = B.sid";
		$set_vc = ",B.oid as vc_oid";
		
	}
	if($company['slug'] == 'verycd' && $liquan)
	{
		$set_where_liquan = "and B.oid <> ''";
		$set_page = "&liquan=$liquan";
	}	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		pay_data A
		$set_left_vc
	where 
		A.cid = '$cid'		
		$set_user 
		$set_day
		$set_oid
		$set_sid
		$set_s
		$set_t
		$set_where_liquan
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*
			$set_vc
		from 
			pay_data A 
			$set_left_vc
		where 
			A.cid = '$cid'		
			$set_user 
			$set_day 
			$set_oid
			$set_sid
			$set_s
			$set_t
			$set_where_liquan
		order by
			A.pid desc 
		limit
			$start_num,$pageNum
		");	
		while($prs = $db->fetch_array($query)){	
			$sidArr[] = $prs['sid'];
			$list_array[] = $prs;
		}

		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=data&action=DataPay&username=$username&oid=$oid&stime=$stime&etime=$etime&cid=$cid&sid=$sid&s=$s&t=$t$set_page");	
		
	}	
		
	if($sidArr)
	{
		$sidArr = array_unique($sidArr);
		$sid_arr = implode(",",$sidArr);
		$query = $db->query("
		select 
			A.sid,
			A.is_combined,
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
				$ss[$srs['sid']] = $srs;
			}
		}
	}			
	
	if ($adminWebType == 'u' && !$username && !$stime && !$etime && !$oid) 
	{
		$list_array = '';
		$list_array_pages = '';
	}	
	$db->close();
	include_once template('data_pay');
}

 //--------------------------------------------------------------------------------------------设置充值记录[待充/成功]
function  SetPaySuccess() 
{
	global $db,$adminWebName; 
	if ($adminWebName != 'admin') 
	{
		showMsg('NOPOWER');	
		return;	
	}
	$pid = ReqNum('pid');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$oid = ReqStr('oid');
	$username = ReqStr('username');
	$s = ReqNum('s');
	if ($s)
	{
		$success = 0;
	}else{
		$success = 1;
	}
	
	if (!$pid || !$sid || !$cid || !$oid) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}
	$db->query("update pay_data set success = '$success' where  cid= '$cid' and oid = '$oid' and pid = '$pid'");
	showMsg('SETOK','','','greentext');	
	SetReplyPayPlayer($username,$cid,$is_combined);
	$db->close();			
	
	
}

 //--------------------------------------------------------------------------------------------设置充值记录[测试/正常]
function  SetPayStatus() 
{
	global $db,$adminWebName; 
	if ($adminWebName != 'admin') 
	{
		showMsg('NOPOWER');	
		return;	
	}
	$pid = ReqNum('pid');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$oid = ReqStr('oid');
	$username = ReqStr('username');
	$s = ReqNum('s');
	if ($s)
	{
		$status = 0;
	}else{
		$status = 1;
	}
	
	if (!$pid || !$sid || !$cid || !$oid) 
	{
		showMsg('NOCHOOSESERVER');	
		return;		
	}
	$db->query("update pay_data set status = '$status' where  cid= '$cid' and oid = '$oid' and pid = '$pid'");
	showMsg('SETOK','','','greentext');	
	SetReplyPayPlayer($username,$cid,$is_combined);
	$db->close();			
	
	
}


 //--------------------------------------------------------------------------------------------设置充值VIP接口[失败/正常]
function  SetPayVIP() 
{
	global $db,$adminWebName; 
	if ($adminWebName != 'admin') 
	{
		showMsg('NOPOWER');	
		return;	
	}
	$pid = ReqNum('pid');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$oid = ReqStr('oid');
	$s = ReqNum('s');
	if ($s)
	{
		$vip = 0;
	}else{
		$vip = 1;
	}
	
	if (!$pid || !$sid || !$cid || !$oid) 
	{
		showMsg('NOCHOOSESERVER');	
		return;		
	}
	$db->query("update pay_data set vip_level_up = '$vip' where  cid= '$cid' and oid = '$oid' and pid = '$pid'");
	showMsg('SETOK','','','greentext');	
	$db->close();			
	
	
}


//--------------------------------------------------------------------------------------------日报表数据对比

function DataDay() {
	global $db,$pdb,$adminWebType;
	$open_date = ReqStr('open_date');
	$days = ReqNum('days');
	if (!$days) $days = 3;
	if (!$open_date) $open_date = date('Y-m-d');
	$open_date_e = date('Y-m-d',strtotime($open_date)+86400*$days);
	$today = date('Y-m-d');
	$day_s = date("Y-m-d 00:00:00");
	$day_e = date("Y-m-d 23:59:59");
	
	//--------------------------------------------------------------------------------------------
	if($adminWebType != 's')//如果不是开发
	{
		global $adminWebCid;	
		$set_cid = "and cid in ($adminWebCid)";

	}	
	//----------------------------------------------------------------------
	$dt_start = strtotime($open_date);
	$dt_end = strtotime($open_date_e);
	
	while ($dt_start<$dt_end){
		$day_list[] =  date('Y-m-d',$dt_start);
		$dt_start = strtotime('+1 day',$dt_start);

	}
/*	for ($i=$open_date;$i<$open_date_e;$i++)
	{
		$day_list[] = $i;
		
	}*/
	//----------------------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
		count(sid) as s_num
	from 
		servers
	where
		sid > 0
		$set_cid
	group by 
		odate	
	order by 
		odate desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_open_list[]=$drs;
	}		
	//---------------------服务器---------------------------------------------
	$query = $db->query("
	select 
		cid,
		sid,
		name,
		o_name,
		open_date
	from 
		servers
	where 
		date_format(open_date, '%Y-%m-%d') = '$open_date'
		$set_cid
	order by 
		sid desc			
	");
	if($db->num_rows($query))
	{
		//$today_pay_amount = 0;
		while($srs = $db->fetch_array($query))
		{	
			$cid = $srs['cid'];
			$sid = $srs['sid'];
			//----------------------------------------------------------------------
		
			$dquery = $db->query("
			select 
				* 
			from 
				game_data
			where 
				cid = '$cid'
				and sid = '$sid'
				and gdate >= '$open_date' and gdate <= '$open_date_e'
			order by 
				gdate asc
			");
			while($drs = $db->fetch_array($dquery))
			{
				$data[$sid.'_'.$drs['gdate']] = $drs;
			}
			if(is_array($data))
			{ 
				$i=0;
				foreach($data as $drs)
				{
					$yesterday_create_count = $data[$sid.'_'.date('Y-m-d',strtotime($drs['gdate'])-86400)]['create_count'];
					$drs['out_rate'] = $yesterday_create_count ? round($drs['out_count']/$yesterday_create_count,2)*100 : 0;
					$out_rate += $drs['out_rate'];
					if ($yesterday_create_count) $i++;
					
					if($drs['gdate'] == $today)
					{
						$pay_amount = $db->result($db->query("select sum(amount) from pay_data where dtime >= '$day_s' and dtime <= '$day_e' and cid = '$cid' and sid = '$sid' and status = 0 and success = 1"),0); //今日充值
						$drs['pay_amount'] = round($pay_amount,2);
						//$today_pay_amount += $drs['pay_amount'];
					}
					//if($drs['gdate'] == $open_date && $drs['gdate'] != $today) $today_pay_amount += $drs['pay_amount'];//当天
					$data[$sid.'_'.$drs['gdate']] = $drs;
				}
			}		
			
			//----------------------------------------------------------------------
		
		
			$list_array[] =  $srs;
		}
	}	

		
	$db->close();


	 
	include_once template('data_day');
	
}

//--------------------------------------------------------------------------------------------收益趋势表

function DataDayList() {
	global $db,$odb,$adminWebName,$adminWebType,$adminWebCid;
	$mobile = 164;//手机平台CID
	$cid = ReqNum('cid');
	if($cid)
	{	
		$set_scid = " and cid = '$cid'";
		$set_scid2 = " and A.cid = '$cid'";
	}	
	
	
	if($adminWebType == 'c')//如果不是开发
	{	
		$set_cid = " and cid in ($adminWebCid)";
		$set_cid2 = " and A.cid in ($adminWebCid)";
		$set_cid3 = "cid in ($adminWebCid)";
	}	
	
	$company_list = globalDataList('company',$set_cid3,'corder asc,cid asc');//运营商		
	$open_date = ReqStr('open_date');
	if (!$open_date) $open_date = date('Y-m-d',time()-86400);

	//---------------------------------------------------------------------------------------------------------
	
	$mday = $db->result_first("select min(open_date) from servers where `open` = 1 and open_date <> '' $set_cid $set_scid");
	if($mday){
		$dt_start = strtotime(date('Y-m-d',strtotime($mday)));
		$dt_end = strtotime(date('Y-m-d',time()-86400));
		while ($dt_start<=$dt_end){
			$day_list[] =  date('Y-m-d',$dt_start);
			$dt_start = strtotime('+1 day',$dt_start);
		}
		//print_r($day_list);
		$day_list = array_reverse($day_list);
		$d = date('Y-m-d',time()-86400);
		$open_count = $db->result($db->query("select count(case when open = 1 and test = 0 and date_format(open_date, '%Y-%m-%d') <= '$d' then sid end) from servers where open = 1 and cid <> '$mobile' $set_cid $set_scid"),0);
		$m_open_count = $db->result($db->query("select count(case when date_format(open_date, '%Y-%m-%d') <= '$d' then id end) from servers_merger where id > 0 and cid <> '$mobile' $set_cid $set_scid"),0);
		//----------------------------------------------------------------------
		$query = $db->query("
		select 
			distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
			count(sid) as s_num
		from 
			servers
		where 
			open = 1
			and test = 0
			and cid <> '$mobile'
			$set_cid
			$set_scid
		group by 
			odate	
		order by 
			odate desc
		");
		while($srs = $db->fetch_array($query))
		{
			$server[$srs['odate']]=$srs;
		}	
		//-----------------------当日合服收益-----------------------------------------------
		$query = $db->query("
		select 
			A.gdate,
			sum(if(date_format(B.open_date, '%Y-%m-%d') = A.gdate,A.pay_amount,0)) as pay_amount_today
		from 
			game_data A
			left join servers_merger B on A.sid = B.combined_to
		where 
			A.gid > 0
			and A.cid <> '$mobile'
			$set_cid2
			$set_scid2
		group by 
			A.gdate
		");
		if($db->num_rows($query))
		{		
			while($mrs = $db->fetch_array($query))
			{
				//$sidArr .= $sidArr ? ','.$mrs['combined_to'] : $mrs['combined_to'];	
				$mrs['pay_amount_today'] = round($mrs['pay_amount_today'],1);
				$mpay[$mrs['gdate']]=$mrs;
			}
		}
		
		//-----------------------合服-----------------------------------------------
		$query = $db->query("
		select 
			distinct(date_format(open_date, '%Y-%m-%d')) AS odate ,
			count(*) as m_num
		from 
			servers_merger
		where 
			id > 0
			and cid <> '$mobile'
			$set_cid
			$set_scid
		group by 
			odate
		");
		if($db->num_rows($query))
		{		
			while($mrs = $db->fetch_array($query))
			{
				//$sidArr .= $sidArr ? ','.$mrs['combined_to'] : $mrs['combined_to'];	
				$merger[$mrs['odate']]=$mrs;
			}
			$set_sid_no = " and A.sid not in($sidArr)";
		}
		
		//-----------------------版本更新日志-----------------------------------------------
		$query = $odb->query("
		select 
			*
		from 
			renew_log
		");
		if($odb->num_rows($query))
		{		
			while($lrs = $odb->fetch_array($query))
			{
				$date[$lrs['date']]=$lrs;
			}
		}		
		
		//---------------------服务器---------------------------------------------
	
		$gquery = $db->query("
		select
			A.gdate,
			sum(A.pay_amount) as pay_amount,
			sum(if(date_format(B.open_date, '%Y-%m-%d') = A.gdate,A.pay_amount,0)) as pay_amount_today,
			sum(if(date_format(B.open_date, '%Y-%m-%d') <= '$open_date' and A.gdate >= '$open_date',A.pay_amount,0)) as pay_amount_d,
			sum(if(date_format(B.open_date, '%Y-%m-%d') = '$open_date' and A.gdate >= '$open_date',A.pay_amount,0)) as pay_amount_dd
		from 
			game_data A
			left join servers B on A.sid = B.sid
		where
			A.gid > 0
			and A.cid <> '$mobile'
			$set_cid2
			$set_scid2
		group by 
			A.gdate
		");
		while($grs = $db->fetch_array($gquery))
		{
			$grs['pay_amount'] = round($grs['pay_amount'],1);
			$grs['pay_amount_today'] = round($grs['pay_amount_today'],1);
			$grs['pay_amount_d'] = round($grs['pay_amount_d'],1);
			$grs['pay_amount_dd'] = round($grs['pay_amount_dd'],1);
			$data[$grs['gdate']] = $grs;
		}		
		//print_r($data);
	}

	//----------------------------------------------------------------------
	$odb->close();
	$db->close();


	 
	include_once template('data_day_list');
	
}

//--------------------------------------------------------------------------------------------时段充值对比

function DataHourList() {
	global $db,$odb,$adminWebName,$adminWebType,$adminWebCid;
	
	$cid = ReqNum('cid');
	if($cid)
	{	
		$set_scid = " and cid = '$cid'";
	}	
	
	
	if($adminWebType == 'c')//如果不是开发
	{	
		$set_cid = " and cid in ($adminWebCid)";
		$set_cid2 = "cid in ($adminWebCid)";
	}	
	
	$company_list = globalDataList('company',$set_cid2,'corder asc,cid asc');//运营商		
	
	//-------------------------------------------日时段趋势--------------------------------------------------------------
	include_once("include/FusionCharts.php");
	$day_h = ReqStr('day_h');
	$day_h2 = ReqStr('day_h2');
	if (!$day_h) $day_h = date('Y-m-d',time()-86400);
	//if (!$day_h2) $day_h2 = date('Y-m-d',time()-86400*2);
	$sdate = date('Y-m-d 00:00:00');
	$edate = date('Y-m-d 23:59:59');
	$sday_h = $day_h.' 00:00:00';
	$eday_h = $day_h.' 23:59:59';
	$sday_h2 = $day_h2.' 00:00:00';
	$eday_h2 = $day_h2.' 23:59:59';
	//-------------------------------------------------------------------------------------------
	$query = $db->query("
	select 
		date_format(dtime, '%H') as hour,
		sum(amount) as pay_today
	from 
		pay_data
	where 
		dtime >= '$sdate' 
		and dtime <= '$edate'
		and success <> 0
		and status <> 1
		$set_cid
		$set_scid
	group by
		hour			
	");
	if($db->num_rows($query))
	{
		while($prs = $db->fetch_array($query))
		{
			$prs['pay_today'] = round($prs['pay_today'],2);
			$today[$prs['hour']] = $prs['pay_today'];
			//$xml .= "<set name='".$prs['hour']."' value='".$prs['pay_today']."'/>";
		}
		
	}
	//-------------------------------------------------------------------------------------------
	if ($day_h)
	{	
		$query = $db->query("
		select 
			date_format(dtime, '%H') as hour,
			sum(amount) as pay_day
		from 
			pay_data
		where 
			dtime >= '$sday_h' 
			and dtime <= '$eday_h'
			and success <> 0
			and status <> 1
			$set_cid
			$set_scid
		group by
			hour			
		");
		if($db->num_rows($query))
		{
			while($prs = $db->fetch_array($query))
			{
				$prs['pay_day'] = round($prs['pay_day'],2);
				$day[$prs['hour']] = $prs['pay_day'];
			}
			
		}
	}
	//-------------------------------------------------------------------------------------------
	if ($day_h2)
	{
		$query = $db->query("
		select 
			date_format(dtime, '%H') as hour,
			sum(amount) as pay_day2
		from 
			pay_data
		where 
			dtime >= '$sday_h2' 
			and dtime <= '$eday_h2'
			and success <> 0
			and status <> 1
			$set_cid
			$set_scid
		group by
			hour			
		");
		if($db->num_rows($query))
		{
			while($prs = $db->fetch_array($query))
			{
				$prs['pay_day2'] = round($prs['pay_day2'],2);
				$day2[$prs['hour']] = $prs['pay_day2'];
			}
			
		}
	}	
	//-------------------------------------------------------------------------------------------
		
	for ($i=0;$i<=23;$i++){
		//$hour_list[str_pad($i,2,"0",STR_PAD_LEFT)] = str_pad($i,2,"0",STR_PAD_LEFT);
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][1] = str_pad($i,2,"0",STR_PAD_LEFT);
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][2] = $today[str_pad($i,2,"0",STR_PAD_LEFT)];
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][3] = $day[str_pad($i,2,"0",STR_PAD_LEFT)];
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][4] = $day2[str_pad($i,2,"0",STR_PAD_LEFT)];
	}	
	
	
	$strXML .= "<graph caption='' subcaption='' xAxisName='' yAxisMinValue='15000' yAxisName='pay count' numberPrefix='' showNames='1' showValues='0' rotateNames='0' showColumnShadow='1' animation='1' showAlternateHGridColor='1' AlternateHGridColor='888888' divLineColor='cccccc'  divLineAlpha='50'  alternateHGridAlpha='5' canvasBorderColor='666666' baseFontColor='666666' decimalPrecision='0' formatNumberScale='0' formatNumber='0' hoverCapSepChar='/' vDivLineThickness='1' numVDivLines='22' vDivLineColor='BBBBBB' hoverCapBgColor='FFFF99'>";

   // 初始化<categories>元素，对于多列图表来说这是必需的。
    $strCategories = "<categories>";

   // 初始化<dataset>元素
    $strDataCurr = "<dataset seriesName='".languagevar('TODAY')."' color='ff6600' lineThickness='4' anchorRadius='6'>";
    $strDataPrev = "<dataset seriesName='".$day_h."' color='6FB7FF' anchorRadius='4' anchorBorderColor='6FB7FF' lineThickness='1'>";
    $strDataPrev2 = "<dataset seriesName='".$day_h2."' color='DDDDDD' anchorRadius='4' anchorBorderColor='CCCCCC' lineThickness='1'>";
// 循环生成各个部分：$strCategories，$strDataCurr，$strDataPrev。
    foreach ($arrData as $arSubData) {
       //Append <category name='...' /> to strCategories
       $strCategories .= "<category name='" . $arSubData[1] . "' />";
      //Add <set value='...' /> to both the datasets
       $strDataCurr .= "<set value='" . $arSubData[2] . "' />";
       $strDataPrev .= "<set value='" . $arSubData[3] . "' />";
       $strDataPrev2 .= "<set value='" . $arSubData[4] . "' />";
    }

   // 结束<categories>元素
    $strCategories .= "</categories>";

// 结束<dataset>元素
    $strDataCurr .= "</dataset>";
    $strDataPrev .= "</dataset>";
    $strDataPrev2 .= "</dataset>";
   // 组合XML字符串
    $strXML .= $strCategories . $strDataPrev2 . $strDataPrev . $strDataCurr . "</graph>";
	$starLineData = renderChartHTML("/style/MSLine.swf", "", $strXML, "myNext", 980, 250);


		
	$db->close();


	 
	include_once template('data_hour_list');
	
}
//--------------------------------------------------------------------------------------------运营商列表
	
function DataCompany() 
{
	global $db,$adminWebName,$adminWebType,$adminWebCid; 
	$order = ReqStr('order');
	if(!$order)
	{
		$order = "pay_amount_t";
	}
	$today_s = date("Y-m-d 00:00:00");
	$today_e = date("Y-m-d 23:59:59");

	if($adminWebType == 'c')//如果不是开发
	{	
		$set_cid = " and A.cid in ($adminWebCid)";
	}	


	//-----------------------------------------------------------------------------

	$yesterday = date('Y-m-d',time()-86400);//昨天
	$thismonth = date('Y-m-01');//月头
	$lastmonths = date('Y-m-01',strtotime("last month"));//上个月头
	$lastmonthe = date('Y-m-t', strtotime('last month'));//上个月尾
	
	$query = $db->query("
	select 
		A.cid,
		A.name,
		A.type,
		sum(if(B.gdate <= '$yesterday',B.pay_amount,0)) as pay_amount,
		sum(if(B.gdate = '$yesterday',B.pay_amount,0)) as pay_amount_y,
		sum(if(B.gdate >= '$thismonth' and B.gdate <= '$yesterday' ,B.pay_amount,0)) as pay_amount_thismonth,
		sum(if(B.gdate >= '$lastmonths' and B.gdate <= '$lastmonthe' ,B.pay_amount,0)) as pay_amount_lastmonth
	from 
		company A
		left join game_data B on A.cid = B.cid
	where
		A.cid > 0
		$set_cid
	group by
		A.cid

	");	

/*	$query = $db->query("
	select 
		A.cid,
		A.name,
		sum(B.pay_amount) AS pay_amount,
		sum(if(B.gdate = DATE_SUB(curdate(), INTERVAL 1 DAY),B.pay_amount,0)) as pay_amount_y
	from 
		company A
		left join game_data B on A.cid = B.cid
	group by
		A.cid
	");*/
	$cnum = $db->num_rows($query);
	if($cnum)
	{
		while($rs = $db->fetch_array($query))
		{
			$rs['pay_amount'] = round($rs['pay_amount'],1);
			$rs['pay_amount_y'] = round($rs['pay_amount_y'],1);
			$rs['pay_amount_thismonth'] = round($rs['pay_amount_thismonth'],1);
			$rs['pay_amount_lastmonth'] = round($rs['pay_amount_lastmonth'],1);
		
			$list_array1[$rs['cid']] =  $rs;
		}	
	}
	//-----------------------------------------------------------------------------


	$query = $db->query("
	select 
		A.cid,
		sum(A.amount) as pay_amount_t
	from 
		pay_data A
		left join company B on A.cid = B.cid
		
	where
		A.success <> 0
		and A.status <> 1
		and A.dtime >= '$today_s' 
		and A.dtime <= '$today_e'
		$set_cid
	group by
		A.cid
	");
	if($db->num_rows($query))
	{
		while($trs = $db->fetch_array($query))
		{
			//$rs['pay_amount'] = 0;
			$trs['pay_amount_t'] = round($trs['pay_amount_t'],1);
			//$rs['pay_amount_y'] = 0;
			$list_array2[$trs['cid']] =  $trs;
		}	
	}

	//$list =  array_add2($list_array1,$list_array2);
	
	//$list=array();
	//$list=array_merge($list_array1,$list_array2);
	//print_r($list);

	//$list_array=array_unique($list);
	//print_r($list_array);


	//print_r($list);
	//-----------------------------------------------------------------------------
	
	$squery = $db->query("
	select 
		A.cid,
		count(A.sid) as s_count,
		count(case when A.open_date > now() then A.sid end) as wait_open_count,
		count(case when A.open = 1 and A.private = 1 and A.open_date <= now() then sid end) as open_count,
		count(case when A.open_date >= '$today_s' and A.open_date <= '$today_e' then A.sid end) as open_today_count,
		count(case when A.is_combined = 1 and A.open_date <= now() then A.sid end) as merger_count
	from 
		servers A
	where
		A.test = 0
		$set_cid		
		
	group by 
		A.cid
	");		
	if($db->num_rows($squery))
	{
		while($srs = $db->fetch_array($squery))
		{
		
			$s[$srs['cid']] = $srs;
		}
	}
/*	//----------------------------------------------统计合服------------------------------------------------------------------
	$mquery = $db->query("
	select 
		A.cid,
		count(case when A.open_date <= now() then A.combined_to end) as open_count
	from 
		servers_merger A
	where
		A.combined_to > 0
		$set_cid		
		
	group by 
		A.cid
	");		
	if($db->num_rows($mquery))
	{
		while($mrs = $db->fetch_array($mquery))
		{
		
			$m[$mrs['cid']] = $mrs;
		}
	}	
*/	
	
	//if ($adminWebName == 'admin') print_r($s);
	//-----------------------------------------------------------------------------
	foreach($list_array1 as $key=>$value){
	  $list[$key] = $list_array2[$key]['pay_amount_t'] ? array_merge($list_array1[$key],$list_array2[$key],$s[$key]) : $list_array1[$key];
	 // print_r($list);
	}		
	$list_array = sysSortArray($list,$order,'SORT_DESC');
	//if ($adminWebName == 'admin') print_r($list_array);
	//-----------------------------------------------------------------------------
	
	
	$db->close();
	include_once template('data_company');
}

/*//--------------------------------------------------------------------------------------------运营商列表
	
function DataCompany() 
{
	global $db; 
	$order = ReqStr('order');
	if($order)
	{
		$set_order = " $order desc,";
	}else{
		$set_order = " pay_amount_t desc,";
	}
	$today_s = date("Y-m-d 00:00:00");
	$today_e = date("Y-m-d 23:59:59");



	//-----------------------------------------------------------------------------


	$query = $db->query("
	select 
		A.cid,
		A.name,
		sum(B.pay_amount) AS pay_amount,
		sum(if(B.gdate = DATE_SUB(curdate(), INTERVAL 1 DAY),B.pay_amount,0)) as pay_amount_y
	from 
		company A
		left join game_data B on A.cid = B.cid
	group by
		A.cid		
	");
	$cnum = $db->num_rows($query);
	if($cnum)
	{
		$i = 1;
		while($rs = $db->fetch_array($query))
		{
			$rs['pay_amount'] = round($rs['pay_amount'],2);
			$rs['pay_amount_y'] = round($rs['pay_amount_y'],2);
			$rs['i'] = $i++;
			$list_array[$rs['cid']] =  $rs;
		}	
	}
	//-----------------------------------------------------------------------------

//
//	$query = $db->query("
//	select 
//		A.cid,
//		A.name,
//		sum(amount) AS pay_amount,
//		sum(if(date_format(B.dtime, '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY),B.amount,0)) as pay_amount_y,
//		sum(if(B.dtime >= '$today_s' and B.dtime <= '$today_e',B.amount,0)) as pay_amount_t
//	from 
//		company A
//		left join pay_data B on A.cid = B.cid and B.success = 1 and B.status = 0
//	group by
//		A.cid
//	order by 
//		$set_order
//		A.corder asc,
//		A.cid asc		
//	");
//	$cnum = $db->num_rows($query);
//	if($cnum)
//	{
//		$i = 1;
//		while($rs = $db->fetch_array($query))
//		{
//			$rs['pay_amount'] = round($rs['pay_amount'],2);
//			$rs['pay_amount_t'] = round($rs['pay_amount_t'],2);
//			$rs['pay_amount_y'] = round($rs['pay_amount_y'],2);
//			$rs['i'] = $i++;
//			$list_array2[$rs['cid']] =  $rs;
//		}	
//	}




	//-----------------------------------------------------------------------------
	
	$squery = $db->query("
	select 
		A.cid,
		count(A.sid) as s_count,
		count(case when A.open_date > now() then A.sid end) as wait_open_count,
		count(case when A.open = 1 and A.private = 1 and A.test = 0 and A.open_date <= now() then sid end) as open_count,
		count(case when A.open_date >= '$today_s' and A.open_date <= '$today_e' then A.sid end) as open_today_count
	from 
		servers A
	group by 
		A.cid
	");		
	if($db->num_rows($squery))
	{
		while($srs = $db->fetch_array($squery))
		{
			$s[$srs['cid']] = $srs;
		}
	}
	//-----------------------------------------------------------------------------
	$db->close();
	include_once template('data_company');
}

*/

//--------------------------------------------------------------------------------------------在线数据
	
function DataOnline() 
{
	global $db,$adminWebName; 
	$mobile = 164;//手机平台CID
	$month = ReqStr('month');
	if(!$month)
	{
		$month = date('Y-m');
	}
	$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   
	for ($i=1;$i<=$day_num;$i++)
	{
		$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
		
	}
	for ($i=0;$i<=23;$i++){
		$hour_list[str_pad($i,2,"0",STR_PAD_LEFT)] = str_pad($i,2,"0",STR_PAD_LEFT);		
	}
	$day_list = array_reverse($day_list); 
	//-----------------------------------------------------------------------------
	$max = $db->fetch_first("select * from max_online");
	if($max){
		$max_online = $max['max_online'];
		$max_online_time = $max['max_online_time'];
	}
	
	
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(from_unixtime(online_time), '%Y-%m')) AS time 
	from 
		`online`		
	order by 
		time desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}
	
	//--------------------------------在线---------------------------------------------
	$query = $db->query("
	select 
		online_count,
		date_format(from_unixtime(`online_time`), '%Y-%m-%d') as day,
		date_format(from_unixtime(`online_time`), '%H') as hour
	from 
		`online`
	where
		date_format(from_unixtime(`online_time`), '%Y-%m') = '$month'
	group by
		day,hour

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
			$data[$rs['day']][$rs['hour']] =  $rs;
		}
	}
	//--------------------------------充值次数---------------------------------------------
	$query = $db->query("
	select 
		count(*) as pay_count,
		date_format(dtime, '%Y-%m-%d') as day,
		date_format(dtime, '%H') as hour
	from 
		`pay_data`
	where
		date_format(dtime, '%Y-%m') = '$month'
		and cid <> '$mobile'
	group by
		day,hour

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
			$pay[$rs['day']][$rs['hour']] =  $rs;
		}
	}
	//if($adminWebName == 'admin') print_r($pay);
	//-----------------------------------------------------------------------------

	include_once("include/FusionCharts.php");
	$day = date('Y-m-d');
	$day_h = ReqStr('day_h');
	$day_h2 = ReqStr('day_h2');
	if (!$day_h) $day_h = date('Y-m-d',time()-86400);

	for ($i=0;$i<=23;$i++){
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][1] = str_pad($i,2,"0",STR_PAD_LEFT);
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][2] = $data[$day][str_pad($i,2,"0",STR_PAD_LEFT)]['online_count'];
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][3] = $data[$day_h][str_pad($i,2,"0",STR_PAD_LEFT)]['online_count'];
		$arrData[str_pad($i,2,"0",STR_PAD_LEFT)][4] = $data[$day_h2][str_pad($i,2,"0",STR_PAD_LEFT)]['online_count'];

		$arrData2[str_pad($i,2,"0",STR_PAD_LEFT)][1] = str_pad($i,2,"0",STR_PAD_LEFT);
		$arrData2[str_pad($i,2,"0",STR_PAD_LEFT)][2] = $pay[$day][str_pad($i,2,"0",STR_PAD_LEFT)]['pay_count'];
		$arrData2[str_pad($i,2,"0",STR_PAD_LEFT)][3] = $pay[$day_h][str_pad($i,2,"0",STR_PAD_LEFT)]['pay_count'];
		$arrData2[str_pad($i,2,"0",STR_PAD_LEFT)][4] = $pay[$day_h2][str_pad($i,2,"0",STR_PAD_LEFT)]['pay_count'];
		
		
	}


	$strXML .= "<graph caption='' subcaption='' xAxisName='' yAxisMinValue='15000' yAxisName='online count' numberPrefix='' showNames='1' showValues='0' rotateNames='0' showColumnShadow='1' animation='1' showAlternateHGridColor='1' AlternateHGridColor='888888' divLineColor='cccccc'  divLineAlpha='50'  alternateHGridAlpha='5' canvasBorderColor='666666' baseFontColor='666666' decimalPrecision='0' formatNumberScale='0' formatNumber='0' hoverCapSepChar='/' vDivLineThickness='1' numVDivLines='22' vDivLineColor='BBBBBB' hoverCapBgColor='FFFF99'>";

    $strCategories = "<categories>";

    $strDataCurr = "<dataset seriesName='今日' color='ff6600' lineThickness='4' anchorRadius='6'>";
    $strDataPrev = "<dataset seriesName='".$day_h."' color='6FB7FF' anchorRadius='4' anchorBorderColor='6FB7FF' lineThickness='1'>";
    $strDataPrev2 = "<dataset seriesName='".$day_h2."' color='DDDDDD' anchorRadius='4' anchorBorderColor='CCCCCC' lineThickness='1'>";
    foreach ($arrData as $arSubData) {
       $strCategories .= "<category name='" . $arSubData[1] . "' />";
       $strDataCurr .= "<set value='" . $arSubData[2] . "' />";
       $strDataPrev .= "<set value='" . $arSubData[3] . "' />";
       $strDataPrev2 .= "<set value='" . $arSubData[4] . "' />";
    }

    $strCategories .= "</categories>";

    $strDataCurr .= "</dataset>";
    $strDataPrev .= "</dataset>";
    $strDataPrev2 .= "</dataset>";
    $strXML .= $strCategories . $strDataPrev2 . $strDataPrev . $strDataCurr . "</graph>";
	$starLineData = renderChartHTML("/style/MSLine.swf", "", $strXML, "myNext", 980, 250);

	//---------------------------------------------------------------------------------------------------------
	$strXML2 .= "<graph caption='' subcaption='' xAxisName='' yAxisMinValue='15000' yAxisName='pay count' numberPrefix='' showNames='1' showValues='0' rotateNames='0' showColumnShadow='1' animation='1' showAlternateHGridColor='1' AlternateHGridColor='888888' divLineColor='cccccc'  divLineAlpha='50'  alternateHGridAlpha='5' canvasBorderColor='666666' baseFontColor='666666' decimalPrecision='0' formatNumberScale='0' formatNumber='0' hoverCapSepChar='/' vDivLineThickness='1' numVDivLines='22' vDivLineColor='BBBBBB' hoverCapBgColor='FFFF99'>";

    $strCategories2 = "<categories>";

    $strDataCurr2 = "<dataset seriesName='".languagevar('TODAY')."' color='ff6600' lineThickness='4' anchorRadius='6'>";
    $strDataPrev2 = "<dataset seriesName='".$day_h."' color='6FB7FF' anchorRadius='4' anchorBorderColor='6FB7FF' lineThickness='1'>";
    $strDataPrev22 = "<dataset seriesName='".$day_h2."' color='DDDDDD' anchorRadius='4' anchorBorderColor='CCCCCC' lineThickness='1'>";
    foreach ($arrData2 as $arSubData2) {
       $strCategories2 .= "<category name='" . $arSubData2[1] . "' />";
       $strDataCurr2 .= "<set value='" . $arSubData2[2] . "' />";
       $strDataPrev2 .= "<set value='" . $arSubData2[3] . "' />";
       $strDataPrev22 .= "<set value='" . $arSubData2[4] . "' />";
    }

    $strCategories2 .= "</categories>";

    $strDataCurr2 .= "</dataset>";
    $strDataPrev2 .= "</dataset>";
    $strDataPrev22 .= "</dataset>";
    $strXML2 .= $strCategories2 . $strDataPrev22 . $strDataPrev2 . $strDataCurr2 . "</graph>";
	$starLineData2 = renderChartHTML("/style/MSLine.swf", "", $strXML2, "myNext", 980, 250);

	//---------------------------------------------------------------------------------------------------------

	$db->close();
	include_once template('data_online');
}


//--------------------------------------------------------------------------------------------新注册用户曲线
	
function DataNewUser() 
{
	global $db,$adminWebName; 
	$month = ReqStr('month');
	if(!$month)
	{
		$month = date('Y-m');
	}
	$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   
	for ($i=1;$i<=$day_num;$i++)
	{
		$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
		
	}

	//$day_list = array_reverse($day_list); 
	
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(gdate, '%Y-%m')) AS gdate 
	from 
		game_data	
	order by 
		gdate desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}

	//--------------------------------最高---------------------------------------------
	$maxRegNum = 0;
	$maxCreNum = 0;
	//--------------------------------注册与创建---------------------------------------------
	$query = $db->query("
	select 
		gdate,
		sum(register_count) as reg_count,
		sum(create_count) as cre_count
	from 
		game_data
	where
		date_format(`gdate`, '%Y-%m') = '$month'
	group by
		gdate

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
		
			if($maxRegNum < $rs['reg_count']) $maxRegNum = $rs['reg_count'];
			if($maxCreNum < $rs['cre_count']) $maxCreNum = $rs['cre_count'];
			$data[$rs['gdate']] =  $rs;
		}
	}


	//---------------------------------------------------------------------------------------------------------

	$db->close();
	include_once template('data_new_user');
}


//--------------------------------------------------------------------------------------------月充值报表
	
function DataMonthPay() 
{
	global $db,$adminWebName,$adminWebType,$adminWebCid; 
	$mobile = 164;//手机平台CID
	$cid = ReqStr('cid');
	if($cid) 
	{
		$set_cid = " and cid = '$cid'";
	}
	if($adminWebType == 'c')//如果不是开发
	{	
		$set_cid2 = " and cid in ($adminWebCid)";
		$set_cid3 = " cid in ($adminWebCid)";
	}	
	$company_list = globalDataList('company',$set_cid3,'corder asc,cid asc');//运营商		
	$amount_all = round($db->result($db->query("select sum(pay_amount) from game_data where gid > 0  and cid <> '$mobile' $set_cid $set_cid2"),0),1);

	//-----------------------------------------------------------------------------
	$query = $db->query("
	select 
		date_format(`gdate`, '%Y-%m') as month,
		sum(`pay_amount`) as pay_amount
	from 
		game_data
	where
		gdate <> '0000-00-00'
		and cid <> '$mobile'
		$set_cid
		$set_cid2
	group by
		month
	order by
		month desc

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
			if($maxAmount < $rs['pay_amount']) $maxAmount = $rs['pay_amount'];
			$rs['pay_amount'] = round($rs['pay_amount'],1);
			$month[$rs['month']] =  $rs;
		}
	}

	//---------------------------------------------------------------------------------------------------------

	$db->close();
	include_once template('data_month_pay');
}

?>