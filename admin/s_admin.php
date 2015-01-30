<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('admin');
switch (ReqStr('action'))
{
	case 'DelAdmin': DelAdmin();break;	
	case 'SaveAddAdmin': SaveAddAdmin();break;	
	case 'AddAdmin': AddAdmin();break;	
	case 'EditAdmin': EditAdmin();break;
	case 'SaveEditAdmin': SaveEditAdmin();break;
	default:  Main();
}
	
function Main() 
{
	global $db; 
	
	$query = $db->query("select * from ".SETUPDB."admin order by adminID asc");		
	if($db->num_rows($query))
	{
		while($ars = $db->fetch_array($query))
		{
			$admin_array[] = $ars;
		}
	}
	include_once template('s_admin');
}	
function AddAdmin()
{
	global $db; 
	$query = $db->query("select * from ".SETUPDB."web_menu order by Mtype asc,Morder asc");	
	if($db->num_rows($query))
	{		
		while($prs = $db->fetch_array($query))
		{
			$power_array[] =  $prs;
			
		}
	}
	include_once template('s_admin_add');
}
function EditAdmin()
{
	global $db; 
	$adminID = ReqNum('id');
	if (empty($adminID))
	{
		showMsg('错误参数！');
	}else{

		$query = $db->query("select * from ".SETUPDB."admin where adminID = '$adminID'");
		if($db->num_rows($query))
		{	
			$rs = $db->fetch_array($query);
			
			$query = $db->query("select * from ".SETUPDB."web_menu order by Mtype asc, Morder asc");	
			if($db->num_rows($query))
			{		
				while($prs = $db->fetch_array($query))
				{
					$power_array[] =  $prs;
					
				}
			}	
					
			$adminPowerArr = explode(',',$rs['adminPower']);
			include_once template('s_admin_edit');
		}else{
			showMsg('无此信息！');
		}
	}
}

//--------------------------------------------------------------------------增加管理员
function SaveAddAdmin() {
	global $db; 
	$adminName = ReqStr('adminName');
	$adminPassWord = ReqStr('adminPassWord');
	$adminPower = ReqArray('adminPower');
	if(!empty($adminPower))
	{
		$adminPower = implode(",",$adminPower);//组合为字符串
	}
	if (!$adminName || !$adminPassWord || !$adminPower)
	{
		showMsg('错误参数！');
	}else{
		$num = $db->result($db->query("select count(*) from ".SETUPDB."admin where adminName = '$adminName'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('管理员已存在！');
		}else{
			$adminPassWord = md5($adminPassWord);
			$db->query("insert into ".SETUPDB."admin (adminName,adminPassWord,adminPower) values ('$adminName','$adminPassWord','$adminPower')");
			showMsg('操作成功！','?in=admin','','greentext');
		}
	}
}  

//--------------------------------------------------------------------------修改管理员
function SaveEditAdmin() 
{
	global $db; 
	$adminID=ReqNum('id');
	$adminName=ReqStr('adminName');
	$adminPassWord=ReqStr('adminPassWord');
	$adminPower=ReqArray('adminPower');
	if(!empty($adminPower)) $adminPower = implode(",",$adminPower);//组合为字符串
	if (empty($adminID)  || !$adminName  || !$adminPower)
	{
		showMsg('错误参数！');
	}else{
	
		$num = $db->result($db->query("select count(*) from ".SETUPDB."admin where adminName = '$adminName' and adminID <> '$adminID'"),0); //检查	
		if($num)
		{    //当已经有记录时
			showMsg('管理员已存在！');
		}else{
			if ($adminPassWord != "")//判断是否修改密码
			{
				$adminPassWord = md5($adminPassWord);
				$setPassword=",adminPassWord = '$adminPassWord'";
			}else{
				$setPassword="";
			}
			$db->query("update ".SETUPDB."admin set adminName = '$adminName',adminPower = '$adminPower' $setPassword where adminID = '$adminID' ");
			showMsg('操作成功！','?in=admin','','greentext');		
		}
	
	}
}  

//--------------------------------------------------------------------------删除管理员
function DelAdmin()
{
	global $db; 
	$adminID=ReqNum('id');
	if (empty($adminID))
	{
		showMsg('错误参数！');
	}else{
		if ($adminID==1)
		{
			showMsg($lang[nodeladmin]);
	
		}else{	
			$db->query("delete From ".SETUPDB."admin where adminID = '$adminID'");
			showMsg();
		}
	}
} 
 
?> 