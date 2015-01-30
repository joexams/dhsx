<?php
$ver = isset($argv[1]) ? trim($argv[1]) : null;
if (!$ver) {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once callApiVer($ver);
//------------------------检查没有充成功的订单记录------------------------------------
$query = $db->query("
select 
	A.pid,
	A.cid,
	A.sid,
	A.username,
	A.coins,
	A.oid,
	A.dtime,
	A.vip_level_up,
	B.name,
	B.server_ver,
	B.api_server,
	B.api_port,
	B.api_pwd,
	B.open_date,
	B.is_combined,
	C.name as company_name,
	C.slug
from 
	pay_data A
	left join servers B on A.sid = B.sid 
	left join company C on A.cid = C.cid 
where 
	A.success = 0 
	and A.status = 0 
	and A.dtime < current_timestamp() - 30
	and B.server_ver = '$ver'
order by
	A.dtime desc
");
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		api_base::$SERVER = $rs['api_server'];
		api_base::$PORT   = $rs['api_port'];
		api_base::$ADMIN_PWD   = $rs['api_pwd'];
		
		
		
		$rs['pdate'] = date('Y-m-d',strtotime($rs['dtime']));	
		$player = api_admin::get_nickname_by_username($rs['username']);	


		if ($player['nickname'][1]) {
			$nickname = addslashes($player['nickname'][1]);//取用户游戏呢称
	
			if(!$rs['vip_level_up']) 
			{
				$msg = api_admin::charge($player['player_id'],$rs['oid'],$rs['coins']);//VIP接口累积充值
			}else{
				$msg['result'] = 1;
			}	
		
			$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$rs[cid]' and sid = '$rs[sid]' and player_id = '$player[player_id]' and vip_level_up = 1 and success = 1 and status = 0"),0); //是否首充
	
			if ($msg['result'] == 1) {//充值成功
	
				$db->query("update pay_data set vip_level_up = 1,nickname = '$nickname',ditme_up = now() where  cid= '$rs[cid]' and oid = '$rs[oid]'");//确定VIP等级接口执行后再更新
				$msgingot = api_admin::increase_player_ingot($player['player_id'],$rs['coins']);//加元宝
				if ($msgingot['result'] == 1) {//加元宝成功
					$db->query("update pay_data set success = 1,player_id = '$player[player_id]',ditme_up = now() where cid = '$rs[cid]' and oid = '$rs[oid]'");//确定充元宝也成功后在更新
					
					//------------------------------------------------------------------------------		
					require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
					if (!$isnew)//如果第一次冲
					{
						//------------------------------记录每日新增充值用户数-----------------------------
	
						$d = $db->result($db->query("select count(*) from pay_day_new where cid = '$rs[cid]' and sid = '$rs[sid]' and pdate = '$rs[pdate]'"),0);
						if (!$d)
						{
							$db->query("insert into pay_day_new(cid,sid,pdate,new_player) values ('$rs[cid]','$rs[sid]','$rs[pdate]',1)");
						}else{
							$db->query("update pay_day_new set new_player = new_player+1 where cid = '$rs[cid]' and sid = '$rs[sid]' and pdate = '$rs[pdate]'");
						}
					}
					if(!$rs['is_combined']) {
						$cid=$rs['cid'];
						$sid=$rs['sid'];
						$openid=$rs['username'];
						SetGiftDays3($player['player_id'],$rs['coins'],$rs['open_date']);//非合服，开服前3天充值活动
					}
				}
			}
		}
	}
}
$db->close();


?>