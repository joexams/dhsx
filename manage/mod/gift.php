<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
function  GiftDataServers() {
	global $db,$adminWebType,$page;
	$gift_data_id = ReqNum('gift_data_id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'",'sid desc');//服务器	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'",'sid desc');//服务器
		$set_cid_arr = "and A.cid in ($adminWebCid)";
			
	}
	
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if($gift_data_id)
	{
		$set_gift = "and A.gift_data_id = '$gift_data_id'";
	}	

	$gift_data_list = globalDataList('gift_data','`default` = 0');//活动模版				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		gift_data_servers A
		left join gift_data B on A.gift_data_id = B.id
		left join servers C on A.sid = C.sid
		left join company D on A.cid = D.cid
		
	where 
		A.gid <> 0
		$set_cid
		$set_sid	
		$set_gift	
		$set_cid_arr
	"),0); //获得总条数
	if($num){
		$i = 1;
		$query = $db->query("
		select 
			A.*,
			B.*,
			C.name as servers_name,
			C.o_name as servers_o_name,
			D.name as company_name,
			E.adminName
		from 
			gift_data_servers A
			left join gift_data B on A.gift_data_id = B.id
			left join servers C on A.sid = C.sid
			left join company D on A.cid = D.cid
			left join admin E on A.adminID = E.adminID
		where 
			A.gid <> 0
			$set_cid
			$set_sid
			$set_gift
			$set_cid_arr
		order by
			A.gid desc,
			A.sid desc,
			B.type desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			if ($rs['etime'] == '9999-01-01 00:00:00') 
			{
				$rs['etime'] = '9999-01-01 00:00';
			}else{
				$rs['etime'] = date('Y-m-d H:i',strtotime($rs['etime']));
			}
			$rs['stime'] = date('Y-m-d H:i',strtotime($rs['stime']));
			$rs['i'] = $i++;
			$list_array[] = $rs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=gift&action=GiftDataServers&cid=$cid&sid=$sid&gift_data_id=$gift_data_id");	
	}	
	$db->close();
	include template('setting_gift_data_s');
}	



 //--------------------------------------------------------------------------------------------增加活动

function  GiftDataServersAdd() {
	global $db,$adminWebID,$adminWebType,$adminWebName; 
	//$company_list = globalDataList('company');//运营商


	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
				
	}
	$gift_data_list = globalDataList('gift_data','`default` = 0');//活动模版	
	
	
	$db->close();
	include_once template('setting_gift_data_s_add');
}
 //--------------------------------------------------------------------------------------------修改活动

function  GiftDataServersEdit() {
	global $db,$adminWebType;
	$gid = ReqNum('gid');
	$sid = ReqNum('sid');
	if(!$gid)
	{	
		showMsg('错误参数！');	
		return;	
	
	}	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	
	
	$rs = $db->fetch_first("
	select 
		A.*,
		B.name as servers_name,
		B.o_name as servers_o_name,
		C.name as company_name,
		D.id,
		D.gift_type,
		D.type,
		D.name as gift_data_name
	from 
		gift_data_servers A
		left join servers B on A.sid = B.sid
		left join company C on A.cid = C.cid
		left join gift_data D on A.gift_data_id = D.id
	where 
		A.gid = '$gid'
		and A.cid = '$cid'
		and A.sid = '$sid'
	");	
	if(!$rs)
	{
		showMsg('无此信息！');	
		return;		
	}
	if ($rs['etime'] == '9999-01-01 00:00:00') 
	{
		$rs['etime'] = '9999-01-01 00:00';
	}else{
		$rs['etime'] = date('Y-m-d H:i',strtotime($rs['etime']));
	}
	$rs['stime'] = date('Y-m-d H:i',strtotime($rs['stime']));
	
	//------------------------------------------------------------------
	
	
	$iquery = $db->query("select * from gift_data_item where gift_data_id = '$rs[id]' order by `order` asc,id asc");	
	if($db->num_rows($iquery))
	{		
		while($irs = $db->fetch_array($iquery))
		{
			 $list_array[] = $irs;
			
		}
	}
	$gquery = $db->query("select * from gift_data_gold where gift_data_id = '$rs[id]' order by `order` asc,id asc");	
	if($db->num_rows($gquery))
	{		
		while($grs = $db->fetch_array($gquery))
		{
			 $gold_list_array[] = $grs;
			
		}
	}
	
	$db->close();
	include_once template('setting_gift_data_s_edit');
}
 //--------------------------------------------------------------------------------------------提交发布活动
function  SaveGiftDataServersAdd() 
{
	global $db,$adminWebID,$adminWebType; 
	$sid = ReqNum('sid');
	$gift_data_id = ReqNum('gift_data_id');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');

	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	
	if (!$cid || !$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}
	if (!$gift_data_id) 
	{
		showMsg('未选择活动模版！');	
		return;		
	}	
	
	$type_n = $db->result_first("select type from gift_data where id = '$gift_data_id'");
	$type_o = $db->result($db->query("select count(*) from gift_data_servers A ,gift_data B where A.gift_data_id = B.id and A.cid = '$cid' and  A.sid = '$sid' and  B.type = '$type_n'"),0);
	if ($type_o) 
	{
		showMsg('该活动类型此服已发布过！');	
		return;		
	}	
	$num = $db->result($db->query("select count(*) from gift_data_servers where cid = '$cid' and  sid = '$sid' and  gift_data_id = '$gift_data_id'	"),0); //获得总条数
	if ($num) 
	{
		showMsg('该活动此服已发布过！');	
		return;		
	}
	$msg = $query = $db->query("
	insert into 
		gift_data_servers
		(`cid`,`sid`,`gift_data_id`,`stime`,`etime`,`adminID`,`ctime`) 
	values 
		('$cid','$sid','$gift_data_id','$stime','$etime','$adminWebID',now())
	");
	if ($msg) 
	{

		showMsg('发布成功！','?in=gift','','greentext','','n');	
		insertServersAdminData(0,0,0,'发布活动','活动模版ID:'.$gift_data_id.',服务器ID:'.$sid.')');//插入操作记录
		return;	

	}else{
		showMsg('发布失败！');	
		return;	
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------修改活动
function  SaveGiftDataServersEdit() 
{
	global $db,$adminWebID,$adminWebType; 
	$sid = ReqNum('sid');
	$gid = ReqNum('gid');	
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');

	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	if (!$cid || !$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}

	
	if ($gid) 
	{
		$msg = $db->query("
		update 
			gift_data_servers 
		set 
			stime = '$stime',
			etime = '$etime'
		where 
			gid = '$gid'
		");
		showMsg('操作成功！',"",'','greentext');	
		insertServersAdminData($cid,$sid,0,'修改活动','活动ID:'.$gid.',服务器ID:'.$sid.'');//插入操作记录
		return;	

	}else{
		showMsg('操作失败！');	
		return;	
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------删除活动
function  DelGiftDataServers() 
{
	global $db,$adminWebID,$adminWebType; 
	$gid = ReqNum('gid');
	$sid = ReqNum('sid');
	if (empty($gid))
	{
		showMsg('错误参数！');
		return;		
	}	
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	if (!$cid || !$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}
	
	
	$db->query("delete from gift_data_servers where cid = '$cid' and sid = '$sid' and gid = '$gid'");
	showMsg('删除成功！',"",'','greentext');			
	insertServersAdminData($cid,$sid,0,'删除活动','活动ID:'.$gid.'');//插入操作记录
	$db->close();			
		
}


?> 