<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SetMissionAreaMonsterTeam': SetMissionAreaMonsterTeam();break;
	case 'SetMissionAreaMonster': SetMissionAreaMonster();break;
	case 'Monster': Monster();break;
	case 'SetMonster': SetMonster();break;
	case 'SetMonsterSkill': SetMonsterSkill();break;
	default:  MissionAreaMonsterTeam();
}
function MissionAreaMonsterTeam()
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$mission_area = ReqStr('mission_area');
	$where = '';
	if ($mission_area != ''){
		$where = "where mission_area_id=".$mission_area;
		$page_str = "&mission_area=".$mission_area;
	}

	//代表怪物
	$query = $db->query('select a.id,b.text as name from monster a,chinese_text b where a.name_text_id=b.id');
	while($rs = $db->fetch_array($query)){
		$monster_list[] =  $rs;
	}
	//副本区域
	$query = $db->query("select a.id,concat(c.text,'-',b.level_id,'-',a.number) as name from mission_area a,mission_level b,chinese_text c,mission d where a.level_id=b.id and b.mission_id=d.id and  d.name_text_id=c.id");
	while($rs = $db->fetch_array($query)){
		$mission_area_list[] =  $rs;
	}
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mission_area_monster_team
	$where
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			mission_area_monster_team
		$where
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=monster&action=MissionAreaMonsterTeam");	
	}	
	include_once template('t_mission_area_monster_team');
}
function  SetMissionAreaMonsterTeam() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$monster_id = ReqArray('monster_id');
	$award_mission_key = ReqArray('award_mission_key');
	$award_exp = ReqArray('award_exp');
	$mission_area_id = ReqArray('mission_area_id');
	
	$monster_id_n = ReqNum('monster_id_n');
	$award_mission_key_n = ReqStr('award_mission_key_n');
	$award_exp_n = ReqNum('award_exp_n');
	$mission_area_id_n = ReqNum('mission_area_id_n');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mission_area_monster_team where id in ($id_arr)");
		$db->query("delete from mission_area_monster where mission_area_monster_team_id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $monster_id[$i])
			{

				$db->query("
				update 
					mission_area_monster_team 
				set 
					`monster_id`='$monster_id[$i]',
					`award_mission_key`='$award_mission_key[$i]',
					`award_exp`='$award_exp[$i]',
					`mission_area_id`='$mission_area_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($monster_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_area_monster_team
			(`id`,`monster_id`,`award_mission_key`,`award_exp`,`mission_area_id`) 
		values 
			('','$monster_id_n','$award_mission_key_n','$award_exp_n','$mission_area_id_n')
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

function  SetMissionAreaMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$mission_area_monster_team_id = ReqStr('mission_area_monster_team_id');
	$monster_id = ReqArray('monster_id');
	$pos = ReqArray('pos');
	
	$monster_id_n = ReqNum('monster_id_n');
	$pos_n = ReqStr('pos_n');
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mission_area_monster where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $monster_id[$i])
			{

				$db->query("
				update 
					mission_area_monster 
				set 
					`monster_id`='$monster_id[$i]',
					`pos`='$pos[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($monster_id_n)
	{
	
		$query = $db->query("
		insert into 
			mission_area_monster
			(`id`,`mission_area_monster_team_id`,`monster_id`,`pos`) 
		values 
			('','$mission_area_monster_team_id','$monster_id_n','$pos_n')
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
	$msg = urlencode($msg);
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}

function Monster()
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//角色
	$query = $db->query('select a.id,concat(substr(c.pinyin,1,1),a.xin_ji,a.jue_xing,b.text,a.xin_ji,"星",a.jue_xing,"觉") as name,a.first_skill,a.second_skill,a.third_skill,a.fourth_skill from role a,chinese_text b,hero c where a.hero_id=c.id and c.name_text_id=b.id order by b.text,a.jue_xing,a.xin_ji');
	while($rs = $db->fetch_array($query)){
		$role_list[] =  $rs; 
		$role[$rs["id"]]["first_skill"] = $rs["first_skill"];
		$role[$rs["id"]]["second_skill"] = $rs["second_skill"];
		$role[$rs["id"]]["third_skill"] = $rs["third_skill"];
		$role[$rs["id"]]["fourth_skill"] = $rs["fourth_skill"];
	}
	//技能
	$query = $db->query('select a.skill_number,b.lv,CONCAT(c.text,b.lv) as name,a.id as skill_id from skill_base a,skill_lv b,chinese_text c where a.id=b.skill_base_id and a.name_text_id=c.id');
	while($rs = $db->fetch_array($query)){
		switch ($rs["skill_number"]){
			case 1:
				$first_skill_list[] = $rs;
				break;
			case 2:
				$second_skill_list[] = $rs;
				break;
			case 3:
				$third_skill_list[] = $rs;
				break;
			case 4:
				$four_skill_list[] = $rs;
				break;
		}
	}
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		monster
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			monster
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=monster&action=Monster");	
	}	
	$chinese_text = globalChineseList();
	include_once template('t_monster');
}

function  SetMonster() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$role_id = ReqArray('role_id');
	$name_text_id = ReqArray('name_text_id');
	$role_lv = ReqArray('role_lv');
	$sign = ReqArray('sign');
	$attack_add = ReqArray('attack_add');
	$defense_add = ReqArray('defense_add');
	$health_add = ReqArray('health_add');
	
	$first_skill_lv_id = ReqArray('first_skill_lv_id');
	$second_skill_lv_id = ReqArray('second_skill_lv_id');
	$third_skill_lv_id = ReqArray('third_skill_lv_id');
	$fourth_skill_lv_id = ReqArray('fourth_skill_lv_id');
	
	$role_id_n = ReqStr('role_id_n');
	$name_text_id_n = chinese_text(ReqStr('name_text_id_n'));
	$role_lv_n = ReqStr('role_lv_n');
	$sign_n = ReqStr('sign_n');
	$attack_add_n = ReqNum('attack_add_n');
	$defense_add_n = ReqNum('defense_add_n');
	$health_add_n = ReqNum('health_add_n');
	
	$first_skill_lv_id_n = ReqNum('first_skill_lv_id_n');
	$second_skill_lv_id_n = ReqNum('second_skill_lv_id_n');
	$third_skill_lv_id_n = ReqNum('third_skill_lv_id_n');
	$fourth_skill_lv_id_n = ReqNum('fourth_skill_lv_id_n');
	
	//伙伴属性
	$query = $db->query('select id,attack,attack_add,defense,defense_add,health,health_add from role');
	while($rs = $db->fetch_array($query)){
		$role_list[$rs['id']] =  $rs;
	}
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from monster where id in ($id_arr)");
		$msg = "删除成功！";
	}	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{
				$attack = $role_list[$role_id[$i]]["attack"]+($role_list[$role_id[$i]]["attack_add"]*($role_lv[$i]-1))+$attack_add[$i];
				$defense = $role_list[$role_id[$i]]["defense"]+($role_list[$role_id[$i]]["defense_add"]*($role_lv[$i]-1))+$defense_add[$i];
				$health = $role_list[$role_id[$i]]["health"]+($role_list[$role_id[$i]]["health_add"]*($role_lv[$i]-1))+$health_add[$i];
				$name_text_id[$i] = chinese_text($name_text_id[$i]);
				$db->query("
				update 
					monster 
				set 
					`role_id`='$role_id[$i]',
					`name_text_id`='$name_text_id[$i]',
					`role_lv`='$role_lv[$i]',
					`sign`='$sign[$i]',
					`attack_add`='$attack_add[$i]',
					`defense_add`='$defense_add[$i]',
					`health_add`='$health_add[$i]',
					`attack`='$attack',
					`defense`='$defense',
					`health`='$health',
					`first_skill_lv_id`='$first_skill_lv_id[$i]',
					`second_skill_lv_id`='$second_skill_lv_id[$i]',
					`third_skill_lv_id`='$third_skill_lv_id[$i]',
					`fourth_skill_lv_id`='$fourth_skill_lv_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($role_id_n>0)
	{
		$attack = $role_list[$role_id_n]["attack"]+($role_list[$role_id_n]["attack_add"]*($role_lv_n-1))+$attack_add_n;
		$defense = $role_list[$role_id_n]["defense"]+($role_list[$role_id_n]["defense_add"]*($role_lv_n-1))+$defense_add_n;
		$health = $role_list[$role_id_n]["health"]+($role_list[$role_id_n]["health_add"]*($role_lv_n-1))+$health_add_n;
		$query = $db->query("
		insert into monster			(`id`,`role_id`,`name_text_id`,`role_lv`,`sign`,`attack_add`,`defense_add`,`health_add`,`attack`,`defense`,`health`,`first_skill_lv_id`,`second_skill_lv_id`,`third_skill_lv_id`,`fourth_skill_lv_id`) 
		values 			('','$role_id_n','$name_text_id_n','$role_lv_n','$sign_n','$attack_add_n','$defense_add_n','$health_add_n','$attack','$defense','$health','$first_skill_lv_id_n','$second_skill_lv_id_n','$third_skill_lv_id_n','$fourth_skill_lv_id_n')
		");
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

function  SetMonsterSkill() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id_old = ReqArray('id_old');
	$monster_id = ReqStr('monster_id');
	$skill_id = ReqArray('skill_id');
	
	$skill_id_n = ReqNum('skill_id_n');
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from monster_skill where id in ($id_arr)");
		$msg = "删除成功！";
		
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $skill_id[$i])
			{

				$db->query("
				update 
					monster_skill 
				set 
					`skill_id`='$skill_id[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($skill_id_n)
	{
	
		$query = $db->query("
		insert into 
			monster_skill
			(`id`,`skill_id`,`monster_id`) 
		values 
			('','$skill_id_n','$monster_id')
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
	$msg = urlencode($msg);
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);
}