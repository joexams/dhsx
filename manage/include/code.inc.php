<?php
if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

//==========================================生成库存兑换券活动===================================================================
# $isall  是否所有平台都可以使用 0=否 1=是
# $repeat  是否允许单个服重复领取 0=不允许 1=允许
# $dayr  是否每天可以领取多个 0=不允许 1=允许
# $total  一共可以领取多少个 0=不允许 >0 允许次数
function code_party_kc($sdate,$edate,$sign,$player_id,$arr,$showmsg,$server=0,$isall=0,$repeat=0,$dayr=1,$total=0){
	global $db,$cid,$sid,$username,$nickname,$code;
	$today = date("Y-m-d 00:00:00");
	$dayr = intval($dayr);
	$total = intval($total);

	if ( strpos($code, 'agnjza') === 0 ) {
		$p_code[0] = 'AGNJZA';
	}else {
		$p_code = explode("_",$code);
	}
	
	$p_db = 'code_party_'.$sign;//数据表
	if($isall == 0){
		$set_cid = " and cid = '$cid'";
	}
	if($p_code[0] == $sign)
	{
		if ($server > 0 and $server != $sid){
			echo 10;
			exit;
		}
		if(date('Y-m-d H:i:s') >= $sdate  &&  date('Y-m-d H:i:s') <= $edate)
		{
			if ($repeat == 0) {//不允许重复
				$p = $db->fetch_first("select * from $p_db where cid = '$cid' and sid = '$sid' and username = '$username'");
			}else{
				if ($total > 0) {
					$num = $db->result($db->query("select count(*) from $p_db where cid = '$cid' and sid = '$sid' and username = '$username'"),0);
					if ($num >= $total) {
						echo 6;
						exit;
					}
					$p = false;
				}else {
					if ($dayr == 0) {//不允许每天重复领取
						$p = $db->fetch_first("select * from $p_db where cid = '$cid' and sid = '$sid' and username = '$username' and ctime >= '$today'");
					}else {
						$num = $db->result($db->query("select count(*) from $p_db where cid = '$cid' and sid = '$sid' and username = '$username' and ctime >= '$today'"),0);
						if ($num >= $dayr) {
							echo 6;
							exit;
						}
						$p = false;
					}
				}
			}

			if (!$p) 
			{
				$c = $db->fetch_first("select * from $p_db where code = '$code' $set_cid");
				if (!$c) //不存在
				{
					echo 7;
					exit();			
				}
				if ($c['player_id'])//已领取 
				{
					echo 8;
					exit();			
				}	
				$msg = $db->query("update $p_db set player_id = '$player_id',username = '$username',nickname = '$nickname',sid = '$sid',cid = '$cid',ctime = now() where code = '$code'");//设置领取
				
				if($msg)
				{
					api_admin::add_player_super_gift($player_id, 16, $arr['ingot'], $arr['coins'], $arr['fame'], $arr['skill'],1252, '获得礼包一个，请于背包内查看！', $arr['item'], $arr['fate'], $arr['soul']);//礼包
						//api_admin::add_player_super_gift($player_id, 16, 0, 0, 0, 0,1213, '获得礼包一个，请于背包内查看！', $arr['item'], $arr['fate'], $arr['soul']);//礼包
					echo '1|'.$showmsg;//成功
					exit();	
				}else{
					echo 0;//异常错误
					exit();		
				}
			}else{
				echo 6;//该玩家已领取过
				exit();
			}
		}
	}
	
}

?>