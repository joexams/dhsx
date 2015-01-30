<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

  //--------------------------------------------------------------------------------------------BUG记录

function Bug() {
	global $db,$odb,$adminWebType,$adminWebCid,$page;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqNum('type');
	$status = ReqNum('status');
	$username = ReqStr('username');
	
	
	$pj = array(1=>'很满意',2=>'满意',3=>'一般',4=>'不满意',5=>'很不满意');
	
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0","open_date desc,sid desc");//服务器	
		$set_cid_arr = "and A.cid in ($adminWebCid)";
	}elseif($adminWebType == 'u'){
		global $adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and A.sid in ($adminWebServers)";
			$set_sid_arr2 = " and sid in ($adminWebServers)";
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				showMsg(languagevar('NOSERVERPOWER'));	
				return;			
			}			
			
		}
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0 $set_sid_arr2","open_date desc,sid desc");//服务器
		$set_cid_arr = "and A.cid in ($adminWebCid)";
		
	}
	$set_username = $username ? " and (A.username like '%$username%' OR A.nickname like '%$username%')" : '';
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}
	if($type)
	{
		$set_type = "and A.type = '$type'";
	}
	if(!$status)
	{
		$set_status = "and A.status = 0";
	}elseif($status == 1)
	{
		$set_status = "and A.status = 1";
	}elseif($status == -1){
		$set_status = "and A.status = -1";
	}elseif($status == 2){
		$set_status = "";
	}	

	
				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $odb->result($odb->query("
	select 
		count(*) 
	from 
		gm_bug A
	where 
		A.id > 0 
		$set_cid_arr
		$set_sid_arr
		$set_cid
		$set_sid
		$set_type
		$set_status
		$set_username
	"),0); //获得总条数
	if($num){	
		$query = $odb->query("
		select 
			A.*
		from 
			gm_bug A 
		where 
			A.id > 0
			$set_cid_arr
			$set_sid_arr
			$set_cid
			$set_sid
			$set_type
			$set_status
			$set_username
		order by
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $odb->fetch_array($query)){	
			$sidArr[] = $rs['sid'];
			$uidArr[] = $rs['player_id'];
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=bug&type=$type&status=$status&username=$username&cid=$cid&sid=$sid");	
	}	


	//---------------------------------------------------------------------
	if($sidArr)
	{
		$sidArr = array_unique($sidArr);
		$sid_arr = implode(",",$sidArr);
		$uidArr = array_unique($uidArr);
		$uid_arr = implode(",",$uidArr);
		//---------------------找服务器名-----------------------------
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
				$s[$srs['sid']] =  $srs;
			}
			//print_r($s);
		}
		//---------------找充值玩家-----------------------------------
		$query = $db->query("
		select 
			sid,
			player_id
		from 
			pay_data
		where 
			sid in ($sid_arr)	
			and player_id in ($uid_arr)	
		group by 
			sid,username
		");
		if($db->num_rows($query))
		{
			while($vrs = $db->fetch_array($query))
			{	
				$v[$vrs['sid'].'_'.$vrs['player_id']] =  $vrs;
			}
		}		
		
		//print_r($v);
		$db->close();
	}


	$odb->close();

		
	include_once template('bug');
}
  //--------------------------------------------------------------------------------------------回复BUG记录

function ReplyBug() {
	global $odb,$adminWebType,$adminWebName;
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	$reply = ReqStr('reply');
	$is_over = ReqNum('is_over');
	$time = time();
	if (empty($id) || empty($sid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}		
	if($adminWebType == 'u')
	{

		global $adminWebServers;
		if ($adminWebServers) 
		{	
			$adminWebServersArr = explode(',',$adminWebServers);	
			if(!in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				showMsg(languagevar('NOSERVERPOWER'));	
				return;	
			
			}
		}
	}


	if($sid)
	{
		$msg = $odb->query("update gm_bug set `reply_content`= '$reply',`reply_user`= '$adminWebName',`reply_time`= '$time',`status` = 1,`is_over` = '$is_over' where id = '$id'");

		if ($msg) {
			showMsg(languagevar('SETOK'),"",'','greentext');	
		}else{
			showMsg(languagevar('SETERR'));	
		}		
		$odb->close();
	}
}
//--------------------------------------------------------------------------------------------批量删除BUG
function  DelBug() 
{
	global $odb,$adminWebType; 
	$sid = ReqNum('sid');
	$type = ReqNum('type');
	$id_del = ReqArray('id_del');
	
	if (empty($type))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}	
/*	
	if($adminWebType == 'u')
	{

		global $adminWebServers;
		if ($adminWebServers) 
		{	
			$adminWebServersArr = explode(',',$adminWebServers);	
			if(!in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				showMsg('您没有此服的权限！');	
				return;	
			
			}
		}
	}*/
	if ($type == 1)
	{
		$type_name = languagevar('DEL');
	
	}elseif ($type == 2){
		$type_name = languagevar('PB');	
	}elseif ($type == 3){
		$type_name = languagevar('QXPB');	
	}

	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		if ($type == 1) {
			$odb->query("delete from gm_bug where id in ($id_arr)");
		}elseif($type == 2) {
			$odb->query("update gm_bug set `status`= -1 where id in ($id_arr)");
		}elseif($type == 3) {
			$odb->query("update gm_bug set `status`= 0 where id in ($id_arr)");
		}
		$odb->close();
		insertServersAdminData(0,0,0,languagevar('WJFK'),languagevar('WJFK').$type_name.' ID('.$id_arr.')');//插入操作记录		
		showMsg($type_name.languagevar('SETOK'),'','','greentext');	
		
	}else{
		showMsg(languagevar('NOCHOOSESERVER'));	
	}		

}


?> 