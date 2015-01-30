<?php
//赠送礼包

if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}



function  SetGiftFirstPay($player_id,$cid,$sid,$username) {
	global $db;

	$type  = 29;
	$gift_id = 521;

	$ingot = 0;
	$coins = 100000;
	$fame  = 100;
	$skill = 100;
	$message   = '恭喜您获得首充大礼包！';
	//包子1217  仙石1322  丹药380 道符1329 金刚石1445
	$item_list = array(
		array(
			'item_id' => 1217,
			'level'	  => 1,
			'number'  => 2
		),
		array(
            'item_id' => 1322,
			'level'	  => 1,
			'number'  => 4
		),
		array(
			'item_id' => 380,
			'level'	  => 1,
			'number'  => 3
		),
		array(
			'item_id' => 1329,
			'level'	  => 1,
			'number'  => 4
		),
		array(
			'item_id' => 1445,
			'level'	  => 1,
			'number'  => 5
		),
	);
	$fate_list = array(); 
	$soul_list = array();
	
	$ret = 0;
	$msggift = api_admin::add_player_super_gift($player_id, $type, $ingot, $coins, $fame, $skill, $gift_id, $message, $item_list, $fate_list, $soul_list);//首充送		
	if($msggift['result'] == 1){
		$show = '首充送礼成功！(帐号)';
		$ret = 1;
	}else{
		$show = '首充送礼失败！(帐号)';
	}
	insertServersAdminData($cid,$sid,$player_id,$username,$show,1);//插入操作记录
	return $ret;
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
	$time = time();
	
	if($combined_to) {
		$u = explode(".",$username);
		$hz = '.'.end($u);//后缀
		$username = str_replace($hz, "", $username);
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

	if (!$is)
	{
		$db->query("
		insert into pay_player(
			cid,
			sid,
			username,
			nickname
		) values (
			'$cid',
			'$sid',
			'$username',
			'$nickname'
		)");
	}
	SetReplyPayPlayer($username,$cid,0);//重计
}


function  SetAddRole($stime,$etime,$player_id,$openid,$pay_ingot,$role_id,$ingot) {
	global $db,$cid,$sid;
	if(date('Y-m-d H:i:s') >= $stime &&  date('Y-m-d H:i:s') <= $etime){
		if ($pay_ingot >= $ingot) {
			$msg = api_admin::add_new_role ($player_id, $role_id);
			if($msg['result'] == 1){
				$show = '赠送伙伴成功！(帐号)';
			}else{
				$show = '赠送伙伴失败！(帐号)';
			}
			insertServersAdminData($cid,$sid,$player_id,$openid,$show,1);//插入操作记录
			
		}
	}
}
?>