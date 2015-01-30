<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'Mounts': Mounts();break;
	case 'SetMounts': SetMounts();break;
	case 'MountsAttribute': MountsAttribute();break;
	case 'SetMountsAttribute': SetMountsAttribute();break;

}

//--------------------------------------------------------------------------------------------坐骑
function MountsAttribute() {
	global $db,$page; 
	global $mounts_id;
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	$mounts_list = globalDataList('mounts');
	$wherestr = '';
	$mounts_id = $mounts_id > 0 ? $mounts_id : 1;
	if ($mounts_id > 0) {
		$wherestr = 'where mounts_id='.$mounts_id;
	}

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mounts_attribute 
	$wherestr
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			mounts_attribute 
		$wherestr
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mounts&action=MountsAttribute");	

	}	
	include_once template('t_mounts_attribute');
}


//--------------------------------------------------------------------------------------------坐骑
function Mounts() {
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item', 'type_id in (7)');

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		mounts
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			mounts
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=mounts&action=Mounts");	

	}	
	include_once template('t_mounts');
}

//--------------------------------------------------------------------------------------------坐骑录入
function SetMounts() {
	global $db;
	global $id_del, $id_old, $id, $name, $describe, $come_from, $item_id, $item_spirit, $display;
	global $id_n, $name_n, $describe_n, $come_from_n, $item_id_n, $item_spirit_n, $display_n;
	
	if ($id_old){
		$id_num = count($id_old);
		if ($id_num > 0){
			for ($i=0;$i<=$id_num;$i++)	{
				if ($name[$i]){
					$db->query("
					update 
						mounts 
					set 
						`name`='$name[$i]',
						`describe`='$describe[$i]',
						`come_from`='$come_from[$i]',
						`item_id`='$item_id[$i]',
						`display`='$display[$i]',
						`item_spirit`='$item_spirit[$i]'
						
					where 
						id = '$id_old[$i]'
					");
				}
			}
			$msg = "更新成功！";
		}
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
		$query = $db->query("
		insert into 
			mounts(
			`name`,
			`come_from`,
			`item_id`,
			`item_spirit`,
			`display`,
			`describe`	
		) values (
			'$name_n',
			'$come_from_n',
			'$item_id_n',
			'$item_spirit_n',
			'$display_n',
			'$describe_n'
			)
		") ;
		if($query){
			$msg .= "<br />增加成功！";
		}else{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del){
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mounts where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


function SetMountsAttribute(){
	global $db; 
	global $id_del, $id_old, $mounts_id, $lv, $next_lv_exp, $health, $attack, $defense, $magic_attack, $magic_defense, $stunt_attack, $stunt_defense, $hit, $block, $dodge, $critical, $momentum, $break_block, $break_critical, $kill, $speed; 
	global $mounts_id_n, $lv_n, $next_lv_exp_n, $health_n, $attack_n, $defense_n, $magic_attack_n, $magic_defense_n, $stunt_attack_n, $stunt_defense_n, $hit_n, $block_n, $dodge_n, $critical_n, $mome_n, $break_block_n, $break_critical_n, $kill_n, $speed_n; 

		
	//-----------------更新-------------------------------------------
	if ($id_old){
		$id_num = count($id_old);
		if ($id_num > 0){
			for ($i=0;$i<=$id_num;$i++)	{
				if ($mounts_id[$i]) {
					$db->query("
					update 
						mounts_attribute 
					set 
					`mounts_id`='$mounts_id[$i]',
					`lv`='$lv[$i]',
					`next_lv_exp`='$next_lv_exp[$i]',
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
					`momentum`='0',
					`break_block`='$break_block[$i]',
					`break_critical`='$break_critical[$i]',
					`kill`='$kill[$i]',
					`speed`='$speed[$i]'
					where 
						id = '$id_old[$i]'
					");
				}
			}
			$msg = "更新成功！";
		}
	}
		
	//-----------------增加记录-------------------------------------------
	if ($mounts_id_n)
	{
		$query = $db->query("
		insert into 
			mounts_attribute(
			`mounts_id`,
			`lv`,
			`next_lv_exp`,
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
			`momentum`,		
			`break_block`,
			`break_critical`,
			`kill`,
			`speed`
		) values (
			'$mounts_id_n',
			'$lv_n',
			'$next_lv_exp_n',
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
			'0',
			'$break_block_n',
			'$break_critical_n',	
			'$kill_n',		
			'$speed_n'			
			)
		") ;
		if($query){
			$msg .= "<br />增加成功！";
		}else{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del){
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from mounts_attribute where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}