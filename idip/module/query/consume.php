<?php

class consume extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$begin_time = isset($params['uiBeginTime']) ? intval($params['uiBeginTime']) : 0;
		$end_time = isset($params['uiEndTime']) ? intval($params['uiEndTime']) : 0;
		$page_no = isset($params['ucPageNo']) ? intval($params['ucPageNo']) : 0;

		$body['ucPageNo'] = 0;
		$body['ucPageSize'] = 0;
		$body['uiTotalOpenId'] = 0;
		$body['pOpenIdList_count'] = 0;
		$body['pOpenIdList']       = array();

		$result_code = 1;
		$flag = false;
		if ($zoneid > 0)	$flag = $this->set_db($zoneid);
		if (!$flag)	{
			echo json_encode($this->setResponse($body, '', $result_code));
			exit;
		}

		$suf = $flag === true ? '' : $flag;
		$sql = $sql2 = '';
		if ($flag === true) {
			$sql = "SELECT player_id, sum(value+change_charge_value) as num FROM player_ingot_change_record WHERE change_time>='$begin_time' AND change_time<='$end_time' AND (value<0 OR change_charge_value<0)  AND type<>35 GROUP BY player_id limit 50,50";
		}else {
			$suf = str_replace('s', '', $flag);
			$begin_id = intval($suf) * 1000000;
			$end_id = (intval($suf)+1) * 1000000;
			$sql = "SELECT player_id, sum(value+change_charge_value) as num FROM player_ingot_change_record WHERE player_id>$begin_id AND player_id<$end_id AND change_time>='$begin_time' AND change_time<='$end_time' AND (value<0 OR change_charge_value<0) AND type<>35 GROUP BY player_id";
		}

		$pagesize 	 = intval($pagesize);
		$page = max(intval($page), 1);
		$offset = $pagesize*($page-1);

		$list  = $this->gamesql->getAll($sql);
		if (!$list) {
			echo json_encode($this->setResponse($body, '', $result_code));
			exit;
		}

		$consume_list = array();
		foreach ($list as $key => $value) {
			$consume_list[$value['player_id']] += abs($value['num']);
		}
		$player_ids = array_keys($consume_list);

		$sql = "SELECT id, username FROM player WHERE id IN (".implode(',', $player_ids).") AND is_tester=0";
		$player_list = $this->gamesql->getAll($sql);
		$pOpenIdList = array();
		foreach ($player_list as $key => $value) {
			$username = $value['username'];
			if (strpos($value['username'], '.') !== false) {
				$arr = explode('.', $value['username']);
				$username = $arr[0];
			}
			$pOpenIdList[] = array('szOpenId' => $username, 'iNum' => $consume_list[$value['id']]);
		}

		$body['pOpenIdList_count'] = count($pOpenIdList);
		$body['pOpenIdList']       = $pOpenIdList;
		$result_code = 0;
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