<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
function  Player()
{
	global $pdb,$db,$cid,$sid,$server,$adminWebType,$page;
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$username = trim(ReqStr('username'));
	$ip = trim(ReqStr('ip'));
	$player_id = trim(ReqNum('player_id'));
	$order = ReqStr('order');
	$type = ReqStr('type');
	$mt = ReqNum('mt');
	$mtmid = ReqNum('mtmid');
	$source = ReqStr('source');
	$day = ReqNum('day');
	$level = ReqNum('level');
	$vip = ReqNum('vip');
	$st = ReqNum('st');
	$level_s = ReqNum('level_s');
	$level_e = ReqNum('level_e');
	$yellow = ReqNum('yellow');
	$yellow_level = ReqNum('yellow_level');
	$blue = ReqNum('blue');
	$blue_level = ReqNum('blue_level');	
	
	if ($yellow == 1) 
	{
		$set_yellow = " and A.is_yellow_vip = 1";
	}elseif($yellow == 2){
		$set_yellow = " and A.is_yellow_year_vip = 1";
	}
	if ($blue == 1) 
	{
		$set_blue = " and A.is_blue_vip = 1";
	}elseif($blue == 2){
		$set_blue = " and A.is_blue_year_vip = 1";
	}	
		
	if ($yellow_level) 
	{
		$set_yellow_level = " and A.yellow_vip_level = '$yellow_level'";
	}	
	if ($blue_level) 
	{
		$set_blue_level = " and A.blue_vip_level = '$blue_level'";
	}	
	
	if ($level_s && $level_e) 
	{
		$set_role_level = " and E.level >= '$level_s' and E.level <= '$level_e'";
	}else{
		//$level_s = 1;
		//$level_e = 100;
	}	
	$hour = $day*24;
	for ($i=1;$i<=12;$i++)
	{
		$vip_list[] = $i;
		
	}
	if ($st == 1) 
	{	
		$set_username = $username ? " and (A.username = '$username' OR A.nickname = '$username')" : '';
	}elseif ($st == 2){
		$set_username = $username ? " and (A.username like '%$username%' OR A.nickname like '%$username%')" : '';
	}
	$set_uid = $player_id ? " and (A.id = '$player_id')" : '';
	$set_ip = $ip ? " and (C.last_login_ip = '$ip' or C.first_login_ip = '$ip')" : '';		
	$set_mission = (!$mid && !$t) ? "left join mission I on F.mission = I.lock": '';
	$set_time = $day ? " AND DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.total_ingot > 0 AND A.is_tester = 0" : '';
	$set_level = $level ? " AND E.level = '$level'" : '';
	if ($vip) 
	{
		$set_vip = " and A.vip_level >= '$vip'";
		$set_order = "A.vip_level desc,";	
	}
	if ($type == 'test_1_2') 
	{
		ReServerTest($cid,$sid);
		$set_type = "and (A.is_tester = 1 OR A.is_tester = 2)";
	}elseif($type == 'test_3'){
		$set_type = "and A.is_tester = 3";
	}elseif($type == 'test_4'){
		$set_type = "and A.is_tester = 4";
	}elseif($type == 'login'){
		$set_type = "and A.disable_login <> '' and A.disable_login > UNIX_TIMESTAMP(NOW())";
	}elseif($type == 'talk'){
		$set_type = "and A.disable_talk <> '' and  A.disable_talk > UNIX_TIMESTAMP(NOW())";
	}elseif($type == 'more_pay'){
		
		
		$query = $db->query("
		select 
			username
		from 
			pay_player
		where 
			cid = '$cid'
			and sid_arr like '%,%'
		");
		if($db->num_rows($query))
		{
			while($mrs = $db->fetch_array($query))
			{	
				$more_array .=  $mrs['username'].',';
			}

				
			$more_list = substr($more_array,0,strlen($more_array)-1);
			$more_list = "'".str_replace(",", "','",$more_list)."'";
			$set_type = "and username in ($more_list)";
				
			
			
		}else{
			$set_type = "and username = '2#$^@&$@^$&$SRGW$&@URHW'";
		}
	
	}
	
	
	if ($source) 
	{
		$set_source = "and C.source = '$source'";
	}	
	
	if ($order) 
	{
		$select_com = ",sum(if(M.value < 0 or M.change_charge_value < 0,M.value+M.change_charge_value,0)) as consume_ingot";
		$set_ingot_left = "left join player_ingot_change_record M on A.id = M.player_id";
		$set_nickname = "and A.nickname <> ''";
	}
	if ($order == 'level') 
	{
		$set_order = "E.level desc,E.experience desc,";	
	}elseif ($order == 'mission') 
	{
		if (!$mt) 
		{
			$ml = 'max_mission_lock';
		}elseif ($mt == 1) {
			$ml = 'max_hero_mission_lock';
		}
		if ($mtmid) {
			$set_wyh = " and J.mission_id = 314";	
			$set_lock_m = " and I.id = 314";	
		}else{
			$set_lock_m = "and B.{$ml} = I.`lock`";
		}
		$max_lock = $pdb->result($pdb->query(" select max({$ml}) from player_data"),0);//总关卡
		$set_mission_show =	",J.first_challenge_time,I.name as mission_name,L.name as town_name";
		$set_mission_left =	"
				left join player_mission_record J on A.id = J.player_id and J.is_finished = 1
				left join mission I on I.id = J.mission_id $set_lock_m $set_wyh
				left join mission_section K on I.mission_section_id = K.id
				left join town L on K.town_id = L.id
				";
		$set_lock = " and B.{$ml} <= {$max_lock} and I.type = {$mt}";	
		
		$set_order = "I.`lock` desc,J.first_challenge_time asc,";	
	}elseif ($order == 'vip') 
	{
		$set_order = "A.vip_level desc,";	
	}elseif ($order == 'ingot') 
	{
		$set_order = "B.ingot desc,";	
	}elseif ($order == 'coins') 
	{
		$set_order = "B.coins desc,";	
	}elseif ($order == 'fame') 
	{
		$set_order = "B.fame desc,";
	}elseif ($order == 'sport') 
	{
		$set_order = "F.ranking asc,";
	}elseif ($order == 'consume') 
	{
		$set_order = "consume_ingot asc,";
	}
	///--------------------------------------------------------------------------------------

	//if ($order != 'mission') 
	//{

		$num = $pdb->result($pdb->query("
		select 
			count(distinct(A.id)) 
		from 
			player A 
			left join player_data B on A.id = B.player_id
			left join player_trace C on A.id = C.player_id
			left join player_charge_record D on A.id = D.player_id
			left join player_role E on A.id = E.player_id and A.main_role_id = E.id
			$set_mission_left	
		where 
			A.id > 0
			$set_uid
			$set_username
			$set_nickname
			$set_ip
			$set_lock
			$set_type
			$set_source
			$set_time
			$set_level
			$set_role_level
			$set_vip
			$set_yellow
			$set_yellow_level
			$set_blue
			$set_blue_level			
		"),0);
	//}else{
	//	$num = 1;
	//}
	if($num)
	{
		$query = $pdb->query("
		select 
			A.id,
			A.is_tester,
			A.disable_login,
			A.disable_talk,
			A.username,
			A.nickname,
			A.vip_level,
			A.regdate,
			A.is_yellow_vip,
			A.is_yellow_year_vip,
			A.yellow_vip_level,
			A.is_blue_vip,
			A.is_blue_year_vip,
			A.blue_vip_level,			
			B.ingot,
			B.coins,
			B.fame,
			B.skill,
			B.charge_ingot,
			B.ingot,			
			C.last_login_ip,
			C.first_login_time,
			C.last_login_time,
			C.source,
			D.total_ingot,
			E.level as player_level
			$select_com
			$set_mission_show
			$set_nums
		from 
			player A
			left join player_data B on A.id = B.player_id
			left join player_trace C on A.id = C.player_id
			left join player_charge_record D on A.id = D.player_id
			left join player_role E on A.id = E.player_id and A.main_role_id = E.id
			$set_ingot_left
			$set_mission_left
		where 
			A.id <> 0 
			$set_uid
			$set_username 
			$set_nickname
			$set_ip			
			$set_lock
			$set_type	
			$set_source
			$set_time
			$set_level
			$set_role_level
			$set_vip
			$set_yellow
			$set_yellow_level
			$set_blue
			$set_blue_level			
		group by 
			A.id		
		order by 
			$set_order
			A.id desc 
		limit 
			$start_num,$pageNum
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['login_day'] =  round((time() - $rs['last_login_time'])/86400);
			$list_array[] =  $rs;
		}
		$username_url = urlencode($username);
		$list_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&username=$username_url&player_id=$player_id&ip=$ip&order=$order&type=$type&mt=$mt&source=$source&day=$day&level=$level&vip=$vip&st=$st&level_s=$level_s&level_e=$level_e&yellow=$yellow&yellow_level=$yellow_level&blue=$blue&blue_level=$blue_level&cid=$cid&sid=$sid");	
	}

	$pdb->close();
	include_once template('player');
}
//-------------------------------------------------------导出玩家

function  PlayerExport()
{
	global $pdb,$db,$cid,$sid,$adminWebType,$server;
	
	///-------------------VIP-------------------------------------------------------------------
	for ($i=1;$i<=12;$i++)
	{
		$vip_list[] = $i;
		
	}
	///-------------------level-------------------------------------------------------------------
	for ($i=1;$i<=120;$i++)
	{
		$level_list[] = $i;
		
	}
	///--------------------source------------------------------------------------------------------
	$query = $pdb->query("
	select 
		B.source
	from 
		player A
		left join player_trace B on A.id = B.player_id
	where 
		B.source <> ''
	group by 
		B.source	
	");
		
	while($rs = $pdb->fetch_array($query))
	{	
		
		$source_list[] =  $rs;
	}	
	$pdb->close();
	include_once template('export');

}

//-------------------------------------------------------查看玩家

function  PlayerView()
{
	global $pdb,$db,$cid,$sid,$adminWebType,$server;
	global $uid,$player; 
	if($server['slug'] == 'verycd')
	{
		$sign = md5($player['username'].'_'.$server['key']);//MD5
		$url = 'http://www.xd.com/users/checkuser?id='.$player['username'].'&sign='.$sign;//服务器充值
		$msg = @file_get_contents($url);
		if ($msg != false)
		{
			$username_web =  $msg;
		}
			
	}

	$consume_ingot = $pdb->result($pdb->query("
		select 
			sum(if(value < 0 or change_charge_value < 0,value+change_charge_value,0)) as consume_ingot
		from 
			player_ingot_change_record
		where
			player_id = '$uid'
		"),0); //获得总条数

	
	$other = $pdb->fetch_first("
	select 
		name as faction_name
	from 
		player_faction_member A 
		left join player_faction B on A.faction_id = B.id
	where 
		A.player_id = '$uid'
	");
	if($other)
	{
		$faction_name = $other['faction_name'];
	}

	$deploy_mode_id = $player['deploy_mode_id'];//默认识阵型
	
	$query = $pdb->query("
	select 
		A.*,
		B.name as role_name,
		C.*,
		D.deploy_mode_id,
		E.spirit_state_id,
		E.level as state_level,
		E.state_point
	from 
		player_role A
		left join role B on A.role_id = B.id
		left join player_role_data C on A.id = C.player_role_id
		left join player_deploy_grid D on A.id = D.player_role_id and A.player_id = '$uid' and D.deploy_mode_id = '$deploy_mode_id'
		left join player_role_spirit_state E on A.id = E.player_role_id
	where 
		A.player_id = '$uid' 
	order by 
		A.state asc,
		D.deploy_mode_id desc,
		A.id asc
	");
	
	
	if($pdb->num_rows($query))
	{				
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['role_name_url'] = urlencode($rs['role_name']);
			$role[$rs['id']] = $rs['role_name'];//伙伴名
			$player_role_array[] =  $rs;
		}
	}	
	//------------------------------境界--------------------------------------------------------
	$query = $pdb->query("
	select 
		*
	from 
		spirit_state
	");
	
	
	if($pdb->num_rows($query))
	{				
		while($rs = $pdb->fetch_array($query))
		{	
			$spirit[$rs['id']] =  $rs['name'];
		}
	}	
	
	//-----------------------------绝技描述-------------------------------------
	$query = $pdb->query("
	select 
		*
	from 
		study_stunt_type
	");
	
	
	if($pdb->num_rows($query))
	{				
		while($rs = $pdb->fetch_array($query))
		{	
			$study[$rs['id']] =  $rs['study_stunt_name'];
		}
	}		
	//------------------------------其它服充值情况--------------------------------------------------------
	
	if(!serverAdmin('pay','y'))
	{
	
		$query = $db->query("
		select 
			sum(A.amount) as pay_amount,
			count(A.pid) as pay_count,
			B.name,
			B.o_name,
			B.server
		from 
			pay_data A
			left join servers B on A.sid = B.sid
		where 
			A.cid = '$cid'
			and A.sid <> '$sid'
			and A.username = '$player[username]'	
		group by 
			A.sid
		order by
			A.sid desc
		");
		if($db->num_rows($query))
		{
			while($prs = $db->fetch_array($query))
			{	
				$prs['pay_amount'] = round($prs['pay_amount'],2);
				$other_servers_pay_array[] =  $prs;
			}
		}
	}
	
	$pdb->close();
	include_once template('player_view_'.$server['server_ver']);
}

//------------------------------------------------------竞技场排名
function SuperSportRanking() 
{
	global $pdb,$cid,$sid,$adminWebType,$server;
	global $uid,$player,$page; 
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;	


	//-------------------------------------------------
	$next = $pdb->fetch_first("
	select 
		*
	from 
		player_server_data
	where 
		id = 1
	");
	if($next)
	{
		$next_time = date('Y-m-d H:i:s',$next['data']);
	}
	$last = $pdb->fetch_first("
	select 
		*
	from 
		player_server_data
	where 
		id = 2
	");
	if($last)
	{
		$last_time = date('Y-m-d H:i:s',$last['data']);
	}	
	
	//-------------------------------------------------
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_super_sport_ranking  A
		where
			A.ranking <= 50
		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.username,
			B.nickname,
			B.is_tester,
			B.vip_level,
			sum(if(C.value < 0,C.value,0)) as consume_ingot,
			D.*
		from 
		
			player_super_sport_ranking A
			left join player B on A.player_id = B.id
			left join player_ingot_change_record C on A.player_id = C.player_id
			left join player_super_sport D on A.player_id = D.player_id
		where
			A.ranking <= 50
			
		group by
			C.player_id
		order by 
			A.ranking asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=SuperSportRanking&cid=$cid&sid=$sid");	
	}
	$pdb->close();
	include_once template('player_super_sport_ranking');

}
//--------------------------------------------------------------------------------------------帮派数据

function  Faction()
{
	global $pdb,$cid,$sid,$server,$adminWebType,$page;; 	
	$level = trim(ReqNum('level'));
	$name = trim(ReqStr('name'));
	if ($name) 
	{
		$set_name = " and A.name like '%$name%' ";	
	}
	if ($level) 
	{
		$set_level = " and A.level >= '$level' ";	
	}
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$num_all = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_faction A 		
	where 
		A.id <> 0
	"),0);

	$num = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_faction A 		
	where 
		A.id <> 0
		$set_name 
		$set_level
	"),0);
	if($num)
	{
		$query = $pdb->query("
		select 
			A.*,
			B.name as faction_class_name
		from 
			player_faction A
			left join camp B on A.camp_id = B.id
		where 
			A.id <> 0 
			$set_name 	
			$set_level		
		order by 
			A.id desc 
		limit 
			$start_num,$pageNum
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Faction&name=$name&level=$level&cid=$cid&sid=$sid");	
	}
	$pdb->close();
	include_once template('player_faction');
}

//--------------------------------------------------------------------------------------------数据报表

function Data() {
	global $db,$cid,$pdb,$sid,$server,$adminWebType,$page;
	$type = ReqStr('type');
	if (!$type) 
	{
		serverAdmin('data');
		DataDay($type);	
	}elseif($type == 'reg'){
		serverAdmin('data');
		DataReg($type);
	}elseif($type == 'role_rate'){
		serverAdmin('data');
		DataRoleRate($type);
	}elseif($type == 'pay'){
		serverAdmin('pay');
		DataPay($type);
	}elseif($type == 'pay_data'){
		serverAdmin('pay_data');
		DataPayData($type);
	}elseif($type == 'pay_rate'){
		serverAdmin('pay_data');
		DataPayRate($type);
	}elseif($type == 'pay_order'){
		serverAdmin('pay_order');
		DataPayOrder($type);
	}elseif($type == 'player_level'){//key
		serverAdmin('data_key'); 
		DataPlayerLevel($type);
	}elseif($type == 'player_level_vip'){//key
		serverAdmin('data_key'); 
		DataPlayerLevelVip($type);
	}elseif($type == 'player_out'){//key
		serverAdmin('data_key'); 
		DataPlayerOut($type);
	}elseif($type == 'player_town_online'){//key
		serverAdmin('data_key'); 
		DataPlayerTownOnline($type);
	}elseif($type == 'player_mammon'){//key
		serverAdmin('data_key'); 
		DataPlayerMammon($type);
	}elseif($type == 'item'){//key
		serverAdmin('data_key'); 
		DataItem($type);
	}elseif($type == 'fate'){//key
		serverAdmin('data_key'); 
		DataFate($type);
	}elseif($type == 'consume'){//key
		serverAdmin('data_key'); 
		DataConsume($type);
	}elseif($type == 'role'){//key
		serverAdmin('data_key'); 
		DataRole($type);
	}elseif($type == 'power'){//key
		serverAdmin('data_key'); 
		DataPower($type);
	}elseif($type == 'player_new_out'){//key
		serverAdmin('data_key'); 
		DataNewOut($type);
	}
	
}

//--------------------------------------------------------------------------------------------新入游戏流失

function DataNewOut($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	
	$rs = $pdb->fetch_first("
	select 
		count(case when E.level < 2 then A.id end) as player_1_level,
		count(case when C.state = 0 then A.id end) as quser_1,
		count(case when C.state = 2 then A.id end) as quser_1_no,
		count(case when D.state = 0 then A.id end) as quser_2,
		count(case when E.experience = 220 then A.id end) as no_kill,
		count(case when H.type_id = 2 then A.id end) as no_item,
		count(case when G.x = 200 and G.y = 450 then A.id end) as no_move
	from 
		player A
		left join player_quest C on A.id = C.player_id and C.quest_id = 1
		left join player_quest D on A.id = D.player_id and D.quest_id = 2
		left join player_role E on A.id = E.player_id and A.main_role_id = E.id
		left join player_item F on A.id = F.player_id and F.grid_id = 1
		left join player_last_pos G on A.id = G.player_id and G.town_id = 1
		left join item H on F.item_id = H.id
	where
		A.nickname <> ''
		and A.is_tester = 0
		and E.level <= 2

	");	
	if ($rs)
	{
		$player_1_level = $rs['player_1_level'];
		$quser_1 = $rs['quser_1'];
		$quser_1_no = $rs['quser_1_no'];
		$quser_2 = $rs['quser_2'];
		$no_kill = $rs['no_kill'];
		$no_item = $rs['no_item'];
		$no_move = $rs['no_move'];
	}
	
	$rs = $pdb->fetch_first("
	select 
		count(A.id) as player_num,
		count(case when A.nickname = '' then A.id end) as player_no_role,
		count(case when B.level >= 2 then A.id end) as player_2_level
	from 
		player A
		left join player_role B on A.id = B.player_id and A.main_role_id = B.id
	where
		A.id <> 0
		and A.is_tester = 0
	

	");	
	if ($rs)
	{
		$player_num = $rs['player_num'];
		$player_no_role = $rs['player_no_role'];
		$player_2_level = $rs['player_2_level'];
	}		
	$no_kill_mission = $pdb->result($pdb->query("select count(player_id) from player_mission_record where mission_id = 1 and current_monster_team_lock = 0"),0);
	
	$pdb->close();

	include_once template('player_data_new_out');
	

}

//--------------------------------------------------------------------------------------------城镇在线

function DataPlayerTownOnline($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	
	$query = $pdb->query("
	select 
		id,
		name as town_name
	from 
		town
	where
		type = 0 or type = 2
	order by 
		id asc
	");	
	$i = 0;	
	while($rs = $pdb->fetch_array($query)){
		$rs['i'] = $i++;
		$list_array[] = $rs;
	}	
	
	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];

	$onlie = api_admin::get_all_town_player_count();
	if ($onlie)
	{
		$all_onlie  = $onlie['player_count'];
	}
	$pdb->close();

	include_once template('player_data_town_online');
	
}

//--------------------------------------------------------------------------------------------体力统计

function DataPower($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$level = ReqNum('level');	
	$filename = $sid."_sxd_data_power_".$level.".php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);
	



	if($is_update)
	{
	
		if(!$level)
		{
			$set_level = "and D.level > 20";
		}elseif($level == 1){
			$set_level = "and D.level <= 20";
		}
	
	
		$rs = $pdb->fetch_first("
		select 		
			count(case when A.nickname <> '' then A.id end) as power_player,
			count(case when A.nickname <> '' and C.total_ingot > 0 then A.id end) as power_player_pay,
			count(case when A.nickname <> '' and DATE_FORMAT(FROM_UNIXTIME(B.last_login_time), '%Y-%m-%d') = CURDATE() then A.id end) as power_player_today
		from 
			player A
			left join player_trace B on A.id = B.player_id
			left join player_charge_record C on A.id = C.player_id
			left join player_role D on A.id = D.player_id and A.main_role_id = D.id
		where 
			A.is_tester = 0	
			$set_level	
		");	
	
	
		$power = array();
		$prs = $pdb->fetch_first("
		select 		
			count(case when A.power = 0 and A.power <= 5  then A.player_id end) as power_player_0,
			count(case when A.power >= 6 and A.power <= 20 then A.player_id end) as power_player_1,
			count(case when A.power >= 21 and A.power <= 50 then A.player_id end) as power_player_2,
			count(case when A.power >= 51 and A.power <= 100 then A.player_id end) as power_player_3,
			count(case when A.power >= 101 and A.power <= 200 then A.player_id end) as power_player_4,
			count(case when A.power >= 201 and A.power <= 300 then A.player_id end) as power_player_5,
			count(case when A.power >= 301 and A.power <= 400 then A.player_id end) as power_player_6,
			count(case when A.power >= 401 and A.power <= 500 then A.player_id end) as power_player_7,
			count(case when A.power >= 501 and A.power <= 600 then A.player_id end) as power_player_8,
			count(case when A.power >= 601 and A.power <= 700 then A.player_id end) as power_player_9,
			count(case when A.power >= 701 and A.power <= 800 then A.player_id end) as power_player_10,
			count(case when A.power >= 801 and A.power <= 900 then A.player_id end) as power_player_11,
			count(case when A.power >= 901 and A.power <= 1000 then A.player_id end) as power_player_12,
			count(case when A.power >= 1001 then A.player_id end) as power_player_13,
			

			count(case when A.power = 0 and A.power <= 5 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_0,
			count(case when A.power >= 6 and A.power <= 20 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_1,
			count(case when A.power >= 21 and A.power <= 50 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_2,
			count(case when A.power >= 51 and A.power <= 100 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_3,
			count(case when A.power >= 101 and A.power <= 200 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_4,
			count(case when A.power >= 201 and A.power <= 300 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_5,
			count(case when A.power >= 301 and A.power <= 400 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_6,
			count(case when A.power >= 401 and A.power <= 500 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_7,
			count(case when A.power >= 501 and A.power <= 600 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_8,
			count(case when A.power >= 601 and A.power <= 700 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_9,
			count(case when A.power >= 701 and A.power <= 800 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_10,
			count(case when A.power >= 801 and A.power <= 900 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_11,
			count(case when A.power >= 901 and A.power <= 1000 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_12,
			count(case when A.power >= 1001 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_13,

			
			count(case when A.power = 0 and A.power <= 5 and C.total_ingot > 0  then A.player_id end) as power_player_pay_0,
			count(case when A.power >= 6 and A.power <= 20 and C.total_ingot > 0 then A.player_id end) as power_player_pay_1,
			count(case when A.power >= 21 and A.power <= 50 and C.total_ingot > 0 then A.player_id end) as power_player_pay_2,
			count(case when A.power >= 51 and A.power <= 100 and C.total_ingot > 0 then A.player_id end) as power_player_pay_3,
			count(case when A.power >= 101 and A.power <= 200 and C.total_ingot > 0 then A.player_id end) as power_player_pay_4,
			count(case when A.power >= 201 and A.power <= 300 and C.total_ingot > 0 then A.player_id end) as power_player_pay_5,
			count(case when A.power >= 301 and A.power <= 400 and C.total_ingot > 0 then A.player_id end) as power_player_pay_6,
			count(case when A.power >= 401 and A.power <= 500 and C.total_ingot > 0 then A.player_id end) as power_player_pay_7,
			count(case when A.power >= 501 and A.power <= 600 and C.total_ingot > 0 then A.player_id end) as power_player_pay_8,
			count(case when A.power >= 601 and A.power <= 700 and C.total_ingot > 0 then A.player_id end) as power_player_pay_9,
			count(case when A.power >= 701 and A.power <= 800 and C.total_ingot > 0 then A.player_id end) as power_player_pay_10,
			count(case when A.power >= 801 and A.power <= 900 and C.total_ingot > 0 then A.player_id end) as power_player_pay_11,
			count(case when A.power >= 901 and A.power <= 1000 and C.total_ingot > 0 then A.player_id end) as power_player_pay_12,
			count(case when A.power >= 1001 and C.total_ingot > 0 then A.player_id end) as power_player_pay_13		

		from 
			player_data A
			left join player B on A.player_id = B.id
			left join player_charge_record C on A.player_id = C.player_id
			left join player_role D on B.id = D.player_id and B.main_role_id = D.id
			left join player_trace E on A.player_id = E.player_id
		where 
			B.is_tester = 0
			$set_level	
		");
		$pdb->close();
	}
	
	
	if($rs){
		$power_player = $rs['power_player'];//-----------总数
		$power_player_pay = $rs['power_player_pay'];//-----------充值
		$power_player_today = $rs['power_player_today'];//-----------登陆
	}	
	
	
	if($prs){
		$power = array(
			0 => array('player' => $prs['power_player_0'],'player_pay' => $prs['power_player_pay_0'],'player_today' => $prs['power_player_today_0']),
			1 => array('player' => $prs['power_player_1'],'player_pay' => $prs['power_player_pay_1'],'player_today' => $prs['power_player_today_1']),
			2 => array('player' => $prs['power_player_2'],'player_pay' => $prs['power_player_pay_2'],'player_today' => $prs['power_player_today_2']),
			3 => array('player' => $prs['power_player_3'],'player_pay' => $prs['power_player_pay_3'],'player_today' => $prs['power_player_today_3']),
			4 => array('player' => $prs['power_player_4'],'player_pay' => $prs['power_player_pay_4'],'player_today' => $prs['power_player_today_4']),
			5 => array('player' => $prs['power_player_5'],'player_pay' => $prs['power_player_pay_5'],'player_today' => $prs['power_player_today_5']),
			6 => array('player' => $prs['power_player_6'],'player_pay' => $prs['power_player_pay_6'],'player_today' => $prs['power_player_today_6']),
			7 => array('player' => $prs['power_player_7'],'player_pay' => $prs['power_player_pay_7'],'player_today' => $prs['power_player_today_7']),
			8 => array('player' => $prs['power_player_8'],'player_pay' => $prs['power_player_pay_8'],'player_today' => $prs['power_player_today_8']),
			9 => array('player' => $prs['power_player_9'],'player_pay' => $prs['power_player_pay_9'],'player_today' => $prs['power_player_today_9']),
			10 => array('player' => $prs['power_player_10'],'player_pay' => $prs['power_player_pay_10'],'player_today' => $prs['power_player_today_10']),
			11 => array('player' => $prs['power_player_11'],'player_pay' => $prs['power_player_pay_11'],'player_today' => $prs['power_player_today_11']),
			12 => array('player' => $prs['power_player_12'],'player_pay' => $prs['power_player_pay_12'],'player_today' => $prs['power_player_today_12']),
			13 => array('player' => $prs['power_player_13'],'player_pay' => $prs['power_player_pay_13'],'player_today' => $prs['power_player_today_13']),
		);
		
	}
	
	$level_power_list = array(
		0 => '0 - 5',
		1 => '6 - 20',
		2 => '21 - 50',
		3 => '51 - 100',
		4 => '101 - 200',
		5 => '201 - 300',
		6 => '301 - 400',
		7 => '401 - 500',
		8 => '501 - 600',
		9 => '601 - 700',
		10 => '701 - 800',
		11 => '801 - 900',
		12 => '901 - 1000',
		13 => '1001 - ∞'
	);	
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$rs='.var_export($rs, TRUE).";\n"; 
		$str .= '$prs='.var_export($prs, TRUE).";\n";
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		

	include_once template('player_data_power');
	
}
//--------------------------------------------------------------------------------------------伙伴统计

function DataRole($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$filename = $sid."_sxd_data_role.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);

	if($is_update)
	{
		$role = array();
		$query = $pdb->query("
		select 
			COUNT(B.role_id) AS role_count,
			COUNT(CASE WHEN B.state = 0 THEN B.role_id END) AS role_in_count,
			COUNT(CASE WHEN B.state = 1 THEN B.role_id END) AS role_out_count,
			A.id,
			A.name as role_name,
			A.fame
		from 
			role A
			left join player_role B on A.id = B.role_id
		where 
			A.lock >= 5
		group by 
			A.id
		order by 
			role_count desc,
			A.id desc
		");	
		while($rrs = $pdb->fetch_array($query)){
			$rrs['name_url'] = urlencode($rrs['role_name']);
			$rid .= $rrs['id'].',';
			$role[] = $rrs;
		}
		if ($rid) //取出没有记录内容的分类
		{
			$rid = substr($rid,0,strlen($rid)-1);
			$query = $pdb->query("
			select
				A.id,
				A.name as role_name,
				A.fame
			from 
				role A
			where
				A.lock >= 5
				and A.id not in ($rid)
			order by 
				A.id desc
			");	
			while($rrs = $pdb->fetch_array($query)){
				$role[] = $rrs;
			}
		
		}
		$pdb->close();
		
	}
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$role='.var_export($role, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		

	include_once template('player_data_role');
	
}
//--------------------------------------------------------------------------------------------消费统计

function DataConsume($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$s = ReqStr('s');	
	$username = trim(ReqStr('username'));
	$is_tester = ReqStr('is_tester');
	$username_url = urlencode($username);
	$vip_s = ReqNum('vip_s');
	$vip_e = ReqNum('vip_e');
	$level_s = ReqNum('level_s');
	$level_e = ReqNum('level_e');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$order = ReqStr('order');
	$day = ReqNum('day');
	$t = ReqNum('t');
	$p = ReqNum('p');
	$hour = $day*24;	
	
	
	
	if ($stime) $stime_s = strtotime($stime.' 00:00:00');
	if ($etime) $etime_e = strtotime($etime.' 23:59:59');
		
/*	if ($username) 
	{
		$set_username = " and C.username = '$username'";	
	}else{
		$set_is_tester = " and C.is_tester = 0 ";	
	}*/
	if ($stime && $etime) 
	{
		$set_time = "AND A.change_time >= '$stime_s' AND A.change_time <= '$etime_e'";
	}
	if ($vip_s && $vip_e) 
	{
		$set_vip = "AND B.vip_level >= '$vip_s' AND B.vip_level <= '$vip_e'";
	}
	if ($level_s && $level_e) 
	{
		$set_level = "AND C.level >= '$level_s' AND C.level <= '$level_e'";
		$set_left_role = "left join player_role C on B.id = C.player_id and B.main_role_id = C.id";
	}	
	if (($vip_s && $vip_e) || ($level_s && $level_e))
	{
		$set_left_player = "left join player B on A.player_id = B.id";
	
	}
	
/*	if($order)
	{
		$set_order = "$order desc,";
	}
	if(!$t)
	{
		$set_t = "AND A.type = 0";
	}elseif($t == 1){
		$set_t = "AND A.type = 1";
	}*/
	if ($day) {
		$set_left_day = "left join player_trace D on B.id = D.player_id";
		$set_day = "and DATE_SUB(NOW(),INTERVAL $hour HOUR) >= FROM_UNIXTIME(D.last_login_time, '%Y-%m-%d %H:%i:%s') AND D.last_login_time > 0";
	}
	
	if(!$username && !$set_day)//如果不在查询才使用缓存
	{
		$filename = $sid."_sxd_data_consume_".$t."_".$stime.$etime."_".$vip_s.$vip_e."_".$level_s.$level_e.".php";//文件名
		$dir = UCTIME_ROOT."/data/";//目录
		$flie = $dir.$filename;//全地址
		$filetime  = @filemtime($flie);//文件创建时间
		@include_once($flie);
		if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
		$updatetime = setTime($filetime);
	}
	//-----------------------测试号---------------------------------------
	if ($username) 
	{
		$player_id = $pdb->result_first("select id from player where username = '$username'"); 
		$set_username = " and A.player_id = '$player_id'";	
	}else{
	
		$query = $pdb->query("
		select 
			A.id
		from 
			player A
		where
			A.is_tester <> 0
		");
		while($prs = $pdb->fetch_array($query)){
			$playerIdArr[] = $prs['id'];
		}
		$parr =  $playerIdArr ? implode(",",$playerIdArr) : '';
		if ($parr) $set_is_tester = " and A.player_id not in ($parr)";	
	}	
	
	//--------------------------------------------------------------
	
	$query = $pdb->query("
	select 
		A.id,
		A.name as type_name
	from 
		ingot_change_type A
	where
		A.type = '$t'
	order by 
		A.id desc
	");
	while($trs = $pdb->fetch_array($query)){
		$typeArr[$trs['id']] = $trs['type_name'];
		$typeIdArr[] = $trs['id'];
	}
	$tiarr =  $typeIdArr ? implode(",",$typeIdArr) : '';
	
	//--------------------------------------------------------------
	if($is_update  || $username || $day)
	{
	
		$rs = $pdb->fetch_first("
		select 		
			COUNT(distinct(A.player_id)) AS player_count,
			COUNT(A.type) AS type_count,
			SUM(abs(A.value)+abs(A.change_charge_value)) AS value_count
		from 
			player_ingot_change_record A
			$set_left_player
			$set_left_role
			$set_left_day
		where
			A.type in ($tiarr)
			$set_is_tester
			$set_username
			$set_time
			$set_day
			$set_vip
			$set_level			
		");

	//--------------------------------------------------------------


		$consume = array();
		
		$query = $pdb->query("
		select 
			A.type,
			COUNT(distinct(A.player_id)) AS player_count,
			COUNT(A.type) AS type_count,
			SUM(abs(A.value)+abs(A.change_charge_value)) AS value_count
		from 
			player_ingot_change_record A
			$set_left_player
			$set_left_role
			$set_left_day
		where
			A.type in ($tiarr)
			$set_is_tester
			$set_username
			$set_time
			$set_day
			$set_vip
			$set_level
		group by 
			A.type

		");			

		while($crs = $pdb->fetch_array($query)){
			$crs['value_count'] = abs($crs['value_count']);
			$crs['type_name'] = $typeArr[$crs['type']];
			$crs['name_url'] = urlencode($typeArr[$crs['type']]);
			$tid .= $crs['type'].',';
			$consume[$crs['type']] = $crs;
		}
		if ($tid) //取出没有记录内容的分类
		{
			$tid = substr($tid,0,strlen($tid)-1);
			$query = $pdb->query("
			select
				C.id as type,
				C.name as type_name
			from 
				ingot_change_type C
			where
				C.type = '$t'
				and C.id not in ($tid)
				$set_t
			order by 
				C.id desc
			");	
			while($crs = $pdb->fetch_array($query)){
				$consume[$crs['type']] = $crs;
			}
		
		}
	}
	if($rs){

		$player_count = $rs['player_count'];
		$type_count = $rs['type_count'];
		$value_count = $rs['value_count'];
	}	
	$pdb->close();
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$rs='.var_export($rs, TRUE).";\n"; 
		$str .= '$consume='.var_export($consume, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	if($s){//用于汇总统计时的输出
		echo '<script>parent.$("'.$s.'").innerHTML="OK";parent.CycleUpdate();</script>';
	}else{
		$consume = $consume ? sysSortArray($consume,$order ? $order : 'value_count','SORT_DESC') : array();
		include_once template('player_data_consume');
	}	
	
}
//--------------------------------------------------------------------------------------------老用户流失

function DataPlayerOut($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$s = ReqStr('s');	
	$slevel = ReqNum('slevel');
	$elevel = ReqNum('elevel');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	
	if ($stime) $stime_s = strtotime($stime.' 00:00:00');
	if ($etime) $etime_e = strtotime($etime.' 23:59:59');
	
	
	$day = ReqNum('day');
	if (!$slevel) {
		$slevel = 1;
	}
	if (!$elevel) {
		$elevel = SXD_SYSTEM_ITEM_LEVEL;
	}
	if (!$day) {
		$day = 5;
	}
	$hour = $day*24;	
	
	if ($stime && $etime) 
	{
		$set_time = "AND C.first_login_time >= '$stime_s' AND C.first_login_time <= '$etime_e'";
	}


	for ($i=$slevel;$i<=$elevel;$i++)
	{
		$level_list[$i] = $i;
		
	}
	

	$queryall = $pdb->query("
	select 
		COUNT(A.id) AS player_count,
		COUNT(CASE WHEN D.total_ingot > 0 THEN D.player_id END) AS player_pay_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 THEN C.player_id END) AS player_out_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.total_ingot > 0 THEN C.player_id END) AS player_out_pay_count
	from 
		player A
		left join player_role B on A.id = B.player_id and A.main_role_id = B.id
		left join player_trace C on A.id = C.player_id
		left join player_charge_record D on A.id = D.player_id
	where
		A.main_role_id > 0
		and A.is_tester = 0
		$set_time	
	");	
	$all = $pdb->fetch_array($queryall);



	$query = $pdb->query("
	select 
		B.level,
		COUNT(A.id) AS player_count,
		COUNT(CASE WHEN D.total_ingot > 0 THEN D.player_id END) AS player_pay_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 THEN C.player_id END) AS player_out_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.total_ingot > 0 THEN C.player_id END) AS player_out_pay_count
	from 
		player A
		left join player_role B on A.id = B.player_id and A.main_role_id = B.id
		left join player_trace C on A.id = C.player_id
		left join player_charge_record D on A.id = D.player_id
	where
		A.main_role_id > 0
		and A.is_tester = 0
		$set_time
	group by 
		B.level		
	");	
	

	while($prs = $pdb->fetch_array($query)){
		$player[$prs['level']] = $prs;
	}		
//print_r($player);

//----------------------------------------------------------------------------------------------------------------------------------

	$queryvipall = $pdb->query("
	select 
		COUNT(A.id) AS player_vip_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.total_ingot > 0 THEN C.player_id END) AS player_out_vip_count
	from 
		player A
		left join player_trace C on A.id = C.player_id
		left join player_charge_record D on A.id = D.player_id
	where
		A.vip_level > 0
		and A.is_tester = 0
		$set_time
	");	
	$vipall = $pdb->fetch_array($queryvipall);


	$vipquery = $pdb->query("
	select 
		A.vip_level,
		COUNT(A.id) AS player_vip_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.total_ingot > 0 THEN C.player_id END) AS player_out_vip_count
	from 
		player A
		left join player_trace C on A.id = C.player_id
		left join player_charge_record D on A.id = D.player_id
	where
		A.vip_level > 0
		and A.is_tester = 0
		$set_time
	group by 
		A.vip_level		
	");	
	

	while($vrs = $pdb->fetch_array($vipquery)){
		$vip[$vrs['vip_level']] = $vrs;
	}		
	$pdb->close();
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	$filename = $sid."_sxd_data_playerout_".$day."_".$slevel."_".$elevel."_".$stime."_".$etime.".php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	
	if ($is_update) 
	{
		$str = '$all='.var_export($all, TRUE).";\n"; 
		$str .= '$vipall='.var_export($vipall, TRUE).";\n"; 
		$str .= '$player='.var_export($player, TRUE).";\n"; 
		$str .= '$vip='.var_export($vip, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	if($s){//用于汇总统计时的输出
		echo '<script>parent.$("'.$s.'").innerHTML="OK";parent.CycleUpdate();</script>';
	}else{
		include_once template('player_data_player_out');
	}	
	
	
	
	
	
}



//--------------------------------------------------------------------------------------------玩家等级分布

function DataPlayerLevel($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	
	$filename = $sid."_sxd_data_player_level.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);

	if ($is_update) 
	{
		$trs = $pdb->fetch_first("
		select 		
			count(case when A.nickname <> '' then A.id end) as num,
			count(case when C.camp_id = 3 then A.id end) as ss_num,
			count(case when C.camp_id = 4 then A.id end) as kl_num,
			count(case when B.role_id = 1 or B.role_id = 3 or B.role_id = 5 then A.id end) as boy_num,
			count(case when B.role_id = 2 or B.role_id = 4 or B.role_id = 6 then A.id end) as girl_num			
			
		from 
			player A
			left join player_role B on A.id = B.player_id and A.main_role_id = B.id
			left join player_data C on A.id = C.player_id
		where 
			A.is_tester = 0	
		");
	//-------------------------------------------------------------------------------------------	
		$player = array();
		$query = $pdb->query("
		select 
			B.level,
			count(A.id) as num,
			count(case when C.camp_id = 3 or C.camp_id = 4 then A.id end) as sk_num,
			count(case when C.camp_id = 3 then A.id end) as ss_num,
			count(case when C.camp_id = 4 then A.id end) as kl_num
		from 
			player A
			left join player_role B on A.id = B.player_id and A.main_role_id = B.id
			left join player_data C on A.id = C.player_id
		where 
			A.is_tester = 0		
			and B.level > 0
		group by 
			B.level	
		");
		$i = 1;
		while($rs = $pdb->fetch_array($query))
		{	
			if ($i == 1)
			{
				$rs['i'] = 1;
				
			}elseif ($i == 9){
				$i = 0;
			}
			$i++;
			$player[$rs['level']] =  $rs;
		}
		
		//---------------------------------------------------------------------------------------
		$level = array();
		$lrs = $pdb->fetch_first("
		select
			count(case when B.level >= 1 and B.level <= 9 then A.id end) as level_num_9,
			count(case when B.level >= 10 and B.level <= 19 then A.id end) as level_num_19,
			count(case when B.level >= 20 and B.level <= 29 then A.id end) as level_num_29,
			count(case when B.level >= 30 and B.level <= 39 then A.id end) as level_num_39,
			count(case when B.level >= 40 and B.level <= 49 then A.id end) as level_num_49,
			count(case when B.level >= 50 and B.level <= 59 then A.id end) as level_num_59,
			count(case when B.level >= 60 and B.level <= 69 then A.id end) as level_num_69,
			count(case when B.level >= 70 and B.level <= 79 then A.id end) as level_num_79,
			count(case when B.level >= 80 and B.level <= 89 then A.id end) as level_num_89,
			count(case when B.level >= 90 and B.level <= 100 then A.id end) as level_num_99,
			count(case when B.level >= 100 and B.level <= 110 then A.id end) as level_num_110,
			
			count(case when C.camp_id = 3 and B.level >= 1 and B.level <= 9 then A.id end) as level_ss_num_9,
			count(case when C.camp_id = 3 and B.level >= 10 and B.level <= 19 then A.id end) as level_ss_num_19,
			count(case when C.camp_id = 3 and B.level >= 20 and B.level <= 29 then A.id end) as level_ss_num_29,
			count(case when C.camp_id = 3 and B.level >= 30 and B.level <= 39 then A.id end) as level_ss_num_39,
			count(case when C.camp_id = 3 and B.level >= 40 and B.level <= 49 then A.id end) as level_ss_num_49,
			count(case when C.camp_id = 3 and B.level >= 50 and B.level <= 59 then A.id end) as level_ss_num_59,
			count(case when C.camp_id = 3 and B.level >= 60 and B.level <= 69 then A.id end) as level_ss_num_69,
			count(case when C.camp_id = 3 and B.level >= 70 and B.level <= 79 then A.id end) as level_ss_num_79,
			count(case when C.camp_id = 3 and B.level >= 80 and B.level <= 89 then A.id end) as level_ss_num_89,
			count(case when C.camp_id = 3 and B.level >= 90 and B.level <= 100 then A.id end) as level_ss_num_99,
			count(case when C.camp_id = 3 and B.level >= 100 and B.level <= 110 then A.id end) as level_ss_num_110,

			count(case when C.camp_id = 4 and B.level >= 1 and B.level <= 9 then A.id end) as level_kl_num_9,
			count(case when C.camp_id = 4 and B.level >= 10 and B.level <= 19 then A.id end) as level_kl_num_19,
			count(case when C.camp_id = 4 and B.level >= 20 and B.level <= 29 then A.id end) as level_kl_num_29,
			count(case when C.camp_id = 4 and B.level >= 30 and B.level <= 39 then A.id end) as level_kl_num_39,
			count(case when C.camp_id = 4 and B.level >= 40 and B.level <= 49 then A.id end) as level_kl_num_49,
			count(case when C.camp_id = 4 and B.level >= 50 and B.level <= 59 then A.id end) as level_kl_num_59,
			count(case when C.camp_id = 4 and B.level >= 60 and B.level <= 69 then A.id end) as level_kl_num_69,
			count(case when C.camp_id = 4 and B.level >= 70 and B.level <= 79 then A.id end) as level_kl_num_79,
			count(case when C.camp_id = 4 and B.level >= 80 and B.level <= 89 then A.id end) as level_kl_num_89,
			count(case when C.camp_id = 4 and B.level >= 90 and B.level <= 100 then A.id end) as level_kl_num_99,
			count(case when C.camp_id = 4 and B.level >= 100 and B.level <= 110 then A.id end) as level_kl_num_110
			
			
		from 
			player A
			left join player_role B on A.id = B.player_id and A.main_role_id = B.id
			left join player_data C on A.id = C.player_id
		where 
			A.is_tester = 0
			and B.level > 0
		");
		
		$pdb->close();
	
	}
	if($lrs){
		$lv = array(
			9 => array('level_num' => $lrs['level_num_9'],'level_ss_num' => $lrs['level_ss_num_9'],'level_kl_num' => $lrs['level_kl_num_9']),
			19 => array('level_num' => $lrs['level_num_19'],'level_ss_num' => $lrs['level_ss_num_19'],'level_kl_num' => $lrs['level_kl_num_19']),
			29 => array('level_num' => $lrs['level_num_29'],'level_ss_num' => $lrs['level_ss_num_29'],'level_kl_num' => $lrs['level_kl_num_29']),
			39 => array('level_num' => $lrs['level_num_39'],'level_ss_num' => $lrs['level_ss_num_39'],'level_kl_num' => $lrs['level_kl_num_39']),
			49 => array('level_num' => $lrs['level_num_49'],'level_ss_num' => $lrs['level_ss_num_49'],'level_kl_num' => $lrs['level_kl_num_49']),
			59 => array('level_num' => $lrs['level_num_59'],'level_ss_num' => $lrs['level_ss_num_59'],'level_kl_num' => $lrs['level_kl_num_59']),
			69 => array('level_num' => $lrs['level_num_69'],'level_ss_num' => $lrs['level_ss_num_69'],'level_kl_num' => $lrs['level_kl_num_69']),
			79 => array('level_num' => $lrs['level_num_79'],'level_ss_num' => $lrs['level_ss_num_79'],'level_kl_num' => $lrs['level_kl_num_79']),
			89 => array('level_num' => $lrs['level_num_89'],'level_ss_num' => $lrs['level_ss_num_89'],'level_kl_num' => $lrs['level_kl_num_89']),
			99 => array('level_num' => $lrs['level_num_99'],'level_ss_num' => $lrs['level_ss_num_99'],'level_kl_num' => $lrs['level_kl_num_99']),
			110 => array('level_num' => $lrs['level_num_110'],'level_ss_num' => $lrs['level_ss_num_110'],'level_kl_num' => $lrs['level_kl_num_110'])
		);
	
	}	
	if($trs){
		$num = $trs['num'];//-----------总数
		$ss_num = $trs['ss_num'];//-----------蜀山
		$kl_num = $trs['kl_num'];//-----------昆仑
		$ss_rate  = round($ss_num/($ss_num+$kl_num)*100,2);
		$kl_rate  = round($kl_num/($ss_num+$kl_num)*100,2);
		
		$boy_num = $trs['boy_num'];//-----------男
		$girl_num = $trs['girl_num'];//-----------女		
		$boy_rate  = round($boy_num/($boy_num+$girl_num)*100,2);
		$girl_rate  = round($girl_num/($boy_num+$girl_num)*100,2);
		
	}	
	
	
	for ($i=1;$i<=SXD_SYSTEM_ITEM_LEVEL;$i++)
	{
		$level_list[$i] = $i;
		
	}
	
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
		$str = '$trs='.var_export($trs, TRUE).";\n"; 
		$str .= '$lrs='.var_export($lrs, TRUE).";\n"; 
		$str .= '$player='.var_export($player, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	
	include_once template('player_data_player_level');
	
}
//--------------------------------------------------------------------------------------------玩家VIP等级分布

function DataPlayerLevelVip($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	
	$filename = $sid."_sxd_data_player_level_vip.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);

	if ($is_update) 
	{
		$player_vip_num = $pdb->result($pdb->query("
		select 
			count(*) as player_vip_num
		from 
			player
			
		where 
			vip_level > 0
			and is_tester = 0	
		"),0);

		$player = array();
		$query = $pdb->query("
		select 
			vip_level,
			count(id) as num
		from 
			player
		where 
			vip_level > 0
			and is_tester = 0		
		group by 
			vip_level	
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			
			$player[$rs['vip_level']] =  $rs;
		}
		
		$pdb->close();
		
	}
	for ($i=1;$i<=12;$i++)
	{
		$level_list[$i] = $i;
		
	}
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
		$str = '$player_vip_num='.($player_vip_num ? $player_vip_num : 0).";\n"; 
		$str .= '$player='.var_export($player, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	
	include_once template('player_data_player_level_vip');
	
}
//--------------------------------------------------------------------------------------------玩家财富统计

function DataPlayerMammon($type) {
	global $cid,$pdb,$sid,$server,$adminWebType; 
	
	$filename = $sid."_sxd_data_player_mammon.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);

	if ($is_update) 
	{
		$rs = $pdb->fetch_first("
		select 		
			sum(A.ingot) as ingot_num,
			count(case when A.ingot <> '' then A.player_id end) as ingot_player,
			sum(A.coins) as coins_num,
			count(case when A.coins <> '' then A.player_id end) as coins_player
		from 
			player_data A
			left join player B on A.player_id = B.id
		where 
			B.is_tester = 0		
		");

	
	//-------------------------------------------------------------------------------------------	

		$irs = $pdb->fetch_first("
		select 		
			count(case when A.ingot >= 1 and A.ingot <= 100 then A.player_id end) as ingot_player_1,
			count(case when A.ingot >= 101 and A.ingot <= 500 then A.player_id end) as ingot_player_2,
			count(case when A.ingot >= 501 and A.ingot <= 1000 then A.player_id end) as ingot_player_3,
			count(case when A.ingot >= 1001 and A.ingot <= 5000 then A.player_id end) as ingot_player_4,
			count(case when A.ingot >= 5001 and A.ingot <= 10000 then A.player_id end) as ingot_player_5,
			count(case when A.ingot >= 10001 and A.ingot <= 50000 then A.player_id end) as ingot_player_6,
			count(case when A.ingot >= 50001 and A.ingot <= 100000 then A.player_id end) as ingot_player_7,
			count(case when A.ingot >= 100001 and A.ingot <= 500000 then A.player_id end) as ingot_player_8,
			count(case when A.ingot >= 500001 then A.player_id end) as ingot_player_9,
			
			sum(if(A.ingot >= 1 and A.ingot <= 100,A.ingot,0)) as ingot_num_1 ,
			sum(if(A.ingot >= 101 and A.ingot <= 500,A.ingot,0)) as ingot_num_2 ,
			sum(if(A.ingot >= 501 and A.ingot <= 1000,A.ingot,0)) as ingot_num_3 ,
			sum(if(A.ingot >= 1001 and A.ingot <= 5000,A.ingot,0)) as ingot_num_4 ,
			sum(if(A.ingot >= 5001 and A.ingot <= 10000,A.ingot,0)) as ingot_num_5 ,
			sum(if(A.ingot >= 10001 and A.ingot <= 50000,A.ingot,0)) as ingot_num_6 ,
			sum(if(A.ingot >= 50001 and A.ingot <= 100000,A.ingot,0)) as ingot_num_7 ,
			sum(if(A.ingot >= 100001 and A.ingot <= 500000,A.ingot,0)) as ingot_num_8 ,
			sum(if(A.ingot >= 500001,A.ingot,0)) as ingot_num_9,
			
			
			count(case when A.coins >= 1 and A.coins <= 100 then A.player_id end) as coins_player_1,
			count(case when A.coins >= 101 and A.coins <= 500 then A.player_id end) as coins_player_2,
			count(case when A.coins >= 501 and A.coins <= 1000 then A.player_id end) as coins_player_3,
			count(case when A.coins >= 1001 and A.coins <= 5000 then A.player_id end) as coins_player_4,
			count(case when A.coins >= 5001 and A.coins <= 10000 then A.player_id end) as coins_player_5,
			count(case when A.coins >= 10001 and A.coins <= 50000 then A.player_id end) as coins_player_6,
			count(case when A.coins >= 50001 and A.coins <= 100000 then A.player_id end) as coins_player_7,
			count(case when A.coins >= 100001 and A.coins <= 500000 then A.player_id end) as coins_player_8,
			count(case when A.coins >= 500001 and A.coins <= 1000000 then A.player_id end) as coins_player_9,
			count(case when A.coins >= 1000001 and A.coins <= 5000000 then A.player_id end) as coins_player_10,
			count(case when A.coins >= 5000001 and A.coins <= 10000000 then A.player_id end) as coins_player_11,
			count(case when A.coins >= 10000001 then A.player_id end) as coins_player_12,


			
			sum(if(A.coins >= 1 and A.coins <= 100,A.coins,0)) as coins_num_1 ,
			sum(if(A.coins >= 101 and A.coins <= 500,A.coins,0)) as coins_num_2 ,
			sum(if(A.coins >= 501 and A.coins <= 1000,A.coins,0)) as coins_num_3 ,
			sum(if(A.coins >= 1001 and A.coins <= 5000,A.coins,0)) as coins_num_4 ,
			sum(if(A.coins >= 5001 and A.coins <= 10000,A.coins,0)) as coins_num_5 ,
			sum(if(A.coins >= 10001 and A.coins <= 50000,A.coins,0)) as coins_num_6 ,
			sum(if(A.coins >= 50001 and A.coins <= 100000,A.coins,0)) as coins_num_7 ,
			sum(if(A.coins >= 100001 and A.coins <= 500000,A.coins,0)) as coins_num_8 ,
			sum(if(A.coins >= 500001 and A.coins <= 1000000,A.coins,0)) as coins_num_9 ,
			sum(if(A.coins >= 1000001 and A.coins <= 5000000,A.coins,0)) as coins_num_10 ,
			sum(if(A.coins >= 5000001 and A.coins <= 10000000,A.coins,0)) as coins_num_11 ,
			sum(if(A.coins >= 10000001,A.coins,0)) as coins_num_12
			
		from 
			player_data A
			left join player B on A.player_id = B.id
		where 
			B.is_tester = 0
		");
		
		$pdb->close();
		
	}
	
	if($rs){
		//$num = $rs['num'];//-----------总数
		$ingot_num = $rs['ingot_num'];//-----------元宝
		$coins_num = $rs['coins_num'];//-----------铜钱
		$ingot_player = $rs['ingot_player'];//-----------元宝持有人数
		$coins_player = $rs['coins_player'];//-----------铜钱持有人数
	}	
	if($irs){
		$ingot = array(
			1 => array('player' => $irs['ingot_player_1'],'num' => $irs['ingot_num_1']),
			2 => array('player' => $irs['ingot_player_2'],'num' => $irs['ingot_num_2']),
			3 => array('player' => $irs['ingot_player_3'],'num' => $irs['ingot_num_3']),
			4 => array('player' => $irs['ingot_player_4'],'num' => $irs['ingot_num_4']),
			5 => array('player' => $irs['ingot_player_5'],'num' => $irs['ingot_num_5']),
			6 => array('player' => $irs['ingot_player_6'],'num' => $irs['ingot_num_6']),
			7 => array('player' => $irs['ingot_player_7'],'num' => $irs['ingot_num_7']),
			8 => array('player' => $irs['ingot_player_8'],'num' => $irs['ingot_num_8']),
			9 => array('player' => $irs['ingot_player_9'],'num' => $irs['ingot_num_9'])
		);
		$coins = array(
			1 => array('player' => $irs['coins_player_1'],'num' => $irs['coins_num_1']),
			2 => array('player' => $irs['coins_player_2'],'num' => $irs['coins_num_2']),
			3 => array('player' => $irs['coins_player_3'],'num' => $irs['coins_num_3']),
			4 => array('player' => $irs['coins_player_4'],'num' => $irs['coins_num_4']),
			5 => array('player' => $irs['coins_player_5'],'num' => $irs['coins_num_5']),
			6 => array('player' => $irs['coins_player_6'],'num' => $irs['coins_num_6']),
			7 => array('player' => $irs['coins_player_7'],'num' => $irs['coins_num_7']),
			8 => array('player' => $irs['coins_player_8'],'num' => $irs['coins_num_8']),
			9 => array('player' => $irs['coins_player_9'],'num' => $irs['coins_num_9']),
			10 => array('player' => $irs['coins_player_10'],'num' => $irs['coins_num_10']),
			11 => array('player' => $irs['coins_player_11'],'num' => $irs['coins_num_11']),
			12 => array('player' => $irs['coins_player_12'],'num' => $irs['coins_num_12'])
		);		
		
		
	}	
	//-------------------------------------------------------------------------------------------	
	
	$level_ingot_list = array(
		1 => '1 - 100',
		2 => '101 - 500',
		3 => '501 - 1000',
		4 => '1001 - 5000',
		5 => '5001 - 10000',
		6 => '10001 - 50000',
		7 => '50001 - 100000',
		8 => '100001 - 500000',
		9 => '500001 - ∞'
	);
	$level_coins_list = array(
		1 => '1 - 100',
		2 => '101 - 500',
		3 => '501 - 1000',
		4 => '1001 - 5000',
		5 => '5001 - 10000',
		6 => '10001 - 50000',
		7 => '50001 - 100000',
		8 => '100001 - 500000',
		9 => '500001 - 1000000',
		10 => '1000001 - 5000000',
		11 => '5000001 - 10000000',
		12 => '10000001 - ∞'
	);	
	
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		//$str .= '$irs=\''.$irs."';\n";//存入数组 
		$str = '$rs='.var_export($rs, TRUE).";\n";//存入数组 
		$str .= '$irs='.var_export($irs, TRUE).";\n";//存入数组 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------	

	include_once template('player_data_player_mammon');
	
}




//--------------------------------------------------------------------------------------------装备统计

function DataItem($type) {
	global $cid,$pdb,$sid,$server,$adminWebType; 
	
	$filename = $sid."_sxd_data_item.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);
/*	
	if ($adminWebType == 's') {
	
		$query = $pdb->query("
		select 
			A.player_id,
			A.upgrade_level,
			A.player_role_id,
			A.upgrade_level,
			B.name as item_name,
			C.username,
			C.nickname,
			D.first_login_time,
			D.first_login_ip
		from 
			player_item A,
			item B,
			player C,
			player_trace D
		where 
			A.item_id = B.id
			and A.player_id = C.id
			and C.id = D.player_id
			and C.is_tester = 0
			and B.quality = 5
			and B.type_id not in (10007,10008,10009)
			
		order by
			A.player_id asc
		");
			
		while($irs = $pdb->fetch_array($query))
		{	
			
			echo '<strong>玩家</strong>：<a href="?in=player&action=PlayerView&cid='.$cid.'&sid='.$sid.'&uid='.$irs['player_id'].'" target="_blank">'.$irs['username'].'('.$irs['nickname'].')</a>  <strong>物品</strong>：'.$irs['item_name'].'  <strong>注册</strong>：'.date('Y-m-d H:i:s',$irs['first_login_time']).'  <strong>IP</strong>：'.$irs['first_login_ip'].'<br />';
		}	
		//print_r($item);
	}*/
	
	//-------------------------------------------------------------------------------------------	
	if($is_update)
	{
		$item = array();
		$irs = $pdb->fetch_first("
		select 
			count(A.id) as num,
			count(case when B.quality = 1 then A.id end) as num_1,
			count(case when B.quality = 2 then A.id end) as num_2,
			count(case when B.quality = 3 then A.id end) as num_3,
			count(case when B.quality = 4 then A.id end) as num_4,
			count(case when B.quality = 5 then A.id end) as num_5,
			
			count(distinct(A.player_id)) as player,

			count(distinct(case when B.quality = 1 then A.player_id end)) as player_1,
			count(distinct(case when B.quality = 2 then A.player_id end)) as player_2,
			count(distinct(case when B.quality = 3 then A.player_id end)) as player_3,
			count(distinct(case when B.quality = 4 then A.player_id end)) as player_4,
			count(distinct(case when B.quality = 5 then A.player_id end)) as player_5
			
		from 
			player_item A,
			item B,
			player C
		where 
			A.item_id = B.id
			and B.type_id <= 6
			and A.player_id = C.id
			and C.is_tester = 0			
		");

	//-------------------------------------------------------------------------------------------	

		$query = $pdb->query("
		select 
			A.upgrade_level,
			count(A.id) as num,
			count(case when B.quality = 1 then A.id end) as num_1,
			count(case when B.quality = 2 then A.id end) as num_2,
			count(case when B.quality = 3 then A.id end) as num_3,
			count(case when B.quality = 4 then A.id end) as num_4,
			count(case when B.quality = 5 then A.id end) as num_5,
			
			count(distinct(A.player_id)) as player,
			count(distinct(case when B.quality = 1 then A.player_id end)) as player_1,
			count(distinct(case when B.quality = 2 then A.player_id end)) as player_2,
			count(distinct(case when B.quality = 3 then A.player_id end)) as player_3,
			count(distinct(case when B.quality = 4 then A.player_id end)) as player_4,
			count(distinct(case when B.quality = 5 then A.player_id end)) as player_5
			
		from 
			player_item A,
			item B,
			player C
		where 
			A.item_id = B.id
			and B.type_id <= 6
			and A.player_id = C.id
			and C.is_tester = 0
		group by 
			A.upgrade_level	
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			
			$item[$rs['upgrade_level']] =  $rs;
		}
		$pdb->close();
		
		
	}
	//-------------------------------------------------------------------------------------------	
	
	for ($i=1;$i<=60;$i++)
	{
		$level_list[$i] = $i;
		
	}

	
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
		$str = '$irs='.var_export($irs, TRUE).";\n"; 
		$str .= '$item='.var_export($item, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------	
	
	
	include_once template('player_data_item');
	
}


//--------------------------------------------------------------------------------------------命格统计

function DataFate($type) {
	global $cid,$pdb,$sid,$server,$adminWebType; 
	$v = ReqNum('v');
	$p = ReqNum('p');	
	$order = ReqStr('order');
	if($order)
	{
		$set_order = "$order desc,";
	}
		
	if($v == 1)
	{
		$set_v = "and C.vip_level < 6";
	}elseif($v == 2){
		$set_v = "and C.vip_level >= 6";
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
		$set_p = " and C.id not in ($player_id_list)";
	}elseif($p == 2){
		$set_p = " and D.total_ingot > 0";
		$set_left = "left join player_charge_record D on C.id = D.player_id";
	}	
	
	
	//--------------------------------------------------------------
	
/*	$filename = $sid."_sxd_data_fate_".$p."_".$v."_".$order.".php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);
*/
	//--------------------------------------------------------------
	
	//if($is_update)
	//{
	
		$rs = $pdb->fetch_first("
		select 		
			COUNT(distinct(B.player_id)) AS player_count,
			COUNT(B.fate_id) AS fate_count
		from 
			fate A
			left join player_fate B on A.id = B.fate_id
			left join player C on B.player_id = C.id
			$set_left
		where
			A.fate_quality_id > 1
			and C.is_tester = 0 
			$set_p
			$set_v

			
		");

		//-------------------------------------------------------------	
		$fate = array();
		$query = $pdb->query("
		select 
			COUNT(distinct(B.player_id)) AS player_count,
			COUNT(B.fate_id) AS fate_count,
			A.id,
			A.name as fate_name,
			E.name as quality_name
		from 
			fate A
			left join player_fate B on A.id = B.fate_id
			left join player C on B.player_id = C.id
			left join fate_quality E on A.fate_quality_id = E.id
			$set_left
		where
			A.fate_quality_id > 1
			and C.is_tester = 0 
			$set_p
			$set_v			
		group by 
			A.id
		order by
			$set_order
			fate_count desc,
			A.id desc
		");	
		while($frs = $pdb->fetch_array($query)){
			$frs['name_url'] = urlencode($frs['fate_name']);
			$fid .= $frs['id'].',';
			$fate[] = $frs;
		}
		if ($fid) //取出没有记录内容的分类
		{
			$fid = substr($fid,0,strlen($fid)-1);
			$query = $pdb->query("
			select
				A.id,
				A.name as fate_name,
				B.name as quality_name

			from 
				fate A
				left join fate_quality B on A.fate_quality_id = B.id
			where
				A.fate_quality_id > 1
				and A.id not in ($fid)
			order by 
				A.id desc
			");	
			while($frs = $pdb->fetch_array($query)){
				$fate[] = $frs;
			}
		
		}	
	
	//}
	if($rs){
		$player_count = $rs['player_count'];
		$fate_count = $rs['fate_count'];
	}
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
/*	if ($is_update) 
	{
		$str = '$rs='.var_export($rs, TRUE).";\n"; 
		$str .= '$fate='.var_export($fate, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}*/
	//-------------------------------------------------------------------------------------------	
	
	
	include_once template('player_data_fate');
	
}
//--------------------------------------------------------------------------------------------创建角色比

function DataRoleRate($type) {
	global $cid,$pdb,$sid,$server,$adminWebType,$server,$page;
	$tt = ReqNum('tt');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$stimes = strtotime($stime.' 00:00:00');
	$etimee = strtotime($etime.' 23:59:59');
	$open_date = $server['open_date'];
	$order = ReqStr('order');
	$source = ReqStr('source');
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	
	if($order)
	{
		$set_order = "order by $order desc";
	}	
	if($source)
	{
		$set_source = " and B.source = '$source'";
	}	
	
	if (!$tt || $tt == 1)
	{
		if ($stime && $etime) 
		{
			$set_time = "AND B.first_login_time >= '$stimes' AND B.first_login_time <= '$etimee'";
		}
	}elseif ($tt == 2){
		if ($stime && $etime) 
		{
			$set_time = "AND A.regdate >= '$stimes' AND A.regdate <= '$etimee'";
		}
	}
	//--------------------------------------------------------------

	
	
	$rs = $pdb->fetch_first("
	select 		
		count(distinct(A.id)) as player_num,
		count(distinct(case when A.nickname <> '' then A.id end)) as player_role_num,
		count(distinct(case when C.level >= 2 then A.id end)) as player_role_num_2,
		count(distinct(case when C.level >= 10 then A.id end)) as player_role_num_10,
		count(distinct(case when C.level >= 20 then A.id end)) as player_role_num_20,
		count(distinct(case when C.level >= 30 then A.id end)) as player_role_num_30,
		count(distinct(case when C.level >= 40 then A.id end)) as player_role_num_40,
		count(distinct(case when D.total_ingot > 0 then A.id end)) as player_role_pay,	
		sum(E.charge_ingot) as amount	
	from 
		player A
		left join player_trace B on A.id = B.player_id
		left join player_role C on A.id = C.player_id and A.main_role_id = C.id
		left join player_charge_record D on A.id = D.player_id
		left join player_order_execute_record E on A.id = E.player_id
		
	where 
		A.id <> 0
		and A.is_tester = 0
		$set_time
		$set_source
	");

	if($rs){
		$player_num = $rs['player_num']; 	
		$player_role_num =$rs['player_role_num']; 	
		$player_role_num_2 =$rs['player_role_num_2']; 	
		$player_role_num_10 =$rs['player_role_num_10']; 	
		$player_role_num_20 =$rs['player_role_num_20']; 	
		$player_role_num_30 =$rs['player_role_num_30']; 	
		$player_role_num_40 =$rs['player_role_num_40']; 	
		$player_role_pay =$rs['player_role_pay']; 	
		$amount =$rs['amount']; 	

	}	
	
	$num = $pdb->result($pdb->query("
	select 
		count(distinct(B.source))
	from 
		player A
		left join player_trace B on A.id = B.player_id
		left join player_role C on A.id = C.player_id and A.main_role_id = C.id
		left join player_charge_record D on A.id = D.player_id
		left join player_order_execute_record E on A.id = E.player_id		
	where 
		A.is_tester = 0
		$set_time	
		$set_source
	"),0);	
	if($num)
	{	
	
	
		//--------------------------------------------------------------
		$query = $pdb->query("
		select 
			B.source,
			count(distinct(A.id)) as player_num,
			count(distinct(case when A.nickname <> '' then A.id end)) as player_role_num,
			count(distinct(case when C.level >= 2 then A.id end)) as player_role_num_2,
			count(distinct(case when C.level >= 10 then A.id end)) as player_role_num_10,
			count(distinct(case when C.level >= 20 then A.id end)) as player_role_num_20,
			count(distinct(case when C.level >= 30 then A.id end)) as player_role_num_30,
			count(distinct(case when C.level >= 40 then A.id end)) as player_role_num_40,
			count(distinct(case when D.total_ingot > 0 then A.id end)) as player_role_pay,
			sum(E.charge_ingot) as amount
		from 
			player A
			left join player_trace B on A.id = B.player_id
			left join player_role C on A.id = C.player_id and A.main_role_id = C.id
			left join player_charge_record D on A.id = D.player_id
			left join player_order_execute_record E on A.id = E.player_id
		where 
			A.is_tester = 0
			$set_time
			$set_source
		group by 
			B.source
			$set_order	
		limit 
			$start_num,$pageNum		
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
		
			$source_list[$rs['source']] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Data&type=role_rate&stime=$stime&etime=$etime&open_date=$open_date&order=$order&tt=$tt&cid=$cid&sid=$sid");	
	}

	$pdb->close();
	include_once template('player_data_role_rate');
	
}


//--------------------------------------------------------------------------------------------日报表

function DataDay() {
	global $db,$cid,$pdb,$sid,$server,$adminWebType,$page;
	global $cookiepath,$cookiedomain;
	$cookie = $_COOKIE['sxd_setgamedata_'.$cid.'_'.$sid];
	if (!$cookie) {
		SetGameData($cid,$sid);
		setcookie('sxd_setgamedata_'.$cid.'_'.$sid, 'y',time()+SXD_SYSTEM_DATATIME_OUT,$cookiepath,$cookiedomain);
	}
	$month = ReqStr('month');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$stimes = strtotime($stime.' 00:00:00');
	$etimee = strtotime($etime.' 23:59:59');
	
	
	if(!$month)
	{
		$month = date('Y-m');
	}
	//----------------------------------------上个月----------------------------------------------------------------------------	
	$firstday = date("Y-m-01",strtotime($month.'-01'));
	$lastmonthday = date('Y-m-d',strtotime($firstday)-86400);//上个月最后一天
	//-----------------------------------------------------------------
	if ($stime && $etime && $etime >= $stime) 
	{
	
		$dt_start = strtotime($stime);
		$dt_end = strtotime($etime);
		while ($dt_start<=$dt_end){
			$day_list[] =  date('Y-m-d',$dt_start);
			$dt_start = strtotime('+1 day',$dt_start);
		}

		$slastday = date('Y-m-d',strtotime($stime)-86400);//前一天
		$elastday = date('Y-m-d',strtotime($etime)-86400);//前一天
		$set_time = "and gdate >= '$slastday' and gdate <= '$etime'";
		$set_time2 = "and gdate >= '$stime' and gdate <= '$etime'";
		$set_time3 = "and date_format(dtime, '%Y-%m-%d') >= '$stime' and date_format(dtime, '%Y-%m-%d') <= '$etime'";
		$set_time4 = "and B.first_login_time >= '$stimes' and B.first_login_time <= '$etimee'";
		
	}else{
		//--------------------------循环日期---------------------------------------
		
		$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   
	
		for ($i=1;$i<=$day_num;$i++)
		{
			$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
			
		}
		
		$set_time = "and (date_format(gdate, '%Y-%m') = '$month' or gdate = '$lastmonthday')";
		$set_time2 = "and date_format(gdate, '%Y-%m') = '$month'";
		$set_time3 = "and date_format(dtime, '%Y-%m') = '$month'";
		$set_time4 = "and date_format(from_unixtime(B.first_login_time), '%Y-%m') = '$month'";
	}
	
	//-----------最高在线-------------------------------------------------------------
	$max = $db->fetch_first("
		select 
			date_format(from_unixtime(max_online_time), '%Y-%m-%d %H:%i') as max_online_time,
			max(max_online_count) as max_online_count
		from 
			game_data
		where 
			cid = '$cid'
			and sid = '$sid'
			
		group by 
			max_online_count		
		order by 
			max_online_count desc
	");	
	
	if($max)
	{
		$max_online_count = $max['max_online_count'];
		$max_online_time = $max['max_online_time'];		
	}	
	//-----------当前最高在线-------------------------------------------------------------
	$max_now = $db->fetch_first("
		select 
			date_format(from_unixtime(max_online_time), '%Y-%m-%d %H:%i') as max_online_now_time,
			max(max_online_count) as max_online_now_count
		from 
			game_data
		where 
			cid = '$cid'
			and sid = '$sid'
			$set_time2	
		group by 
			max_online_count		
		order by 
			max_online_now_count desc
	");	
	
	if($max_now)
	{
		$max_online_now_count = $max_now['max_online_now_count'];
		$max_online_now_time = $max_now['max_online_now_time'];	
	}	
	//-----------统计数据-------------------------------------------------------------
	$d = $db->fetch_first("
		select 
			sum(login_count) as login_count,
			sum(out_count) as out_count,
			sum(avg_online_count) as avg_online_count,
			sum(pay_amount) as pay_amount,
			sum(pay_num) as pay_num,
			sum(new_player) as new_player,
			sum(consume) as consume,
			count(*) as data_num
			
		from 
			game_data
		where 
			cid = '$cid'
			and sid = '$sid'
			$set_time2	
	");	
	
	if($d)
	{

		$login_count = $d['login_count'];
		$out_count = $d['out_count'];
		$avg_online_count = $d['avg_online_count'];
		$pay_amount = $d['pay_amount'];	
		$pay_num = $d['pay_num'];	

		$new_player = $d['new_player'];	
		$consume = $d['consume'];	
		$data_num = $d['data_num'];
	}
	
	$rs = $pdb->fetch_first("
	select 		
		count(A.id) as register_count,
		count(case when A.nickname <> '' then A.id end) as create_count
	from 
		player A
		left join player_trace B on A.id = B.player_id
	where 
		A.id <> 0
		$set_time4
	");

	if($rs){
		$register_count = $rs['register_count'];
		$create_count = $rs['create_count'];
	}		
	
	
	//-------------------------------------------总充值人数----------------------------------------------------
	$pay_player_count = $db->result_first("
	SELECT 		
		COUNT(DISTINCT(player_id)) AS pay_player_count
	FROM 
		pay_data 
	WHERE 
		cid = '$cid'
		and sid = '$sid'
		AND status = 0	
		AND success = 1	
		$set_time3	
	");
	
	
	
	
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(gdate, '%Y-%m')) AS time 
	from 
		game_data
	where
		cid = '$cid'
		and sid = '$sid'		
	order by 
		time desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}
	//---------------------数据---------------------------------------------
	$query = $db->query("
	select 
		* 
	from 
		game_data
	where 
		cid = '$cid'
		and sid = '$sid'
		$set_time
	order by 
		gdate asc
	");
	while($drs = $db->fetch_array($query))
	{
		$data[$drs['gdate']]=$drs;
	}
	if(is_array($data))
	{ 
		$i=0;
		foreach($data as $drs)
		{
			$yesterday_create_count = $data[date('Y-m-d',strtotime($drs['gdate'])-86400)]['create_count'];
			$drs['out_rate'] = $yesterday_create_count ? round($drs['out_count']/$yesterday_create_count,2)*100 : 0;
			$out_rate += $drs['out_rate'];
			if ($yesterday_create_count) $i++;
			$data[$drs['gdate']]=$drs;
		}
	}
		
	$db->close();
	$pdb->close();


	 
	include_once template('player_data_day');
	
}
function SetGameData($cid,$sid,$day='') {
	global $db,$pdb,$server,$cookiepath,$cookiedomain;
/*	if ($_COOKIE['sxd_setgamedata_'.$cid.'_'.$sid]) {
		return;
	}*/
	if (!$server['private'] && $server['open_date'] <= date('Y-m-d H:i:s')) {//如果到开服日期，并且是私有
		$db->query("update servers set private = 1 where cid = '$cid' and sid = '$sid' ");
	}
	if (!$server['private']) {//如果不是公开服不执行
		return;
	}	
	$today = $day ? $day : date('Y-m-d');
	//$today = date('Y-m-d',time());//今日
	$yesterday = date('Y-m-d',strtotime($today)-86400);//昨天数据
	//-------------------------------------------当前最高在线----------------------------------------------------
	$max = $pdb->fetch_first("
		SELECT 
			`time` AS max_online_time,
			MAX(online_count) AS max_online_count
		FROM 
			server_state
		WHERE 
			DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$today'
		GROUP BY
			online_count		
		ORDER BY 
			max_online_count desc
	");
	if($max)
	{
		$max_online_count = $max['max_online_count'];
		$max_online_time = $max['max_online_time'];		
	}
	if($max)
	{	
		//-------------------------------------------平均在线----------------------------------------------------
		$avg = $pdb->fetch_first("
		SELECT
			SUM(online_count) AS online_count,
			COUNT(*) AS hour_count
		FROM 
			server_state
		WHERE 
			DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$today'		
		");

		if($max)
		{
			$avg_online_count = round($avg['online_count']/$avg['hour_count']);
		}				
	
		//-------------------------------------------注册/创建/登陆数据----------------------------------------------------
		
		$reg = $pdb->fetch_first("
		SELECT
			SUM(register_count) AS register_count,
			SUM(create_count) AS create_count,
			SUM(login_count) AS login_count
		FROM 
			server_state
		WHERE 
			DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$today'
	
		");
		if($reg)
		{
			$register_count = $reg['register_count'];
			$create_count = $reg['create_count'];		
			$login_count = $reg['login_count'];		
		}	
		//-------------------------------------------新手流失----------------------------------------------------
		$out_count = $pdb->result_first("
			SELECT 
				COUNT(B.player_id) AS out_count
			FROM 
				player A
				LEFT JOIN player_trace B ON A.id = B.player_id
				LEFT JOIN player_role C ON A.id = C.player_id and A.main_role_id = C.id
	
			WHERE 
				C.level < 10
				AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') = '$yesterday'			
		");	
		
		
	
		//-------------------------------------------消费----------------------------------------------------
		$con = $pdb->fetch_first("

		select 
			SUM(if(change_charge_value < 0,change_charge_value,0)) AS consume
		from 
			player_ingot_change_record
		where 
			DATE_FORMAT(FROM_UNIXTIME(change_time), '%Y-%m-%d') = '$today'
		"); 		
		if($con){
			$consume = round(($con['consume']/$server['coins_rate']),2);				
			
		}			
		
		
		
	}
		//-------------------------------------------充值----------------------------------------------------
		$pay = $db->fetch_first("
		SELECT 		
			COUNT(DISTINCT(player_id)) AS pay_player_count,
			SUM(amount) AS pay_amount,
			COUNT(*) AS pay_num
		FROM 
			pay_data 
		WHERE 
			cid = '$cid'
			AND sid = '$sid'
			AND status = 0	
			AND success = 1	
			AND DATE_FORMAT(`dtime`, '%Y-%m-%d') = '$today'
		");
		if($pay){
			$pay_player_count = $pay['pay_player_count'];
			$pay_amount = round($pay['pay_amount'],2);
			$pay_num = $pay['pay_num'];		
			

		}
		
		//-------------------------------------------首充----------------------------------------------------

		$new_player = $db->result_first("
		SELECT 
			new_player
		FROM 
			pay_day_new
		WHERE 
			cid = '$cid'
			AND sid = '$sid'
			AND DATE_FORMAT(pdate, '%Y-%m-%d') = '$today'
		");
	
		//-----------------------------------执行插入------------------------------------------------------------
		$d = $db->result($db->query("select count(*) from game_data where cid = '$cid' and sid = '$sid' and gdate = '$today'"),0);
		if (!$d)
		{
			$db->query("
			insert into 
			game_data
				(cid,sid,max_online_time,max_online_count,avg_online_count,register_count,create_count,login_count,out_count,pay_player_count,pay_amount,pay_num,consume,new_player,gdate) 

			values 
				('$cid','$sid','$max_online_time','$max_online_count','$avg_online_count','$register_count','$create_count','$login_count','$out_count','$pay_player_count','$pay_amount','$pay_num','$consume','$new_player','$today') 
			");
		}else{
			$db->query("
			update 
				game_data 
			set 
				max_online_time = '$max_online_time',
				max_online_count = '$max_online_count',
				avg_online_count = '$avg_online_count',
				register_count = '$register_count',
				create_count = '$create_count',
				login_count = '$login_count',
				out_count = '$out_count',
				pay_player_count = '$pay_player_count',
				pay_amount = '$pay_amount',
				pay_num = '$pay_num',
				consume = '$consume',
				new_player = '$new_player'
			where 
				cid = '$cid' 
				and sid = '$sid' 
				and gdate = '$today'
			");
		}
		
	//}
}
//--------------------------------------------------------------------------------------------充值消费比率

function DataPayRate($type) {
	global $cid,$db,$pdb,$sid,$server;
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');

	if ($stime && $etime) 
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') >= '$stime' and date_format(dtime, '%Y-%m-%d') <= '$etime'";
		$set_day2 = "and date_format(from_unixtime(A.change_time), '%Y-%m-%d') >= '$stime' and date_format(from_unixtime(A.change_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$stime'";
		$set_day2 = "and date_format(from_unixtime(A.change_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$etime'";
		$set_day2 = "and date_format(from_unixtime(A.change_time), '%Y-%m-%d') = '$etime'";
	}
	
	
	//-----------充值-------------------------------------------------------------
	$amount = $db->result($db->query("
	select 
		sum(amount) 
	from 
		pay_data
	where 
		sid = '$sid'
		and success = 1
		and status = 0
		$set_day
	"),0); 	
	$amount = round($amount,2);
	
	//-----------消费/系统赠送或游戏内获得的元宝数量-------------------------------------------------------------
	
	$crs = $pdb->fetch_first("
	select 
		sum(if(D.type = 0,A.value,0)) as consume_0,
		sum(if(D.type = 1 and D.id != 35,A.value,0)) as consume_1
	from 
		player_ingot_change_record A
		left join player_charge_record B on A.player_id = B.player_id
		left join player C on A.player_id = C.id
		left join ingot_change_type D on A.type = D.id
	where 
		B.total_ingot > 0
		and C.is_tester = 0
		$set_day2
	");	
	if($crs) 
	{
		$consume_0 = $crs['consume_0'];//消费
		$consume_1 = $crs['consume_1'];//获取
	}
	$consume = round(($consume_0+$consume_1)/$server['coins_rate'],2);
	$db->close();
	$pdb->close();
	include_once template('player_data_pay_rate');
	
}
//--------------------------------------------------------------------------------------------充值排行

function DataPayOrder($type) {
	global $cid,$db,$sid,$server,$adminWebType,$page;;
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	
	$pay = $db->fetch_first("
	select 		
		count(distinct(player_id)) as pay_player_count,
		count(*) as pay_count,
		sum(amount) as pay_amount
	from 
		pay_data 
	where 
		sid = '$sid'
		and success = 1
		and status = 0	
	");
	if($pay){
		$pay_player_count = $pay['pay_player_count'];//-----------充值人数
		$pay_count = $pay['pay_count'];//-----------充值次数
		$pay_amount = $pay['pay_amount'];//-----------收入
	}
		///----------------------------------充值排行----------------------------------------------------

	if($pay_player_count)
	{
		$i=1*$start_num+1;
		$query = $db->query("
		select 
			sum(amount) as money,
			count(*) as payNum,
			amount,
			player_id,
			username,
			nickname,
			dtime,
			ditme_up
		from 
			(
				select 
					* 
				from 
					pay_data 
				where 
					sid = '$sid'
					and success = 1
					and status = 0
				order by 
					pid desc
			) 
			pay_data
		where 
			sid = '$sid'
			and success = 1
			and status = 0				
				
		group by 
			player_id		
		order by 
			money desc ,
			pid desc
		limit 
			$start_num,$pageNum
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['i'] = $i++;
			$list_array[$rs['player_id']] =  $rs;
		}
		$list_array_pages = multi($pay_player_count,$pageNum,$page,$adminWebType.".php?in=player&action=Data&type=pay_order&cid=$cid&sid=$sid");	
	}
	
	
	
	$db->close();
	include_once template('player_data_pay_order');
	
}

//--------------------------------------------------------------------------------------------充值报表

function DataPayData($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;;
	$month = ReqStr('month');
	if(!$month)
	{
		$month = date('Y-m');
	}
	//--------------------------循环日期---------------------------------------
	
	$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   

	for ($i=1;$i<=$day_num;$i++)
	{
		$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
		
	}
	
	$set_time = "and date_format(gdate, '%Y-%m') = '$month'";
	$set_time2 = "and date_format(dtime, '%Y-%m') = '$month'";


	//-----------统计数据-------------------------------------------------------------
	$d = $db->fetch_first("
		select 
			sum(pay_amount) as pay_amount,
			sum(pay_num) as pay_num,
			sum(new_player) as new_player,
			sum(consume) as consume			
		from 
			game_data
		where 
			cid = '$cid'
			and sid = '$sid'
			$set_time	
	");	
	
	if($d)
	{

		$pay_amount = $d['pay_amount'];	
		$pay_num = $d['pay_num'];	
		$new_player = $d['new_player'];	
		$consume = $d['consume'];	
	}
	
	
	
	//-------------------------------------------总充值人数----------------------------------------------------
	$pay_player_count = $db->result_first("
	SELECT 		
		COUNT(DISTINCT(player_id)) AS pay_player_count
	FROM 
		pay_data 
	WHERE 
		cid = '$cid'
		and sid = '$sid'
		AND status = 0	
		AND success = 1	
		$set_time2
	");
	
	
	
	
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(gdate, '%Y-%m')) AS time 
	from 
		game_data
	where
		cid = '$cid'
		and sid = '$sid'		
	order by 
		time desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}

	//---------------------数据---------------------------------------------
	$query = $db->query("
	select 
		* 
	from 
		game_data
	where 
		cid = '$cid'
		and sid = '$sid'
		$set_time
	order by 
		gdate asc
	");
	while($drs = $db->fetch_array($query))
	{
		$data[$drs['gdate']]=$drs;
	}
		
	$db->close();
	$pdb->close();
	include_once template('player_data_pay_data');
}
//--------------------------------------------------------------------------------------------充值记录
function DataPay($type) {
	global $db,$cid,$sid,$server,$adminWebType,$adminWebName,$page;
	$usertype = ReqStr('usertype');
	$username = ReqStr('username');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$oid = ReqStr('oid');
	$uid = ReqNum('uid');
	$nickname = $username;
	

	if ($username) 
	{
	
		if ($usertype == 2)
		{
			require_once callApiVer($server['server_ver']);
			api_base::$SERVER = $server['api_server'];
			api_base::$PORT   = $server['api_port'];
			api_base::$ADMIN_PWD   = $server['api_pwd'];
		
			$user = api_admin::get_username_by_nickname($username);//找帐号
			if ($user['result'] == 1) {
				$username = $user['username'][1];
			}else{
				showMsg('无此玩家！');	
				return;
			}
		}
		if ($uid) 
		{
			$set_uid = "and player_id ='$uid'";
		}
		
		$set_user = "and username ='$username' $set_uid";
	}	
	if ($oid) 
	{
		$set_oid = "and oid ='$oid'";
	}
	if ($stime && $etime) 
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') >= '$stime' and date_format(dtime, '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$etime'";
	}
	
	//-----------收入/待充/测试------------------------------------------------------------
	$mrs = $db->fetch_first("
	select 		
		sum(if(success = 1 and status = 0,amount,0)) as pay_amount ,
		sum(if(success = 0,amount,0)) as pay_amount_no ,
		sum(if(status = 1,amount,0)) as pay_amount_test
	from 
		pay_data 
	where 
		sid = '$sid'	
		$set_user 
		$set_day
	");
	if($mrs){
		$pay_amount = $mrs['pay_amount'];//-----------收入
		$pay_amount_no = $mrs['pay_amount_no'];//-----------待充
		$pay_amount_test = $mrs['pay_amount_test'];//-----------测试
	}	
	
		
	//-----------充值记录----------------------------------------------------------
	
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		pay_data
	where 
		sid = '$sid'	
		$set_user 
		$set_day
		$set_oid
		
	"),0); //获得总条数
	if($num){	
		$query = $db->query("
		select 
			*
		from 
			pay_data
		where 
			sid = '$sid'
			$set_user 
			$set_day 
			$set_oid
		order by
			pid desc 
		limit
			$start_num,$pageNum
		");	
		while($prs = $db->fetch_array($query)){	
			$list_array[] = $prs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Data&type=pay&username=$username&oid=$oid&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
	}	
	$db->close();
	include_once template('player_data_pay');
}
//--------------------------------------------------------------------------------------------在线注册登陆情况

function DataReg($type) {
	global $db,$cid,$pdb,$sid,$server,$adminWebType,$page;
	$month = ReqStr('month');
	//-----------------------------------------------------------------
	if (!$month) 
	{
		$month = date('Y-m');
	}
	//--------------------------循环日期---------------------------------------
	
	$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   

	for ($i=1;$i<=$day_num;$i++)
	{
		$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
		
	}
	//-----------月份日期-------------------------------------------------------
	$query = $pdb->query("
	select 
		distinct(date_format(from_unixtime(`time`), '%Y-%m')) AS time 
	from 
		server_state A 	
	order by 
		time desc
	");
	while($drs = $pdb->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}	
	//----------------------------------------上个月----------------------------------------------------------------------------	
	$firstday = date("Y-m-01",strtotime($month.'-01'));
	$lastmonthday = date('Y-m-d',strtotime($firstday)-86400);//上个月最后一天
	//$lastmonthday2 = date('Y-m-d',strtotime($firstday)-86400*2);//上个月最后第二天	
	


	//-----------最高在线-------------------------------------------------------------
	$max_month = $pdb->fetch_first("
		select 
			date_format(from_unixtime(`time`), '%Y-%m-%d %H:%i') as max_online_month_time,
			max(online_count) as max_online_month_count
		from 
			server_state
		where 
			date_format(from_unixtime(`time`), '%Y-%m') = '$month' 
		group by 
			online_count		
		order by 
			max_online_month_count desc
	");
	
	if($max_month)
	{
		$max_online_month_count = $max_month['max_online_month_count'];
		$max_online_month_time = $max_month['max_online_month_time'];		
		
	}	
	
	$max = $pdb->fetch_first("
		select 
			date_format(from_unixtime(`time`), '%Y-%m-%d %H:%i') as max_online_time,
			max(online_count) as max_online_count
		from 
			server_state
		group by 
			online_count		
		order by 
			max_online_count desc
	");	
	
	if($max)
	{
		$max_online_count = $max['max_online_count'];
		$max_online_time = $max['max_online_time'];		
	}	

	//----------------------------------------注册/创建/登陆数据----------------------------------------------------------------------------

	$query = $pdb->query("
	select
		max(online_count) as max_online_count,
		sum(register_count) as register_count,
		sum(create_count) as create_count,
		sum(login_count) as login_count,
		sum(online_count) as online_count,
		count(*) as hour_count,
		date_format(from_unixtime(`time`), '%Y-%m-%d') as data_date
	from 
		server_state 
	where 
		date_format(from_unixtime(`time`), '%Y-%m') = '$month' 
	or 
		date_format(from_unixtime(`time`), '%Y-%m-%d') = '$lastmonthday' 
	group by 
		data_date

	");
	while($drs = $db->fetch_array($query)){
		$data[$drs['data_date']] = $drs;
	}	
	
	//----------------------------------------新手流失----------------------------------------------------------------------------
	$query = $pdb->query("
	select 
		count(B.player_id) as out_count,
		date_format(from_unixtime(B.first_login_time), '%Y-%m-%d') as data_date
	from 
		player A
		left join player_trace B on A.id = B.player_id
		left join player_role C on A.id = C.player_id and A.main_role_id = C.id
	where 
		(date_format(from_unixtime(B.first_login_time), '%Y-%m') = '$month' 
		or date_format(from_unixtime(B.first_login_time), '%Y-%m-%d') = '$lastmonthday')
		and C.level < 10
	group by 
		data_date		
	");	
	while($pors = $pdb->fetch_array($query)){
		$playerout[$pors['data_date']] = $pors;
	}
			
	$db->close();
	$pdb->close();
	include_once template('player_data_reg');
}

//-------------------------------------------------------------------------------------------关卡报表

function TollGate() {
	global $pdb,$cid,$sid,$server;
	$tt = ReqNum('tt');
	//$order = ReqStr('order');
	//$stime = ReqStr('stime');
	//$etime = ReqStr('etime');
/*	if (!$order || $order == 'desc') 
	{
		$order = 'desc';
		$order2 = 'asc';
	}elseif($order == 'asc'){
		$order2 = 'desc';
	}*/
	
	

	$filename = $sid."_sxd_tollgate_".$tt.".php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = @filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);
	
	
/*	if ($stime && $etime) 
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') >= '$stime' and date_format(dtime, '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(dtime, '%Y-%m-%d') = '$etime'";
	}*/
	if($is_update)
	{
		$tollgatenum = $pdb->result($pdb->query("select count(distinct(`lock`)) from mission where type = '$tt'"),0);//总关卡
		$times_array = array();
		$failedchallenge_array = array();
		$list_array = array();

		$query = $pdb->query("
		select 
			distinct(A.lock),
			A.name as mission_name,
			sum(B.failed_challenge) as failed_challenge,
			sum(B.times) as times,
			count(B.player_id) as people,
			count(case when B.is_finished = 1 then B.player_id end) as finished_people,
			count(case when B.is_finished = 0 then B.player_id end) as no_finished_people,		
			B.mission_id,
			D.name as town_name	,
			D.camp_id		
		from 
			mission A,
			player_mission_record B,
			mission_section C,
			town D,
			player E
		where 
			A.id = B.mission_id
			and A.mission_section_id = C.id
			and C.town_id = D.id
			and A.type = '$tt'
			and B.player_id = E.id
			and E.is_tester = 0
		group by 
			A.lock				
		order by 
			A.lock desc
		");
		$tollgateplayernum = $pdb->num_rows($query);
		if($tollgateplayernum)
		{
			$i=0;
			while($rs = $pdb->fetch_array($query))
			{	
				$rs['i'] = $i++;
				$times_array[] =  $rs['times'];
				$failedchallenge_array[] =  $rs['failed_challenge'];
				$list_array[$rs['i']] =  $rs;
			}
			$timesmax =  max($times_array);
			$failedchallengemax =  max($failedchallenge_array);
		}
		
		$pdb->close();
	
	}
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$timesmax='.($timesmax ? $timesmax : 0).";\n"; 
		$str .= '$tollgatenum='.($tollgatenum ? $tollgatenum : 0).";\n"; 
		$str .= '$tollgateplayernum='.($tollgateplayernum ? $tollgateplayernum : 0).";\n"; 
		$str .= '$failedchallengemax='.($failedchallengemax ? $failedchallengemax : 0).";\n"; 
		$str .= '$times_array='.var_export($times_array, TRUE).";\n"; 
		$str .= '$failedchallenge_array='.var_export($failedchallenge_array, TRUE).";\n"; 
		$str .= '$list_array='.var_export($list_array, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	
	
	include_once template('player_tollgate');
} 




//--------------------------------------------------------------------------------------------渠道信息

function DataSource() {
	global $cid,$pdb,$sid,$server,$adminWebType,$page;
	$tt = ReqNum('tt');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$order = ReqStr('order');
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	if($order)
	{
		$set_order = "order by $order desc";
	}	
	if (!$tt)
	{
		if ($stime && $etime) 
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.regdate), '%Y-%m-%d') >= '$stime' AND DATE_FORMAT(FROM_UNIXTIME(A.regdate), '%Y-%m-%d') <= '$etime'";
		}elseif($stime && !$etime)
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.regdate), '%Y-%m-%d') = '$stime'";
		}elseif(!$stime && $etime)
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(A.regdate), '%Y-%m-%d') = '$etime'";
		}
	}elseif ($tt == 1){
		if ($stime && $etime) 
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') >= '$stime' AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') <= '$etime'";
		}elseif($stime && !$etime)
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') = '$stime'";
		}elseif(!$stime && $etime)
		{
			$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') = '$etime'";
		}
	}
	//--------------------------------------------------------------

	
	
	$rs = $pdb->fetch_first("
	select 		
		count(distinct(A.id)) as player_num,
		count(distinct(case when A.nickname <> '' then A.id end)) as player_role_num,
		count(distinct(case when C.level >= 10 then A.id end)) as player_role_num_10,
		count(distinct(case when C.level >= 20 then A.id end)) as player_role_num_20,
		count(distinct(case when C.level >= 40 then A.id end)) as player_role_num_40,
		count(distinct(case when D.total_ingot > 0 then A.id end)) as player_role_pay,	
		sum(E.charge_ingot) as amount	
	from 
		player A
		left join player_trace B on A.id = B.player_id
		left join player_role C on A.id = C.player_id and A.main_role_id = C.id
		left join player_charge_record D on A.id = D.player_id
		left join player_order_execute_record E on A.id = E.player_id
		
	where 
		A.id <> 0
		and A.is_tester = 0
		$set_time
	");

	if($rs){
		$player_num = $rs['player_num']; 	
		$player_role_num =$rs['player_role_num']; 	
		$player_role_num_10 =$rs['player_role_num_10']; 	
		$player_role_num_20 =$rs['player_role_num_20']; 	
		$player_role_num_40 =$rs['player_role_num_40']; 	
		$player_role_pay =$rs['player_role_pay']; 	
		$amount =$rs['amount']; 	

	}	
	
	$num = $pdb->result($pdb->query("
	select 
		count(distinct(B.source))
	from 
		player A
		left join player_trace B on A.id = B.player_id
	where 
		A.is_tester = 0
		$set_time		
		
	"),0);	
	if($num)
	{	
	
	
		//--------------------------------------------------------------
		$query = $pdb->query("
		select 
			B.source,
			count(distinct(A.id)) as player_num,
			count(distinct(case when A.nickname <> '' then A.id end)) as player_role_num,
			count(distinct(case when C.level >= 10 then A.id end)) as player_role_num_10,
			count(distinct(case when C.level >= 20 then A.id end)) as player_role_num_20,
			count(distinct(case when C.level >= 40 then A.id end)) as player_role_num_40,
			count(distinct(case when D.total_ingot > 0 then A.id end)) as player_role_pay,
			sum(E.charge_ingot) as amount
		from 
			player A
			left join player_trace B on A.id = B.player_id
			left join player_role C on A.id = C.player_id and A.main_role_id = C.id
			left join player_charge_record D on A.id = D.player_id
			left join player_order_execute_record E on A.id = E.player_id
		where 
			A.is_tester = 0
			$set_time
		group by 
			B.source
			$set_order	
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			
			$source_list[$rs['source']] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=DataSource&stime=$stime&etime=$etime&open_date=$open_date&order=$order&cid=$cid&sid=$sid");	
	}

	$pdb->close();
	include_once template('player_data_source');
	
}
function UpdateData() {
	global $db,$cid,$pdb,$sid,$server,$adminWebType;
   	$day = ReqStr('day');
	if ($day > date('Y-m-d')){
		showMsg('还没有数据！');	
		return;	
	}	
	if ($day && $cid && $sid){
		SetGameData($cid,$sid,$day);
		showMsg('更新成功！','','','greentext');	
	}else{
		showMsg('错误参数！');	
		return;		

	}
   
}

function  SavePlayerEdit()
{
	global $cid,$sid,$server,$adminWebType;
   	$uid = ReqNum('uid');
   	$nickname = ReqStr('nickname');
   	$nickname_old = ReqStr('nickname_old');
	if (!$uid || !$nickname ){
		showMsg('错误参数！');	
		return;	
	}	
	require_once callApiVer($server['server_ver']);
	api_base::$SERVER = $server['api_server'];
	api_base::$PORT   = $server['api_port'];
	api_base::$ADMIN_PWD   = $server['api_pwd'];	
	$msg = api_admin::set_nickname($uid, $nickname);
	if (!$msg['result']) {
		showMsg('修改失败！');	
		return;	
	}else{
		insertServersAdminData($cid,$sid,$uid,$nickname,'修改昵称，旧昵称：'.$nickname_old);//插入操作记录
		showMsg('修改成功！','','','greentext');	
		return;		
	}
	
}

?>