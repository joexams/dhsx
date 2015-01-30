<?php 
	include_once(dirname(__FILE__)."/config.inc.php");
	include_once(dirname(__FILE__)."/conn.php");
	$param = $_GET['term'];
	global $menu_db;
	$query = $menu_db->query("select `describe` as label,url from ho_sys_menu where `describe` like '%$param%' and `level`=3 and status=1 order by id asc");
	$list_array=array();
	while($rs = $menu_db->fetch_array($query))
		{	
			$list_array[] = $rs;
		}
	echo json_encode($list_array);
?>