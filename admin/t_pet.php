<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'PetAnimalAward': PetAnimalAward();break;
	case 'SetPetAnimal': SetPetAnimal();break;
	case 'SetPetAnimalStage': SetPetAnimalStage();break;
	case 'SetPetAnimalAward': SetPetAnimalAward();break;
	case 'AttributeMounts': AttributeMounts();break;
	case 'SetAttributeMounts': SetAttributeMounts();break;
	case 'Pet': Pet(); break;
	case 'SetPet': SetPet(); break;


	default:  PetAnimal();
}
//--------------------------------------------------------------------------------------------宠物信息表
function Pet() {
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("select count(*) from pet"),0);
	if($num){			
		$query = $db->query("select * from pet limit $start_num,$pageNum");
		while($rs = $db->fetch_array($query)){
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=pet&action=Pet");	
	}	
	include_once template('t_pet');
}

//--------------------------------------------------------------------------------------------设置宠物信息表
function SetPet() {
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$id_old = ReqArray('id_old');
	$name = ReqArray('name');
	$description = ReqArray('description');
	
	$name_n = ReqStr('name_n');
	$description_n = ReqStr('description_n');
	//-----------------更新-------------------------------------------

	if ($id_old){
		$id_num = count($id_old);
		if ($id_num > 0){
			for ($i=0;$i<=$id_num;$i++)	{
				if ($name[$i]){
					$db->query("
					update 
						pet 
					set 
						`name`='$name[$i]',
						`description`='$description[$i]'
						
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
			pet(
			`name`,
			`description`	
		) values (
			'$name_n',
			'$description_n'
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
		$db->query("delete from pet where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');
}

//--------------------------------------------------------------------------------------------坐骑属性
function AttributeMounts() {
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		

	$item_list = globalDataList('item','type_id=7');//坐骑物品
	//------------------------------------------------------------
	$num = $db->result($db->query("select count(*) from attribute_mounts"),0);
	if($num){			
		$query = $db->query("select * from attribute_mounts order by item_id asc limit $start_num,$pageNum");
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=pet&action=AttributeMounts");	
	}	
	include_once template('t_attribute_mounts');
}

//--------------------------------------------------------------------------------------------宠物奖励

function  PetAnimalAward(){
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("select count(*) from pet_animal_award"),0);
	if($num){			
		$query = $db->query("select * from pet_animal_award	order by id asc	limit $start_num,$pageNum");
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=pet&action=PetAnimalAward");	
	}
	include_once template('t_pet_animal_award');
}
//--------------------------------------------------------------------------------------------宠物

function  PetAnimal(){
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("select count(*) from pet_animal"),0);
	if($num){			
		$query = $db->query("select * from pet_animal order by lv asc limit $start_num,$pageNum");
		while($rs = $db->fetch_array($query)){	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=pet");	
	}	
	include_once template('t_pet_animal');
}

//--------------------------------------------------------------------------------------------设置坐骑属性
function SetAttributeMounts() {
	global $db; 
	global $id_del, $id_old, $item_id, $health, $attack, $defense, $magic_attack, $magic_defense, $stunt_attack, $stunt_defense, $hit, $block, $dodge, $critical, $momentum, $break_block, $break_critical, $kill, $speed; 
	global $item_id_n, $health_n, $attack_n, $defense_n, $magic_attack_n, $magic_defense_n, $stunt_attack_n, $stunt_defense_n, $hit_n, $block_n, $dodge_n, $critical_n, $mome_n, $break_block_n, $break_critical_n, $kill_n, $speed_n; 

		
	//-----------------更新-------------------------------------------
	if ($id_old){
		$id_num = count($id_old);
		if ($id_num > 0){
			for ($i=0;$i<=$id_num;$i++)	{
				if ($item_id[$i]) {
					$db->query("
					update 
						attribute_mounts 
					set 
					`item_id`='$item_id[$i]',
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
						item_id = '$id_old[$i]'
					");
				}
			}
			$msg = "更新成功！";
		}
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
		$query = $db->query("
		insert into 
			attribute_mounts(
			`item_id`,
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
			'$item_id_n',
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
		$db->query("delete from attribute_mounts where item_id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');

}

//--------------------------------------------------------------------------------------------批量设置宠物
function  SetPetAnimal(){
	global $db; 
	$id_del = ReqArray('id_del');
	$lv = ReqArray('lv');
	$lv_old = ReqArray('lv_old');
	$name = ReqArray('name');
	$color = ReqArray('color');
	$player_lv = ReqArray('player_lv');
	$attack = ReqArray('attack');
	$magic_attack = ReqArray('magic_attack');
	$stunt_attack = ReqArray('stunt_attack');
	$critical = ReqArray('critical');
	$dodge = ReqArray('dodge');
	$block = ReqArray('block');
	$break_critical = ReqArray('break_critical');
	$break_block = ReqArray('break_block');
	$hit = ReqArray('hit');
	$kill = ReqArray('kill');
	$health = ReqArray('health');
	$defense = ReqArray('defense');
	$magic_defense = ReqArray('magic_defense');
	$stunt_defense = ReqArray('stunt_defense');
	
	
	
	$lv_n = ReqNum('lv_n');
	$name_n = ReqStr('name_n');
	$color_n = ReqStr('color_n');
	$player_lv_n = ReqNum('player_lv_n');
	$attack_n = ReqNum('attack_n');
	$magic_attack_n = ReqNum('magic_attack_n');
	$stunt_attack_n = ReqNum('stunt_attack_n');
	$critical_n = ReqNum('critical_n');
	$dodge_n = ReqNum('dodge_n');
	$block_n = ReqNum('block_n');
	$break_critical_n = ReqNum('break_critical_n');
	$break_block_n = ReqNum('break_block_n');
	$hit_n = ReqNum('hit_n');
	$kill_n = ReqNum('kill_n');		
	$health_n = ReqNum('health_n');
	$defense_n = ReqNum('defense_n');
	$magic_defense_n = ReqNum('magic_defense_n');
	$stunt_defense_n = ReqNum('stunt_defense_n');
	//-----------------更新-------------------------------------------
	//
	if ($lv_old){
		$id_num = count($lv_old);
		if ($id_num > 0){
			foreach ($lv_old as $key => $value) {
				if ($lv[$value] && $name[$value] && $color[$value]){
					$db->query("
					update 
						pet_animal 
					set 
						`lv`='$lv[$value]',
						`name`='$name[$value]',
						`color`='$color[$value]',
						`player_lv`='$player_lv[$value]',
						`attack`='$attack[$value]',
						`magic_attack`='$magic_attack[$value]',
						`stunt_attack`='$stunt_attack[$value]',
						`critical`='$critical[$value]',
						`dodge`='$dodge[$value]',
						`block`='$block[$value]',
						`break_critical`='$break_critical[$value]',
						`break_block`='$break_block[$value]',
						`hit`='$hit[$value]',
						`kill`='$kill[$value]',
						`health`='$health[$value]',
						`defense`='$defense[$value]',
						`magic_defense`='$magic_defense[$value]',
						`stunt_defense`='$stunt_defense[$value]'
						
					where 
						lv = '$value'
					");
				}
			}
			$msg = "更新成功！";
		}
	}
		
	//-----------------增加记录-------------------------------------------
	if ($lv_n && $name_n && $color_n)
	{
	
		$query = $db->query("
		insert into 
			pet_animal(
			`lv`,
			`name`,
			`color`,
			`player_lv`,
			`attack`,
			`magic_attack`,
			`stunt_attack`,
			`critical`,
			`dodge`,
			`block`,
			`break_critical`,
			`break_block`,
			`hit`,
			`kill`,
			`health`,
			`defense`,
			`magic_defense`,
			`stunt_defense`			
		) values (
			'$lv_n',
			'$name_n',
			'$color_n',
			'$player_lv_n',
			'$attack_n',
			'$magic_attack_n',
			'$stunt_attack_n',
			'$critical_n',
			'$dodge_n',
			'$block_n',
			'$break_critical_n',
			'$break_block_n',
			'$hit_n',
			'$kill_n',
			'$health_n',
			'$defense_n',
			'$magic_defense_n',
			'$stunt_defense_n'			
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
		$db->query("delete from pet_animal where lv in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设置宠物阶段信息表
function  SetPetAnimalStage(){
	global $db; 
	$id_del = ReqArray('id_del');
	$lv = ReqArray('lv');
	$stage = ReqArray('stage');
	$exp = ReqArray('exp');

	$lv_n = ReqNum('lv_n');
	$stage_n = ReqNum('stage_n');
	$exp_n = ReqStr('exp_n');
	
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');	

	
	//-----------------更新-------------------------------------------
	if ($lv){
		$id_num = count($lv);
		for ($i=0;$i<=$id_num;$i++)	{
			if ($lv[$i] && $stage[$i] && $exp[$i]){

				$db->query("update pet_animal_stage set `exp`='$exp[$i]' where pet_animal_lv = '$lv[$i]' and stage = '$stage[$i]'");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($lv_n && $stage_n ){
	
		$query = $db->query("insert into pet_animal_stage (`stage`,`exp`,`pet_animal_lv`) values ('$stage_n','$exp_n','$lv_n')") ;
		if($query){
			$msg .= " 增加成功！";
		}else{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del){
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from pet_animal_stage where pet_animal_lv = '$idArr[0]' and stage = '$idArr[1]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}



//--------------------------------------------------------------------------------------------批量设置宠物奖励
function  SetPetAnimalAward() {
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$ingot = ReqArray('ingot');
	$coin = ReqArray('coin');
	$exp = ReqArray('exp');
	$lv = ReqArray('lv');
	
	$name_n = ReqStr('name_n');
	$ingot_n = ReqNum('ingot_n');
	$coin_n = ReqNum('coin_n');
	$exp_n = ReqNum('exp_n');
	$lv_n = ReqNum('lv_n');
		
	//-----------------更新-------------------------------------------
	//
	if ($id){
		$id_num = count($id);
		if ($id_num > 0){
			foreach ($id as $key => $value) {
				$db->query("update pet_animal_award set `name`='$name[$value]', `ingot`='$ingot[$value]', `coin`='$coin[$value]', `exp`='$exp[$value]',	`lv`='$lv[$value]' where id = '$value'");
			}
			$msg = "更新成功！";
		}
	}

	//-----------------增加记录-------------------------------------------
	if ($name_n){
	
		$query = $db->query("insert into pet_animal_award(`name`,`ingot`,`coin`,`exp`,`lv`) values ('$name_n','$ingot_n','$coin_n','$exp_n','$lv_n')") ;
		if($query){
			$msg .= "<br />增加成功！";
		}else{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del){
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from pet_animal_award where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}