<?php

class friend extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : '';

		$body['FriendList_count'] = 0;
		$body['FriendList']       = array();

		$result_code = 1;
		$flag = false;
		if ($zoneid > 0 && parent::isOpenId($openid)){
			$flag = $this->set_db($zoneid);
		}

		if ($flag){
            $openid = $flag === true ? $openid : $openid.'.'.$flag;
            $sql = "SELECT `id`, `username`, `nickname` FROM player WHERE username='{$openid}'";
            $player = $this->gamesql->getRow($sql);

            if ($player){
            	if (!empty($player['nickname'])) {
	            	$playerId = $player['id'];
	            	$sql = "SELECT `friend_id`, `username`, `nickname`, `is_online` FROM player_friends a LEFT JOIN player b ON a.friend_id=b.id WHERE player_id='$playerId'";
	            	$list = $this->gamesql->getAll($sql);
	            	$friendList = array();
	            	if ($list) {
	            		foreach ($list as $key => $value) {
	            			if ($flag !== true && strpos($value['username'], '.s') !== false) {
	            				$username = explode('.', $value['username']);
	            				$friendList[$key]['szOpenId'] = $username[0];
	            				$suffix = $username[1];
	            				if ($suffix == $flag) {
	            					$friendList[$key]['uiAreaId'] = $zoneid;
	            				}else {
	            					$servername = 'qq_' . $username[1];
	            					$sql = "SELECT sid, server FROM servers WHERE `name`='$servername'";
	            					$server = $this->sql->getRow($sql);
	            					if (!$server)	continue;
	            					$domainArray = explode('.', $server['server']);
	            					$areaId = intval(str_replace('s', '', $domainArray[0]));
	            					if ($areaId <= 0)	continue;
	            					$friendList[$key]['uiAreaId'] = $areaId;
	            				}
	            			}else {
	            				$friendList[$key]['uiAreaId'] = $zoneid;
	            				$friendList[$key]['szOpenId'] = $value['username'];
	            			}
	            			$friendList[$key]['szNickName'] = $value['nickname'];
	            			$friendList[$key]['ucIsOnline'] = $value['is_online'];
	            		}
	            	}
	            	
            		$result_code = 0;
	            	$body['FriendList_count'] = count($friendList);
	            	$body['FriendList'] = $friendList;
	            }
            }
        }

        echo json_encode($this->setResponse($body, '', $result_code));
	}

	/**
	 * 设置数据连接
	 */
	private function set_db($zoneid) {
		$server = 's'.$zoneid.'.app100616996.qqopenapp.com';

		$config = $this->base->getConfig();
		if ($config['db_master']) {
			$sql = "SELECT b.name2 AS db_host,a.name,combined_to,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$server',server)<>0 AND b.type=1";
		}else {
			$sql = "SELECT db_server AS db_host,name,combined_to,db_root,db_pwd,db_name FROM servers WHERE FIND_IN_SET('$server',server) <> 0";
		}
        $db_server = $this->sql->getRow($sql);

        $servername = '';
		if (isset($db_server['combined_to']) && $db_server['combined_to'] > 0){
            $servername = isset($db_server['name']) ? $db_server['name'] : false;
			
			if ($config['db_master']) {
				$sql = "SELECT b.name2 AS db_host,combined_to,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE sid='".$db_server['combined_to']."' AND b.type=1";
			}else {
				$sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name FROM servers WHERE sid='".$db_server['combined_to']."'";
			}
			$db_server = $this->sql->getRow($sql);
		}

		if ($db_server){
			if (strpos($db_server['db_host'], ':') !== false){
                $db_host = explode(':', $db_server['db_host']);
                $db_server['db_host'] = $db_host[0];
                $db_server['db_port'] = $db_host[1];
            }
			$this->gamesql = new Sql(new Sql_Driver_Mysqli(), $db_server['db_host'], $db_server['db_root'], $db_server['db_pwd'], $db_server['db_name'], $db_server['db_port']);

            //return true;
            return empty($servername) ? true : str_replace('qq_', '', $servername);
		}

		return false;
	}

}