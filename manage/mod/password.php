<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

//--------------------------------------------------------------------------------------------修改密码
	
function EditPwd() 
{
	global $adminWebID,$adminWebName; 

	include_once template('edit_pwd');
}	

//--------------------------------------------------------------------------------------------保存修改密码
function  SaveEditPwd() 
{
	global $db,$adminWebID; 
	$adminPassWordOld = ReqStr('adminPassWordOld');
	$adminPassWordNew = ReqStr('adminPassWordNew');

	CheckPwd($adminPassWordNew);
	
	if(!$adminPassWordOld)
	{
		showMsg('OLDPWDADDMSG');	
		return;
	}
	if(!$adminPassWordNew)
	{
		showMsg('NEWPWDADDMSG');	
		return;
	}	
	$query = $db->query("select * from admin where adminID = '$adminWebID'");	
	if($db->num_rows($query))
	{
		$rs = $db->fetch_array($query);	
		
		if($rs['adminPassWord'] != md5($adminPassWordOld))
		{	
			showMsg('OLDPWDADDERRMSG');	
			return;
		}
		$adminPassWordNew = md5($adminPassWordNew);
		$db->query("update admin set adminPassWord = '$adminPassWordNew' where adminID = '$adminWebID' ");
		insertServersAdminData(0,0,0,'修改密码','修改密码成功');//插入操作记录		
		showMsg('NEWPWDADDOKMSG','','','greentext');	
	}else{
		showMsg('NODATA');	
		return;
	
	}		
	
	$db->close();
}

?> 