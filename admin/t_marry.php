<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'MarrySkill': MarrySkill();break;
	case 'MarryDescription': MarryDescription();break;
	case 'MarrySkillEffect': MarrySkillEffect();break;
	case 'SetMarrySkill': SetMarrySkill();break;
	case 'SetMarryDescription': SetMarryDescription();break;
	case 'SetMarrySkillEffect': SetMarrySkillEffect();break;
	default:  MarrySkill();
}

//--------------------------------------------------------------------------------------------结婚技能表
function MarrySkill()
{
	global $db,$page;
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select
		count(1)
	from
		marry_skill
	"),0);
	if($num)
	{
		$query = $db->query("
				select
				*
				from
				marry_skill
				order by
				id asc
				limit
				$start_num,$pageNum
				");
		while($rs = $db->fetch_array($query))
		{
		$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=marry&action=MarrySkill");
	
	}
		include_once template('t_marry_skill');
}
//--------------------------------------------------------------------------------------------结婚亲密度描述表
function MarryDescription()
{
	global $db,$page;
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select
		count(1)
	from
		marry_description
	"),0);
	if($num)
	{
		$query = $db->query("
				select
				*
				from
				marry_description
				order by
				id asc
				limit
				$start_num,$pageNum
				");
		while($rs = $db->fetch_array($query))
		{
		$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=marry&action=MarryDescription");
	
	}
		include_once template('t_marry_description');
	
	
}
//--------------------------------------------------------------------------------------------结婚技能效果表
function MarrySkillEffect()
{
	global $db,$page;
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$marry_skill_list = globalDataList('marry_skill');
	//------------------------------------------------------------
	
	$num = $db->result($db->query("
	select
		count(1)
	from
		marry_skill_effect
	"),0);
	if($num)
	{
		$query = $db->query("
				select
				*
				from
				marry_skill_effect
				order by
				id asc
				limit
				$start_num,$pageNum
				");
		while($rs = $db->fetch_array($query))
		{
		$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=marry&action=MarrySkillEffect");
	
	}
		include_once template('t_marry_skill_effect');
	
	
}
//--------------------------------------------------------------------------------------------批量设置结婚技能表
function SetMarrySkill()
{
	global $db;
	global $id_del,$id,$desc,$sign;
	global $desc_n,$sign_n;
	
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
					marry_skill
					set
					`desc`='$desc[$i]',
					`sign`='$sign[$i]'
					where 
					id = '$id[$i]'
					");
			}
			
		}
	$msg = "更新成功！";
	}
	
	//-----------------增加记录-------------------------------------------
	if ($desc_n || $sign_n)
	{
	
		$query = $db->query("
		insert into marry_skill(
			`desc`,
			`sign`
			) values (
			'$desc_n',
			'$sign_n'
			)") ;
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
			$db->query("delete from marry_skill where id = '$idArr[0]'");
		}
		$msg .= "<br />删除成功！";
	}
	
	showMsg($msg,'','','greentext');
}
//--------------------------------------------------------------------------------------------批量设置结婚亲密度描述表
function SetMarryDescription()
{
	global $db;
	global $id_del,$id,$favor_value,$description;
	global $favor_value_n,$description_n;
	
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
					marry_description
					set
					`favor_value`='$favor_value[$i]',
					`description`='$description[$i]'
					where 
					id = '$id[$i]'
					");
			}
			
		}
	$msg = "更新成功！";
	}
	
	//-----------------增加记录-------------------------------------------
	if ($favor_value_n || $description_n)
	{
	
		$query = $db->query("
		insert into marry_description(
			`favor_value`,
			`description`
			) values (
			'$favor_value_n',
			'$description_n'
			)") ;
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
			$db->query("delete from marry_description where id = '$idArr[0]'");
		}
		$msg .= "<br />删除成功！";
	}
	
	showMsg($msg,'','','greentext');
	
	
}
//--------------------------------------------------------------------------------------------批量设置结婚技能效果表
function SetMarrySkillEffect()
{
	global $db;
	global $id_del,$id,$picture_id,$panel_id,$skill_id,$effect_value,$need_favor,$is_percent;
	global $picture_id_n,$panel_id_n,$skill_id_n,$effect_value_n,$need_favor_n,$is_percent_n;
	
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
					marry_skill_effect
					set
					`picture_id`='$picture_id[$i]',
					`panel_id`='$panel_id[$i]',
					`skill_id`='$skill_id[$i]',
					`effect_value`='$effect_value[$i]',
					`is_percent`='$is_percent[$i]',
					`need_favor`='$need_favor[$i]'
					where 
					id = '$id[$i]'
					");
			}
			
		}
	$msg = "更新成功！";
	}
	
	//-----------------增加记录-------------------------------------------
	if ($picture_id_n)
	{
		$query = $db->query("
		insert into marry_skill_effect(
			`picture_id`,
			`panel_id`,
			`skill_id`,
			`effect_value`,
			`is_percent`,
			`need_favor`	
			) values (
			'$picture_id_n',
			'$panel_id_n',
			'$skill_id_n',
			'$effect_value_n',
			'$is_percent_n',
			'$need_favor_n'	
			)") ;
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
			$db->query("delete from marry_skill_effect where id = '$idArr[0]'");
		}
		$msg .= "<br />删除成功！";
	}
	
	showMsg($msg,'','','greentext');
}