<?php
include_once(dirname(__FILE__)."/config.inc.php");
include_once(dirname(__FILE__)."/conn.php");
if (!$adminWebID)
{
	exit();		
}
@include language($adminWebLang);
header("expires:mon,26jul199705:00:00gmt"); 
header("cache-control:no-cache,must-revalidate"); 
header("pragma:no-cache");//禁止缓存
header("Content-Type:text/html;charset=utf-8");//避免输出乱码

switch (ReqStr('action'))
{
	case 'CallNowOnline': webAdmin('key_data_set');CallNowOnline();break;
	case 'CallPayExport': webAdmin('data_export');CallPayExport();break;
	case 'CallCheckServers': CallCheckServers();break;
	case 'CallServers': CallServers();break;
	case 'CallServersSet': webAdmin('server_open');CallServersSet();break;
	case 'CallServersMergerSet': webAdmin('server_open');CallServersMergerSet();break;
	case 'CallCompanyServers': CallCompanyServers();break;
	case 'CallCompanyServersMerger': CallCompanyServersMerger();break;
	case 'CallCompanyServersDomain': CallCompanyServersDomain();break;
	case 'CallCompanyServersUrl': CallCompanyServersUrl();break;
	case 'CallOpenDateServers': CallOpenDateServers();break;
	case 'CallSetupPower': CallSetupPower();break;	
	case 'CallTodayLogin': CallTodayLogin();break;
	case 'CallTodayPay': serverAdmin('pay');CallTodayPay();break;
	case 'CallTodayPayS': serverAdmin('pay');CallTodayPayS();break;
	case 'CallPlayerRole': CallPlayerRole();break;
	case 'CallApplySet': CallApplySet();break;
	case 'CallApplyReply': CallApplyReply();break;
	case 'CallCodeBatch': CallCodeBatch();break;
	case 'CallCodeExport': CallCodeExport();break;
	case 'CallCodePartyExport':webAdmin('code_set');  CallCodePartyExport();break;
	case 'CallGmReply': CallGmReply();break;
	case 'CallReplyBug': CallReplyBug();break;
	case 'CallOnline':webAdmin('key_data_set'); CallOnline();break;
	case 'CallOnlineDetail':webAdmin('key_data_set'); CallOnlineDetail();break;
	//case 'CallGiftDataCopy': CallGiftDataCopy();break;
}
//-------------------------------------------------------------------------------------------详细在线报表
function  CallOnlineDetail() {
	global $db,$sid;
	$day = ReqStr('day');
	$day_s = strtotime($day.' 00:00:00');
	$day_e = strtotime($day.' 23:59:59');
	//-----------在线------------------------------------------------------------
	$query = $db->query("
	select 		

		max(online_count) as maxOnlineNum
	from 
		online_detail
	where 
		`online_time` >= '$day_s' 
		and `online_time` <= '$day_e' 
	");
	if($db->num_rows($query)){
		$ssrs = $db->fetch_array($query);
		$maxOnlineNum = $ssrs['maxOnlineNum'];//-----------最大在线
		
	}
	
	
	$query = $db->query("
	select 
		*,
		date_format(from_unixtime(`online_time`), '%H:%i') as t
	from 
		online_detail
	where 
		`online_time` >= '$day_s' 
		and `online_time` <= '$day_e' 

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
			$array[$rs['t']] =  $rs;
		}
	}
	//print_r(var_export($array));
	$db->close();

	include_once template('data_online_detail');
}
//-------------------------------------------------------------------------------------------总在线报表
function  CallOnline() {
	global $db;
	$day = ReqStr('day');
	
	for ($i=0;$i<=23;$i++){
		$hour_list[str_pad($i,2,"0",STR_PAD_LEFT)] = str_pad($i,2,"0",STR_PAD_LEFT);
		
	}	
	
	//-----------------------------------------------------------------------
	$query = $db->query("
	select
		max(online_count) as maxOnlineNum,
		sum(online_count) as allOnlineNum,
		count(*) as hour_count
	from 
		`online`
	where 
		 date_format(from_unixtime(`online_time`), '%Y-%m-%d') = '$day'
	");
	if($db->num_rows($query)){
		$ssrs = $db->fetch_array($query);
		$maxOnlineNum = $ssrs['maxOnlineNum'];//-----------最大在线
		$allOnlineNum = $ssrs['allOnlineNum'];//-----------所有在线
		$hour_count = $ssrs['hour_count'];//-----------小时数
		
	}	
	$query = $db->query("
	select 
		*,
		date_format(from_unixtime(`online_time`), '%H') as hour
	from 
		`online`
	where 
		date_format(from_unixtime(`online_time`), '%Y-%m-%d') = '$day' 

	");
	if($db->num_rows($query)){				
		while($rs = $db->fetch_array($query)){	
			$array[$rs['hour']] =  $rs;
		}
	}
	//print_r(var_export($array));
	$db->close();

	include_once template('online_hour');
}
//--------------------------------------------------------------------------------------------在线人数
function CallNowOnline() {
	include_once(dirname(__FILE__)."/online_data.php");
	echo $total_online_num;
}
function  CallPayExport() {

	global $db,$adminWebType,$adminWebCid;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	if(!$cid){
		echo languagevar('ERROR');
		return;	
	}	
	if($adminWebType == 'c'){
		$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
		if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			echo languagevar('NOPOWER');
			return;
		}		
				
	}elseif($adminWebType == 'u'){
		echo languagevar('NOPOWER');
		return;			
	}
	//---------------------------------------------------------------------


	if ($stime && $etime) 
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') >= '$stime' and date_format(dtime, '%Y-%m-%d') <= '$etime'";
		$day_title = "(".$stime."-".$etime.")";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$stime'";
		$day_title = "(".$stime.")";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$etime'";
		$day_title = "(".$etime.")";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}
	
	//---------------------------------------------------------------------
	$query = $db->query("
	select 
		A.name,
		A.o_name,
		B.name as company_name
	from 
		servers A
		left join company B on A.cid = B.cid
	where 
		A.cid = '$cid'
		$set_sid
	");
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);
		$ss = $sid ? '-'.$server['name'].'-'.$server['o_name'] : '';
		$sn = $server['company_name'].$ss;
	}
	//---------------------------------------------------------------------	
	
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".urlencode($sn.$day_title).".xls");//$filename   导出文件名; 
	header("Pragma: public"); 	

	//-----------收入/待充/测试------------------------------------------------------------
	$query = $db->query("
	select 		
		sum(A.amount) as amount 
	from 
		pay_data A
	where 
		A.cid = '$cid'
		and A.success = 1 and A.status = 0		
		$set_day
		$set_sid
	");
	if($db->num_rows($query)){
		$mrs = $db->fetch_array($query);
		$amount = round($mrs['amount'],2);//-----------收入
	}
	//---------------------------------------------------------------------
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<body>';	
	echo '<table width="500" border="1" cellpadding="2" cellspacing="0">';
	echo '<tr>';
	echo "<td colspan=\"5\" height=\"30\"><strong>".$sn.$day_title."</strong></td>"; 
	echo '</tr>';
	echo '<tr>';
	echo "<td colspan=\"5\">".languagevar('PAY').":<strong>".$amount."</strong> 元</td>"; 
	echo '</tr>';
	
	echo '<tr>';
	echo '
	<td><strong>'.languagevar('SERVER').'</strong></td>
	<td><strong>'.languagevar('USER').'</strong></td>
	<td><strong>'.languagevar('PAYORDER').'</strong></td>
	<td><strong>'.languagevar('PAY').'</strong></td>
	<td><strong>'.languagevar('PAYTIME').'</strong></td>
	';
	echo '</tr>';
	$query = $db->query("
	select 
		A.*,
		B.name as servers_name,
		B.o_name as servers_o_name,
		C.name as company_name
	from 
		pay_data A 
		left join servers B on A.sid = B.sid
		left join company C on A.cid = C.cid
	where 
		A.cid = '$cid'	
		and A.success = 1 and A.status = 0	
		$set_day 
		$set_sid
	order by
		A.dtime asc 
	");	
	if($db->num_rows($query)){

		while($prs = $db->fetch_array($query)){	
			
			echo '<tr>';
			echo '
			<td>'.$prs['company_name'].'-'.$prs['servers_name'].'-'.$prs['servers_o_name'].'</td>
			<td><strong>'.$prs['username'].'</strong> ('.$prs['nickname'].')</td>
			<td style="vnd.ms-excel.numberformat:@">'.$prs['oid'].'</td>
			<td>'.$prs['amount'].'</td>
			<td>'.$prs['dtime'].'</td>
			';
			echo '</tr>';
			
		}
	}
	
	echo '</table>';
	echo '</body></html>';
	$db->close();
}
//--------------------------------------------------------------------------------------------检查游戏地址是否存在
	
function CallCheckServers() 
{
	global $db; 
	$sid=ReqNum('sid');
	$server=ReqStr('server');
	//echo $sid.$server;
	$serverArr = explode("\r\n",$server);
	$query = $db->query("select server from servers where sid <> '$sid'");
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{	
			$serverArrAll = $rs['server'] ? explode(",",$rs['server']) : array();
			for ($i=0;$i<=count($serverArr);$i++){
				if (in_array($serverArr[$i],$serverArrAll))
				{
					echo $serverArr[$i].languagevar('EXIST').'<br />';
					return;
				}
			}
		
		}
	}

	$db->close();
}
//--------------------------------------------------------------------------------------------服务器详细设置
	
function CallServersSet() 
{
	global $db; 
	$sid=ReqNum('sid');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	if(!$sid){
		echo languagevar('ERROR');
		return;	
	}		
	
	
	$rs = $db->fetch_first("select A.*,B.name as company_name from servers A left join company B on A.cid = B.cid where A.sid = '$sid'");
	if(!$rs)
	{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	$db->close();
	include_once template('c_servers_set');
}
//--------------------------------------------------------------------------------------------服务器合服修改
	
function CallServersMergerSet() 
{
	global $db,$adminWebCid; 
	$id=ReqNum('id');
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	if(!$id){
		echo languagevar('ERROR');
		return;	
	}		
	
	//------------------------------------------------------------------------------------------------	
	$query = $db->query("select sid_m from servers_merger where cid  = '$cid' and id <> '$id'");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$sid_m .= $sid_m ? ','.$rs['sid_m'] : $rs['sid_m'];	
		}
		$set_m = " and A.sid not in ($sid_m)";
	}
		
	//------------------------------------------------------------------------------------------------	
	$rs = $db->fetch_first("
	select 
		A.*,
		B.name as company_name,
		C.name as sname
	from 
		servers_merger A 
		left join company B on A.cid = B.cid 
		left join servers C on A.combined_to = C.sid 
	where 
		A.id = '$id'
	");
	if(!$rs)
	{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	//$servers_list = globalDataList('servers',"cid = '$cid' and (combined_to = 0 or sid in ($rs[sid_m])) $set_m",'open_date desc,sid desc');//服务器	
	
	
	
	$query = $db->query("
	select 
		A.sid,
		A.name,
		A.o_name,
		B.name2
	from 
		servers A
		left join servers_address B on A.api_server = B.name
	where 
		A.cid  = '$cid' 
		and (combined_to = 0 or sid in ($rs[sid_m])) 
		and A.sid  <> '$sid' 
		$set_m
	order by 
		A.open_date desc,
		A.sid desc
	");
	if($db->num_rows($query))
	{
		while($srs = $db->fetch_array($query))
		{
			if ($srs["name2"]) $srs["name2"] = '('.languagevar('JIFANG').$srs["name2"].')';
			$servers_list[] = $srs;
		}
	}
	
	$ism = $db->result($db->query("
	select 		
		count(*)
	from 
		servers
	where 
		cid  = '$cid' 
		and sid in ($rs[sid_m]) 
		and combined_to > 0
	"),0);	
	
	$mergerServersArr = $rs['sid_m'] ? explode(',',$rs['sid_m']) : array();
	
	
	$db->close();
	include_once template('c_servers_merger_set');
}
function  CallCodeExport() {

	global $db,$adminWebType;
	$sid = ReqNum('sid');
	$batch_id = ReqNum('batch_id');
	$title = urlencode(ReqStr('title'));
	$number = ReqNum('number');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');	
	}elseif($adminWebType == 'c'){
		global $cid;

	}
	if(!$cid || !$sid){
		echo languagevar('ERROR');
		return;	
	}		
	if($cid)
	{
		$set_cid = "and B.cid = '$cid'";
	}
	if($sid)
	{
		$set_sid = "and B.sid = '$sid'";
	}		
	if($batch_id)
	{
		$set_batch = "and A.batch_id = '$batch_id'";
	}	
	if($number)
	{
		$set_number = "and A.number = '$number'";
	}	


	header('Content-Type: application/octet-stream'); 
	header("Content-Disposition:attachment;filename=".$title.".txt");//$filename   导出文件名; 
	header("Pragma: public"); 	

	
/*	header('Content-Type: application/octet-stream'); 
	header("Content-Disposition:attachment;filename=".$title.".txt");//$filename   导出文件名; 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 				
*/	//---------------------------------------------------------------------
	

	$query = $db->query("
	select 
		A.*
	from 
		code A
		left join code_batch B on A.batch_id = B.id
	where 
		A.id <> 0
		$set_cid
		$set_sid
		$set_batch
		$set_number
	order by
		A.id desc 
	");	
	while($rs = $db->fetch_array($query)){	
		
		echo $rs['code']."\r\n";
	}

	$db->close();
}


function  CallCodePartyExport() {

	global $db,$adminWebType;
	$cid = ReqNum('cid');
	$p_db = ReqStr('p_db');
	$number = ReqNum('number');
/*	if(!$cid){
		echo languagevar('ERROR');
		return;	
	}	*/	
	if($cid)
	{
		$set_cid = "and cid = '$cid'";
	}
	if($number)
	{
		$set_number = "and number = '$number'";
	}	
	$p_f_db = 'code_party_'.$p_db;

	header('Content-Type: application/octet-stream'); 
	header("Content-Disposition:attachment;filename=".$p_db."_No.".$number.".txt");//$filename   导出文件名; 
	header("Pragma: public"); 	

	
	//---------------------------------------------------------------------
	

	$query = $db->query("
	select 
		*
	from 
		$p_f_db
	where 
		player_id = 0
		$set_cid
		$set_number
	order by
		id desc 
	");	
	while($rs = $db->fetch_array($query)){	
		
		echo $rs['code']."\r\n";
	}

	$db->close();
}

//-------------------------------------------------------------------------------------------物品申请操作
function  CallApplySet() {
	global $db,$adminWebCid; 
	$aid = ReqNum('aid');
	$cid = ReqNum('cid');
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();

	if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
		return;
	}		
	
	$query = $db->query("
	select 
		A.*,
		B.name as servers_name,
		C.name as company_name
	from 
		apply_data A
		left join servers B on A.sid = B.sid
		left join company C on A.cid = C.cid
	where 
		A.aid = '$aid'
	");
	if($db->num_rows($query))
	{
		$rs = $db->fetch_array($query);
		if($rs['status'] == 3){
			echo languagevar('WPSQADDYPZ');
			return;
		}elseif($rs['status'] == 2){
			echo languagevar('WPSQADDYGB');
			return;
		}	

		
		include_once template('setting_apply_set');
		
	}else{
		echo languagevar('NULL');
	}
	$db->close();
}

//-------------------------------------------------------------------------------------------物品申请回复
function  CallApplyReply() {
	global $db,$adminWebCid; 
	$aid = ReqNum('aid');
	$cid = ReqNum('cid');

	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();

	if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
		echo languagevar('NOPOWER');
		return;
	}		
	$rs = $db->fetch_first("
	select 		
		A.*,
		B.name as servers_name,
		C.name as company_name

	from 
		apply_data A
		left join servers B on A.sid = B.sid
		left join company C on A.cid = C.cid

	where
		A.aid = '$aid'
	");
	$db->close();

	include_once template('setting_apply_reply');
}


/*//-------------------------------------------------------------------------------------------反馈回复
function  CallGmReply() {
	global $db,$adminWebType,$adminWebCid; 
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');	
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
	
	if($adminWebType == 'c'){
		if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			echo languagevar('NOPOWER');
			return;
		}		
				
	}elseif($adminWebType == 'u'){
		global $adminWebServers;
		if ($adminWebServers) 
		{
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				echo languagevar('NOPOWER');
				return;			
			}
		}		
	}
	if($sid)
	{	
		$query = $db->query("select * from servers where sid = '$sid'");
		if($db->num_rows($query))
		{
			$server = $db->fetch_array($query);
		
			$pdbhost = SetToDB($server['db_server']);//数据库服务器
			$pdbuser = $server['db_root'];//数据库用户名
			$pdbpw = $server['db_pwd'];//数据库密码
			$pdbname = $server['db_name'];//数据库名	
			$pdbcharset = 'utf8';//数据库编码,不建议修改.
			$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
			//-----------------------------------------------------------------------------------------------
			$pdb = new mysql();
			$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
			unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			
			$query = $pdb->query("
			select 		
				A.*,
				B.username,
				B.nickname				
			from 
				player_bug A
				left join player B on A.player_id = B.id
			where 
				 A.id = '$id'
			");
			if($pdb->num_rows($query)){
				$rs = $pdb->fetch_array($query);
			}
			$pdb->close();
		}else{
			echo languagevar('NOSERVER');
		}	
		
	}
	$db->close();

	include_once template('setting_gm_reply');
}
*/

//-------------------------------------------------------------------------------------------反馈回复
function  CallReplyBug() {
	global $odb,$adminWebType,$adminWebCid; 
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');	
	$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
	
	if($adminWebType == 'c'){
		if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			echo languagevar('NOPOWER');
			return;
		}		
				
	}elseif($adminWebType == 'u'){
		global $adminWebServers;
		if ($adminWebServers) 
		{
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				echo languagevar('NOPOWER');
				return;			
			}
		}		
	}
	if($sid)
	{	

			
		$query = $odb->query("
		select 		
			*				
		from 
			gm_bug 
		where 
			 id = '$id'
		");
		if($odb->num_rows($query)){
			$rs = $odb->fetch_array($query);
		}
		$odb->close();

		
	}


	include_once template('bug_reply');
}
function CallCodeBatch() {//调用激活码批次
	global $db;
	$sid = ReqNum('sid');
	$query = $db->query("select * from code_batch where sid = '$sid'");
	echo "obj.options[obj.options.length] = new Option('".languagevar('DHQXZPC')."','0');\n"; 
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."(".languagevar('CREATE').":".$rs["ctime"].")','".$rs["id"]."');\n"; 
		}

	}
	$db->close();
}
function CallServers() {//调用服务器列表
	global $db,$adminWebServers,$adminWebType,$adminWebCid;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	//$url = urldecode(ReqStr('url'));
	$url = ReqStr('url');
	//echo $url;

	if ($adminWebType == 'u' && $adminWebServers) $set_sid_arr = " and sid in ($adminWebServers)";
	if ($adminWebType == 'u') $set_cid_arr = " and cid in ($adminWebCid)";
	$query = $db->query("select sid,name,o_name,private,open from servers where cid  = '$cid' $set_sid_arr $set_cid_arr order by sid desc");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			//$str = preg_replace("/(&amp;username\=(\d+))/is","&username=".urlencode('\\1'),$url);
		//	$str = preg_replace("/(&amp;sid\=(\d+))/is","&sid=".$rs['sid'],$url);
			$rs['new_url'] = preg_replace("/(&amp;sid\=(\d+))/is","&sid=".$rs['sid'],$url);
			$list_array[] = $rs;
		}
		if ($list_array) $rows = array_chunk($list_array,5); 	
	}
	$db->close();
	include_once template('servers_list');
}

function CallCompanyServers() {//调用服务器
	global $db,$adminWebServers,$adminWebType,$adminWebCid;
	$cid = ReqNum('cid');
	if ($adminWebType == 'u' && $adminWebServers) $set_sid_arr = " and sid in ($adminWebServers)";
	if ($adminWebType == 'u') $set_cid_arr = " and cid in ($adminWebCid)";
	$query = $db->query("select * from servers where cid  = '$cid'  and combined_to = 0 $set_sid_arr $set_cid_arr order by open_date desc,sid desc");
	echo "obj.options[obj.options.length] = new Option('".languagevar('ALLSERVER')."','0');\n"; 
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			//if (!$rs["open"]) $rs["is_open"] = '(关服)';
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."-".$rs["o_name"].$rs["is_open"]."','".$rs["sid"]."');\n"; 
		}
	}
	$db->close();
}

function CallCompanyServersMerger() {//调用服务器合服
	global $db;
	$cid = ReqNum('cid');
	
	$query = $db->query("select sid_m from servers_merger where cid  = '$cid'");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			$sid_m .= $sid_m ? ','.$rs['sid_m'] : $rs['sid_m'];	
		}
		$set_m = " and A.sid not in ($sid_m)";
	}
	
	
	
	
	$query = $db->query("
	select 
		A.sid,
		A.name,
		A.o_name,
		B.name2
	from 
		servers A
		left join servers_address B on A.api_server = B.name
	where 
		A.cid  = '$cid' and A.combined_to = 0 $set_m 
	order by 
		A.open_date desc,
		A.sid desc
	");
	echo "obj.options[obj.options.length] = new Option('".languagevar('ALLSERVER')."','0');\n"; 
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			if ($rs["name2"]) $rs["name2"] = '('.languagevar('JIFANG').$rs["name2"].')';
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."-".$rs["o_name"].$rs["name2"]."','".$rs["sid"]."');\n"; 
		}
	}
	$db->close();
}

function CallCompanyServersDomain() {//调用服务器取二级地址
	global $db;
	$cid = ReqNum('cid');
	$query = $db->query("select * from servers where cid  = '$cid' order by open_date desc,sid desc");
	echo "obj.options[obj.options.length] = new Option('".languagevar('ALLSERVER')."','0');\n"; 
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			if (!$rs["open"]) $rs["is_open"] = '('.languagevar('WPZ').')';
			$server = explode(',',$rs['server']);
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."-".$rs["o_name"].$rs["is_open"]."','".$server[0]."');\n"; 
		}
	}
	$db->close();
}
function CallOpenDateServers() {//调用日期范围内开服的服务器
	global $db,$adminWebCid,$adminWebType;
	$cid = ReqNum('cid');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	if ($stime && $etime && $stime < $etime) 
	{
		$stime = $stime.' 00:00:00';
		$etime = $etime.' 23:59:59';
		//echo "obj.options[obj.options.length] = new Option('".languagevar('DATEERR')."','0');\n"; 
		//return;
		$set_time = " and A.open_date >= '$stime' AND A.open_date <= '$etime' ";
	}
	
	if($adminWebType != 's')//如果不是开发
	{
		$set_cid = " and A.cid in ($adminWebCid)";
	}		
	if($cid)
	{
		$set_cid2 = " and A.cid = '$cid'";
	}		
	
	
	$query = $db->query("
	select 
		A.sid,
		A.server,
		A.name,
		A.o_name,
		A.open_date,
		B.name as company_name 
	from 
		servers A 
		left join company B on A.cid = B.cid 
	where 
		A.open = 1
		and A.combined_to = 0
		$set_cid
		$set_cid2
		$set_time
	order by 
		B.cid asc,
		A.open_date desc,
		A.sid desc
	");
	$num = $db->num_rows($query);
	if($num)
	{	

		$GLOBALS['num']=$num;
		echo "obj.options[obj.options.length] = new Option('-------------------".langmsg('GYJF')."-------------------','0');\n"; 
		while($rs = $db->fetch_array($query)){
			$rs['open_date'] = date('Y-m-d',strtotime($rs['open_date']));
			echo "obj.options[obj.options.length] = new Option('[".$rs['open_date'].']'.$rs["company_name"]."-".$rs["name"]."-".$rs["o_name"]." (".$rs["server"].")','".$rs["sid"]."');\n"; 
		}
	}else{
		echo "obj.options[obj.options.length] = new Option('".languagevar('NULL')."','0');\n"; 
	}
	$db->close();
}
function CallCompanyServersUrl() {//调用服务器
	global $db;
	$companyArr = ReqStr('companyArr');
	$cid =  substr($companyArr,0,strlen($companyArr)-1);
	$query = $db->query("select A.sid,A.server,A.name,A.o_name,B.name as company_name from servers A left join company B on A.cid = B.cid where A.cid in ($cid) order by B.corder asc,A.open_date desc,A.sid desc");
	while($rs = $db->fetch_array($query)){
		echo "obj.options[obj.options.length] = new Option('".$rs["company_name"]."-".$rs["name"]."-".$rs["o_name"]." (".$rs["server"].")','".$rs["sid"]."');\n"; 
	}
	$db->close();
}
function CallSetupPower() {//调用系统权限
	global $db;
	$adminType = ReqStr('adminType');
	$query = $db->query("select * from setup_power where ptype  = '$adminType' order by porder asc");
	echo "obj.options[obj.options.length] = new Option('无','');\n"; 
	while($rs = $db->fetch_array($query)){
		echo "obj.options[obj.options.length] = new Option('".$rs["pname"]."','".$rs["power"]."');\n"; 
	}
	$db->close();
}

function  CallTodayLogin() {
	global $db; 
	$sid = ReqNum('sid');
	if(!$sid)
	{
		echo languagevar('ERROR');	
		return;	
	}

	$today_login = $db->result($db->query("
	select 		
		login_count
	from 
		game_data
	where 
		sid = '$sid'
		and gdate = curdate()
	"),0);
	echo $today_login ? $today_login : 0;
	$db->close();
}

function  CallTodayPay() {
	global $db,$adminWebType,$adminWebCid; 
	if($adminWebType != 's') $set_cid = " and cid in ($adminWebCid)";
	$day_s = date("Y-m-d 00:00:00");
	$day_e = date("Y-m-d 23:59:59");
	
	$today_pay = $db->result($db->query("
	select 		
		sum(amount)
	from 
		pay_data
	where 
		dtime >= '$day_s' 
		and dtime <= '$day_e'
		and status <> 1	
		and success <> 0	
		$set_cid
	"),0);
	echo round($today_pay,2);
	$db->close();
}

	
	
function  CallTodayPayS() {
	global $db,$adminWebType,$adminWebCid; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	if(empty($sid) || empty($cid))
	{
		echo languagevar('ERROR');
		return;
	}
	if($adminWebType != 's'){	
		$set_cid_in = " and cid in ($adminWebCid)";
	}
	
	if($adminWebType == 'u'){	
		global $adminWebServers;
		if ($adminWebServers) 
		{
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				echo languagevar('NOPOWER');
				return;			
			}
		}
	}	
	
	
	if(empty($sid))
	{
		$set_sid = " and sid = '$sid'";
	}
	$day_s = date("Y-m-d 00:00:00");
	$day_e = date("Y-m-d 23:59:59");
	
	$today_pay = $db->result($db->query("
	select 		
		sum(amount)
	from 
		pay_data
	where 
		dtime >= '$day_s' 
		and dtime <= '$day_e'
		and sid = '$sid'
		and cid = '$cid'
		and status <> 1	
		and success <> 0
		$set_cid_in
	"),0);
	echo round($today_pay,2);
	$db->close();
}
/*function CallGiftDataCopy() 
{
	global $db,$adminWebType;
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$id=ReqNum('id');
	$type=ReqNum('type');
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	$servers_list = globalDataList('servers',"cid = '$cid'");//服务器	
	
	include_once template('setting_gift_data_copy');
}*/

?>