<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
//--------------------------------------------------------------------------------------------数据报表

function Logs() {
	global $db,$cid,$pdb,$sid;
	$type = ReqStr('type');
	if (!$type || $type == 'ingot') 
	{
		LogIngot($type);	
	}elseif($type == 'coins'){
		LogCoins($type);
	}elseif($type == 'item'){
		LogItem($type);
	}elseif($type == 'fate'){
		LogFate($type);
	}elseif($type == 'fame'){
		LogFame($type);
	}elseif($type == 'power'){
		LogPower($type);
	}elseif($type == 'exp'){
		LogExp($type);
	}elseif($type == 'take'){
		LogTake($type);
	}elseif($type == 'faction'){
		LogFaction($type);
	}elseif($type == 'boss'){
		LogBoss($type);
	}elseif($type == 'skill'){
		LogSkill($type);
	}elseif($type == 'farmland'){
		LogFarmland($type);
	}elseif($type == 'flower'){
		LogFlower($type);
	}elseif($type == 'tree'){
		LogTree($type);
	}elseif($type == 'soul'){
		LogSoul($type);
	}elseif($type == 'soul_stone'){
		LogSoulStone($type);
	}
	
}
//--------------------------------------------------------------------------------------------灵石记录

function LogSoulStone($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from soul_stone_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$soul_stone_log_type[] =  $rs;
		$soulstonetype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
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
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_soul_stone_change_record A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				B.type as stone_type,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_soul_stone_change_record A
				left join soul_stone_change_type B on A.type = B.id
				left join player C on A.player_id = C.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=soul_stone&username=$username&usertype=$usertype&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_soul_stone');
}
//-------------------------------------------------------------------------------------------灵件记录

function LogSoul($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from soul_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$soul_log_type[] =  $rs;
		$soultype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
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
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_soul_change_record A 	
			left join player B on A.player_id = B.id
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				B.username,
				B.nickname,
				B.is_tester,
				C.name as soul_name,
				D.name as quality_name
			from 
				player_soul_change_record A
				left join player B on A.player_id = B.id
				left join soul C on A.soul_id = C.id
				left join soul_quality D on C.soul_quality_id = D.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$lidArr .= $rs['soul_attribute_id_location_1'].','.$rs['soul_attribute_id_location_2'].','.$rs['soul_attribute_id_location_3'].',';
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=soul&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
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
	}
	
	$pdb->close();
	include_once template('player_log_soul');
}
//--------------------------------------------------------------------------------------------仙露记录

function LogTree($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from coin_tree_count_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$tree_log_type[] =  $rs;
		$treetype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_coin_tree_count_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_coin_tree_count_log A
				left join player C on A.player_id = C.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=tree&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_tree');
}
//-------------------------------------------------------------------------------------------鲜花记录

function LogFlower($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	$s_usertype = ReqNum('s_usertype') ? ReqNum('s_usertype') : 1;
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.send_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.send_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.send_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.send_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	

	if ($username) 
	{
		//------------------------获取运营商及服务器数据------------------------------------

		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];

	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		
		if ($s_usertype == 1) {
			$set_username = " and A.player_id = '$player_id'";	
		}elseif ($s_usertype == 2) {
			$set_username = " and A.from_player_id = '$player_id'";	
		}

	}			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_flower_count_log A 	
			left join player B on A.player_id = B.id	
		where 
			A.id > 0 
			$set_day 
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				B.username,
				B.nickname,
				B.is_tester,
				C.username as from_username,
				C.nickname as from_nickname,
				C.is_tester as f_is_tester
			from 
				player_flower_count_log A
				left join player B on A.player_id = B.id
				left join player C on A.from_player_id = C.id
			where 
				A.id > 0
				$set_day 
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=flower&username=$username&usertype=$usertype&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}

	
	$pdb->close();
	include_once template('player_log_flower');
}
//-------------------------------------------------------------------------------------------药园记录

function LogFarmland($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

/*	$query = $pdb->query("select * from skill_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		if ($rs['type'] == 1) $skill_log_type_1[] =  $rs;
		if ($rs['type'] == 0) $skill_log_type_0[] =  $rs;
		//$skill_log_type[] =  $rs;
		$skilltype[$rs['id']] = $rs;
	}
*/	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	//$stime = ReqStr('stime');
	//$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
/*	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	*/
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}		
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_farmland_log A 	
			left join player B on A.player_id = B.id	
		where 
			A.id <> 0 
			$set_day 
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				B.username,
				B.nickname,
				B.is_tester,
				C.name as herbs_name,
				E.name as role_name
			from 
				player_farmland_log A
				left join player B on A.player_id = B.id
				left join herbs C on A.herbs_id = C.id
				left join player_role D on A.player_role_id = D.id
				left join role E on D.role_id = E.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=farmland&username=$username&usertype=$usertype&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_farmland');
}
//--------------------------------------------------------------------------------------------阅历记录

function LogSkill($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from skill_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		if ($rs['type'] == 1) $skill_log_type_1[] =  $rs;
		if ($rs['type'] == 0) $skill_log_type_0[] =  $rs;
		//$skill_log_type[] =  $rs;
		$skilltype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_skill_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_skill_log A
				left join player C on A.player_id = C.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=skill&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_skill');
}

//--------------------------------------------------------------------------------------------BOSS战记录

function LogBoss($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$usertype = ReqNum('usertype');
	$username = trim(ReqStr('username'));
	$is_defeat = trim(ReqNum('is_defeat'));
	$date = ReqStr('date');
	if ($date)
	{
		$dateArr = explode('-',$date);
		$set_day = "and year = '$dateArr[0]' and month = '$dateArr[1]' and day = '$dateArr[2]'";
	}	
	if ($is_defeat)
	{
		$set_is_defeat = "and is_defeat = 1";
	}	
	//-----------月份日期-------------------------------------------------------
	$query = $pdb->query("
	select 
		year,
		month,
		day
	from 
		player_defeat_world_boss_record	
	group by 
		year,month,day
	order by 
		year desc,
		month desc,
		day desc
	");
	while($drs = $pdb->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}	

	
	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		$set_username = " and A.player_id = '$player_id'";	
	}

	//			

	$num = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_defeat_world_boss_record A 	
		left join world_boss B on A.world_boss_id = B.id
		left join town C on B.town_id = C.id
	where 
		A.id > 0 
		$set_day
		$set_faction_name
		$set_is_defeat
	"),0);
	if($num)
	{
		$query = $pdb->query("
		select 
			A.*,
			C.name as town_name,
			D.username,
			D.nickname,
			D.is_tester
			
		from 
			player_defeat_world_boss_record A 	
			left join world_boss B on A.world_boss_id = B.id
			left join town C on B.town_id = C.id	
			left join player D on A.player_id = D.id
		where 
			A.id > 0 
			$set_day 
			$set_username 	
			$set_is_defeat
		order by 
			A.id desc 
		limit 
			$start_num,$pageNum
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=boss&username=$username&is_defeat=$is_defeat&cid=$cid&sid=$sid");	
	}

	
	$pdb->close();
	include_once template('player_log_boss');
}


//--------------------------------------------------------------------------------------------帮派战记录

function LogFaction($type) {
	global $pdb,$cid,$sid,$server,$adminWebType,$page;

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$faction_name = trim(ReqStr('faction_name'));
	$date = ReqStr('date');
	if ($date)
	{
		$dateArr = explode('-',$date);
		$set_day = "and year = '$dateArr[0]' and month = '$dateArr[1]' and day = '$dateArr[2]'";
	}	
	//-----------月份日期-------------------------------------------------------
	$query = $pdb->query("
	select 
		year,
		month,
		day
	from 
		player_faction_join_faction_war_record	
	group by 
		year,month,day
	order by 
		year desc,
		month desc,
		day desc
	");
	while($drs = $pdb->fetch_array($query))
	{
		$day_moth_list[]=$drs;
	}	

	
	if ($faction_name) 
	{
		$set_faction_name = " and B.name = '$faction_name'";	
	}

	//			

	$num = $pdb->result($pdb->query("
	select 
		count(*) 
	from 
		player_faction_join_faction_war_record A 	
		left join player_faction B on A.faction_id = B.id
		left join faction_war C on A.faction_war_id = C.id	
	where 
		A.id <> 0 
		$set_day
		$set_faction_name
	"),0);
	if($num)
	{
		$query = $pdb->query("
		select 
			A.*,
			B.member_count,
			B.name as faction_name,
			D.name as town_username
		from 
			player_faction_join_faction_war_record A
			left join player_faction B on A.faction_id = B.id
			left join faction_war C on A.faction_war_id = C.id	
			left join town D on C.town_id = D.id
		where 
			A.id <> 0
			$set_day 
			$set_faction_name 	
		order by 
			A.id desc 
		limit 
			$start_num,$pageNum
		");
		while($rs = $pdb->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['faction_name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=faction&faction_name=$faction_name&cid=$cid&sid=$sid");	
	}

	
	$pdb->close();
	include_once template('player_log_faction');
}
//--------------------------------------------------------------------------------------------经验记录

function LogTake($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from take_bible_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$take_bible_log_type[] =  $rs;
		$taketype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		$set_username = " and A.player_id = '$player_id'";	


		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_take_bible_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester,
				D.nickname as be_username
			from 
				player_take_bible_log A
				left join player C on A.player_id = C.id
				left join player D on A.be_rob_player_id = D.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=take&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_take');
}
//--------------------------------------------------------------------------------------------经验记录

function LogExp($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from exp_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$exp_change_type[] =  $rs;
		$exptype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}		
		$set_username = " and A.player_id = '$player_id'";	


		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_role_exp_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester,
				E.name as role_name
			from 
				player_role_exp_log A
				left join player C on A.player_id = C.id
				left join player_role D on A.player_id = D.id
				left join role E on D.role_id = E.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=exp&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_exp');
}
//--------------------------------------------------------------------------------------------体力记录

function LogPower($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from power_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$power_change_type[] =  $rs;
		$powertype[$rs['id']] = $rs;
	}

	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 

	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];

	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		$set_username = " and A.player_id = '$player_id'";	

		$power = $pdb->fetch_first("
		select 
			sum(if(A.value >= 0,A.value,0)) as value_add,
			sum(if(A.value < 0,A.value,0)) as value_del
		from 
			player_power_log A
		where 
			A.id > 0 
			$set_username
		"); //计算总量

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_power_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id > 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_power_log A
				left join player C on A.player_id = C.id
			where 
				A.id > 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=power&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_power');
}

//--------------------------------------------------------------------------------------------猎命记录

function LogFame($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from fame_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$fame_change_type[] =  $rs;
		$fametype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		$set_username = " and A.player_id = '$player_id'";	

		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_fame_log A 	
			left join player C on A.player_id = C.id	
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_fame_log A
				left join player C on A.player_id = C.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=fame&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_fame');
}

//--------------------------------------------------------------------------------------------铜钱记录

function LogCoins($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from coin_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		if ($rs['type'] == 1) $coins_change_type1_array[] =  $rs;
		if ($rs['type'] == 0) $coins_change_type0_array[] =  $rs;
		$coinstype[$rs['id']] = $rs;
	}




	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$coins = trim(ReqNum('coins'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
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
	if ($coins) 
	{
		$set_coins = " and A.value = '$coins'";	
	}
	if ($username) 
	{
	
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		$set_username = " and A.player_id = '$player_id'";	
		
		
	}	
	if ($coins || $username) 
	{	
		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_coin_change_record A 	
			left join player C on A.player_id = C.id	
		where 
			A.id > 0 
			$set_day
			$set_type
			$set_username
			$set_coins
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester
			from 
				player_coin_change_record A
				left join player C on A.player_id = C.id
			where 
				A.id > 0
				$set_day 
				$set_type
				$set_username 	
				$set_coins		
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=coins&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	$pdb->close();
	include_once template('player_log_coins');
}


function GetFateLeval($experience,$leveArr){ //等级
	$levelArr = array_reverse($leveArr);
	foreach($levelArr as $val)
	{ 
		if($experience >= $val['request_experience'])
		{ 
			return $val['level']; 
		} 
	} 
} 

//--------------------------------------------------------------------------------------------猎命记录

function LogFate($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;

	$query = $pdb->query("select * from fate_log_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		$fate_change_type[] =  $rs;
		$fatetype[$rs['id']] = $rs;
	}
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$fatename = trim(ReqStr('fatename'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	if ($stime && $etime) 
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') >= '$stime' and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') <= '$etime'";
	}elseif($stime && !$etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$stime'";
	}elseif(!$stime && $etime)
	{
		$set_day = "and date_format(FROM_UNIXTIME(A.op_time), '%Y-%m-%d') = '$etime'";
	}	
	
	
	
	if (!$tidArr) $tidArr = ',';

	if (!empty($tidArr) && $tidArr != ',') {
		$tidArrList = array_filter(explode(',',$tidArr));
		$tidArray = implode(',',$tidArrList);

		$set_type = "and A.op_type in ($tidArray)";
	}else{
		$tidArrList = array();
	}
	
	if ($fatename) 
	{
		$set_fate = " and D.name = '$fatename'";	
	}
	
	//-----------------等级表------------------------------------------------------	
	
	$lquery = $pdb->query("select * from fate_quality_level");
	while($lrs = $pdb->fetch_array($lquery))
	{	
		$level[$lrs['fate_quality_id']][] =  $lrs;
	}
	//print_r($level);
	//-----------------------------------------------------------------------	
	
	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];

	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}		
		$set_username = " and A.player_id = '$player_id'";	


		//			

		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_fate_log A 	
			left join player C on A.player_id = C.id	
			left join fate D on A.fate_id = D.id
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
			$set_fate 
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester,
				D.name as fate_name,
				D.fate_quality_id,
				E.name as merge_fate_name
			from 
				player_fate_log A
				left join player C on A.player_id = C.id
				left join fate D on A.fate_id = D.id
				left join fate E on A.merge_fate_id = E.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
				$set_fate		
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$rs['fate_level'] = GetFateLeval($rs['fate_experience'],$level[$rs['fate_quality_id']]);
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=fate&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&fatename=$fatename&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_fate');
}

//--------------------------------------------------------------------------------------------物品记录

function LogItem($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;
	$query = $pdb->query("select * from item_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		if ($rs['type'] == 1) $item_change_type1_array[] =  $rs;
		if ($rs['type'] == 0) $item_change_type0_array[] =  $rs;
		$itemtype[$rs['id']] = $rs;
	}


	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$username = trim(ReqStr('username'));
	$itemname = trim(ReqStr('itemname'));
	$tidArr = trim(ReqStr('tidArr'));
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$usertype = ReqNum('usertype');
	$f_db = ReqNum('f_db');
	if(!$f_db)
	{
		$s_f_db = 'player_item_change_record2';
		$f_db = 0;
	}else{
		$s_f_db  = 'player_item_change_record';
		$f_db = 1;
	}
	
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
	
	if ($itemname) 
	{
		$set_item = " and D.name = '$itemname'";	
	}
	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		
		$set_username = " and A.player_id = '$player_id'";	
	
	
		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			$s_f_db A 	
			left join player C on A.player_id = C.id	
			left join item D on A.item_id = D.id
		where 
			A.id <> 0 
			$set_day
			$set_type
			$set_username
			$set_item 
		"),0);
		if($num)
		{
			$query = $pdb->query("
			select 
				A.*,
				C.username,
				C.nickname,
				C.is_tester,
				D.name as item_name
			from 
				$s_f_db A
				left join player C on A.player_id = C.id
				left join item D on A.item_id = D.id
			where 
				A.id <> 0
				$set_day 
				$set_type
				$set_username 	
				$set_item		
			order by 
				A.id desc 
			limit 
				$start_num,$pageNum
			");
			while($rs = $pdb->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=item&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&itemname=$itemname&f_db=$f_db&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_item');
}
  //--------------------------------------------------------------------------------------------元宝记录

function LogIngot($type) {
	global $db,$pdb,$cid,$sid,$server,$adminWebType,$page;
	
	$query = $pdb->query("select * from ingot_change_type order by id asc ");
	while($rs = $pdb->fetch_array($query))
	{	
		if ($rs['type'] == 1) $ingot_change_type1_array[] =  $rs;
		if ($rs['type'] == 0) $ingot_change_type0_array[] =  $rs;
		$ingottype[$rs['id']] = $rs;
	}
	
	
	
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
	if ($order) 
	{
		$set_order = " A.value desc,";	
	}	


	if ($vip) 
	{

		$set_left = "left join player_charge_record D on A.player_id = D.player_id";
		$set_level = "and D.total_ingot > 0";

	}		

	if ($username) 
	{
		//require_once callApiVer($server['server_ver']);
		//api_base::$SERVER = $server['api_server'];
		//api_base::$PORT   = $server['api_port'];
		//api_base::$ADMIN_PWD   = $server['api_pwd'];
	
		if ($usertype == 1) {
			$player = $pdb->fetch_first("select id from player where username = '$username'"); //查询是否有新手卡活动并且支持自动生成

			//$player = api_admin::find_player_by_username($username);
			$player_id = $player['id'];
		}elseif ($usertype == 2){
			$player = $pdb->fetch_first("select id from player where nickname = '$username'"); //查询是否有新手卡活动并且支持自动生成
			//$player = api_admin::find_player_by_nickname($username);
			$player_id = $player['id'];
			
		}
		$set_username = " and A.player_id = '$player_id'";	

	}else{
		$set_is_tester = " and C.is_tester = 0 ";	
	}

	if ($tidArr != ',' || $username) 
	{

	
		$irs = $pdb->fetch_first("
		select
			sum(if(A.value > 0,A.value,0)) as add_ingot,
			sum(if(A.value < 0,A.value,0)) as del_ingot
	
		from 
			player_ingot_change_record A 	
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
		}
		
		$num = $pdb->result($pdb->query("
		select 
			count(*) 
		from 
			player_ingot_change_record A 	
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
				C.username,
				C.nickname
			from 
				player_ingot_change_record A
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
			$list_array_pages = multi($num,$pageNum,$page,$adminWebType.".php?in=player&action=Logs&type=ingot&username=$username&usertype=$usertype&vip=$vip&tidArr=$tidArr&is_tester=$is_tester&order=$order&stime=$stime&etime=$etime&cid=$cid&sid=$sid");	
		}
	}
	
	$pdb->close();
	include_once template('player_log_ingot');
} 



?>