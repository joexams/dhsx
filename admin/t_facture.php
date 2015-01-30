<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'FactureReelStuff': FactureReelStuff();break;	


	case 'SetFactureReelProduct': SetFactureReelProduct();break;
	case 'SetFactureReelStuff': SetFactureReelStuff();break;
	default:  FactureReelProduct();
}

//--------------------------------------------------------------------------------------------炼成丹药/装备表


function  FactureReelProduct() 
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$tid = ReqNum('tid');
	if(!$tid){
		$tid = 1001;
	}	
	
	
	
	$item_type_list = globalDataList('item_type','id in (1001,1002)');//炼丹/炼器
	$reel_list = globalDataList('item',"type_id = '$tid'",'require_level desc,type_id asc');//炼丹/炼器
	$item_list = globalDataList('item','type_id in (1,2,3,4,5,6,7,11001,11002,11003)','require_level desc,type_id asc');//物品
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		facture_reel_product A
		left join item B on A.reel_id = B.id
		left join item C on A.item_id = C.id		
	where 
		B.type_id = '$tid'		
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.name as reel_name,
			C.name as item_name,
			C.require_level,
			C.type_id
		from 
			facture_reel_product A
			left join item B on A.reel_id = B.id
			left join item C on A.item_id = C.id
		where 
			B.type_id = '$tid'
		order by 
			A.reel_id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=facture&action=FactureReelProduct&tid=$tid");	
	}	
	include_once template('t_facture_reel_product');
}

//--------------------------------------------------------------------------------------------制作材料方法表


function  FactureReelStuff() 
{
	global $db; 
	$reel_id = ReqNum('reel_id');
	$reel_list_1 = globalDataList('item','type_id = 1001','id asc');//炼丹
	$reel_list_2 = globalDataList('item','type_id = 1002','id asc');//炼器
	if(!$reel_id){
		$reel_id = $db->result_first("select min(id) from item where type_id in (1001,1002)");
	}	
	$item_list = globalDataList('item','type_id in (1,2,3,4,5,6,10003)','require_level desc,type_id asc');//物品
	
	
	//--------------------MISSION----------------------------------------

	
	$query = $db->query("
	select 
		A.id,
		A.lock,
		A.name as mission_name,
		B.town_id,
		C.name as town_name
	from 
		mission A
		left join mission_section B on A.mission_section_id = B.id
		left join town C on B.town_id = C.id
	order by
		B.town_id asc,
		A.lock asc,
		A.id asc
	");
	$i = 1;
	while($mrs = $db->fetch_array($query))
	{	
		$mrs['i'] = $i++;
		$mission_list[] =  $mrs;
	}	
	
	
	
	//------------------------------------------------------------
		
	$query = $db->query("
	select 
		A.*,
		B.name as item_name,
		B.require_level,
		B.type_id
	from 
		facture_reel_stuff A
		left join item B on A.item_id = B.id
	where 
		A.reel_id = '$reel_id'			
	order by 
		A.item_id asc		
	");
	if($db->num_rows($query))
	{	
		$i = 0;
		while($rs = $db->fetch_array($query))
		{	
			$rs['item_mission_id_array'] = explode(',',$rs['mission_id']);			
			$rs['i'] = $i++;
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_facture_reel_stuff');
}


//--------------------------------------------------------------------------------------------批量设置炼成丹药/装备表
function  SetFactureReelProduct() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$item_id = ReqArray('item_id');
	$item_number = ReqArray('item_number');
	$ingot = ReqArray('ingot');

	$reel_id_n = ReqNum('reel_id_n');
	$item_id_n = ReqNum('item_id_n');
	$item_number_n = ReqNum('item_number_n');
	$ingot_n = ReqNum('ingot_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $item_id[$i] && $item_number[$i])
			{
				$db->query("
				update 
					facture_reel_product 
				set 
					`item_number`='$item_number[$i]',
					`ingot`='$ingot[$i]'
				where 
					reel_id = '$id[$i]'
					and item_id = '$item_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($reel_id_n && $item_id_n && $item_number_n)
	{
		$query = $db->query("
		insert into 
			facture_reel_product
			(`reel_id`,`item_id`,`item_number`,`ingot`) 
		values 
			('$reel_id_n','$item_id_n','$item_number_n','$ingot_n')
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
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from facture_reel_product where reel_id = '$idArr[0]' and item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置制作材料方法表
function  SetFactureReelStuff() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$item_id = ReqArray('item_id');
	$item_number = ReqArray('item_number');
	$item_description = ReqArray('item_description');

	$reel_id_n = ReqNum('reel_id_n');
	$item_id_n = ReqNum('item_id_n');
	$item_number_n = ReqNum('item_number_n');
	$item_description_n = ReqStr('item_description_n');
	$item_mission_id_n = ReqArray('item_mission_id_n');
	$item_mission_id_n = !empty($item_mission_id_n) ? implode(",",$item_mission_id_n) : 0;//组合为字符串
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $item_id[$i] && $item_number[$i])
			{
				$item_mission_id = ReqArray('item_mission_id_'.$i);
				$item_mission_id = !empty($item_mission_id) ? implode(",",$item_mission_id) : 0;//组合为字符串
				$db->query("
				update 
					facture_reel_stuff 
				set 
					`item_number`='$item_number[$i]',
					`item_description`='$item_description[$i]',
					`mission_id`='$item_mission_id'
				where 
					reel_id = '$id[$i]'
					and item_id = '$item_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($reel_id_n && $item_id_n && $item_number_n)
	{
		$query = $db->query("
		insert into 
			facture_reel_stuff
			(`reel_id`,`item_id`,`item_number`,`item_description`,`mission_id`) 
		values 
			('$reel_id_n','$item_id_n','$item_number_n','$item_description_n','$item_mission_id_n')
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
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from facture_reel_stuff where reel_id = '$idArr[0]' and item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

?>