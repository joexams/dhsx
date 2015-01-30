<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'EnhanceWeapon': EnhanceWeapon();break;
	case 'SetEnhanceWeapon': SetEnhanceWeapon();break;

	case 'EnhanceWeaponEffect': EnhanceWeaponEffect();break;
	case 'SetEnhanceWeaponEffect': SetEnhanceWeaponEffect();break;

	case 'SetEnhanceWeaponLevelup': SetEnhanceWeaponLevelup();break;

}

function SetEnhanceWeaponLevelup()
{
	global $db; 
	global $id, $id_del, $id_arr, $level, $weapon_id, $value2, $need_crystal, $need_jade, $effect_id, $prob, $value, $inc_health, $inc_atk, $inc_def;
	global $level_n, $weapon_id_n, $need_crystal_n, $value2_n, $need_jade_n, $effect_id_n, $prob_n, $value_n, $inc_health_n, $inc_atk_n, $inc_def_n;
	global $url,$winid;
	//-----------------更新-------------------------------------------
	if ($weapon_id)
	{
	
		$id_num = count($weapon_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($weapon_id[$i] && $level[$i])
			{

				$db->query("
				update 
					enhance_weapon_levelup 
				set 
					`level`='$level[$i]',
					`weapon_id`='$weapon_id[$i]',
					`need_crystal`='$need_crystal[$i]',
					`need_jade`='$need_jade[$i]',
					`effect_id`='$effect_id[$i]',
					`prob`='$prob[$i]',
					`value`='$value[$i]',
					`value2`='$value2[$i]',
					`inc_health`='$inc_health[$i]',
					`inc_atk`='$inc_atk[$i]',
					`inc_def`='$inc_def[$i]'
				where 
					weapon_id = $weapon_id[$i] 
					and 
					level = '$level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}

	//-----------------增加记录-------------------------------------------
	if ($weapon_id_n && $level_n )
	{
		$query = $db->query("
		insert into 
			enhance_weapon_levelup
			(
			`level`,
			`weapon_id`,
			`need_crystal`,
			`need_jade`,
			`effect_id`,
			`prob`,
			`value`,
			`value2`,
			`inc_health`,
			`inc_atk`,
			`inc_def`
			) 
		values 
			(
			'$level_n',
			'$weapon_id_n',
			'$need_crystal_n',
			'$need_jade_n',
			'$effect_id_n',
			'$prob_n',
			'$value_n',
			'$value2_n',
			'$inc_health_n',
			'$inc_atk_n',
			'$inc_def_n'
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
			$db->query("delete from enhance_weapon_levelup where weapon_id = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";
		
	}	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}

function EnhanceWeaponEffect()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		enhance_weapon_effect
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			enhance_weapon_effect
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=weapon&action=EnhanceWeaponEffect");				
	}	
	include_once template('t_enhance_weapon_effect');
}

function SetEnhanceWeaponEffect()
{
	global $db; 
	global $id, $id_del, $id_arr, $sign, $description;
	global $sign_n, $description_n;
	//-----------------更新-------------------------------------------
	if ($description)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					enhance_weapon_effect 
				set 
					`sign`='$sign[$i]',
					`description`='$description[$i]'
				where 
					id = $id[$i]
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($description_n)
	{
		$query = $db->query("
		insert into 
			enhance_weapon_effect
			(
			`sign`,
			`description`
			) 
		values 
			(
			'$sign_n',
			'$description_n'
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
		$db->query("delete from enhance_weapon_effect where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}

function EnhanceWeapon()
{
	global $db,$page; 

	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$effect_list = globalDataList('enhance_weapon_effect');//装备类型
	
	$num = $db->result($db->query("
	select 
		count(*)
	from 
		enhance_weapon
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			enhance_weapon
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");

		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=weapon&action=EnhanceWeapon");				
	}	
	include_once template('t_enhance_weapon');
}

function SetEnhanceWeapon()
{
	global $db; 
	global $id, $id_del, $id_arr, $name, $description, $type, $effect, $need_feats, $need_weapon, $need_weapon_lv;
	global $name_n, $description_n, $type_n, $effect_n, $need_feats_n, $need_weapon_n, $need_weapon_lv_n;
	//-----------------更新-------------------------------------------
	if ($name)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{
				$db->query("
				update 
					enhance_weapon 
				set 
					`name`='$name[$i]',
					`description`='$description[$i]',
					`type`='$type[$i]',
					`effect`='$effect[$i]',
					`need_weapon`='$need_weapon[$i]',
					`need_weapon_lv`='$need_weapon_lv[$i]',
					`need_feats`='$need_feats[$i]'
				where 
					id = $id[$i]
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
			enhance_weapon
			(
			`name`,
			`description`,
			`type`,
			`effect`,
			`need_weapon`,
			`need_weapon_lv`,
			`need_feats`
			) 
		values 
			(
			'$name_n',
			'$description_n',
			'$type_n',
			'$effect_n',
			'$need_weapon_n',
			'$need_weapon_lv_n',
			'$need_feats_n'
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
		$db->query("delete from enhance_weapon where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}