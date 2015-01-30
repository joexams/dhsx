<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once(UCTIME_ROOT."/gold/OpenApiV3.php");
//----------------------------充值接口--------------------------------------------
$dir = UCTIME_ROOT."/gold/";
$val = var_export($_GET, TRUE);
$openid = ReqStr('openid');//用户名
$appid = ReqStr('appid');
$ts = ReqNum('ts');
$payitem = ReqStr('payitem');
$amt = ReqStr('amt');
$token = ReqStr('token');
$billno = ReqStr('billno');
$version = ReqStr('version');
$providetype = ReqStr('providetype');
$payamt_coins = ReqStr('payamt_coins');
$pubacct_payamt_coins = ReqStr('pubacct_payamt_coins');
$sig = ReqStr('sig');
$ip = getIp();//来路IP	

$goldcoupon   = isset($_GET['goldcoupon']) && intval($_GET['goldcoupon']) > 0 ? intval($_GET['goldcoupon']) : 0;
$slivercoupon = isset($_GET['slivercoupon']) && intval($_GET['slivercoupon']) > 0 ? intval($_GET['slivercoupon']) : 0;
$coppercoupon = isset($_GET['coppercoupon']) && intval($_GET['coppercoupon']) > 0 ? intval($_GET['coppercoupon']) : 0;
$payamt_coins = intval($payamt_coins) > 0 ? intval($payamt_coins) : 0;
$pubacct_payamt_coins = intval($pubacct_payamt_coins) > 0 ? intval($pubacct_payamt_coins) : 0;
$oldamt = $amt;
$amt = $amt + ($pubacct_payamt_coins * 10) + ($payamt_coins * 10);

if (!$openid || !$appid || !$ts || !$payitem  || !$amt || !$token || !$billno || !$sig) {
	echo json_encode(array('ret'=> 4,'msg'=>'get null'));
	//writetofile('log.txt', $val."\n\n====================###=======================\n\n",'a',$dir);//写入
	exit();
}

//---------查TOKEN-----------------------------------------------------------------------------------------------------------------------------------------------------------
$trs = $db->fetch_first("select * from pay_token where token = '$token'");
if($trs)
{
	$domain = 's'.$trs['zoneid'].'.app100616996.qqopenapp.com';//组合成游戏服地址
}else{
	echo json_encode(array('ret'=> 4,'msg'=>'token err'));
	//writetofile('log.txt',"11\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}





//---------判断是否合服-----------------------------------------------------------------------------------------------------------------------------------------------------------
$cts = $db->fetch_first("select combined_to,sid,`name` from servers where FIND_IN_SET('$domain',server) <> 0 and combined_to > 0");
if($cts)
{
	$name = $cts['name'];
	$sid_old = $cts['sid'];
	$combined_to = $cts['combined_to'];
	
	$set_where = "where A.sid = '$combined_to'";
}else{
	$set_where = "where FIND_IN_SET('$domain',A.server) <> 0";
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


$query = $db->query("
	select 
		A.sid,
		A.name,
		A.server_ver,
		A.api_server,
		A.api_port,
		A.api_pwd,
		A.open_date,
		A.test,
		A.is_new,
		B.cid,
		B.charge_ips,
		B.key,
		B.coins_rate,
		B.slug
	from 
		servers A 
		left join company B on A.cid = B.cid 
		$set_where
	");		
if($db->num_rows($query))
{
	$rs = $db->fetch_array($query);	
	$open_date = $rs['open_date']; //开服时间	
	$sname = $rs['name']; //代号
	$cid = $rs['cid']; //所属运营
	$sid = $rs['sid']; //服务器编号
	$test = $rs['test']; //是否测试服
	//$charge_ips = $rs['charge_ips'] ? explode("|",trim($rs['charge_ips'])) : ''; //允许IP
	$key = $rs['key']; //密钥
	$coins_rate = $rs['coins_rate']; //元宝兑换比率	
	$slug = $rs['slug']; //标示	
}else{
	echo json_encode(array('ret'=> 4,'msg'=>'server null'));
	//writetofile('log.txt',"2\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}

//------------------------检查通讯密码------------------------------------
$_GET['billno'] = str_replace("-", "%2D", $_GET['billno']);
foreach($_GET  as $k => $value){ 
       $params[$k] = $value;
}
$sig = $params["sig"];
unset($params["sig"]);

$my_sig = SnsSigCheck::makeSig("get", "/gold/index.php", $params, $key.'&');
if ($version != 'v3') {
	$param_for_sign = array();
	foreach ($params as $key => $value) {
            if(in_array($key, array('appid','openid','payitem','amt','token','billno','ts'))) {
                $param_for_sign[$key] = $value;
            }
        }

	$param_for_sign['billno'] = str_replace("%2D", "-", $_GET['billno']); 
	$my_sig = strtoupper(makeV2Sig($param_for_sign, '12731d393543f86736b8d92654d3e6f1'));
	$sig = strtoupper($sig);
}

if ($my_sig != $sig) {
	echo json_encode(array('ret'=> 4,'msg'=>'sig error'));
	//writetofile('log.txt',"3\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}
//------------------------获取充值定单是否存在------------------------------------
$paydata = $db->fetch_first("select success from pay_data where cid = '$cid' and oid = '$billno'");
if ($paydata) {//有定单记录

	if ($paydata['success']) {//成功充过值

		echo json_encode(array('ret'=> 0,'msg'=>'ok'));
		//writetofile('log.txt',"4\n\n===========================================\n\n",'a',$dir);//写入
		exit();
	}else{//待充状态
		echo json_encode(array('ret'=> 4,'msg'=>'billno error'));
		//writetofile('log.txt',"5\n\n===========================================\n\n",'a',$dir);//写入
		exit();		
	}
}

//------------------------数据操作接口------------------------------------

require_once callApiVer($rs['server_ver']);
api_base::$SERVER = $rs['api_server'];
api_base::$PORT   = $rs['api_port'];
api_base::$ADMIN_PWD   = $rs['api_pwd'];

//---
if($combined_to)
{
	$openid_old = $openid;//先记录合服前的帐号
}
$openid = CombinedUser($openid,$name,$combined_to);

//---
$player = api_admin::find_player_by_username($openid);
if (!$player['result']) {
	echo json_encode(array('ret'=> 4,'msg'=>'openid null'));
	//writetofile('log.txt',"6\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}
$nickname = api_admin::get_nickname_by_username($openid);
if (!isset($nickname['nickname'][1])) {	
	echo json_encode(array('ret'=> 4,'msg'=>'openid null'));
	//writetofile('log.txt',"7\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}
//------------------------------------------------------------
$dtime = $ts ? date('Y-m-d H:i:s',$ts) : date('Y-m-d H:i:s');//转为时间
$timenow = $dtime;
$dtime_unix = strtotime($dtime);
$pdate = date('Y-m-d');
$player_id = $player['player_id'];//获取玩家ID
if($test == 1){
	$status = 1;//测试服，充值都是测试
}else{
	$status = $dtime < $open_date  ? 1 : 0;//状态，判断是否测试期
}

$nickname = addslashes($nickname['nickname'][1]);//取用户游戏呢称
$amount = round($amt/100,2);//转换成人民币
$yb = explode("*",$payitem);
$coins = $yb[1]*$yb[2];//转换成要加的元宝
$present_ingot = $coins-round($oldamt/10);//赠送元宝
$charge_ingot = $coins-$present_ingot;//实际充值元宝

//$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$cid' and sid = '$sid' and username = '$openid'"),0); //是否首充
//if($combined_to && !$isnew)//如果是合服则判断被合服的充值
//{
//	$isnew = $db->result($db->query("select count(*) from pay_data where cid = '$cid' and sid = '$sid_old' and username = '$openid_old'"),0); //合服前是否首充
//}
$db->query("insert into pay_data(cid,sid,player_id,username,nickname,amount,coins,oid,dtime,dtime_unix,success,status,vip_level_up,sign,ip) values ('$cid','$sid','$player_id','$openid','$nickname','$amount','$coins','$billno','$dtime','$dtime_unix',0,'$status',0,'$sig','$ip')") ;

if ($goldcoupon > 0 || $slivercoupon > 0 || $coppercoupon > 0 || $payamt_coins > 0 || $pubacct_payamt_coins > 0){
	$db->query("INSERT INTO pay_data_detail(oid,amt,goldcoupon,slivercoupon,coppercoupon,payamt_coins,pubacct_payamt_coins,dateline) VALUES ('$billno','$oldamt','$goldcoupon','$slivercoupon', '$coppercoupon','$payamt_coins','$pubacct_payamt_coins','$dtime_unix')");
}

$isnew = 1;
if (!$status) {
	if ($combined_to) {
		$payscount = $db->result($db->query("select count(*) from pay_player_servers where cid='$cid' and sid='$sid_old' and username='$openid_old'"),0);
		if (!$payscount) {
			$db->query("INSERT INTO pay_player_servers(cid,sid,username,nickname,player_id,pay_num,amount,last_pay_amount,last_pay_time) VALUES ('$cid','$sid_old','$openid_old','$nickname', '$player_id','1','$amount','$amount','$dtime_unix')");
            $isnew = 0;
		}else {
			$db->query("UPDATE pay_player_servers SET pay_num=pay_num+1,amount=amount+$amount,last_pay_amount=$amount,last_pay_time='$dtime_unix' WHERE cid='$cid' AND sid='$sid_old' AND username='$openid_old'");
		}
	}else {
		$payscount = $db->result($db->query("select count(*) from pay_player_servers where cid='$cid' and sid='$sid' and username='$openid'"),0);
		if (!$payscount) {
			$db->query("INSERT INTO pay_player_servers(cid,sid,username,nickname,player_id,pay_num,amount,last_pay_amount,last_pay_time) VALUES ('$cid','$sid','$openid','$nickname', '$player_id','1','$amount','$amount','$dtime_unix')");
            $isnew = 0;
		}else {
			$db->query("UPDATE pay_player_servers SET pay_num=pay_num+1,amount=amount+$amount,last_pay_amount=$amount,last_pay_time='$dtime_unix' WHERE cid='$cid' AND sid='$sid' AND username='$openid'");
		}
	}
}

$msg = api_admin::charge($player_id,$billno,$coins);//充值累积用于VIP等级提升
if ($msg['result'] == 1) {//充值成功
	$db->query("update pay_data set vip_level_up = 1,ditme_up = now() where  cid= '$cid' and oid = '$billno'");//确定VIP等级接口执行后再更新
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------
	
	$msgingot = api_admin::increase_player_ingot($player_id,$charge_ingot,$present_ingot);//加元宝
	if ($msgingot['result'] == 1) {//加元宝成功
		$db->query("update pay_data set success = 1,ditme_up = now() where cid = '$cid' and oid = '$billno'");//确定充元宝也成功后在更新
		
		//------------------------------------------------------------------------------		
		require_once UCTIME_ROOT.'/mod/'.$rs['server_ver'].'/set_api.php';
		if (!$isnew)//如果第一次冲
		{
			//------------------------------记录每日新增充值用户数-----------------------------
			//if (!$status)//并且是非测试其
			//{
				$d = $db->result($db->query("select count(*) from pay_day_new where cid = '$cid' and sid = '$sid' and pdate = '$pdate'"),0);
				if (!$d)
				{
					$db->query("insert into pay_day_new(cid,sid,pdate,new_player) values ('$cid','$sid','$pdate',1)");
				}else{
					$db->query("update pay_day_new set new_player = new_player+1 where cid = '$cid' and sid = '$sid' and pdate = '$pdate'");
				}
			//}
			
		}
		//------------------------------------------------------------------------------
		echo json_encode(array('ret'=> 0,'msg'=>'ok'));
		//------------------------删除TOKEN表记录------------------------------------------------------
		$db->query("delete from pay_token where token = '$token'");
		//------------------------统计个人充值------------------------------------------------------
		SetPayPlayer($openid,$nickname,$amount,$cid,$sid,$combined_to);
		//------------------------开服前3天充值活动------------------------------------------------------
		if(!$combined_to && date('Y-m-d H:i:s') >= '2012-03-02 10:00:00') SetGiftDays3($player_id,$coins,$open_date);//非合服
		//------------------------送伙伴活动------------------------------------------------------
		//if($domain == 's2.app100616996.qqopenapp.com' && $rs['server_ver'] == 2012061801) {//测试服才执行
			SetAddRole('2012-06-27 00:00:00','2012-06-30 23:59:59',$player_id,$openid,$coins,27,5000);//端午送财神活动
		//}
		
		//writetofile('log.txt',"8\n\n===========================================\n\n",'a',$dir);//写入
		exit();
	}else{
		echo json_encode(array('ret'=> 0,'msg'=>'ok'));
		//writetofile('log.txt',"9\n\n===========================================\n\n",'a',$dir);//写入
		exit();
	}
}else{
	echo json_encode(array('ret'=> 0,'msg'=>'ok'));
	//writetofile('log.txt',"10\n\n===========================================\n\n",'a',$dir);//写入
	exit();
}
$db->close();

function makeV2Sig ($params, $appkey) {
        ksort($params);

        $step2 = '';
        foreach ($params as $key => $value) {
            $step2 .= "$key$value";
        }

        $step3 = $step2 . $appkey;
        $step4 = md5($step3);

        return $step4;
    }
?>
