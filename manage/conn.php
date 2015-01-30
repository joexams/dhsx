<?php
if(!empty($_COOKIE['game_auth_manage_qq']))
{
	list($adminWebID,$adminWebName) = explode("\t", authcode($_COOKIE['game_auth_manage_qq'], 'DECODE'));
	$query = $db->query("select * from admin where adminID = '$adminWebID'");	
	$num = $db->num_rows($query);
	if($num)
	{ 
		$ars = $db->fetch_array($query);
		$adminWebPwd = $ars['adminPassWord'];	
		$adminWebPower = $ars['adminPower'];//系统权限
		$adminWebServersPower = $ars['serversPower'];	//服务器权限
		$adminWebCid = $ars['cid'];	//负责运营商ID列表
		$adminWebServers = $ars['servers'];	//负责服务器ID列表
		$adminWebLang = $ars['adminLang'];	//语言包
		//$adminWebSetServersPower = $ars['adminSetServersPower'];	//可设置单服客服权限
		//$adminWebSetPower = $ars['adminSetPower'];	//可设置全局客服权限
		$adminWebType = $ars['adminType'];	//用户类型
	}
	else
	{
		$adminWebID = $adminWebName = '';
		setcookie('game_auth_manage_qq', '', -86400,$cookiepath,$cookiedomain);
	}
} 
else 
{
	$adminWebID = $adminWebName = '';

}

systemDefine();//-----------------系统常量
?>