<?php
if(!empty($_COOKIE['qq_game_auth_admin']))
{
	list($adminWebName,$adminWebPower) = explode("@#$%", authcode($_COOKIE['qq_game_auth_admin'], 'DECODE'));
	$adminWebPower = explode(',',$adminWebPower);
} 
else 
{
	$adminWebName = $adminWebPower = '';
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	setcookie('qq_game_auth_admin', '', -86400,$cookiepath,$cookiedomain);
	setcookie('qq_game_mysql_admin', '', -86400,$cookiepath,$cookiedomain);

}
foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) {
		$_key{0} != '_' && $$_key = daddslashes($_value);
	
	}
}
unset($_request, $_key, $_value);
//---------------------------------------------------------------------------------------------------------------------------------
//$look = 是否是可见栏目权限判断 0为直接输出 1为return
//$type = 弹出信息类型
//$show = 0为直接输出 1为return
function webAdmin($power,$show='',$look='',$type=''){//判断是否网站管理员
	global $db,$adminWebName,$adminWebPower;
	if (!$adminWebName || !$adminWebPower)
	{
		if ($show=='y')
		{
			return 1;
		}
		else
		{
			showMsg("您没有权限！",'login.php',$type);
			exit();		
		}
	}
	if ($look=='y')//如果该栏目涉及到查看
	{
		if (in_array($power.'-look',$adminWebPower) && !($adminWebName=='admin'))
		{
			if ($show=='y')
			{
				return 1;
			}
			else
			{
				showMsg("您没有编辑权限！",'',$type);
				exit();				
			}
		}	
	}		
	if (!in_array($power,$adminWebPower) && !($adminWebName=='admin'))
	{
		if ($show=='y')
		{
			return 1;
		}	
		else
		{
			showMsg("您没有权限！",'',$type);
			exit();	
		}
	}	
}
?>