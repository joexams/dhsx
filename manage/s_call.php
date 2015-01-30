<?php
include_once(dirname(__FILE__)."/config.inc.php");
include_once(dirname(__FILE__)."/conn.php");
if ($adminWebType != 's')
{
	showMsg("您没有权限！",'login.php','web');
	exit();		
}
@include language($adminWebLang);

header("expires:mon,26jul199705:00:00gmt"); 
header("cache-control:no-cache,must-revalidate"); 
header("pragma:no-cache");//禁止缓存
header("Content-Type:text/html;charset=utf-8");//避免输出乱码

switch (ReqStr('action'))
{
	case 'CallTestDB': webAdmin('server');CallTestDB();break;
	case 'CallCompanySet': webAdmin('company');CallCompanySet();break;
	case 'CallServersSet': webAdmin('server');CallServersSet();break;
	case 'CallServersAdd': webAdmin('server');CallServersAdd();break;
	case 'CallServersAdmin': webAdmin('server');CallServersAdmin();break;
	case 'CallServersAdminLogin': webAdmin('admin');CallServersAdminLogin();break;
	case 'CallServersSetOpen': webAdmin('server');CallServersSetOpen();break;
	
}

/*//--------------------------------------------------------------------------------------------开服部署
	
function CallServersSetOpen() 
{
	$sid=ReqNum('sid');
	$name=ReqStr('name_e_'.$sid);
	$api_port=ReqStr('api_port_e_'.$sid);
	$api_server=ReqStr('api_server_e_'.$sid);
	$db_server=ReqStr('db_server_e_'.$sid);//从库
	$db_server2= SetToDB2($db_server);//主库


	$api_server = explode('.',$api_server);
	$api_server = $api_server[0];

	$db_server= explode('.',$db_server);
	$db_server_1= $db_server[0];
	
	$db_server2= explode('.',$db_server2);
	$db_server_2= $db_server2[0];
	
	if(!$name || !$api_port || !$api_server || !$db_server_2 || !$db_server_1)
	{
		echo '<strong class="redtext">参数错误！</strong>';
		return;
	}
	
	$path_root = "/root/expect_open $name $api_port $api_server $db_server_2 $db_server_1";
	echo $path_root;
	$open_ret = system($path_root);
	print "open_ret: $open_ret\n";
	//echo $name.'|'.$api_port.'|'.$api_server.'|'.$db_server_2.'|'.$db_server_1;
	//print `php $path_root $name $api_port $api_server $db_server_2 $db_server_1`;
	echo '<strong class="greentext">√部署成功！</strong>';
}
*/
//--------------------------------------------------------------------------------------------测试数据库是否正常连接
	
function CallTestDB() 
{
	$t=ReqNum('t');
	$sid=ReqNum('sid');
	$db_server=ReqStr('db_server_e_'.$sid);
	$db_name=ReqStr('db_name_e_'.$sid);
	$db_root=ReqStr('db_root_e_'.$sid);
	$db_pwd=ReqStr('db_pwd_e_'.$sid,'htm');	
	if ($t == 1)
	{
		$connect_err = '<strong class="redtext">连接失败</strong>';
		$db_err = '<strong class="redtext">DB不存在</strong>';
		$ok = '<strong class="greentext">√</strong>';
	}else{
		$connect_err = '<strong class="redtext">数据库连接失败！</strong>';
		$db_err = '<strong class="redtext">数据库异常错误！找不到['.$db_name.']数据库！</strong>';
		$ok = '<strong class="greentext">√连接成功！</strong>';	
	}
	$link = mysql_connect($db_server,$db_root,$db_pwd) or die ($connect_err);
	mysql_select_db($db_name, $link) or die ($db_err);
	echo $ok;
	
}
//--------------------------------------------------------------------------------------------运营商详细设置
	
function CallCompanySet() 
{
	global $db; 
	$cid=ReqNum('cid');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$money_array = array('人民币','美元','港币','台币','澳币','新加坡元','韩币');		
	$rs = $db->fetch_first("select * from company where cid = '$cid'");
	if(!$rs)
	{
		showMsg('无此运营商！');	
		return;	
	}
	$rs['toselect'] = array(strval((float)$rs['timeoffset']) => 'selected="selected"');
	$rs['charge_ips'] = str_replace("|", "\n",$rs['charge_ips']);	
	$rs['cdn'] = str_replace("|", "\n",$rs['cdn']);	
	//$rs['link'] = str_replace("|", "\n",$rs['link']);	
	$rs['link'] = explode('|',$rs['link']);
	$db->close();
	include_once template('s_company_set');
}
//--------------------------------------------------------------------------------------------服务器详细设置
	
function CallServersSet() 
{
	global $db,$adminWebName; 
	$hf=ReqStr('hf');//用于合服的服务器代号加0
	$oldsid=ReqNum('oldsid');
	
	$sid=ReqNum('sid');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$servers_address_api_list = globalDataList('servers_address','type = 0','name asc');//API地址
	$servers_address_db_list = globalDataList('servers_address','type = 1','name asc');//DB地址
	$servers_address_ver_list = globalDataList('servers_address','type = 2','name asc');//VER地址
	$rs = $db->fetch_first("select A.*,B.name as company_name,B.slug from servers A left join company B on A.cid = B.cid where A.sid = '$sid'");
	if(!$rs)
	{
		showMsg('无此运营商！');	
		return;	
	}
	$slug = $rs['slug'] == 'verycd' || $rs['slug'] == 'txwy' || $rs['slug'] == 'test' ? '' : $rs['slug'].'_' ;	
	$rs['server'] = str_replace(",", "\n",$rs['server']);	
	$rs['db_pwd'] = htmlspecialchars($rs['db_pwd']);
	$rs['db_pwd2'] = htmlspecialchars($rs['db_pwd2']);

	$servers_list = globalDataList('servers',"cid = '$rs[cid]' and sid <> '$sid'",'open_date desc,sid desc');//VER地址
	
	
	if($oldsid)//获取旧服传进来的SID用于合服
	{
		$ors = $db->fetch_first("select server_ver,api_server,db_server from servers where sid = '$oldsid'");
		if($ors)
		{
			if(!$rs['server_ver']) $rs['server_ver'] = $ors['server_ver'];
			if(!$rs['api_server']) $rs['api_server'] = $ors['api_server'];
			if(!$rs['db_server']) $rs['db_server'] = $ors['db_server'];
			
		}
	}
	
	$newpwd = 'YuUkD<%PsB(0u]!x';

	
	$db->close();
	include_once template('s_servers_set');
}
//--------------------------------------------------------------------------------------------服务器添加
	
function CallServersAdd() 
{
	global $db,$adminWebName; 
	$id=ReqNum('id');
	$cid=ReqNum('cid');
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$servers_address_api_list = globalDataList('servers_address','type = 0','name asc');//API地址
	$servers_address_db_list = globalDataList('servers_address','type = 1','name asc');//DB地址
	$servers_address_ver_list = globalDataList('servers_address','type = 2','name asc');//VER地址
	$rs = $db->fetch_first("select * from company where cid = '$cid'");
	if(!$rs)
	{
		showMsg('无此运营商！');	
		return;	
	}
	$mrs = $db->fetch_first("select * from servers_merger where id = '$id'");
	if(!$mrs)
	{
		showMsg('无此合服记录！');	
		return;	
	}
	
	$slug = $rs['slug'] == 'verycd' || $rs['slug'] == 'txwy' || $rs['slug'] == 'test' ? '' : $rs['slug'].'_' ;	
	$rs['server'] = str_replace(",", "\n",$rs['server']);	
	$rs['db_pwd'] = htmlspecialchars($rs['db_pwd']);
	$rs['db_pwd2'] = htmlspecialchars($rs['db_pwd2']);
	
	$servers_list = globalDataList('servers',"cid = '$rs[cid]' and sid <> '$sid'",'open_date desc,sid desc');//VER地址
	
	$db->close();
	include_once template('s_servers_add');
}
//------------------------------------------------------服务器管理员
function CallServersAdmin() 
{

	global $db;
	$sid=ReqNum('sid');
	

	$query = $db->query("
	select 
		*
	from 
		admin
	where 
		FIND_IN_SET('$sid',servers)<>0
	order by 
		adminID asc
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query))
		{	
		
			if ($rs['serversPower']) 
			{
				$rs['serversPowerA'] = explode(',',$rs['serversPower']);
				foreach($rs['serversPowerA'] as $prs => $val)
				{
					$rs['serversPowerList'] .= "'".$val."',";
				}
				$rs['serversPowerList'] =  substr($rs['serversPowerList'],0,strlen($rs['serversPowerList'])-1);
				$rs['servers_power_list'] = globalDataList('servers_power',"power in (".$rs['serversPowerList'].",'ptype asc,porder asc')");//服务器权限
				
			}
			$rs['serversPowerArr'] = $rs['serversPower'] ? explode(',',$rs['serversPower']) : array();		
			$list_array[] =  $rs;
		}
	}
	
	$db->close();
	include_once template('s_servers_admin');

}

//------------------------------------------------------管理员登陆历史
function CallServersAdminLogin() 
{

	global $db,$page;
	include_once(UCTIME_ROOT."/include/ip.php");
	$aid = ReqNum('aid');
	$name=ReqStr('name');
	$name_url = urlencode($name); 	
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;	
		
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			servers_admin_login
		where 
			adminID = '$aid'					
		"),0); //获得总条数
	if($num){			

		$query = $db->query("
		select 
			*
		from 
			servers_admin_login
		where 
			adminID = '$aid'
		order by 
			id desc
		limit 
			$start_num,$pageNum				
		");
				
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = ajaxPage($num, $pageNum, $page, "s_call.php?action=CallServersAdminLogin&aid=$aid&name=$name&winid=$winid", '',10,$winid);		
	}
	
	$db->close();
	include_once template('s_servers_admin_login');

}

?>
