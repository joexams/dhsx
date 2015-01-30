<?php
class num extends Module_DefaultAbstract
{
	protected $gamesql;
	public function onPost(){
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : '';
		$starttime = isset($params['uiBeginTime']) ? trim($params['uiBeginTime']) : 0;
		$endtime = isset($params['uiEndTime']) ? trim($params['uiEndTime']) : 0;

		$body['uiBeginTime']  = $starttime;
		$body['uiEndTime']    = $endtime;
		$body['uiCashNum']    = 0;
		$body['uiConsumeNum'] = 0;

		$result_code = 1;
		if ($zoneid > 0 && parent::isOpenId($openid)){
			$cashnum = $consume =  0;
			$server = 's'.$zoneid.'.app100616996.qqopenapp.com';

			$config = $this->base->getConfig();
			if ($config['db_master']) {
				$sql = "SELECT sid,b.name2 AS db_host,a.name,combined_to,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE FIND_IN_SET('$server',server)<>0 AND b.type=1";
			}else {
				$sql = "SELECT sid,db_server AS db_host,name,combined_to,db_root,db_pwd,db_name FROM servers WHERE FIND_IN_SET('$server',server) <> 0";
			}
			$db_server = $this->sql->getRow($sql);
            $servername = $db_server['name'];
			$nopenid = $openid;
			//合服
		    $wherestr = "username='$openid' AND sid='".$db_server['sid']."' AND cid=1";
            if (isset($db_server['combined_to']) && $db_server['combined_to'] > 0){
                $sids = $db_server['sid'];

                if ($config['db_master']) {
                	$sql = "SELECT sid,b.name2 AS db_host,a.name,db_root,db_pwd,db_name FROM servers a LEFT JOIN servers_address b ON db_server=b.name WHERE sid='".$db_server['combined_to']."' AND b.type=1";
                }else {
                	$sql = "SELECT sid,db_server AS db_host,name,db_root,db_pwd,db_name FROM servers WHERE sid='".$db_server['combined_to']."'";
                }
				$db_server = $this->sql->getRow($sql);

				$s = explode('_', $servername);
				$nopenid = $openid.'.'.$s[1];
                $sids .= ','.$db_server['sid'];
			    $wherestr = "(username='$openid' OR username='$nopenid') AND sid IN ($sids) AND cid=1";
            }
			$db_host = explode(':', $db_server['db_host']);
			$db_server['db_server'] = $db_host[0];
            $db_server['db_port']   = $db_host[1];
			//充值元宝
			
			$wherestr .= $starttime > 0 ? " AND dtime_unix>'$starttime'" : '';
			$wherestr .= $endtime > 0 ? " AND dtime_unix<='$endtime'" : '';

			$sql = "SELECT sum(coins) AS total FROM pay_data WHERE $wherestr";
			$cashnum = $this->sql->getField($sql);
            $cashnum = $cashnum ? $cashnum : 0;
			//消费元宝
			$this->gamesql = new Sql(new Sql_Driver_Mysqli(), $db_server['db_server'], $db_server['db_root'], $db_server['db_pwd'], $db_server['db_name'], $db_server['db_port']);
			$sql = "SELECT id FROM player WHERE username='$nopenid'";
			$player_id = $this->gamesql->getField($sql);

			$wherestr = "player_id='$player_id'";
			$wherestr .= $starttime > 0 ? " AND change_time>'$starttime'" : '';
			$wherestr .= $endtime > 0 ? " AND change_time<='$endtime'" : '';
			
			$sql = "SELECT sum(value+change_charge_value) AS total FROM player_ingot_change_record WHERE $wherestr AND (value<0 or change_charge_value<0)";
			$consume = $this->gamesql->getField($sql);

			$result_code = 0;
			$body['uiCashNum']    = $cashnum ? $cashnum : 0;
			$body['uiConsumeNum'] = $consume ? $consume : 0;
		}

		echo json_encode($this->setResponse($body, '', $result_code));
	}
}
