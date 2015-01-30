<?php

class baseinfo extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$roleid = isset($params['uiRoleId']) ? intval($params['uiRoleId']) : 0;

		$body['szRoleName']    = null;
		$body['usLevel']       = null;
		$body['ucJob']         = null;
		$body['szFactionName'] = null;
		$body['uiCharTitle']   = null;
		$body['uiReputation']  = null;
		$body['uiRanking']     = null;
		$body['uiCopyStep']    = null;
		$body['uiServer']      = null;

		$result_code = 1;
		$flag = false;
		if ($zoneid > 0){
			$flag = $this->set_db($zoneid);
		}

		if ($flag){
			$rolesql = "SELECT a.id,nickname,b.level,b.role_id,f.name AS factionname,d.fame,e.ranking,d.max_mission_lock FROM player a 
						LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
						LEFT JOIN player_data d ON a.id=d.player_id 
						LEFT JOIN player_super_sport_ranking e ON a.id=e.player_id 
						LEFT JOIN player_faction f ON a.id=f.player_id 
						WHERE a.id='$roleid'";
			$player = $this->gamesql->getRow($rolesql);

			if ($player){
				$result_code = 0;

				$famesql = "SELECT id,name FROM fame_level_data WHERE require_fame<='".$player['fame']."' ORDER BY require_fame DESC";
				$player2 = $this->gamesql->getRow($famesql);

				$missionsql = "SELECT id,name FROM mission WHERE `lock`='".$player['max_mission_lock']."'";
				$player3 = $this->gamesql->getRow($missionsql);
				

				$body['szRoleName']    = $player['nickname'];
				$body['usLevel']       = $player['level'];
				$body['ucJob']         = $player['role_id'];
				$body['szFactionName'] = !empty($player['factionname']) ? $player['factionname'] : 'null';
                $body['uiCharTitle']   = $player2['id'];
				$body['uiReputation']  = $player['fame'];
				$body['uiRanking']     = $player['ranking'];
				$body['szCopyStep']    = isset($player3['name']) ? $player3['name'] : '';
				$body['uiServer']      = str_replace('qq_s', '', $flag);
				unset($player, $player2, $player3);
			}
		}

		echo json_encode($this->setResponse($body, '', $result_code));
	}

	/**
	 * 设置数据连接
	 */
	private function set_db($zoneid){
		$server = 's'.$zoneid.'.app100616996.qqopenapp.com';
		
		$config = $this->base->getConfig();
		if ($config['db_master']) {
			$sql = "SELECT b.name2 AS db_host,combined_to,db_root,db_pwd,db_name,a.name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$server',server)<>0 AND b.type=1";
		}else {
			$sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name,name FROM servers WHERE FIND_IN_SET('$server', server) <> 0";
		}
        $db_server = $this->sql->getRow($sql);

		$servername = isset($db_server['name']) ? $db_server['name'] : false;
		if (isset($db_server['combined_to']) && $db_server['combined_to'] > 0){
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

			return $servername;
		}

		return false;
	}
}
