<?php

class recharge extends Module_DefaultAbstract
{
	public function onPost() {
		$params = $this->getRequestHeaderParams();

		$zoneid = isset($params['uiAreaId']) ? intval($params['uiAreaId']) : 0;
		$begin_time = isset($params['uiBeginTime']) ? intval($params['uiBeginTime']) : 0;
		$end_time = isset($params['uiEndTime']) ? intval($params['uiEndTime']) : 0;
		$page_no = isset($params['ucPageNo']) ? intval($params['ucPageNo']) : 1;

		$body['ucPageNo'] = 0;
		$body['ucPageSize'] = 50;
		$body['uiTotalOpenId'] = 0;
		$body['pOpenIdList_count'] = 0;
		$body['pOpenIdList']      = array();
		$result_code = 1;

		$server = 's'.$zoneid.'.app100616996.qqopenapp.com';
		$config = $this->base->getConfig();
		$sql = "SELECT sid, name, combined_to FROM servers WHERE FIND_IN_SET('$server',server) <> 0";
		$row = $this->sql->getRow($sql);
		if (!$row) {
			echo json_encode($this->setResponse($body, '', $result_code));
			exit;
		}
		$sid = $new_sid = 0;

		$combined_to = $row['combined_to'];
		$sid = $row['sid'];
		
		$open_date = 0;
		if ($combined_to) {
			$sql = "SELECT sid, name, open_date FROM servers WHERE sid='".$combined_to."'";
			$row = $this->sql->getRow($sql);
			$new_sid = $row['sid'];
			$open_date = strtotime($row['open_date']);
		}

		$where = '';
		if ($new_sid > 0) {
			if ($open_date > $begin_time) {
				$where = "(sid='$sid' OR sid='$new_sid')";
			}else {
				$where = "sid='$new_sid'";
			}
		}else {
			$where = "sid='$sid'";
		}
		
		$where .= " AND dtime_unix>='$begin_time' AND dtime_unix<='$end_time'";
		$sql = "SELECT SUM(coins) as ingot, username FROM pay_data WHERE $where  AND status<>1 AND success<>0 GROUP BY username";
		$list = $this->sql->getAll($sql);
		if (!$list) {
			echo json_encode($this->setResponse($body, '', $result_code));
			exit;
		}

		$pOpenIdList = $arr = array();
		foreach ($list as $key => $value) {
			$username = $value['username'];
			if (strpos($value['username'], '.') !== false) {
				$arr = explode('.', $value['username']);
				$username = $arr[0];
			}
			$pOpenIdList[$username] = array('szOpenId' => $username, 'iNum' => $value['ingot']);
		}

		$body['ucPageNo'] = 1;
		$body['pOpenIdList_count'] = $body['uiTotalOpenId'] = $body['ucPageSize'] = count($pOpenIdList);
		$body['pOpenIdList']       = array_values($pOpenIdList);
		$result_code = 0;
		echo json_encode($this->setResponse($body, '', $result_code));
	}
}