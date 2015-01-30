<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'Dragonball': Dragonball();break;
	case 'SetDragonball': SetDragonball();break;

	case 'DragonballBreakthrough': DragonballBreakthrough();break;
	case 'SetDragonballBreakthrough': SetDragonballBreakthrough();break;

	case 'DragonballUpgradeInfo': DragonballUpgradeInfo();break;
	case 'SetDragonballUpgradeInfo': SetDragonballUpgradeInfo();break;

	case 'FireDragonballExp': FireDragonballExp();break;
	case 'SetFireDragonballExp': SetFireDragonballExp();break;

	case 'DragonballQuality': DragonballQuality();break;
	case 'SetDragonballQuality': SetDragonballQuality();break;

	case 'DragonballEffect': DragonballEffect();break;
	case 'SetDragonballEffect': SetDragonballEffect();break;

	case 'DragonballBuff': DragonballBuff();break;
	case 'SetDragonballBuff': SetDragonballBuff();break;

	case 'DragonballLevelDescription': DragonballLevelDescription();break;
	case 'SetDragonballLevelDescription': SetDragonballLevelDescription();break;
}

//===============================================================玩家龙珠等级效果描述
function DragonballLevelDescription()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$dragonball_list = globalDataList('dragonball');

	$fields = array();
	$fnum = 3;
	$query = $db->query("SHOW full fields FROM dragonball_level_description");
	while($rs = $db->fetch_array($query))
	{
		$fnum++;
		$fields[$rs['Field']] =  $rs['Comment'];
	}

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_level_description
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_level_description
		order by 
			dragonball_id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballLevelDescription");	
	}	
	include_once template('t_dragonball_level_description');
}

function SetDragonballLevelDescription()
{
	global $db; 
	global $id_del, $id, $dragonball_id, $level_1, $level_2, $level_3, $level_4, $level_5, $level_6, $level_7, $level_8, $level_9, $level_10;
	global $dragonball_id_n, $level_1_n, $level_2_n, $level_3_n, $level_4_n, $level_5_n, $level_6_n, $level_7_n, $level_8_n, $level_9_n, $level_10_n;

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
					dragonball_level_description 
				set 
					`level_1`='$level_1[$i]',
					`level_2`='$level_2[$i]',
					`level_3`='$level_3[$i]',
					`level_4`='$level_4[$i]',
					`level_5`='$level_5[$i]',
					`level_6`='$level_6[$i]',
					`level_7`='$level_7[$i]',
					`level_8`='$level_8[$i]',
					`level_9`='$level_9[$i]',
					`level_10`='$level_10[$i]'
				where  
					dragonball_id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($dragonball_id_n)
	{
	
		$query = $db->query("
		insert into 
			dragonball_level_description
			(`dragonball_id`, `level_1`, `level_2`,`level_3`,`level_4`,`level_5`,`level_6`,`level_7`,`level_8`,`level_9`,`level_10`) 
		values 
			('$dragonball_id_n', '$level_1_n','$level_2_n','$level_3_n','$level_4_n','$level_5_n','$level_6_n','$level_7_n','$level_8_n','$level_9_n','$level_10_n')
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
		$db->query("delete from dragonball_level_description where dragonball_id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}



//===============================================================神龙上供 龙珠升级经验费用信息表
function DragonballUpgradeInfo()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$quality_list = globalDataList('dragonball_quality');


	$fields = array();
	$fnum = 0;
	$query = $db->query("SHOW full fields FROM dragonball_upgrade_info");
	while($rs = $db->fetch_array($query))
	{
		$fnum++;
		$fields[$rs['Field']] =  $rs['Comment'];
	}

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_upgrade_info
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_upgrade_info
		order by 
			quality_id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballUpgradeInfo");	
	}	
	include_once template('t_dragonball_upgrade_info');
}

function SetDragonballUpgradeInfo()
{
	global $db; 
	global $id_del, $id, $quality_id, $level2_exp, $level3_exp, $level4_exp, $level5_exp, $level6_exp, $level7_exp, $level8_exp, $level9_exp, $level10_exp, $mergelevel1_costs, $mergelevel2_costs, $mergelevel3_costs, $mergelevel4_costs, $mergelevel5_costs, $mergelevel6_costs, $mergelevel7_costs, $mergelevel8_costs, $mergelevel9_costs, $mergelevel10_costs;
	global $quality_id_n, $level2_exp_n, $level3_exp_n, $level4_exp_n, $level5_exp_n, $level6_exp_n, $level7_exp_n, $level8_exp_n, $level9_exp_n, $level10_exp_n, $mergelevel1_costs_n, $mergelevel2_costs_n, $mergelevel3_costs_n, $mergelevel4_costs_n, $mergelevel5_costs_n, $mergelevel6_costs_n, $mergelevel7_costs_n, $mergelevel8_costs_n, $mergelevel9_costs_n, $mergelevel10_costs_n;

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
					dragonball_upgrade_info 
				set 
					`level2_exp`='$level2_exp[$i]',
					`level3_exp`='$level3_exp[$i]',
					`level4_exp`='$level4_exp[$i]',
					`level5_exp`='$level5_exp[$i]',
					`level6_exp`='$level6_exp[$i]',
					`level7_exp`='$level7_exp[$i]',
					`level8_exp`='$level8_exp[$i]',
					`level9_exp`='$level9_exp[$i]',
					`level10_exp`='$level10_exp[$i]',
					`mergelevel1_costs`='$mergelevel1_costs[$i]',
					`mergelevel2_costs`='$mergelevel2_costs[$i]',
					`mergelevel3_costs`='$mergelevel3_costs[$i]',
					`mergelevel4_costs`='$mergelevel4_costs[$i]',
					`mergelevel5_costs`='$mergelevel5_costs[$i]',
					`mergelevel6_costs`='$mergelevel6_costs[$i]',
					`mergelevel7_costs`='$mergelevel7_costs[$i]',
					`mergelevel8_costs`='$mergelevel8_costs[$i]',
					`mergelevel9_costs`='$mergelevel9_costs[$i]',
					`mergelevel10_costs`='$mergelevel10_costs[$i]'
				where 
					quality_id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($quality_id_n)
	{
	
		$query = $db->query("
		insert into 
			dragonball_upgrade_info
			(`quality_id`, `level2_exp`,`level3_exp`,`level4_exp`,`level5_exp`,`level6_exp`,`level7_exp`,`level8_exp`,`level9_exp`,`level10_exp`,`mergelevel1_costs`,`mergelevel2_costs`,`mergelevel3_costs`,`mergelevel4_costs`,`mergelevel5_costs`,`mergelevel6_costs`,`mergelevel7_costs`,`mergelevel8_costs`,`mergelevel9_costs`,`mergelevel10_costs`) 
		values 
			('$quality_id_n','$level2_exp_n','$level3_exp_n','$level4_exp_n','$level5_exp_n','$level6_exp_n','$level7_exp_n','$level8_exp_n','$level9_exp_n','$level10_exp_n','$mergelevel1_costs_n','$mergelevel2_costs_n','$mergelevel3_costs_n','$mergelevel4_costs_n','$mergelevel5_costs_n','$mergelevel6_costs_n','$mergelevel7_costs_n','$mergelevel8_costs_n','$mergelevel9_costs_n','$mergelevel10_costs_n')
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
		$db->query("delete from dragonball_upgrade_info where quality_id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}




//===============================================================神龙上供 龙珠突破信息表
function DragonballBreakthrough()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	$quality_list = globalDataList('dragonball_quality');
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_breakthrough
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_breakthrough
		order by 
			quality_id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballBreakthrough");	
	}	
	include_once template('t_dragonball_breakthrough');
}

function SetDragonballBreakthrough()
{
	global $db; 
	global $id_del, $id, $quality_id, $green_count, $blue_count, $purple_count, $gold_count;
	global $quality_id_n, $green_count_n, $blue_count_n, $purple_count_n, $gold_count_n;

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
					dragonball_breakthrough 
				set 
					`quality_id`='$quality_id[$i]',
					`green_count`='$green_count[$i]',
					`blue_count`='$blue_count[$i]',
					`purple_count`='$purple_count[$i]',
					`gold_count`='$gold_count[$i]'
				where 
					quality_id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($quality_id_n)
	{
	
		$query = $db->query("
		insert into 
			dragonball_breakthrough
			(`quality_id`,`green_count`,`blue_count`,`purple_count`,`gold_count`) 
		values 
			('$quality_id_n','$green_count_n','$blue_count_n','$purple_count_n','$gold_count_n')
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
		$db->query("delete from dragonball_breakthrough where quality_id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


//===============================================================神龙上供 火龙珠经验表
function FireDragonballExp()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		fire_dragonball_exp
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			fire_dragonball_exp
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=FireDragonballExp");	
	}	
	include_once template('t_fire_dragonball_exp');
}

function SetFireDragonballExp()
{
	global $db; 
	global $id_del, $id, $xiao, $zhong, $da, $lie;
	global $xiao_n, $zhong_n, $da_n, $lie_n;
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
					fire_dragonball_exp 
				set 
					`xiao`='$xiao[$i]',
					`zhong`='$zhong[$i]',
					`da`='$da[$i]',
					`lie`='$lie[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($xiao_n || $zhong_n || $da_n || $lie_n)
	{
	
		$query = $db->query("
		insert into 
			fire_dragonball_exp
			(`xiao`,`zhong`,`da`,`lie`) 
		values 
			('$xiao_n','$zhong_n','$da_n','$lie_n')
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
		$db->query("delete from fire_dragonball_exp where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


//===============================================================神龙上供 龙珠
function Dragonball()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	
	$effect_list = globalDataList('dragonball_effect');
	$quality_list = globalDataList('dragonball_quality');

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=Dragonball");	
	}	
	include_once template('t_dragonball');
}

function SetDragonball()
{
	global $db; 
	global $id_del, $id, $name, $sign, $effect_id, $quality_id, $star_class;
	global $name_n, $sign_n, $effect_id_n, $quality_id_n, $star_class_n;
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
					dragonball 
				set 
					`name`='$name[$i]',
					`star_class`='$star_class[$i]',
					`sign`='$sign[$i]',
					`effect_id`='$effect_id[$i]',
					`quality_id`='$quality_id[$i]'
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
			dragonball
			(`name`,`sign`,`effect_id`,`quality_id`,`star_class`) 
		values 
			('$name_n','$sign_n','$effect_id_n','$quality_id_n','$star_class_n')
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
		$db->query("delete from dragonball where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}



//===============================================================神龙上供 龙珠品质
function DragonballQuality()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_quality
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_quality
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballQuality");	
	}	
	include_once template('t_dragonball_quality');
}

function SetDragonballQuality()
{
	global $db; 
	global $id_del, $id, $name, $sign, $dragonball_init_exp, $price;
	global $name_n, $sign_n, $dragonball_init_exp_n, $price_n;
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
					dragonball_quality 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`price`='$price[$i]',
					`dragonball_init_exp`='$dragonball_init_exp[$i]'
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
			dragonball_quality
			(`name`,`sign`,`dragonball_init_exp`,`price`) 
		values 
			('$name_n','$sign_n','$dragonball_init_exp_n','$price_n')
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
		$db->query("delete from dragonball_quality where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


//===============================================================神龙上供 龙珠效果
function DragonballEffect()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_effect
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_effect
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballEffect");	
	}	
	include_once template('t_dragonball_effect');
}

function SetDragonballEffect()
{
	global $db; 
	global $id_del, $id, $name, $sign;
	global $name_n, $sign_n;
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
					dragonball_effect 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			dragonball_effect
			(`name`,`sign`) 
		values 
			('$name_n', '$sign_n')
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
		$db->query("delete from dragonball_effect where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}


//===============================================================神龙上供 buff种类
function DragonballBuff()
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		dragonball_buff
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			* 
		from 
			dragonball_buff
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=dragonball&action=DragonballBuff");	
	}	
	include_once template('t_dragonball_buff');
}

function SetDragonballBuff()
{
	global $db; 
	global $id_del, $id, $name, $sign;
	global $name_n, $sign_n;
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
					dragonball_buff 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			dragonball_buff
			(`name`,`sign`) 
		values 
			('$name_n','$sign_n')
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
		$db->query("delete from dragonball_buff where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}