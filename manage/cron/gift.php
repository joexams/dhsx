<?php
	//执行不同版本的活动
	$pass = isset($argv[1]) ? trim($argv[1]) : null;
	if ($pass != 'cron') {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	include_once(dirname(dirname(__FILE__))."/o.config.inc.php");
	$crontime = date('Y-m-d H:i:s');
	$yesterday_s = date('Y-m-d 00:00:00');
	$yesterday_e = date('Y-m-d 23:59:59');
	$db->query("update servers set private = 1 where open_date >= '$yesterday_s' and open_date <= '$yesterday_e' and private = 0");//设置今日日开服为公开
	
	
	
	
	$query = $db->query("
	select 
		A.cid,
		A.sid,
		A.name,
		A.server,
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.open_date,
		A.level_act,
		A.mission_act,
		A.repute_act,
		B.locale
	from 
		servers A
		left join company B on A.cid = B.cid 
	where 
		A.open_date <> ''
		and A.open_date < now()
		and DATE_ADD(A.open_date, INTERVAL 3 DAY) > DATE_ADD(now(), INTERVAL 1 DAY_HOUR)
		and (A.level_act = 1 or A.mission_act = 1)
		and A.open = 1
		and A.is_combined = 0
	order by
		A.open_date asc,
		A.sid asc
	");	
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{
			//$ccc = "【SID:".$rs['sid']."|".$rs['name']."|".$rs['open_date']."】\n";
			if($rs['api_server'] && $rs['api_port'] && $rs['api_pwd'])
			{
				callapi::load_api_class($rs['server_ver']);
				api_base::$SERVER = $rs['api_server'];
				api_base::$PORT   = $rs['api_port'];
				api_base::$ADMIN_PWD   = $rs['api_pwd'];
				include_once(UCTIME_ROOT."/include/".$rs['locale']."_lang.php");//语言包
				$arr = array(1=>500,2=>300,3=>100);//奖励元宝
		
				//-------------------------------------------------等级------------------------------------------------------------------------------
				$o = api_admin::get_player_level_ranking(3);
				$o = unserialize($o);
				print_r($o);
				for ($i=1;$i<=3;$i++)
				{
					if($o[$i]['player_id']){
	
						$contents = str_replace(array("{order}","{obj}"), array($i,$arr[$i].$lang['YB']),$lang['G_LEVEL']);
						$msg = api_admin::add_player_gift_data($o[$i]['player_id'], 16, $arr[$i],0, 0, $contents, array());
						$m = $msg['result'] ? languagevar('SUCCE') : languagevar('FAIL');
						insertServersAdminData($rs['cid'],$rs['sid'],$o[$i]['player_id'],addslashes($o[$i]['nickname']),languagevar('HDJL').':'.$contents.'('.languagevar('USERNICK').')('.$m.')',1);//插入操作记录	
						
						$odb->query("INSERT INTO ho_sys_log_activity(cid,sid,playerid,playername,playernickname,`key`,content,crontime,dateline) VALUES(".$rs['cid'].", ".$rs['sid'].", ".$o[$i]['player_id'].",'".$o[$i]['username']."',".addslashes($o[$i]['nickname']).",'LevelRanking', '".languagevar('HDJL').":".$contents."(".languagevar('USERNICK').")(".$m.")', '$crontime',".time().");");
					}
				}
				unset($o,$i,$m,$msg);
				
				//---------------------------------------------------副本----------------------------------------------------------------------------
	
				$o = api_admin::get_player_mission_ranking(3);
				$o = unserialize($o);
				print_r($o);
				for ($i=1;$i<=3;$i++)
				{
					if($o[$i]['player_id']){
						$contents = str_replace(array("{order}","{mission}","{obj}"), array($i,$o[$i]['mission_name'],$arr[$i].$lang['YB']),$lang['G_MISSION']);
						$msg = api_admin::add_player_gift_data($o[$i]['player_id'], 16, $arr[$i],0, 0, $contents, array());
						$m = $msg['result'] ? languagevar('SUCCE') : languagevar('FAIL');
						insertServersAdminData($rs['cid'],$rs['sid'],$o[$i]['player_id'],addslashes($o[$i]['nickname']),languagevar('HDJL').':'.$contents.'('.languagevar('USERNICK').')('.$m.')',1);//插入操作记录	

						$odb->query("INSERT INTO ho_sys_log_activity(cid,sid,playerid,playername,playernickname,`key`,content,crontime,dateline) VALUES(".$rs['cid'].", ".$rs['sid'].", ".$o[$i]['player_id'].",'".(isset($o[$i]['username'])?$o[$i]['username']:'')."','".addslashes($o[$i]['nickname'])."','MissionRanking', '".languagevar('HDJL').":".$contents."(".languagevar('USERNICK').")(".$m.")', '$crontime',".time().");");
					}	
				}
				unset($o,$i,$m,$msg);
				//-------------------------------------------------------------------------------------------------------------------------------
						
	
			}
			
		}
	
	}	
	
	
	
$db->close();
	$odb->close();



?>