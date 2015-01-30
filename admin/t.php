<?php 
	session_start();
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(dirname(__FILE__)."/conn.php");
	webAdmin('t','','','web');
	if(KillBad('in')!='' && file_exists('t_'.KillBad('in').'.php'))
	{
		$mod = KillBad('in');
	}else{
		$mod = 'town';
	}
	//--------------------------------顶部导航-----------------------------------------------
	global $menu_db;
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
	//获取菜单
	$query = $menu_db->query("
	select 
		*
	from 
		ho_sys_menu
	where 
		father_id = 0 and status = 1 and level=1
	order by 
		id asc
	");
	$num = $menu_db->num_rows($query);
	if($num)
	{	
		while($rs = $menu_db->fetch_array($query))
		{	
			$second_menu_query = $menu_db->query("select `url`,`describe`,`id` as sid from ho_sys_menu where father_id=".$rs['id']." and status=1 order by id asc");
			$second_menu_list=array();
			$list_tarray=array();
			while ($second_rs = $menu_db->fetch_array($second_menu_query)) {
				$second_menu_list[] = $second_rs;
				$third_menu_query = $menu_db->query("select `url`,`describe` from ho_sys_menu where father_id=".$second_rs['sid']." and status=1 order by id asc");
				$third_menu_list=array();
				while ($third_rs = $menu_db->fetch_array($third_menu_query)) {
					$third_menu_list[] = $third_rs;
				}
				$second_rs['third_menu'] = $third_menu_list;
				$list_tarray[] = $second_rs;
			}
			$rs['second_menu'] =  $list_tarray;
			$list_array[] = $rs;
		}
	}

//----------------------------------------------------------------------------------------------------------------------------------------------
	$menu = "t_menu.php";
	$contentUrl = "t_".$mod.".php"; //单一入口识
	include_once template('t');
	
	include_once(dirname(__FILE__)."/bot.php");

?>