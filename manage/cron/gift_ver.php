<?php
$ver = isset($argv[1]) ? trim($argv[1]) : null;
if (!$ver) {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once callApiVer($ver);
require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
//$dir = UCTIME_ROOT."/order/";

$query = $db->query("
select 
	B.id,
	B.type,
	B.gift_type,
	B.gift_id,
	B.order_limit,
	C.cid,
	C.sid,
	C.name,
	C.server,
	C.api_server,
	C.api_port,
	C.api_pwd,
	D.locale
from 
	gift_data_servers A 
	left join gift_data B on A.gift_data_id = B.id
	left join servers C on A.sid = C.sid 
	left join company D on A.cid = D.cid 
where 
	A.stime <= now() 
	and A.etime >= now()
	and (B.type = 3 or B.type = 4 or B.type = 5)
	and C.server_ver = '$ver'

order by
	A.sid asc
");	
if($db->num_rows($query))
{

	
	while($rs = $db->fetch_array($query))
	{

		api_base::$SERVER = $rs['api_server'];
		api_base::$PORT   = $rs['api_port'];
		api_base::$ADMIN_PWD   = $rs['api_pwd'];
		$time = time();
		$key = '@admin2_SHEN0_XIAN1_DAO1_^^';
		$chksum = md5("".$time."_".$key."");
		include_once(UCTIME_ROOT."/include/".$rs['locale']."_lang.php");//语言包
		//print $rs['name']."\n";
		
		//---------------------------
		//print_r($rs);
/*		echo '<pre>';
		print_r($rs);
		echo '</pre>';
*/
		//&is_tester=0 不包含测试=1包含.不传默认包含
		if($rs['type'] == 3){
			$url = 'http://'.$rs['api_server'].'/'.strtolower($rs['name']).'/route.php?m=plt&tn='.$rs['order_limit'].'&t='.$time.'&chksum='.$chksum.'';	
		}elseif($rs['type'] == 4){
			$url = 'http://'.$rs['api_server'].'/'.strtolower($rs['name']).'/route.php?m=mt&tn='.$rs['order_limit'].'&t='.$time.'&chksum='.$chksum.'';
		}elseif($rs['type'] == 5){
			$url = 'http://'.$rs['api_server'].'/'.strtolower($rs['name']).'/route.php?m=pft&tn='.$rs['order_limit'].'&t='.$time.'&chksum='.$chksum.'';
		}
		$o = @file_get_contents($url);

		if($o != 1 && $o != 2 && $o != 3 && $o != 4 && $o != 5 && $o != 6)
		{
	
			$o = unserialize($o);
	/*		echo '<pre>';
			print_r($o);
			echo '</pre>';
	*/		//--------------------------------赠送元宝-------------------------------------------------------------------------------
			if($rs['gift_type'] != 2)
			{
				
				
				$gquery = $db->query("select gift_data_id,`order`,`number`,`type` from gift_data_gold where gift_data_id = '$rs[id]' order by `order` asc,id asc");	
				if($db->num_rows($gquery))
				{		
					while($grs = $db->fetch_array($gquery))
					{
						if($grs['type'] == 1)
						{
							$tyne_name = $lang['YB'];
							$ingot = $grs['number'];
							$coins = 0;
						}elseif($grs['type'] == 2){
							$tyne_name = $lang['TQ'];
							$coins = $grs['number'];
							$ingot = 0;
						}
						if($o[$grs['order']])
						{
							//echo $o[$grs['order']]['player_id'].'|'.$grs['order'].'|'.$rs['gift_type'].'|'.$rs['gift_id'].'|'.$grs['number'].'|'.$o[$grs['order']]['mission_name'].'<br />';
							if($rs['type'] == 3){
								$contents = str_replace(array("{order}","{obj}"), array($grs['order'],$grs['number'].$tyne_name),$lang['G_LEVEL']);
								$msg = AddGiftData($o[$grs['order']]['player_id'],$rs['gift_type'],$ingot,$coins,$rs['gift_id'],$contents,array());
							}elseif($rs['type'] == 4){
								$contents = str_replace(array("{order}","{mission}","{obj}"), array($grs['order'],$o[$grs['order']]['mission_name'],$grs['number'].$tyne_name),$lang['G_MISSION']);
								$msg = AddGiftData($o[$grs['order']]['player_id'],$rs['gift_type'],$ingot,$coins,$rs['gift_id'],$contents,array());
							}elseif($rs['type'] == 5){
								$contents = str_replace(array("{order}","{obj}"), array($grs['order'],$grs['number'].$tyne_name),$lang['G_REPUTE']);
								
								$msg = AddGiftData($o[$grs['order']]['player_id'],$rs['gift_type'],$ingot,$coins,$rs['gift_id'],$contents,array());
							}
							
							
							$m = $msg['result'] ? '成功' : '失败';
							insertServersAdminData($rs['cid'],$rs['sid'],$o[$grs['order']]['player_id'],$o[$grs['order']]['nickname'],'活动奖励:'.$contents.'('.$m.')');//插入操作记录		
	
						}			
						
					}
				}
				//$filename = $rs['id']."_".$rs['gift_type']."_".$rs['type']."_".$grs['order'].".txt";//文件名
				//writetofile($filename,$contents,'w',$dir);//写入
				
				
				
			}
			//--------------------------------赠送物品-------------------------------------------------------------------------------		
			if($rs['gift_type'] != 1)
			{
				$iquery = $db->query("select gift_data_id,`order`,`number`,`item_id` from gift_data_item where gift_data_id = '$rs[id]' order by `order` asc,id asc");	
				if($db->num_rows($iquery))
				{	
					while($irs = $db->fetch_array($iquery))
					{
						
						if($o[$irs['order']])
						{
							//echo $o[$irs['order']]['player_id'].'|'.$irs['order'].'|'.$rs['gift_type'].'|'.$rs['gift_id'].'|'.$irs['number'].'|'.$o[$irs['order']]['mission_name'].'<br />';
							if($rs['type'] == 3){
								$contents = str_replace(array("{order}"), array($irs['order']),$lang['G_LEVEL_I']);
								if ($rs['gift_id']) {
									$msg = AddGiftData($o[$irs['order']]['player_id'],$rs['gift_type'],0,0,$rs['gift_id'],$contents,array(array('item_id' => $irs['item_id'], 'number' => $irs['number'])));
	
								}	
							}elseif($rs['type'] == 4){
								$contents = str_replace(array("{order}","{mission}"), array($irs['order'],$o[$irs['order']]['mission_name']),$lang['G_MISSION_I']);
								if ($rs['gift_id']) {
									$msg = AddGiftData($o[$irs['order']]['player_id'],$rs['gift_type'],0,0,$rs['gift_id'],$contents,array(array('item_id' => $irs['item_id'], 'number' => $irs['number'])));
	
								}
							}elseif($rs['type'] == 5){
								$contents = str_replace(array("{order}"), array($irs['order']),$lang['G_REPUTE_I']);
								if ($rs['gift_id']) {
									$msg = AddGiftData($o[$irs['order']]['player_id'],$rs['gift_type'],0,0,$rs['gift_id'],$contents,array(array('item_id' => $irs['item_id'], 'number' => $irs['number'])));
	
								}
							}
							$m = $msg['result'] ? '成功' : '失败';
							insertServersAdminData($rs['cid'],$rs['sid'],$o[$irs['order']]['player_id'],$o[$irs['order']]['nickname'],'活动奖励:'.$contents.'('.$m.')');//插入操作记录		
						}				 
						
					}
				}
				
				//$filename = $rs['id']."_".$rs['gift_type']."_".$rs['type']."_".$irs['order'].".txt";//文件名
				//writetofile($filename,$contents,'w',$dir);//写入

				
				
			}
			
		}
		//---------------------------
	}
}
//print "--------------------------------------\n";
$db->close();

?>