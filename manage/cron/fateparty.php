<?php
header("Content-Type:text/html;charset=UTF-8");
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");

function IsSend($m,$v){
	if ($m) {
		return '<span class="greentext">赠送'.$v.'成功</span>     ';
	}else{
		return '<strong class="redtext">赠送'.$v.'失败</strong>     ';
	}
}
function SearchUserNum($list,$nlist,$n=1){
	//print_r($api);
	$nnn = 0;
	if ($list) {
	
		foreach($list as $rr =>$num) {
			if ($num == $n) {
				
				$nnn += 1;
			}	
		}
	}
	if ($nnn) return '<strong>'.$nnn.'人</strong>';	 
}

function SearchUser($cid,$sid,$list,$nlist,$n=1,$api,$arr,$e=0){
	//print_r($api);
	global $adminWebType;
	$msg = '';
	$skill = 0;
	$fame = 0;
	if ($list) {
	
		if($e)//执行赠送
		{
			callapi::load_api_class($api['server_ver']);
			api_base::$SERVER = $api['api_server'];
			api_base::$PORT   = $api['api_port'];
			api_base::$ADMIN_PWD   = $api['api_pwd'];				
		}
	
	
		foreach($list as $rr =>$num) {
			if ($num == $n) {
			
				if($e)
				{
					if(array_key_exists("skill", $arr)) {
						$skill = $arr['skill'];
						$msg .= '['.$arr['skill'].']阅历';
					}
					if(array_key_exists("fame", $arr)) {
						$fame = $arr['fame'];
						$msg .= ' ['.$arr['fame'].']声望';
					}
					
					
					$giftmsg = api_admin::add_player_super_gift ($nlist[$rr], 16, 0, 0, $fame, $skill, 0, '恭喜您获得：'.$msg, array(), array(), array());
					if ($giftmsg['result']) {
						$msgshow = '<span class="greentext">赠送'.$msg.'成功</span>';
					}else{
						$msgshow = '<strong class="redtext">赠送'.$msg.'失败</strong>';
					}
				}
				//$lll .= '<strong>'.$rr.'</strong> '.$num.'次<br />';
				$lll .= '<a href="../'.$adminWebType.'.php?in=player&action=PlayerView&cid='.$cid.'&sid='.$sid.'&uid='.$nlist[$rr].'" target="_blank"><strong>'.$rr.'</strong></a> '.$msgshow.'<br />';
				unset($msg,$msgshow);
			}	
		}
	}
	return $lll ? $lll : '-';	 
}
function FateUser($sid,$stime,$etime,$num=1){
	global $db;
	$server = $db->fetch_first("
	select 
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.db_server,
		A.db_root,
		A.db_pwd,
		A.db_name
	from 
		servers A
	where 
		A.sid = '$sid'
	");
	if($server)
	{

		$pdbhost = SetToDB($server['db_server']);//数据库服务器
		$pdbuser = $server['db_root'];//数据库用户名
		$pdbpw = $server['db_pwd'];//数据库密码
		$pdbname = $server['db_name'];//数据库名	
		$pdbcharset = 'utf8';//数据库编码,不建议修改.
		$pconnect = 0;// 数据库持久连接 0=关闭, 1=打开
		$api = array('server_ver'=>$server['server_ver'],'api_server'=>$server['api_server'],'api_port'=>$server['api_port'],'api_pwd'=>$server['api_pwd']);
		//-----------------------------------------------------------------------------------------------
		$pdb = new mysql();
		$pdb->connect($pdbhost, $pdbuser, $pdbpw, $pdbname, $pconnect, true, $pdbcharset);
		unset($pdbhost, $pdbuser, $pdbpw, $pdbname,$pdbcharset);
						
	}else{
		return array();
	}
	
	
	$stime_s = strtotime($stime.' 00:00:00');
	$etime_e = strtotime($etime.' 23:59:59');
	
	$query = $pdb->query("
	select 
		A.fate_id,
		A.player_id,
		B.username,
		B.nickname
	from 
		player_fate_log A
		left join player B on A.player_id = B.id
	where 
		A.fate_id in (20,34,37,38,39,40)
		and A.op_time >= '$stime_s' and A.op_time <= '$etime_e'
		and A.op_type = 1
	group by
		A.fate_id,
		A.player_id
	order by 
		A.player_id asc,
		A.fate_id asc
	");
	if($pdb->num_rows($query))
	{
		while($rs = $pdb->fetch_array($query))
		{	
			//$rs['api'] = array();
			$list_array[] =  $rs;
		}
		return array('api'=>$api,'list'=>$list_array);
	}else{
		return array();
	}
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
$e = ReqNum('e');
$pwd = ReqStr('pwd');
$run = ReqStr('run');
$action = ReqStr('action');
if($e == 1 && $pwd != 2208755 ) {
	$errmsg = '<strong class="redtext">密码输入错误！本次操作未执行！</strong>';
	unset($e);//密码判断失效
}
//$company_list = globalDataList('company','','corder asc,cid asc');//运营商	
$servers_list = globalDataList('servers',"cid = '$cid'",'open_date desc');//服务器
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>查询命格活动</title>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script language = "javaScript" src = "../include/js/common.js" type="text/javascript"></script>
<script language = "javaScript" src = "../include/js/menu.js" type="text/javascript"></script>
</head>
<body>
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<script language='JavaScript' type='text/JavaScript' src='../include/js/calendar.js'></script>
<form action="fateparty.php" method="post" name="form">
<table class="table">
   <tr>
    <th colspan="2" class="title_1">查询命格活动</th>
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
	</select>	</td>
  </tr> 
  <tr>
    <td align="right"><strong>时间范围</strong></td>
	<td>
	<input name="stime" id="stime" type="text" onclick="showcalendar(event, this)"   value="<?php echo $stime?>" size="10" readonly /> - 
	<input name="etime" id="etime" type="text" onclick="showcalendar(event, this)"   value="<?php echo $etime?>" size="10" readonly />
	</td>
  </tr> 
  <?php //if($sid_arr) { ?>
  <tr>
    <td align="right"><strong>执行赠送</strong></td>
	<td>
	<select name="e">
	<option value="0">不执行</option>
	<option value="1">执行</option>
	</select>
	谨慎选择！<span class="graytext">选择执行则将按设置发放相应奖励给玩家</span>
	</td>
  </tr> 
  <?php //} ?>
  <tr>
    <td align="right"><strong>操作密码</strong></td>
	<td><input type="text" name="pwd" value="" size="10"/> <?php echo $errmsg; ?><span class="graytext">选择执行则必须输入操作密码，密码请联系某某某获取</span></td>
  </tr> 
  <tr>
    <td align="right"><input type="hidden" name="action" value="ok" /><input type="hidden" name="run" value="<?php echo random(10)?>" /></td>  
    <td>
	<input type="submit" id="Submit" name="Submit" value="搜索"  class="button" onClick='javascript: return confirm("你确定执行操作？");'/></td>
  </tr>    
</table>
</form>

<?php
if($action == 'ok'){

	if ($run == $_COOKIE['sxd_fateparty_e']) 
	{
		echo '<strong class="redtext">请勿重复操作！</strong>';
		exit();
	}
	setcookie('sxd_fateparty_e', $run,0,$cookiepath,$cookiedomain);

	if($sid_arr != 0){
		$set_sid =  " and sid in ($sid_arr)";
	}
		
	$query = $db->query("select cid,sid,name from servers where cid = '$cid'  $set_sid");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{	
			if ($stime && $etime) {
				$list = FateUser($rs['sid'],$stime,$etime,1);
				$rs['list'] = $list['list'];
				//print_r($rs['list']);
				if ($rs['list']) {
					foreach($rs['list'] as $frs) {
						$rs['u'][] = $frs['username'];	
						$rs['nlist'][$frs['username']] = $frs['player_id'];
					}
					if ($rs['u']) $rs['ulist'] = array_count_values($rs['u']);
					//print_r($rs['ulist']);
				}

			}
?>
	<br />
	<table class="table">
	   <tr class="title_3">
		<td colspan="2"><strong><?php echo $rs['name']?></strong></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td width='100' class="td2" valign="top" align="right">猎到1种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],1)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],1,$list['api'],array('skill'=>10000),$e); ?></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td class="td2" valign="top" align="right">猎到2种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],2)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],2,$list['api'],array('skill'=>10000,'fame'=>1000),$e); ?></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td class="td2" valign="top" align="right">猎到3种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],3)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],3,$list['api'],array('skill'=>10000,'fame'=>2000),$e); ?></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td class="td2" valign="top" align="right">猎到4种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],4)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],4,$list['api'],array('skill'=>10000,'fame'=>3000),$e); ?></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td class="td2" valign="top" align="right">猎到5种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],5)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],5,$list['api'],array('skill'=>10000,'fame'=>5000),$e); ?></td>
	   </tr>
	   <tr onmouseover=this.className="td3" onmouseout=this.className="">
		<td class="td2" valign="top" align="right">猎到6种<br /><?php echo SearchUserNum($rs['ulist'],$rs['nlist'],6)?></td><td><?php echo SearchUser($rs['cid'],$rs['sid'],$rs['ulist'],$rs['nlist'],6,$list['api'],array('skill'=>10000,'fame'=>10000),$e); ?></td>
	   </tr>
	</table>
<?php
			unset($list);
		}
	}
}
$db->close();
?>
</body>
</html>