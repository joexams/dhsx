<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
function  CodePartyTable() {
	global $db,$adminWebType;
	include_once template('setting_code_party_table');
}

function  CodePartyAdd() {
	global $db,$adminWebType;

	$cid = ReqNum('cid');
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	$query = $db->query("select TABLE_NAME,TABLE_COMMENT from information_schema.tables where table_name like 'code_party_%' order by CREATE_TIME desc");	
	if($db->num_rows($query)){
		while($trs = $db->fetch_array($query)){	
			$comment = explode(";",$trs['TABLE_COMMENT']);
			$trs['TABLE_COMMENT'] = $comment[0];
			$name = explode("_",$trs['TABLE_NAME']);
			$trs['TABLE_NAME'] = $name[2];			
			$table[] = $trs;
		}
	}
	$db->close();
	include_once template('setting_code_party_add');
}


function  Pcode() {
	global $db,$adminWebType,$page;
	
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$cid = ReqNum('cid');	
	$sid = ReqNum('sid');
	$p_db = ReqStr('p_db');
	$code = ReqStr('code');
	$number = ReqNum('number');
	$username = trim(ReqStr('username'));
	$use = ReqNum('use');
	$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器
	//$party_code_list = SXD_SYSTEM_PARTY_CODE ? explode("|",trim(SXD_SYSTEM_PARTY_CODE)) : array(); //活动库
	
	$query = $db->query("select TABLE_NAME,TABLE_COMMENT from information_schema.tables where table_name like 'code_party_%' order by CREATE_TIME desc");	
	if($db->num_rows($query)){
		while($trs = $db->fetch_array($query)){	
			$comment = explode(";",$trs['TABLE_COMMENT']);
			$trs['TABLE_COMMENT'] = $comment[0];
			$name = explode("_",$trs['TABLE_NAME']);
			$trs['TABLE_NAME'] = $name[2];			
			$party_code_list[] = $trs;
		}
	}
	
	
	
	if($p_db)
	{
		$p_f_db = 'code_party_'.$p_db;
	
		if($cid)
		{
			$set_cid = "and A.cid = '$cid'";
		}
		if($sid)
		{
			$set_sid = "and A.sid = '$sid'";
		}	
		if($code)
		{
			$set_code = "and A.code = '$code'";
		}
		if($username)
		{
			$set_username = "and A.username = '$username' ";
		}	
		if($number)
		{
			$set_number = "and A.number = '$number'";
		}
		if($use == 1)
		{
			$set_use = "and A.player_id > 0";
			$set_order = "ctime desc,";
		}elseif($use == 2){
			$set_use = "and A.player_id = 0";
		}
		//---------------------------------------------------------------------
		$query = $db->query("
		select 
			number
		from 
			$p_f_db
		where 
			cid = '$cid'
		group by
			number asc 
		");	
		if($db->num_rows($query)){
			while($nrs = $db->fetch_array($query)){	
				$code_number_array[] = $nrs;
			}
		}

					
		//---------------------------------------------------------------------
		
		$pageNum = 50;
		$start_num = ($page-1)*$pageNum;	
		$num = $db->result($db->query("
		select 
			count(*) 
		from 
			$p_f_db A
		where 
			A.id > 0
			$set_cid
			$set_sid
			$set_code
			$set_number
			$set_username
			$set_use
		"),0); //获得总条数
		if($num){
			$query = $db->query("
			select 
				A.*,
				B.name as company_name,
				C.name as servers_name
			from 
				$p_f_db A
				left join company B on A.cid = B.cid
				left join servers C on A.sid = C.sid
			where 
				A.id <> 0
				$set_cid
				$set_sid
				$set_code
				$set_number
				$set_username
				$set_use
			order by
				$set_order
				A.id desc 
			limit
				$start_num,$pageNum
			");	
			while($rs = $db->fetch_array($query)){	
				
				$list_array[] = $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=code&action=Pcode&cid=$cid&sid=$sid&code=$code&number=$number&use=$use&p_db=$p_db");	
		}	
		$db->close();
	}
	include template('setting_pcode');
}

function  CodeAdd() {
	global $db,$adminWebType;

	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器	
	}
	$db->close();
	include_once template('setting_code_add');
}
function  CodeAddAgain() {
	global $db,$adminWebName,$adminWebType;
	$batch_id = ReqNum('batch_id');
	if(!$batch_id)
	{	
		showMsg('错误参数！');	
		return;	
	
	}	
	
	$rs = $db->fetch_first("
	select 
		A.*,
		B.name as servers_name
		 
	from 
		code_batch A
		left join servers B on A.sid = B.sid
	where 
		A.id = '$batch_id'
	");	
	if(!$rs)
	{
		showMsg('无此批激活码！');	
		return;		
	}
	$db->close();
	include_once template('setting_code_add_again');
}

function  CodeBatch() {
	global $db,$adminWebType,$page;
	
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;	
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器
		
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


				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		code_batch A
		left join servers B on A.sid = B.sid
	where 
		A.id <> 0
		$set_cid
		$set_sid
		$set_cid_arr	
	"),0); //获得总条数
	if($num){
		$query = $db->query("
		select 
			A.*,
			B.name as servers_name,
			B.o_name as servers_o_name,
			C.name as company_name,
			D.adminName
		from 
			code_batch A
			left join servers B on A.sid = B.sid
			left join company C on A.cid = C.cid
			left join admin D on A.adminID = D.adminID
		where 
			A.id <> 0
			$set_cid
			$set_sid
			$set_cid_arr
		order by
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			$list_array[] = $rs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=code&action=CodeBatch&cid=$cid&sid=$sid");	
	}	
	$db->close();
	include template('setting_code_batch');
}
function  Code() {
	global $db,$adminWebType,$page;
	
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$sid = ReqNum('sid');
	$batch_id = ReqNum('batch_id');
	$code = ReqStr('code');
	$username = trim(ReqStr('username'));
	$player_id = trim(ReqNum('player_id'));
	$is_code = ReqNum('is_code');
	$cid = ReqNum('cid');	
	if($adminWebType == 's')
	{
		
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器
		$code_batch_list = globalDataList('code_batch',"sid = '$sid'");//批次	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'","sid desc");//服务器	
		$code_batch_list = globalDataList('code_batch',"sid = '$sid'");//批次
		$set_cid_arr = "and B.cid in ($adminWebCid)";
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
			$adminWebServersArr = explode(',',$adminWebServers);	
			if($sid && !in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
			{	
				showMsg('您没有此服的权限！');	
				return;	
			
			}			
			
		}			
		
		if (!$sid) 
		{
			$sid = $db->result_first("select sid from servers where cid = '$cid' and open = 1 $set_sid_arr limit 1");
		}		
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 $set_sid_arr");//服务器
		$code_batch_list = globalDataList('code_batch',"sid = '$sid'","sid desc");//批次		
		
	}

	
	if($cid)
	{
		$set_cid = "and B.cid = '$cid'";
	}
	if($sid)
	{
		$set_sid = "and B.sid = '$sid'";
	}	
	if($code)
	{
		$set_code = "and A.code = '$code'";
	}
	if($is_code == 1)
	{
		$set_is_code = "and A.player_id > 0";
	}elseif($is_code == 2){
		$set_is_code = "and A.player_id = 0";
	}	
	if($username)
	{
		$set_username = "and (A.username = '$username' or A.nickname = '$username') ";
	}	
	if($player_id)
	{
		$set_id = "and A.player_id = '$player_id'";
	}			
	if($batch_id)
	{
		$set_batch = "and A.batch_id = '$batch_id'";
	}	
				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		code A
		left join code_batch B on A.batch_id = B.id
		left join servers C on B.sid = C.sid
	where 
		A.id <> 0
		$set_cid
		$set_sid
		$set_code
		$set_batch
		$set_username
		$set_id
		$set_is_code
		$set_cid_arr
	"),0); //获得总条数
	if($num){
		$query = $db->query("
		select 
			A.*,
			B.item_name,
			B.ingot,
			B.coins,
			B.item_val,
			B.cid,
			B.sid,
			C.name as servers_name,
			D.name as company_name
		from 
			code A
			left join code_batch B on A.batch_id = B.id
			left join servers C on B.sid = C.sid
			left join company D on B.cid = D.cid
		where 
			A.id <> 0
			$set_cid
			$set_sid
			$set_code
			$set_batch
			$set_username
			$set_id
			$set_is_code
			$set_cid_arr
		order by
			A.ctime desc ,
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			
			$list_array[] = $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=code&action=Code&cid=$cid&sid=$sid&code=$code&batch_id=$batch_id");	
	}	

	if ($adminWebType == 'u' && !$username && !$code) 
	{
		$list_array = '';
		$list_array_pages = '';
	}	
	
	$db->close();
	include template('setting_code');
}
 //--------------------------------------------------------------------------------------------提交生成激活码
function  SaveCodePartyAdd() 
{
	global $db,$adminWebID,$adminWebType; 
	$cid = ReqNum('cid');
	$num = ReqNum('num');
	//$cqz = ReqStr('cqz');
	$bhz = ReqStr('bhz');
	if (!$cid || !$bhz) 
	{
		showMsg('错误参数！');	
		return;		
	}	
	if ($num > 100000) 
	{
		showMsg('一次性只能生成100000条激活码！');	
		return;		
	}
	$p_f_db = 'code_party_'.$bhz;
	
//------------------------------建表--------------------------------------------------------	
	$istable = $db->result($db->query("select count(*) from information_schema.tables where table_name = '$p_f_db'"),0);
	if(!$istable){
		showMsg('兑换券表不存在！');	
		return;
	}
//--------------------------------------------------------------------------------------	
	if ($num) 
	{
		$rs = $db->fetch_first("select max(distinct(number)) as number from $p_f_db where cid = '$cid'");	//搜索批次
		if($rs['number'])
		{
			$number = $rs['number']+1;	
		}else{
			$number = 1;	
		}
		if($num > 5000){
			$n = floor($num/5000);
			$isnum = 5000;
		}else{
			$n = 1;
			$isnum = $num;
		}
		for($z = 0;$z<$n;$z++){
			for($i = 0;$i<$isnum;$i++){
				$code = $bhz."_".md5(random(6)."-".random(6)."-".random(6)."-".random(6)."-".random(6)."-".microtime());
				if ($sql != '') $sql .= ',';
				$sql .= "(".$cid.",'".$code."','".$number."')";	
			}
			
			$c = $db->query("insert into $p_f_db (cid,code,number) values ".$sql."");	
			unset($sql);
		}	
		if ($c) 
		{
			$show = $num.'条兑换券生成成功！';	
		}else{
			$show = $num.'条兑换券生成失败！';	
		}
	}
	showMsg('发布成功！'.$show,"",'','greentext','','n');	
	insertServersAdminData($cid,$sid,0,'发布新手卡','数量('.$num.')');//插入操作记录

	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------创建兑换券
function  SaveCodePartyTable() 
{
	global $db,$adminWebID,$adminWebType; 
	$bhz = ReqStr('bhz');
	$info = ReqStr('info');
	if (!$bhz || !$info) 
	{
		showMsg('错误参数！');	
		return;		
	}	
	$p_f_db = 'code_party_'.$bhz;
	
//------------------------------建表--------------------------------------------------------	
	$istable = $db->result($db->query("select count(*) from information_schema.tables where table_name = '$p_f_db'"),0);
	if(!$istable){
		$table = $db->query("
		CREATE TABLE `$p_f_db` (
		  `id` int(11) NOT NULL auto_increment,
		  `number` int(11) NOT NULL default '1' COMMENT '第几次生成',
		  `cid` int(11) NOT NULL default '0',
		  `sid` int(11) NOT NULL default '0',
		  `ctime` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '领取时间',
		  `player_id` int(11) NOT NULL default '0',
		  `username` varchar(50) NOT NULL,
		  `nickname` varchar(50) NOT NULL,
		  `code` varchar(100) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `code` (`code`),
		  KEY `cid_code` (`cid`,`code`),
		  KEY `cid_sid_username` (`cid`,`sid`,`username`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".$info."'
		");	
		if ($table) 
		{
			showMsg('创建活动兑换券表['.$p_f_db.']成功！',"",'','greentext');
			insertServersAdminData(0,0,0,'兑换券表','表后缀(_'.$bhz.')，描述('.$info.')');//插入操作记录
		}else{
			showMsg('创建活动兑换券表['.$p_f_db.']失败！');
		}
		
		
	}else{
		showMsg('创建失败，['.$p_f_db.']已存在！');
	}
	$db->close();			
		
}


 //--------------------------------------------------------------------------------------------提交生成激活码
function  SaveCodeAdd() 
{
	global $db,$adminWebID,$adminWebType; 
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	$item_val = ReqNum('item_val');
	$ingot = ReqNum('ingot');
	$coins = ReqNum('coins');
	$edate = ReqStr('edate');
	$name = ReqStr('name');
	$item_id = ReqNum('item_id');
	$item_name = ReqStr('item_name');
	$num = ReqNum('num');
	$juche = ReqNum('juche');	

	if (!$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}
	if (!$name) 
	{
		showMsg('用途说明未输入！');	
		return;		
	}
/*	if (!$num) 
	{
		showMsg('生成数量有错！');	
		return;		
	}*/
	if ($num > 10000) 
	{
		showMsg('一次性只能生成10000条激活码！');	
		return;		
	}
	if ($juche == 1) 
	{
		$batch = $db->result($db->query("select count(*) from code_batch where sid = '$sid' and juche = 1"),0); //获得总条数
		if ($batch) 
		{
			showMsg('该服已发布过支持自动生成兑换券的活动，无法重复发布！',"",'','','','n');	
			return;
		}	
	}	

	$msg = $query = $db->query("
	insert into 
		code_batch
		(`cid`,`sid`,`name`,`ingot`,`coins`,`num`,`item_id`,`item_name`,`item_val`,`juche`,`adminID`,`edate`,`ctime`) 
	values 
		('$cid','$sid','$name','$ingot','$coins','$num','$item_id','$item_name','$item_val','$juche','$adminWebID','$edate',now())
	");
	if ($msg) 
	{
		if ($num) 
		{
			$batch_id =  $db->insert_id();
	
			for($i = 0;$i<$num;$i++){
				$code = md5(random(6)."-".random(6)."-".random(6)."-".random(6)."-".random(6)."-".microtime());
				if ($sql != '') $sql .= ',';
				$sql .= "(".$batch_id.",'".$code."')";	
			}
			
			$c = $db->query("insert into code (batch_id,code) values ".$sql."");		
			if ($c) 
			{
				$show = $num.'条兑换券生成成功！[<a href="call.php?action=CallCodeExport&cid='.$cid.'&sid='.$sid.'&batch_id='.$batch_id.'&title='.urlencode($name).'">导出此次生成的兑换券</a>]';	
			}else{
				$show = $num.'条兑换券生成失败！';	
			}
		}
		showMsg('发布成功！'.$show,"",'','greentext','','n');	
		insertServersAdminData($cid,$sid,0,'发布兑换券','用途('.$name.')数量('.$num.')');//插入操作记录
		return;			
	}else{
		showMsg('发布失败！');	
		return;	
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------追加激活码
function  SaveCodeAddAgain() 
{
	global $db,$adminWebID,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$batch_id = ReqNum('batch_id');	
	$num = ReqNum('num');	
	$edate = ReqStr('edate');
	$title = ReqStr('title');
	$juche = ReqNum('juche');
	$item_id = ReqNum('item_id');
	$item_name = ReqStr('item_name');
	
	if ($batch_id) 
	{
		if($num)//如果有追加
		{	
		
			if ($num > 10000) 
			{
				showMsg('一次性只能追加10000条激活码！');	
				return;		
			}		
			$rs = $db->fetch_first("select max(distinct(number)) as number from code where batch_id = '$batch_id'");	//搜索批次
			if($rs['number'])
			{
				$number = $rs['number']+1;	
			}else{
				$number = 1;	
			}
			for($i = 0;$i<$num;$i++){
				$code = md5(random(6)."-".random(6)."-".random(6)."-".random(6)."-".random(6)."-".microtime());
				if ($sql != '') $sql .= ',';
				$sql .= "(".$batch_id.",".$number.",'".$code."')";	
			}
		
			$c = $db->query("insert into code (batch_id,number,code) values ".$sql."");	
			if ($c) 
			{
				$set_add = ",num = num+$num";
				$show_msg = '[<a href="call.php?action=CallCodeExport&cid='.$cid.'&sid='.$sid.'&batch_id='.$batch_id.'&number='.$number.'&title='.urlencode($title.'('.$number.')').'">导出此次追加的激活码</a>]';
			}else{
				showMsg('追加失败！');	
				return;
			}
		}
		$db->query("update code_batch set edate = '$edate',juche = '$juche',item_id = '$item_id',item_name = '$item_name' $set_add where id = '$batch_id'");//更新领取次数
		showMsg('操作成功！'.$show_msg,"",'','greentext','','n');	
		insertServersAdminData($cid,$sid,0,'追加修改激活码','ID('.$batch_id.')数量('.$num.')');//插入操作记录
		return;	

	}else{
		showMsg('操作失败！');	
		return;	
	}
	$db->close();			
		
}
 //--------------------------------------------------------------------------------------------删除激活码批次
function  DelCode() 
{
	global $db,$adminWebID,$adminWebType; 
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	if (empty($id) || empty($cid) || empty($id))
	{
		showMsg('ERROR');
		return;		
	}	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		code_batch
	where 
		cid = '$cid' and sid = '$sid' and id = '$id'
		
	"),0);
	if (!$num)
	{
		showMsg('NULL');
		return;		
	}	
	$return = $db->query("delete from code_batch where cid = '$cid' and sid = '$sid' and id = '$id'");
	$db->query("delete from code where batch_id = '$id'");
	showMsg('SETOK',"",'','greentext');			
	insertServersAdminData($cid,$sid,0,'删除激活码','批次('.$id.')');//插入操作记录
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------复员激活码批次
function  CancelCode() 
{
	global $db,$adminWebID,$adminWebType; 
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	if (empty($cid) || empty($sid))
	{
		showMsg('错误参数！');
		return;		
	}	
	if ($id)
	{
		$set_id = "  and id = '$id' ";	
	}
	$brs = $db->fetch_first("select id from code_batch where cid = '$cid' and sid = '$sid'");	//搜索批次
	if($brs){
		$db->query("update code set username = '',player_id = 0,ip = '',ctime = '0000-00-00 00:00:00'  where batch_id = '$brs[id]' $set_id");//恢复
		showMsg('复原成功！',"",'','greentext');			
		insertServersAdminData($cid,$sid,0,'复原兑换券','('.$cid.')('.$sid.')('.$id.')');//插入操作记录
	}else{
		showMsg('无此批次兑换券！');			
	}
	$db->close();			
		
}

?> 