<?php
  //--------------------------------------------------------------------------------------------测试号

function Increase() {

	global $db,$adminWebID,$adminWebType,$adminWebName;
	$cid = ReqNum('cid');
	if($adminWebType == 's')
	{
		$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
	}elseif($adminWebType == 'c'){
		global $adminWebCid;
		$company_list = globalDataList('company',"cid in ($adminWebCid)",'corder asc,cid asc');//运营商	
		
		$set_cid_arr = "and A.cid in ($adminWebCid)";
				
	}
	if(!$cid)
	{
		$cid = $db->result_first("select cid from company order by corder asc limit 1");
	}
	
	//---------------------------------------------------------------------
	
	$query = $db->query("
	select 
		*
	from 
		servers
	where 
		cid = '$cid'
		and open = 1
		$set_cid_arr
	order by
		sid asc 
	");
	if($db->num_rows($query))
	{				
		while($rs = $db->fetch_array($query)){	
			$list_array[] = $rs;
		}
	}	
	$db->close();
	include_once template('increase');
}


//--------------------------------------------------------------------------------------------赠送

function SetIncrease() {
	global $db,$adminWebID,$adminWebType;
	$cid = ReqNum('cid');
	$type = ReqStr('type');
	$val = ReqNum('val');
	$sid = ReqArray('sid');
	$path_root = UCTIME_ROOT.'/mod/increase_post.php';
	if(!$sid)
	{
		showMsg('NOCHOOSESERVER');	
		return;
	}	
	$sidArr =  $sid ? implode(",",$sid) : '';
	$query = $db->query("
		select 
			server_ver
		from 
			servers
		where
			sid in ($sidArr)
			and open = 1
		group by 
			server_ver
		");	
	if($db->num_rows($query))
	{
	
		while($rs = $db->fetch_array($query))
		{
			print `php $path_root $rs[server_ver] $sidArr $type $val`;//执行不同版本
		}	
	}
	//exit();
	insertServersAdminData(0,0,0,'全服赠送','服务器ID:'.$sidArr);//插入操作记录		
	showMsg('TESTADDGIFTOKMSG','','','greentext','','n');		
	$db->close();

}
?> 