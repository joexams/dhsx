<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

//--------------------------------------------------------------------------------------------礼包配置
function GiftSetting() {
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$starttime = date('Y-m-d');
	$endtime = date('Y-m-d', time()+30*24*3600);

	$num = $db->result($db->query("select count(*) from gift_setting"),0);
	$query = $db->query("select * from gift_setting order by dateline desc limit $start_num,$pageNum");
	while($rs = $db->fetch_array($query)){
        $items = unserialize($rs['items']);
        $awardlist = $items['awardlist'];
        $itemlist = $items['itemlist'];
        $fatelist = $items['fatelist'];
        $soullist = $items['soullist'];
        foreach ($awardlist as $key => $value) {
        	$list[$value['award_type']] = $value['value'];
        }
        $list['itemlist'] = $itemlist;
        $list['fatelist'] = $fatelist;
        $list['soullist'] = $soullist;
        $list['giftid'] = $rs['giftid'];
        $list['giftname'] = $rs['giftname'];
        $list['gifttype'] = $rs['gifttype'];
        $list['limitnumber'] = $rs['limitnumber'];
        $list['starttime'] = date('Y-m-d', $rs['starttime']);
        $list['endtime'] = date('Y-m-d', $rs['endtime']);
        $list['message'] = $rs['message'];
        $list_array[] = $list;
    }
    $list_array_pages = multi($num,$pageNum,$page,"s.php?in=setting&action=GiftSetting");

    $giftid = ReqNum('giftid');
    $giftarr = array(
    	'itemlist' => array(),
    	'fatelist' => array(),
    	'soullist' => array(),
    	'giftid'   => $giftid, 
    	'giftname'   => '', 
    	'gifttype'   => 0, 
    	'coin'   => 0, 
    	'fame'   => 0, 
    	'skill'   => 0, 
    	'ingot'   => 0, 
    	'message'   => '', 
    	'limitnumber'   => 1, 
    	'starttime' => $starttime,
    	'endtime' => $endtime,
    );
    if ($giftid > 0) {
    	$gift = $db->fetch_first("select * from gift_setting where giftid=$giftid");

    	$items = unserialize($gift['items']);
    	$awardlist = $items['awardlist'];
    	$itemlist = $items['itemlist'];
    	$fatelist = $items['fatelist'];
    	$soullist = $items['soullist'];
    	foreach ($awardlist as $key => $value) {
    		$giftarr[$value['award_type']] = $value['value'];
    	}
    	$giftarr['itemlist'] = $itemlist;
    	$giftarr['fatelist'] = $fatelist;
    	$giftarr['soullist'] = $soullist;
    	$giftarr['giftid'] = $gift['giftid'];
    	$giftarr['giftname'] = $gift['giftname'];
    	$giftarr['gifttype'] = $gift['gifttype'];
    	$giftarr['limitnumber'] = $gift['limitnumber'];
    	$giftarr['starttime'] = date('Y-m-d', $gift['starttime']);
    	$giftarr['endtime'] = date('Y-m-d', $gift['endtime']);
    	$giftarr['message'] = $gift['message'];
    }
	include_once template('setting_cdkey');
}

 //--------------------------------------------------------------------------------------------礼包配置
function SetGiftSetting() {
	global $db, $odb, $adminWebType;
	$giftname = ReqStr('giftname');
	$gifttype = ReqNum('gifttype');
	$message = ReqStr('message');
	$limitnumber = ReqNum('limitnumber');
	$coins = ReqNum('coins');
	$fame = ReqNum('fame');
	$skill = ReqNum('skill');
	$ingot = ReqNum('ingot');
	$starttime = ReqStr('starttime');
	$endtime = ReqStr('endtime');
	$item = isset($_POST['item']) ? $_POST['item'] : array();
	$fate = isset($_POST['fate']) ? $_POST['fate'] : array();
	$soul = isset($_POST['soul']) ? $_POST['soul'] : array();

	$starttime = $starttime ? strtotime($starttime) : time();
	$endtime = $endtime ? strtotime($endtime) : (time() + 30 * 24 * 3600);
	if (empty($giftname)) {
		showMsg('礼包名不能为空');	
		return;	
	}
	if ($limitnumber < 1) {
		showMsg('限制次数不能小于1');	
		return;	
	}
	
	$itemlist = $fatelist = $soullist = array();
	if (!empty($item)) {
		foreach ($item['id'] as $key => $ivalue) {
			if (intval($ivalue) < 1) continue;
			$itemlist[] = array(
				'item_id' => intval($ivalue),
				'level' => intval($item['level'][$key]) > 0 ? intval($item['level'][$key]) : 1,
				'number' => intval($item['number'][$key]) > 0 ? intval($item['number'][$key]) : 1,
			);
		}
	}
	if (!empty($fate)) {
		foreach ($fate['id'] as $key => $fvalue) {
			if (intval($fvalue) < 1) continue;
			$fatelist[] = array(
				'fate_id' => intval($fvalue),
				'level' => intval($fate['level'][$key]) > 0 ? intval($fate['level'][$key]) : 1,
				'number' => intval($fate['number'][$key]) > 0 ? intval($fate['number'][$key]) : 1,
			);
		}
	}
	if (!empty($soul)) {
		foreach ($soul['id'] as $key => $svalue) {
			if (intval($svalue) < 1) continue;
			$soullist[] = array(
				'soul_id' => intval($svalue),
				'number' => intval($soullist['number'][$key]) > 0 ? intval($soullist['number'][$key]) : 1,
			);
		}
	}

	$awardlist = array(
		array('award_type'=>"coin", 'value'=>$coins),
		array('award_type'=>"skill", 'value'=>$skill),
		array('award_type'=>"fame", 'value'=>$fame),
		array('award_type'=>"ingot", 'value'=>$ingot),
	);

	$award = array(
		'awardlist' => $awardlist,
		'itemlist' => $itemlist,
		'fatelist' => $fatelist,
		'soullist' => $soullist,
	);


	$maxid = $db->result($db->query("select max(giftid) from gift_setting"),0);
	if ($maxid == 0) {
		$maxid = 10001;
	}else if ($maxid>0 && $maxid<10000) {
		$maxid = 10000 + $maxid;
	}else {
		$maxid = $maxid + 1;
	}

	$giftid = ReqNum('giftid');

	$items = serialize($award);
	if ($giftid > 0) {
		$ret = $db->query("UPDATE gift_setting SET giftname='$giftname',gifttype='$gifttype',limitnumber='$limitnumber',items='$items',starttime='$starttime',endtime='$endtime',message='$message' WHERE giftid=$giftid");
		$msg = '修改礼包：'.$giftname;
		$maxid = $giftid;
	}else {

		$exists = $db->result($db->query("select count(*) from gift_setting where giftname='$giftname'"),0);
		if ($exists) {
			showMsg('不能重复添加相同的礼包');	
			return;
		}

		$dateline = time();
		$ret = $db->query("INSERT INTO gift_setting(giftid, giftname, gifttype, limitnumber, items, starttime, endtime, message, dateline) VALUES($maxid, '$giftname', '$gifttype', '$limitnumber', '$items', '$starttime', '$endtime', '$message', '$dateline')");
		$msg = '添加礼包：'.$giftname;

		if ($ret) {
			$tablename = 'active_gift_'.$maxid;
			$istable = $odb->result($odb->query("select count(*) from information_schema.tables where table_name = '$tablename'"),0);
			if(!$istable) {
				$table = $odb->query("
				CREATE TABLE `$tablename` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `cid` int(11) unsigned NOT NULL DEFAULT '0',
					  `sid` int(11) unsigned NOT NULL DEFAULT '0',
					  `player_id` int(11) unsigned NOT NULL DEFAULT '0',
					  `username` varchar(50) NOT NULL,
					  `nickname` varchar(50) NOT NULL,
					  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
					  `lastdotime` int(10) unsigned NOT NULL DEFAULT '0',
					  `times` tinyint(3) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `cid_sid_username` (`cid`,`sid`,`username`) USING BTREE,
					  KEY `dateline` (`createtime`),
					  KEY `lastdotime` (`lastdotime`),
					  KEY `times` (`times`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='$giftname';
				");
				if ($table) {
					$msg .= '，创建礼包记录表['.$tablename.']';
				}else {
					$msg .= '，但无法创建礼包记录表['.$tablename.']';
				}
			}
		}
	}
	
	if ($ret) {
		insertServersAdminData(0,0,0,languagevar('SERVER'), $msg.'，礼包ID：'.$maxid);//插入操作记录
		$msg .= ' 成功';
	}else {
		$msg .= ' 失败';
	}
	$db->close();
	showMsg($msg,'','','greentext','','n');	
}


 //--------------------------------------------------------------------------------------------商品广告
function ShopItemAd()
{
	global $db,$adminWebType; 
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}

		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}

	$query = $db->query("select * from servers where test=0 and open=1 and combined_to=0 limit 1");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NULL'));	
		return;	
	}
	//--------------------------------------------------------
	if($server['db_server'] && $server['db_root'] && $server['db_pwd'] && $server['db_name']) 
	{
        $pdb = new mysql(); 
		$pdbhost    = $server['db_server'];
		$pdbuser    = $server['db_root'];
		$pdbpw      = $server['db_pwd'];
		$pdbname    = $server['db_name'];
		$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname);

		$query = $pdb->query("select * from online_shop_advertisement");
		while($rs = $pdb->fetch_array($query)){
			$adlist[] = $rs;
		}
	}
	//--------------------------------------------------------

	include_once template('setting_shop_item_ad');
}
 //--------------------------------------------------------------------------------------------保存商品广告

function SetShopItemAd()
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$adlist = ReqArray('ads');

	$ads  = array();
	foreach ($adlist as $key => $value) {
		$ads[]['ad_id'] = $value;
	}

	$servers = ReqArray('servers');
	if (!$servers)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}
	if (!$ads) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}

	$sidArr =  $servers ? implode(",",$servers) : '';
	$query = $db->query("
	select 
		sid,	
		name,
		server_ver,
		api_server,
		api_port,
		api_pwd
	from 
		servers
	where 
		sid in ($sidArr)
	order by 
		sid asc
	");

	while($srs = $db->fetch_array($query)){
        callapi::load_api_class($srs['server_ver']);
        api_base::$SERVER = $srs['api_server'];
        api_base::$PORT   = $srs['api_port'];
        api_base::$ADMIN_PWD   = $srs['api_pwd'];
        
        $msg = api_admin::change_ad($ads);
        if($msg['result'] == 1) {
            $msg_show .= '<strong>'.$srs['name'].'</strong> - OK!<br />';
        }else{
            $msg_show .= '<strong>'.$srs['name'].'</strong> - ERR!<br />';
        }
    }

    $contents = languagevar('SERVER').'ID:'.$sidArr;
    insertServersAdminData($cid,0,0,languagevar('SERVER'),$contents);//插入操作记录
    $db->close();
    showMsg(languagevar('SETOK').'<br />'.$msg_show,'','','greentext','','n');	
}

 //--------------------------------------------------------------------------------------------设置游戏数据
	
function GameData() 
{
	global $db,$adminWebType; 
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}
	
	
	$db->close();
	include_once template('setting_game_data');
}
 //--------------------------------------------------------------------------------------------赠送
	
function Increase() 
{
	global $db,$adminWebType; 

	$type = ReqStr('type');	
	if(!$type) $type = 'ingot';
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}

		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}
	
	//--------------------------------------------------------------------------------------
	
	
	
	if($type == 'ingot' || !$type)
	{
		webAdmin('increase_ingot');
		$typename = languagevar('SYB');
	}elseif($type == 'delingot'){
		webAdmin('del_ingot');
		$typename = languagevar('KYB');
	}elseif($type == 'coins'){
		webAdmin('increase_coins');
		$typename = languagevar('STQ');
	}elseif($type == 'delcoins'){
		webAdmin('del_coins');
		$typename = languagevar('KTQ');
	}elseif($type == 'item'){
		webAdmin('increase_item');	
		$typename = languagevar('SZB');
		for ($i=1;$i<=SXD_SYSTEM_ITEM_LEVEL;$i++)
		{
			$item_level_list[$i] = $i;
		}			
		
		$call = 'onChange="selectAjax(\'player_call.php?action=callItemType\',\'type_id\',\'sid\',1);$(\'item_id\').options.length = 0;"';
	}elseif($type == 'exp'){	
		webAdmin('increase_exp');
		$typename = languagevar('SJY');
		$u_call = 'onBlur="selectAjax(\'player_call.php?action=callPlayerRole\',\'player_role_id\',\'username|sid|usertype\',1)"';
	}elseif($type == 'mg'){
		webAdmin('increase_mg');
		$typename = languagevar('SMG');
		
		for ($i=1;$i<=10;$i++)
		{
			$fate_level_list[$i] = $i;
		}		
		
		$call = 'onChange="selectAjax(\'player_call.php?action=callFate\',\'fate_id\',\'sid\',1);$(\'fate_id\').options.length = 0;"';
	}elseif($type == 'delmg'){
		webAdmin('del_mg');
		$typename = languagevar('KMG');		
		$u_call = 'onBlur="selectAjax(\'player_call.php?action=callPlayerFateLog\',\'fate_list\',\'username|sid|usertype\')"';
	}elseif($type == 'repute'){
		webAdmin('increase_repute');
		$typename = languagevar('SSW');
	}elseif($type == 'thew'){
		webAdmin('increase_thew');
		$typename = languagevar('STL');
	}elseif($type == 'soul'){
		webAdmin('increase_soul');
		$typename = languagevar('SLJ');
		$call = 'onChange="selectAjax(\'player_call.php?action=callSoul\',\'soul_id\',\'sid\',1);$(\'soul_id\').options.length = 0;"';
		$u_call = 'onBlur="selectAjax(\'player_call.php?action=callPlayerSoulLog\',\'soul_list\',\'username|sid|usertype|soul_id\')"';
	}elseif($type == 'skill'){
		webAdmin('increase_skill');
		$typename = languagevar('SYL');
	}elseif($type == 'point'){
		webAdmin('increase_point');
		$typename = languagevar('SJJD');
	}elseif($type == 'roll'){
		webAdmin('increase_roll');
		$typename = languagevar('SBBCS');
	}elseif($type == 'achievement'){
		webAdmin('increase_achievement');
		$call = 'onChange="selectAjax(\'player_call.php?action=callAchievement\',\'achievement_id\',\'sid\',1);$(\'achievement_id\').options.length = 0;"';
		$typename = languagevar('WJCJ');
	}elseif($type == 'rura'){
		webAdmin('increase_rura');
		$typename = '赠送武魂';
	}elseif($type == 'times'){
		webAdmin('increase_times');
		$typename = '送喂养次数';
	}elseif($type == 'stone'){
		webAdmin('increase_stone');
		$typename = '赠送灵石';
	}
	$db->close();
	include_once template('setting_increase');
}
  //--------------------------------------------------------------------------------------------设置测试号
	
function PlayerTest() 
{
	global $db,$adminWebType;
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}
	
	
	$db->close();
	include_once template('setting_player_test');
}	

  //--------------------------------------------------------------------------------------------设置明星号
	
function PlayerStar() 
{
	global $db,$adminWebType;
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}
	
	
	$db->close();
	include_once template('setting_player_star');
}	

  //--------------------------------------------------------------------------------------------调VIP等级
	
function UpVip() 
{
	global $db,$adminWebType;
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";
		}
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}
	if(!webAdmin('c_test','y') || !webAdmin('s_test','y') || !webAdmin('u_test','y'))//如果有权限
	{	
		$status = 1;
	}
	$db->close();
	include_once template('setting_upvip');
}	


 //--------------------------------------------------------------------------------------------发公告
	
function Bulletin() 
{
	global $db,$adminWebType,$adminWebCid,$adminWebServers;
	$sid = ReqNum('sid');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0","sid desc");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0","sid desc");//服务器	
				
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
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1  and combined_to = 0 $set_sid_arr");//服务器
	}

	if ($sid) 
	{
		$query = $db->query("select * from servers where sid = '$sid'");		
		if($db->num_rows($query))
		{
			$server = $db->fetch_array($query);	
		}else{
			showMsg(languagevar('NULL'));	
			return;	
		}
		//--------------------------------------------------------
		if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
		{

			callapi::load_api_class($server['server_ver']);
			api_base::$SERVER = $server['api_server'];
			api_base::$PORT   = $server['api_port'];
			api_base::$ADMIN_PWD   = $server['api_pwd'];
			
			
			$list_array = api_admin::get_affiche_list();
			if ($list_array)
			{
				foreach($list_array as $rs)
				{
					$bulletin_array = $rs;
				}
			}
		}
		//--------------------------------------------------------
	}
	$db->close();
	include_once template('setting_bulletin');
}

//-----------------------------------踢玩家下线
function PlayerOut() {
	global $db,$adminWebType,$page;

	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid' and combined_to = 0");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0");//服务器	
				
	}elseif($adminWebType == 'u'){
		global $cid,$company,$adminWebServers;
		if ($adminWebServers) 
		{
			$set_sid_arr = " and sid in ($adminWebServers)";			
		}		
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1 and combined_to = 0 $set_sid_arr");//服务器
	}

	
	$db->close();
	include_once template('setting_player_out');
}	

//--------------------------------------------------------------------------------------------保存调VIP等级
function  SaveUpVip() 
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$username = trim(ReqStr('username'));
	$usertype = ReqNum('usertype');
	$vip = ReqNum('vip');
	$is_tester = ReqNum('is_tester');
	$money = ReqNum('money');
	$cause = ReqStr('cause');	
	if(!$sid)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
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

	$query = $db->query("select * from servers where sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}

	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];

		//----------------------帐号不存在--------------------------------------
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}		

		if (!$player['result']) {
			showMsg(languagevar('NOUSER'));
			return;	
		}
		
		//------------------------------------------------------------
		$msg = api_admin::set_player_vip_level($player['player_id'],$vip);
		if ($msg['result'] == 1) {
			if ($money) {//如果选择加元宝才执行
				
				if($server['server_ver'] >= 2011072101){
					$msgingot = api_admin::system_send_ingot($player['player_id'],$money);//顺便加元宝
				}else{
					$msgingot = api_admin::increase_player_ingot($player['player_id'],$money);//顺便加元宝
				}			
				
				if ($msgingot['result'] == 1) {//加元宝成功
					$addingot = ','.languagevar('HDYBCG').$money;
				}else{
					$addingot = ','.languagevar('HDYBSB');
				}
			}else{
				$addingot = ','.languagevar('BJYB');
			}
			if($is_tester)//如果有设置测试号
			{
				
				$msgtest = api_admin::set_tester($player['player_id'], $is_tester);
				if ($msgtest['result'] == 1) {//加元宝成功
					//SetServerTest($cid,$sid,$username,1);
					ReServerTest($cid,$sid);
					$addtest = ','.languagevar('SWCSHCG');
				}else{
					$addtest = ','.languagevar('SWCSHSB');
				}			
				
			}	
			
			$cause = $cause ? $cause : languagevar('WTX');
			$contents = languagevar('VIPSQOKMSG').$vip.$addingot.$addtest.','.languagevar('REASON').':'.$cause.$n;
			insertServersAdminData($cid,$sid,$player['player_id'],$username,$contents);//插入操作记录		
			//showMsg('VIPSQOKMSG','','','greentext','','n');
			//$GLOBALS['vip'] = $vip;
			//$GLOBALS['addingot'] = $addingot;
			////$GLOBALS['addtest'] = $addtest;
			showMsg(langmsg('VIPSQOKMSG').$vip.$addingot.$addtest,'','','greentext','','n');
			//showMsg('VIPSQOKMSG','','','greentext','','n');
			return;	
		}else{
			showMsg(languagevar('SETERR'));
			return;	
		}
	}else{
		showMsg(languagevar('ERROR'));	
	}
	$db->close();

}
//--------------------------------------------------------------------------------------------保存设置测试号
function  SetPlayerTest() 
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$username = trim(ReqStr('username'));
	$is_tester = ReqNum('is_tester');
	$usertype = ReqNum('usertype');
	if(!$sid)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
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
	if($adminWebType != 's')//如果不是开发号
	{
		if($is_tester != 1 &&  $is_tester != 4)//如果不是设置测试号和新手指导员
		{
			if (webAdmin('key_data_set','y')) {//判断有没权限
				showMsg(languagevar('NOPOWER'));	
				return;
			}
		}
	}

	$query = $db->query("select A.*,B.t_player from servers A left join company B on A.cid = B.cid where A.sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}

	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];

		//----------------------帐号不存在--------------------------------------
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}	
		
		
		if (!$player['result']) {
			showMsg(languagevar('NOUSER'));
			return;	
		}
		
		//------------------------------------------------------------
		if($is_tester)
		{
			if($adminWebType != 's')//不是开发号操作才判断
			{
			
				if($is_tester == 4)
				{
					if(CheckServerTEST($sid,4) >= 5)
					{
						showMsg(languagevar('ZDYNUMMSG'));	
						return;						
					}
				}else{
					$test_player = $server['test_player'] ? $server['test_player'] : $server['t_player'];
					if(CheckServerTEST($sid,1) >= $test_player)
					{
						showMsg(languagevar('CSHNUMMSG').$test_player);	
						return;						
					}
				}
			}
		
			$typeName = languagevar('SETCSH');
			$t = 1;
		}else{
			$typeName = languagevar('DELSETCSH');
			$t = 2;
		}	
		if($is_tester == 4)
		{
			$set_player = languagevar('XSZDY');
		}else{
			$set_player = languagevar('CSH');
		}
		$msg = api_admin::set_tester($player['player_id'], $is_tester);
		sleep(1);
		if ($msg['result'] == 1) {
			$contents = $typeName.'('.$username.')'.$set_player.$n;
			insertServersAdminData($cid,$sid,$player['player_id'],$username,$contents);//插入操作记录	
			ReServerTest($cid,$sid);
			showMsg($typeName.languagevar('SETOK'),'','','greentext','','n');
			return;	
		}else{
			showMsg($typeName.languagevar('SETERR'));
			return;	
		}
	}else{
		showMsg(languagevar('ERROR'));	
	}
	$db->close();

}

//--------------------------------------------------------------------------------------------保存设置明星号
function  SetPlayerStar() 
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$username = trim(ReqStr('username'));
	$is_star = ReqNum('is_star');
	$usertype = ReqNum('usertype');
	if(!$sid)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
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


	$query = $db->query("select * from servers  where sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}

	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];

		//----------------------帐号不存在--------------------------------------
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}	
		
		
		if (!$player['result']) {
			showMsg(languagevar('NOUSER'));
			return;	
		}
		
		//------------------------------------------------------------
		if($is_star)
		{
			$typeName = languagevar('SETSTAR');
		}else{
			$typeName = languagevar('DELSTAR');
		}	
		$msg = api_admin::set_star_account($player['player_id'], $is_star);
		//sleep(1);
		if ($msg['result'] == 1) {
			$contents = $typeName.'('.$username.')'.$n;
			insertServersAdminData($cid,$sid,$player['player_id'],$username,$contents);//插入操作记录	
			showMsg($typeName.languagevar('SETOK'),'','','greentext','','n');
			return;	
		}else{
			showMsg($typeName.languagevar('SETERR'));
			return;	
		}
	}else{
		showMsg(languagevar('ERROR'));	
	}
	$db->close();

}
//--------------------------------------------------------------------------------------------检查指导员/测试号数量

function CheckServerTEST($sid,$t) {
	global $db;
	if($t == 4)
	{
		$set_t = " where is_tester = 4 ";
	}else{
		$set_t = " where is_tester = 1 OR is_tester = 2 ";
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
			$num = $pdb->result($pdb->query("select count(*) from player $set_t"),0);
			return $num;
		}
	}
}
//--------------------------------------------------------------------------------------------保存送
function  SaveIncrease() 
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqStr('type');
	$usertype = ReqNum('usertype');
	$username = trim(ReqStr('username'));
	$player_role_id = ReqNum('player_role_id');
	$val = ReqNum('val');
	$cause = ReqStr('cause');	
	
	$item_id = ReqNum('item_id');
	$item_name = ReqStr('item_name');
	$item_level = ReqNum('item_level');
	
	$fateids = ReqArray('fateids');
	$fate_id = ReqNum('fate_id');
	$fate_name = ReqStr('fate_name');
	$fate_level = ReqNum('fate_level');
	
	$soul_id = ReqNum('soul_id');
	$soul_name = ReqStr('soul_name');		
	$soul_a = ReqStr('soul_a');
	
	$achievement_id = ReqStr('achievement_id');
	$achievement_name = ReqStr('achievement_name');
	if(!$sid)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;
	}
	if(!$type)
	{
		showMsg(languagevar('NOCHOOSETYPE'));	
		return;
	}
	if(!$username)
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
	//---------------------------------------------------------------------------------------------
	
	$query = $db->query("
	select 
		server_ver,
		api_server,
		api_pwd,
		api_port
	from 
		servers 
	where 
		sid = '$sid'
	");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
		require_once callApiVer($server['server_ver']);
		
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	//---------------------------------------------------------------------------------------------
	if ($type == 'achievement') 
	{
		$apply = array('id'=>$achievement_id,'name'=>urlencode($achievement_name));
		$apply = json_encode($apply);
	}		
	if ($type == 'item') 
	{
		$apply = array('id'=>$item_id,'name'=>urlencode($item_name),'level'=>$item_level);
		$apply = json_encode($apply);
	}	
	if ($type == 'mg') 
	{
		$apply = array('id'=>$fate_id,'name'=>urlencode($fate_name),'level'=>$fate_level);
		$apply = json_encode($apply);
					
	}		
	if ($type == 'delmg') 
	{
		for($i=0;$i<count($fateids);$i++){
			$arr = explode(",",$fateids[$i]);
			$ids[] = array('player_fate_id' => $arr[0]);
			$names[] = $arr[1];
		}
		$fnames = implode(",",$names);
		$apply = array('ids'=>$ids,'names'=>$fnames);
		$apply = json_encode($apply);
					
	}		
	if ($type == 'soul') 
	{
		if ($soul_a) {
			$soula = explode('|',$soul_a);	
			$soula1 = explode(':',$soula[0]);	
			$soula2 = explode(':',$soula[1]);	
			$soula3 = explode(':',$soula[2]);
			$skey = $soula[3];
			$soul_msg = '('.languagevar('ATTRIBUTE').':'.$soul_a.')';
			$av1 = $soula1[1]*10;
			$av2 = $soula2[1]*10;
			$av3 = $soula3[1]*10;			
			
		}
		
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
	$username = explode(',',$username);	
	if(count($username)>1) $player_role_id = 0;
	for ($i=0;$i<count($username);$i++)
	{
		$msg .= SetIncrease($cid,$sid,$type,$usertype,$username[$i],$apply,$val,$cause.$soul_msg,$server['api_server'],$server['api_pwd'],$server['api_port'],$player_role_id) ;
	}
	showMsg($msg,"",'','greentext','','','n');	
}


//--------------------------------------------------------------------------------------------提交设置游戏数据
function  SaveGameData() 
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqArray('sid');
	$world_boss_id = ReqNum('world_boss_id');
	$boss_openclose = ReqNum('boss_openclose');
	$level = ReqNum('level');
	
	$faction_war_id = ReqNum('faction_war_id');	
	$bp_openclose = ReqNum('bp_openclose');	
	//$open_flag = ReqNum('open_flag');	
	$msg = '';
	if (!$cid || !$sid)
	{
		showMsg('ERROR');	
		return;
	}
	
	
	$sid_arr = implode(",",$sid);
	//---------------------------------------------------------------------------------------------
	
	$query = $db->query("
	select 
		`name`,
		server_ver,
		api_server,
		api_pwd,
		api_port
	from 
		servers 
	where 
		sid in ($sid_arr)
	");		
	if($db->num_rows($query))
	{
		while($server = $db->fetch_array($query)){
		//$server = $db->fetch_array($query);	
			//require_once callApiVer($server['server_ver']);
			callapi::load_api_class($server['server_ver']);
			api_base::$SERVER = $server['api_server'];
			api_base::$PORT   = $server['api_port'];
			api_base::$ADMIN_PWD   = $server['api_pwd'];	
			
			if($world_boss_id && $boss_openclose)
			{
				if($boss_openclose == 1)
				{
					$boss_open_msg = api_admin::open_world_boss($world_boss_id);
					if($boss_open_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-开启世界BOSS成功！<br />';
					}else{
						$msg .= $server['name'].'-开启世界BOSS失败！<br />';
					}
				}else{
					$boss_open_msg = api_admin::close_world_boss($world_boss_id);
					if($boss_open_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-关闭世界BOSS成功！<br />';
					}else{
						$msg .= $server['name'].'-关闭世界BOSS失败！<br />';
					}			
					
					
				}
			}
			if($world_boss_id && $level)
			{
				$level = $level - 30;
				$boss_level_msg = api_admin::set_world_boss_level($world_boss_id,$level);
				if($boss_level_msg['result'] == 1) 
				{
					$msg .= $server['name'].'-设置世界BOSS等级成功！<br />';
				}else{
					$msg .= $server['name'].'-设置世界BOSS等级失败！<br />';
				}		
	
			}		
			
			if($faction_war_id && $bp_openclose)
			{
	
				if($bp_openclose == 1)
				{
					$faction_war_msg = api_admin::open_faction_war($faction_war_id);
					if($faction_war_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-开启帮派战成功！<br />';
					}else{
						$msg .= $server['name'].'-开启帮派战失败！<br />';
					}
				}else{
					$faction_war_msg = api_admin::close_faction_war($faction_war_id);
					if($faction_war_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-关闭帮派战成功！<br />';
					}else{
						$msg .= $server['name'].'-关闭帮派战失败！<br />';
					}			
					
					
				}		
	
			}
			

			if($_POST['open_flag'] != -1 && isset($_POST['control_flag']))
			{
	
				if($_POST['open_flag'] == 1)
				{
					$control_camp_war_msg = api_admin::control_camp_war($_POST['open_flag']);
					if($control_camp_war_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-开启阵营战成功！<br />';
					}else{
						$msg .= $server['name'].'-开启阵营战失败！<br />';
					}
				}else{
					$control_camp_war_msg = api_admin::control_camp_war($_POST['open_flag']);
					if($control_camp_war_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-关闭阵营战成功！<br />';
					}else{
						$msg .= $server['name'].'-关闭阵营战失败！<br />';
					}			
					
					
				}		
	
			}
			if($_POST['control_flag'] != -1 && isset($_POST['control_flag']))
			{
				if($_POST['control_flag'] == 1)
				{
					$control_flag_msg = api_admin::control_beelzebub_trials($_POST['control_flag']);
					if($control_flag_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-开启魔王试炼成功！<br />';
					}else{
						$msg .= $server['name'].'-开启魔王试炼失败！<br />';
					}
				}else{
					$control_flag_msg = api_admin::control_beelzebub_trials($_POST['control_flag']);
					if($control_flag_msg['result'] == 1) 
					{
						$msg .= $server['name'].'-关闭魔王试炼成功！<br />';
					}else{
						$msg .= $server['name'].'-关闭魔王试炼失败！<br />';
					}			
					
					
				}	
	
			}			
			
			unset($SERVER, $PORT, $ADMIN_PWD);
		}		
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	//---------------------------------------------------------------------------------------------
	if($msg){
		showMsg($msg,"",'','greentext','','','n');	
		insertServersAdminData($cid,0,0,languagevar('SERVER'),$msg);//插入操作记录
	}else{
		showMsg('未进行任何操作！',"",'','','','','n');	
	}
	
}
//--------------------------------------------------------------------------------------------保存发布公告
function  SaveBulletin() 
{
	global $db,$adminWebType; 
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	$url = trim(ReqStr('url'));
	$content = trim(ReqStr('content'));
	$pf_id = ReqNum('pf_id') ? intval(ReqNum('pf_id')) : 0;
	$servers = ReqArray('servers');
	$time = ReqStr('time');
	$time = $time ? strtotime($time)  : 0;	
	if ($url) $content = '<a href="'.$url.'" target="_blank">'.$content.'</a>';

	if (!$servers)
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}
	if (!$content) 
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
			

			foreach($servers as $rs => $sid)
			{		
				if(!in_array($sid,$adminWebServersArr))//如果服务器不属于此运客服
				{	
					showMsg(languagevar('NOSERVERPOWERMSG'));	
					return;	
				
				}
			}

		}
	}
	$sidArr =  $servers ? implode(",",$servers) : '';
	$query = $db->query("
	select 
		sid,	
		name,
		server_ver,
		api_server,
		api_port,
		api_pwd
	from 
		servers
	where 
		sid in ($sidArr)
	order by 
		sid asc
	");
	while($srs = $db->fetch_array($query)){
		callapi::load_api_class($srs['server_ver']);
		api_base::$SERVER = $srs['api_server'];
		api_base::$PORT   = $srs['api_port'];
		api_base::$ADMIN_PWD   = $srs['api_pwd'];
	
		$msg = api_admin::add_affiche($content, $pf_id, $time);
		if($msg['result'] == 1) {
			$msg_show .= '<strong>'.$srs['name'].'</strong> - OK!<br />';
		}else{
			$msg_show .= '<strong>'.$srs['name'].'</strong> - ERR!<br />';
		}
	}			
	
	$pf_arr = array(
		0 => '所有平台',
		1 => 'qq空间',
		2 => '朋友网',
		3 => '微博',
		4 => 'q加',
		5 => '财付通',
		6 => 'qq游戏',
		7 => '官网',
		8 => '3366平台',
		9 => '联盟',
	);

	$contents = languagevar('FGG').'('.$pf_arr[$pf_id].'):'.urldecode($content).',('.languagevar('SERVER').'ID:'.$sidArr.')';	
	insertServersAdminData($cid,0,0,languagevar('SERVER'),$contents);//插入操作记录
	$db->close();
	showMsg(languagevar('SETOK').'<br />'.$msg_show,'','','greentext','','n');	

}


//--------------------------------------------------------------------------批量清空公告
function SetDelBulletin()
{
	global $db,$adminWebType; 
	$cid = ReqNum('cid');
	$sid_del = ReqArray('sid_del');
	if (!$sid_del) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
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
	$show = '';
	$sid_arr = implode(",",$sid_del);
	$query = $db->query("select `name`,o_name,server_ver,api_server,api_pwd,api_port from servers where cid = '$cid' and sid in ($sid_arr) and combined_to = 0 and open_date < now() order by sid desc");		
	if($db->num_rows($query))
	{
			
		while($server = $db->fetch_array($query))
		{	
			
			//--------------------------------------------------------
			if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
			{
		
				callapi::load_api_class($server['server_ver']);
				api_base::$SERVER = $server['api_server'];
				api_base::$PORT   = $server['api_port'];
				api_base::$ADMIN_PWD   = $server['api_pwd'];
				
				
				$msg = api_admin::delete_all_affiche();
				//------------------------------------------------------------
				if ($msg['result'] == 1)
				{		
					$show .= $server['name'].'_'.$server['o_name'].' '.languagevar('SETOK').'<br />';	
				}else{
					$show .= '<strong class="redtext">'.$server['name'].'_'.$server['o_name'].' '.languagevar('SETERR').'</strong><br />';
				}
		
			}
		}
	}else{
		$show = languagevar('NOSERVER');
	}
	insertServersAdminData($cid,$sid,0,languagevar('SERVER'),languagevar('DELGG').'SID:'.$sid_arr);//插入操作记录
	$db->close();	
	showMsg($show,'','','greentext','','','n');
} 

//--------------------------------------------------------------------------删除公告
function DelBulletin()
{
	global $db,$adminWebType; 
	$id = ReqNum('id');
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$content = ReqStr('content');
	if (!$id) 
	{
		showMsg(languagevar('ERROR'));	
		return;		
	}

	if (!$sid) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
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
	
	$query = $db->query("select * from servers where sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}
	//--------------------------------------------------------
	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];
		
		
		$msg = api_admin::delete_affiche($id);

		//------------------------------------------------------------
		if ($msg['result'] == 1)
		{		
			insertServersAdminData($cid,$sid,0,languagevar('SERVER'),languagevar('DELGG').'ID:'.$id);//插入操作记录
			showMsg(languagevar('SETOK'),'','','greentext');	
		}else{
			showMsg(languagevar('SETERR'));
		}

	}
	$db->close();	
} 

 //--------------------------------------------------------------------------------------------设置踢玩家下线
function  SetPlayerOut() 
{
	global $db,$adminWebID,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqNum('type');
	$usertype = ReqNum('usertype');
	$username = trim(ReqStr('username'));
	$minute = ReqNum('minute');
	$cause = ReqStr('cause');
	if (!$sid) 
	{
		showMsg(languagevar('NOCHOOSESERVER'));	
		return;		
	}
	if (!$username || !$cause) 
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
	
	$query = $db->query("select * from servers where sid = '$sid'");		
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);	
		
	}else{
		showMsg(languagevar('NOSERVER'));	
		return;	
	}


	if($server['api_server'] && $server['api_pwd'] && $server['api_port']) 
	{

		require_once callApiVer($server['server_ver']);
		api_base::$SERVER = $server['api_server'];
		api_base::$PORT   = $server['api_port'];
		api_base::$ADMIN_PWD   = $server['api_pwd'];

		//----------------------帐号不存在--------------------------------------
		
		if ($usertype == 1) {
			$n = '('.languagevar('USERNAME').')';
			$player = api_admin::find_player_by_username($username);
		}elseif ($usertype == 2){
			$n = '('.languagevar('USERNICK').')';
			$player = api_admin::find_player_by_nickname($username);
		}	

		if (!$player['result']) {	
			showMsg(languagevar('NOUSER'));	
			return;	
		}else{
			$player_id = $player['player_id'];
		}
	}
	
	if(!$minute)
	{
		$t = languagevar('CANCEL');
	}
	if($type == 1)
	{
		$msg = api_admin::disable_player_talk($player_id, $minute*60);
		$typeName = $t.languagevar('BANNED');
	}elseif($type == 2){
		$msg = api_admin::disable_player_login($player_id, $minute*60);
		$typeName = $t.languagevar('FREEZE');
	}
	if($msg['result'] == 1)
	{
		showMsg($typeName.languagevar('SETOK'),"",'','greentext');	
		$contents = languagevar('USERSET').':'.$typeName.','.languagevar('SJXZ').'('.$minute.languagevar('MINUTES').'),'.languagevar('REASON').'('.$cause.')'.$n;
		insertServersAdminData($cid,$sid,$player_id,$username,$contents);//插入操作记录
	}else{
		showMsg($typeName.languagevar('SETERR'));	
	}
	$db->close();			
	
	
}



?> 