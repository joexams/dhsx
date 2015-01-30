<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
    case 'RunBusinessTown': RunBusinessTown();break;
    case 'RunBusinessItem': RunBusinessItem();break;
    case 'RunBusinessDistance': RunBusinessDistance();break;

    case 'SetRunBusinessTown': SetRunBusinessTown(); break;
    case 'SetRunBusinessItem': SetRunBusinessItem(); break;
    case 'SetRunBusinessDistance': SetRunBusinessDistance(); break;
    default:RunBusinessTown();

}

//--------------------------------------------------------------------------------------------城镇间距离时间表'
function RunBusinessDistance() {
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$rb_town_list = globalDataList('run_business_town');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		run_business_distance
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			run_business_distance
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=runbusiness&action=RunBusinessDistance");	

	}	
	include_once template('t_run_business_distance');
}

//--------------------------------------------------------------------------------------------保存城镇间距离时间表
function SetRunBusinessDistance() {
	global $db;
	global $id_del, $id_old, $from_run_business_town_id, $to_run_business_town_id, $need_time, $left, $right;
	global $from_run_business_town_id_n, $to_run_business_town_id_n, $need_time_n, $left_n, $right_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		foreach ($id_del as $key => $value) {
			$ids = explode('_', $value);
			$db->query("delete from run_business_distance where from_run_business_town_id=".$ids[0]." and to_run_business_town_id=".$ids[1]."");
		}
		
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $from_run_business_town_id[$i])
			{
				$ids = explode('_', $id_old[$i]);
				$db->query("
				update 
					run_business_distance 
				set 
					`from_run_business_town_id`='$from_run_business_town_id[$i]',
					`to_run_business_town_id`='$to_run_business_town_id[$i]',
					`need_time`='$need_time[$i]',
					`left`='$left[$i]',
					`right`='$right[$i]'
				where 
					from_run_business_town_id=".$ids[0]." 
					and to_run_business_town_id=".$ids[1]."
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($from_run_business_town_id_n && $to_run_business_town_id_n)
	{
	
		$query = $db->query("
		insert into 
			run_business_distance
			(`from_run_business_town_id`,`to_run_business_town_id`,`need_time`,`left`,`right`) 
		values 
			('$from_run_business_town_id_n','$to_run_business_town_id_n','$need_time_n','$left_n','$right_n')
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


//--------------------------------------------------------------------------------------------跑商物品价格范围'
function RunBusinessItem() {
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$rb_town_list = globalDataList('run_business_town');
	$item_list = globalDataList('item', 'type_id in (29000)');
	$item_type_list = globalDataList('run_business_item_type');
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		run_business_item
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			run_business_item
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=runbusiness&action=RunBusinessItem");	

	}	
	include_once template('t_run_business_item');
}

//--------------------------------------------------------------------------------------------保存跑商城镇
function SetRunBusinessItem() {
	global $db;
	global $id_del, $id_old, $run_business_town_id, $item_id, $type, $min_price, $max_price, $item_type, $name, $pic_sine, $count;
	global $run_business_town_id_n, $item_id_n, $type_n, $min_price_n, $max_price_n, $count_n, $item_type_n, $name_n, $pic_sine_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		foreach ($id_del as $key => $value) {
			$ids = explode('_', $value);
			$ids[2] = intval($ids[2]);
			$db->query("delete from run_business_item where run_business_town_id=".$ids[0]." and item_id=".$ids[1]." and type=".$ids[2]."");
		}
		
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $run_business_town_id[$i])
			{
				$ids = explode('_', $id_old[$i]);
				$type[$i] = intval($type[$i]);
				$ids[2] = intval($ids[2]);
				$db->query("
				update 
					run_business_item 
				set 
					`run_business_town_id`='$run_business_town_id[$i]',
					`item_id`='$item_id[$i]',
					`type`='$type[$i]',
					`min_price`='$min_price[$i]',
					`count`='$count[$i]',
					`max_price`='$max_price[$i]',
					`item_type`='$item_type[$i]',
					`name`='$name[$i]',
					`pic_sine`='$pic_sine[$i]'
				where 
					run_business_town_id=".$ids[0]." 
					and item_id=".$ids[1]." 
					and type=".$ids[2]."
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($run_business_town_id_n && $item_id_n)
	{
		$type_n = intval($type_n);
		$query = $db->query("
		insert into 
			run_business_item
			(`run_business_town_id`,`item_id`,`type`,`min_price`,`max_price`,`count`,`item_type`,`name`,`pic_sine`) 
		values 
			('$run_business_town_id_n','$item_id_n','$type_n','$min_price_n','$max_price_n','$count_n','$item_type_n','$name_n','$pic_sine_n')
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

//--------------------------------------------------------------------------------------------跑商城镇
function RunBusinessTown() {
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		run_business_town
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			run_business_town
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=runbusiness&action=RunBusinessTown");	

	}	
	include_once template('t_run_business_town');
}

//--------------------------------------------------------------------------------------------保存跑商城镇
function SetRunBusinessTown() {
	global $db;
	global $id_del, $id_old, $sign, $name, $description;
	global $sign_n, $name_n, $description_n;
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from run_business_town where level in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $sign[$i])
			{

				$db->query("
				update 
					run_business_town 
				set 
					`sign`='$sign[$i]',
					`name`='$name[$i]',
					`description`='$description[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($sign_n)
	{
	
		$query = $db->query("
		insert into 
			run_business_town
			(`sign`,`name`,`description`) 
		values 
			('$sign_n','$name_n','$description_n')
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