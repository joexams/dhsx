<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

function  Player()
{
	global $pdb,$cid,$sid,$server,$adminWebType,$page;
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	
	$username = trim(ReqStr('username'));
	$ip = trim(ReqStr('ip'));
	$player_id = trim(ReqNum('player_id'));
	$order = ReqStr('order');
	$type = ReqStr('type');
	$mt = ReqNum('mt');
	//$set_username = $username ? " and (A.username like '%$username%' OR A.nickname like '%$username%')" : '';
	$set_username = $username ? " and (A.username = '$username' OR A.nickname = '$username')" : '';
	$set_uid = $player_id ? " and (A.id = '$player_id')" : '';
	$set_ip = $ip ? " and (C.last_login_ip = '$ip' or C.first_login_ip = '$ip')" : '';		
	$set_mission = (!$mid && !$t) ? "left join mission I on F.mission = I.lock": '';

	if ($type == 'test') 
	{
		$set_type = "and A.is_tester <> 0";
	}elseif($type == 'login'){
		$set_type = "and A.disable_login <> '' and A.disable_login > UNIX_TIMESTAMP(NOW())";
	}elseif($type == 'talk'){
		$set_type = "and A.disable_talk <> '' and  A.disable_talk > UNIX_TIMESTAMP(NOW())";
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
		
		$max_lock = $pdb->result($pdb->query(" select max({$ml}) from player_data"),0);//总关卡
		$set_mission_show =	",J.first_challenge_time,I.name as mission_name,L.name as town_name";
		$set_mission_left =	"
				left join mission I on B.{$ml} = I.`lock`
				left join player_mission_record J on J.player_id = B.player_id and I.id = J.mission_id
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
	}elseif ($order == 'power') 
	{
		$set_order = "B.power desc,";
		$set_nickname = "and nickname <> ''";
	}elseif ($order == 'fame') 
	{
		$set_order = "B.fame desc,";
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
			left join player_role E on A.id = E.player_id and A.main_role_id = E.id
			$set_mission_left		
		where 
			A.id > 0
			$set_uid
			$set_username
			$set_ip
			$set_lock
			$set_type
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
			B.ingot,
			B.coins,
			B.fame,
			B.skill,
			B.power,
			B.max_power,
			C.last_login_ip,
			C.first_login_time,
			C.last_login_time,
			C.source,
			E.level as player_level
			$set_mission_show
			$set_nums
		from 
			player A
			left join player_data B on A.id = B.player_id
			left join player_trace C on A.id = C.player_id
			left join player_role E on A.id = E.player_id and A.main_role_id = E.id
			$set_mission_left
		where 
			A.id <> 0 
			$set_uid
			$set_username 
			$set_ip			
			$set_lock
			$set_type	
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
			$list_array[] =  $rs;
		}
		$username_url = urlencode($username);
		$list_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&username=$username_url&player_id=$player_id&ip=$ip&order=$order&type=$type&mt=$mt&cid=$cid&sid=$sid");	
	}

	$pdb->close();
	include_once template('player');
}

//-------------------------------------------------------查看玩家

function  PlayerView()
{
	global $pdb,$cid,$sid,$adminWebType,$server;
	global $uid,$player; 
	
	//$money = $pdb->result($pdb->query("select sum(charge_ingot) from player_order_execut_record where player_id = '$uid'"),0);

	$deploy_mode_id = $player['deploy_mode_id'];//默认识阵型
	
	$query = $pdb->query("
	select 
		A.*,
		B.name as role_name,
		C.*,
		D.deploy_mode_id
	from 
		player_role A
		left join role B on A.role_id = B.id
		left join player_role_data C on A.id = C.player_role_id
		left join player_deploy_grid D on A.id = D.player_role_id and A.player_id = '$uid' and D.deploy_mode_id = '$deploy_mode_id'
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
			$player_role_array[] =  $rs;
		}
	}	
	
	$pdb->close();
	include_once template('player_view');
}
//------------------------------------------------------击杀BOSS奖励
function BossGift() 
{
	global $pdb,$cid,$sid,$adminWebType,$server;
	global $uid,$player; 

	global $pdb,$sid,$uid,$player,$page;
	$msg=ReqStr('msg','htm');
	$winid=ReqStr('winid');	
	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;	
	
	$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_world_boss_gift  A

		"),0); //获得总条数
	if($num){
		$query = $pdb->query("
		select 
			A.*,
			B.name as item_name,
			C.username,
			C.nickname
		from 
			player_world_boss_gift A
			left join item B on A.item_id = B.id
			left join player C on A.player_id = C.id
		order by 
			A.id desc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			if ($rs['type'] == 0)
			{
				$rs['type_name'] = '物品';
			}elseif ($rs['type'] == 1){
				$rs['type_name'] = '铜钱';
			}elseif ($rs['type'] == 2){
				$rs['type_name'] = '元宝';
			}elseif ($rs['type'] == 3){
				$rs['type_name'] = '声望';
			}elseif ($rs['type'] == 4){
				$rs['type_name'] = '经验';
			}
			if ($rs['status'] == 0)
			{
				$rs['status_name'] = '未点击领取礼包';
			}elseif ($rs['status'] == 1){
				$rs['status_name'] = '已经领取礼包';
			}
			$list_array[] =  $rs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=BossGift&cid=$cid&sid=$sid");	
	}
	$pdb->close();
	include_once template('player_boss_gift');

}
//--------------------------------------------------------------------------------------------帮派数据

function  Faction()
{
	global $pdb,$cid,$sid,$server,$adminWebType,$page;; 	
	$name = trim(ReqStr('name'));
	if ($name) 
	{
		$set_name = " and A.name like '%$name%' ";	
	}

	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	$num = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_faction A 		
	where 
		A.id <> 0
		$set_name 
	"),0);
	if($num)
	{
		$query = $pdb->query("
		select 
			A.*,
			B.name as faction_class_name,
			C.faction_level_name
		from 
			player_faction A
			left join camp B on A.camp_id = B.id
			left join faction_level C on A.level = C.id
		where 
			A.id <> 0 
			$set_name 			
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
		$list_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Faction&name=$name&cid=$cid&sid=$sid");	
	}
	$pdb->close();
	include_once template('player_faction');
}
//--------------------------------------------------------------------------------------------元宝记录

function Money() {
	global $pdb,$cid,$sid,$server,$adminWebType,$page;
	$ingot_change_type0_array = globalDataListPlayer('ingot_change_type','type = 0');//消费类型
	$ingot_change_type1_array = globalDataListPlayer('ingot_change_type','type = 1');//获取类型
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$is_tester = ReqNum('is_tester');
	$order = ReqStr('order');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	$vip = ReqNum('vip');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.type in ($tidArray)";
	}else{
		$tidArrList = array();
	}
	

	if ($username) 
	{
		if ($usertype == 1) {
			$set_username = " and C.username = '$username'";	
		}elseif ($usertype == 2){
			$set_username = " and C.nickname = '$username'";	
		}
	}else{
		$set_is_tester = " and C.is_tester = 0 ";	
	}
	if ($order) 
	{
		$set_order = " A.value desc,";	
	}	


	if ($vip) 
	{

		$set_left = "left join player_charge_record D on A.player_id = D.player_id";
		$set_level = "and D.level_up_time > 0";

	}	

	
	$irs = $pdb->fetch_first("
	select
		sum(if(A.value > 0,A.value,0)) as add_ingot,
		sum(if(A.value < 0,A.value,0)) as del_ingot

	from 
		player_ingot_change_record A 	
		left join ingot_change_type B on A.type = B.id
		left join player C on A.player_id = C.id	
		$set_left		
	where 
		A.id <> 0 
		$set_level
		$set_is_tester
		$set_day
		$set_type
		$set_username 
	");
	if($irs){
		$add_ingot = $irs['add_ingot'];
		$del_ingot = $irs['del_ingot'];
		//$ingot = $add_ingot+abs($del_ingot);
	}
/*	$add_ingot = $pdb->result($pdb->query("
	select
		sum(A.value) as add_ingot
	from 
		player_ingot_change_record A 	
		left join ingot_change_type B on A.type = B.id
		left join player C on A.player_id = C.id			
	where 
		A.value > 0
		$set_is_tester
		$set_day
		$set_type
		$set_username 
	"),0);
	$del_ingot = $pdb->result($pdb->query("
	select
		sum(A.value) as del_ingot

	from 
		player_ingot_change_record A 	
		left join ingot_change_type B on A.type = B.id
		left join player C on A.player_id = C.id			
	where 
		A.value < 0
		$set_is_tester
		$set_day
		$set_type
		$set_username 
	"),0);
	
*/	
	
	$num = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_ingot_change_record A 	
		left join ingot_change_type B on A.type = B.id
		left join player C on A.player_id = C.id	
		$set_left		
	where 
		A.id <> 0 
		$set_level
		$set_is_tester
		$set_day
		$set_type
		$set_username 
	"),0);
	if($num)
	{
		$query = $pdb->query("
		select 
			A.*,
			B.name as type_name,
			C.username,
			C.nickname
		from 
			player_ingot_change_record A
			left join ingot_change_type B on A.type = B.id
			left join player C on A.player_id = C.id
			$set_left
		where 
			A.id <> 0
			$set_level
			$set_is_tester
			$set_day 
			$set_type
			$set_username 			
		order by 
			$set_order A.id desc 
		limit 
			$start_num,$pageNum
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Money&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&is_tester=$is_tester&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
	}
	
	$pdb->close();
	include_once template('player_money');
}
//--------------------------------------------------------------------------------------------数据报表

function Data() {
	global $db,$cid,$pdb,$sid,$server,$adminWebType,$page;
	$type = ReqStr('type');
	if (!$type) 
	{
		DataDay($type);	
	}elseif($type == 'reg'){
		DataReg($type);
	}elseif($type == 'pay'){
		serverAdmin('pay');
		DataPay($type);
	}elseif($type == 'pay_data'){
		serverAdmin('pay');
		DataPayData($type);
	}elseif($type == 'pay_rate'){
		serverAdmin('pay');
		DataPayRate($type);
	}elseif($type == 'pay_order'){
		serverAdmin('pay');
		DataPayOrder($type);
	}elseif($type == 'role_rate'){
		DataRoleRate($type);
	}elseif($type == 'player_level'){
		DataPlayerLevel($type);
	}elseif($type == 'player_out'){
		DataPlayerOut($type);
	}elseif($type == 'player_mammon'){
		DataPlayerMammon($type);
	}elseif($type == 'item'){
		DataItem($type);
	}elseif($type == 'consume'){
		DataConsume($type);
	}elseif($type == 'role'){
		DataRole($type);
	}elseif($type == 'power'){
		DataPower($type);
	}elseif($type == 'other_server_p'){
		DataOtherServerP($type);
	}
	
}



//--------------------------------------------------------------------------------------------滚服统计

function DataOtherServerP($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;	
	$servers_list = globalDataList('servers',"cid = '$cid' and sid <> $sid and db_server = '$server[db_server]'");//服务器	
	$player_role_num = $pdb->result($pdb->query("select count(*) from player_trace where player_id <> 0"),0); 
	$pdb->close();

	include_once template('player_data_other_server_p');
	
}
//--------------------------------------------------------------------------------------------体力统计

function DataPower($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$level = ReqNum('level');	
	$filename = $sid."_sxd_data_power_".$level.".php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = filemtime($flie);//文件创建时间
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
			count(case when B.player_id > 0 then A.id end) as power_player,
			count(case when B.player_id > 0 and C.level_up_time > 0 then A.id end) as power_player_pay,
			count(case when B.player_id > 0 and DATE_FORMAT(FROM_UNIXTIME(B.last_login_time), '%Y-%m-%d') = CURDATE() then A.id end) as power_player_today
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

			
			count(case when A.power = 0 and A.power <= 5 and level_up_time > 0  then A.player_id end) as power_player_pay_0,
			count(case when A.power >= 6 and A.power <= 20 and level_up_time > 0 then A.player_id end) as power_player_pay_1,
			count(case when A.power >= 21 and A.power <= 50 and level_up_time > 0 then A.player_id end) as power_player_pay_2,
			count(case when A.power >= 51 and A.power <= 100 and level_up_time > 0 then A.player_id end) as power_player_pay_3,
			count(case when A.power >= 101 and A.power <= 200 and level_up_time > 0 then A.player_id end) as power_player_pay_4,
			count(case when A.power >= 201 and A.power <= 300 and level_up_time > 0 then A.player_id end) as power_player_pay_5,
			count(case when A.power >= 301 and A.power <= 400 and level_up_time > 0 then A.player_id end) as power_player_pay_6,
			count(case when A.power >= 401 and A.power <= 500 and level_up_time > 0 then A.player_id end) as power_player_pay_7,
			count(case when A.power >= 501 and A.power <= 600 and level_up_time > 0 then A.player_id end) as power_player_pay_8,
			count(case when A.power >= 601 and A.power <= 700 and level_up_time > 0 then A.player_id end) as power_player_pay_9,
			count(case when A.power >= 701 and A.power <= 800 and level_up_time > 0 then A.player_id end) as power_player_pay_10,
			count(case when A.power >= 801 and A.power <= 900 and level_up_time > 0 then A.player_id end) as power_player_pay_11,
			count(case when A.power >= 901 and A.power <= 1000 and level_up_time > 0 then A.player_id end) as power_player_pay_12,
			count(case when A.power >= 1001 and level_up_time > 0 then A.player_id end) as power_player_pay_13		

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
	
	$pdb->close();
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$power_player='.$power_player.";\n"; 
		$str .= '$power_player_pay='.$power_player_pay.";\n"; 
		$str .= '$power_player_today='.$power_player_today.";\n"; 
		$str .= '$power='.var_export($power, TRUE).";\n";//存入数组 
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
	$filetime  = filemtime($flie);//文件创建时间
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
	}
	$pdb->close();
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str = '$role='.var_export($role, TRUE).";\n";//存入数组 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		

	include_once template('player_data_role');
	
}
//--------------------------------------------------------------------------------------------消费统计

function DataConsume($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$username = trim(ReqStr('username'));
	$is_tester = ReqStr('is_tester');
	$username_url = urlencode($username);
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$order = ReqStr('order');
	$t = ReqNum('t');
	$v = ReqNum('v');
	$p = ReqNum('p');
	if ($username) 
	{
		$set_username = " and C.username = '$username'";	
	}else{
		$set_is_tester = " and C.is_tester = 0 ";	
	}
	if ($stime && $etime) 
	{
		$set_time = "AND FROM_UNIXTIME(B.change_time, '%Y-%m-%d') >= '$stime' AND FROM_UNIXTIME(B.change_time, '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_time = "AND FROM_UNIXTIME(B.change_time, '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_time = "AND FROM_UNIXTIME(B.change_time, '%Y-%m-%d') = '$etime'";
	}
	if($order)
	{
		$set_order = "$order desc,";
	}
	if(!$t)
	{
		$set_t = "AND A.type = 0";
	}elseif($t == 1){
		$set_t = "AND A.type = 1";
	}
	if($v == 1)
	{
		$set_v = "AND C.vip_level < 6";
	}elseif($v == 2){
		$set_v = "AND C.vip_level >= 6";
	}	
	if($p == 1)
	{
		$query = $pdb->query("
		select
			player_id
		from 
			player_charge_record
		where
			level_up_time > 0
		order by 
			player_id asc
		");	
		while($prs = $pdb->fetch_array($query)){
			$player_id[] = $prs['player_id'];
		}
		$player_id_list = implode(',', $player_id);
		$set_p = " and C.id not in ($player_id_list)";
	}elseif($p == 2){
		$set_p = " and D.level_up_time > 0";
		$set_left = "left join player_charge_record D on C.id = D.player_id";
	}	
	if(!$set_time && !$username)//如果不在查询才使用缓存
	{
		$filename = $sid."_sxd_data_consume_".$t."_".$p."_".$v."_".$order.".php";//文件名
		$dir = UCTIME_ROOT."/data/";//目录
		$flie = $dir.$filename;//全地址
		$filetime  = filemtime($flie);//文件创建时间
		@include_once($flie);
		if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
		$updatetime = setTime($filetime);
	}
	//--------------------------------------------------------------
	if($is_update || $set_time || $username)
	{
		$rs = $pdb->fetch_first("
		select 		
			COUNT(distinct(B.player_id)) AS player_count,
			COUNT(B.type) AS type_count,
			SUM(abs(B.value)) AS value_count
		from 
			ingot_change_type A
			left join player_ingot_change_record B on A.id = B.type
			left join player C on B.player_id = C.id
			$set_left
		where
			A.id > 0
			$set_p
			$set_v
			$set_is_tester
			$set_t
			$set_username
			$set_time
			
		");
	}
	if($rs){
		$player_count = $rs['player_count'];
		$type_count = $rs['type_count'];
		$value_count = $rs['value_count'];
	}

	//--------------------------------------------------------------

	if($is_update || $set_time || $username)
	{
		$consume = array();
		$query = $pdb->query("
		select 
			COUNT(distinct(B.player_id)) AS player_count,
			COUNT(CASE WHEN A.id > 0 THEN B.type END) AS type_count,
			SUM(if(A.id > 0,abs(B.value),0)) AS value_count,
			A.id,
			A.name as type_name
		from 
			ingot_change_type A
			left join player_ingot_change_record B on A.id = B.type
			left join player C on B.player_id = C.id
			$set_left
		where
			A.id > 0
			$set_p
			$set_v
			$set_is_tester
			$set_t
			$set_username
			$set_time
		group by 
			A.id
		order by 
			$set_order
			value_count desc,
			A.id desc
		");	
		while($crs = $pdb->fetch_array($query)){
			$crs['name_url'] = urlencode($crs['type_name']);
			$tid .= $crs['id'].',';
			$consume[] = $crs;
		}
		if ($tid) //取出没有记录内容的分类
		{
			$tid = substr($tid,0,strlen($tid)-1);
			$query = $pdb->query("
			select
				A.id,
				A.name as type_name
			from 
				ingot_change_type A
			where
				A.id > 0
				and A.id not in ($tid)
				$set_t
			order by 
				A.id desc
			");	
			while($crs = $pdb->fetch_array($query)){
				$consume[] = $crs;
			}
		
		}
	}
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str .= '$rs='.var_export($rs, TRUE).";\n";//存入数组 
		$str .= '$consume='.var_export($consume, TRUE).";\n";//存入数组 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	include_once template('player_data_consume');
	
}
//--------------------------------------------------------------------------------------------老用户流失

function DataPlayerOut($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	$slevel = ReqNum('slevel');
	$elevel = ReqNum('elevel');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$day = ReqNum('day');
	if (!$slevel) {
		$slevel = 1;
	}
	if (!$elevel) {
		$elevel = 100;
	}
	if (!$day) {
		$day = 5;
	}
	$hour = $day*24;	
	
	if ($stime && $etime) 
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(C.first_login_time), '%Y-%m-%d') >= '$stime' AND DATE_FORMAT(FROM_UNIXTIME(C.first_login_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(C.first_login_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_time = "AND DATE_FORMAT(FROM_UNIXTIME(C.first_login_time), '%Y-%m-%d') = '$etime'";
	}



	for ($i=$slevel;$i<=$elevel;$i++)
	{
		$level_list[$i] = $i;
		
	}
	

	$queryall = $pdb->query("
	select 
		COUNT(A.id) AS player_count,
		COUNT(CASE WHEN D.level_up_time > 0 THEN D.player_id END) AS player_pay_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 THEN C.player_id END) AS player_out_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.level_up_time > 0 THEN C.player_id END) AS player_out_pay_count
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
		COUNT(CASE WHEN D.level_up_time > 0 THEN D.player_id END) AS player_pay_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 THEN C.player_id END) AS player_out_count,
		COUNT(CASE WHEN DATE_SUB(NOW(),INTERVAL $hour HOUR) >= DATE_FORMAT(FROM_UNIXTIME(C.last_login_time), '%Y-%m-%d %H:%i:%s') AND C.last_login_time > 0 AND D.level_up_time > 0 THEN C.player_id END) AS player_out_pay_count
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
	$pdb->close();
	include_once template('player_data_player_out');
	
}



//--------------------------------------------------------------------------------------------玩家等级分布

function DataPlayerLevel($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;
	
	$filename = $sid."_sxd_data_player_level.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = filemtime($flie);//文件创建时间
	@include_once($flie);
	if(!$filetime || time() - $filetime >= SXD_SYSTEM_FILETIME_OUT)	$is_update = 1;	//如果调用的缓存文件不存在或过期
	$updatetime = setTime($filetime);

	if ($is_update) 
	{
		$trs = $pdb->fetch_first("
		select 		
			count(A.player_id) as num,
			count(case when A.camp_id = 3 then A.player_id end) as ss_num,
			count(case when A.camp_id = 4 then A.player_id end) as kl_num
		from 
			player_data A
			left join player B on A.player_id = B.id
		where 
			B.is_tester = 0	
		");
	}
	if($trs){
		$num = $trs['num'];//-----------总数
		$ss_num = $trs['ss_num'];//-----------蜀山
		$kl_num = $trs['kl_num'];//-----------昆仑
		$ss_rate  = round($ss_num/($ss_num+$kl_num)*100,2);
		$kl_rate  = round($kl_num/($ss_num+$kl_num)*100,2);
	}	
	//-------------------------------------------------------------------------------------------	
	if ($is_update) 
	{
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
		group by 
			B.level	
		");
			
		while($rs = $pdb->fetch_array($query))
		{	
			
			$player[$rs['level']] =  $rs;
		}
	
	}
	for ($i=1;$i<=100;$i++)
	{
		$level_list[$i] = $i;
		
	}
	
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
		$str .= '$trs='.var_export($trs, TRUE).";\n";//存入数组 
		$str .= '$player='.var_export($player, TRUE).";\n";//存入数组 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	
	include_once template('player_data_player_level');
	
}

//--------------------------------------------------------------------------------------------玩家财富统计

function DataPlayerMammon($type) {
	global $cid,$pdb,$sid,$server,$adminWebType; 
	
	$filename = $sid."_sxd_data_player_mammon.php";//文件名
	$dir = UCTIME_ROOT."/data/";//目录
	$flie = $dir.$filename;//全地址
	$filetime  = filemtime($flie);//文件创建时间
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
	}
	if($rs){
		//$num = $rs['num'];//-----------总数
		$ingot_num = $rs['ingot_num'];//-----------元宝
		$coins_num = $rs['coins_num'];//-----------铜钱
		$ingot_player = $rs['ingot_player'];//-----------元宝持有人数
		$coins_player = $rs['coins_player'];//-----------铜钱持有人数
	}
	
	//-------------------------------------------------------------------------------------------	
	if ($is_update) 
	{

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
	
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		//$str .= '$irs=\''.$irs."';\n";//存入数组 
		$str .= '$rs='.var_export($rs, TRUE).";\n";//存入数组 
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
	$filetime  = filemtime($flie);//文件创建时间
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
	}
	//-------------------------------------------------------------------------------------------	
	
	for ($i=1;$i<=60;$i++)
	{
		$level_list[$i] = $i;
		
	}

	
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	if ($is_update) 
	{
		//$str .= '$irs=\''.$irs."';\n";//存入数组 
		$str .= '$irs='.var_export($irs, TRUE).";\n";//存入数组 
		$str .= '$item='.var_export($item, TRUE).";\n";//存入数组 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------	
	
	
	include_once template('player_data_item');
	
}
//--------------------------------------------------------------------------------------------创建角色比

function DataRoleRate($type) {
	global $cid,$pdb,$sid,$server,$adminWebType;

	$player_num = $pdb->result($pdb->query("select count(*) from player where id <> 0"),0); 	
	$player_role_num = $pdb->result($pdb->query("select count(*) from player_trace where player_id <> 0"),0); 
	$pdb->close();
	include_once template('player_data_role_rate');
	
}

//--------------------------------------------------------------------------------------------日报表

function DataDay($type) {
	global $db,$cid,$pdb,$sid,$server,$adminWebType,$page;
	SetGameData($cid,$sid);
	$month = ReqStr('month');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	if(!$month)
	{
		$month = date('Y-m');
	}
	//----------------------------------------上个月----------------------------------------------------------------------------	
	$firstday = date("Y-m-01",strtotime($month.'-01'));
	$lastmonthday = date('Y-m-d',strtotime($firstday)-86400);//上个月最后一天
	//-----------------------------------------------------------------
	if ($stime && $etime && $etime > $stime) 
	{
	
		$dt_start = strtotime($stime);
		$dt_end = strtotime($etime);
		while ($dt_start<=$dt_end){
			$day_list[] =  date('Y-m-d',$dt_start);
			$dt_start = strtotime('+1 day',$dt_start);
		}

		//$slastday = date('Y-m-d',strtotime($stime)-86400);//前一天
		//$elastday = date('Y-m-d',strtotime($etime)-86400);//前一天
		$set_time = "AND DATE_FORMAT(gdate, '%Y-%m-%d') >= '$stime' AND DATE_FORMAT(gdate, '%Y-%m-%d') <= '$etime'";
		
	}else{
		//--------------------------循环日期---------------------------------------
		
		$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   
	
		for ($i=1;$i<=$day_num;$i++)
		{
			$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
			
		}
		
		$set_time = "and (date_format(gdate, '%Y-%m') = '$month' OR date_format(gdate, '%Y-%m-%d') = '$lastmonthday')";
	}
	
	//-----------最高在线-------------------------------------------------------------
	$max = $db->fetch_first("
		select 
			date_format(from_unixtime(max_online_time), '%Y-%m-%d %H:%i') as max_online_time,
			max(max_online_count) as max_online_count
		from 
			game_data
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
			$set_time	
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
			sum(register_count) as register_count,
			sum(create_count) as create_count,
			sum(login_count) as login_count,
			sum(out_count) as out_count,
			sum(avg_online_count) as avg_online_count,
			sum(pay_amount) as pay_amount,
			sum(pay_player_count) as pay_player_count,
			sum(pay_num) as pay_num,
			sum(new_player) as new_player,
			sum(consume) as consume,
			count(*) as data_num
			
		from 
			game_data
		where 
			cid = '$cid'
			and sid = '$sid'
			$set_time	
	");	
	
	if($d)
	{
		$register_count = $d['register_count'];
		$create_count = $d['create_count'];
		$login_count = $d['login_count'];
		$out_count = $d['out_count'];
		$avg_online_count = $d['avg_online_count'];
		$pay_amount = $d['pay_amount'];	
		$pay_player_count = $d['pay_player_count'];	
		$pay_num = $d['pay_num'];	
		$new_player = $d['new_player'];	
		$consume = $d['consume'];	
		$data_num = $d['data_num'];
	}	
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(gdate, '%Y-%m')) AS time 
	from 
		game_data
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


	 
	include_once template('player_data_day');
	
}
function SetGameData($cid,$sid) {
	global $db,$pdb,$server,$cookiepath,$cookiedomain;
	if ($_COOKIE['sxd_setgamedata_'.$cid.'_'.$sid]) {
		return;
	}
	$today = date('Y-m-d',time());//今日
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
	
	
		//-------------------------------------------消费----------------------------------------------------
		$con = $pdb->fetch_first("
		select 
			SUM(if(D.type = 0,A.value,0)) AS consume_0,
			SUM(if(D.type = 1 AND D.id != 35,A.value,0)) AS consume_1
		from 
			player_ingot_change_record A
			LEFT JOIN player_charge_record B ON A.player_id = B.player_id
			LEFT JOIN player C ON A.player_id = C.id
			LEFT JOIN ingot_change_type D ON A.type = D.id
		where 
			B.level_up_time > 0
			AND C.is_tester = 0
			AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$today'		
		
		"); 		
		if($con){
			$con['value'] = $con['consume_0']+$con['consume_1'];
			$consume = round(($con['value']/$server['coins_rate']),2);				
			
		}			
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
		
	}
	setcookie('sxd_setgamedata_'.$cid.'_'.$sid, 'y',time()+SXD_SYSTEM_DATATIME_OUT,$cookiepath,$cookiedomain);
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
		B.level_up_time > 0
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
	//-----------------------------------------------------------------
	if ($month) 
	{
		$set_month = " and date_format(dtime, '%Y-%m') ='$month'";
	}
	else
	{
		$month = date('Y-m');
		$set_month = " and date_format(dtime, '%Y-%m') ='$month'";
	}
	//--------------------------循环日期---------------------------------------
	
	$day_num =  date("t",strtotime($month.'-01'));//计算本月天数   

	for ($i=1;$i<=$day_num;$i++)
	{
		$day_list[$month.'-'.str_pad($i,2,"0",STR_PAD_LEFT)] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
		
	}
	//-----------月份日期-------------------------------------------------------
	$query = $db->query("
	select 
		distinct(date_format(dtime, '%Y-%m')) AS dtime 
	from 
		pay_data	
	where 
		sid = '$sid'
		and success = 1
		and status = 0	
	order by 
		dtime desc,
		pid desc
	");
	while($drs = $db->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}	

	//-----------人数/收入---------------------------------------------------------------------
	
	$pay = $db->fetch_first("
	select 		
		count(distinct(player_id)) as pay_player_count,
		sum(amount) as pay_amount
	from 
		pay_data 
	where 
		sid = '$sid'
		and success = 1
		and status = 0	
		$set_month
	");
	if($pay){
		$pay_player_count = $pay['pay_player_count'];//-----------充值人数
		$pay_amount = $pay['pay_amount'];//-----------收入
	}
	//-----------首充---------------------------------------------------------------------
	

	$query = $db->query("
	select 
		*
	from 
		pay_day_new
	where 
		date_format(pdate, '%Y-%m') = '$month'
		and cid = '$cid'
		and sid = '$sid'
	");


	while($rs = $db->fetch_array($query))
	{	
		$one_array[$rs['pdate']] =  $rs;
	}
	//-----------消费-------------------------------------------------------------
	$query = $pdb->query("
	select 
		sum(if(D.type = 0,A.value,0)) as consume_0,
		sum(if(D.type = 1 and D.id != 35,A.value,0)) as consume_1,
		date_format(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') as data_date
	from 
		player_ingot_change_record A
		left join player_charge_record B on A.player_id = B.player_id
		left join player C on A.player_id = C.id
		left join ingot_change_type D on A.type = D.id
	where 
		date_format(FROM_UNIXTIME(A.change_time), '%Y-%m') = '$month'
		and B.level_up_time > 0
		and C.is_tester = 0
	group by 
		data_date				
	
	"); 		
	while($crs = $pdb->fetch_array($query)){
		$crs['value'] = $crs['consume_0']+$crs['consume_1'];
		$consume[$crs['data_date']] = $crs;
	}
	//-----------充值---------------------------------------------------------------------
	
	$query = $db->query("
	select 
		date_format(dtime, '%Y-%m-%d') as data_date ,
		sum(amount) as amount,
		count(distinct(username)) as people,
		count(*) as payNum
	from 
		pay_data
	where 
		sid = '$sid'
		and success = 1
		and status = 0	
		$set_month
	group by 
		data_date				
	order by 
		dtime desc,
		pid desc
	");

	while($drs = $db->fetch_array($query)){
		$drs['amount'] = round($drs['amount'],2);
		$array[$drs['data_date']] = $drs;
	}
	$pdb->close();
	$db->close();
	include_once template('player_data_pay_data');
}
//--------------------------------------------------------------------------------------------充值记录
function DataPay($type) {
	global $db,$cid,$sid,$server,$adminWebType,$page;
	$usertype = ReqStr('usertype');
	$username = ReqStr('username');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$oid = ReqStr('oid');
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
		$set_user = "and username ='$username'";
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
	$filetime  = filemtime($flie);//文件创建时间
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
	$tollgatenum = $pdb->result($pdb->query("select count(distinct(`lock`)) from mission where type = '$tt'"),0);//总关卡
	if($is_update)
	{
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
	
	}
	$pdb->close();
	
	//-------------------------------------生成缓存文件------------------------------------------------------	
	
	if ($is_update) 
	{
		$str .= '$timesmax='.$timesmax.";\n"; 
		$str .= '$tollgateplayernum='.$tollgateplayernum.";\n"; 
		$str .= '$failedchallengemax='.$failedchallengemax.";\n"; 
		$str .= '$times_array='.var_export($times_array, TRUE).";\n"; 
		$str .= '$failedchallenge_array='.var_export($failedchallenge_array, TRUE).";\n"; 
		$str .= '$list_array='.var_export($list_array, TRUE).";\n"; 
		writetofile($filename,"<?php\r\nif(!defined('IN_UCTIME')) exit('Access Denied');\r\n".$str."\r\n?>",'w',$dir);//写入
	}
	//-------------------------------------------------------------------------------------------		
	
	
	include_once template('player_tollgate');
} 
   
?>