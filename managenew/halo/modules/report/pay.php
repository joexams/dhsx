<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class pay extends admin {
	private $paydb;
	function __construct(){
		parent::__construct();
		$this->paydb = common::load_model('pay_model');
	}

	public function log(){
		$data['isall'] = 1;
		include template('report', 'pay');
	}
	/**
	 * 充值排行
	 * @return [type] [description]
	 */
	public function ranking(){
		
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
			
			if (!empty($starttime)){
				$wherestr .= !empty($wherestr) ? " AND gdate>='$starttime'" : "gdate>='$starttime'";
			}
			if (!empty($endtime)){
				$wherestr .= !empty($wherestr) ? " AND gdate<='$endtime'" : " gdate<='$endtime'";
			}

			if ($sflag == 1){
				$wherestr = $sid != 1 ? " WHERE sid IN ($strsids) AND ".$wherestr : $wherestr;
				$sql = "SELECT $column_1 FROM game_data $wherestr";
				$data['type'] = 2;
			}else {
				$wherestr = $cid != 1 ? " WHERE cid IN ($strcids) AND ".$wherestr : $wherestr;
				$sql = "SELECT $column,gdate FROM game_data $wherestr GROUP BY cid,gdate";
				$data['type'] = 1;
			}
		}

		$list = $this->paydb->get_list($sql);
		if ($list) {
			foreach ($list as $key => $value) {
				$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
			}
		}
		$data['list'] = $list;
		unset($list);
		output_json(0, '', $data);
	}
	/**
	 * 充值排行列表
	 * @return [type] [description]
	 */
	public function ajax_rank_list(){
		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$pagesize  = 20;
		$page      = max(intval($page), 1);
		$offset    = $pagesize*($page-1);

		$wherestr = '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
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
		$sql = "SELECT (@i:=@i+1) AS ranking,cid,username,nickname,amount,last_pay_amount,last_pay_time,pay_num FROM pay_player,(SELECT @i:=0) AS rank $wherestr ORDER BY amount DESC LIMIT $offset, $pagesize";
		$list = $this->paydb->get_list($sql);
		//if ($recordnum <= 0){
			$wherestr = str_ireplace('where', '', $wherestr);
			$count = $this->paydb->count($wherestr, '*');

			$ts = $this->paydb->get_one($wherestr, 'SUM(amount) AS total_amount, sum(pay_num) AS total_pay_times');
			$data['total_amount'] = round($ts['total_amount'], 2);
			$data['total_pay_times'] = $ts['total_pay_times'];
			$data['arpu'] = round($data['total_amount'] / $count, 2);
		//}

		$data['list']  = $list;
		$data['count'] = $count;
		output_json(0, '', $data);
	}
	/**
	 * 按条件获取充值记录
	 * @return [type] [description]
	 */
	public function ajax_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		$wherestr = '';
		if (isset($_GET['dogetSubmit'])){

			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$playername = isset($_GET['playername']) ? trim($_GET['playername']) : '';
			$stattime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
			$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime']) : 0;
			$oid = isset($_GET['oid']) ? trim($_GET['oid']) : '';
			$success = isset($_GET['success']) ? intval($_GET['success']) : 0;

			$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$pagesize  = 20;
			$page      = max(intval($page), 1);
			$offset    = $pagesize*($page-1);

			if ($cid > 0){
				$wherestr = " cid='$cid'";
			}
			if ($sid > 0){
				$wherestr .= !empty($wherestr) ? " AND sid='$sid'" : " sid='$sid'";
			}
			if (!empty($playername)){
				$wherestr .= !empty($wherestr) ? " AND username='$playername'" : " username='$playername'";
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
			$listsql = "SELECT pid, sid, dtime,amount,coins,oid,success FROM pay_data 
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
			unset($list);
			output_json(0, '', $data);
		}else if ($sid > 0 && $id > 0){
			$this->paydb->table_name  = 'pay_data';
			$data['list'] = $this->paydb->select(array('player_id'=>$id, 'sid'=>$sid), 'pid, dtime,amount,coins,oid,success', 100, 'pid DESC');
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

		$wherestr = '';
		if ($cid > 0){
			$wherestr = "a.cid='$cid'";
		}
		if ($type == 1){
			if (!empty($opendate)){
				$wherestr .= !empty($wherestr) ? " AND open_date<='$opendate'" : " open_date<='$opendate'";
			}
		}
		$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : '';
		$sql = "SELECT gdate,SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.sid=b.sid $wherestr GROUP BY gdate";
		$list = $this->paydb->get_list($sql);
		$tmplist = array();
		foreach ($list as $key => $value) {
			$dateline = strtotime(date('Y-m-d 08:00:00', strtotime($value['gdate'])))*1000;
			$amount = intval($value['amount']);
			$tmplist[$key] = array($dateline, $amount);
		}
		$verdb = common::load_model('version_model');

		$verlist = $verdb->select('', 'version,content,dateline');
		$versionlist = $avglist = $totalserverlist = $curserverlist = $tmpcombinelist = array();

		foreach ($verlist as $key => $value) {
			$versionlist[$key]['x'] =  strtotime(date('Y-m-d 08:00:00', $value['dateline'])) * 1000;
			$versionlist[$key]['title'] = $value['version'];
			$versionlist[$key]['text'] = $value['content'];
		}

		$wherestr = !empty($wherestr) ? ' AND is_combined=1' : ' WHERE is_combined=1';
		$sql = "SELECT date_format(open_date, '%Y-%m-%d') AS open_date, SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.sid=b.sid AND gdate=date_format(open_date, '%Y-%m-%d') $wherestr GROUP BY open_date";
		$combinelist = $this->paydb->get_list($sql);
		if ($combinelist){
			foreach ($combinelist as $key => $value) {
				$dateline = strtotime(date('Y-m-d 08:00:00', strtotime($value['open_date']))) * 1000;
				if ($dateline > 0){
					$intamount = intval($value['amount']);

					$tmpcombinelist[$key]['x'] = $dateline;
					$tmpcombinelist[$key]['title'] = '合服收入：'.round($value['amount'], 2);
					$tmpcombinelist[$key]['text'] = '合服收入：'.round($value['amount'], 2);	
				}
			}

			$versionlist = array_merge($versionlist, $tmpcombinelist);
			unset($combinelist, $tmpcombinelist);
		}

		$data['all']['flags'] = $data['today']['flags'] = $versionlist;
		if ($type == 1){
			$data['all']['list'] = $tmplist;
			unset($list, $tmplist);
		}else {
			$this->paydb->table_name = 'servers';
			foreach ($list as $key => $value) {
				$num = $this->paydb->count("open_date<='".$value['gdate']." 23:59:59'", 'sid');

				$dateline = strtotime(date('Y-m-d 08:00:00', strtotime($value['gdate'])))*1000;
				$num = intval($num);
				$totalserverlist[$key] = array($dateline, $num);

				if ($num > 0){
					$avgamount = round($value['amount']/$num, 2);
				}else {
					$avgamount = 0;
				}
				
				$avglist[$key] = array($dateline, $avgamount);
			}
			$data['all']['avglist'] = $avglist;
			$data['all']['totalservlist'] = $totalserverlist;
			$data['all']['list'] = $tmplist;
			unset($list, $tmplist, $avglist, $totalserverlist);
		}		

		//当日新服
		$list = $avglist = $totalserverlist = array();		
		if ($type == 1){
			$wherestr = '';
			if ($cid > 0){
				$wherestr = "cid='$cid' AND ";
			}
			if (!empty($opendate)){
				$wherestr .= "open_date>='$opendate' AND ";
			}
			$sql = "SELECT date_format(open_date,'%Y-%m-%d') AS open_date FROM servers 
					WHERE $wherestr open=1 AND open_date>='".$opendate."' AND open_date<'".date('Y-m-d 00:00:00')."' AND combined_to=0 GROUP BY open_date";
			$curservers = $this->paydb->get_list($sql);

			$wherestr = $cid > 0 ? "cid='$cid'" : '';
			foreach ($curservers as $key => $value) {
				$dateline =  strtotime(date('Y-m-d 08:00:00', strtotime($value['open_date']))) * 1000;
				$sql = "SELECT SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.cid=b.cid AND a.sid=b.sid 
						WHERE $wherestr gdate='".$value['open_date']."' AND open_date>='".$value['open_date']." 00:00:00' AND open_date<='".$value['open_date']." 23:59:59' AND combined_to=0";
				$amount = $this->paydb->get_list($sql);
				$intamount = $amount[0]['amount'] ? intval($amount[0]['amount']) : 0;
				$list[$key] = array($dateline, $intamount);
			}

			$data['today']['list'] = $list;
			unset($list, $verlist, $versionlist);
		}else {
			$sql = "SELECT COUNT(sid) AS num, COUNT(CASE WHEN is_combined=1 THEN 1 ELSE NULL END) AS combinednum,date_format(open_date,'%Y-%m-%d') AS open_date FROM servers 
					WHERE open=1 AND open_date<'".date('Y-m-d 00:00:00')."' GROUP BY open_date";
			$curservers = $this->paydb->get_list($sql);

			foreach ($curservers as $key => $value) {
				$dateline =  strtotime(date('Y-m-d 08:00:00', strtotime($value['open_date']))) * 1000;
				$num = intval($value['num']);
				$totalserverlist[$key] = array($dateline, $num);

				$sql = "SELECT SUM(pay_amount) AS amount FROM game_data a LEFT JOIN servers b ON a.cid=b.cid AND a.sid=b.sid 
						WHERE gdate='".$value['open_date']."' AND open_date>='".$value['open_date']." 00:00:00' AND open_date<='".$value['open_date']." 23:59:59' AND is_combined=0";
				$amount = $this->paydb->get_list($sql);
				$intamount = $amount[0]['amount'] ? intval($amount[0]['amount']) : 0;
				$list[$key] = array($dateline, $intamount);

				$combinednum = intval($value['combinednum']);
				$avglist[$key] = array($dateline, $combinednum);
			}
			$data['today']['avglist'] = $avglist;
			$data['today']['totalservlist'] = $totalserverlist;
			$data['today']['list'] = $list;
			unset($list, $verlist, $versionlist, $avglist, $totalserverlist);
		}

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

		$listday = $listday1 = $listday2 = array();
		if (!empty($date1)){
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date1)."' AND dtime_unix<'".strtotime($date1.' 23:59:59')."' $wherestr GROUP BY hour";
			$day1 = $this->paydb->get_list($sql);
			foreach ($day1 as $key => $value) {
				$listday1[$key] = array($value['hour'], intval($value['totalamount']));
			}
		}

		if (!empty($date2)){
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date2)."' AND dtime_unix<'".strtotime($date2.' 23:59:59')."' $wherestr GROUP BY hour";
			$day2 = $this->paydb->get_list($sql);
			foreach ($day2 as $key => $value) {
				$listday2[$key] = array($value['hour'], intval($value['totalamount']));
			}
		}

		$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,SUM(amount) AS totalamount FROM pay_data WHERE dtime_unix>='".strtotime($date)."' AND dtime_unix<'".strtotime($date.' 23:59:59')."' $wherestr GROUP BY hour";
		$day = $this->paydb->get_list($sql);
		foreach ($day as $key => $value) {
			$listday[$key] = array($value['hour'], intval($value['totalamount']));
		}

		$data['list']['today'] = $listday;
		$data['list']['day1']  = $listday1;
		$data['list']['day2']  = $listday2;

		output_json(0, '', $data);
	}
}
