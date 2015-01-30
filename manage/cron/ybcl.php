<?php
header("Content-Type:text/html;charset=UTF-8");
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
if ($adminWebType != 's' && $adminWebType != 'c')
{
	echo 'ERR!';
	exit();		
}

$cid = ReqNum('cid');
$sid = ReqNum('sid');
$action = ReqStr('action');
$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
$servers_list = globalDataList('servers',"cid = '$cid'",'open_date desc');//服务器
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>查询</title>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script language = "javaScript" src = "../include/js/common.js" type="text/javascript"></script>
<script language = "javaScript" src = "../include/js/menu.js" type="text/javascript"></script>
</head>
<body>
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<form action="ybcl.php" method="get" name="form">
<table class="table">
   <tr>
    <th colspan="2" class="title_1">充值元宝/存量</th>
  </tr> 
   <tr>
    <td width="100" align="right"><strong>平台</strong></td>
	<td>
	<select name="cid" id="cid"  onChange="selectAjax('../call.php?action=CallCompanyServers','sid','cid',1)">
	 <option class="select">运营商</option>
	 
	<?php if(is_array($company_list)) { foreach($company_list as $crs) { ?>
	 <option value="<?php echo $crs['cid']?>" 
	<?php if($crs['cid'] == $cid) { ?>
	 selected="selected" 
	<?php } ?>
	><?php echo $crs['name']?></option>
	 
	<?php } } ?>
	</select>	  	
	<select name="sid" id = "sid">
	<option  value="">全部服务器</option>
	<?php if($servers_list) { ?>
	 
	<?php if(is_array($servers_list)) { foreach($servers_list as $srs) { ?>
	 <option value="<?php echo $srs['sid']?>" 
	<?php if($srs['sid'] == $sid) { ?>
	 selected="selected" 
	<?php } ?>
	><?php echo $srs['name']?>-<?php echo $srs['o_name']?></option>
	 
	<?php } } ?>
	 
	<?php } ?>
	</select>	</td>
  </tr> 
  <tr>
    <td align="right"><input type="hidden" name="action" value="uuupay" /></td>  
    <td>
	<input type="submit" id="Submit" name="Submit" value="搜索"  class="button"/></td>
  </tr>    
</table>
</form>

<?php
if($action == 'uuupay'){
	
	if(!$cid){
		echo '错误参数！';
		return;	
	}	
	if($sid){
		$set_sid = " and A.sid = '$sid'";
	}	
	$pay_ingot = 0;
	$pay_ingot_last = 0;
	$ingot_last = 0;
	$query = $db->query("select A.sid,A.db_server,A.db_root,A.db_pwd,A.db_name from servers A where A.cid = '$cid' and A.open_date < now() $set_sid order by A.sid desc");		
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$pdbhost = SetToDB($rs['db_server']);//数据库服务器
			$pdbuser = $rs['db_root'];//数据库用户名
			$pdbpw = $rs['db_pwd'];//数据库密码
			$pdbname = $rs['db_name'];//数据库名	
			$pdbcharset = 'utf8';//数据库编码,不建议修改.
			$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
			
			$pdb = new mysql();
			$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
			unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
			
			
			//-------------------------------------------------------------------------------------------------------------
			$uquery = $pdb->query("select charge_ingot,ingot from player A LEFT JOIN player_data B on A.id = B.player_id and  A.nickname <> ''");
			$num = $pdb->num_rows($uquery);
			if($num)
			{
				while($urs = $pdb->fetch_array($uquery))
				{	
					$pay_ingot_last += $urs['charge_ingot'];
					$ingot_last += $urs['ingot'];
				}
				
			}
			//-------------------------------------------------------------------------------------------------------------
			
			
			
			//print_r($usernameArr);		
		}
	}else{
		echo '找不到任何服务器！';
		exit();
	}

	
	//--------------------------------------------------------------------------------------

	$m = $db->fetch_first("
	select 
		sum(amount) AS amount,
		sum(coins) AS coins
	from 
		pay_data  A
	where 
		A.cid = '$cid'
		and A.success <> 0
		and A.status <> 1
		$set_sid
	");	
	$pay_ingot = $m['coins'] - ($m['coins'] - round($m['amount'])*10);
?>
	<br />
	<table class="table">
	   <tr>
		<td width='200' class="td2" align="right"><strong>充值元宝(不含赠送)</strong>：</td><td><?php echo $pay_ingot?></td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>充值元宝存量</strong>：</td><td><?php echo $pay_ingot_last?></td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>赠送元宝存量</strong>：</td><td><?php echo $ingot_last?></td>
	   </tr>
	</table>

<?php
}
$db->close();
?>
</body>
</html>
