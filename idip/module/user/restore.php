<?php

class restore extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();
		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : 0;
		$backid = isset($params['uiBackupId']) ? intval($params['uiBackupId']) : 0;
		$backtime = isset($params['uiBackupTime']) ? intval($params['uiBackupTime']) : 0;

		$body['iResult'] = null;
		$body['szRetMsg'] = null;

		$result_code = 1;
		$flag = false;
		if ($zoneid > 0 && parent::isOpenId($openid)){
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
    	        	$callback = $api_admin::read_backup($player_id, $backid);
    	        	if ($callback['result'] == 1){
    	        		$applycontent = '玩家通过Q游助手回档';
    	        		$content['key']      = 'read_backup';
    	        		$content['sid']      = $sid;
    	        		$content['playerid'] = $player_id;
    	        		$content['content'] = '角色回档成功'.PHP_EOL
    	        							.'操作原因：'.$applycontent;
    	        		$content['playernickname'] = $nickname;
    	        		$content['playername'] = $openid;
    	        		$content['userid'] = '';
    	        		$content['username'] = '';
        		 		$content['ip']       = $this->getIp();
        				$content['dateline'] = time();

    	        		$insertarr = array(
    	        			'cid' => $cid,
    	        			'sid' => $sid, 
    	        			'player_id' => $player_id,
    	        			'username' => $openid,
    	        			'nickname' => $nickname,
    	        			'backupid' => $backid,
    	        			'backuptime' => $backtime,
    	        			'applycontent' => $applycontent,
    	        			'userid' => 0,
    	        			'applytime' => time(),
    	        		);
    	        		$this->sql->insert('ho_pf_find_backup', $insertarr);

    	        		$otherdb = new Sql(new Sql_Driver_Mysqli(), $this->config['sql_host'], $this->config['sql_user'], $this->config['sql_pw'], $this->config['sql_db_other'], $this->config['sql_port']);
    	        		$otherdb->insert('ho_sys_log_source', $content);
    	        		$result_code = 0;
    	        		$body['iResult'] = 0;
    	        		$body['szRetMsg'] = 'OK';
    	        	}else {
    	        		$body['szRetMsg'] = 'api error';
    	        	}
    	        }else {
    	        	$body['szRetMsg'] = 'openid error';
    	        }
    	    }
		}

		echo json_encode($this->setResponse($body, '', $result_code));
	}

	private function getIp() {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
	}

}