<?php
header("Content-Type:text/html;charset=UTF-8");
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
include_once UCTIME_ROOT.'/u.config.inc.php';//一些字符处理等函数文件

function PayAmountAll($cid,$user,$s_time,$e_time,$set_sid=''){
	global $db;
	$username_arr = "'".str_replace(",", "','",implode(",",$user))."'";
	$amount = $db->result($db->query("
	select 
		sum(amount)
	from 
		pay_data
	where 
		cid = '$cid'
		$set_sid
		and username in ($username_arr) 
		and dtime_unix >= '$s_time' 
		and dtime_unix <= '$e_time'
	"),0);
	return $amount;
}
function PayNumAll($cid,$user,$s_time,$e_time,$set_sid=''){
	global $db;
	$username_arr = "'".str_replace(",", "','",implode(",",$user))."'";
	$num = $db->result($db->query("
	select 
		count(distinct(username))
	from 
		pay_data
	where 
		cid = '$cid'
		$set_sid
		and username in ($username_arr) 
		and dtime_unix >= '$s_time' 
		and dtime_unix <= '$e_time'
	"),0);	
	return $num;
}
if ($adminWebType != 's' && $adminWebType != 'c')
{
	echo 'ERR!';
	exit();		
}

$cid = 1;
$sid = ReqArray('sid');
$sid_arr = $sid ? implode(",",$sid) : '';
$stime = ReqStr('stime');
$etime = ReqStr('etime');
$source = ReqStr('source');
$form = ReqStr('form');
$action = ReqStr('action');
//$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
$servers_list = globalDataList('servers',"cid = '$cid' and name <> 'qq_s0'",'open_date desc');//服务器
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
<script language='JavaScript' type='text/JavaScript' src='../include/js/calendar.js'></script>
<form action="userpaya.php" method="post" name="form">
<table class="table">
   <tr>
    <th colspan="2" class="title_1">查询注收比</th>
  </tr>
  <tr>
    <td width="100" align="right" valign="top"><strong>服务器</strong></td>
	<td>
	<select name="sid[]" size="10" multiple="multiple" id = "sid">
	<option  value="0"
	<?php if(!$sid_arr) { ?>
	 selected="selected" 
	<?php } ?>
	
	>全部服务器</option>
	<?php if($servers_list) { ?>
	 
	<?php if(is_array($servers_list)) { foreach($servers_list as $srs) { ?>
	 <option value="<?php echo $srs['sid']?>" 
	<?php if($sid && in_array($srs['sid'],$sid)) { ?>
	 selected="selected" 
	<?php } ?>
	><?php echo $srs['name']?>-<?php echo $srs['o_name']?></option>
	 
	<?php } } ?>
	 
	<?php } ?>
	</select>	
	</td>
  </tr> 
  <tr>
    <td align="right"><strong>注册时间</strong></td>
	<td>
	<input name="stime" id="stime" type="text" onclick="showcalendar(event, this)"   value="<?php echo $stime?>" size="10" readonly />
	</td>
  </tr> 
  <tr>
    <td align="right"><strong>截止时间</strong></td>
	<td>
	<input name="etime" id="etime" type="text" onclick="showcalendar(event, this)"   value="<?php echo $etime?>" size="10" readonly />
	</td>
  </tr>  
  <tr>
    <td align="right"><strong>渠道via</strong></td>
	<td>
	<input name="source" id="source" type="text"  value="<?php echo $source?>" size="40" />
	</td>
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
	if($sid_arr != 0){
		$set_sid =  " and sid in ($sid_arr)";
	}
	
	
	
	$query = $db->query("select name from servers where cid = '$cid' and name <> 'qq_s0' $set_sid");		
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			$sname[] = $rs['name'];	
		}
	}	
	$sname_arr = "'".str_replace(",", "','",implode(",",$sname))."'";
	$set_sname = " and server_name in ($sname_arr)";

	if($source)
	{
		$set_source = " and source = '$source'";
	}	
	

	if($form)
	{
		$set_form = " and plat_form = '$form'";
	}	
	//-------------------------------------------------------------------------------------------------------------
	$uquery = $udb->query("select openid from player where date_format(FROM_UNIXTIME(reg_time), '%Y-%m-%d') = '$stime' $set_sname $set_form  $set_source");
	if($udb->num_rows($uquery))
	{
		while($urs = $udb->fetch_array($uquery))
		{	
			$usernameArr[] = $urs['openid'];
		}

	}
if($usernameArr)
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	$payamount_all = 0;
	$paynum_all = 0;
	$usernameArr = array_unique($usernameArr);
	$regnum = count($usernameArr);//注册人数
	$s_time = strtotime($stime.' 00:00:00');
	$e_time = strtotime($etime.' 23:59:59');
	$uArr = array_chunk($usernameArr,5000); //拆分	
	//-----------------------------------------------------------------------------------------------------------------------------------
	foreach($uArr as $user) {
		$payamount_all += PayAmountAll($cid,$user,$s_time,$e_time,$set_sid);	
		$paynum_all += PayNumAll($cid,$user,$s_time,$e_time,$set_sid);
		
	}
?>
	<br />
	<table class="table">
	   <tr>
		<td width='200' class="td2" align="right"><strong><?php echo $stime?>注册人数</strong>：</td><td><?php echo ($regnum ? $regnum : 0)?>人</td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>截止<?php echo $etime?>付费人数</strong>：</td><td><?php echo $paynum_all?>人</td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>截止<?php echo $etime?>充值总额</strong>：</td><td><?php echo round($payamount_all,2)?>元</td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>ARPU</strong>(充值总额/付费人数)：</td><td><?php if($payamount_all) echo $payamount_all/$paynum_all?></td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>注收比</strong>(充值总额/注册人数)：</td><td><?php if($payamount_all) echo $payamount_all/$regnum?></td>
	   </tr>
	   <tr>
		<td class="td2" align="right"><strong>渗透率</strong>(付费人数/注册人数)：</td><td><?php if($paynum_all) echo $paynum_all/$regnum?></td>
	   </tr>
	</table>
<br />
<?php
	}else{
		echo '此日期或渠道无注册玩家';
	}
}
$db->close();
?>
</body>
</html>
