<?php
	header("Content-Type:text/html;charset=UTF-8");
	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	$yesterday_s = date('Y-m-d 00:00:00');
	$yesterday_e = date('Y-m-d 23:59:59');
	$db->query("update servers set private = 1 where open_date >= '$yesterday_s' and open_date <= '$yesterday_e' and private = 0");//设置今日日开服为公开
	
	$time = time();
	$key = '@admin2_SHEN0_XIAN1_DAO1_^^';
	$chksum= md5("".$time."_".$key."");
	
	
	$query = $db->query("
	select 
		A.cid,
		A.sid,
		A.name,
		A.server,
		A.server_ver,
		A.open_date,
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

			include_once(UCTIME_ROOT."/include/".$rs['locale']."_lang.php");//语言包
			$arr = array(1=>500,2=>300,3=>100);//奖励元宝
	
			//-------------------------------------------------等级------------------------------------------------------------------------------
			
			$url = 'http://'.$rs['server'].'/'.strtolower($rs['name']).'/route.php?m=plt&tn=3&t='.$time.'&chksum='.$chksum.'&mt=0';	////&is_tester=0 不包含测试=1包含.不传默认包含
			echo $url.'<br />';
			$o = file_get_contents($url);
			$o = unserialize($o);
			print_r($o);
			for ($i=1;$i<=3;$i++)
			{

				$contents = str_replace(array("{order}","{obj}"), array($i,$arr[$i].$lang['YB']),$lang['G_LEVEL']);
				echo $o[$i]['player_id'].'|'.$o[$i]['nickname'].'|'.$contents.'<br />';
			}
			unset($o,$url,$i,$contents);
			echo '<br />---------------------------------------------------------------------------------------------------------------------------------------------<br />';
			//---------------------------------------------------副本----------------------------------------------------------------------------

			$url = 'http://'.$rs['server'].'/'.strtolower($rs['name']).'/route.php?m=mt&tn=3&t='.$time.'&chksum='.$chksum.'&mt=0';	////&is_tester=0 不包含测试=1包含.不传默认包含
			echo $url.'<br />';
			$o = file_get_contents($url);
			$o = unserialize($o);
			print_r($o);
			for ($i=1;$i<=3;$i++)
			{
				$contents = str_replace(array("{order}","{mission}","{obj}"), array($i,$o[$i]['mission_name'],$arr[$i].$lang['YB']),$lang['G_MISSION']);
				echo $o[$i]['player_id'].'|'.$o[$i]['nickname'].'|'.$contents.'<br />';
			}
			unset($o,$url,$i,$contents);
			echo '<br />===============================================================================================================================================<br />';
			
		}
	
	}else{
		echo 'NULL';
	}
	
	
	
	$db->close();



?>