<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class pay extends admin {
	private $paydb, $getdb;
	function __construct(){
		parent::__construct();
		$this->paydb = common::load_model('pay_model');
	}

	public function log(){
		$data['isall'] = 1;
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'pay');
	}
	/**
	 * 充值排行
	 * @return [type] [description]
	 */
	public function ranking(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'pay_ranking');
	}
	/**
	 * 充值汇总
	 * @return [type] [description]
	 */
	public function total(){
		// $this->paydb->table_name = 'servers';
		// $list = $this->paydb->select();
		// foreach ($list as $key => $value) {
		// 	$begin = strtotime('2011-01-01');
		// 	$end = strtotime('2012-07-21');

		// 	$this->paydb->table_name = 'game_data';

		// 	if ($value['sid'] < 4878){
		// 		continue;
		// 	}
		// 	for($i=$begin; $i<=$end;$i+=(24*3600))
		// 	{
		// 		$info['gdate'] = date("Y-m-d",$i);
		// 		$info['pay_amount'] = rand(10, 10000);
		// 		$info['pay_player_count'] = rand(100,5000);
		// 		$info['pay_num'] = rand(10,3000);
		// 		$info['sid'] = $value['sid'];
		// 		$info['cid'] = $value['cid'];
		// 		$this->paydb->insert($info);
		// 	}
		// }

		include template('report', 'pay_total');
	}
	/**
	 * 充值汇总列表
	 * @return [type] [description]
	 */
	public function ajax_total_list(){
		$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
		$cid = isset($_GET['cid']) ? $_GET['cid'] : array();
		$timeall = isset($_GET['timeall']) ? intval($_GET['timeall']) : 0;
		$type = isset($_GET['type']) && is_array($_GET['type']) ? $_GET['type'] : array();
		// $cidall = isset($_GET['cidall']) ? intval($_GET['cidall']) : 0;
		// $sidall = isset($_GET['sidall']) ? intval($_GET['sidall']) : 0;
		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? trim($_GET['starttime']) : '';
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? trim($_GET['endtime']) : date('Y-m-d');
		
		$sflag = 0;
		if (count($cid) <= 0){
			output_json(1, Lang('company_no_selected'));
		}
		$strcids = implode($cid, ',');
		if (count($sid) > 0){
			$sflag = 1;
			$strsids = implode($sid, ',');
		}

		$data['datelist'] = array();

		$begin = strtotime($starttime);
		$end = strtotime($endtime);
		
		if ($timeall ==0 && ($end-$begin)/(24*3600) > 60){
			output_json(1, Lang('日期范围不能超过60天'));
		}

		$list = $alllist = array();
		if ($begin < $end){
			for($i=$begin; $i<=$end;$i+=(24*3600))
			{
			    $data['datelist'][] = date("Y-m-d",$i);
			}
		}

		$data['typecount'] = 1; 
		$column = 'cid,sid,SUM(pay_amount) AS pay_amount';
		$column_1 = 'cid,sid,gdate,pay_amount';
		if (count($type) > 0){
			foreach ($type as $value) {
				switch ($value) {
					case 1:
						$column .= ",SUM(pay_num) AS pay_num";
						$column_1 .= ',pay_num';
						break;
					case 2:
						$column .= ",SUM(pay_player_count) AS pay_player_count";
						$column_1 .= ',pay_player_count';
						break;
					case 3:
						$column .= ",(SUM(pay_amount)/SUM(pay_player_count)) AS arpu";
						$column_1 .= ',(pay_amount/pay_player_count) AS arpu';
						break;
				}
			}
			$data['typecount'] += count($type);
		}

		$wherestr = '';
		if ($timeall == 1){
			if ($sflag == 1){
				$wherestr = $sid != 1 ? " WHERE sid IN ($strsids) " : '';
				$sql = "SELECT $column FROM game_data $wherestr GROUP BY sid";
				$data['type'] = 2;
			}else {
				$wherestr = $cid != 1 ? " WHERE cid IN ($strcids) " : '';
				$sql = "SELECT $column FROM game_data $wherestr GROUP BY cid";
				$data['type'] = 1;
			}
		}else {
			$wherestr = $sflag == 1 ? ($sid != 1 ? " WHERE sid IN ($strsids) " : '') : ($cid != 1 ? " WHERE cid IN ($strcids) " : '');
			if (!empty($starttime)){
				$wherestr .= !empty($wherestr) ? " AND gdate>='$starttime'" : "gdate>='$starttime'";
				//$wherestr2.= !empty($wherestr) ? " AND dtime_unix>='$begin'" : "dtime_unix>='$begin'";
			}
			if (!empty($endtime)){
				$wherestr .= !empty($wherestr) ? " AND gdate<='$endtime'" : " gdate<='$endtime'";
				//$wherestr2.= !empty($wherestr) ? " AND dtime_unix<='$end'" : " dtime_unix<='$end'";
			}
			$wherestr2 .= !empty($wherestr2) ? " AND status <> 1 AND success <> 0" : '';

			if ($sflag == 1){
//				$wherestr = $sid != 1 ? " WHERE sid IN ($strsids) ".$wherestr : $wherestr;
				$sql = "SELECT $column_1 FROM game_data $wherestr";
				$data['type'] = 2;

				$sql2 = "SELECT COUNT(DISTINCT username),sid FROM pay_data $wherestr2 GROUP BY sid";

				$sql3 = "SELECT COUNT(DISTINCT username),from_unixtime(dtime_unix, '%Y-%m-%d') as gdate FROM pay_data $wherestr2 GROUP BY gdate";
			}else {
				
//				$wherestr = $cid != 1 ? " WHERE cid IN ($strcids) ".$wherestr : $wherestr;
				$sql = "SELECT $column,gdate FROM game_data $wherestr GROUP BY cid,gdate";
				$data['type'] = 1;

				$sql2 = "SELECT COUNT(DISTINCT username),cid FROM pay_data $wherestr2 GROUP BY cid";

				$sql3 = "SELECT COUNT(DISTINCT username),gdate FROM pay_data $wherestr2 GROUP BY gdate";
			}
		}
		$list = $this->paydb->get_list($sql);

		if ($list) {
			foreach ($list as $key => $value) {
				$id = $data['type'] == 1 ? $value['cid'] : $value['sid'];
				$alllist[$id][$value['gdate']] = $value;

				$alllist[0][$value['gdate']]['pay_num'] += $value['pay_num'];
				$alllist[0][$value['gdate']]['pay_amount'] += $value['pay_amount'];
				$alllist[0][$value['gdate']]['pay_player_count'] += $value['pay_player_count'];
				$alllist[0][$value['gdate']]['arpu'] = round($alllist[0][$valaaaaue['gdate']]['pay_amount']/$alllist[0][$value['gdate']]['pay_player_count'],2);
			}
		}
		$serverdb  = common::load_model('public_model');
		if ($data['type'] == 1) {
			$serverdb->table_name = 'company';
			$serverlist = $serverdb->select("cid IN ($strcids)", 'cid as id, `name`');
			$merge = array(array('id' => 0, 'name' => '总计'));
		}else {
			$serverdb->table_name = 'servers';
			$serverlist = $serverdb->select("sid IN ($strsids)", 'sid as id, `name`');
			$merge = array(array('id' => 0, 'name' => '总计'));
		}
		$data['serverlist'] = array_merge($merge, $serverlist);
		unset($serverlist);

		$data['alllist'] = $alllist;//print_r($data);die();
		include template('ajax', 'pay_total_list');
	}
	/**
	 * 充值排行列表
	 * @return [type] [description]
	 */
	public function ajax_ranking_list(){
		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$pagesize  = 20;
		$page      = max(intval($page), 1);
		$offset    = $pagesize*($page-1);

		$wherestr = '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$isall = isset($_GET['isall']) ? intval($_GET['isall']) : 0;
		$list = array();
		$count = $data['total_amount'] = $data['total_pay_times'] = $data['arpu'] = 0;

		if ($isall == 1){
			$offset   = 0;
			$pagesize = 50;
		}

		$wherestr = parent::check_pf_priv();
		$this->paydb->table_name = 'pay_player';
		if ($cid > 0){
			$wherestr .= !empty($wherestr) ? ' AND cid='.$cid.'' : ' WHERE cid='.$cid.'';
		}
		if ($sid > 0){
			$wherestr .= !empty($wherestr) ? ' AND sid='.$sid.'' : ' WHERE sid='.$sid.'';
		}
 		$sql = "SELECT (@i:=@i+1)+$pagesize*($page-1) AS ranking,cid,username,nickname,amount,last_pay_amount,last_pay_time,pay_num FROM pay_player,(SELECT @i:=0) AS rank $wherestr ORDER BY amount DESC LIMIT $offset, $pagesize";
		$list = $this->paydb->get_list($sql);
		//if ($recordnum <= 0){
			$wherestr = str_ireplace('where', '', $wherestr);
			$count = $this->paydb->count($wherestr, '*');

			$ts = $this->paydb->get_one($wherestr, 'SUM(amount) AS total_amount, sum(pay_num) AS total_pay_times');
			$data['total_amount'] = round($ts['total_amount'], 2);
			$data['total_pay_times'] = $ts['total_pay_times'];
			$data['arpu'] = $count > 0 ? round($data['total_amount'] / $count, 2) : 0;
			$data['total_pay_person'] = $count;
		//}

		$data['list']  = $list;
		$data['count'] = $count;
		if ($isall == 1){
			$data['count'] = 50;
		}
		output_json(0, '', $data);
	}
	/**
	 * 按条件获取充值记录
	 * @return [type] [description]
	 */
	public function ajax_log_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$playername = isset($_GET['playername']) ? trim($_GET['playername']) : '';
		$wherestr = '';
		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$pagesize  = 20;
		$page      = max(intval($page), 1);
		$offset    = $pagesize*($page-1);
		if (isset($_GET['dogetSubmit'])){
			
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$playername = isset($_GET['playername']) ? trim($_GET['playername']) : '';
			$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : strtotime('-30 day');
			$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime']." 23:59:59") : time();
			$oid = isset($_GET['oid']) ? trim($_GET['oid']) : '';
			$success = isset($_GET['success']) ? intval($_GET['success']) : 0;
			if ($cid > 0){
				$wherestr = " cid='$cid'";
			}
			if ($sid > 0){
				$wherestr .= !empty($wherestr) ? " AND sid='$sid'" : " sid='$sid'";
			}
			if (!empty($playername)){
				$wherestr .= !empty($wherestr) ? " AND (username='$playername' or nickname='$playername')" : " (username='$playername' or nickname='$playername')";
			}
			if (!empty($oid)){
				$wherestr .= !empty($wherestr) ? " AND oid='$oid'" : " oid='$oid'";
			}
			if ($starttime > 0){
				$wherestr .= !empty($wherestr) ? " AND dtime_unix>=$starttime" : " dtime_unix>=$starttime";
			}
			if ($endtime > 0){
				$wherestr .= !empty($wherestr) ? " AND dtime_unix<=$endtime" : " dtime_unix<=$endtime";
			}
			if ($success == 1) {
				$wherestr .= !empty($wherestr) ? " AND success<>0" : " success<>0";
			}else if ($success == 2) {
				$wherestr .= !empty($wherestr) ? " AND success=0" : " success=0";
			}else if ($success == 3) {
				$wherestr .= !empty($wherestr) ? " AND status=1" : " status=1";
			}

			$wherestr = !empty($wherestr) ? 'WHERE '.$wherestr : $wherestr;
			$listsql = "SELECT pid, sid, dtime, username, nickname, amount, coins,oid,success FROM pay_data 
						$wherestr 
						ORDER BY pid DESC 
						LIMIT $offset,$pagesize;";
			$list = $this->paydb->get_list($listsql);
			if ($recordnum <= 0){
				$this->paydb->table_name = 'pay_data';
				$wherestr = str_ireplace('where', '', $wherestr);
				$recordnum = $this->paydb->count($wherestr, 'pid');
			}
			$data['count'] = $recordnum;
			$data['list']  = $list;
			foreach ($data['list'] as $key => $value){
				if ($_SESSION['roleid'] > 2){
					unset($value['pid']);
				}
				$newlist[] = $value; 
			}
			$data['list'] = $newlist;
			unset($list);
			output_json(0, '', $data);
		}else{
			$this->paydb->table_name  = 'pay_data';
			if ($sid > 0){
				$wherestr .= !empty($wherestr) ? " AND sid='$sid'" : " sid='$sid'";
				$wherearr['sid'] = $sid;
			}
			
			if (!empty($playername)){
				$wherestr .= !empty($wherestr) ? " AND username='$playername'" : " username='$playername'";
				$wherearr['username'] = $playername;
			}
			if ($id > 0){
				$wherestr .= !empty($wherestr) ? " AND player_id='$id'" : " player_id='$id'";
				$wherearr['player_id'] = $id;
			}
			$wherestr .= !empty($wherestr) ? " AND success=0" : " success=0";
			$wherearr['success'] = 0;
			$data['count'] = $this->paydb->count($wherearr, 'pid');
			$wherestr = !empty($wherestr) ? 'WHERE '.$wherestr : $wherestr;
			$listsql = "SELECT pid, sid, dtime, username, nickname, amount, coins,oid,success FROM pay_data 
						$wherestr 
						ORDER BY pid DESC 
						LIMIT $offset,$pagesize;";
			$data['list'] = $this->paydb->get_list($listsql);
			foreach ($data['list'] as $key => $value){
				if ($_SESSION['roleid'] > 2){
					unset($value['pid']);
				}
				$newlist[] = $value; 
			}
			$data['list'] = $newlist;
			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 收益趋势
	 * @return [type] [description]
	 */
	public function trend(){

		include template('report', 'pay_trend');
	}
	/**
	 * 收益趋势   列表
	 * @return [type] [description]
	 */
	public function ajax_trend_list(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$opendate = isset($_GET['opendate']) ? trim($_GET['opendate']) : '';
		$type = isset($_GET['type']) ? intval($_GET['type']) : 0;

		$daytype = isset($_GET['daytype']) ? intval($_GET['daytype']) : 0;
		$chartData = $pay = $curlist = array();
		$max = 0;
		$startDate = $endDate = '';

		switch ($daytype) {
			case 0:
				$startDate = strtotime(date('Y-m-d 00:00:00'));
				$endDate = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
				break;
			case 1:
				$startDate = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
				$endDate = strtotime(date('Y-m-d 00:00:00'));
				break;
			case 2:
				$startDate = date('Y-m-d', strtotime('-7 day'));
				for($i = 1 ; $i <= 7 ; $i++ ) {
					$chartData['categories'][] = date('Y-m-d', strtotime("+{$i} day", strtotime($startDate)));
				}
				break;
			case 3:
				$startDate = date('Y-m-d', strtotime('-30 day'));
				for($i = 1 ; $i <= 30 ; $i++ ) {
					$chartData['categories'][] = date('Y-m-d', strtotime("+{$i} day", strtotime($startDate)));
				}
				break;
			case 4:
				$startDate = '';
				$endDate = '';
				break;
		}

		$wherestr = '';
		if ($cid > 0){
			$wherestr = "cid='$cid'";
		}
		if ($daytype < 2) {
			$cursql = "SELECT sid,date_format(open_date,'%Y-%m-%d') AS open_date FROM servers 
					WHERE ".(!empty($wherestr) ? $wherestr.' AND ' : '' )." open=1 AND UNIX_TIMESTAMP(open_date)>='".$startDate."' AND UNIX_TIMESTAMP(open_date)<'".$endDate."' AND combined_to=0 GROUP BY open_date";
			$curservers = $this->paydb->get_list($cursql);

			if (!empty($startDate)) {
				$wherestr .= empty($wherestr) ? "dtime_unix>='$startDate'" : " AND dtime_unix>='$startDate'";
			}
			if (!empty($endDate)) {
				$wherestr .= empty($wherestr) ? "dtime_unix<'$endDate'" : " AND dtime_unix<'$endDate'";
			}
			$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : '';
			$sql = "SELECT from_unixtime(dtime_unix, '%H') as gdate, SUM(amount) as amount FROM pay_data $wherestr GROUP BY gdate";

			for($i = 0 ; $i < 24 ; $i++) {
				$chartData['categories'][] = str_pad($i, 2, '0', STR_PAD_LEFT);
			}

			if (!empty($curservers)) {
				foreach ($curservers as $key => $value) {
					$sids[] = $value['sid'];
				}

				$wherestr = !empty($wherestr) ? $wherestr." AND sid IN (".implode(',', $sids).")" : "where sid IN (".implode(',', $sids).")";
				$sql2 = "SELECT from_unixtime(dtime_unix, '%H') as gdate, SUM(amount) as amount FROM pay_data $wherestr GROUP BY gdate";
				$curlist = $this->paydb->get_list($sql2);
			}
			
		}else {
			if (!empty($startDate)) {
				$wherestr .= empty($wherestr) ? "gdate>='$startDate'" : " AND gdate>='$startDate'";
			}
			$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : '';
			$sql = "SELECT gdate,SUM(pay_amount) AS amount FROM game_data $wherestr GROUP BY gdate";
		}
		$list = $this->paydb->get_list($sql);

		$pay = $serverpay = array();
		foreach ($list as $key => $value) {
			$pay[$key] = array(
				'name' => $value['gdate'],
				'y' => round($value['amount'], 2),
			);
			if ($daytype == 4) {
				$chartData['categories'][] = $value['gdate'];
			}
				
			if ($daytype > 1 && (date('w', strtotime($value['gdate'])) == 0 || date('w', strtotime($value['gdate'])) == 6)){
				$pay[$key]['marker'] = array(
					'fillColor' => 'red',
					'states' => array('hover' => array('fillColor' => 'red'))
				);
			}
			$max = max($max, intval($value['amount']));
		}

		if (!empty($curlist)) {
			foreach ($curlist as $key => $value) {
				$serverpay[$key] = array(
					'name' => $value['gdate'],
					'y' => round($value['amount'], 2),
				);

				if ($daytype > 1 && (date('w', strtotime($value['gdate'])) == 0 || date('w', strtotime($value['gdate'])) == 6)){
					$serverpay[$key]['marker'] = array(
						'fillColor' => 'red',
						'states' => array('hover' => array('fillColor' => 'red'))
					);
				}
			}
		}

		$chartData['chartOptions'] = array(
			'title' => array('text' => ''),
			'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on'),
			'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>1, 'tickInterval'=>round($max/4), 'min'=>0, 'max' => $max+10, 'name'=>''),
			'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
		);

		if ($daytype == 2) {
			$chartData['chartOptions']['plotOptions'] = array('series' => array('marker' => array('enabled' => true)));
		}

		$chartData['series'][] = array('name' => '充值总收入', 'data' => $pay);
		$chartData['series'][] = array('name' => '当日新服收入', 'data' => $serverpay);
		$data['list'] = $chartData;

		// if ($type == 1){
		// 	$data['all']['list'] = $tmplist;
		// 	unset($list, $tmplist);
		// }else {
		// 	$this->paydb->table_name = 'servers';
		// 	foreach ($list as $key => $value) {
		// 		$num = $this->paydb->count("open_date<='".$value['gdate']." 23:59:59'", 'sid');

		// 		$dateline = strtotime(date('Y-m-d 08:00:00', strtotime($value['gdate'])))*1000;
		// 		$num = intval($num);
		// 		$totalserverlist[$key] = array($dateline, $num);

		// 		if ($num > 0){
		// 			$avgamount = round($value['amount']/$num, 2);
		// 		}else {
		// 			$avgamount = 0;
		// 		}
				
		// 		$avglist[$key] = array($dateline, $avgamount);
		// 	}
		// 	$data['all']['avglist'] = $avglist;
		// 	$data['all']['totalservlist'] = $totalserverlist;
		// 	$data['all']['list'] = $tmplist;
		// 	unset($list, $tmplist, $avglist, $totalserverlist);
		// }		

		//当日新服
		// $list = $avglist = $totalserverlist = array();		
		// if ($type == 1){
		// 	$wherestr = '';
		// 	if ($cid > 0){
		// 		$wherestr = "cid='$cid' AND ";
		// 	}
		// 	if (!empty($opendate)){
		// 		$wherestr .= "open_date>='$opendate' AND ";
		// 	}
			// $sql = "SELECT date_format(open_date,'%Y-%m-%d') AS open_date FROM servers 
			// 		WHERE $wherestr open=1 AND open_date>='".$opendate."' AND open_date<'".date('Y-m-d 00:00:00')."' AND combined_to=0 GROUP BY open_date";
			// $curservers = $this->paydb->get_list($sql);

			// $wherestr = $cid > 0 ? "cid='$cid'" : '';
			// foreach ($curservers as $key => $value) {
			// 	$dateline =  strtotime(date('Y-m-d 08:00:00', strtotime($value['open_date']))) * 1000;
			// 	$sql = "SELECT SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.cid=b.cid AND a.sid=b.sid 
			// 			WHERE $wherestr gdate='".$value['open_date']."' AND open_date>='".$value['open_date']." 00:00:00' AND open_date<='".$value['open_date']." 23:59:59' AND combined_to=0";
			// 	$amount = $this->paydb->get_list($sql);
			// 	$intamount = $amount[0]['amount'] ? intval($amount[0]['amount']) : 0;
			// 	$list[$key] = array($dateline, $intamount);
			// }

		// 	$data['today']['list'] = $list;
		// 	unset($list, $verlist, $versionlist);
		// }else {
		// 	$sql = "SELECT COUNT(sid) AS num, COUNT(CASE WHEN is_combined=1 THEN 1 ELSE NULL END) AS combinednum,date_format(open_date,'%Y-%m-%d') AS open_date FROM servers 
		// 			WHERE open=1 AND open_date<'".date('Y-m-d 00:00:00')."' GROUP BY open_date";
		// 	$curservers = $this->paydb->get_list($sql);

		// 	foreach ($curservers as $key => $value) {
		// 		$dateline =  strtotime(date('Y-m-d 08:00:00', strtotime($value['open_date']))) * 1000;
		// 		$num = intval($value['num']);
		// 		$totalserverlist[$key] = array($dateline, $num);

		// 		$sql = "SELECT SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.cid=b.cid AND a.sid=b.sid 
		// 				WHERE gdate='".$value['open_date']."' AND open_date>='".$value['open_date']." 00:00:00' AND open_date<='".$value['open_date']." 23:59:59' AND is_combined=0";
		// 		$amount = $this->paydb->get_list($sql);
		// 		$intamount = $amount[0]['amount'] ? intval($amount[0]['amount']) : 0;
		// 		$list[$key] = array($dateline, $intamount);

		// 		$combinednum = intval($value['combinednum']);
		// 		$avglist[$key] = array($dateline, $combinednum);
		// 	}
		// 	$data['today']['avglist'] = $avglist;
		// 	$data['today']['totalservlist'] = $totalserverlist;
		// 	$data['today']['list'] = $list;
		// 	unset($list, $verlist, $versionlist, $avglist, $totalserverlist);
		// }

		output_json(0, '', $data);
	}
	/**
	 * 时段充值对比
	 * @return [type] [description]
	 */
	public function compare(){
		include template('report', 'pay_compare');
	}

	public function ajax_compare_list(){
		$date1 = isset($_GET['date1']) && !empty($_GET['date1']) ? trim($_GET['date1']) : '';
		$date2 = isset($_GET['date2']) && !empty($_GET['date2']) ? trim($_GET['date2']) : '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$date = date('Y-m-d');

		$wherestr = $cid > 0 ? ' AND cid='.$cid.'' : '';

		$chartData = array();

		$listday = $listday1 = $listday2 = array();
		$max = 0;
		if (!empty($date1)){
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date1)."' AND dtime_unix<'".strtotime($date1.' 23:59:59')."' $wherestr GROUP BY hour";
			$day1 = $this->paydb->get_list($sql);
			foreach ($day1 as $key => $value) {
				$listday1[$value['hour']] = array(
					'name' => $value['hour'].':00',
					'y' => intval($value['totalamount']),
				);
				if (intval($value['totalamount']) > $max) {
					$max = intval($value['totalamount']);
				}
			}
		}

		if (!empty($date2)){
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date2)."' AND dtime_unix<'".strtotime($date2.' 23:59:59')."' $wherestr GROUP BY hour";
			$day2 = $this->paydb->get_list($sql);
			foreach ($day2 as $key => $value) {
				$listday2[$value['hour']] = array(
					'name' => $value['hour'].':00',
					'y' => intval($value['totalamount']),
				);
				if (intval($value['totalamount']) > $max) {
					$max = intval($value['totalamount']);
				}
			}
		}

		$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date)."' AND dtime_unix<'".strtotime($date.' 23:59:59')."' $wherestr GROUP BY hour";
		$day = $this->paydb->get_list($sql);
		foreach ($day as $key => $value) {
			$listday[$value['hour']] = array(
				'name' => $value['hour'].':00',
				'y' => intval($value['totalamount']),
			);
			if (intval($value['totalamount']) > $max) {
				$max = intval($value['totalamount']);
			}
		}

		for($i=0; $i<25; $i++) {
			$hour = str_pad($i, 2, '0', STR_PAD_LEFT);
			$chartData['categories'][] = $hour.':00';
			if (!empty($date1)) {
				if (!isset($listday1[$hour])) {
					$listday1[$hour] = array(
						'name' => $hour.':00',
						'y' => null,
					);
				} 
			}
			if (!empty($date2)) {
				if (!isset($listday2[$hour])) {
					$listday2[$hour] = array(
						'name' => $hour.':00',
						'y' => null,
					);
				} 
			}
			if (!empty($listday)) {
				if (!isset($listday[$hour])) {
					$listday[$hour] = array(
						'name' => $hour.':00',
						'y' => null,
					);
				}
			}
		}

		sort($listday1);
		sort($listday2);
		sort($listday);

		$weekarray = array("天","一","二","三","四","五","六"); 
		// if (!empty($listday)) {
			$chartData['series'][] = array('name' => '今天', 'data' => array_values($listday));
		// }
		if (!empty($date1)) {
			$chartData['series'][] = array('name' => $date1.' 星期'.$weekarray[date('w', strtotime($date1))], 'data' => array_values($listday1));
		}
		if (!empty($date2)) {
			$chartData['series'][] = array('name' => $date2.' 星期'.$weekarray[date('w', strtotime($date2))], 'data' => array_values($listday2));
		}
		
		$chartData['chartOptions'] = array(
			'title' => array('text' => ''),
			'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 2),
			'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=> round($max/4), 'min'=>0, 'max' => $max+50, 'name'=>''),
			'plotOptions' => array('series' => array('marker' => array('enabled' => false)))
		);

		$data['list'] = $chartData;
		$data['count'] = $max;
		unset($chartData);
		output_json(0, '', $data);
	}
	/**
	 * 消费充值比率
	 */
	public function consume()
	{
		if (isset($_GET['dogetSubmit'])) {
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			if ($sid <= 0) output_json(1, Lang('error'));

			$pubdb = common::load_model('public_model');
			$this->getdb = $pubdb->set_db($sid);
			if ($this->getdb === false) output_json(1, Lang('error'));

			$startTime = isset($_GET['startTime']) && !empty($_GET['startTime']) ? trim($_GET['startTime']) : '';
			$endTime = isset($_GET['endTime']) && !empty($_GET['endTime']) ? trim($_GET['endTime']) : '';

			$wherestr = '';
			if (!empty($startTime) && !empty($endTime)) {
				$startTime = strtotime(date('Y-m-d 00:00:00', strtotime($startTime)));
				$endTime = strtotime(date('Y-m-d 23:59:59', strtotime($endTime)));
				$wherestr = " AND change_time>='$startTime' AND change_time<='$endTime'";
			}

			$sql = "SELECT SUM(value) AS num FROM player_ingot_change_record a 
					LEFT JOIN player b ON a.player_id=b.id 
					WHERE a.type<>35 AND b.is_tester=0 AND b.vip_level>0 $wherestr";
			$consume = $this->getdb->get_count($sql);

			$sql = "SELECT SUM(value) AS num FROM player_ingot_change_record a 
					LEFT JOIN player b ON a.player_id=b.id 
					WHERE a.type=35 AND b.is_tester=0 AND b.vip_level>0 $wherestr";
			$pay = $this->getdb->get_count($sql);

			$data['list']['pay'] = round($pay/10, 2);
			$data['list']['consume'] = round($consume/10, 2);
			$data['list']['rate'] = $pay > 0 ? round($consume/$pay, 2) : 0;
			output_json(0, '', $data);
		}else {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			include template('report', 'report_pay_consume');
		}
	}
	public function income()
	{
		if (isset($_GET['dogetSubmit'])) {
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$type = isset($_GET['type']) ? trim($_GET['type']) : 'month';
		$wherestr = '';
		if ($cid > 0) {
			$wherestr = 'cid='.$cid;
		}
		$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$pagesize = 20;
		$offset = $pagesize*($page-1);
		switch ($type) {
			case 'year':
				$dtype = "%Y";
				
				break;
			case 'week':
				$dtype = "%Y.%u";
				break;
			case 'day':
				$dtype = "%Y-%m-%d";
				break;
			default:
				$dtype = "%Y-%m";
				$pagesize = 200;
				break;
		}
		$this->paydb->table_name = 'game_data';
		
		$wherestr ? $wherestr = 'where '.$wherestr : $wherestr = '';
		$sql = "select date_format(gdate, '$dtype') as idate, sum(`pay_player_count`) as pay_player_count,  ROUND(sum(pay_amount),2) as pay_amount, sum(pay_num) as pay_num, sum(new_player) as new_player,ROUND((ROUND(sum(pay_amount),2)/sum(`pay_player_count`)),2) as arpu from game_data $wherestr GROUP BY idate ORDER BY idate desc LIMIT $offset, $pagesize";
		$recordnum = $this->paydb->get_list("SELECT COUNT(*) AS num FROM `game_data` $wherestr GROUP BY date_format(gdate, '$dtype')");
		$list = $this->paydb->get_list($sql);
		$max = 0;
		foreach ($list as $key => $value){
			$max = max($max, $value['pay_amount']);
		}
		foreach ($list as $key2 => $value2){
			$value2['max'] = $value2['pay_amount']/$max * 180 +5;
			$list2[] = $value2;
		}
		$data['list']  = $list2;
		$data['count'] = count($recordnum);
		output_json(0, '', $data);
		}else{
		include template('report', 'pay_income');
		}
	}
}
