<?php
class state extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$openid  = isset($params['szOpenId']) ? trim($params['szOpenId']) : 0;
		$zoneid  = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$eventid = isset($params['uiEventId']) ? intval($params['uiEventId']) : 0;
		$roleid = isset($params['uiRoleId']) ? intval($params['uiRoleId']) : 0;

		if (in_array($eventid, array(1))) {
			$result_code = 1;
			$flag        = false;
			$state       = 1;

			if ($zoneid > 0 && parent::isOpenId($openid)){
				$flag = $this->set_db($zoneid);
			}
	
			if ($flag){
				$openid = $flag === true ? $openid : $openid.'.'.$flag;
				$result_code = 0;
				switch ($eventid) {
					case 1:
						$state = $this->getCollectItemState($openid);
						break;
					default:
						$state = 3;
				}
			}
		}else {
			$result_code = 0;
			$state = 3;
		}

		$body['ucState'] = $state;
		echo json_encode($this->setResponse($body, '', $result_code));
	}
	/**
	 * 查询玩家是否收集齐“2012"、“TGC”、“Joy”、“Up”四个物品
	 * 1455		1456	1457	1458
	 * @return [type] [description]
	 */
	private function getCollectItemState($openid) {
		$state = 1;
		$sql = "select id,username from player where username='$openid'";
		$player = $this->gamesql->getRow($sql);
		$playerid = $player['id'];
		
		if ($playerid > 0) {
			$wherestr = "player_id='$playerid' AND item_id IN(1455, 1456, 1457, 1458)";
			$itemsql = "select count(*) as num from player_item where $wherestr";
			$item = $this->gamesql->getRow($itemsql);
			$itemcount = $item ? $item['num'] : 0;
			if ($itemcount > 3) {
				$state = 0;
			}
		}

		return $state;
	}

	/**
	 * 设置数据连接
	 */
	private function set_db($zoneid){
		$server = 's'.$zoneid.'.app100616996.qqopenapp.com';
		
		$config = $this->base->getConfig();
		if ($config['db_master']) {
			$sql = "SELECT b.name2 AS db_host,a.name,combined_to,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$server',server)<>0 AND b.type=1";
		}else {
			$sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name,name FROM servers WHERE FIND_IN_SET('$server',server) <> 0";
		}
		$db_server = $this->sql->getRow($sql);

		$servername = '';
		if (isset($db_server['combined_to']) && $db_server['combined_to'] > 0) {
			$servername = isset($db_server['name']) ? $db_server['name'] : false;

			$sql = "SELECT db_server AS db_host,combined_to,db_root,db_pwd,db_name FROM servers WHERE sid='".$db_server['combined_to']."'";
			$db_server = $this->sql->getRow($sql);
		}

		if ($db_server){

			if (strpos($db_server['db_host'], ':') !== false){
                $db_host = explode(':', $db_server['db_host']);
                $db_server['db_host'] = $db_host[0];
                $db_server['db_port'] = $db_host[1];
            }
			$this->gamesql = new Sql(new Sql_Driver_Mysqli(), $db_server['db_host'], $db_server['db_root'], $db_server['db_pwd'], $db_server['db_name'], $db_server['db_port']);

			return empty($servername) ? true : str_replace('qq_', '', $servername);
		}

		return false;
	}
}
