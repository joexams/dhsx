<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SetNPC': SetNPC();break;
	case 'NpcFunction': NpcFunction();break;
	case 'SetNpcFunction': SetNpcFunction();break;


	default:  NPC();
}
//--------------------------------------------------------------------------------------------NPC

function  NPC() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	$npc_function_list = globalDataList('npc_function');//NPC功能	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		npc
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			npc
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=npc");	
	}	
	include_once template('t_npc');
}

//--------------------------------------------------------------------------------------------批量设置NPC
function  SetNPC() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$dialog = ReqArray('dialog');
	$shop_name = ReqArray('shop_name');
	$player_dialog = ReqArray('player_dialog');
	$npc_func_id = ReqArray('npc_func_id');
	
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$dialog_n = ReqStr('dialog_n');
	$shop_name_n = ReqStr('shop_name_n');
	$player_dialog_n = ReqStr('player_dialog_n');
	$npc_func_id_n = ReqStr('npc_func_id_n');
		
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
					npc 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`dialog`='$dialog[$i]',
					`shop_name`='$shop_name[$i]',
					`player_dialog`='$player_dialog[$i]',
					`npc_func_id`='$npc_func_id[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			npc
			(`name`,`sign`,`dialog`,`shop_name`,`player_dialog`,`npc_func_id`) 
		values 
			('$name_n','$sign_n','$dialog_n','$shop_name_n','$player_dialog_n','$npc_func_id_n')
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
		$db->query("delete from npc where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------NPC功能

function  NpcFunction() 
{
	global $db; 
	$query = $db->query("
	select 
		*
	from 
		npc_function
	order by 
		id asc
	");
	if($db->num_rows($query))
	{	

		while($rs = $db->fetch_array($query))
		{	
			//$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
	}	
	include_once template('t_npc_function');
}

//--------------------------------------------------------------------------------------------批量设置NPC功能
function  SetNpcFunction() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
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
					npc_function 
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
			npc_function
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
		$db->query("delete from npc_function where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}			
	showMsg($msg,'','','greentext');	
}

?>