<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'ItemToSoul': ItemToSoul();break;
	case 'ItemCardJob': ItemCardJob();break;
	case 'ItemType': ItemType();break;
	case 'ItemIngot': ItemIngot();break;
	case 'ItemPrice': ItemPrice();break;
	case 'ItemQuality': ItemQuality();break;
	case 'ItemUpgrade': ItemUpgrade();break;
	case 'ItemPackGrid': ItemPackGrid();break;	
	case 'ItemUpgradePrice': ItemUpgradePrice();break;	
	case 'SuperGift': SuperGift();break;	
	case 'SkillBook': SkillBook(); break;
	case 'EnhanceItem': EnhanceItem(); break;
	case 'OnlineShopItem': OnlineShopItem(); break;
	case 'OnlineShopAdvertisement': OnlineShopAdvertisement(); break;
	case 'MysteriousShopItem': MysteriousShopItem(); break;

	case 'SetItem': SetItem();break;
	case 'SetItemToSoul': SetItemToSoul();break;
	case 'SetItemCardJob': SetItemCardJob();break;
	case 'SetItemType': SetItemType();break;
	case 'SetItemPrice': SetItemPrice();break;
	case 'SetItemQuality': SetItemQuality();break;	
	case 'SetItemIngot': SetItemIngot();break;	
	case 'SetItemUpgrade': SetItemUpgrade();break;	
	case 'SetItemPackGrid': SetItemPackGrid();break;	
	case 'SetItemEquipJob': SetItemEquipJob();break;
	case 'SetItemUpgradePrice': SetItemUpgradePrice();break;
	
	case 'SetAvatarItemMonster': SetAvatarItemMonster();break;
	case 'SetSuperGift': SetSuperGift();break;
	case 'SetSkillBook': SetSkillBook(); break;
	case 'SetEnhanceItem': SetEnhanceItem(); break;
	case 'SetOnlineShopItem': SetOnlineShopItem(); break;
	case 'SetOnlineShopAdvertisement': SetOnlineShopAdvertisement(); break;
	case 'SetMysteriousShopItem': SetMysteriousShopItem(); break;

	case 'ItemMaterialPackage': ItemMaterialPackage(); break;
	case 'SetItemMaterialPackage': SetItemMaterialPackage(); break;
	
	case 'FlowerSwap': FlowerSwap(); break;
	case 'SetFlowerSwap': SetFlowerSwap(); break;

	case 'YellowBlueJunItems': YellowBlueJunItems(); break;
	case 'SetYellowBlueJunItems': SetYellowBlueJunItems(); break;

	case 'FeatsItem': FeatsItem(); break;
	case 'SetFeatsItem': SetFeatsItem(); break;

	case 'FeatsLv': FeatsLv(); break;
	case 'SetFeatsLv': SetFeatsLv(); break;

	default:  Item();
}

//---------------------------------------------------------------功勋等级
function FeatsLv()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*)
	from 
		feats_lv
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			feats_lv
		order by 
			feats_lv asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=FeatsLv");
	}	
	include_once template('t_feats_lv');
}

function SetFeatsLv()
{
	global $db; 
	global $id, $id_del, $id_arr, $feats, $feats_lv;
	global $feats_n, $feats_lv_n;
	//-----------------更新-------------------------------------------
	if ($id)
	{
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					feats_lv 
				set 
					`feats`=$feats[$i],
					`feats_lv`=$feats_lv[$i]
				where 
					feats_lv = $id[$i]
				");
			}
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($feats_lv_n)
	{
		$query = $db->query("
		insert into 
			feats_lv
			(
			`feats`,
			`feats_lv`
			) 
		values 
			(
			'$feats_n',
			'$feats_lv_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from feats_lv where feats_lv in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	showMsg($msg,'','','greentext');
}



//---------------------------------------------------------------功勋物品表
function FeatsItem()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','','','`id`,`name`,`sign`');//装备类型
//	$tatter_list = globalDataList('god_partner_tatter','','','`id`,`name`,`sign`');//碎片列表
//	$list = array_merge($item_list,$tatter_list);

	foreach ($item_list as $key => $value) {
		$item_list[$key]['sign'] = ucfirst(substr($value['sign'], 0, 1));
	}
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		feats_item
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			feats_item
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=FeatsItem");				
	}	
	include_once template('t_feats_item');
}


function SetFeatsItem()
{
	global $db; 
	global $id, $id_del, $id_arr, $item_id, $value, $feats, $feats_lv, $award_type;
	global $item_id_n, $value_n, $feats_n, $feats_lv_n, $award_type_n;
	//-----------------更新-------------------------------------------
	if ($id)
	{
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					feats_item 
				set 
					`item_id`='$item_id[$i]',
					`value`=$value[$i],
					`feats`=$feats[$i],
					`feats_lv`=$feats_lv[$i],
					`award_type`=$award_type[$i]
				where 
					id = $id[$i]
				");
			}
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
		$query = $db->query("
		insert into 
			feats_item
			(
			`item_id`,
			`value`,
			`feats`,
			`feats_lv`,
			`award_type`
			) 
		values 
			(
			'$item_id_n',
			'$value_n',
			'$feats_n',
			'$feats_lv_n',
			'$award_type_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from feats_item where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


function YellowBlueJunItems()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item');//装备类型

	foreach ($item_list as $key => $value) {
		$item_list[$key]['sign'] = ucfirst(substr($value['sign'], 0, 1));
	}
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		yellow_blue_jun_items
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			yellow_blue_jun_items
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=YellowBlueJunItems");				
	}	
	include_once template('t_yellow_blue_jun_items');
}

function SetYellowBlueJunItems()
{
	global $db; 
	global $id, $id_del, $id_arr, $fame, $state_point, $skill, $ingot, $coin, $stone, $items_id, $items_count;
	global $fame_n, $state_point_n, $skill_n, $ingot_n, $coin_n, $stone_n, $items_id_n, $items_count_n;
	//-----------------更新-------------------------------------------
	if ($id)
	{
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					yellow_blue_jun_items 
				set 
					`fame`='$fame[$i]',
					`state_point`=$state_point[$i],
					`skill`=$skill[$i],
					`ingot`=$ingot[$i],
					`coin`=$coin[$i],
					`stone`=$stone[$i],
					`items_id`=$items_id[$i],
					`items_count`=$items_count[$i]
				where 
					id = $id[$i]
				");
			}
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($fame_n > 0 || $state_point_n > 0 || $skill_n > 0 || $coin_n > 0 || $ingot_n > 0 || $stone_n > 0 || $items_id_n > 0)
	{
		$query = $db->query("
		insert into 
			yellow_blue_jun_items
			(
			`fame`,
			`state_point`,
			`skill`,
			`ingot`,
			`coin`,
			`stone`,
			`items_id`,
			`items_count`
			) 
		values 
			(
			'$fame_n',
			'$state_point_n',
			'$skill_n',
			'$ingot_n',
			'$coin_n',
			'$stone_n',
			'$items_id_n',
			'$items_count_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from yellow_blue_jun_items where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


function FlowerSwap()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item');//装备类型
	$game_function = globalDataList('game_function');//功能
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		flower_swap
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			flower_swap
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=FlowerSwap");				
	}	
	include_once template('t_flower_swap');
}

function SetFlowerSwap()
{
	global $db; 
	global $id, $id_del, $id_arr, $award_coin, $award_skill, $award_power, $award_fame, $award_xian_ling, $award_state_point, $award_stone, $award_item, $award_item_count, $need_flower, $need_ingot,$award_fate_scrap, $framerate, $game_function;
	global $award_coin_n, $award_skill_n, $award_power_n, $award_fame_n, $award_xian_ling_n, $award_state_point_n, $award_stone_n, $award_item_n, $award_item_count_n, $need_flower_n, $need_ingot_n, $award_fate_scrap_n, $framerate_n, $game_function_n;
	//-----------------更新-------------------------------------------
	if ($id)
	{
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					flower_swap 
				set 
					`award_coin`='$award_coin[$i]',
					`award_skill`=$award_skill[$i],
					`award_power`=$award_power[$i],
					`award_fame`=$award_fame[$i],
					`award_xian_ling`=$award_xian_ling[$i],
					`award_state_point`=$award_state_point[$i],
					`award_stone`=$award_stone[$i],
					`award_item`=$award_item[$i],
					`award_item_count`=$award_item_count[$i],
					`need_flower`=$need_flower[$i],
					`award_fate_scrap`=$award_fate_scrap[$i],
					`framerate`=$framerate[$i],
					`game_function`=$game_function[$i],
					`need_ingot`=$need_ingot[$i]
				where 
					id = $id[$i]
				");
			}
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($award_coin_n > 0 || $award_skill_n > 0 || $award_power_n > 0 || $award_fame_n > 0 || $award_xian_ling_n > 0 || $award_item_n > 0 || $framerate_n > 0 || $game_function_n > 0)
	{
		$query = $db->query("
		insert into 
			flower_swap
			(
			`award_coin`,
			`award_skill`,
			`award_power`,
			`award_fame`,
			`award_xian_ling`,
			`award_state_point`,
			`award_stone`,
			`award_item`,
			`award_item_count`,
			`need_flower`,
			`award_fate_scrap`,
			`framerate`,
			`game_function`,
			`need_ingot`
			) 
		values 
			(
			'$award_coin_n',
			'$award_skill_n',
			'$award_power_n',
			'$award_fame_n',
			'$award_xian_ling_n',
			'$award_state_point_n',
			'$award_stone_n',
			'$award_item_n',
			'$award_item_count_n',
			'$need_flower_n',
			'$award_fate_scrap_n',
			'$framerate_n',
			'$game_function_n',
			'$need_ingot_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from flower_swap where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}

function ItemMaterialPackage(){
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item', 'type_id=10003');//装备类型
	
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		item_material_package
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			item_material_package
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=ItemMaterialPackage");				
	}	
	include_once template('t_itemmaterialpackage');
}


function SetItemMaterialPackage() {
	global $db; 
	global $id, $id_del, $id_arr, $material_package_level, $material_id, $material_number;
	global $material_package_level_n, $material_id_n, $material_number_n;
	//-----------------更新-------------------------------------------
	if ($material_id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					item_material_package 
				set 
					`material_id`='$material_id[$i]',
					`material_package_level`=$material_package_level[$i],
					`material_number`=$material_number[$i]
				where 
					id = $id[$i]
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($material_id_n)
	{
	
		$query = $db->query("
		insert into 
			item_material_package
			(
			`material_id`,
			`material_package_level`,
			`material_number`
			) 
		values 
			(
			'$material_id_n',
			'$material_package_level_n',
			'$material_number_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_material_package where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//----------------------------------------------------------------------------------------神秘商店物品表'
function MysteriousShopItem() {
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','type_id in (11001, 11002, 11003, 10003, 25000, 23000, 26000, 50000,14000)');//任务物品
	$type  = array(
		0 => array('id' => 1,'name' => '资源',),1 => array('id' => 2,'name' => '材料',),
	);
	$category = array(
		0 => array('id' => 1,'name' => '物品',),
		1 => array('id' => 2,'name' => '声望',),
		2 => array('id' => 3,'name' => '铜钱',),
		3 => array('id' => 4,'name' => '阅历',),
		4 => array('id' => 5,'name' => '灵石',),
		5 => array('id' => 6,'name' => '境界点',),
	);
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		mysterious_shop_item
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			mysterious_shop_item
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=MysteriousShopItem");
	}	
	include_once template('t_mysterious_shop_item');
}
//----------------------------------------------------------------------------------------神秘商店物品表'
function SetMysteriousShopItem() {
	global $db; 
	global $id_old,$id_del,$categoty,$type,$item_id,$amount,$ingot,$coin,$lv_min,$lv_max,$available; 
	global $categoty_n,$type_n,$item_id_n,$amount_n,$ingot_n,$coin_n,$lv_min_n,$lv_max_n,$available_n;

	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					mysterious_shop_item 
				set 
					`categoty`='$categoty[$i]',
					`type`='$type[$i]',
					`item_id`='$item_id[$i]',
					`amount`='$amount[$i]',
					`ingot`='$ingot[$i]',
					`coin`='$coin[$i]',
					`lv_min`='$lv_min[$i]',
					`lv_max`='$lv_max[$i]',
					`available`='$available[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			mysterious_shop_item
			(`categoty`,`type`,`item_id`,`amount`,`ingot`,`coin`,`lv_min`,`lv_max`,`available`) 
		values 
			('$categoty_n','$type_n','$item_id_n','$amount_n','$ingot_n','$coin_n','$lv_min_n','$lv_max_n','$available_n')
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
		$id_arr = implode(",",$id_del);
		$db->query("delete from mysterious_shop_item where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

//----------------------------------------------------------------------------------------商城物品表
function OnlineShopItem()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','type_id in (14000,17000,7,22000,23000,24000,25000,26000, 32000,33000,35000,36000,44000,40000,47000)');//任务物品

	$sell_type = $category = array();
	$query = $db->query("select * from online_shop_sell_type");
	while($trs = $db->fetch_array($query))
	{	
		$sell_type[] =  $trs;
	}

	$query = $db->query("select * from online_shop_category");
	while($crs = $db->fetch_array($query))
	{	
		$category[] =  $crs;
	}

	$num = $db->result($db->query("
	select 
		count(*)
	from 
		online_shop_item
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			online_shop_item
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=OnlineShopItem");
	}	
	include_once template('t_online_shop_item');

}

function SetOnlineShopItem() {
	global $db; 
	global $id_old,$id_del,$category,$sell_type,$item_id,$item_amount,$price,$is_first_page,$is_on_sell,$org_price; 
	global $category_n,$sell_type_n,$item_id_n,$item_amount_n,$price_n,$is_first_page_n,$is_on_sell_n,$org_price_n;

	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					online_shop_item 
				set 
					`category`='$category[$i]',
					`sell_type`='$sell_type[$i]',
					`item_id`='$item_id[$i]',
					`item_amount`='$item_amount[$i]',
					`price`='$price[$i]',
					`is_first_page`='$is_first_page[$i]',
					`is_on_sell`='$is_on_sell[$i]',
					`org_price`='$org_price[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			online_shop_item
			(`category`,`sell_type`,`item_id`,`item_amount`,`price`,`is_first_page`,`is_on_sell`,`org_price`) 
		values 
			('$category_n','$sell_type_n','$item_id_n','$item_amount_n','$price_n','$is_first_page_n','$is_on_sell_n','$org_price_n')
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
		$id_arr = implode(",",$id_del);
		$db->query("delete from online_shop_item where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------商城广告表
function OnlineShopAdvertisement() {
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*)
	from 
		online_shop_advertisement
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			online_shop_advertisement
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=OnlineShopAdvertisement");
	}	
	include_once template('t_online_shop_advertisement');
}


function SetOnlineShopAdvertisement() {
	global $db; 
	global $id_old,$id_del,$name; 
	global $name_n;

	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					online_shop_advertisement 
				set 
					`name`='$name[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			online_shop_advertisement(`name`) 
		values 
			('$name_n')
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
		$id_arr = implode(",",$id_del);
		$db->query("delete from online_shop_advertisement where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}



//--------------------------------------------------------------------------------------------物品换零件

function  ItemToSoul() 
{
	global $db,$page; 
	
	$item_id = ReqNum('item_id');
	if(!$item_id){
		$item_id = 1263;
	}	
	
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$soul_list = globalDataList('soul');//灵件
	$item_list = globalDataList('item','type_id=10006 and id = 1263');//任务物品
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		item_to_soul A
		left join soul B on A.soul_id = B.id
	where 
		A.item_id = '$item_id'		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as soul_name
		from 
			item_to_soul A 
			left join soul B on A.soul_id = B.id
		where 
			A.item_id = '$item_id'
		order by 
			A.item_id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=ItemToSoul&item_id=$item_id");				
	}	
	include_once template('t_item_to_soul');
}

//--------------------------------------------------------------------------------------------物品

function  Item() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$name=ReqStr('name');
	$a = ReqStr('a');
	$type_id = ReqNum('type_id');
	$order = ReqStr('order');	
	$item_type_list = globalDataList('item_type');//装备类型
	$item_price_list = globalDataList('item_price');//装备价格等级
	$item_quality_list = globalDataList('item_quality');//装备品质等级
	$item_ingot_list = globalDataList('item_ingot');//元宝级
	$role_job_list = globalDataList('role_job');//职业
	if($type_id)
	{
		//$type_id = $db->result_first("select min(id) from item_type ");
		$set_type = " and A.type_id = '$type_id'";
	}
	
	if ($name) 
	{
		$set_name = " and A.name like '%$name%'";	
	}	

	if ($order) 
	{
		$set_order = "A.$order asc,";	
	}
	
	if ($a) 
	{
		$set_s = " and C.role_job_id <>''";	
		$select = "left join item_equip_job C on A.id = C.item_id and C.role_job_id in ($a)";
		
	}	
	
	//------------------------------------------------------------
	$wq = $db->fetch_first("select id from item_type where name = '武器'");
	$wqid = $wq['id'];
		
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		distinct(count(*))
	from 
		item A
		left join item_type B on A.type_id = B.id
		$select		
	where
		A.id <> 0		
		$set_type
		$set_name
		$set_s
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			distinct(A.id),
			A.*,
			B.name as item_type_name
		from 
			item A 
			left join item_type B on A.type_id = B.id
			$select
		where
			A.id <> 0
			$set_type
			$set_name
			$set_s
		group by id
		order by 
			$set_order
			A.id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			//echo $rs['role_job_id'].'<br />';
			$rs['name_url'] = urlencode($rs['name']);
			if(!$rs['job_name'])
			{
				$rs['job_name'] = '-';
			}
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&type_id=$type_id&name=$name&order=$order&a=$a");				
	}	
	include_once template('t_item');
}

//--------------------------------------------------------------------------------------------牌子商品

function  ItemCardJob() 
{
	global $db,$page; 
	
	$card_item_id = ReqNum('card_item_id');
	if(!$card_item_id){
		$card_item_id = 145;
	}	
	
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item');//装备
	$card_item_list = globalDataList('item','type_id=10006');//任务物品
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		item_card_job A
		left join item B on A.item_id = B.id
	where 
		A.card_item_id = '$card_item_id'		
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as item_name,
			C.name as card_item_name
		from 
			item_card_job A 
			left join item B on A.item_id = B.id
			left join item C on A.card_item_id = C.id
		where 
			A.card_item_id = '$card_item_id'
		order by 
			A.item_id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=ItemCardJob&card_item_id=$card_item_id");				
	}	
	include_once template('t_item_card_job');
}

//--------------------------------------------------------------------------------------------物品类型

function  ItemType() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		item_type
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_type');
}
//--------------------------------------------------------------------------------------------物品价格等级

function  ItemPrice() 
{
	global $db; 
	$type = ReqNum('type');
	
	$query = $db->query("
	select 
		*
	from 
		item_price
	order by 
		level asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_price');
}
//--------------------------------------------------------------------------------------------元宝等级

function  ItemIngot() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		item_ingot
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_ingot');
}
//--------------------------------------------------------------------------------------------物品价格等级

function  ItemQuality() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		item_quality
	order by 
		quality asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_quality');
}

//--------------------------------------------------------------------------------------------物品强化等级

function  ItemUpgrade() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		item_upgrade
	order by 
		level asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_upgrade');
}

//------------------------------------------------------物品强化价格表
function ItemUpgradePrice() 
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$type_id = ReqNum('type_id');
	$item_type_list = globalDataList('item_type','id<=10000');//装备类型	
	$item_quality_list = globalDataList('item_quality');//装备品质	
	if(!$type_id)
	{
		$type_id = $db->result_first("select min(id) from item_type ");
	}	

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		item_upgrade_price A
	where 
		A.item_type_id = '$type_id'	
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as item_type_name,
			C.name as item_quality_name
		from 
			item_upgrade_price A
			left join item_type B on A.item_type_id = B. id
			left join item_quality C on A.item_quality_id = C. quality
		where 
			A.item_type_id = '$type_id'
		order by 
			A.upgrade_level asc
		limit 
			$start_num,$pageNum			
		");
		if($db->num_rows($query))
		{				
			while($rs = $db->fetch_array($query))
			{	
				$list_array[] =  $rs;
			}
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=ItemUpgradePrice&type_id=$type_id");				
	}
	include_once template('t_item_upgrade_price');

}
//--------------------------------------------------------------------------------------------背包格子

function  ItemPackGrid() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		item_pack_grid
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_item_pack_grid');
}
//--------------------------------------------------------------------------------------------成长礼包

function  SuperGift() 
{
	global $db; 
	$gift_id = ReqNum('gift_id');
	$item_list = globalDataList('item','type_id not in (10001,10003,10004,10007,10009)','require_level desc,type_id asc');//物品
	if(!$gift_id){
		$gift_id = $db->result_first("select min(id) from item where type_id in (10007,10009)");
	}	
	//--------------------------------------------------
	$query = $db->query("
	select
		A.id,
		A.name,
		A.require_level,
		C.name as role_job_name
	from 
		item A
		left join item_equip_job B on A.id = B.item_id
		left join role_job C on B.role_job_id = C.id
	where 
		A.type_id in (10007,10009)
	order by 
		A.require_level desc,
		A.type_id asc
	");
	if($db->num_rows($query))
	{	

		while($irs = $db->fetch_array($query))
		{
			$item_list[] =  $irs;
		}
	}
	
	//--------------------------------------------------
	
	$query = $db->query("
	select
		A.id,
		A.name as item_name,
		A.require_level,
		C.name as role_job_name
	from 
		item A
		left join item_equip_job B on A.id = B.item_id
		left join role_job C on B.role_job_id = C.id
	where 
		A.type_id in (10007,10009)
	order by 
		A.require_level asc,
		C.id asc
	");
	if($db->num_rows($query))
	{	

		while($grs = $db->fetch_array($query))
		{
			$gift_list[] =  $grs;
		}
		if ($gift_list) $rows = array_chunk($gift_list,3); 	
	}
	
	
	//--------------------------------------------------
	$query = $db->query("
	select 
		A.*
	from 
		super_gift A
	where 
		A.gift_id = '$gift_id'		
	order by 
		A.id desc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
		
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_super_gift');
}

//--------------------------------------------------------------------------------------------批量设置物品
function  SetItem() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$type_id = ReqArray('type_id');
	$icon_id = ReqArray('icon_id');
	$price_level = ReqArray('price_level');
	$usage = ReqArray('usage');
	$description = ReqArray('description');
	$quality = ReqArray('quality');
	$attack = ReqArray('attack');
	$attack_up = ReqArray('attack_up');
	$defense = ReqArray('defense');
	$defense_up = ReqArray('defense_up');
	$stunt_attack = ReqArray('stunt_attack');
	$stunt_attack_up = ReqArray('stunt_attack_up');
	$stunt_defense = ReqArray('stunt_defense');
	$stunt_defense_up = ReqArray('stunt_defense_up');
	$magic_attack = ReqArray('magic_attack');
	$magic_attack_up = ReqArray('magic_attack_up');
	$magic_defense = ReqArray('magic_defense');
	$magic_defense_up = ReqArray('magic_defense_up');
	$health = ReqArray('health');
	$health_up = ReqArray('health_up');
	$require_level = ReqArray('require_level');
	$strength = ReqArray('strength');
	$agile = ReqArray('agile');
	$intellect = ReqArray('intellect');
	$ingot_level = ReqArray('ingot_level');
	$speed = ReqArray('speed');
	$speed_up = ReqArray('speed_up');
	$sign = ReqArray('sign');
	$ingot = ReqArray('ingot');
	$day = ReqArray('day');
	
	
	$name_n = ReqStr('name_n');
	$type_id_n = ReqNum('type_id_n');
	$icon_id_n = ReqNum('icon_id_n');
	$price_level_n = ReqNum('price_level_n');
	$usage_n = ReqStr('usage_n');
	$description_n = ReqStr('description_n');
	$quality_n = ReqNum('quality_n');
	$attack_n = ReqNum('attack_n');
	$attack_up_n = ReqNum('attack_up_n');
	$defense_n = ReqNum('defense_n');
	$defense_up_n = ReqNum('defense_up_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$stunt_attack_up_n = ReqNum('stunt_attack_up_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	$stunt_defense_up_n = ReqNum('stunt_defense_up_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$magic_attack_up_n = ReqNum('magic_attack_up_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$magic_defense_up_n = ReqNum('magic_defense_up_n');
	$health_n = ReqNum('health_n');
	$health_up_n = ReqNum('health_up_n');
	$require_level_n = ReqNum('require_level_n');
	$strength_n = ReqNum('strength_n');
	$agile_n = ReqNum('agile_n');
	$intellect_n = ReqNum('intellect_n');	
	$ingot_level_n = ReqNum('ingot_level_n');		
	$speed_n = ReqNum('speed_n');
	$speed_up_n = ReqNum('speed_up_n');
	$sign_n = ReqStr('sign_n');
	$ingot_n = ReqNum('ingot_n');
	$day_n = ReqNum('day_n');

	//-----------------更新-------------------------------------------
	if ($id && $name && $type_id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					item 
				set 
					`name`='$name[$i]',
					`type_id`='$type_id[$i]',
					`icon_id`='$icon_id[$i]',
					`price_level`='$price_level[$i]',
					`ingot_level`='$ingot_level[$i]',
					`usage`='$usage[$i]',
					`description`='$description[$i]',
					`quality`='$quality[$i]',
					`attack`='$attack[$i]',
					`attack_up`='$attack_up[$i]',
					`defense`='$defense[$i]',
					`defense_up`='$defense_up[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_attack_up`='$stunt_attack_up[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`stunt_defense_up`='$stunt_defense_up[$i]',
					`magic_attack`='$magic_attack[$i]',
					`magic_attack_up`='$magic_attack_up[$i]',
					`magic_defense`='$magic_defense[$i]',
					`magic_defense_up`='$magic_defense_up[$i]',
					`health`='$health[$i]',
					`health_up`='$health_up[$i]',
					`require_level`='$require_level[$i]',
					`strength`='$strength[$i]',
					`agile`='$agile[$i]',
					`intellect`='$intellect[$i]',
					`speed`='$speed[$i]',
					`speed_up`='$speed_up[$i]',
					`sign`='$sign[$i]',
					`ingot`='$ingot[$i]',
					`day`='$day[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			item
			(
			`name`,
			`icon_id`,
			`type_id`,
			`price_level`,
			`ingot_level`,
			`usage`,
			`description`,
			`quality`,
			`attack`,
			`attack_up`,
			`defense`,
			`defense_up`,
			`stunt_attack`,
			`stunt_attack_up`,
			`stunt_defense`,
			`stunt_defense_up`,
			`magic_attack`,
			`magic_attack_up`,
			`magic_defense`,
			`magic_defense_up`,
			`health`,
			`health_up`,
			`require_level`,
			`strength`,
			`agile`,
			`intellect`,
			`speed`,
			`speed_up`,
			`sign`,
			`ingot`,
			`day`
			) 
		values 
			(
			'$name_n',
			'$icon_id_n',
			'$type_id_n',
			'$price_level_n',
			'$ingot_level_n',
			'$usage_n',
			'$description_n',
			'$quality_n',
			'$attack_n',
			'$attack_up_n',
			'$defense_n',
			'$defense_up_n',
			'$stunt_attack_n',
			'$stunt_attack_up_n',
			'$stunt_defense_n',
			'$stunt_defense_up_n',
			'$magic_attack_n',
			'$magic_attack_up_n',
			'$magic_defense_n',
			'$magic_defense_up_n',
			'$health_n',
			'$health_up_n',
			'$require_level_n',
			'$strength_n',
			'$agile_n',
			'$intellect_n',
			'$speed_n',
			'$speed_up_n',
			'$sign_n',
			'$ingot_n',
			'$day_n'
			)
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置牌子商品
function  SetItemCardJob() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$item_id = ReqArray('item_id');
	$card_item_id = ReqArray('card_item_id');
	$card_item_id_old = ReqArray('card_item_id_old');
	$number = ReqArray('number');

	
	$item_id_n = ReqStr('item_id_n');
	$card_item_id_n = ReqNum('card_item_id_n');
	$number_n = ReqNum('number_n');
			
	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i] && $card_item_id[$i] && $card_item_id_old[$i])
			{

				$db->query("
				update 
					item_card_job 
				set 
					`number`='$number[$i]',
					`card_item_id`='$card_item_id[$i]'
				where 
					item_id = '$item_id[$i]'
					and card_item_id = '$card_item_id_old[$i]'
					
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n && $card_item_id_n)
	{
	
		$query = $db->query("
		insert into 
			item_card_job
			(
			`item_id`,
			`card_item_id`,
			`number`
			) 
		values 
			(
			'$item_id_n',
			'$card_item_id_n',
			'$number_n'
			)
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
			$db->query("delete from item_card_job where item_id = '$idArr[0]' and card_item_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置物品类型
function  SetItemType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$max_repeat_num = ReqArray('max_repeat_num');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$max_repeat_num_n = ReqStr('max_repeat_num_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_type where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $id[$i] && $name[$i]&& $sign[$i])
			{

				$db->query("
				update 
					item_type 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`max_repeat_num`='$max_repeat_num[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n && $name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			item_type
			(`id`,`name`,`sign`,`max_repeat_num`) 
		values 
			('$id_n','$name_n','$sign_n','$max_repeat_num_n')
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

//--------------------------------------------------------------------------------------------批量设置物品价格等级
function  SetItemPrice() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('level');
	$item_price = ReqArray('item_price');
	$equip_price = ReqArray('equip_price');
	//$ingot_price = ReqArray('ingot_price');
	
	$level_n = ReqNum('level_n');
	$item_price_n = ReqNum('item_price_n');
	$equip_price_n = ReqNum('equip_price_n');
	//$ingot_price_n = ReqNum('ingot_price_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					item_price 
				set 
					`item_price`='$item_price[$i]',
					`equip_price`='$equip_price[$i]'
				where 
					level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n)
	{
	
		$query = $db->query("
		insert into 
			item_price
			(`level`,`item_price`,`equip_price`) 
		values 
			('$level_n','$item_price_n','$equip_price_n')
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_price where level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置物品品质等级
function  SetItemQuality() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('quality');
	$name = ReqArray('name');
	$quality_n = ReqNum('quality_n');
	$name_n = ReqStr('name_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					item_quality 
				set 
					`name`='$name[$i]'
				where 
					quality = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($quality_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			item_quality
			(`quality`,`name`) 
		values 
			('$quality_n','$name_n')
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_quality where quality in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置牌子换灵件
function  SetItemToSoul() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$item_id = ReqArray('item_id');
	$soul_id = ReqArray('soul_id');
	$item_num = ReqArray('item_num');

	
	$item_id_n = ReqStr('item_id_n');
	$soul_id_n = ReqNum('soul_id_n');
	$item_num_n = ReqNum('item_num_n');
			
	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i] && $soul_id[$i])
			{

				$db->query("
				update 
					item_to_soul 
				set 
					`item_num`='$item_num[$i]',
					`item_id`='$item_id[$i]'
				where 
					item_id = '$item_id[$i]'
					and soul_id = '$soul_id[$i]'
					
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n && $soul_id_n)
	{
	
		$query = $db->query("
		insert into 
			item_to_soul
			(
			`item_id`,
			`soul_id`,
			`item_num`
			) 
		values 
			(
			'$item_id_n',
			'$soul_id_n',
			'$item_num_n'
			)
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
			$db->query("delete from item_to_soul where item_id = '$idArr[0]' and soul_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置元宝级别等级
function  SetItemIngot() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$ingot = ReqArray('ingot');
	$id_n = ReqNum('id_n');
	$ingot_n = ReqStr('ingot_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					item_ingot
				set 
					`ingot`='$ingot[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n && $ingot_n)
	{
	
		$query = $db->query("
		insert into 
			item_ingot
			(`id`,`ingot`) 
		values 
			('$id_n','$ingot_n')
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_ingot where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量设置物品强化等级
function  SetItemUpgrade() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$level = ReqArray('level');
	$name = ReqArray('name');
	$level_n = ReqNum('level_n');
	$name_n = ReqStr('name_n');
	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$level_num = count($level);

		for ($i=0;$i<=$level_num;$i++)	
		{
			if ($level[$i])
			{

				$db->query("
				update 
					item_upgrade 
				set 
					`name`='$name[$i]'
				where 
					level = '$level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			item_upgrade
			(`level`,`name`) 
		values 
			('$level_n','$name_n')
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_upgrade where level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置背包格子
function  SetItemPackGrid() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$ingot = ReqArray('ingot');
	$unlock_level = ReqArray('unlock_level');
	//$equip_item_type = ReqArray('equip_item_type');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$ingot_n = ReqNum('ingot_n');
	$unlock_level_n = ReqNum('unlock_level_n');
	//$equip_item_type_n = ReqNum('equip_item_type_n');	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from item_pack_grid where id in ($id_arr)");
		$msg = "删除成功！";
		
	}		
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i])
			{

				$db->query("
				update 
					item_pack_grid 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`ingot`='$ingot[$i]',
					`unlock_level`='$unlock_level[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n && $name_n && $ingot_n)
	{
	
		$query = $db->query("
		insert into 
			item_pack_grid
			(`id`,`name`,`ingot`,`unlock_level`) 
		values 
			('$id_n','$name_n','$ingot_n','$unlock_level_n')
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

//--------------------------------------------------------------------------------------------批量设置装备穿戴要求职业对应表
function  SetItemEquipJob() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	
	$item_id_n = ReqNum('item_id_n');
	$role_job_id_n = ReqNum('role_job_id_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
	

		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n && $role_job_id_n)
	{
		if ($role_job_id_n == 99999999)
		{	
			$role_job_list = globalDataList('role_job','id not in (8,9,10)');//角色	
			
			for ($i=0;$i<count($role_job_list);$i++)	{
				//$msg .= $role_job_list[$i]['id'].'<br />';
				//echo $role_job_list[$i].'<br />';
				$role_job_id = $role_job_list[$i]['id'];
				$num = $db->result($db->query("select count(*)from item_equip_job where item_id = '$item_id' and role_job_id = '$role_job_id'"),0);	
				if(!$num){
				
					$query = $db->query("
					insert into 
						item_equip_job
						(`item_id`,`role_job_id`) 
					values 
						('$item_id_n','$role_job_id')
					") ;
				}
			}			
		}else{
			$query = $db->query("
			insert into 
				item_equip_job
				(`item_id`,`role_job_id`) 
			values 
				('$item_id_n','$role_job_id_n')
			") ;	
		}
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
			$db->query("delete from item_equip_job where item_id = '$idArr[0]' and role_job_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------批量设置物品强化价格表
function  SetItemUpgradePrice() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$upgrade_level = ReqArray('upgrade_level');
	$item_type_id = ReqArray('item_type_id');
	$item_quality_id = ReqArray('item_quality_id');
	$upgrade_price = ReqArray('upgrade_price');

	$upgrade_level_n = ReqNum('upgrade_level_n');
	$item_type_id_n = ReqNum('item_type_id_n');
	$item_quality_id_n = ReqNum('item_quality_id_n');
	$upgrade_price_n = ReqNum('upgrade_price_n');
	
	//-----------------更新-------------------------------------------
	if ($upgrade_level)
	{
	
		$upgrade_level_num = count($upgrade_level);

		for ($i=0;$i<=$upgrade_level_num;$i++)	
		{
			if ($upgrade_level[$i] && $item_type_id[$i] && $item_quality_id[$i] && $upgrade_price[$i])
			{

				$db->query("
				update 
					item_upgrade_price 
				set 
					`upgrade_price`='$upgrade_price[$i]'
				where 
					upgrade_level = '$upgrade_level[$i]'
					and item_type_id = '$item_type_id[$i]'
					and item_quality_id = '$item_quality_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($upgrade_price_n && $upgrade_level_n && $item_type_id_n && $item_quality_id_n)
	{
	
		$query = $db->query("
		insert into 
			item_upgrade_price
			(`upgrade_price`,`upgrade_level`,`item_type_id`,`item_quality_id`) 
		values 
			('$upgrade_price_n','$upgrade_level_n','$item_type_id_n','$item_quality_id_n')
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
			$db->query("delete from item_upgrade_price where upgrade_level = '$idArr[0]' and item_type_id = '$idArr[1]' and item_quality_id = '$idArr[2]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置物品对应怪物
function  SetAvatarItemMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$monster_id = ReqArray('monster_id');

	$item_id = ReqNum('item_id');
	$monster_id_n = ReqNum('monster_id_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	
		
	//-----------------增加记录-------------------------------------------
	if ($item_id && $monster_id_n)
	{
	
		$query = $db->query("
		insert into 
			avatar_item_monster
			(`item_id`,`monster_id`) 
		values 
			('$item_id','$monster_id_n')
		") ;
		if($query)
		{
			$msg = "增加成功！";
		}
		else
		{
			$msg = '<strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from avatar_item_monster where item_id = '$idArr[0]' and monster_id = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
	
}


//--------------------------------------------------------------------------------------------批量设置成长礼包
function  SetSuperGift() 
{
	global $db; 
	$id = ReqArray('id');
	$id_del = ReqArray('id_del');
	$item_id = ReqArray('item_id');
	$item_number = ReqArray('item_number');
	$type = ReqArray('type');
	$ingot = ReqArray('ingot');
	$coins = ReqArray('coins');
	

	$gift_id_n = ReqNum('gift_id_n');
	$item_id_n = ReqNum('item_id_n');
	$item_number_n = ReqNum('item_number_n');
	$type_n = ReqNum('type_n');
	$ingot_n = ReqNum('ingot_n');
	$coins_n = ReqNum('coins_n');	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $type[$i])
			{

				$db->query("
				update 
					super_gift 
				set 
					`type`='$type[$i]',
					`item_id`='$item_id[$i]',
					`item_number`='$item_number[$i]',
					`ingot`='$ingot[$i]',
					`coins`='$coins[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($gift_id_n && $type_n)
	{
	
		$query = $db->query("
		insert into 
			super_gift
			(`gift_id`,`item_id`,`item_number`,`type`,`ingot`,`coins`) 
		values 
			('$gift_id_n','$item_id_n','$item_number_n','$type_n','$ingot_n','$coins_n')
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
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from super_gift where id in ($id_arr)");
		$msg = "<br />删除成功！";
		
	}		
	showMsg($msg,'','','greentext');	
}


function SkillBook() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$stoneitem_list = globalDataList('item','type_id=22000');
	$skillitem_list = globalDataList('item','type_id=24000');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		attribute_stone_skill_book
	"),0);
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			attribute_stone_skill_book
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=SkillBook");	

	}	
	include_once template('t_skillbook');
}

function SetSkillBook() {
	global $db; 
	global $item_id,$item_id_old,$stone_lv_old,$id_del,$item_id,$stone_lv,$stone_item_id; 
	global $item_id_n,$stone_lv_n,$stone_item_id_n;

	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i])
			{

				$db->query("
				update 
					attribute_stone_skill_book 
				set 
					`item_id`='$item_id[$i]',
					`stone_lv`='$stone_lv[$i]',
					`stone_item_id`='$stone_item_id[$i]'
				where 
					item_id = '$item_id_old[$i]'
					and stone_lv = '$stone_lv_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			attribute_stone_skill_book
			(`item_id`,`stone_lv`,`stone_item_id`) 
		values 
			('$item_id_n','$stone_lv_n','$stone_item_id_n')
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
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from attribute_stone_skill_book where item_id = '$idArr[0]' and `stone_lv` = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');	
}


function EnhanceItem() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$item_list = globalDataList('item','type_id=26000');
	$war_attribute_type_list = globalDataList('war_attribute_type');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		enhance_item
	"),0);
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			enhance_item
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=item&action=EnhanceItem");	

	}	
	include_once template('t_enhance_item');
}

function SetEnhanceItem() {
	global $db; 
	global $item_id,$item_id_old,$lv_old,$id_del,$item_id,$lv,$war_attribute_type_id,$value,$need_item_id,$need_item_count,$duration,$need_coins; 
	global $item_id_n,$lv_n,$war_attribute_type_id_n,$value_n,$need_item_id_n,$need_item_count_n,$duration_n,$need_coins_n;

	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i])
			{

				$db->query("
				update 
					enhance_item 
				set 
					`item_id`='$item_id[$i]',
					`lv`='$lv[$i]',
					`war_attribute_type_id`='$war_attribute_type_id[$i]',
					`value`='$value[$i]',
					`need_item_id`='$need_item_id[$i]',
					`need_item_count`='$need_item_count[$i]',
					`duration`='$duration[$i]',
					`need_coins`='$need_coins[$i]'
				where 
					item_id = '$item_id_old[$i]'
					and lv = '$lv_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			enhance_item
			(`item_id`,`lv`,`war_attribute_type_id`,`value`,`need_item_id`,`need_item_count`,`duration`,`need_coins`) 
		values 
			('$item_id_n','$lv_n','$war_attribute_type_id_n','$value_n','$need_item_id_n','$need_item_count_n','$duration_n','$need_coins_n')
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
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from enhance_item where item_id = '$idArr[0]' and `lv` = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');	

}
?>