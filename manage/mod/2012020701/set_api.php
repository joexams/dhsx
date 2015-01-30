<?php
//赠送礼包

if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

function  AddGiftData($player_id,$gift_type,$ingot,$coins,$gift_id,$message,$array) {
	$msg = api_admin::add_player_gift_data($player_id, $gift_type, $ingot,$coins, $gift_id, $message, $array);
	return	$msg;	
}
function  SetGift($player_id,$type,$pay_ingot) {
	global $db,$cid,$sid,$username,$dtime,$slug;
	
	//---------------------------------------------------------------------------------------------
	if ($slug != 'txwy' && $slug != 'etxwy') 
	{
		$sdate = '2011-09-10';
		$edate = '2011-09-12';
		if(date('Y-m-d') >= $sdate  &&  date('Y-m-d') <= $edate)
		{
			$show_add = '<br />您在'.$edate.' 23:59:59前的所有充值都能享受到10%元宝返还哦！';
		}
	}
	//---------------------------------------------------------------------------------------------
	
	$first_pay_act = $db->result_first("select first_pay_act from servers where cid = '$cid' and sid = '$sid'");
	if($first_pay_act)
	{
	
		//------------------------------------------------------
		$rs = $db->fetch_first("
		select 
			*
		from 
			gift_data
		where 
			type = '$type'
			and `default` = 1
		");	
		if($rs)
		{
			if($rs['ingot_rate'] == 1 && $pay_ingot)//如果是按充值百分比
			{
				$ingot = round($pay_ingot*($rs['ingot']/100));
				if ($ingot < 1) $ingot = 0;//赠送的元宝小于1，则不送
			}else{
				$ingot = $rs['ingot'];
			}
			$coins = $rs['coins'];
			$iquery = $db->query("select * from gift_data_item where gift_data_id = '$rs[id]' order by id asc");	
			if($db->num_rows($iquery))
			{		
				while($irs = $db->fetch_array($iquery))
				{
					 $list_array[] = $irs;
					
				}
			}			
			$array = $list_array ? $list_array : array();
			$msggift = api_admin::add_player_gift_data($player_id, $rs['gift_type'], $ingot,$coins, $rs['gift_id'], $rs['message'].$show_add, $array);//首充送		
			if($msggift['result'] == 1){
				$show = '首充送礼成功！(帐号)';
			}else{
				$show = '首充送礼失败！(帐号)';
			}
			insertServersAdminData($cid,$sid,$player_id,$username,$show,1);//插入操作记录

		}
		
		
		//------------------------------------------------------
	}
}



function  SetGift24($player_id,$pay_ingot) {
	global $db,$cid,$sid,$username;
	$sdate = '2012-09-12';
	$edate = '2012-09-20';
	if(date('Y-m-d') >= $sdate)
	{
		$rs = $db->fetch_first("select min(dtime) as dtime from pay_data where cid = '$cid' and sid = '$sid' and username = '$username'");	
		if ($rs)
		{	
			if (time() <= strtotime($rs['dtime'])+86400 && date('Y-m-d',strtotime($rs['dtime'])) >= $sdate &&  date('Y-m-d',strtotime($rs['dtime'])) <= $edate)//如果在首充24小时内
			{
				$ingot = round($pay_ingot*0.1);
				if ($ingot >= 1)//如果返超过1元宝
				{
					$msggift = api_admin::add_player_gift_data($player_id, 3, $ingot,0, 521, '您刚充值['.$pay_ingot.']元宝，获得返还['.$ingot.']元宝！<br />您在'.date('m月d日H点i分',strtotime($rs['dtime'])+86400).'前的所有充值都能享受到10%元宝返还哦！', array());//首充送		
					if($msggift['result'] == 1){
						$show = '首充24小时内再次充值返还10%成功！(帐号)';
					}else{
						$show = '首充24小时内再次充值返还10%失败！(帐号)';
					}
					insertServersAdminData($cid,$sid,$player_id,$username,$show,1);//插入操作记录
				}
			}
		}
	}
}

function  SetGiftDays($player_id,$pay_ingot,$cid,$sid,$username) {
	global $db;
	$sdate = '2011-09-10';
	$edate = '2011-09-12';
	if (date('Y-m-d') >= $sdate &&  date('Y-m-d') <= $edate)//如果在活动时间范围内
	{
		$ingot = floor($pay_ingot*0.1);
		if ($ingot >= 1)//如果返超过1元宝
		{
		
			$s = $db->fetch_first("select open_date from servers where sid = '$sid' and date_format(open_date, '%Y-%m-%d') >= '$sdate' and date_format(open_date, '%Y-%m-%d') <= '$edate'");
			if ($s) {
		
				$msggift = api_admin::add_player_gift_data($player_id, 3, $ingot,0, 521, '您刚充值['.$pay_ingot.']元宝，获得返还['.$ingot.']元宝！<br />您在'.$edate.' 23:59:59前的所有充值都能享受到10%元宝返还哦！', array());//首充送		
				if($msggift['result'] == 1){
					$show = '中秋活动充值返还10%成功！(帐号)';
				}else{
					$show = '中秋活动充值返还10%失败！(帐号)';
				}
				insertServersAdminData($cid,$sid,$player_id,$username,$show,1);//插入操作记录
			}
		}
	}


}


function  SetGiftMerger($player_id,$pay_ingot,$cid,$sid,$username,$open_date) {
	global $db;
	$sdate = date('Y-m-d',strtotime($open_date));
	$edate = date("Y-m-d",strtotime($open_date."+3 day"));

	if (date('Y-m-d') >= $sdate &&  date('Y-m-d') <= $edate)//如果在活动时间范围内
	{
		$ingot = floor($pay_ingot*0.1);
		if ($ingot >= 1)//如果返超过1元宝
		{
			$msggift = api_admin::add_player_gift_data($player_id, 3, $ingot,0, 521, '您刚充值['.$pay_ingot.']元宝，获得返还['.$ingot.']元宝！<br />您在'.$edate.' 23:59:59前的所有充值都能享受到10%元宝返还哦！', array());//首充送		
			if($msggift['result'] == 1){
				$show = '合服充值返还10%成功！(帐号)';
			}else{
				$show = '合服充值返还10%失败！(帐号)';
			}
			insertServersAdminData($cid,$sid,$player_id,$username,$show,1);//插入操作记录

		}
	}


}

?>