<?php

class info extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost(){
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : '';
		$roleid = isset($params['uiRoleId']) ? intval($params['uiRoleId']) : 0;

		$body['uiRoleId']        = null;
		$body['szRoleName']      = null;
		$body['usLevel']         = null;
		$body['usVipLevel']      = null;
		$body['uiRegisterTime']  = null;
		$body['uiLastLoginTime'] = null;

		$result_code = 1;
		$flag = false;
		if ($zoneid > 0 && parent::isOpenId($openid)){
			$flag = $this->set_db($zoneid);
		}

		if ($flag){
            $openid = $flag === true ? $openid : $openid.'.'.$flag;
			$rolesql = "SELECT a.id,nickname,level,vip_level,regdate,first_login_time,last_login_time FROM player a 
						LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
						LEFT JOIN player_trace c ON a.id=c.player_id 
						WHERE username='$openid'";
			$player = $this->gamesql->getRow($rolesql);

			if ($player){
				if (!empty($player['nickname'])) {
					$result_code = 0;

					$body['uiRoleId']        = $player['id'];
					$body['szRoleName']      = $player['nickname'];
					$body['usLevel']         = $player['level'] ? $player['level'] : 0;
					$body['usVipLevel']      = $player['vip_level'];
					$body['uiRegisterTime']  = $player['first_login_time'];
					$body['uiLastLoginTime'] = $player['last_login_time'];
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
