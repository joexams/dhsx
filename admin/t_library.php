<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'LibraryStuntRelation': LibraryStuntRelation();break;
	case 'SetLibraryStuntRelation': SetLibraryStuntRelation();break;
	case 'SetLibraryLevelWarAttr': SetLibraryLevelWarAttr();break;
	default:  LibraryLevelWarAttr();
}


//--------------------------------------------------------------------------------------------藏经阁绝招对应关系

function  LibraryStuntRelation() 
{
	global $db,$page; 
	
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$role_stunt_list = globalDataList('role_stunt');
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(1) 
	from 
		library_stunt_relation
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			A.*,
			B.name as role_stunt_name
		from 
			library_stunt_relation A
			left join role_stunt B on A.role_stunt_id = B.id
		order by 
			A.role_stunt_id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=library&action=LibraryStuntRelation");	

	}	
	include_once template('t_library_stunt_relation');
}

//--------------------------------------------------------------------------------------------藏经阁等级属性

function  LibraryLevelWarAttr() 
{
	global $db,$page; 
	
	$role_job_id = ReqNum('role_job_id') ? ReqNum('role_job_id') : 1;
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$role_job_list = globalDataList('role_job');
	$role_stunt_list = globalDataList('role_stunt');
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(1) 
	from 
		library_level_war_attr
	where 
		role_job_id = '$role_job_id'
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			library_level_war_attr
		where 
			role_job_id = '$role_job_id'
			
		order by 
			library_level asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=library&role_job_id=$role_job_id");	

	}	
	include_once template('t_library_level_war_attr');
}


//--------------------------------------------------------------------------------------------批量设置藏经阁等级属性
function  SetLibraryLevelWarAttr() 
{
	global $db; 
	global $id_del,$role_job_id,$role_stunt_id,$library_level,$need_xian_ling,$need_player_lavel,$need_wusheng_lib_lv,$need_jianling_lib_lv,$need_feiyu_lib_lv,$strength,$agile,$intellect,$health,$attack,$defense,$magic_attack,$magic_defense,$stunt_attack,$stunt_defense,$hit,$block,$dodge,$critical,$break_block,$break_critical,$kill,$protect; 
	global $role_stunt_id_n,$role_job_id_n,$library_level_n,$need_xian_ling_n,$need_player_lavel_n,$need_wusheng_lib_lv_n,$need_jianling_lib_lv_n,$need_feiyu_lib_lv_n,$strength_n,$agile_n,$intellect_n,$health_n,$attack_n,$defense_n,$magic_attack_n,$magic_defense_n,$stunt_attack_n,$stunt_defense_n,$hit_n,$block_n,$dodge_n,$critical_n,$break_block_n,$break_critical_n,$kill_n,$protect_n; 
	

	//-----------------更新-------------------------------------------
	if ($library_level)
	{
	
		$id_num = count($library_level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($library_level[$i])
			{

				$db->query("
				update 
					library_level_war_attr 
				set 
					`role_stunt_id`='$role_stunt_id[$i]',
					`need_xian_ling`='$need_xian_ling[$i]',
					`need_player_lavel`='$need_player_lavel[$i]',
					`need_wusheng_lib_lv`='$need_wusheng_lib_lv[$i]',
					`need_jianling_lib_lv`='$need_jianling_lib_lv[$i]',
					`need_feiyu_lib_lv`='$need_feiyu_lib_lv[$i]',
					`strength`='$strength[$i]',
					`agile`='$agile[$i]',
					`intellect`='$intellect[$i]',
					`health`='$health[$i]',
					`attack`='$attack[$i]',
					`defense`='$defense[$i]',
					`magic_attack`='$magic_attack[$i]',
					`magic_defense`='$magic_defense[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`hit`='$hit[$i]',
					`block`='$block[$i]',
					`dodge`='$dodge[$i]',
					`critical`='$critical[$i]',
					`break_block`='$break_block[$i]',
					`break_critical`='$break_critical[$i]',
					`kill`='$kill[$i]',
					`protect`='$protect[$i]'
				where 
					role_job_id = '$role_job_id[$i]'
					and library_level = '$library_level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($library_level_n)
	{
	
		$query = $db->query("
		insert into library_level_war_attr(
			`role_job_id`,
			`role_stunt_id`,
			`library_level`,
			`need_xian_ling`,
			`need_player_lavel`,
			`need_wusheng_lib_lv`,
			`need_jianling_lib_lv`,
			`need_feiyu_lib_lv`,
			`strength`,
			`agile`,
			`intellect`,
			`health`,
			`attack`,
			`defense`,
			`magic_attack`,
			`magic_defense`,
			`stunt_attack`,
			`stunt_defense`,
			`hit`,
			`block`,
			`dodge`,
			`critical`,
			`break_block`,
			`break_critical`,
			`kill`,
			`protect`
		) values (
			'$role_job_id_n',
			'$role_stunt_id_n',
			'$library_level_n',
			'$need_xian_ling_n',
			'$need_player_lavel_n',
			'$need_wusheng_lib_lv_n',
			'$need_jianling_lib_lv_n',
			'$need_feiyu_lib_lv_n',
			'$strength_n',
			'$agile_n',
			'$intellect_n',
			'$health_n',
			'$attack_n',
			'$defense_n',
			'$magic_attack_n',
			'$magic_defense_n',
			'$stunt_attack_n',
			'$stunt_defense_n',
			'$hit_n',
			'$block_n',
			'$dodge_n',
			'$critical_n',
			'$break_block_n',
			'$break_critical_n',
			'$kill_n',
			'$protect_n'
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
			$db->query("delete from library_level_war_attr where role_job_id = '$idArr[0]' and library_level = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}



//--------------------------------------------------------------------------------------------批量设置绝招对应关系
function  SetLibraryStuntRelation() 
{
	global $db; 
	global $role_stunt_id,$next_role_stunt_id,$id_del; 
	global $role_stunt_id_n,$next_role_stunt_id_n; 
	//-----------------更新-------------------------------------------
	if ($role_stunt_id)
	{
	
		$id_num = count($role_stunt_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($role_stunt_id[$i] && $next_role_stunt_id[$i])
			{

				$db->query("
				update 
					library_stunt_relation 
				set 
					`next_role_stunt_id`='$next_role_stunt_id[$i]'
				where 
					role_stunt_id = '$role_stunt_id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($role_stunt_id_n && $next_role_stunt_id_n)
	{
	
		$query = $db->query("
		insert into 
			library_stunt_relation
			(`role_stunt_id`,`next_role_stunt_id`) 
		values 
			('$role_stunt_id_n','$next_role_stunt_id_n')
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
		$db->query("delete from library_stunt_relation where role_stunt_id in ($id_arr)");
		$msg = "<br />删除成功！";
		
	}		
	showMsg($msg,'','','greentext');	
}



?>