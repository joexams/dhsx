<?php
$ver = isset($argv[1]) ? trim($argv[1]) : null;
if (!$ver) {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
require_once callApiVer($ver);
require_once UCTIME_ROOT.'/mod/'.$ver.'/set_api.php';
$dir = UCTIME_ROOT."/order/";
$filename = date('Y-m-d').".txt";//文件名
$adminWebType = 's';//用于打印MYSQL错误
$query = $db->query("
select 
	A.cid,
	A.sid,
	A.name,
	A.server,
	A.api_server,
	A.api_port,
	A.api_pwd,
	A.open_date,
	A.level_act,
	A.mission_act,
	A.repute_act,
	B.locale
from 
	servers A
	left join company B on A.cid = B.cid 
where 
	A.open_date <> ''
	and A.open_date < now()
	and DATE_ADD(A.open_date, INTERVAL 4 DAY) > DATE_ADD(now(), INTERVAL 1 DAY_HOUR)
	and A.server_ver = '$ver'
	and (A.level_act = 1 or A.mission_act = 1)
	and A.open = 1
order by
	A.open_date asc,
	A.sid asc
");	
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		$ccc = "【SID:".$rs['sid']."|".$rs['name']."|".$rs['open_date']."】\n";
		if($rs['api_server'] && $rs['api_port'] && $rs['api_pwd'])
		{
	
			api_base::$SERVER = $rs['api_server'];
			api_base::$PORT   = $rs['api_port'];
			api_base::$ADMIN_PWD   = $rs['api_pwd'];
			include_once(UCTIME_ROOT."/include/".$rs['locale']."_lang.php");//语言包
			//print $rs['name']."\n";
			
			//---------------------------
/*			echo '<pre>';
			print_r($rs);
			echo '</pre>';*/
	
			//&is_tester=0 不包含测试=1包含.不传默认包含
			if ($rs['level_act'] == 1 && $rs['mission_act'] == 1) 
			{
				$set_type = " and (type = 3 or type = 4)";
			}elseif ($rs['level_act'] == 1 && $rs['mission_act'] == 0) {
				$set_type = " and type = 3";
			}elseif ($rs['level_act'] == 0 && $rs['mission_act'] == 1){
				$set_type = " and type = 4";
			}
			
			
			$gdquery = $db->query("select * from gift_data where `default` = 1 $set_type");	
			if($db->num_rows($gdquery))
			{
				while($gdrs = $db->fetch_array($gdquery))
				{	
	
	/*				echo '<pre>';
					print_r($gdrs);
					echo '</pre>';*/			
	
	
	
					//-------------------------------------------------------------------------------------------------------------------------------
					if($gdrs['type'] == 3 && $rs['level_act'] == 1){
						$o = api_admin::get_player_level_ranking($gdrs['order_limit']);
						$n = 'level';
					}elseif($gdrs['type'] == 4 && $rs['mission_act'] == 1){
						$o = api_admin::get_player_mission_ranking($gdrs['order_limit']);
						$n = 'mission';
					}elseif($gdrs['type'] == 5 && $rs['repute_act'] == 1){
						$o = api_admin::get_player_fame_ranking($gdrs['order_limit']);
						$n = 'fame';
					}
					
					
					$o = unserialize($o);
						
	/*						echo '<pre>';
					print_r($o);
					echo '</pre>';*/
					
					//--------------------------------赠送元宝-------------------------------------------------------------------------------
					if($gdrs['gift_type'] != 2)
					{
						
						
						$gquery = $db->query("select gift_data_id,`order`,`number`,`type` from gift_data_gold where gift_data_id = '$gdrs[id]' order by `order` asc,id asc");	
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
									if($gdrs['type'] == 3){
										$contents = str_replace(array("{order}","{obj}"), array($grs['order'],$grs['number'].$tyne_name),$lang['G_LEVEL']);
									}elseif($gdrs['type'] == 4){
										$contents = str_replace(array("{order}","{mission}","{obj}"), array($grs['order'],$o[$grs['order']]['mission_name'],$grs['number'].$tyne_name),$lang['G_MISSION']);
									}elseif($gdrs['type'] == 5){
										$contents = str_replace(array("{order}","{obj}"), array($grs['order'],$grs['number'].$tyne_name),$lang['G_REPUTE']);
									}
									$msg = AddGiftData($o[$grs['order']]['player_id'],$gdrs['gift_type'],$ingot,$coins,$gdrs['gift_id'],$contents,array());
									$m = $msg['result'] ? languagevar('SUCCE') : languagevar('FAIL');
									insertServersAdminData($rs['cid'],$rs['sid'],$o[$grs['order']]['player_id'],addslashes($o[$grs['order']]['nickname']),languagevar('HDJL').':'.$contents.'('.languagevar('USERNICK').')('.$m.')',1);//插入操作记录		
			
								}			
								
							}
						}
						
						
						
					}
					//--------------------------------赠送物品-------------------------------------------------------------------------------		
					if($gdrs['gift_type'] != 1)
					{
						$iquery = $db->query("select gift_data_id,`order`,`number`,`item_id` from gift_data_item where gift_data_id = '$gdrs[id]' order by `order` asc,id asc");	
						if($db->num_rows($iquery))
						{	
							while($irs = $db->fetch_array($iquery))
							{
								
								if($o[$irs['order']])
								{
									//echo $o[$irs['order']]['player_id'].'|'.$irs['order'].'|'.$rs['gift_type'].'|'.$rs['gift_id'].'|'.$irs['number'].'|'.$o[$irs['order']]['mission_name'].'<br />';
									if($gdrs['type'] == 3 ){
										$contents = str_replace(array("{order}"), array($irs['order']),$lang['G_LEVEL_I']);
									}elseif($gdrs['type'] == 4){
										$contents = str_replace(array("{order}","{mission}"), array($irs['order'],$o[$irs['order']]['mission_name']),$lang['G_MISSION_I']);
									}elseif($gdrs['type'] == 5){
										$contents = str_replace(array("{order}"), array($irs['order']),$lang['G_REPUTE_I']);
									}
									$gmsg = AddGiftData($o[$irs['order']]['player_id'],$gdrs['gift_type'],0,0,$gdrs['gift_id'],$contents,array(array('item_id' => $irs['item_id'], 'number' => $irs['number'])));
									$m = $gmsg['result'] ? languagevar('SUCCE') : languagevar('FAIL');
									insertServersAdminData($rs['cid'],$rs['sid'],$o[$irs['order']]['player_id'],addslashes($o[$irs['order']]['nickname']),languagevar('HDJL').':'.$contents.'('.languagevar('USERNICK').')('.$m.')',1);//插入操作记录		
								}	
									 
								
							}
						}
						
					}
					//------------
					$ccc .= "[".$n."] = ".serialize($o)."\n\n";
					//writetofile($filename,$ccc,'a',$dir);//写入
					unset($o,$ccc);
					//-------------------------------------------------------------------------------------------------------------------------------
					
					
					
					
					
				}
			}
		}
		
	}

}
//print "--------------------------------------\n";
$db->close();

?>