<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{

	case 'TargetInfo': TargetInfo();break;

	case 'SetTargetInfo': SetTargetInfo();break;
}


function TargetInfo() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','type_id in (1,3,4,5,6,7,1002,10003,17000,22000, 23000)');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		target_info
	"),0);	
	if($num)
	{	
		$query = $db->query("
		select 
			*
		from 
			target_info
		limit 
			$start_num,$pageNum			
		");
	
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=target&action=TargetInfo");			
	}	
	include_once template('t_target_info');
}

function SetTargetInfo() {
	global $db;
	global $id_del, $id_old, $day, $name, $ingot, $sign, $description,$type, $total, $sort_order, $coin, $fame, $skill, $power, $stone, $item_id, $item_amount, $long_yu_ling;
	global $day_n, $name_n, $ingot_n, $sign_n, $description_n,$type_n, $total_n, $sort_order_n, $coin_n, $fame_n, $skill_n, $power_n, $stone_n, $item_id_n, $item_amount_n, $long_yu_ling_n;

	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from target_info where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $name[$i] && $sign[$i])
			{
				$tid = $id_old[$i];
				$db->query("
				update 
					target_info 
				set 
					`day`='$day[$i]',
					`name`='$name[$i]',
					`ingot`='$ingot[$i]',
					`sign`='$sign[$i]',
					`description`='$description[$i]',
					`type`='$type[$tid]',
					`total`='$total[$i]',
					`sort_order`='$sort_order[$i]',
					`coin`='$coin[$i]',
					`fame`='$fame[$i]',
					`skill`='$skill[$i]',
					`power`='$power[$i]',
					`stone`='$stone[$i]',
					`item_id`='$item_id[$i]',
					`long_yu_ling`='$long_yu_ling[$i]',
					`item_amount`='$item_amount[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			target_info
			(day, name, ingot, sign, description,type, total, sort_order, coin, fame, skill, power, stone, item_id, item_amount, long_yu_ling) 
		values 
			('$day_n', '$name_n', '$ingot_n', '$sign_n',' $description_n','$type_n', '$total_n', '$sort_order_n', '$coin_n', '$fame_n', '$skill_n', '$power_n', '$stone_n', '$item_id_n', '$item_amount_n', '$long_yu_ling_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	showMsg($msg,'','','greentext');	
}