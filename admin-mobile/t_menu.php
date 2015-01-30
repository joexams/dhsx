<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

	$show_menu_admin = false;
	if (getIp() == '192.168.24.94'){
		$show_menu_admin = true;
	}
	global $menu_db; 
	if(KillBad('in')!='' && file_exists('t_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'town';
	}
	if ($mod == 'common'){
		$table_name = KillBad('table');
		$father_id = $menu_db->result_first("select a.father_id from `ho_sys_menu` a,`ho_sys_menu` b where a.id=b.father_id and b.table_name='$table_name'");
	}else{
	$url = '?in='.$mod;
	$father_info = $menu_db->fetch_first("SELECT a.id,a.level,a.father_id FROM `ho_sys_menu` a WHERE a.url like '$url%' order by `level` limit 1");
	switch ($father_info['level'])
	{
		case 1:
			$father_id = $father_info['id'];
			break;
		case 3:
			$f_id = $father_info['father_id'];
			$father_id = $menu_db->result_first("SELECT a.father_id FROM `ho_sys_menu` a WHERE a.id='$f_id'");
			break;
	}
	}
	$where = LEFT_MENU_SHOW_ALL?" `level`=2 and status=1 ":" `level`=2 and status=1 and father_id='$father_id' ";
	$query = $menu_db->query("
	select 
		*
	from 
		ho_sys_menu
	where 
		$where
	order by 
		id asc
	");
	$num = $menu_db->num_rows($query);
	if($num)
	{	
		$list_array=array();
		while($rs = $menu_db->fetch_array($query))
		{	
			$second_menu_query = $menu_db->query("select `url`,`describe` from ho_sys_menu where father_id=".$rs['id']." and status=1 order by id asc");
			$second_menu_list=array();
			while ($second_rs = $menu_db->fetch_array($second_menu_query)) {
				$second_menu_list[] = $second_rs;
			}
			
			$rs['second_menu'] =  $second_menu_list;
			$list_array[] = $rs;
			
		}
	}
	include_once template('t_menu');

?>