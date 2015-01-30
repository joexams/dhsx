<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
include_once(UCTIME_ROOT."/include/code.inc.php");
//----------------------------提交CODE--------------------------------------------

$nickname = ReqStr('username');//昵称
$openid = ReqStr('openid');//昵称
$nickname = stripslashes($nickname);//反转义昵称
$code = trim(ReqStr('code'));//激活码
$time = ReqNum('time');//UNIX时间戳
$domain = ReqStr('webdomain');//充的是哪个服二级域名
$ip = getIp();//来路IP
$overtime = 600; #设定300秒超时	

if (!$code)//内容为空
{
	echo 2;
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

//------------------------查找要提交的服------------------------------------
$servers = $db->fetch_first("
select 		
	A.*,
	B.slug,
	B.key
from 
	servers A
	left join company B on A.cid = B.cid
	$set_where
");	

if($servers)
{
	$cid = $servers['cid'];//cid
	$key = $servers['key'];//KEY
	$slug = $servers['slug'];//哪个平台
	$sid = $servers['sid'];//哪个服
}else{
	echo 4;//来路不明
	exit();	
}

//--------------------------------------------------------


require_once callApiVer($servers['server_ver']);
api_base::$SERVER = $servers['api_server'];
api_base::$PORT   = $servers['api_port'];
api_base::$ADMIN_PWD   = $servers['api_pwd'];
//$nickname = urldecode($nickname);//用户名
//$code = urldecode($code);//激活码
//----------------------帐号不存在--------------------------------------
if (!empty($openid)) {
	$player = api_admin::find_player_by_username($openid);
	if (!$player['result']) {
		echo 5;
		exit();
	}
	if (empty($nickname)) {
		$user = api_admin::get_nickname_by_username($openid);//找帐号
		if (!$user['result']) {
			echo 5;
			exit();
		}
		$nickname = $user['nickname'][1];
	}

	$username = $openid;
	$nickname = addslashes($nickname);
}else {
	$player = api_admin::find_player_by_nickname($nickname);
	if (!$player['result']) {
		echo 5;
		exit();
	}
	$user = api_admin::get_username_by_nickname($nickname);//找帐号
	if (!$user['result']) {
		echo 5;
		exit();
	}
	$username = addslashes($user['username'][1]);
	$nickname = addslashes($nickname);
}


$code = strtolower($code);//转小写
//-------------------------------------------------------------------------------
if($combined_to)//如果是合服要将后缀去掉再加密对比
{
	if (strpos($name,'_',0)) 
	{
		$s = explode("_",trim($name));
		$sname = $s[1];
		
	}else{
		$sname = $name;
	}
	$hz = '.'.$sname;
	$username = str_replace($hz, "", $username);
	$sid = $sid_old;//指向旧服数据
}	


//============================================全平台活动处理=================================================================

$p_code = explode("_",$code);

//==========================================活动===================================================================

//12月QQ会员拉收活动全月礼包
/*
code_party_kc('2012-12-17 00:00:00','2013-03-31 23:59:59','qq105',$player['player_id'],array(
	'coins'=>4000000,
	'skill'=>130000,
	'item' => array(
		0 => array('item_id' => 1375, 'level' => 1, 'number' => 8),
		1 => array('item_id' => 1403, 'level' => 1, 'number' => 8),
	),
	'fate' => array(
		0 => array('fate_id' => 44, 'level' => 1, 'number' => 10, 'actived_fate_id1' => 0, 'actived_fate_id2' => 0),
	),
	
),'恭喜您获得[4000000铜钱]、[130000阅历]、[4级绝技攻击道符*8]、[4级速度道符*8]、[命格碎片*10]',1, 1, 1, 30);
*/


//12月QQ会员拉收活动单月礼包
/*
code_party_kc('2012-12-17 00:00:00','2013-03-31 23:59:59','qq104',$player['player_id'],array(
	'coins'=>300000,
	'fame'=>800,
	'skill'=>1000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 403, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[300000铜钱]、[800声望]、[1000阅历]、[鲁班神钻*1]、[5品绝技丹制作卷*1]',1, 1, 1, 30);
*/


//3366 Bug奖励礼包  10W铜钱 100声望

code_party_kc('2012-11-29 00:00:00','2013-06-30 23:59:59','qq103',$player['player_id'],array('coins'=>100000, 'fame'=>100,
),'恭喜您获得[100000铜钱]、[100声望]',1, 1, 0, 5);


//心悦专区普发礼包  8W铜钱 8000阅历
/*
code_party_kc('2012-11-22 00:00:00','2013-01-31 23:59:59','qq102',$player['player_id'],array('coins'=>80000, 'skill'=>8000,
),'恭喜您获得[80000铜钱]、[8000阅历]',1);
*/

//超Q专区超Q年费礼包  黄飞虎变身卡*1 3级仙石宝袋*2 鲁班神钻*2 4级速度道符*2
code_party_kc('2012-11-22 00:00:00','2013-06-30 23:59:59','qq101',$player['player_id'],array(
	'item' => array(
		0 => array('item_id' => 1275, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1370, 'level' => 1, 'number' => 2),
		2 => array('item_id' => 1326, 'level' => 1, 'number' => 2),
		3 => array('item_id' => 1403, 'level' => 1, 'number' => 2),
	),
),'恭喜您获得[黄飞虎变身卡*1]、[3级仙石宝袋*2]、[鲁班神钻*2]、[4级速度道符*2]',1);


//超Q专区主题月礼包  30W铜钱 5000阅历 牡丹仙子变身卡*1 4级生命道符*1  
code_party_kc('2012-11-22 00:00:00','2013-06-30 23:59:59','qq100',$player['player_id'],array('coins'=>300000, 'skill'=>5000,
	'item' => array(
		0 => array('item_id' => 1116, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1332, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[300000铜钱]、[5000阅历]、[牡丹仙子变身卡*1]、[4级生命道符*1]',1);


//超Q专区新手礼包  10W铜钱 300声望 黄飞虎变身卡*1 4级绝技攻击道符*1  
code_party_kc('2012-11-22 00:00:00','2013-06-30 23:59:59','qq99',$player['player_id'],array('coins'=>100000, 'fame'=>300,
	'item' => array(
		0 => array('item_id' => 1275, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1375, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[100000铜钱]、[300声望]、[黄飞虎变身卡*1]、[4级绝技攻击道符*1]',1);


//超Q专区抢礼包  10W铜钱 300声望 冰魁变身卡*1  
code_party_kc('2012-11-22 00:00:00','2013-06-30 23:59:59','qq98',$player['player_id'],array('coins'=>100000, 'fame'=>300,
	'item' => array(
		0 => array('item_id' => 1121, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[100000铜钱]、[300声望]、[冰魁变身卡*1]',1);


//黄钻&财付通开通三月礼包  100W铜钱 3级仙石宝袋*2 鲁班神钻*2 
code_party_kc('2012-11-21 00:00:00','2013-06-30 23:59:59','qq97',$player['player_id'],array('coins'=>1000000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 2),
		1 => array('item_id' => 1370, 'level' => 1, 'number' => 2),
	),
),'恭喜您获得[1000000铜钱]、[3级仙石宝袋*2]、[鲁班神钻*2]',1);


//黄钻&财付通开通单月礼包  20W铜钱 5000阅历
code_party_kc('2012-11-21 00:00:00','2013-06-30 23:59:59','qq96',$player['player_id'],array('coins'=>200000, 'skill'=>5000,
),'恭喜您获得[200000铜钱]、[5000阅历]',1);

/*
//黄钻抵扣券每日礼包3 3W铜钱 1000阅历 50声望
code_party_kc('2012-11-20 00:00:00','2013-06-30 23:59:59','qq95',$player['player_id'],array('coins'=>30000, 'skill'=>1000, 'fame'=>50,
),'恭喜您获得[30000铜钱]、[1000阅历]、[50声望]',1, 1, 4);


//黄钻抵扣券每日礼包2 1W铜钱 1000阅历 50声望
//code_party_kc('2012-11-20 00:00:00','2013-06-30 23:59:59','qq94',$player['player_id'],array('coins'=>10000, 'skill'=>1000, 'fame'=>50,
),'恭喜您获得[10000铜钱]、[1000阅历]、[50声望]',1, 1, 4);


//黄钻抵扣券每日礼包1 1W铜钱 1000阅历
code_party_kc('2012-11-20 00:00:00','2013-06-30 23:59:59','qq93',$player['player_id'],array('coins'=>10000, 'skill'=>1000,
),'恭喜您获得[10000铜钱]、[1000阅历]',1, 1, 4);
*/

//钻皇12月抽奖  10W铜钱 牡丹仙子变身卡*1
code_party_kc('2012-11-20 00:00:00','2013-06-30 23:59:59','qq92',$player['player_id'],array('coins'=>100000,
	'item' => array(
		0 => array('item_id' => 1116, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[100000铜钱]、[牡丹仙子变身卡*1]',1);


//七雄争霸502礼包  100W铜钱 500声望 包子*10
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq91',$player['player_id'],array('fame'=>500,'coins'=>1000000,
	'item' => array(
		0 => array('item_id' => 1217, 'level' => 1, 'number' => 10),
	),
),'恭喜您获得[1000000铜钱]、[包子*10]、[500声望]',1, 1, 0, 5);


//七雄争霸501礼包  鲁班神钻*2 4级生命仙石*2 100W铜钱 1W阅历
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq90',$player['player_id'],array('coins'=>1000000,'skill'=>10000,
	'item' => array(
		0 => array('item_id' => 1325, 'level' => 1, 'number' => 2),
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 2),
	),
),'恭喜您获得[鲁班神钻*2]、[4级生命仙石*2]、[1000000铜钱]、[10000铜钱]',1, 1, 0, 5);


//七雄争霸202礼包  三品武力丹*1 100声望 包子*10
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq89',$player['player_id'],array('fame'=>100,
	'item' => array(
		0 => array('item_id' => 440, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1217, 'level' => 1, 'number' => 10),
	),
),'恭喜您获得[三品武力丹*1]、[包子*10]、[100声望]',1, 1, 0, 5);


//七雄争霸201礼包  4级生命仙石*1 鲁班神钻*1 10W铜钱
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq88',$player['player_id'],array('coins'=>100000,
	'item' => array(
		0 => array('item_id' => 1325, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[4级生命仙石*1]、[鲁班神钻*1]、[100000铜钱]',1, 1, 0, 5);


//七雄争霸102礼包 四品绝技葫芦*1 2级仙石宝袋*5
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq87',$player['player_id'],array(
	'item' => array(
		0 => array('item_id' => 993, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1369, 'level' => 1, 'number' => 5),
	),
),'恭喜您获得[四品绝技葫芦*1]、[2级仙石宝袋*5]',1, 1, 0, 5);


//七雄争霸101礼包 3级生命仙石*1 3级速度道符*3
code_party_kc('2012-11-19 00:00:00','2013-06-30 23:59:59','qq86',$player['player_id'],array(
	'item' => array(
		0 => array('item_id' => 1324, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1402, 'level' => 1, 'number' => 3),
	),
),'恭喜您获得[3级生命仙石*1]、[3级速度道符*3]',1, 1, 0, 5);


//开通会员礼包 50W铜钱 2级仙石宝袋*1
/*
code_party_kc('2012-11-15 00:00:00','2012-12-30 23:59:59','qq85',$player['player_id'],array('coins'=>500000,
	'item' => array(
		0 => array('item_id' => 1369, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[500000铜钱]、[2级仙石宝袋*1]',1);
*/

//会员免费礼包 10W铜钱 1W阅历
/*
code_party_kc('2012-11-15 00:00:00','2012-12-30 23:59:59','qq84',$player['player_id'],array('coins'=>100000,'skill'=>10000,
),'恭喜您获得[100000铜钱]、[10000阅历]',1);
*/

//TGC直播活动 100万铜钱 3级仙石宝袋*1
/*
code_party_kc('2012-11-15 00:00:00','2012-12-30 23:59:59','qq83',$player['player_id'],array('coins'=>1000000,
	'item' => array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[1000000铜钱]、[3级仙石宝袋*1]',1, 1, 0, 7);
*/

//07073钻石礼包 200W铜钱 3级仙石宝袋*1 鲁班神钻*1
/*
code_party_kc('2012-11-12 00:00:00','2012-12-30 23:59:59','qq82',$player['player_id'],array('coins'=>2000000,
	'item' => array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 2),
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[2000000铜钱]、[3级仙石宝袋*2]、[鲁班神钻*1]',1);
*/

//07073水晶礼包 100W铜钱 3级仙石宝袋*1
/*
code_party_kc('2012-11-12 00:00:00','2012-12-30 23:59:59','qq81',$player['player_id'],array('coins'=>1000000,
	'item' => array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[1000000铜钱]、[3级仙石宝袋*1]',1);
*/


//07073黄金礼包 50W铜钱 2级仙石宝袋*1
/*
code_party_kc('2012-11-12 00:00:00','2012-12-30 23:59:59','qq80',$player['player_id'],array('coins'=>500000,
	'item' => array(
		0 => array('item_id' => 1369, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[500000铜钱]、[2级仙石宝袋*1]',1);
*/

//开通QQ会员新手礼包 100声望 10W铜钱 1000阅历
/*
code_party_kc('2012-11-05 00:00:00','2012-12-30 23:59:59','qq79',$player['player_id'],array('fame'=>100,'coins'=>100000,'skill'=>1000,
),'恭喜您获得[100声望]、[100000铜钱]、[1000阅历]',1);
*/

//开通QQ会员年费礼包额外 5000声望 200W铜钱 鲁班神钻*10 四级生命仙石*1
/*
code_party_kc('2012-11-05 00:00:00','2012-12-30 23:59:59','qq78',$player['player_id'],array('fame'=>5000,'coins'=>2000000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 10),
		1 => array('item_id' => 1325, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[5000声望]、[2000000铜钱]、[鲁班神钻*10]、[四级生命仙石*1]',1, 1, 0, 25);
*/

//开通QQ会员年费礼包 5000声望 200W铜钱 鲁班神钻*10
/*
code_party_kc('2012-11-05 00:00:00','2012-12-30 23:59:59','qq77',$player['player_id'],array('fame'=>5000,'coins'=>2000000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 10),
	),
),'恭喜您获得[5000声望]、[2000000铜钱]、[鲁班神钻*10]',1, 1, 0, 25);
*/

//开通QQ会员月费礼包额外 500声望 20W铜钱 鲁班神钻*1 三级生命仙石*1
/*
code_party_kc('2012-11-05 00:00:00','2012-12-30 23:59:59','qq76',$player['player_id'],array('fame'=>500,'coins'=>200000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 1),
		1 => array('item_id' => 1324, 'level' => 1, 'number' => 1),
	),	
),'恭喜您获得[500声望]、[200000铜钱]、[鲁班神钻*1]、[三级生命仙石*1]',1, 1, 0, 30);
*/

//开通QQ会员月费礼包 500声望 20W铜钱 鲁班神钻*1
/*
code_party_kc('2012-11-05 00:00:00','2012-12-30 23:59:59','qq75',$player['player_id'],array('fame'=>500,'coins'=>200000,
	'item' => array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[500声望]、[200000铜钱]、[鲁班神钻*1]',1, 1, 0, 30);
*/


//TGC卡册礼包 200W铜钱 3级仙石宝袋*2 海龙王变身卡*1
/*
code_party_kc('2012-11-02 00:00:00','2012-12-30 23:59:59','qq74',$player['player_id'],array('coins'=>2000000,
	'item' => array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 2),
		1 => array('item_id' => 1120, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[2000000铜钱]、[3级仙石宝袋*2]、[远古冰晶兽变身卡*1]',1);
*/

//QQ彩贝礼包 100W铜钱 2级仙石宝袋*2 海龙王变身卡*1
code_party_kc('2012-11-02 00:00:00','2013-06-30 23:59:59','qq73',$player['player_id'],array('coins'=>1000000,
	'item' => array(
		0 => array('item_id' => 1369, 'level' => 1, 'number' => 2),
		1 => array('item_id' => 1117, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[1000000铜钱]、[2级仙石宝袋*2]、[海龙王变身卡*1]',1);


//黄钻回馈活动 20W铜钱 2W阅历 冰魁变身卡*5
code_party_kc('2012-11-01 00:00:00','2013-06-30 23:59:59','AGNJZA',$player['player_id'],array('coins'=>200000,'skill'=>20000,
	'item' => array(
		0 => array('item_id' => 1121, 'level' => 1, 'number' => 5),
	),
),'恭喜您获得[200000铜钱]、[20000阅历]、[冰魁变身卡*5]',1);

//钻皇抽奖礼包 10W铜钱 1W阅历 1级生命仙石*4 牡丹仙子变身卡*1 
code_party_kc('2012-10-31 00:00:00','2013-06-30 23:59:59','qq72',$player['player_id'],array('coins'=>100000,'skill'=>10000,
	'item' => array(
		0 => array('item_id' => 1322, 'level' => 1, 'number' => 4),
		1 => array('item_id' => 1116, 'level' => 1, 'number' => 1),
	),
),'恭喜您获得[100000铜钱]、[10000阅历]、[1级生命仙石*4]、[牡丹仙子变身卡*1]',1);

//微博传播活动 50W铜钱
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq71',$player['player_id'],array('coins'=>500000,),'恭喜您获得[500000铜钱]',1);


//微博传播活动 50体力
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq70',$player['player_id'],array('power'=>50,),'恭喜您获得[50体力]',1);


//微博传播活动 1W阅历
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq69',$player['player_id'],array('skill'=>10000,),'恭喜您获得[10000阅历]',1);


//微博传播活动 2级仙石宝袋
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq68',$player['player_id'],array(
	'item'=>array(
		0 => array('item_id' => 1369, 'level' => 1, 'number' => 1),//2级仙石宝袋
	),
),'恭喜您获得[2级仙石宝袋*1]',1);


//微博传播活动 10W铜钱
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq67',$player['player_id'],array('coins'=>100000,),'恭喜您获得[100000铜钱]',1);

//微博传播活动 1级仙石宝袋
code_party_kc('2012-10-18 00:00:00','2013-06-30 23:59:59','qq66',$player['player_id'],array(
	'item'=>array(
		0 => array('item_id' => 1328, 'level' => 1, 'number' => 1),//1级仙石宝袋
	),
),'恭喜您获得[1级仙石宝袋*1]',1);



//快乐掌柜蓝钻礼包
//code_party_kc('2012-10-17 00:00:00','2012-12-31 23:59:59','qq65',$player['player_id'],array('coins'=>200000,'skill'=>20000,),'恭喜您获得[20000阅历]、[200000铜钱]',1);

//快乐掌柜礼包
//code_party_kc('2012-10-17 00:00:00','2012-12-31 23:59:59','qq64',$player['player_id'],array('coins'=>100000,'skill'=>10000,),'恭喜您获得[10000阅历]、[100000铜钱]',1);




//红钻 红钻尊享礼包
/*
code_party_kc('2012-10-10 00:00:00','2012-12-31 23:59:59','qq63',$player['player_id'],array(
	'coins'=>200000,
	'item'=>array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 2),//3级仙石宝袋
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[3级仙石宝袋*2]、[200000铜钱]',1);
*/

//红钻 大话神仙修行礼包
//code_party_kc('2012-10-10 00:00:00','2012-12-31 23:59:59','qq62',$player['player_id'],array('coins'=>100000,'skill'=>10000,),'恭喜您获得[10000阅历]、[100000铜钱]',1);



//蓝钻 年费蓝钻礼包
/*
code_party_kc('2012-10-10 00:00:00','2012-12-31 23:59:59','qq61',$player['player_id'],array(
	'coins'=>1000000,
	'item'=>array(
		0 => array('item_id' => 1370, 'level' => 1, 'number' => 1),//3级仙石宝袋
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 1),//鲁班神钻
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[3级仙石宝袋*1]、[鲁班神钻*1]、[1000000铜钱]',1);
*/


//蓝钻 普通蓝钻礼包
/*
code_party_kc('2012-10-10 00:00:00','2012-12-31 23:59:59','qq60',$player['player_id'],array(
	'coins'=>100000,
	'skill'=>10000,
	'item'=>array(
		0 => array('item_id' => 1369, 'level' => 1, 'number' => 1),//2级仙石宝袋
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[2级仙石宝袋*1]、[10000阅历]、[100000铜钱]',1);
*/


//QT玩家访谈 访谈达人
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq59',$player['player_id'],array(
	'power'=>50,
	'fame'=>300,
	'coins'=>1000000,
	'skill'=>50000,
	'item'=>array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 2),//鲁班神钻
		1 => array('item_id' => 1323, 'level' => 1, 'number' => 5),//2级生命仙石
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[2级生命仙石*5]、[鲁班神钻*2]、[50000阅历]、[1000000铜钱]、[50体力]、[300声望]',1,1);
*/

//腾讯微博积分商城 微博礼包
//code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq58',$player['player_id'],array('coins'=>200000,'skill'=>20000),'恭喜您获得[20000阅历]、[200000铜钱]',1);


//QQ会员国庆活动 年费QQ会员特权礼包
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq57',$player['player_id'],array(
	'coins'=>500000,
	'skill'=>100000,
	'item'=>array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 2),//鲁班神钻
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[鲁班神钻*2]、[100000阅历]、[500000铜钱]',1);
*/

//QQ会员国庆活动 QQ会员专属礼包
//code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq56',$player['player_id'],array('coins'=>200000,'skill'=>20000),'恭喜您获得[20000阅历]、[200000铜钱]',1);


//官网QQ签名活动 推广大使
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq55',$player['player_id'],array(
	'coins'=>200000,
	'skill'=>10000,
	'item'=>array(
		0 => array('item_id' => 1323, 'level' => 1, 'number' => 2),//2级生命仙石
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[2级生命仙石*2]、[10000阅历]、[200000铜钱]',1);
*/

//官网QQ签名活动 保持签名15天
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq54',$player['player_id'],array(
	'coins'=>300000,
	'skill'=>30000,
	'item'=>array(
		0 => array('item_id' => 1326, 'level' => 1, 'number' => 2),//鲁班神钻
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[鲁班神钻*2]、[30000阅历]、[300000铜钱]',1);
*/

//官网QQ签名活动 保持签名10天
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq53',$player['player_id'],array(
	'coins'=>200000,
	'skill'=>20000,
	'item'=>array(
		0 => array('item_id' => 1323, 'level' => 1, 'number' => 3),//2级生命仙石
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[2级生命仙石*3]、[20000阅历]、[200000铜钱]',1);
*/


//官网QQ签名活动 修改签名
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq52',$player['player_id'],array(
	'coins'=>100000,
	'skill'=>10000,
	'item'=>array(
		0 => array('item_id' => 1322, 'level' => 1, 'number' => 4),//1级生命仙石
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[1级生命仙石*4]、[10000阅历]、[100000铜钱]',1);
*/



//官方签到活动 18枚印章礼包
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq51',$player['player_id'],array(
	'skill'=>100000,
	'item'=>array(
		0 => array('item_id' => 1324, 'level' => 1, 'number' => 2),//3级生命仙石
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 2),//鲁班神钻
		2 => array('item_id' => 1374, 'level' => 1, 'number' => 4),//三级绝技道符
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[3级生命仙石*4]、[鲁班神钻*2]、[100000阅历]、[三级绝技道符*4]',1);
*/

//官方签到活动 12枚印章礼包
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq50',$player['player_id'],array(
	'skill'=>50000,
	'item'=>array(
		0 => array('item_id' => 1323, 'level' => 1, 'number' => 4),//2级生命仙石
		1 => array('item_id' => 1326, 'level' => 1, 'number' => 1),//鲁班神钻
		2 => array('item_id' => 346, 'level' => 1, 'number' => 10),//紫玉牌
	),	
	'fate'=>array(),	
	'soul'=>array(),
),'恭喜您获得[2级生命仙石*4]、[鲁班神钻*1]、[50000阅历]、[紫玉牌*10]',1);
*/

//官方签到活动 6枚印章礼包
/*
code_party_kc('2012-09-20 00:00:00','2012-12-31 23:59:59','qq49',$player['player_id'],array(
	'skill'=>10000,
	'coins'=>100000,
	'item'=>array(
		0 => array('item_id' => 1323, 'level' => 1, 'number' => 2),//2级生命仙石
	),	
	'fate'=>array(),
	'soul'=>array(),
),'恭喜您获得[2级生命仙石*2]、[10000阅历]、[100000铜钱]',1);
*/




//=============================================================================================================





code_party_kc('2012-05-28 00:00:00','2012-06-30 23:59:59','qq1',$player['player_id'],array('skill'=>1000,'coins'=>50000,'fame'=>100),'恭喜您获得[100声望]、[1000阅历]、[50000铜钱]',1);
code_party_kc('2012-05-28 00:00:00','2012-06-30 23:59:59','qq2',$player['player_id'],array('skill'=>5000,'coins'=>100000,'fame'=>500),'恭喜您获得[500声望]、[5000阅历]、[100000铜钱]',1);
code_party_kc('2012-05-28 00:00:00','2012-06-30 23:59:59','qq3',$player['player_id'],array('skill'=>10000,'coins'=>500000,'fame'=>1000),'恭喜您获得[1000声望]、[10000阅历]、[500000铜钱]',1);

// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq4',$player['player_id'],array('skill'=>20000,'coins'=>200000),'恭喜您获得[20000阅历]、[200000铜钱]',1);//QQ管家
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq5',$player['player_id'],array('skill'=>15000,'coins'=>150000),'恭喜您获得[15000阅历]、[150000铜钱]',1);//QQ年费会员
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq6',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//QQ会员
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq7',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//财付通专享
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq8',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//财付通特权
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq9',$player['player_id'],array('skill'=>5000,'coins'=>50000,'power'=>50,'fame'=>50),'恭喜您获得[5000阅历]、[50000铜钱]、[50声望]、[50体力]',1);//收听礼包

// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq10',$player['player_id'],array('coins'=>100000),'恭喜您获得[100000铜钱]',1);//抽奖1
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq11',$player['player_id'],array('skill'=>10000),'恭喜您获得[10000阅历]',1);//抽奖2
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq12',$player['player_id'],array('fame'=>100),'恭喜您获得[100声望]',1);//抽奖3
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq13',$player['player_id'],array('power'=>100),'恭喜您获得[100体力]',1);//抽奖4
// code_party_kc('2012-06-14 00:00:00','2012-12-31 23:59:59','qq14',$player['player_id'],array('ingot'=>100),'恭喜您获得[100元宝]',1);//抽奖5
// code_party_kc('2012-06-20 00:00:00','2012-12-31 23:59:59','qq15',$player['player_id'],array('skill'=>10000,'coins'=>100000,'power'=>10,'ingot'=>10),'恭喜您获得[10000阅历]、[100000铜钱]、[10元宝]、[10体力]',1);//QQ网购礼包
//=============================================================================================================
//code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq16',$player['player_id'],array('skill'=>20000,'coins'=>200000),'恭喜您获得[20000阅历]、[200000铜钱]',1);//QQ音乐特权礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq17',$player['player_id'],array('skill'=>10000,'coins'=>200000),'恭喜您获得[10000阅历]、[200000铜钱]',1);//超级QQ特权礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq18',$player['player_id'],array('skill'=>5000,'coins'=>150000),'恭喜您获得[5000阅历]、[150000铜钱]',1);//QQ浏览器特权礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq19',$player['player_id'],array('skill'=>20000,'coins'=>300000,'fame'=>90),'恭喜您获得[20000阅历]、[300000铜钱]、[90声望]',1);//QQ拼音精英礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq20',$player['player_id'],array('skill'=>10000,'coins'=>150000,'fame'=>90),'恭喜您获得[10000阅历]、[150000铜钱]、[90声望]',1);//大话神仙激情夏日礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq21',$player['player_id'],array('coins'=>100000),'恭喜您获得[100000铜钱]',1);//大话神仙青铜礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq22',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//大话神仙白银礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq23',$player['player_id'],array('skill'=>10000,'coins'=>150000,'fame'=>60),'恭喜您获得[10000阅历]、[150000铜钱]、[60声望]',1);//大话神仙黄金礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq24',$player['player_id'],array('skill'=>15000,'coins'=>150000),'恭喜您获得[15000阅历]、[150000铜钱]',1);//Q+专属礼包
// code_party_kc('2012-06-30 00:00:00','2012-12-30 23:59:59','qq25',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//大话神仙典藏礼包
//=============================================================================================================
// code_party_kc('2012-07-04 00:00:00','2012-12-30 23:59:59','qq26',$player['player_id'],array('skill'=>10000,'coins'=>150000),'恭喜您获得[10000阅历]、[150000铜钱]',1);//拍拍充值特权礼包
//=============================================================================================================
// code_party_kc('2012-07-10 00:00:00','2012-12-31 23:59:59','qq27',$player['player_id'],array('skill'=>2000,'coins'=>20000,'fame'=>100),'恭喜您获得[2000阅历]、[20000铜钱]、[100声望]',1);//蓝钻10QB礼包
/*
$code_send_arr = array(
	'fame'=>1000,
	'item'=>array(
		0 => array('item_id' => 1215, 'level' => 1, 'number' => 1),//仙剑
		1 => array('item_id' => 380, 'level' => 1, 'number' => 1),//1品武力丹
		2 => array('item_id' => 438, 'level' => 1, 'number' => 1),//2品武力丹
		3 => array('item_id' => 440, 'level' => 1, 'number' => 1),//3品武力丹
		4 => array('item_id' => 1131, 'level' => 1, 'number' => 2),//齐天大圣变身卡
		),	
	'fate'=>array(),
	'soul'=>array(),
);
*/
// code_party_kc('2012-07-10 00:00:00','2012-12-31 23:59:59','qq28',$player['player_id'],$code_send_arr,'恭喜您获得[1000声望]、[1个大礼包]',1);//蓝钻150QB大礼包
// code_party_kc('2012-07-10 00:00:00','2012-12-31 23:59:59','qq29',$player['player_id'],array('skill'=>10000,'coins'=>200000,'fame'=>90),'恭喜您获得[10000阅历]、[200000铜钱]、[90声望]',1);//QQ网吧豪华大礼包
// code_party_kc('2012-07-12 00:00:00','2012-12-31 23:59:59','qq30',$player['player_id'],array('skill'=>5000,'coins'=>150000),'恭喜您获得[5000阅历]、[150000铜钱]',1);//大话神仙精英礼包
//=============================================================================================================

// code_party_kc('2012-07-18 00:00:00','2012-12-31 23:59:59','qq31',$player['player_id'],array('skill'=>10000,'coins'=>100000),'恭喜您获得[10000阅历]、[100000铜钱]',1);//游戏人生暑期礼包
// code_party_kc('2012-07-18 00:00:00','2012-12-31 23:59:59','qq32',$player['player_id'],array('skill'=>10000,'coins'=>200000),'恭喜您获得[10000阅历]、[200000铜钱]',1);//07073珍藏礼包
// code_party_kc('2012-07-18 00:00:00','2012-12-31 23:59:59','qq33',$player['player_id'],array(
// 	'item'=>array(
// 		0 => array('item_id' => 380, 'level' => 1, 'number' => 1),//1品武力丹
// 		1 => array('item_id' => 438, 'level' => 1, 'number' => 1),//2品武力丹
// 		2 => array('item_id' => 440, 'level' => 1, 'number' => 1),//3品武力丹
// 		3 => array('item_id' => 1131, 'level' => 1, 'number' => 2),//齐天大圣变身卡
// 		4 => array('item_id' => 1192, 'level' => 1, 'number' => 1),//葫芦坐骑
// 		),	
// 	'fate'=>array(),
// 	'soul'=>array(),
// ),'恭喜您获得[1个大礼包]',1);//07073大礼包100QB

// code_party_kc('2012-07-18 00:00:00','2012-12-31 23:59:59','qq34',$player['player_id'],array(
// 	'fame'=>1000,
// 	'item'=>array(
// 		0 => array('item_id' => 380, 'level' => 1, 'number' => 1),//1品武力丹
// 		1 => array('item_id' => 438, 'level' => 1, 'number' => 1),//2品武力丹
// 		2 => array('item_id' => 440, 'level' => 1, 'number' => 1),//3品武力丹
// 		3 => array('item_id' => 1131, 'level' => 1, 'number' => 2),//齐天大圣变身卡
// 		4 => array('item_id' => 1192, 'level' => 1, 'number' => 1),//葫芦坐骑
// 		),	
// 	'fate'=>array(),
// 	'soul'=>array(),
// ),'恭喜您获得[1000声望]、[1个大礼包]',1);//07073大礼包150QB



// code_party_kc('2012-08-07 00:00:00','2012-12-31 23:59:59','qq35',$player['player_id'],array('skill'=>5000,'coins'=>50000),'恭喜您获得[5000阅历]、[50000铜钱]',1);//新手小康包
//=============================================================================================================
//蓝钻铜质礼包
// code_party_kc('2012-08-11 00:00:00','2012-12-31 23:59:59','qq36',$player['player_id'],array(
// 	'power'=>50,
// 	'coins'=>100000,
// 	'item'=>array(
// 		0 => array('item_id' => 957, 'level' => 1, 'number' => 1),//玄奇法冠制作卷
// 		),	
// 	'fate'=>array(),
// 	'soul'=>array(),
// ),'恭喜您获得[100000铜钱]、[50体力]、[玄奇法冠制作卷*1]',1);

//蓝钻银质礼包
// code_party_kc('2012-08-11 00:00:00','2012-12-31 23:59:59','qq37',$player['player_id'],array(
// 	'item'=>array(
// 		0 => array('item_id' => 1202, 'level' => 1, 'number' => 1),//神兵重铸符
// 		1 => array('item_id' => 346, 'level' => 1, 'number' => 20),//紫玉牌
// 		2 => array('item_id' => 1051, 'level' => 1, 'number' => 1),//赤霄战靴制作卷
// 		),	
// 	'fate'=>array(),
// 	'soul'=>array(),
// ),'恭喜您获得[神兵重铸符*1]、[紫玉牌*20]、[赤霄战靴制作卷*1]',1);

//蓝钻金质礼包
// code_party_kc('2012-08-11 00:00:00','2012-12-31 23:59:59','qq38',$player['player_id'],array(
// 	'item'=>array(
// 		0 => array('item_id' => 1202, 'level' => 1, 'number' => 2),//神兵重铸符
// 		1 => array('item_id' => 347, 'level' => 1, 'number' => 20),//黄玉牌
// 		2 => array('item_id' => 1192, 'level' => 1, 'number' => 1),//葫芦坐骑
// 		3 => array('item_id' => 1088, 'level' => 1, 'number' => 1),//天罡法袍制作卷
// 		),	
// 	'fate'=>array(),
// 	'soul'=>array(),
// ),'恭喜您获得[神兵重铸符*2]、[黄玉牌*20]、[葫芦坐骑*1]、[天罡法袍制作卷*1]',1);

//蓝钻尊荣礼包
// code_party_kc('2012-08-11 00:00:00','2012-12-31 23:59:59','qq39',$player['player_id'],array(
// 	'item'=>array(),	
// 	'fate'=>array(
// 		0 => array('fate_id' => 41, 'level' => 1, 'number' => 1, 'actived_fate_id1' => 0, 'actived_fate_id2' => 0),//万里长屠
// 		),	
// 	'soul'=>array(),
// ),'恭喜您获得[万里长屠*1]',1);

// code_party_kc('2012-07-18 00:00:00','2012-12-31 23:59:59','qq40',$player['player_id'],array('skill'=>20000,'coins'=>200000,'power'=>50),'恭喜您获得[20000阅历]、[200000铜钱]、[50体力]',1);//265g专属浪漫礼包
// code_party_kc('2012-08-21 00:00:00','2012-12-31 23:59:59','qq41',$player['player_id'],array('coins'=>50000),'恭喜您获得[50000铜钱]',1);//黄钻抵扣券礼包
//=============================================================================================================
//金质礼包
/*
code_party_kc('2012-08-24 00:00:00','2012-12-31 23:59:59','qq42',$player['player_id'],array(
	'item'=>array(
		0 => array('item_id' => 1202, 'level' => 1, 'number' => 2),//神兵重铸符
		1 => array('item_id' => 347, 'level' => 1, 'number' => 20),//黄玉牌
		2 => array('item_id' => 1192, 'level' => 1, 'number' => 1),//葫芦坐骑
		3 => array('item_id' => 1088, 'level' => 1, 'number' => 1),//天罡法袍制作卷	
	),	
	'fate'=>array(
		0 => array('fate_id' => 41, 'level' => 1, 'number' => 1, 'actived_fate_id1' => 0, 'actived_fate_id2' => 0),//万里长屠
		),	
	'soul'=>array(),
),'恭喜您获得[神兵重铸符*2]、[黄玉牌*20]、[葫芦坐骑*1]、[天罡法袍制作卷*1]、[万里长屠*1]',1);
*/

// code_party_kc('2012-08-24 00:00:00','2012-12-31 23:59:59','qq43',$player['player_id'],array('skill'=>10000,'coins'=>1000000,'fame'=>1000),'恭喜您获得[10000阅历]、[1000000铜钱]、[1000声望]',1);//临时送的礼包，
// code_party_kc('2012-08-27 00:00:00','2012-12-31 23:59:59','qq44',$player['player_id'],array('skill'=>100000,'coins'=>1000000,'fame'=>1000),'恭喜您获得[100000阅历]、[1000000铜钱]、[1000声望]',1);///3366补偿礼包
// code_party_kc('2012-08-29 00:00:00','2012-12-31 23:59:59','qq45',$player['player_id'],array('skill'=>10000,'coins'=>300000,'power'=>40),'恭喜您获得[10000阅历]、[300000铜钱]、[40体力]',1);///疾风传265G礼包
// code_party_kc('2012-08-29 00:00:00','2012-12-31 23:59:59','qq46',$player['player_id'],array('skill'=>20000,'coins'=>300000,'power'=>20),'恭喜您获得[20000阅历]、[300000铜钱]、[20体力]',1);///疾风传07073礼包
// code_party_kc('2012-09-04 00:00:00','2012-12-31 23:59:59','qq47',$player['player_id'],array('skill'=>1000,'coins'=>200000,'power'=>10,'fame'=>10),'恭喜您获得[1000阅历]、[200000铜钱]、[10体力]、[10声望]',1);///微播收听礼包

// code_party_kc('2012-09-07 00:00:00','2012-12-31 23:59:59','qq48',$player['player_id'],array('coins'=>500000),'恭喜您获得[500000铜钱]',1,1,0);///抵扣券礼包


echo 7;
$db->close();


?>