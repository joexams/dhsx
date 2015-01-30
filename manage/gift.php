<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
function  GiftDataAdd() {
	global $db,$adminWebType;
	$type = ReqNum('type');
		
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1");//服务器	
				
	}
	$db->close();
	include_once template('setting_gift_data_add');
}
function  GiftDataEdit() {
	global $db,$adminWebType;
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$id = ReqNum('id');
	if(!$id)
	{	
		showMsg('错误参数！');	
		return;	
	
	}	
	
	$rs = $db->fetch_first("
	select 
		A.*,
		B.name as servers_name
		 
	from 
		gift_data A
		left join servers B on A.sid = B.sid
	where 
		A.id = '$id'
	");	
	if(!$rs)
	{
		showMsg('无此信息！');	
		return;		
	}
	if ($rs['etime'] == '9999-01-01 00:00:00') 
	{
		$rs['etime'] = '9999-01-01 00:00';
	}else{
		$rs['etime'] = date('Y-m-d H:i',strtotime($rs['etime']));
	}
	$rs['stime'] = date('Y-m-d H:i',strtotime($rs['stime']));
	$iquery = $db->query("select * from gift_data_item where gift_data_id = '$id' order by `order` asc,id asc");	
	if($db->num_rows($iquery))
	{		
		while($irs = $db->fetch_array($iquery))
		{
			 $list_array[] = $irs;
			
		}
	}
	$gquery = $db->query("select * from gift_data_gold where gift_data_id = '$id' order by `order` asc,id asc");	
	if($db->num_rows($gquery))
	{		
		while($grs = $db->fetch_array($gquery))
		{
			 $gold_list_array[] = $grs;
			
		}
	}
	
	
	$db->close();
	include_once template('setting_gift_data_edit');
}

function  GiftData() {
	global $db,$adminWebType,$page;
	$type = ReqNum('type');
	$sid = ReqNum('sid');
	
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
		$servers_list = globalDataList('servers',"cid = '$cid'");//服务器	
	}elseif($adminWebType == 'c'){
		global $cid,$company;
		$servers_list = globalDataList('servers',"cid = '$cid' and open = 1");//服务器	
	}
	
	if($cid)
	{
		$set_cid = "and A.cid = '$cid'";
	}
	if($sid)
	{
		$set_sid = "and A.sid = '$sid'";
	}	
	if($type)
	{
		$set_type = "and A.type = '$type'";
	}	

				
	//---------------------------------------------------------------------
	
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		gift_data A
		left join servers B on A.sid = B.sid
	where 
		A.id <> 0
		$set_cid
		$set_sid	
		$set_type	
	"),0); //获得总条数
	if($num){
		$query = $db->query("
		select 
			A.*,
			B.name as servers_name,
			C.name as company_name
		from 
			gift_data A
			left join servers B on A.sid = B.sid
			left join company C on A.cid = C.cid
		where 
			A.id <> 0
			$set_cid
			$set_sid
			$set_type
		order by
			A.etime desc,
			A.id desc 
		limit
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query)){	
			if ($rs['etime'] == '9999-01-01 00:00:00') 
			{
				$rs['etime'] = '9999-01-01 00:00';
			}else{
				$rs['etime'] = date('Y-m-d H:i',strtotime($rs['etime']));
			}
			$rs['stime'] = date('Y-m-d H:i',strtotime($rs['stime']));		
		
			$list_array[] = $rs;
		}
		$list_array_pages=multi($num,$pageNum,$page,$adminWebType.".php?in=setting&action=GiftData&cid=$cid&sid=$sid&type=$type");	
	}	
	$db->close();
	include template('setting_gift_data');
}	
 //--------------------------------------------------------------------------------------------提交发布活动
function  SaveGiftDataAdd() 
{
	global $db,$adminWebID,$adminWebType; 
	$cid = ReqNum('cid');
	$sid = ReqNum('sid');
	$type = ReqNum('type');
	$gift_type = ReqNum('gift_type');
	$gift_id = ReqNum('gift_id');
	$gift_name = ReqStr('gift_name');	
	$ingot = ReqNum('ingot');
	$ingot_rate = ReqStr('ingot_rate');
	if ($ingot_rate=='on') $ingot_rate=1;else $ingot_rate=0;
	$coins = ReqNum('coins');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$name = ReqStr('name');
	$message = ReqStr('message');
	$code_num = ReqNum('code_num');
	$order_limit = ReqNum('order_limit');


	if (!$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}
	
	if (!$type) 
	{
		showMsg('活动类型未输入！');	
		return;		
	}
	if (!$gift_type) 
	{
		showMsg('赠送类型未输入！');	
		return;		
	}	
	if (!$name) 
	{
		showMsg('活动主题未输入！');	
		return;		
	}
	if ($order_limit > 20) 
	{
		showMsg('排名范围不能超过20！');	
		return;		
	}

	$num = $db->result($db->query("select count(*) from gift_data where type = '$type' and sid = '$sid'"),0); //获得总条数
	if ($num) 
	{
		showMsg('您已发布过该类活动了，请先删除旧活动！');	
		return;		
	}		

	$msg = $query = $db->query("
	insert into 
		gift_data
		(`cid`,`sid`,`name`,`ingot`,`ingot_rate`,`coins`,`message`,`type`,`gift_type`,`gift_id`,`gift_name`,`code_num`,`order_limit`,`adminID`,`stime`,`etime`,`ctime`) 
	values 
		('$cid','$sid','$name','$ingot','$ingot_rate','$coins','$message','$type','$gift_type','$gift_id','$gift_name','$code_num','$order_limit','$adminWebID','$stime','$etime',now())
	");
	if ($msg) 
	{
		if ($type == 2) //如果发布的是礼券兑换活动
		{
			$batch_id =  $db->insert_id();
			for($i = 0;$i<$code_num;$i++){
				$code = random(5)."-".random(5)."-".random(5)."-".random(5)."-".random(5);
				if ($sql != '') $sql .= ',';
				$sql .= "(".$batch_id.",'".$code."')";	
			}
			$c = $db->query("insert into code (batch_id,code) values ".$sql."");
		}
	
		$id =  $db->insert_id();
		showMsg('发布成功，进入详细设置！'.$set,'?in=setting&action=GiftDataEdit&cid='.$cid.'&sid='.$sid.'&id='.$id,'','greentext','','n');	
		insertServersAdminData($cid,$sid,0,'发布活动','主题('.$name.')');//插入操作记录
		return;	

	}else{
		showMsg('发布失败！');	
		return;	
	}
	$db->close();			
		
}

 //--------------------------------------------------------------------------------------------修改活动
function  SaveGiftDataEdit() 
{
	global $db,$adminWebID; 
	$sid = ReqNum('sid');
	$id = ReqNum('id');	
	$type = ReqNum('type');
	$gift_type = ReqNum('gift_type');
	$gift_id = ReqNum('gift_id');
	$gift_name = ReqStr('gift_name');
	$ingot = ReqNum('ingot');
	$ingot_rate = ReqStr('ingot_rate');
	if ($ingot_rate=='on') $ingot_rate=1;else $ingot_rate=0;
	$coins = ReqNum('coins');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$name = ReqStr('name');
	$message = ReqStr('message');
	$code_num = ReqNum('code_num');
	$order_limit = ReqNum('order_limit');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	if (!$sid) 
	{
		showMsg('未选择服务器！');	
		return;		
	}
	
	if (!$type) 
	{
		showMsg('活动类型未输入！');	
		return;		
	}
	if (!$gift_type) 
	{
		showMsg('赠送类型未输入！');	
		return;		
	}	
	if (!$name) 
	{
		showMsg('活动主题未输入！');	
		return;		
	}
	if ($order_limit > 20) 
	{
		showMsg('排名范围不能超过20！');	
		return;		
	}	
	if ($id) 
	{
		if ($type == 2) //如果是礼券兑换活动
		{
			if($code_num)//如果有追加
			{	
				$rs = $db->fetch_first("select max(distinct(number)) as number from code where batch_id = '$id'");	//搜索批次
				if($rs['number'])
				{
					$number = $rs['number']+1;	
				}else{
					$number = 1;	
				}
				for($i = 0;$i<$code_num;$i++){
					$code = random(5)."-".random(5)."-".random(5)."-".random(5)."-".random(5);
					if ($sql != '') $sql .= ',';
					$sql .= "(".$id.",".$number.",'".$code."')";	
				}
			
				$c = $db->query("insert into code (batch_id,number,code) values ".$sql."");	
				if ($c) 
				{
					$set_add = ",code_num = code_num+$code_num";
					$show_msg = '[<a href="call.php?action=CallCodeExport&cid='.$cid.'&sid='.$sid.'&batch_id='.$id.'&number='.$number.'&title='.urlencode($title.'('.$number.')').'">导出此次追加的激活码</a>]';
				}else{
					$show_msg = '追加失败！';			
				}
			}
		}





		$db->query("
		update 
			gift_data 
		set 
			gift_type = '$gift_type',
			gift_id = '$gift_id',
			gift_name = '$gift_name',
			ingot = '$ingot',
			ingot_rate = '$ingot_rate',
			coins = '$coins',
			stime = '$stime',
			etime = '$etime',
			name = '$name',
			message = '$message',
			order_limit = '$order_limit'
			$set_add
		where 
			id = '$id'
		");//更新领取次数
		showMsg('操作成功！'.$show_msg,"",'','greentext');	
		insertServersAdminData($cid,$sid,0,'修改活动','主题('.$name.')');//插入操作记录
		return;	

	}else{
		showMsg('操作失败！');	
		return;	
	}
	$db->close();			
		
}
 //--------------------------------------------------------------------------------------------删除活动
function  DelGiftData() 
{
	global $db,$adminWebID,$adminWebType; 
	$id = ReqNum('id');
	$sid = ReqNum('sid');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}
	if (empty($cid) || empty($id))
	{
		showMsg('错误参数！');
		return;		
	}	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		gift_data
	where 
		cid = '$cid' and sid = '$sid' and id = '$id'
		
	"),0);
	if (!$num)
	{
		showMsg('无此信息！');
		return;		
	}	
	$return = $db->query("delete from gift_data where cid = '$cid' and sid = '$sid' and id = '$id'");
	$db->query("delete from gift_data_item where gift_data_id = '$id'");
	$db->query("delete from gift_data_gold where gift_data_id = '$id'");
	//$db->query("delete from code where batch_id = '$id'");
	showMsg('删除成功！',"",'','greentext');			
	insertServersAdminData($cid,$sid,0,'删除活动','('.$id.')');//插入操作记录
	$db->close();			
		
}


//--------------------------------------------------------------------------------------------批量活动物品奖励
function  SetGiftDataItem() 
{
	global $db,$adminWebType; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$number = ReqArray('number');
	$order = ReqArray('order');
	
	$sid = ReqNum('sid');
	$gift_data_id = ReqNum('gift_data_id');
	$item_name = ReqStr('item_name');
	$item_id = ReqNum('item_id');
	$number_n = ReqNum('number_n');
	$order_n = ReqNum('order_n');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from gift_data_item where id in ($id_arr)");
		$msg = "删除成功！";

		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $number[$i])
			{
				$db->query("
				update 
					gift_data_item 
				set 
					`number`='$number[$i]',
					`order`='$order[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($number_n && $item_name && $gift_data_id && $item_id)
	{
	
		$query = $db->query("
		insert into 
			gift_data_item
			(`item_name`,`number`,`gift_data_id`,`item_id`,`order`) 
		values 
			('$item_name','$number_n','$gift_data_id','$item_id','$order_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
			//$add_gift = '增加活动奖励('.$item_name.'-'.$number.')';
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}	
	//$id_up_arr = implode(",",$id);
	//if ($id_del) $del_gift = '删除的奖励物品ID('.$id_arr.')';
	//$contents = '活动奖励:更新的奖励物品ID('.$id_up_arr.')'.$del_gift.$add_gift;
	//insertServersAdminData($cid,$sid,0,'活动奖励',$contents);//插入操作记录		
	$db->close();
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量活动货币奖励
function  SetGiftDataGold() 
{
	global $db,$adminWebType; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$number = ReqArray('number');
	$order = ReqArray('order');
	
	$sid = ReqNum('sid');
	$gift_data_id = ReqNum('gift_data_id');
	$item_name = ReqStr('item_name');
	$type = ReqNum('type');
	$number_n = ReqNum('number_n');
	$order_n = ReqNum('order_n');
	if($adminWebType == 's')
	{
		$cid = ReqNum('cid');
	}elseif($adminWebType == 'c'){
		global $cid;
				
	}

	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from gift_data_gold where id in ($id_arr)");
		$msg = "删除成功！";

		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $number[$i])
			{
				$db->query("
				update 
					gift_data_gold 
				set 
					`number`='$number[$i]',
					`order`='$order[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($number_n && $type && $gift_data_id)
	{
	
		$query = $db->query("
		insert into 
			gift_data_gold
			(`number`,`gift_data_id`,`type`,`order`) 
		values 
			('$number_n','$gift_data_id','$type','$order_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
			//$add_gift = '增加活动奖励('.$item_name.'-'.$number.')';
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}	
	//if ($id) $id_up_arr = implode(",",$id);
	//if ($id_del) $del_gift = '删除的奖励货币ID('.$id_arr.')';
	//$contents = '活动奖励:更新的奖励货币ID('.$id_up_arr.')'.$del_gift.$add_gift;
	//insertServersAdminData($cid,$sid,0,'活动奖励',$contents);//插入操作记录		
	$db->close();
	showMsg($msg,'','','greentext');	
}

?> 