<?php
//赠送礼包

if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

function  AddGiftData($player_id,$gift_type,$ingot,$coins,$gift_id,$message,$array) {
	$msg = api_admin::add_player_gift_data($player_id, $gift_type, $ingot,$coins, $gift_id, $message, $array);
	return	$msg;	
}
function  SetGiftDays3($player_id,$pay_ingot,$open_date) {
	global $db,$cid,$sid,$openid;
	$sdate = date('Y-m-d',strtotime($open_date));
	$edate = date("Y-m-d",strtotime($open_date."+2 day"));
	
	if (date('Y-m-d') >= $sdate &&  date('Y-m-d') <= $edate)//如果在活动时间范围内
	{
	
		$ingot = SetGiftIngot($pay_ingot);
		if ($ingot > 0) {
			$msggift = api_admin::add_player_gift_data($player_id, 3, $ingot,0, 0, '您刚充值['.$pay_ingot.']元宝，获得额外赠送['.$ingot.']元宝！您在'.$edate.' 23:59:59前充值满100元宝都能享受到不同额度的元宝赠送哦！', array());//首充送
		
			if($msggift['result'] == 1){
				$show = '开服前3日充值送'.$ingot.'元宝成功！(帐号)';
			}else{
				$show = '开服前3日充值送'.$ingot.'元宝失败！(帐号)';
			}
			insertServersAdminData($cid,$sid,$player_id,$openid,$show,1);//插入操作记录
		}
	}	

	
	
}

function  SetGiftIngot($ingot) {//计算赠送元宝
	if ($ingot >= 100000) {
		$i = $ingot-100000;
		return 13500+SetGiftIngot($i);
	}
	if ($ingot >= 50000) {
		$i = $ingot-50000;
		return 6475+SetGiftIngot($i);
	}
	if ($ingot >= 10000) {
		$i = $ingot-10000;
		return 1175+SetGiftIngot($i);
	}	
	if ($ingot >= 5000) {
		$i = $ingot-5000;
		return 575+SetGiftIngot($i);
	}	
	if ($ingot >= 1000) {
		$i = $ingot-1000;
		return 105+SetGiftIngot($i);
	}	
	if ($ingot >= 100) {
		$i = $ingot-100;
		return 10+SetGiftIngot($i);
	}	

	return 0;
}

function  SetPayPlayer($username,$nickname,$amount,$cid,$sid,$combined_to) {
	global $db;
	$username = addslashes($username);
	$nickname = addslashes($nickname);
	
	if($combined_to) {
		$u = explode(".",$username);
		$hz = '.'.end($u);//后缀
		$c_username = str_replace($hz, "", $username).'.s';				
		$username = str_replace($hz, "", $username);
		$set_c_username = " or username like '$c_username%'";
	}	
	
	
	$time = time();
	$all_amount = $db->result($db->query("select sum(amount) from pay_data where cid = '$cid' and (username = '$username' $set_c_username) and success <> 0 and status <> 1"),0); //统计个人充值总额
	$pay_num = $db->result($db->query("select COUNT(*) from pay_data where cid = '$cid' and (username = '$username' $set_c_username) and success <> 0 and status <> 1"),0); //统计个人充值次数
	$query = $db->query("select distinct(sid) as sid  from pay_data where cid = '$cid' and (username = '$username' $set_c_username) and success <> 0 and status <> 1");
	$sid_arr = '';
	while($srs = $db->fetch_array($query))
	{	
		$sid_arr = $sid_arr ? $sid_arr.','.$srs['sid'] : $srs['sid'];
	}
	//-------------------------------------------------------------------------------------------------------------------------------------
	
	
	$is = $db->fetch_first("
	select 		
		*
	from 
		pay_player 
	where
		cid = '$cid' 
		and username = '$username'
	");

	if ($is)
	{
		$db->query("
		update 
			pay_player 
		set 
			sid = '$sid',
			sid_arr = '$sid_arr',
			amount = '$all_amount',
			last_pay_amount = '$amount',
			last_pay_time = '$time',
			pay_num = $pay_num,
			nickname = '$nickname'
		where 
			cid = '$cid' 
			and username = '$username'
		");	
	}else{
		$db->query("
		insert into pay_player(
			cid,
			sid,
			sid_arr,
			amount,
			last_pay_amount,
			last_pay_time,
			pay_num,
			username,
			nickname
		) values (
			'$cid',
			'$sid',
			'$sid_arr',
			'$all_amount',
			'$amount',
			'$time',
			'$pay_num',
			'$username',
			'$nickname'
		)");
	}
	
}


?>