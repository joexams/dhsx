<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
//------------------------------------------------------玩家礼包
function callPlayerGift() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//left join player_item_upgrade_cd E on A.id = E.player_item_id and E.player_id = '$id' 
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_gift  A
		where 
			A.player_id = '$uid'					
		"),0); //获得总条数
	if($num){			
		$query = $pdb->query("
		select 
			A.*,
			B.name as gift_name,
			C.name as type_name
		from 
			player_gift A
			left join item B on A.gift_id = B.id
			left join super_gift_type C on A.type = C.id
		where 
			A.player_id = '$uid' 
		order by 
			id desc
		limit 
			$start_num,$pageNum			
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerGift&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_gift');

}
//-------------------------------------------------------------------------------------------参加帮战成员
function  CallPlayerFactionWarMember() {
	global $pdb,$adminWebCid; 
	$name_url = urlencode($name); 
	$faction_id = ReqNum('faction_id');
	$faction_war_id = ReqNum('faction_war_id');
	$member_count = ReqNum('member_count');
	$name = ReqStr('name');
	$year = ReqNum('year');
	$month = ReqNum('month');
	$day = ReqNum('day');
	
	$query = $pdb->query("
	select 
		A.*,
		C.username,
		C.nickname
	from 
		player_join_faction_war_record A
		left join player_faction_member B on A.player_id = B.player_id
		left join player C on B.player_id = C.id
	where 
		A.faction_war_id = '$faction_war_id'
		and B.faction_id = '$faction_id'
		and A.year = '$year'
		and A.month = '$month'
		and A.day = '$day'
	");
	$num = $pdb->num_rows($query);
	if($num)
	{
		while($rs = $pdb->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		
	}
	$pdb->close();
	include_once template('player_log_faction_join_war_member');
	
}
//------------------------------------------------------调用赠送的灵件

function callSoul() {
	global $pdb,$sid;
	$query = $pdb->query("select A.*,B.name as quality_name from soul A left join soul_quality B on A.soul_quality_id = B.id order by A.id asc");
	echo "obj.options[obj.options.length] = new Option('选择灵件','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('[".$rs["quality_name"]."] ".$rs["name"]."','".$rs["id"]."');\n"; 
		}
		
	}
	$pdb->close();

}

//------------------------------------------------------调用命格列表

function callFate() {
	global $pdb,$sid;
	$query = $pdb->query("select A.*,B.name as fate_name from fate A  left join fate_quality B on A.fate_quality_id = B.id where A.fate_quality_id > 1 order by A.fate_quality_id asc");
	echo "obj.options[obj.options.length] = new Option('选择命格','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]." - ".$rs["fate_name"]."','".$rs["id"]."');\n"; 
		}
		
	}
	$pdb->close();

}
//------------------------------------------------------调用物品装备类型

function callItemType() {
	global $pdb,$sid;

	$query = $pdb->query("select id,name from item_type where id not in (10007,10009,10010) order by id asc");
	echo "obj.options[obj.options.length] = new Option('选择装备物品类型','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."','".$rs["id"]."');\n"; 
		}
	}

	$pdb->close();
}
//------------------------------------------------------调用物品装备

function callItem() {
	global $pdb,$sid;
	$type_id = ReqNum('type_id');
	$query = $pdb->query("select id,name,type_id,require_level from item where type_id  = '$type_id' order by require_level desc,type_id asc");
	echo "obj.options[obj.options.length] = new Option('选择装备物品','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			if ($rs['type_id'] <= 7) $rs["level"] = '-'.$rs['require_level'].'级';
			echo "obj.options[obj.options.length] = new Option('".$rs["name"].$rs["level"]."','".$rs["id"]."');\n"; 
		}
	}
	$pdb->close();
}


//------------------------------------------------------调用卖出灵件记录

function callPlayerSoulLog() {
	global $pdb,$sid,$server;
	$username = ReqStr('username');
	$usertype = ReqNum('usertype');
	$soul_id = ReqNum('soul_id');
	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];		
	if ($usertype == 1) {
	
		$player = api_admin::find_player_by_username($username);
	}elseif ($usertype == 2){
		$player = api_admin::find_player_by_nickname($username);
	}		
	if (!$player['result']) {
		echo "无此帐号！"; 
		return;
	}
	//-----------------------------------------------------------------------------------------------
	$query = $pdb->query("
	select 
		A.*,
		B.name as soul_name,
		C.name as quality_name
	from 
		player_soul_change_record A
		left join soul B on A.soul_id = B.id
		left join soul_quality C on B.soul_quality_id = C.id
	where 
		A.player_id = '$player[player_id]'
		and A.type = 1
		and A.soul_id = '$soul_id'
	order by 
		A.id desc 
	");
	while($rs = $pdb->fetch_array($query))
	{	
		$lidArr .= $rs['soul_attribute_id_location_1'].','.$rs['soul_attribute_id_location_2'].','.$rs['soul_attribute_id_location_3'].',';
		$list_array[] =  $rs;
	}
	if($lidArr){
		
		$lidArr = substr($lidArr,0,strlen($lidArr)-1);
		//echo $lidArr;
		$query = $pdb->query("
		select 
			A.id,
			A.unit,
			B.name
		from 
			soul_attribute A
			left join war_attribute_type B on A.war_attribute_type_id = B.id
		where 
			A.id in ($lidArr)
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['unit'] = $rs['unit'] < 1 ?  '%' : '';
			$soula[$rs['id']] =  $rs;
		}	
		//print_r($soula);	
	}
	$pdb->close();
	include_once template('setting_soul_log_add');
}

//------------------------------------------------------调用玩家的伙伴

function callPlayerRole() {
	global $pdb,$sid,$server;
	$username = ReqStr('username');
	$usertype = ReqNum('usertype');
	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];		
	if ($usertype == 1) {
	
		$player = api_admin::find_player_by_username($username);
	}elseif ($usertype == 2){
		$player = api_admin::find_player_by_nickname($username);
	}		

	if (!$player['result']) {
		echo "obj.options[obj.options.length] = new Option('无此帐号！','0');\n"; 
		return;
	}
	//-----------------------------------------------------------------------------------------------
	
	$query = $pdb->query("
	select 
		A.id,
		B.name 
	from 
		player_role A 
		left join role B on A.role_id = B.id
	where 
		A.player_id = '$player[player_id]'
	order by 
		A.id asc
	");
	echo "obj.options[obj.options.length] = new Option('赠送所有伙伴','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."','".$rs["id"]."');\n"; 
		}
	}
	$pdb->close();
}

//------------------------------------------------------调用赠送的物品装备
function callItemGift() {
	global $pdb,$sid;

	$query = $pdb->query("select id,name,type_id,require_level from item where type_id  = 10010 order by require_level desc,type_id asc");
	echo "obj.options[obj.options.length] = new Option('选择礼包','0');\n"; 
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{
			echo "obj.options[obj.options.length] = new Option('".$rs["name"]."','".$rs["id"]."');\n"; 
		}
	}

	$pdb->close();
}








//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

//-------------------------------------------------------------------------------------------在线报表
function  callPlayerDataRegHour() {
	global $pdb,$sid;
	$day = ReqStr('day');
	
	for ($i=0;$i<=23;$i++){
		$hour_list[str_pad($i,2,"0",STR_PAD_LEFT)] = str_pad($i,2,"0",STR_PAD_LEFT);
		
	}	
	
	//-----------登陆/注册/测试------------------------------------------------------------
	$query = $pdb->query("
	select 		
		max(login_count) as maxLoginNum,
		sum(login_count) as allLoginNum,
		max(create_count) as maxCreateNum,
		sum(create_count) as allCreateNum,
		max(online_count) as maxOnlineNum,
		sum(online_count) as allOnlineNum,
		max(register_count) as maxRegNum,
		sum(register_count) as allRegNum,
		count(*) as hour_count
	from 
		server_state
	where 
		 date_format(from_unixtime(`time`), '%Y-%m-%d') = '$day'
	");
	if($pdb->num_rows($query)){
		$ssrs = $pdb->fetch_array($query);
		$maxLoginNum = $ssrs['maxLoginNum'];//-----------最大登陆
		$allLoginNum = $ssrs['allLoginNum'];//-----------所有登陆
		$maxCreateNum = $ssrs['maxCreateNum'];//-----------最大创建
		$allCreateNum = $ssrs['allCreateNum'];//-----------所有创建
		$maxOnlineNum = $ssrs['maxOnlineNum'];//-----------最大在线
		$allOnlineNum = $ssrs['allOnlineNum'];//-----------所有在线
		$maxRegNum = $ssrs['maxRegNum'];//-----------最大注册
		$allRegNum = $ssrs['allRegNum'];//-----------所有注册
		$hour_count = $ssrs['hour_count'];//-----------小时数
		
	}
	
	
	$query = $pdb->query("
	select 
		*,
		date_format(from_unixtime(`time`), '%H') as hour
	from 
		server_state 
	where 
		date_format(from_unixtime(`time`), '%Y-%m-%d') = '$day' 

	");
	if($pdb->num_rows($query)){				
		while($rs = $pdb->fetch_array($query)){	
			$array[$rs['hour']] =  $rs;
		}
	}
	//print_r(var_export($array));
	$pdb->close();

	include_once template('player_data_reg_hour');
}
//-------------------------------------------------------------------------------------------详细在线报表
function  callPlayerDataOnline() {
	global $pdb,$sid;
	$day = ReqStr('day');
	$day_s = strtotime($day.' 00:00:00');
	$day_e = strtotime($day.' 23:59:59');
	//-----------在线------------------------------------------------------------
	$query = $pdb->query("
	select 		

		max(max_online_count) as maxOnlineNum
	from 
		max_online
	where 
		`time` >= '$day_s' 
		and `time` <= '$day_e' 
	");
	if($pdb->num_rows($query)){
		$ssrs = $pdb->fetch_array($query);
		$maxOnlineNum = $ssrs['maxOnlineNum'];//-----------最大在线
		
	}
	
	
	$query = $pdb->query("
	select 
		*,
		date_format(from_unixtime(`time`), '%H:%i') as t
	from 
		max_online 
	where 
		`time` >= '$day_s' 
		and `time` <= '$day_e' 

	");
	if($pdb->num_rows($query)){				
		while($rs = $pdb->fetch_array($query)){	
			$array[$rs['t']] =  $rs;
		}
	}
	//print_r(var_export($array));
	$pdb->close();

	include_once template('player_data_online');
}
//------------------------------------------------------物品装备
function callPlayerItem() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//left join player_item_upgrade_cd E on A.id = E.player_item_id and E.player_id = '$id' 
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_item  A
		where 
			A.player_id = '$uid'	
		and 
			A.grid_id <= '200'					
		"),0); //获得总条数
	if($num){			
		$query = $pdb->query("
		select 
			A.*,
			B.name as item_name,
			C.name as upgrade_name,
			D.name as pack_grid_name
		from 
			player_item A
			left join item B on A.item_id = B.id
			left join item_upgrade C on A.upgrade_level = C.level
			left join item_pack_grid D on A.grid_id = D.id
	
		where 
			A.player_id = '$uid' 
		and 
			A.grid_id <= '200'
		order by 
			grid_id asc,
			id asc
		limit 
			$start_num,$pageNum			
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerItem&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_item');

}

//------------------------------------------------------玩家副本
function callPlayerMissionRecord() 
{

	global $pdb,$uid,$sid,$player,$adminWebType,$page;
	$see = ReqStr('see');
	$type = ReqStr('type');
	$order = ReqStr('order');
	$winid = ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	if (!$order || $order == 'desc') 
	{
		$order = 'desc';
		$order2 = 'asc';
	}elseif($order == 'asc'){
		$order2 = 'desc';
	}	
	$tollgatenum = $pdb->result($pdb->query("select count(distinct(`lock`)) from mission "),0);//总关卡
	$tollgateplayernum = $pdb->result($pdb->query("select count(*) from player_mission_record  A where A.player_id = '$uid'"),0); //玩家关卡数据		
	$timesNum = $pdb->result($pdb->query("
		select 
			sum(times) 
		from 
			player_mission_record  A
		where 
			A.player_id = '$uid'			
		"),0); //获得总条数

	$timesmax = $pdb->result($pdb->query("
		select 
			max(times) 
		from 
			player_mission_record  A
		where 
			A.player_id = '$uid'			
		"),0); //获得总条数

	$failedchallengemax = $pdb->result($pdb->query("
		select 
			max(failed_challenge) 
		from 
			player_mission_record  A
		where 
			A.player_id = '$uid'			
		"),0); //获得总条
	//------------------------------------------------------------
	if ($see == '_data')
	{
	
		$query = $pdb->query("
		select 
			A.*,
			B.name as mission_name,
			C.name as mission_section_name,
			D.name as town_name
		from 
			player_mission_record A
			left join mission B on A.mission_id = B.id
			left join mission_section C on B.mission_section_id = C.id
			left join town D on C.town_id = D.id	
		where 
			A.player_id = '$uid' 
		order by 
			B.lock $order	
		");
		if($tollgateplayernum){			

			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}	
		}	
	}else{
		//------------------------------------------------------------

		if($tollgateplayernum){			
		
			$query = $pdb->query("
			select 
				A.*,
				B.name as mission_name,
				C.name as mission_section_name,
				D.name as town_name
			from 
				player_mission_record A
				left join mission B on A.mission_id = B.id
				left join mission_section C on B.mission_section_id = C.id
				left join town D on C.town_id = D.id	
			where 
				A.player_id = '$uid' 
			order by 
				B.lock desc,
				A.mission_id desc
			limit 
				$start_num,$pageNum				
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = ajaxPage($tollgateplayernum, $pageNum, $page, "player_call.php?action=callPlayerMissionRecord&sid=$sid&uid=$uid&type=$type&winid=$winid", '',10,$winid);		
		}
	}
	
	$pdb->close();
	include_once template('player_mission_record'.$see);
}

//------------------------------------------------------玩家任务
function callPlayerQuest() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_quest  A
		where 
			A.player_id = '$uid'			
		"),0); //获得总条数
	if($num){			
		
		$query = $pdb->query("
		select 
			A.*,
			B.title as quest_title
		from 
			player_quest A
			left join quest B on A.quest_id = B.id
		where 
			A.player_id = '$uid' 
		order by 
			B.lock desc
		limit 
			$start_num,$pageNum					
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerQuest&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_quest');

}

//------------------------------------------------------玩家每日任务
function callPlayerDayQuest() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_day_quest  A
		where 
			A.player_id = '$uid'			
		"),0); //获得总条数
	if($num){			
		
		$query = $pdb->query("
		select 
			A.*,
			B.title as quest_title
		from 
			player_day_quest A
			left join quest B on A.quest_id = B.id
		where 
			A.player_id = '$uid' 
		order by 
			A.quest_id asc
		limit 
			$start_num,$pageNum					
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerDayQuest&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_day_quest');

}



//------------------------------------------------------玩家伙伴身上的装备
function callPlayerRoleEqui() 
{

	global $pdb,$uid,$sid,$player,$page;
	$role_id=ReqNum('role_id');
	$role_name=ReqStr('role_name');	
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_item  A
		where 
			A.player_role_id = '$role_id'
		and 
			A.grid_id > '200' and A.grid_id <= '300'		
		"),0); //获得总条数
	if($num){		
		$query = $pdb->query("
		select 
			A.*,
			B.name as item_name,
			C.name as upgrade_name,
			D.name as pack_grid_name,
			E.name as quality_name
			
		from 
			player_item A
			left join item B on A.item_id = B.id
			left join item_upgrade C on A.upgrade_level = C.level
			left join item_pack_grid D on A.grid_id = D.id
			left join item_quality E on B.quality = E.quality
		where 
			A.player_role_id = '$role_id' 	
		and 
			A.grid_id > '200' and A.grid_id <= '300'		
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");	
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerRoleEqui&sid=$sid&uid=$uid&role_id=$role_id&role_name=$role_name&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_role_equi');

}


//------------------------------------------------------玩家奇术数据
function callPlayerResearch() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$research_list = globalDataListPlayer('research');//奇术
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_research  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){	
		$query = $pdb->query("
		select 
			A.*,
			B.name as research_name,
			C.name as deploy_mode_name,
			C.id as deploy_mode_id
		from 
			player_research A
			left join research B on A.research_id = B.id
			left join deploy_mode C on B.id = C.research_id
		where 
			A.player_id = '$uid' 		
		order by 
			B.research_type_id asc
		limit 
			$start_num,$pageNum			
		");	
				
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['deploy_mode_name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerResearch&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_research');

}

//------------------------------------------------------玩家好友
function callPlayerFriends() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_friends  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){
		
		$query = $pdb->query("
		select 
			A.*,
			B.username as friends_name,
			B.nickname
		from 
			player_friends A
			left join player B on A.friend_id = B.id
		where 
			A.player_id = '$uid' 		
		order by 
			A.group_type desc
		limit 
			$start_num,$pageNum				
		");	
				
		while($rs = $pdb->fetch_array($query))
		{
			$rs['name_url'] = urlencode($rs['friends_name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerFriends&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);		
	}
	
	$pdb->close();
	include_once template('player_friends');

}



//------------------------------------------------------帮派成员
function CallFactionMember() 
{

	global $pdb,$uid,$sid,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$name=ReqStr('name');
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_faction_member  A
		where 
			A.faction_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.username,
			B.nickname,
			C.job_id,
			D.name as faction_job_name
		from 
			player_faction_member A
			left join player B on A.player_id = B.id
			left join player_faction_job C on A.id = C.faction_id and A.player_id = C.player_id
			left join faction_job D on C.job_id = D.id

		where 
			A.faction_id = '$uid'
		order by 
			B.id asc,
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			//echo $rs['job_id'].'<br />';
			$rs['add_time'] = date('Y-m-d H:i:s',$rs['add_time']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=CallFactionMember&sid=$sid&uid=$uid&name=$name&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_faction_member');

}


//------------------------------------------------------帮派公告
function CallFactionNotice() 
{

	global $pdb,$uid,$sid,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$name=ReqStr('name');
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_faction_notice  A
		where 
			A.faction_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.username
		from 
			player_faction_notice A
			left join player B on A.player_id = B.id
		where 
			A.faction_id = '$uid'
		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['req_time'] = date('Y-m-d H:i:t',$rs['req_time']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=CallFactionNotice&sid=$sid&uid=$uid&name=$name&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_faction_notice');

}

//------------------------------------------------------帮派申请
function CallFactionRequest() 
{

	global $pdb,$uid,$sid,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$name=ReqStr('name');
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_faction_request  A
		where 
			A.faction_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.username
		from 
			player_faction_request A
			left join player B on A.player_id = B.id
		where 
			A.faction_id = '$uid'
		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['req_time'] = date('Y-m-d H:i:s',$rs['req_time']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=CallFactionRequest&sid=$sid&uid=$uid&name=$name&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_faction_request');

}



//------------------------------------------------------玩家农田
function callPlayerFarmland() 
{

	global $pdb,$uid,$sid,$player,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_farmland  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.name as herbs_name,
			D.name as role_name
		from 
			player_farmland A
			left join herbs B on A.herbs_id = B.id
			left join player_role C on A.player_role_id = C.id
			left join role D on C.role_id = D.id
		where 
			A.player_id = '$uid'
		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			if ($rs['harvest_time']) $rs['harvest_time'] = date('Y-m-d H:i:s',$rs['harvest_time']);
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerFarmland&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_farmland');

}




//------------------------------------------------------玩家升级记录
function callPlayerLevelUpRecord() 
{

	global $pdb,$sid,$uid,$player,$page;

	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	$winid=ReqStr('winid');	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			level_up_record  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			C.name as role_name
		from 
			level_up_record A
			left join player_role B on A.player_id = B.player_id and A.player_role_id = B.id
			left join role C on B.role_id = C.id
		where 
			A.player_id = '$uid'
		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	

			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerLevelUpRecord&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_level_up_record');

}



//------------------------------------------------------玩家权值
function callPlayerKey() 
{

	global $pdb,$sid,$uid,$player;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	
	$town_list = globalDataListPlayer('town');//城镇
	$quest_list = globalDataListPlayer('quest');//任务
	$mission_section_list = globalDataListPlayer('mission_section');//副本
	$mission_list = globalDataListPlayer('mission');//剧情
	$research_list = globalDataListPlayer('research');//奇术
	$pack_grid_list = globalDataListPlayer('item_pack_grid','id >= 1 and id <= 100');//背包
	$role_equi_list = globalDataListPlayer('item_pack_grid','id >= 201');//装备
	$warehouse_list = globalDataListPlayer('item_pack_grid','id >= 101 and id <= 200');//仓库
	$game_function_list = globalDataListPlayer('game_function');//功能开放
	$role_list = globalDataListPlayer('role');//招募
	$playerkey = $pdb->fetch_first("select * from player_key where player_id = '$uid'");
	$pdb->close();
	include_once template('player_key');

}


//------------------------------------------------------玩家命格
function callPlayerFate() 
{

	global $pdb,$sid,$uid,$player,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_fate  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.name as fate_name,
			D.name as role_name,
			E.name as quality_name
		from 
			player_fate A
			left join fate B on A.fate_id = B.id
			left join player_role C on A.player_role_id = C.id
			left join role D on C.role_id = D.id
			left join fate_quality E on B.fate_quality_id = E.id

		where 
			A.player_id = '$uid'
		order by 
			A.fate_id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerFate&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_fate');

}




//------------------------------------------------------玩家灵件
function callPlayerSoul() 
{

	global $pdb,$sid,$uid,$player,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_soul  A
		where 
			A.player_id = '$uid'
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.name as soul_name,
			C.name as quality_name,
			D.name as type_name
		from 
			player_soul A
			left join soul B on A.soul_id = B.id
			left join soul_quality C on B.soul_quality_id = C.id
			left join soul_all_type D on B.soul_all_type_id = D.id
		where 
			A.player_id = '$uid'
		order by 
			A.soul_id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$lidArr .= $rs['soul_attribute_id_location_1'].','.$rs['soul_attribute_id_location_2'].','.$rs['soul_attribute_id_location_3'].',';
			
			$list_array[] =  $rs;
		}
		if($lidArr){
			
			$lidArr = substr($lidArr,0,strlen($lidArr)-1);
			//echo $lidArr;
			$query = $pdb->query("
			select 
				A.id,
				A.unit,
				B.name
			from 
				soul_attribute A
				left join war_attribute_type B on A.war_attribute_type_id = B.id
			where 
				A.id in ($lidArr)
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$rs['unit'] = $rs['unit'] < 1 ?  '%' : '';
				$soula[$rs['id']] =  $rs;
			}	
			//print_r($soula);	
		}
		
		
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerSoul&sid=$sid&uid=$uid&winid=$winid", '',10,$winid);
	}
	$pdb->close();
	include_once template('player_soul');

}
//------------------------------------------------------玩家伙伴服用丹药记录
function callPlayerRoleElixir() 
{

	global $pdb,$uid,$sid,$player,$page;
	$role_id=ReqNum('role_id');
	$role_name=ReqStr('role_name');	
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_role_elixir  A
		where 
			A.player_role_id = '$role_id'		
		"),0); //获得总条数
	if($num){		
		$query = $pdb->query("
		select 
			A.*,
			B.name as item_name
		from 
			player_role_elixir A
			left join item B on A.item_id = B.id
		where 
			A.player_role_id = '$role_id' 		
		order by 
			A.item_id asc
		limit 
			$start_num,$pageNum				
		");	
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerRoleElixir&sid=$sid&uid=$uid&role_id=$role_id&role_name=$role_name&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_role_elixir');

}

//------------------------------------------------------玩家伙伴命格
function callPlayerRoleFate() 
{

	global $pdb,$uid,$sid,$player,$page;
	$role_id=ReqNum('role_id');
	$role_name=ReqStr('role_name');	
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_role_fate  A
		where 
			A.player_role_id = '$role_id'		
		"),0); //获得总条数
	if($num){		
		$query = $pdb->query("
		select 
			A.*,
			B.fate_id,
			B.fate_level,
			B.experience,
			C.name as fate_name,
			D.name as quality_name
		from 
			player_role_fate A
			left join player_fate B on A.player_fate_id = B.id
			left join fate C on B.fate_id = C.id
			left join fate_quality D on C.fate_quality_id = D.id
			
		where 
			A.player_role_id = '$role_id' 		
		order by 
			A.player_fate_id asc
		limit 
			$start_num,$pageNum				
		");	
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerRoleFate&sid=$sid&uid=$uid&role_id=$role_id&role_name=$role_name&winid=$winid", '',10,$winid);		
	}
	$pdb->close();
	include_once template('player_role_fate');

}

//------------------------------------------------------消费分级统计
function callPlayerDataConsumeLevel() 
{

	global $pdb,$sid,$uid,$player;
	$p = ReqNum('p');
	$type = ReqNum('type');
	$name = ReqStr('name');
	$username = ReqStr('username');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$day = ReqNum('day');
	$hour = $day*24;	
	if ($username) 
	{
		$set_username = " and B.username = '$username'";	
	}else{
		$set_is_tester = " and B.is_tester = 0 ";	
	}	
	if ($day) {
		$set_left_day = "left join player_trace D on B.id = D.player_id";
		$set_day = "and DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(D.last_login_time), '%Y-%m-%d %H:%i:%s') AND D.last_login_time > 0";
	}
	if ($stime && $etime) 
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') >= '$stime' AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$etime'";
	}
	
	
	if($p == 1)
	{
		$query = $pdb->query("
		select
			player_id
		from 
			player_charge_record
		where
			total_ingot > 0
		order by 
			player_id asc
		");	
		while($prs = $pdb->fetch_array($query)){
			$player_id[] = $prs['player_id'];
		}
		$player_id_list = implode(',', $player_id);
		$set_p = " and B.id not in ($player_id_list)";
	}elseif($p == 2){
		$set_p = "and C.total_ingot > 0 AND B.vip_level >= 1";
		$set_left = "left join player_charge_record C on B.id = C.player_id";
	}elseif($p == 3){
		$set_p = " and C.total_ingot > 0 AND B.vip_level < 6";
		$set_left = "left join player_charge_record C on B.id = C.player_id";
	}elseif($p == 4){
		$set_p = " and C.total_ingot > 0 AND B.vip_level >= 6";
		$set_left = "left join player_charge_record C on B.id = C.player_id";
	}elseif($p == 5){
		$set_p = " and C.total_ingot > 0 AND B.vip_level >= 3";
		$set_left = "left join player_charge_record C on B.id = C.player_id";
	}	

	
	
	$rs = $pdb->fetch_first("
	select 		
		COUNT(distinct(A.player_id)) AS player_count,
		COUNT(A.type) AS type_count,
		SUM(A.value+A.change_charge_value) AS value_count
	from 
		player_ingot_change_record A
		left join player B on A.player_id = B.id
		$set_left
		$set_left_day
	where
		A.type = '$type'
		$set_p
		$set_is_tester
		$set_username
		$set_time
		$set_day
	");
	if($rs){
		$player_count = $rs['player_count'];
		$type_count = $rs['type_count'];
		$value_count = $rs['value_count'];
	}


	$query = $pdb->query("
	select 
		COUNT(distinct(A.player_id)) AS player_count,
		COUNT(A.type) AS type_count,
		(A.value+A.change_charge_value) AS value2
	from 
		player_ingot_change_record A
		left join player B on A.player_id = B.id
		$set_left
		$set_left_day
	where
		A.type = '$type'
		$set_p
		$set_is_tester
		$set_username
		$set_time
		$set_day
	group by 
		value2
	order by
		value2 asc
	
	");	
	while($crs = $pdb->fetch_array($query)){
		$list_arrray[$crs['value2']] = $crs;
	}		
	
	$pdb->close();
	include_once template('player_data_consume_level');

}


//------------------------------------------------------命格分级统计
function callPlayerDataFateLevel() 
{

	global $pdb,$sid,$uid,$player;
	$v = ReqNum('v');
	$p = ReqNum('p');
	$id = ReqNum('id');
	$name = ReqStr('name');

	if($v == 1)
	{
		$set_v = "AND B.vip_level < 6";
	}elseif($v == 2){
		$set_v = "AND B.vip_level >= 6";
	}	
	if($p == 1)
	{
		$query = $pdb->query("
		select
			player_id
		from 
			player_charge_record
		where
			total_ingot > 0
		order by 
			player_id asc
		");	
		while($prs = $pdb->fetch_array($query)){
			$player_id[] = $prs['player_id'];
		}
		$player_id_list = implode(',', $player_id);
		$set_p = " and B.id not in ($player_id_list)";
	}elseif($p == 2){
		$set_p = "and C.total_ingot > 0";
		$set_left = "left join player_charge_record C on B.id = C.player_id";
	}	

	
	
	$rs = $pdb->fetch_first("
	select 		
		COUNT(distinct(A.player_id)) AS player_count,
		COUNT(A.fate_id) AS fate_count
	from 
		player_fate A
		left join player B on A.player_id = B.id
		$set_left
	where
		A.fate_id = '$id'
		and B.is_tester = 0
		$set_p
		$set_v
		
	");
	if($rs){
		$player_count = $rs['player_count'];
		$fate_count = $rs['fate_count'];
	}


	$query = $pdb->query("
	select 
		COUNT(distinct(A.player_id)) AS player_count,
		COUNT(A.fate_id) AS fate_count,
		A.fate_level
	from 
		player_fate A
		left join player B on A.player_id = B.id
		$set_left
	where
		A.fate_id = '$id'
		and B.is_tester = 0
		$set_p
		$set_v
	group by 
		A.fate_level
	");	
	while($frs = $pdb->fetch_array($query)){
		$list_array[$frs['fate_level']] = $frs;
	}		
	//print_r($consume);
	
	$pdb->close();
	include_once template('player_data_fate_level');

}

//------------------------------------------------------装备品质分级详细
function callPlayerDataItemLevel() 
{

	global $pdb,$page;
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$quality=ReqNum('quality');
	$level=ReqNum('level');	
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$pageNum = 10; 
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_item A,
			item B,
			player C
		where 
			A.item_id = B.id
			and A.player_id = C.id
			and B.quality = '$quality'
			and A.upgrade_level = '$level'
			and B.type_id <= 6
			and C.is_tester = 0
		"),0); //获得总条数
	if($num){		
		$query = $pdb->query("
		select 
			A.player_id,
			A.upgrade_level,
			A.item_id,
			B.name as item_name,
			C.is_tester,
			C.disable_login,
			C.disable_talk,
			C.username,
			C.nickname,
			D.level as player_level
		from 
			player_item A,
			item B,
			player C,
			player_role D
			
		where 
			A.item_id = B.id
			and A.player_id = C.id
			and A.player_id = D.player_id 
			and C.main_role_id = D.id
			and B.quality = '$quality'
			and A.upgrade_level = '$level'
			and B.type_id <= 6
			and C.is_tester = 0
		order by
			A.player_id asc
		limit 
			$start_num,$pageNum			
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "player_call.php?action=callPlayerDataItemLevel&cid=$cid&sid=$sid&quality=$quality&level=$level&winid=$winid", '',10,$winid);		
	}

	
	$pdb->close();
	include_once template('player_data_item_level');

}


//------------------------------------------------------伙伴等级详细
function callPlayerDataRoleLevel() 
{

	global $pdb;
	$id=ReqNum('id');
	$cid=ReqNum('cid');
	$sid=ReqNum('sid');
	$name=ReqStr('name');

	$rs = $pdb->fetch_first("
	select 		
		COUNT(role_id) AS role_count,
		COUNT(CASE WHEN state = 0 THEN role_id END) AS role_in_count,
		COUNT(CASE WHEN state = 1 THEN role_id END) AS role_out_count
	from 
		player_role
	where
		role_id = '$id'
		
	");
	if($rs){
		$role_count = $rs['role_count'];
		$role_in_count = $rs['role_in_count'];
		$role_out_count = $rs['role_out_count'];
	}



	$query = $pdb->query("
	select 
		COUNT(role_id) AS role_count,
		COUNT(CASE WHEN state = 0 THEN role_id END) AS role_in_count,
		COUNT(CASE WHEN state = 1 THEN role_id END) AS role_out_count,
		level
	from 
		player_role
	where
		role_id = '$id'
	group by 
		level
	order by
		level asc
	");	
	while($rrs = $pdb->fetch_array($query)){
		$list_arrray[] = $rrs;
	}		
	
	$pdb->close();
	include_once template('player_data_role_level');

}
//------------------------------------------------------滚服情况
function callPlayerOtherServerP() 
{
	global $db,$pdb;
	
	$osid=ReqNum('osid');
	$query = $db->query("select db_name from servers where sid = '$osid'");
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);
		$db_name = $server['db_name'];
		
		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player A
			left join {$db_name}.player B on A.username = B.username
		where  
			A.username = B.username
		"),0); //获得总条数
		echo $num;
	
	}


		
}
//--------------------------------------------------------------------------------------------在线人数

function callNowOnline() {
	global $cid,$pdb,$sid,$server; 
	include_once(dirname(dirname(dirname(__FILE__)))."/online_data.php");
	if(!$sid)
	{
		echo 0;	
		return;	
	}	
	//require_once callApiVer($server['server_ver']);
	//api_base::$SERVER = $server['api_server'];
	//api_base::$PORT   = $server['api_port'];
	//api_base::$ADMIN_PWD   = $server['api_pwd'];

	//$now_onlie = api_admin::count_online_player();
	
	$now_onlie = $online_data[$server['name']];
	if ($now_onlie)
	{
		echo $now_onlie;
	}else{
		echo 0;		
	}
}

//--------------------------------------------------------------------------------------------今日消费

function callTodayConsume() {
	global $cid,$pdb,$sid,$server; 
	if(!$sid)
	{
		echo 0;	
		return;	
	}
	$today = strtotime(date('Y-m-d'));
	$con = $pdb->fetch_first("
	select 
		SUM(if(change_charge_value < 0,change_charge_value,0)) AS consume
	from 
		player_ingot_change_record
	where 
		change_time >= '$today'		
	"); 		
	if($con){
		$consume = round(($con['consume']/$server['coins_rate']),2);				
		echo $consume;
	}else{
		echo 0;		
	}
}


//--------------------------------------------------------------------------------------------城镇在线人数

function callTownOnline() {
	global $cid,$pdb,$sid,$server; 
	$id = ReqNum('id');
	if(!$id)
	{
		echo 0;	
		return;	
	}	

	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];

	$onlie = api_admin::get_town_player_count($id);
	if ($onlie)
	{
		echo $onlie['player_count'];
	}else{
		echo 0;		
	}
}


//--------------------------------------------------------------------------------------------登陆服务器模版数据后台
function  CallTemplates() 
{
	global $db,$adminWebID,$adminWebType,$adminWebName,$adminWebServersPower,$cookiepath,$cookiedomain; 
	$sid = ReqNum('sid');
	if(!$sid)
	{
		showMsg('未选择服务器！','','web','','','n');	
		return;
	}
	if($adminWebType != 's')
	{
		showMsg('您没有此权限！');	
		return;	

	}
	
	$query = $db->query("
	select 
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name,
		A.name as server_name,
		B.name as company_name
	from 
		servers A
		left join company B on A.cid = B.cid
	where 
		A.sid = '$sid'
	");
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);
		setcookie('qq_game_mysql_admin', authcode($server['company_name'].'-'.$server['server_name']."@#$%".$server['db_server']."@#$%".$server['db_root']."@#$%".$server['db_pwd']."@#$%".$server['db_name'], 'ENCODE'),0,$cookiepath,$cookiedomain);
		//-----------------------------------------------------------------------------------------------
		
		if (setcookie('qq_game_auth_admin', authcode($adminWebName."@#$%".$adminWebServersPower, 'ENCODE'),0,$cookiepath,$cookiedomain)){
			header("location:".SXD_SYSTEM_TEMP_PATH."");
		}else{
			showMsg('您没有权限，登陆失败！','','web','','','n');
		}
						
	}else{
		showMsg('无此服务器！','','web','','','n');	
		return;
	}

	$db->close();
}

?>