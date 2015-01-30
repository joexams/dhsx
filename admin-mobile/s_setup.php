<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{

	case 'Power': Power();break;
	case 'SetPower': SetPower();break;

	default:  Main();

}
function  Main() {
	global $db; 
	include template('s_main');
}


//-------------------------------------------------------------------------------------------菜单

function Power()
{
	global $db,$lang; 
	$type = ReqStr('type');
	if ($type  ==  'two')
	{
		$set_type = " WHERE Mtype = 2";
	}else{
		$set_type = " WHERE Mtype = 1";
	}
	
	$query = $db->query("select * from ".SETUPDB."web_menu $set_type order by Morder asc");	
	if($db->num_rows($query))
	{		
		while($mrs = $db->fetch_array($query))
		{
			 $menu_array[] = $mrs;
			
		}
	}
	include_once template('s_power');
}

//--------------------------------------------------------------------------修改/删除
function SetPower()
{
	global $db,$lang; 

	$delMid = ReqArray('delMid');
	$Mid = ReqArray('Mid');
	$Morder = ReqArray('Morder');
	$Mname = ReqArray('Mname');
	$Mpower = ReqArray('Mpower');
	$Mtype = ReqArray('Mtype');

	$nMorder = ReqNum('nMorder');
	$nMname = ReqStr('nMname');
	$nMpower = ReqStr('nMpower');
	$nMtype = ReqNum('nMtype');

	if(!empty($delMid))
	{
		$delMidArr = implode(",",$delMid);//组合为字符串
		$db->query("delete From ".SETUPDB."web_menu where Mid in ($delMidArr)");
		showMsg('删除成功！','','','greentext');
	}else{	
		$MidNum = count($Mid);
		for ($i = 0; $i <= $MidNum;$i++)
		{
			if ($Mname[$i] && $Morder[$i] && $Mpower[$i] && $Mtype[$i])
			{
				$db->query("update ".SETUPDB."web_menu set Mname = '$Mname[$i]',Morder = '$Morder[$i]',Mpower = '$Mpower[$i]',Mtype = '$Mtype[$i]' where Mid  =  '$Mid[$i]'");
			}
		}
		if($nMorder && $nMname && $nMpower && $nMtype)
		{
			$db->query("insert into ".SETUPDB."web_menu (Mname,Morder,Mpower,Mtype) values ('$nMname','$nMorder','$nMpower','$nMtype')");
		}		
		
		showMsg('操作成功！','','','greentext');
	}
	//$memcache->delete('web_menu_array');//删除缓存		
}  

?>