<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
  //--------------------------------------------------------------------------------------------被盗找回

function FindData() {

	global $db,$adminWebID,$adminWebName,$adminWebType,$adminWebCid,$page;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqStr('type');
	$state = ReqNum('state');
	$username = ReqStr('username');
	
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0","sid desc");//服务器	
	}elseif($adminWebType == 'c'){
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0","open_date desc,sid desc");//服务器	
		$set_cid_arr = "and A.cid in ($adminWebCid)";
				
	}elseif($adminWebType == 'u'){
		global $adminWebServers;
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
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0 $set_sid_arr ","open_date desc,sid desc");//服务器
		$set_admin = "and A.adminID = '$adminWebID'";
		$set_cid_arr = "and A.cid in ($adminWebCid)";
	}
	
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}	
	if($type)
	{
		$set_type = "and A.type = '$type'";
	}	
	if($state)
	{
		$set_state = "and A.state = '$state'";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if ($username) 
	{
		$set_username = "and (A.username = '$username' or A.nickname = '$username')";
	}	

	//---------------------------------------------------------------------
	
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		find_data A 		
	where 
		A.id > 0
		$set_cid
		$set_cid_arr
		$set_sid
		$set_state
		$set_type
		$set_admin
		$set_username
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*,
			B.adminName
		from 
			find_data A 
			left join admin B on A.adminID = B.adminID
		where 
			A.id > 0
			$set_cid
			$set_cid_arr
			$set_sid
			$set_state
			$set_type
			$set_admin
			$set_username
		order by
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			$sidArr[] = $rs['sid'];
			$rs['val'] = explode('|',$rs['val']);
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=apply&action=FindData&cid=$cid&sid=$sid&state=$state&type=$type&username=".urlencode($username)."");	
	}
	
	
	//if(!webAdmin('c_apply_set','y') || $adminWebType == 's')
	//{	
		//---------------------------------------------------------------------
		if($sidArr)
		{
			$sidArr = array_unique($sidArr);
			$sid_arr = implode(",",$sidArr);
			
			$query = $db->query("
			select 
				A.sid,
				A.name as servers_name,
				B.test_user_arr,
				C.name as company_name
			from 
				servers A
				left join servers_data B on A.sid = B.sid
				left join company C on A.cid = C.cid
			where 
				A.sid in ($sid_arr)
			");
			if($db->num_rows($query))
			{
				while($srs = $db->fetch_array($query))
				{
				
				
					if(!webAdmin('c_apply_set','y') || $adminWebType == 's') {
					
						$srs['testUserArr'] = explode('%',$srs['test_user_arr']);
					}else{
						$srs['testUserArr'] = array();
					}
					$s[$srs['sid']] =  $srs;
				}			
			
			}

		}
	//}
	
	
	
	
	$db->close();
	include_once template('find_data');
}
//--------------------------------------------------------------------------------------------提交申请物品

function ApplyAdd() {
	global $db,$adminWebID,$adminWebType,$adminWebCid;
	global $cid,$company,$adminWebServers;
	$type = ReqStr('type');
	if($adminWebType == 'c'){
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		//$servers_list = globalDataList('servers',"cid = '$cid'");//服务器			
	}elseif($adminWebType == 'u'){
	
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}	
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		//$servers_list = globalDataList('servers',"cid = '$cid' $set_sid_arr","sid desc");//服务器
		
			
	}	
	
	
		

	if($type == 'item'){
		for ($i=1;$i<=SXD_SYSTEM_ITEM_LEVEL;$i++)
		{
			$item_level_list[$i] = $i;
		}			
		
		$call = 'onChange="selectAjax(\'player_call.php?action=callItemType\',\'type_id\',\'sid\',1);$(\'item_id\').options.length = 0;"';
	}elseif($type == 'mg'){
		for ($i=1;$i<=10;$i++)
		{
			$fate_level_list[$i] = $i;
		}		
		
		$call = 'onChange="selectAjax(\'player_call.php?action=callFate\',\'fate_id\',\'sid\',1);$(\'fate_id\').options.length = 0;"';
	}elseif($type == 'soul'){
		$call = 'onChange="selectAjax(\'player_call.php?action=callSoul\',\'soul_id\',\'sid\',1);$(\'soul_id\').options.length = 0;"';
		//$u_call = 'onBlur="selectAjax(\'player_call.php?action=callPlayerSoulLog\',\'soul_list\',\'username|sid|usertype|soul_id\')"';
	}

	
	$db->close();
	include_once template('setting_apply_add');
	
}
  //--------------------------------------------------------------------------------------------申请物品日志

function Apply() {

	global $db,$adminWebID,$adminWebName,$adminWebType,$adminWebCid,$page;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqStr('type');
	$status = ReqNum('status');
	$text = ReqStr('text');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0","open_date desc,sid desc");//服务器	
	}elseif($adminWebType == 'c'){
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0","open_date desc,sid desc");//服务器	
		$set_cid_arr = "and A.cid in ($adminWebCid)";
				
	}elseif($adminWebType == 'u'){
		global $adminWebServers;
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
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0 $set_sid_arr ","open_date desc,sid desc");//服务器
		$set_admin = "and A.adminID = '$adminWebID'";
		$set_cid_arr = "and A.cid in ($adminWebCid)";
	}
	
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}	
	if($type)
	{
		$set_type = "and A.atype = '$type'";
	}	
	if($status)
	{
		$set_status = "and A.status = '$status'";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if ($text) 
	{
		$set_text = "and (A.username like '%$text%' or A.cause like '%$text%')";
	}	
	//------------------------属性---------------------------------------------
	
	
			
/*		$query = $pdb->query("
		select 
			A.id,
			A.unit,
			B.name
		from 
			soul_attribute A
			left join war_attribute_type B on A.war_attribute_type_id = B.id
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['unit'] = $rs['unit'] < 1 ?  '%' : '';
			$soula[$rs['id']] =  $rs;
		}	
*/


	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		apply_data A 		
	where 
		A.aid > 0
		$set_cid
		$set_cid_arr
		$set_sid
		$set_status
		$set_type
		$set_admin
		$set_text
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			A.*,
			B.adminName,
			D.adminName as r_adminName
		from 
			apply_data A 
			left join admin B on A.adminID = B.adminID
			left join admin D on A.r_adminID = D.adminID
		where 
			A.aid > 0
			$set_cid
			$set_cid_arr
			$set_sid
			$set_status
			$set_type
			$set_admin
			$set_text
		order by
			A.aid desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			$sidArr[] = $rs['sid'];
			if ($rs['apply'])  {
				$rs['nn'] = json_decode($rs['apply'], true);
			}
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=apply&action=Apply&cid=$cid&sid=$sid&status=$status&type=$type&text=$text");	
	}
	
	
	if(!webAdmin('c_apply_set','y') || $adminWebType == 's')
	{	
		//---------------------------------------------------------------------
		if($sidArr)
		{
			$sidArr = array_unique($sidArr);
			$sid_arr = implode(",",$sidArr);
			
			$query = $db->query("
			select 
				A.sid,
				A.name as servers_name,
				B.test_user_arr,
				C.name as company_name
			from 
				servers A
				left join servers_data B on A.sid = B.sid
				left join company C on A.cid = C.cid
			where 
				A.sid in ($sid_arr)
			");
			if($db->num_rows($query))
			{
				while($srs = $db->fetch_array($query))
				{
				
				
					$srs['testUserArr'] = explode('%',$srs['test_user_arr']);
					$s[$srs['sid']] =  $srs;
				}			
			
			}

		}
	}
	
	
	
	
	$db->close();
	include_once template('setting_apply');
}

 //--------------------------------------------------------------------------------------------保存提交物品申请
function  SaveApplyAdd() 
{
	global $db,$adminWebID,$adminWebServers; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$val = ReqNum('val');
	
	$username = trim(ReqStr('username'));
	$item_id = ReqNum('item_id');
	$item_name = ReqStr('item_name');
	$item_level = ReqNum('item_level');

	$fate_id = ReqNum('fate_id');
	$fate_name = ReqStr('fate_name');
	$fate_level = ReqNum('fate_level');
	
	$soul_id = ReqNum('soul_id');
	$soul_name = ReqStr('soul_name');	
	$soul_a = ReqStr('soul_a');
	if ($soul_a) {
		$soula = explode('|',$soul_a);	
		$soula1 = explode(':',$soula[0]);	
		$soula2 = explode(':',$soula[1]);	
		$soula3 = explode(':',$soula[2]);
		$skey = $soula[3];
		$av1 = $soula1[1]*10;
		$av2 = $soula2[1]*10;
		$av3 = $soula3[1]*10;
	}
	
	
	$cause = ReqStr('cause');	
	$type = ReqStr('type');
	
	if (!$type) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}
	if ($type == 'item') 
	{
		if (!$item_id) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		if (!$item_name) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		
		$apply = array('id'=>$item_id,'name'=>urlencode($item_name),'level'=>$item_level);
		$apply = json_encode($apply);
	}	
	if ($type == 'mg') 
	{
		if (!$fate_id) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		if (!$fate_name) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		$apply = array('id'=>$fate_id,'name'=>urlencode($fate_name),'level'=>$fate_level);
		$apply = json_encode($apply);
					
	}		
	
	if ($type == 'soul') 
	{
		if (!$soul_id) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		if (!$soul_name) 
		{
			showMsg(languagevar('ERROR'));	
			return;		
		}	
		//$apply = array('id'=>$soul_id,'name'=>urlencode($soul_name));
		
		$apply = array(
			'id'=>$soul_id,
			'name'=>urlencode($soul_name),
			'a1'=>$soula1[0] ? $soula1[0] : 0,
			'av1'=>$av1 ? $av1 : 0,
			'a2'=>$soula2[0] ? $soula2[0] : 0,
			'av2'=>$av2 ? $av2 : 0,
			'a3'=>$soula3[0] ? $soula3[0] : 0,
			'av3'=>$av3 ? $av3 : 0,
			'skey'=>$skey ? $skey : 0,
		);
		
		
		
		$apply = json_encode($apply);
	}		
	if (!$sid) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}
	if (!$username) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}
	if (!$val) {
		showMsg(languagevar('ERROR'));	
		return;
	}	
	if (!$cause) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}
	if ($adminWebServers) 
	{

		$adminWebServersArr = explode(',',$adminWebServers);	
		if(!in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
		{	
			showMsg(languagevar('NOSERVERPOWER'));	
			return;	
		
		}
	}

	$query = $db->query("select server_ver,api_server,api_pwd,api_port from servers where sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	
	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];

	$username = explode(',',$username);	
	for ($i = 0;$i < count($username);$i++)
	{


	
		//----------------------帐号不存在--------------------------------------
		$player = api_admin::find_player_by_username($username[$i]);
		if (!$player['result']) {
			$show .= '<strong class="redtext">'.$username[$i].' '.languagevar('NOUSER').'</strong><br />';
			//return;	
		}else{
			$player_id = $player['player_id'];
			$msg = $query = $db->query("
			insert into 
				apply_data
				(`cid`,`sid`,`adminID`,`player_id`,`username`,`cause`,`atype`,`apply`,`item_id`,`item_name`,`item_level`,`fate_id`,`fate_name`,`fate_level`,`soul_id`,`soul_name`,`val`,`apply_time`) 
			values 
				('$cid','$sid','$adminWebID','$player_id','$username[$i]','$cause','$type','$apply','$item_id','$item_name','$item_level','$fate_id','$fate_name','$fate_level','$soul_id','$soul_name','$val',now())
			");	
			
			if ($msg)
			{
				$show .= "<strong>".$username[$i]."</strong> ".languagevar('WPSQADDOKMSG')."<br />";
			}else{
				$show .= '<strong class="redtext">'.$username[$i].' '.languagevar('WPSQADDERR').'</strong><br />';
			}
					
			
		}
		
		
	}
	
	$db->close();			
	showMsg($show,"?in=apply&action=Apply&cid=$cid",'','greentext','','','n');	

		
}

 //--------------------------------------------------------------------------------------------删除盗号找回
function  DelFindData() 
{
	global $db,$adminWebID; 
	$id = ReqNum('id');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	if (empty($id) || empty($cid) || empty($sid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{

		$db->query("delete from find_data where cid = '$cid' and  sid = '$sid' and  adminID = '$adminWebID' and state = 1 and id = '$id'");
		showMsg(languagevar('SETOK'),"",'','greentext');	
		return;		
	}
	$db->close();			
		
}
 //--------------------------------------------------------------------------------------------删除物品申请
function  DelApply() 
{
	global $db,$adminWebID; 
	$aid = ReqNum('aid');
	$cid = ReqNum('cid');
	if (empty($aid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{

		$db->query("delete from apply_data where cid = '$cid' and  adminID = '$adminWebID' and status = 1 and aid = '$aid'");
		showMsg(languagevar('SETOK'),"",'','greentext');	
		return;		
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------物品申请回复
function  ReplyApply() 
{
	global $db,$adminWebID; 
	$aid = ReqNum('aid');
	$cid = ReqNum('cid');
	$status = ReqNum('status');
	$reply = ReqStr('reply');
	if (empty($aid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
		$rs = $db->fetch_first("
		select 		
			status
		from 
			apply_data
		where
			cid = '$cid' and aid = '$aid'
		");	
		if ($rs['status'] != 1) {
	
			$db->query("update apply_data set reply = '$reply',r_adminID = '$adminWebID',reply_time = now() where  cid = '$cid' and aid = '$aid'");

		}else{
			$db->query("update apply_data set status = '$status',reply = '$reply',r_adminID = '$adminWebID',reply_time = now() where  cid = '$cid' and aid = '$aid'");
		
		}	
		showMsg(languagevar('SETOK'),"",'','greentext');	
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------撤消重批
function  CancelFindData() 
{
	global $db,$adminWebID; 
	$id = ReqNum('id');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$status = ReqNum('status');
	if (empty($id) || empty($cid) || empty($sid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
		$db->query("update find_data set state = 1 where  cid = '$cid' and sid = '$sid' and id = '$id'");
		insertServersAdminData($cid,$sid,0,languagevar('SERVER'),languagevar('WPSQCX').'ID:'.$id);//插入操作记录
		showMsg(languagevar('SETOK'),"",'','greentext');	
	}
	$db->close();			
		
}
 //--------------------------------------------------------------------------------------------撤消重批
function  CancelApply() 
{
	global $db,$adminWebID; 
	$aid = ReqNum('aid');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$status = ReqNum('status');
	if (empty($aid) || empty($cid) || empty($sid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
		$db->query("update apply_data set status = 1 where  cid = '$cid' and sid = '$sid' and aid = '$aid'");
		insertServersAdminData($cid,$sid,0,languagevar('SERVER'),languagevar('WPSQCX').'ID:'.$aid);//插入操作记录
		showMsg(languagevar('SETOK'),"",'','greentext');	
	}
	$db->close();			
		
}
 //--------------------------------------------------------------------------------------------设置物品申请
function  SetApply() 
{
	global $db,$adminWebID; 
	$cid = ReqNum('cid');
	$aid = ReqNum('aid');
	$type = ReqStr('type');
	$status = ReqNum('status');
	$reply = ReqStr('reply');
/*	echo $cid;
	echo '<br />';
	echo $aid;
	echo '<br />';
	echo $type;
	echo '<br />';
	echo $status;
	echo '<br />';
	echo $reply;
	echo '<br />';
	//exit();*/
	if (empty($aid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
	
		$rs = $db->fetch_first("
		select 		
			status
		from 
			apply_data
		where
			cid = '$cid' and aid = '$aid'
		");	

		if ($rs['status'] == 3) {
			showMsg(languagevar('SETERR'));
		}elseif($rs['status'] == 2){
			showMsg(languagevar('SETERR'));	
		}else{
			$db->query("update apply_data set status = 3,reply = '$reply',r_adminID = '$adminWebID',reply_time = now() where  cid = '$cid' and aid = '$aid'");
			if ($type == 'vip'){
				SaveUpVip();
			}else{
				SaveIncrease();
			}
		}
		//$db->close();			
		
	}
		
}

 //--------------------------------------------------------------------------------------------批量设置物品申请
function  SetApplyAll() 
{
	global $db,$adminWebID,$adminWebName,$adminWebType,$adminWebServers,$adminWebPower,$adminWebLang; 
	$aid = ReqArray('aid');
	$setype = ReqNum('setype');
	//$path_root = UCTIME_ROOT.'/mod/apply_ok.php';
	if (empty($aid))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
		$aidArr = implode(",",$aid);
		if ($setype == 3)
		{
			$query = $db->query("
			select 
				A.*,
				B.name as server_name,
				B.o_name as server_o_name,
				B.server_ver,
				B.api_server,
				B.api_pwd,
				B.api_port
			from 
				apply_data A
				left join servers B on A.sid = B.sid
			where 
				A.aid in ($aidArr)	
			");
			if($db->num_rows($query))
			{
				while($rs = $db->fetch_array($query))
				{	
					callapi::load_api_class($rs['server_ver']);
					if($rs['status'] == 1){
						if ($rs['atype'] == 'vip'){
							$m = settingApplyVip($rs['cid'],$rs['sid'],1,$rs['username'],$rs['val'],$rs['cause'],$rs['api_server'],$rs['api_pwd'],$rs['api_port']);
							$msg .= $rs['server_name'].'-'.$rs['server_o_name'].' '.$m.'<br />';
						}else{
							$m = SetIncrease($rs['cid'],$rs['sid'],$rs['atype'],1,$rs['username'],$rs['apply'],$rs['val'],$rs['cause'],$rs['api_server'],$rs['api_pwd'],$rs['api_port']) ;
							$msg .= $rs['server_name'].'-'.$rs['server_o_name'].' '.$m.'<br />';
						}
						$ok = explode(" ",trim($m));
						if($ok[0] == '[OK]')
						{
							$db->query("update apply_data set status = 3 where aid = '$rs[aid]' and status = 1 ");
						}
					
						
					}			
					
				}
				showMsg($msg,"",'','greentext','','','n');	
			}
		
		}elseif ($setype == 2){
			$db->query("update apply_data set status = '$setype',r_adminID = '$adminWebID',reply_time = now() where aid in ($aidArr) and status = 1 ");
			showMsg(languagevar('SETOK'),"",'','greentext');	
		}elseif ($setype == 4){
			$db->query("update apply_data set status = '$setype',r_adminID = '$adminWebID',reply_time = now() where aid in ($aidArr) and status = 1 ");
			showMsg(languagevar('SETOK'),"",'','greentext');	
		}
		$db->close();			
		
	}
		
}



//--------------------------------------------------------------------------------------------保存调VIP等级
function  settingApplyVip($cid,$sid,$usertype,$username,$vip,$cause,$api_server,$api_pwd,$api_port) 
{
	if(!$sid)
	{
		return '<strong class="redtext">'.$username.' '.languagevar('NOCHOOSESERVER').'</strong>';	
	}

	if($api_server && $api_pwd && $api_port) 
	{
		api_base::$SERVER = $api_server;
		api_base::$PORT   = $api_port;
		api_base::$ADMIN_PWD   = $api_pwd;

		//----------------------帐号不存在--------------------------------------
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}

		if (!$player['result']) {
			return '<strong class="redtext">'.$username.' '.languagevar('NOUSER').'</strong>';	
		}
		
		//------------------------------------------------------------
		$msg = api_admin::set_player_vip_level($player['player_id'],$vip);
		if ($msg['result'] == 1) {			
			$cause = $cause ? $cause : languagevar('WTX');
			$contents = languagevar('VIPSQOKMSG').$vip.','.languagevar('REASON').':'.$cause.$n;
			insertServersAdminData($cid,$sid,$player['player_id'],$username,$contents);//插入操作记录		
			return '[OK] <strong>'.$username.'</strong> '.languagevar('VIPSQOK').$vip;

		}else{
			return '<strong class="redtext">'.$username.' '.languagevar('VIPSQERR').'</strong>';	
		}
	}else{
		return '<strong class="redtext">'.$username.' '.languagevar('ERROR').'</strong>';	
	}

}	

 //--------------------------------------------------------------------------------------------批量审批物品找回
function  SetFindData() 
{
	global $db,$adminWebID,$adminWebName,$adminWebType,$adminWebServers,$adminWebPower,$adminWebLang; 
	$id = ReqArray('id');
	$setype = ReqNum('setype');

	if (empty($id))
	{
		showMsg(languagevar('ERROR'));
		return;		
	}else{
		$idArr = implode(",",$id);
		if ($setype == 3)
		{
			$query = $db->query("
			select 
				A.*,
				B.name as server_name,
				B.o_name as server_o_name,
				B.server_ver,
				B.api_server,
				B.api_pwd,
				B.api_port
			from 
				find_data A
				left join servers B on A.sid = B.sid
			where 
				A.id in ($idArr)
			");
			if($db->num_rows($query))
			{
				while($rs = $db->fetch_array($query))
				{	
					callapi::load_api_class($rs['server_ver']);
					if($rs['state'] == 1){
		
		
						$m = settingFindSet($rs['cid'],$rs['sid'],$rs['type'],$rs['player_id'],$rs['username'],$rs['nickname'],$rs['val'],$rs['api_server'],$rs['api_pwd'],$rs['api_port'],$rs['server_ver']);
						if(!$m == 1) {
							$show = '<strong class="redtext">'.$rs['username'].' '.langmsg('SETERR').'</strong>'; 
						}else{
							$db->query("update find_data set state = 3 where id = '$rs[id]' and state = 1 ");
							$GLOBALS['m'] = $m;
							$show = '<strong>'.$rs['username'].'</strong> '.langmsg('BDZHSPMSG'); 				
						}
						$msg .= $rs['server_name'].'-'.$rs['server_o_name'].' '.$show.'<br />';
		
					
						
					}			
					
				}
				showMsg($msg,"",'','greentext','','','n');	
			}
		
		}elseif ($setype == 2){
			$db->query("update find_data set state = '$setype' where id in ($idArr) and state = 1 ");
			showMsg(languagevar('SETOK'),"",'','greentext');	
		}elseif ($setype == 4){
			$db->query("update find_data set state = '$setype' where id in ($idArr) and state = 1 ");
			showMsg(languagevar('SETOK'),"",'','greentext');	
		}
		$db->close();			
		
	}
		
}



//--------------------------------------------------------------------------------------------保存送
function  settingFindSet($cid,$sid,$type,$player_id,$username,$nickname,$val,$api_server,$api_pwd,$api_port,$ver) 
{
	$val = explode('|',$val);
	$item_list = array();
	$fate_list = array();
	$soul_list = array();
	$ingot = 0;
	$coins = 0;
	$fame = 0;
	$skill = 0;
	if($api_server && $api_pwd && $api_port) 
	{

		callapi::load_api_class($ver);
		api_base::$SERVER = $api_server;
		api_base::$PORT   = $api_port;
		api_base::$ADMIN_PWD   = $api_pwd;


		//----------------------帐号不存在--------------------------------------
		$n = '('.languagevar('USERNAME').')';
		$player = api_admin::find_player_by_username($username);
	
		
		if (!$player['result']) {
			return 0;	
		}
		
		if ($val)
		{
			for($i = 0;$i<count($val);$i++)
			{
			
				$apply = $val[$i] ? json_decode($val[$i], true) : array();
				
				if($type == 1){
					if($ver >= 2012081601) {
				
						$soul_list[] = array(
							'soul_id'=>$apply['id'],
							'attributeid1'=>$apply['a1'],
							'attributevalue1'=>$apply['av1'] ? $apply['av1']*10 : 0,
							'attributeid2'=>$apply['a2'],
							'attributevalue2'=>$apply['av2'] ? $apply['av2']*10 : 0,
							'attributeid3'=>$apply['a3'],
							'attributevalue3'=>$apply['av3'] ? $apply['av3']*10 : 0,
							'attributeid4'=>$apply['a4'],
							'attributevalue4'=>$apply['av4'] ? $apply['av4']*10 : 0,							
							'key'=>$apply['key'],
							'number'=>$apply['number'],
						);
					}else{
						$soul_list[] = array(
							'soul_id'=>$apply['id'],
							'attributeid1'=>$apply['a1'],
							'attributevalue1'=>$apply['av1'] ? $apply['av1']*10 : 0,
							'attributeid2'=>$apply['a2'],
							'attributevalue2'=>$apply['av2'] ? $apply['av2']*10 : 0,
							'attributeid3'=>$apply['a3'],
							'attributevalue3'=>$apply['av3'] ? $apply['av3']*10 : 0,
							'key'=>$apply['key'],
							'number'=>$apply['number'],
						);					
					
					}
					
				}elseif($type == 2){
					$item_list[] = array(
						'item_id'=>$apply['id'],
						'level'=>$apply['level'],
						'number'=>$apply['number'],
					);
				}elseif($type == 3){
					if($ver >= 2012080201) {
				
						$fate_list[] = array(
							'fate_id'=>$apply['id'],
							'level'=>$apply['level'],
							'number'=>$apply['number'],
							'actived_fate_id1'=>$apply['fid1'] ? $apply['fid1'] : 0,
							'actived_fate_id2'=>$apply['fid2'] ? $apply['fid2'] : 0,
						);
					
					}else{
						$fate_list[] = array(
							'fate_id'=>$apply['id'],
							'level'=>$apply['level'],
							'number'=>$apply['number'],
							
						);
				
					}
				}
				
				
			}
			//print_r($item_list);
			//print_r($soul_list);
			//print_r($fate_list);
			$num = 0;
			$num_ok = 0;
			$num_err = 0;
			if($type == 1){
				$msg = api_admin::add_player_super_gift($player_id, 12, $ingot, $coins, $fame, $skill,1213, langmsg('BDZHMSG'), $item_list, $fate_list, $soul_list);
				$num++;
				if($msg['result'])
				{
					$num_ok++;
				}else{
					$num_err++;
				}
			}elseif($type == 2){
				$msg = api_admin::add_player_super_gift($player_id, 12, $ingot, $coins, $fame, $skill,1213, langmsg('BDZHMSG'), $item_list, $fate_list, $soul_list);
				$num++;
				if($msg['result'])
				{
					$num_ok++;
				}else{
					$num_err++;
				}
			}elseif($type == 3){
				$msg = api_admin::add_player_super_gift($player_id, 12, $ingot, $coins, $fame, $skill,1213, langmsg('BDZHMSG'), $item_list, $fate_list, $soul_list);
				$num++;
				if($msg['result'])
				{
					$num_ok++;
				}else{
					$num_err++;
				}
			}
			

			//if ($msg['result']) {
				return array('num'=>$num,'num_ok'=>$num_ok,'num_err'=>$num_err);	
			//}else{
			//	return array('result'=>0,'num'=>$num,'num_ok'=>$num_ok,'num_err'=>$num_err);
			//}			
			
		}		
		
		
	}else{
		return 0;	
	}	
	
}	

function  FindPlayerData()
{
	global $db,$adminWebType,$adminWebID;
   	$cid = ReqNum('cid');
   	$sid = ReqNum('sid');
   	$player_id = ReqNum('player_id');
   	$nickname = ReqStr('nickname');
   	$username = ReqStr('username');
   	$type = ReqNum('type');
   	$cause = ReqStr('cause');
	$apply = ReqArray('apply','htm');
	if (!$player_id || !$username || !$nickname || !$apply ){
		showMsg('ERROR');	
		return;	
	}
	$idsArr = array();
	$query = $db->query("
	select 
		A.ids
	from 
		find_data A
	where 
		A.type = '$type'
		and A.cid = '$cid'
		and A.sid = '$sid'
		and A.player_id = '$player_id'
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$idss .=  $idss ? ','.$rs['ids'] : $rs['ids'];
		}	
		$idsArr = explode(',',$idss);
	}				
	
	foreach($apply as $v => $a){
		$a1 = explode('|',$a);
		$val[] = $a1[1];
		$ids[] = $a1[0];
		if(in_array($a1[0],$idsArr)) 
		{
			showMsg('ERROR');
			return;
		}
	}
	
	$val = implode("|",$val);
	$ids = implode(",",$ids);
	
	if (!$ids || !$val){
		showMsg('ERROR');	
		return;	
	}
	
	//echo $val.'<br />'.$ids;
	//exit();
	$query = $db->query("
		insert into 
			find_data
			(`cid`,`sid`,`username`,`nickname`,`cause`,`player_id`,`val`,`ids`,`type`,`adminID`,`postime`) 
		values 
			('$cid','$sid','$username','$nickname','$cause','$player_id','$val','$ids','$type','$adminWebID',now())
		") ;
	showMsg('SETOK','','','greentext');	
	

	
}

?> 