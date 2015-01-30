<?php

class itemmail extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : '';
		$itemid = isset($params['uiItemId']) ? intval($params['uiItemId']) : 0;
		$itemcount = isset($params['iItemCount']) ? intval($params['iItemCount']) : 0;
		$itemlevel = isset($params['uiStrengLevel']) ? intval($params['uiStrengLevel']) : 0;
		$bindstate = isset($params['ucBindState']) ? intval($params['ucBindState']) : 0;

		$mailtitle = isset($params['szMailTitle']) ? trim($params['szMailTitle']) : '';
		$mailcontent = isset($params['szMailContent']) ? trim($params['szMailContent']) : '';

		$body['iResult']       = 1;
		$body['szRetMsg']      = 'error';

		$result_code = 1;
		if ($zoneid > 0 && parent::isOpenId($openid)) {
			$server = 's'.$zoneid.'.app100616996.qqopenapp.com';
			$config = $this->base->getConfig();
			if ($config['db_master']) {
				$sql = "SELECT a.cid,b.sid,b.name2 AS db_host,combined_to,db_root,db_pwd,db_name,a.name,server_ver,api_port,api_server,api_pwd FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$server',server)<>0 AND b.type=1";
			}else {
				$sql = "SELECT cid,sid,db_server AS db_host,combined_to,db_root,db_pwd,db_name,name,server_ver,api_port,api_server,api_pwd FROM servers WHERE FIND_IN_SET('$server', server) <> 0";
			}
	        $db_server = $this->sql->getRow($sql);

	        $servername = '';
			if (isset($db_server['combined_to']) && $db_server['combined_to'] > 0){
				$servername = isset($db_server['name']) ? $db_server['name'] : false;

				if ($config['db_master']) {
					$sql = "SELECT a.cid,b.sid,b.name2 AS db_host,combined_to,db_root,db_pwd,db_name,server_ver,api_port,api_server,api_pwd FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE sid='".$db_server['combined_to']."' AND b.type=1";
				}else {
					$sql = "SELECT cid,sid,db_server AS db_host,combined_to,db_root,db_pwd,db_name,server_ver,api_port,api_server,api_pwd FROM servers WHERE sid='".$db_server['combined_to']."'";
				}
				$db_server = $this->sql->getRow($sql);
			}

			if ($db_server){
				$cid = $db_server['cid'];
				$sid = $db_server['sid'];
				$openid = !empty($servername) ? $openid.'.'.(str_replace('qq_', '', $servername)) : $openid;
				$api_admin = parent::load_api_class('api_admin', $db_server['server_ver'], 1);
				if ($api_admin === false) {
					$body['szRetMsg'] = 'api no load';
					echo json_encode($this->setResponse($body, '', $result_code));exit;
				}
				$api_admin::$SERVER    = $db_server['api_server'];
	            $api_admin::$PORT      = $db_server['api_port'];
    	        $api_admin::$ADMIN_PWD = $db_server['api_pwd'];
				$ret = $api_admin::get_nickname_by_username($openid);
				if ($ret['result'] == 1) {
					$player_id = $ret['player_id'];
					$nickname = isset($ret['nickname'][1]) ? addslashes($ret['nickname'][1]) : '';
					
					$gift = $this->sql->getRow("SELECT * FROM gift_setting WHERE giftid='$itemid'");
					if ($gift) {
						$items = unserialize($gift['items']);
						$awardlist = $items['awardlist'];
						$itemlist = $items['itemlist'];
						$fatelist = $items['fatelist'];
						$soullist = $items['soullist'];

						$gifttype = intval($gift['gifttype']);
						$starttime = intval($gift['starttime']);
						$endtime = intval($gift['endtime']);
						$limitnumber = intval($gift['limitnumber']);
						$giftname = $gift['giftname'];
						$message = $gift['message'];
						$message = !empty($message) ? $message : '恭喜获得大礼包！';

						$now = time();

						if ($starttime < $now && $endtime > $now) {
							$otherdb = new Sql(new Sql_Driver_Mysqli(), $this->config['sql_host'], $this->config['sql_user'], $this->config['sql_pw'], $this->config['sql_db_other'], $this->config['sql_port']);

							$table_name = 'active_gift_'.$gift['giftid'];
							$today = strtotime(date('Y-m-d'));

							$sendflag = false;
							$activegift = $otherdb->getRow("SELECT * FROM $table_name WHERE cid='$cid' AND sid='$sid' AND username='$openid'");
							if (!$activegift) {
								$callback = $api_admin::add_player_active_gift($player_id, 35, 1532, $message, $awardlist, $itemlist, $fatelist, $soullist);
								if ($callback['result'] == 1) {
									$insertarr = array(
										'cid' => $cid,
										'sid' => $sid,
										'player_id' => $player_id,
										'username' => $openid,
										'nickname' => $nickname,
										'createtime' => $now,
										'lastdotime' => $now,
										'times' => 1,
									);
									if ($gifttype == 1) {
										$insertarr['daytimes'] = 1;
									}
									$otherdb->insert($table_name, $insertarr);
									$result_code = 0;
									$body['iResult'] = 0;
									$body['szRetMsg'] = 'OK';
								}else {
									$body['szRetMsg'] = 'api error';
								}
							}else {
								if ($gifttype == 1) {
									if ($activegift['daytimes'] < $limitnumber) {
										$sendflag = true;
									}else {
										$body['szRetMsg'] = 'times limit';
										$result_code = -7;
									}
								}else {
									if ($activegift['times'] < $limitnumber) {
										$sendflag = true;
									}else {
										$body['szRetMsg'] = 'times limit';
										$result_code = -7;
									}
								}
							}

							if ($sendflag) {
								$callback = $api_admin::add_player_active_gift($player_id, 35, 1532, $message, $awardlist, $itemlist, $fatelist, $soullist);
								if ($callback['result'] == 1) {
									if ($gifttype == 1) {
										if (date('Y-m-d', $activegift['lastdotime']) == date('Y-m-d')) {
											$sql = sprintf("UPDATE %s SET times=times+1, daytimes=daytimes+1, lastdotime='%d' WHERE id='%d'", $table_name, time(), $activegift['id']);
										}else {
											$sql = sprintf("UPDATE %s SET times=times+1, daytimes=1, lastdotime='%d' WHERE id='%d'", $table_name, time(), $activegift['id']);
										}
									}else {
										$sql = sprintf("UPDATE %s SET times=times+1, lastdotime='%d' WHERE id='%d'", $table_name, time(), $activegift['id']);
									}
									
									$otherdb->query($sql);
									$result_code = 0;
									$body['iResult'] = 0;
									$body['szRetMsg'] = 'OK';
								}else {
									$body['szRetMsg'] = 'api error';
								}
							}
						}else {
							$body['szRetMsg'] = 'exchange time error';
						}
					}else {
						$body['szRetMsg'] = 'gift id error';
					}
				}
			}
		}
		if ($result_code == 1) {
			error_log(date('Y-m-d H:i:s')."\t".$body['szRetMsg']."\t".http_build_query($params).PHP_EOL, 3, '/data/www/cache/send_'.date('Ym').'.log');
		}
		echo json_encode($this->setResponse($body, '', $result_code));
	}
}
