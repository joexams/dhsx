<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'Title': Title(); break;
	case 'RoleJob': RoleJob();break;
	case 'RoleStunt': RoleStunt();break;
	case 'RoleStuntType': RoleStuntType();break;
	//case 'RoleQquiPosition': RoleQquiPosition();break;
	case 'RoleAttackRange': RoleAttackRange();break;
	case 'SpecialPartner': SpecialPartner(); break;

	case 'SetRoleJob': SetRoleJob();break;
	case 'SetRoleStunt': SetRoleStunt();break;
	case 'SetRoleStuntType': SetRoleStuntType();break;
	case 'SetRole': SetRole();break;
	case 'SetRoleJobLevelData': SetRoleJobLevelData();break;
	//case 'SetRoleQquiPosition': SetRoleQquiPosition();break;
	case 'SetRoleAttackRange': SetRoleAttackRange();break;
	case 'SetTitle': SetTitle();	break;
	case 'SetSpecialPartner': SetSpecialPartner(); break;

	default:  Role();
}


//--------------------------------------------------------------------------------------------特殊伙伴信息
function SpecialPartner() {
	global $db; 
	$role_list = globalDataList('role', 'id>6');//角色信息
	$query = $db->query("
	select 
		*
	from 
		special_partner
	order by 
		role_id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_special_partner');
}


function SetSpecialPartner() {
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$role_id = ReqArray('role_id');
	$req_level = ReqArray('req_level');

	$role_id_n = ReqStr('role_id_n');
	$req_level_n = ReqStr('req_level_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $role_id[$i] && $req_level[$i])
			{

				$db->query("
				update 
					special_partner 
				set 
					`role_id`='$role_id[$i]',
					`req_level`='$req_level[$i]'
				where 
					role_id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($role_id_n  && $req_level_n)
	{
	
		$query = $db->query("
		insert into 
			special_partner
			(`role_id`,`req_level`) 
		values 
			('$role_id_n','$req_level_n')
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
		$db->query("delete from special_partner where role_id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}			
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------称号
function Title() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		title
	"),0);	
	if($num)
	{	
		$query = $db->query("
		select 
			*
		from 
			title
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
	
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=role&action=Title");			
	}	
	include_once template('t_title');
}

function SetTitle() {
		global $db; 
		$id_del = ReqArray('id_del');
		$id = ReqArray('id');
		$name = ReqArray('name');
		$sign = ReqArray('sign');
		$content = ReqArray('content');
		$type = ReqArray('type');
		$health = ReqArray('health');
		$award_coins = ReqArray('award_coins');
		$award_fame = ReqArray('award_fame');
		$award_attack = ReqArray('award_attack');
		$award_speed = ReqArray('award_speed');
		$day = ReqArray('day');

		$name_n = ReqStr('name_n');
		$sign_n = ReqStr('sign_n');
		$content_n = ReqStr('content_n');
		$type_n = ReqStr('type_n');
		$health_n = ReqStr('health_n');
		$award_coins_n = ReqStr('award_coins_n');
		$award_fame_n = ReqStr('award_fame_n');
		$award_attack_n = ReqStr('award_attack_n');
		$award_speed_n = ReqStr('award_speed_n');
		$day_n = ReqStr('day_n');
		
		//-----------------更新-------------------------------------------
		if ($id)
		{
		
			$id_num = count($id);

			for ($i=0;$i<=$id_num;$i++)	
			{
				if ($id[$i] && $name[$i] && $sign[$i])
				{

					$db->query("
					update 
						title 
					set 
						`name`='$name[$i]',
						`sign`='$sign[$i]',
						`content`='$content[$i]',
						`type`='$type[$i]',
						`health`='$health[$i]',
						`award_coins`='$award_coins[$i]',
						`award_fame`='$award_fame[$i]',
						`award_attack`='$award_attack[$i]',
						`award_speed`='$award_speed[$i]',
						`day`='$day[$i]'
					where 
						id = '$id[$i]'
					");
				}
				
			}
			$msg = "更新成功！";
		}
			
		//-----------------增加记录-------------------------------------------
		if ($name_n  && $sign_n)
		{
		
			$query = $db->query("
			insert into 
				title
				(`name`,`sign`,`content`,`type`,`health`,`award_coins`,`award_fame`,`award_attack`,`award_speed`,`day`) 
			values 
				('$name_n','$sign_n','$fcontent_n','$type_n','$health_n','$award_coins_n','$award_fame_n','$award_attack_n','$award_speed_n','$day_n')
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
			$db->query("delete from title where id in ($id_arr)");
			$msg .= "<br />删除成功！";
			
		}			
		showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------角色信息

function  Role() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	$role_job_list = globalDataList('role_job');//职业
	$role_stunt_list = globalDataList('role_stunt');//绝技
	$role_type=ReqNum('role_type');
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		role A 
	where
		role_type = '$role_type'
	"),0);	
	if($num)
	{	
		$query = $db->query("
		select 
			A.*,
			B.name as job_name,
			C.name as stunt_name
		from 
			role A
			left join role_job B on A.role_job_id = B.id
			left join role_stunt C on A.role_stunt_id = C.id
		where
			role_type = '$role_type'
		order by 
			`lock` asc,
			id asc
		limit 
			$start_num,$pageNum			
		");
	
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=role&role_type=$role_type");			
	}	
	include_once template('t_role');
}
//--------------------------------------------------------------------------------------------职业

function  RoleJob() 
{
	global $db;
	$role_stunt_type_list = globalDataList('role_stunt_type');//绝技类型
	$role_attack_range_list = globalDataList('role_attack_range');//攻击范围
	$query = $db->query("
	select 
		*
	from 
		role_job
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{
			$rs['name_url'] = urlencode($rs['name']);	
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_role_job');
}
//--------------------------------------------------------------------------------------------绝技

function  RoleStunt() 
{
	global $db,$page,$search_name; 
	
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum; 
	$role_stunt_type_list = globalDataList('role_stunt_type');//绝技类型
	$role_attack_range_list = globalDataList('role_attack_range');//攻击范围
	
	$where = '';
	if($search_name) $where .= "where `name` = '".$search_name."'";
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		role_stunt
	".$where),0);
	if($num)
	{	
		$query = $db->query("
		select 
			*
		from 
			role_stunt
		".$where." 
		order by 
			id asc
		limit 
			$start_num,$pageNum	
		");

		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=role&action=RoleStunt");
	}
	include_once template('t_role_stunt');
}
//--------------------------------------------------------------------------------------------绝技类型

function  RoleStuntType() 
{
	global $db; 
	$role_attack_range_list = globalDataList('role_attack_range');//攻击范围
	$query = $db->query("
	select 
		*
	from 
		role_stunt_type
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
	include_once template('t_role_stunt_type');
}
//--------------------------------------------------------------------------------------------攻击范围

function  RoleAttackRange() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		role_attack_range
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
	include_once template('t_role_attack_range');
}
/*//--------------------------------------------------------------------------------------------装备位置

function  RoleQquiPosition() 
{
	global $db; 
	$item_type_list = globalDataList('item_type','id<1000');//装备类型
	
	$query = $db->query("
	select 
		*
	from 
		role_equi_position
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
	include_once template('t_role_equi_position');
}*/
//--------------------------------------------------------------------------------------------批量设置职业
function  SetRoleJob() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$role_attack_range_id = ReqArray('role_attack_range_id');

	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$role_attack_range_id_n = ReqNum('role_attack_range_id_n');

	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from role_job where id in ($id_arr)");
		$db->query("delete from role_job_level_data where job_id in ($id_arr)");
		$msg = "删除成功！";
	}	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					role_job 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`role_attack_range_id`='$role_attack_range_id[$i]'
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
			role_job
			(`id`,`name`,`sign`,`role_attack_range_id`) 
		values 
			('$id_n','$name_n','$sign_n','$role_attack_range_id_n')
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
//--------------------------------------------------------------------------------------------批量设置绝技
function  SetRoleStunt() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$name2 = ReqArray('name2');
	$role_stunt_type_id = ReqArray('role_stunt_type_id');
	$role_attack_range_id = ReqArray('role_attack_range_id');
	$sign = ReqArray('sign');
	$description = ReqArray('description');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$name2_n = ReqStr('name2_n');
	$role_stunt_type_id_n = ReqStr('role_stunt_type_id_n');
	$role_attack_range_id_n = ReqStr('role_attack_range_id_n');
	$sign_n = ReqStr('sign_n');
	$description_n = ReqStr('description_n');
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from role_stunt where id in ($id_arr)");
		$msg = "删除成功！";
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					role_stunt 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`name2`='$name2[$i]',
					`sign`='$sign[$i]',
					`role_stunt_type_id`='$role_stunt_type_id[$i]',
					`role_attack_range_id`='$role_attack_range_id[$i]',
					`description`='$description[$i]'
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
			role_stunt
			(`id`,`name`,`name2`,`sign`,`role_stunt_type_id`,`role_attack_range_id`,`description`) 
		values 
			('$id_n','$name_n','$name2_n','$sign_n','$role_stunt_type_id_n','$role_attack_range_id_n','$description_n')
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


//--------------------------------------------------------------------------------------------批量设置绝技类型
function  SetRoleStuntType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from role_stunt_type where id in ($id_arr)");
		$msg = "删除成功！";
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					role_stunt_type 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			role_stunt_type
			(`id`,`name`,`sign`) 
		values 
			('$id_n','$name_n','$sign_n')
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

//--------------------------------------------------------------------------------------------批量设置攻击范围
function  SetRoleAttackRange() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	
	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from role_attack_range where id in ($id_arr)");
		$msg = "删除成功！";
	}	
				
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $id_old[$i] &&  $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					role_attack_range 
				set 
					`id`='$id[$i]',
					`name`='$name[$i]',
					`sign`='$sign[$i]'
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
			role_attack_range
			(`id`,`name`,`sign`) 
		values 
			('$id_n','$name_n','$sign_n')
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
//--------------------------------------------------------------------------------------------批量设置角色信息
function  SetRole() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$fees = ReqArray('fees');
	$ingot = ReqArray('ingot');
	$gender = ReqArray('gender');
	$role_job_id = ReqArray('role_job_id');
	$role_stunt_id = ReqArray('role_stunt_id');
	$strength = ReqArray('strength');
	$agile = ReqArray('agile');
	$intellect = ReqArray('intellect');
	$lock = ReqArray('lock');
	$fame = ReqArray('fame');
	$show_require_fame = ReqArray('show_require_fame');
	$tower_lock = ReqArray('tower_lock');
	$initial_health = ReqArray('initial_health');
        $is_recommend = ReqArray('is_recommend');
	$recommend_fame = ReqArray('recommend_fame');
	$fame_level_for_role = ReqArray('fame_level_for_role');
	$role_type = ReqArray('role_type');
	$introduction = ReqArray('introduction');
	$invite_type = ReqArray('invite_type');
	$state_point_need = ReqArray('state_point_need');
	$role_paradise_level = ReqArray('role_paradise_level');
	$need_lv = ReqArray('need_lv');
	$mission_monster_team = ReqArray('mission_monster_team');
	$favor_item_id = ReqArray('favor_item_id');
	$favor_item_val = ReqArray('favor_item_val');
	$win_condition = ReqArray('win_condition');
	$is_comments = ReqArray('is_comments');


	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$fees_n = ReqNum('fees_n');
	$ingot_n = ReqNum('ingot_n');
	$gender_n = ReqNum('gender_n');
	$role_job_id_n = ReqNum('role_job_id_n');
	$role_stunt_id_n = ReqNum('role_stunt_id_n');
	$strength_n = ReqNum('strength_n');
	$agile_n = ReqNum('agile_n');
	$intellect_n= ReqNum('intellect_n');
	$lock_n= ReqNum('lock_n');
	$fame_n= ReqNum('fame_n');
	$show_require_fame_n= ReqNum('show_require_fame_n');	
	$tower_lock_n= ReqNum('tower_lock_n');
	$initial_health_n= ReqNum('initial_health_n');
	$is_recommend_n= ReqNum('is_recommend_n');
	$recommend_fame_n= ReqNum('recommend_fame_n');
	$fame_level_for_role_n= ReqNum('fame_level_for_role_n');
	$role_type_n= ReqNum('role_type_n');
	$introduction_n = ReqStr('introduction_n');
	$invite_type_n = ReqNum('invite_type_n');
	$state_point_need_n = ReqNum('state_point_need_n');
	$role_paradise_level_n = ReqNum('role_paradise_level_n');
	$need_lv_n = ReqNum('need_lv_n');
	$mission_monster_team_n = ReqNum('mission_monster_team_n');
	$favor_item_id_n = ReqNum('favor_item_id_n');
	$favor_item_val_n = ReqNum('favor_item_val_n');
	$win_condition_n = ReqStr('win_condition_n');
	$is_comments_n = ReqStr('is_comments_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					role 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`fees`='$fees[$i]',
					`ingot`='$ingot[$i]',
					`gender`='$gender[$i]',
					`role_job_id`='$role_job_id[$i]',
					`role_stunt_id`='$role_stunt_id[$i]',
					`strength`='$strength[$i]',
					`agile`='$agile[$i]',
					`intellect`='$intellect[$i]',
					`lock`='$lock[$i]',
					`fame`='$fame[$i]',
					`show_require_fame`='$show_require_fame[$i]',
					`tower_lock`='$tower_lock[$i]',
					`initial_health`='$initial_health[$i]',
					`is_recommend`='$is_recommend[$i]',
					`recommend_fame`='$recommend_fame[$i]',
					`fame_level_for_role`='$fame_level_for_role[$i]',
	                `role_type`='$role_type[$i]',
	                `invite_type`='$invite_type[$i]',
	                `role_paradise_level`='$role_paradise_level[$i]',
	                `state_point_need`='$state_point_need[$i]',
	                `introduction`='$introduction[$i]',
	                `need_lv`='$need_lv[$i]',
	                `mission_monster_team`='$mission_monster_team[$i]',
	                `favor_item_id`='$favor_item_id[$i]',
	                `favor_item_val`='$favor_item_val[$i]',
	                `win_condition`='$win_condition[$i]',
	                `is_comments`='$is_comments[$i]'
				where 
					id = '$id[$i]'
				");
			}

		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n  && $sign_n)
	{
		$query = $db->query("
		insert into 
			role
			(`name`,`sign`,`fees`,`ingot`,`role_job_id`,`gender`,`role_stunt_id`,`strength`,`agile`,`intellect`,`lock`,`fame`,`show_require_fame`,`tower_lock`,`initial_health`,`is_recommend`,`recommend_fame`,`fame_level_for_role`,`role_type`,`introduction`,`invite_type`, `role_paradise_level`, `state_point_need`, `need_lv`, `mission_monster_team`, `favor_item_id`, `favor_item_val`, `win_condition`, `is_comments`) 
		values 
			('$name_n','$sign_n','$fees_n','$ingot_n','$role_job_id_n','$gender_n','$role_stunt_id_n','$strength_n','$agile_n','$intellect_n','$lock_n','$fame_n','$show_require_fame_n','$tower_lock_n','$initial_health_n','$is_recommend_n','$recommend_fame_n','$fame_level_for_role_n','$role_type_n','$introduction_n','$invite_type_n','$role_paradise_level_n','$state_point_need_n','$need_lv_n','$mission_monster_team_n','$favor_item_id_n','$favor_item_val_n','$win_condition_n','$is_comments_n')
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
		$db->query("delete from role where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}			
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置角色职业等级信息
function  SetRoleJobLevelData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$job_id = ReqArray('job_id');
	$level = ReqArray('level');
	$level_old = ReqArray('level_old');
	$require_exp = ReqArray('require_exp');
	$max_health = ReqArray('max_health');
	$attack = ReqArray('attack');
	$defense = ReqArray('defense');
	$stunt_attack = ReqArray('stunt_attack');
	$stunt_defense = ReqArray('stunt_defense');
	$magic_attack = ReqArray('magic_attack');
	$magic_defense = ReqArray('magic_defense');
	$critical = ReqArray('critical');
	$dodge = ReqArray('dodge');
	$hit = ReqArray('hit');
	$block = ReqArray('block');
	$break_critical = ReqArray('break_critical');
	$break_block = ReqArray('break_block');
	$kill = ReqArray('kill');
	$first_attack = ReqArray('first_attack');
	$speed = ReqArray('speed');

	$job_id_n = ReqNum('job_id_n');
	$level_n = ReqNum('level_n');
	$require_exp_n = ReqNum('require_exp_n');
	$max_health_n = ReqNum('max_health_n');
	$attack_n = ReqNum('attack_n');
	$defense_n = ReqNum('defense_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$critical_n = ReqNum('critical_n');
	$dodge_n = ReqNum('dodge_n');
	$hit_n = ReqNum('hit_n');
	$block_n = ReqNum('block_n');	
	$break_critical_n = ReqNum('break_critical_n');
	$break_block_n = ReqNum('break_block_n');
	$kill_n = ReqNum('kill_n');
	$first_attack_n = ReqNum('first_attack_n');
	$speed_n = ReqNum('speed_n');

	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($job_id)
	{
	
		$id_num = count($job_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($job_id[$i] && $level[$i] && $level_old[$i] && $require_exp[$i] && $max_health[$i])
			{

				$db->query("
				update 
					role_job_level_data 
				set 
					`level`='$level[$i]',
					`require_exp`='$require_exp[$i]',
					`max_health`='$max_health[$i]',
					`attack`='$attack[$i]',
					`defense`='$defense[$i]',
					`stunt_attack`='$stunt_attack[$i]',
					`stunt_defense`='$stunt_defense[$i]',
					`magic_attack`='$magic_attack[$i]',
					`magic_defense`='$magic_defense[$i]',
					`critical`='$critical[$i]',
					`dodge`='$dodge[$i]',
					`hit`='$hit[$i]',
					`block`='$block[$i]',
					`break_critical`='$break_critical[$i]',
					`break_block`='$break_block[$i]',
					`kill`='$kill[$i]',
					`first_attack`='$first_attack[$i]',
					`speed`='$speed[$i]'
					
				where 
					job_id = '$job_id[$i]'
				and 
					level = '$level_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($job_id_n && $level_n && $require_exp_n && $max_health_n)
	{
	
		$query = $db->query("
		insert into 
			role_job_level_data
			(`job_id`,`level`,`require_exp`,`max_health`,`attack`,`defense`,`stunt_attack`,`stunt_defense`,`magic_attack`,`magic_defense`,`critical`,`dodge`,`hit`,`block`,`break_critical`,`break_block`,`kill`,`first_attack`,`speed`) 
		values 
			('$job_id_n','$level_n','$require_exp_n','$max_health_n','$attack_n','$defense_n','$stunt_attack_n','$stunt_defense_n','$magic_attack_n','$magic_defense_n','$critical_n','$dodge_n','$hit_n','$block_n',		'$break_critical_n','$break_block_n','$kill_n','$first_attack_n','$speed_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from role_job_level_data where job_id = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


?>