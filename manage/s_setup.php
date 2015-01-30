<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('setup');

switch (ReqStr('action'))
{
	case 'SetupPower': SetupPower();break;
	case 'ServersPower': ServersPower();break;
	//case 'ServersData': ServersData();break;
	case 'SetSetupPower': SetSetupPower();break;
	case 'SetServersPower': SetServersPower();break;
	case 'SetSetup': SetSetup();break;
	case 'UpdateTpl': UpdateTpl();break;
	case 'UpdateData': UpdateData();break;
	default:  Setup();

}
function  Setup() {
	global $db; 
	$setup_type = array(
		array('tval'=>'1','tname'=>'单行'),
		array('tval'=>'2','tname'=>'多行'),
		array('tval'=>'3','tname'=>'数组'),
	);
	$query = $db->query("select * from setup order by sdel asc,sorder asc,sid asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
		
			if ($prs['stype'] == 3) 
			{
				$prs['sval'] = str_replace("|", "\n",$prs['sval']);				
			} 	
		
			 $list_array[] = $prs;
			
		}
	}	
	
	$db->close();
	include template('s_setup');
}


//-------------------------------------------------------------------------------------------系统权限

function SetupPower()
{
	global $db; 
	$type = ReqStr('type');
	if ($type)
	{
		$set_type = " where ptype = '$type'";
	}else{
		$set_type = " where ptype = 's'";
	}

	
	$query = $db->query("select * from setup_power $set_type order by porder asc,pid asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
			 $list_array[] = $prs;
			
		}
	}
	$db->close();
	include_once template('s_setup_power');
}

//-------------------------------------------------------------------------------------------远程服务器权限

function ServersPower()
{
	global $db; 
	$type = ReqStr('type');
	if ($type  ==  'two')
	{
		$set_type = " where ptype = 2";
	}else{
		$set_type = " where ptype = 1";
	}
	
	$query = $db->query("select * from servers_power $set_type order by porder asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
			 $list_array[] = $prs;
			
		}
	}
	$db->close();
	include_once template('s_servers_power');
}



//-------------------------------------------------------------------------------------------获取远程服务器数据报表

function ServersData()
{
	global $db; 
	$yesterday = ReqStr('yesterday');//日期
	$exe = ReqNum('exe');//是否执行
	$sid = ReqNum('sid');//SID
	if (!$sid) 
	{
		echo 'SID为空！';
		exit();
	}	
	if (!$yesterday) 
	{
		$yesterday = date('Y-m-d',time()-86400);//昨天数据
	}
	$yesterday2 = date('Y-m-d',strtotime($yesterday)-86400);//前天数据

	$pdb = new mysql();
	echo '<script language="JavaScript" type="text/JavaScript" src="include/js/calendar.js"></script><table class="table"><tr><th colspan="14">获取远程服务器数据报表</th></tr><tr class="title_3"><td colspan="14"><form action="" method="get" name="form" onSubmit="setSubmit(\'Submit\')">	 	
		时间： <input name="yesterday" id="yesterday" type="text" onclick="showcalendar(event, this)"   value="'.$yesterday.'" size="10" readonly />
		<select name="exe">
			 <option value="0">不执行</option>
			 <option value="1">执行</option>
			</select>
		<input type="submit" name="Submit" id="Submit" value=" 查 询 " class="button"/>
		<input name="in" type="hidden" value="setup"/>	
		<input name="action" type="hidden" value="ServersData"/>	
		</form></td></tr>';
	echo '<tr class="title_2">
	<td>服务器</td><td>最高在线时间</td></td><td>最高在线</td><td>平均在线</td><td>注册</td><td>创建</td><td>登陆</td><td>流失</td>
	<td>充值玩家</td><td>充值金额</td><td>充值次数</td><td>消费</td><td>新增充值</td><td>状态</td>
	</tr>';
	
		
		$query = $db->query("
		SELECT 
			A.cid,
			A.sid,
			A.name,
			A.db_server,
			A.db_root,
			A.db_pwd,
			A.db_name,
			B.coins_rate
		FROM 
			servers A
			LEFT JOIN company B ON A.cid = B.cid
		WHERE
			A.private = 1
			and A.sid = '$sid'
		ORDER BY
			A.cid asc,		
			A.sid asc		
		");
		if($db->num_rows($query))
		{
			while($server = $db->fetch_array($query))
			{	
				$cid =  $server['cid'];
				$sid =  $server['sid'];
				$name =  $server['name'];
				$coins_rate =  $server['coins_rate'];
				//------------------------------------------连远程数据库-----------------------------------------------------
				$pdbhost = SetToDB($server['db_server']);//数据库服务器
				$pdbuser = $server['db_root'];//数据库用户名
				$pdbpw = $server['db_pwd'];//数据库密码
				$pdbname = $server['db_name'];//数据库名	
				$pdbcharset = 'utf8';//数据库编码,不建议修改.
				$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
				
				$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
				unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
				//-------------------------------------------当前最高在线----------------------------------------------------
				$max = $pdb->fetch_first("
					SELECT 
						`time` AS max_online_time,
						MAX(online_count) AS max_online_count
					FROM 
						server_state
					WHERE 
						DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$yesterday'
					GROUP BY
						online_count		
					ORDER BY 
						max_online_count desc
				");
				if($max)
				{
					$max_online_count = $max['max_online_count'];
					$max_online_time = $max['max_online_time'];
				}
				
				
				
				if($max)
				{		
				
					//-------------------------------------------平均在线----------------------------------------------------
					$avg = $pdb->fetch_first("
					SELECT
						SUM(online_count) AS online_count,
						COUNT(*) AS hour_count
					FROM 
						server_state
					WHERE 
						DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$yesterday'		
					");
	
					if($max)
					{
						$avg_online_count = round($avg['online_count']/$avg['hour_count']);
					}			
				
				
					//-------------------------------------------注册/创建/登陆数据----------------------------------------------------
					
					$reg = $pdb->fetch_first("
					SELECT
						SUM(register_count) AS register_count,
						SUM(create_count) AS create_count,
						SUM(login_count) AS login_count
					FROM 
						server_state
					WHERE 
						DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') = '$yesterday'
				
					");
					if($reg)
					{
						$register_count = $reg['register_count'];
						$create_count = $reg['create_count'];		
						$login_count = $reg['login_count'];		
					}	
					//-------------------------------------------新手流失----------------------------------------------------
					$out_count = $pdb->result_first("
						SELECT 
							COUNT(B.player_id) AS out_count
						FROM 
							player A
							LEFT JOIN player_trace B ON A.id = B.player_id
							LEFT JOIN player_role C ON A.id = C.player_id and A.main_role_id = C.id
				
						WHERE 
							C.level < 10
							AND DATE_FORMAT(FROM_UNIXTIME(B.first_login_time), '%Y-%m-%d') = '$yesterday2'			
					");	
					//-------------------------------------------充值----------------------------------------------------
					$pay = $db->fetch_first("
					SELECT 		
						COUNT(DISTINCT(player_id)) AS pay_player_count,
						SUM(amount) AS pay_amount,
						COUNT(*) AS pay_num
					FROM 
						pay_data 
					WHERE 
						cid = '$cid'
						AND sid = '$sid'
						AND status = 0	
						AND DATE_FORMAT(`dtime`, '%Y-%m-%d') = '$yesterday'
					");
					if($pay){
						$pay_player_count = $pay['pay_player_count'];
						$pay_amount = round($pay['pay_amount'],2);
						$pay_num = $pay['pay_num'];		
						
					}
					
					//-------------------------------------------首充----------------------------------------------------
			
					$new_player = $db->result_first("
					SELECT 
						new_player
					FROM 
						pay_day_new
					WHERE 
						cid = '$cid'
						AND sid = '$sid'
						AND DATE_FORMAT(pdate, '%Y-%m-%d') = '$yesterday'
					");
				
				
					//-------------------------------------------消费----------------------------------------------------
					$con = $pdb->fetch_first("
					select 
						SUM(if(D.type = 0,A.value,0)) AS consume_0,
						SUM(if(D.type = 1 AND D.id != 35,A.value,0)) AS consume_1
					from 
						player_ingot_change_record A
						LEFT JOIN player_charge_record B ON A.player_id = B.player_id
						LEFT JOIN player C ON A.player_id = C.id
						LEFT JOIN ingot_change_type D ON A.type = D.id
					where 
						B.total_ingot > 0
						AND C.is_tester = 0
						AND DATE_FORMAT(FROM_UNIXTIME(A.change_time), '%Y-%m-%d') = '$yesterday'		
					
					"); 		
					if($con){
						$con['value'] = $con['consume_0']+$con['consume_1'];
						$consume = round(($con['value']/$coins_rate),2);				
						
					}			
					//-----------------------------------执行插入------------------------------------------------------------
					if($exe){
						$d = $db->result($db->query("select count(*) from game_data where cid = '$cid' and sid = '$sid' and gdate = '$yesterday'"),0);
						if (!$d)
						{
							$e = $db->query("
							insert into 
							game_data
								(cid,sid,max_online_time,max_online_count,avg_online_count,register_count,create_count,login_count,out_count,pay_player_count,pay_amount,pay_num,consume,new_player,gdate) 
							values 
								('$cid','$sid','$max_online_time','$max_online_count','$avg_online_count','$register_count','$create_count','$login_count','$out_count','$pay_player_count','$pay_amount','$pay_num','$consume','$new_player','$yesterday') 
							");
							$s = $e ? '<strong class="greentext">插入成功</strong>' : '<strong class="redtext">插入失败</strong>';
						}else{
							$e = $db->query("
							update 
								game_data 
							set 
								max_online_time = '$max_online_time',
								max_online_count = '$max_online_count',
								avg_online_count = '$avg_online_count',
								register_count = '$register_count',
								create_count = '$create_count',
								login_count = '$login_count',
								out_count = '$out_count',
								pay_player_count = '$pay_player_count',
								pay_amount = '$pay_amount',
								pay_num = '$pay_num',
								new_player = '$new_player'
							where 
								cid = '$cid' 
								and sid = '$sid' 
								and gdate = '$yesterday'
							");
							$s = $e ? '<strong class="greentext">更新成功</strong>' : '<strong class="redtext">更新失败</strong>';
						}
					}else{
						$s = '<span class="graytext">不执行</span>';
					}
					
				}else{
					$s = '<span class="graytext">无数据</span>';
				}
				//-----------------------------------------------------------------------------------------------
				echo '<tr><td>'.$name.'</td><td>'.$max_online_time.'</td><td>'.$max_online_count.'</td><td>'.$avg_online_count.'</td><td>'.$register_count.'</td><td>'.$create_count.'</td><td>'.$login_count.'</td><td>'.$out_count;
				echo '</td><td>'.$pay_player_count.'</td><td>'.$pay_amount.'</td><td>'.$pay_num.'</td><td>'.$consume.'</td><td>'.$new_player.'</td><td>'.$s.'</td></tr>';
				unset($e, $s,$name, $cid, $sid, $max_online_time, $max_online_count, $avg_online_count, $register_count,$create_count,$login_count,$out_count,$pay_player_count,$pay_amount,$pay_num,$consume,$new_player);
				
				
				//-----------------------------------------------------------------------------------------------
				
			}
	
		}
	echo '</table>';
	
	$pdb->close();
	$db->close();
}


//--------------------------------------------------------------------------设置系统权限
function SetSetupPower()
{
	global $db; 

	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$porder = ReqArray('porder');
	$pname = ReqArray('pname');
	$power = ReqArray('power');
	$ptype = ReqArray('ptype');

	$porder_n = ReqNum('porder_n');
	$pname_n = ReqStr('pname_n');
	$power_n = ReqStr('power_n');
	$ptype_n = ReqStr('ptype_n');
	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $pname[$i] && $porder[$i] && $power[$i] && $ptype[$i])
			{

				$db->query("
				update 
					setup_power 
				set 
					`pname`='$pname[$i]',
					`porder`='$porder[$i]',
					`power`='$power[$i]',
					`ptype` = '$ptype[$i]'
				where 
					pid = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if($porder_n && $pname_n && $power_n && $ptype_n)
	{
		$db->query("insert into setup_power (porder,pname,power,ptype) values ('$porder_n','$pname_n','$power_n','$ptype_n')");
		$msg .= "<br />增加成功！";
		$add_setup = '增加系统权限('.$pname_n.')';

	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from setup_power where pid in ($id_arr)");
		$msg .= "<br />删除成功！";
	}		
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_setup = '删除的系统权限ID('.$id_arr.')';
	$contents = '设置系统权限:更新的系统权限ID('.$id_up_arr.')'.$del_setup.$add_setup;
	insertServersAdminData(0,0,0,'系统权限',$contents);//插入操作记录	
	$db->close();
	showMsg($msg,'','','greentext');	

}  



//--------------------------------------------------------------------------设置远程服务器权限
function SetServersPower()
{
	global $db; 

	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$porder = ReqArray('porder');
	$pname = ReqArray('pname');
	$power = ReqArray('power');
	//$ptype = ReqArray('ptype');

	$porder_n = ReqNum('porder_n');
	$pname_n = ReqStr('pname_n');
	$power_n = ReqStr('power_n');
	//$ptype_n = ReqStr('ptype_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $pname[$i] && $porder[$i] && $power[$i])
			{

				$db->query("
				update 
					servers_power 
				set 
					`pname`='$pname[$i]',
					`porder`='$porder[$i]',
					`power`='$power[$i]'
				where 
					pid = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if($porder_n && $pname_n && $power_n)
	{
		$db->query("insert into servers_power (porder,pname,power) values ('$porder_n','$pname_n','$power_n')");
		$msg .= "<br />增加成功！";
		$add_server = '增加服务器权限('.$pname_n.')';
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from servers_power where pid in ($id_arr)");
		$msg .= "<br />删除成功！";
	}		
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_server = '删除的服务器权限ID('.$id_arr.')';
	$contents = '设置服务器权限:更新的服务器权限ID('.$id_up_arr.')'.$del_server.$add_server;
	insertServersAdminData(0,0,0,'服务器权限',$contents);//插入操作记录		
	$db->close();
	showMsg($msg,'','','greentext');	

}  



//--------------------------------------------------------------------------设置系统
function SetSetup()
{
	global $db; 

	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$sname = ReqArray('sname');
	$stype = ReqArray('stype');
	$skey = ReqArray('skey');
	$sval = ReqArray('sval');
	$sdel = ReqArray('sdel');
	$sorder = ReqArray('sorder');

	$sname_n = ReqStr('sname_n');
	$stype_n = ReqNum('stype_n');
	$skey_n = ReqStr('skey_n');
	$sval_n = ReqStr('sval_n');
	$sdel_n = ReqStr('sdel_n');
	$sorder_n = ReqStr('sorder_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $sname[$i] && $stype[$i] && $skey[$i])
			{
				if ($stype[$i] == 3) 
				{
					$sval[$i] = str_replace(array("\n","\r","\t"), array("|","",""),$sval[$i]);
				} 
				$db->query("
				update 
					setup 
				set 
					`sname` = '$sname[$i]',
					`stype` = '$stype[$i]',
					`skey` = '$skey[$i]',
					`sval` = '$sval[$i]',
					`sdel` = '$sdel[$i]',
					`sorder` = '$sorder[$i]'
				where 
					sid = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if($sname_n && $stype_n && $skey_n && $sval_n)
	{
		$db->query("insert into setup (sname,stype,skey,sval,sdel,sorder) values ('$sname_n','$stype_n','$skey_n','$sval_n','$sdel_n','$sorder_n')");
		$msg .= "<br />增加成功！";
		$add_s = '增加设置系统('.$sname_n.')';
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from setup where pid in ($id_arr) and sdel = 1");
		$msg .= "<br />删除成功！";
	}		
	$id_up_arr = implode(",",$id);
	if ($id_del) $del_s = '删除的设置系统ID('.$id_arr.')';
	$contents = '设置系统:更新的设置系统ID('.$id_up_arr.')'.$del_s.$add_s;
	insertServersAdminData(0,0,0,'设置系统',$contents);//插入操作记录
	systemDefine(1);//重更新系统常量
	$db->close();
	showMsg($msg,'','','greentext');	

}  
//--------------------------------------------------------------------------更新模版缓存
function UpdateTpl()
{
	$dir = opendir(UCTIME_ROOT.'/templates.tpl/');

	while (($file = readdir($dir)) !== false){
		if(preg_match("/(\.tpl|\.php)$/", $file)) {
			@unlink(UCTIME_ROOT.'/templates.tpl/'.$file);
			$m .= $file.'<br />';
		}
	}
	closedir($dir);
	insertServersAdminData(0,0,0,'模版','更新模版缓存');//插入操作记录
	showMsg('更新模版缓存成功！<br />'.$m,'','','greentext','','','n');	
}
//--------------------------------------------------------------------------更新模版缓存
function UpdateData()
{
	$dir = opendir(UCTIME_ROOT.'/data/');

	while (($file = readdir($dir)) !== false){
		if(preg_match("/(\.php)$/", $file)) {
			if(time()-@filemtime(UCTIME_ROOT.'/data/'.$file) > 3600){
				@unlink(UCTIME_ROOT.'/data/'.$file);
				$m .= $file.'<br />';
			}
		}
	}
	closedir($dir);
	insertServersAdminData(0,0,0,'模版','更新数据缓存');//插入操作记录
	showMsg('更新数据缓存文件成功！<br />'.$m,'','','greentext','','','n');	
}
?>