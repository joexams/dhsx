<?php
include_once(dirname(__FILE__)."/config.inc.php");
include_once(dirname(__FILE__)."/conn.php");
if (!$adminWebID)
{
	exit();		
}
switch (ReqStr('action'))
{
	case 'CallPayExport': webAdmin('data_export');CallPayExport();break;
	case 'CallServersExport': CallServersExport();break;
	case 'CallCodeExport': CallCodeExport();break;
}

function  CallServersExport() {

	global $db,$adminWebType,$adminWebCid;
	$sid = ReqNum('sid');
	$cid = ReqNum('cid');
	$source = ReqStr('source');
	$role = ReqStr('role');
	$usernameArr = ReqStr('usernameArr');
	$stime = ReqStr('stime');
	$etime = ReqStr('etime');
	$vip_s = ReqNum('vip_s');
	$vip_e = ReqNum('vip_e');
	$level_s = ReqNum('level_s');
	$level_e = ReqNum('level_e');
	$date = date('Y-m-d(His)');
	
	if(!$cid || !$sid){
		echo '错误参数！';
		return;	
	}	
	if($adminWebType == 'c'){
		$adminCidArr =  $adminWebCid ? explode(',',$adminWebCid) : array();
		if($cid && !in_array($cid,$adminCidArr)){//如果服务器不属于此运营商
			echo '你没有权限！';
			return;
		}		
				
	}elseif($adminWebType == 'u'){
		echo '你没有权限！';
		return;			
	}
	//-------------------------------------------------------------------------------------------------------------
	if ($source) 
	{
		$set_source = "and D.source = '$source'";
		$source_title = "(渠道".$source.")";
	}	

	if ($stime && $etime) 
	{
		$stime_s = strtotime($stime.' 00:00:00');
		$etime_e = strtotime($etime.' 23:59:59');

		$set_time = "AND D.first_login_time >= '$stime_s' AND D.first_login_time <= '$etime_e'";
		$time_title = "(注册：".$stime."至".$etime.")";
	}
	if ($vip_s && $vip_e) 
	{
		if ($vip_s == 999) 
		{
			$set_vip = " and C.total_ingot > 0 and A.vip_level <= '$vip_e'";
			$vip_title = "(未达VIP1-VIP".$vip_e.")";
		
		}else{

			$set_vip = " and A.vip_level >= '$vip_s' and A.vip_level <= '$vip_e'";
			$vip_title = "(VIP".$vip_s."-VIP".$vip_e.")";
		}
	}
	if ($level_s && $level_e) 
	{
		$set_level = " and B.level >= '$level_s' and B.level <= '$level_e'";
		$level_title = "(".$level_s."级-".$level_e."级)";
	}
/*	echo $vip_s ;
	echo $vip_e ;
	echo $level_s ;
	echo $level_e ;
	exit();	*/
	
	if($role == 1)
	{
		$set_role = " and A.nickname <> ''";
		$role_title = "有创建角色";
	}elseif($role == 2){
		$set_role = " and A.nickname = ''";
		$role_title = "未创建角色";
	}		
	if ($usernameArr) 
	{
		$username_arr = "'".str_replace(array("\n","\r","\t"), array("','","",""),$usernameArr)."'";
		$set_username = "and A.username in ($username_arr)";
	}	

	//-------------------------------------------------------------------------------------------------------------
	$query = $db->query("select * from servers where cid = '$cid' and sid = '$sid'");
	if($db->num_rows($query))
	{
		$server = $db->fetch_array($query);
	}else{
		exit();
	}
	
	
	//---------------------------------------------------------------------
	$data_list = '';
	$pdbhost = SetToDB($server['db_server']);//数据库服务器
	$pdbuser = $server['db_root'];//数据库用户名
	$pdbpw = $server['db_pwd'];//数据库密码
	$pdbname = $server['db_name'];//数据库名	
	$pdbcharset = 'utf8';//数据库编码,不建议修改.
	$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
	//-----------------------------------------------------------------------------------------------
	$pdb = new mysql();
	$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
	unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
	
	$query = $pdb->query("
	select
		A.username,
		A.nickname,
		A.vip_level,
		B.level,
		C.total_ingot,
		D.first_login_time,
		D.last_login_time,
		D.source
	from 
		player A
		left join player_role B on A.id = B.player_id and A.main_role_id = B.id
		left join player_charge_record C on A.id = C.player_id
		left join player_trace D on A.id = D.player_id
	where
		A.id > 0
		$set_time
		$set_source
		$set_vip
		$set_level
		$set_role
		$set_username
	order by 
		first_login_time asc
	");
	if($pdb->num_rows($query)){

		while($rs = $pdb->fetch_array($query)){	
			$list_array[] = $rs;
		}
		
		$data = '';
		$data .= '<html>';
		$data .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$data .= '<body>';	
		$data .= '<table width="600" border="1" cellpadding="2" cellspacing="0">';
		$data .= '<tr>';
		$data .= '<td colspan="8" height="30"><strong>'.$server['o_name'].$time_title.$source_title.$level_title.$vip_title.$role_title.'</strong> 玩家数据</td>'; 
		$data .= '</tr>';
		$data .= '<tr>';
		$data .= '<td><strong>角色名</strong></td><td><strong>角色等级</strong></td><td><strong>登陆名</strong></td><td><strong>注册</strong></td><td><strong>最后登陆</strong></td><td><strong>渠道</strong></td><td><strong>VIP</strong></td><td><strong>充值元宝</strong></td>';
		$data .= '</tr>';

		foreach($list_array as $rs)
		{
			$data .= '<tr><td>'.$rs['nickname'].'</td><td>'.$rs['level'].'</td><td style="vnd.ms-excel.numberformat:@">'.$rs['username'].'</td><td>'.date('Y-m-d H:i:s',$rs['first_login_time']).'</td><td>'.date('Y-m-d H:i:s',$rs['last_login_time']).'</td><td>'.$rs['source'].'</td><td>'.$rs['vip_level'].'</td><td>'.$rs['total_ingot'].'</td></tr>';
		}

		$data .= '</table></body></html>';	
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$server['name']."-".$date.".xls");//$filename   导出文件名; 
		header("Pragma: public"); 	
			
		echo $data;
		
	}else{
		echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body><script language="javascript" type="text/javascript">alert("找不到任何玩家数据");</script></body></html>';
	}

}


?>