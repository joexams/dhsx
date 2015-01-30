<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
	case 'SaveCompany': webAdmin('c_company');SaveCompany();break;
	default:  webAdmin('c_company');Company();
}

//--------------------------------------------------------------------------------------------运营商
	
function Company() 
{
	global $db,$cid; 

	$query = $db->query("select * from company where cid = '$cid'");		
	if($db->num_rows($query))
	{
		$rs = $db->fetch_array($query);
		$rs['link'] = explode('|',$rs['link']);
		//$rs['link'] = str_replace("|", "\n",$rs['link']);	
	}else{
		showMsg('NULL');	
		return;		
	}
	$db->close();
	include_once template('c_company');
}
	

//--------------------------------------------------------------------------------------------保存修改运营商
function  SaveCompany() 
{
	global $db,$cid; 
	$game_name = ReqStr('game_name');
	$game_text = ReqStr('game_text');
	$link = ReqArray('link','htm');

	if (!$game_name)
	{
		showMsg('ERROR');	
	}else{

		$link = implode("|",$link);
		//$link = str_replace(array("\n","\r","\t"), array("|","",""),$link);
		$query = $db->query("
		update 
			company 
		set 
			`game_name`='$game_name',
			`game_text`='$game_text',
			`link`='$link'
		where 
			cid = '$cid'
		");
		if ($query)
		{
			$contents = '修改运营商设置:运营商(ID:'.$cid.')';
			insertServersAdminData(0,0,0,'运营商',$contents);//插入操作记录		
			showMsg("SETOK",'','','greentext');	

		}else{
			showMsg('SETERR');	
		}
	}
	$db->close();
	
}
 
?> 