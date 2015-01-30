<?php
$ver = isset($argv[1]) ? trim($argv[1]) : null;
$e = isset($argv[2]) ? trim($argv[2]) : null;
if (!$ver) {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once callApiVer($ver);
$adminWebType = 's';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><table border="2" cellpadding="4"><tr><td colspan="20" bgcolor="#cccccc"><strong>'.$ver.'</strong></td></tr>';
echo '<tr>
<td><strong>PID</strong></td>
<td><strong>COMPANY</strong></td>
<td><strong>SERVER</strong></td>
<td><strong>USERNAME</strong></td>
<td><strong>UID</strong></td>
<td><strong>NICKNAME</strong></td>
<td><strong>ORDER</strong></td>
<td><strong>COINS</strong></td>
<td><strong>TIME</strong></td>
<td><strong>VIP_UP</strong></td>
<td><strong>STATUS</strong></td>
</tr>';
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
	and B.server_ver = '$ver'
order by
	A.dtime_unix desc
");
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		api_base::$SERVER = $rs['api_server'];
		api_base::$PORT   = $rs['api_port'];
		api_base::$ADMIN_PWD   = $rs['api_pwd'];
		
		
/*		$pdbhost = $rs['db_server'];//数据库服务器
		$pdbuser = $rs['db_root'];//数据库用户名
		$pdbpw = $rs['db_pwd'];//数据库密码
		$pdbname = $rs['db_name'];//数据库名	
		$pdbcharset = 'utf8';//数据库编码,不建议修改.
		$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
		//-----------------------------------------------------------------------------------------------
		$pdb = new mysql();
		$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
		unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
*/		
		
		
		$rs['pdate'] = date('Y-m-d',strtotime($rs['dtime']));	
		$player = api_admin::get_nickname_by_username($rs['username']);	

		$pid_arr = $pid_arr ? $pid_arr.','.$rs['pid'] : $rs['pid'] ;

		if(!$e) 
		{
			$status = '----';
			//echo '<pre>';
			//print_r($rs) ;
			//echo '</pre>';
		}else{
			//$player = $pdb->fetch_first("select id as player_id,nickname from player where username = '$rs[username]'");
			//$player = api_admin::find_player_by_username($rs['username']);
			//if ($player['result']) {
			if ($player['nickname'][1]) {
				//echo $rs['username'].'('.$player['nickname'][1].'|'.$player['player_id'].')<br /> ';
				$nickname = addslashes($player['nickname'][1]);//取用户游戏呢称
		
				if(!$rs['vip_level_up']) 
				{
					$msg = api_admin::charge($player['player_id'],$rs['oid'],$rs['coins']);//VIP接口累积充值
				}else{
					$msg['result'] = 1;
				}	
			
				$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$rs[cid]' and sid = '$rs[sid]' and player_id = '$player[player_id]' and vip_level_up = 1 and success = 1 and status = 0"),0); //是否首充
		
				if ($msg['result'] == 1) {//充值成功
					//$nickname = $msg['nickname'][1];//取用户游戏呢称
		
					$db->query("update pay_data set vip_level_up = 1,nickname = '$nickname',ditme_up = now() where  cid= '$rs[cid]' and oid = '$rs[oid]'");//确定VIP等级接口执行后再更新
					$msgingot = api_admin::increase_player_ingot($player['player_id'],$rs['coins']);//加元宝
					if ($msgingot['result'] == 1) {//加元宝成功
						$db->query("update pay_data set success = 1,player_id = '$player[player_id]',ditme_up = now() where cid = '$rs[cid]' and oid = '$rs[oid]'");//确定充元宝也成功后在更新
						
						//------------------------------------------------------------------------------		
						require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
						if (!$isnew)//如果第一次冲
						{
		
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

						$status = '<font color="green"><strong>OK!</strong></font>';
					}else{
						$status = '<font color="red">ADD INGOT ERR!</font>';
					}
				}else{
					$status = '<font color="red">VIP API ERR!</font>';
				}
				
				
			}else{
				$status = '<font color="red">NO FIND!</font>';
			}
		}
		echo '<tr>
		<td>'.$rs['pid'].'</td>
		<td>'.$rs['company_name'].'</td>
		<td>'.$rs['name'].'</td>
		<td><a href="../s.php?in=player&action=PlayerView&cid='.$rs['cid'].'&sid='.$rs['sid'].'&uid='.$player['player_id'].'" target="_blank">'.$rs['username'].'</a></td>
		<td>'.$player['player_id'].'</td>
		<td>'.$player['nickname'][1].'</td>
		<td>'.$rs['oid'].'</td>
		<td>'.$rs['coins'].'</td>
		<td>'.$rs['dtime'].'</td>
		<td>'.$rs['vip_level_up'].'</td>
		<td>'.$status.'</td>
		</tr>';	
		
	}
	echo '<tr><td colspan="20">'.$pid_arr .'</td></tr>';
	
}else{
	echo '<tr><td colspan="20"><strong>'.$ver.'</strong> ------ NO INFO!</td></tr>';
	//echo '<br /><strong>'.$ver.'</strong> ------ NO INFO!<br />';
}
echo '</table>';
$db->close();


?>