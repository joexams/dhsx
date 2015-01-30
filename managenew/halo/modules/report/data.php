<?php 
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class data extends admin {
	private $pubdb;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
	}
	/**
	 * 数据列表
	 * @return [type] [description]
	 */
	public function total(){
		include template('report', 'data_total');
	}
	/**
	 * 每日数据
	 * @return [type] [description]
	 */
	public function daily(){
		include template('report', 'data_daily');
	}
	/**
	 * 单日数据
	 * @return [type] [description]
	 */
	public function day_total(){
		$day = isset($_GET['day']) ? trim($_GET['day']) : '';
		$data['day'] = $day;
		include template('report', 'data_day_total');
	}
	/**
	 * 注册在线人数
	 * @return [type] [description]
	 */
	public function active(){
		include template('report', 'data_active');
	}
	/**
	 * 运营状态
	 * @return [type] [description]
	 */
	public function companystatus() {
		if (isset($_GET['doget'])){
			$sql = "SELECT b.cid, b.name, COUNT(sid) AS servernum, COUNT(CASE WHEN is_combined=1 THEN 1 ELSE NULL END) AS combinednum, COUNT(CASE WHEN open=1 THEN 1 ELSE NULL END) AS openednum, COUNT(CASE WHEN open_date>='".date('Y-m-d')."' AND open=1 AND is_combined=0 THEN 1 ELSE NULL END) AS todayopenednum, COUNT(CASE WHEN open_date>='".date('Y-m-d')."' AND is_combined=0 THEN 1 ELSE NULL END) AS waitopennum FROM servers a LEFT JOIN company b ON a.cid=b.cid GROUP BY a.cid";
			$company = $this->pubdb->get_list($sql);
			$data['list'] = $list;
			
			$memkey = md5('data_companystatus');
			$income = getcache($memkey);
			if (!$income) {
				$sql = "SELECT cid, SUM(amount) AS totalincome, SUM(CASE WHEN TO_DAYS(dtime)=TO_DAYS(NOW()) THEN amount ELSE 0 END) AS todayincome, SUM(CASE WHEN TO_DAYS(NOW())-TO_DAYS(dtime)<= 1 THEN amount ELSE 0 END) AS yesterdayincome, SUM(CASE WHEN DATE_FORMAT(dtime, '%Y%m')=DATE_FORMAT(CURDATE(), '%Y%m') THEN amount ELSE 0 END) AS curmonthincome FROM pay_data GROUP BY cid ORDER BY todayincome DESC";
				$income = $this->pubdb->get_list($sql);
				setcache($memkey, $income, '', 'memcache', 'memcache', 30*60);
			}
			$list = array();
			foreach ($company as $key => $value) {
				$flag = false;
				foreach ($income as $ikey => $ivalue) {
					if ($value['cid'] == $ivalue['cid']){
						$list[$key] = array_merge($value, $ivalue, array('rank'=>$ikey+1));
						$flag = true;
						break;
					}
				}
				if (!$flag){
					$list[$key] = array_merge($value, array('totalincome'=>0, 'todayincome'=>0, 'yesterdayincome'=>0, 'curmonthincome'=>0, 'rank'=>0));
				}
			}

			$data['list'] = $list;
			unset($income, $company, $list);
			output_json(0, '', $data);
		}else {
			include template('report', 'data_companystatus');
		}
	}
	/**
	 * 流失率统计
	 * @return [type] [description]
	 */
	public function lossrate() {
		if (isset($_GET['doSubmit']) && $_GET['doSubmit'] == 1){

			output_json(0, '');
		}else {
			include template('report', 'data_lossrate');			
		}
	}
	/**
	 * 渠道统计
	 * @return [type] [description]
	 */
	public function channel() {

		include template('report', 'data_channel');
	}
	
	/**
	 * via统计
	 */
	public function via_stat() {
		$viaid = isset($_GET['viaid']) ? intval($_GET['viaid']) : 1;
		$viadb = common::load_model('via_model');
		$vialist = $viadb->select(array('viaid'=>$viaid), 'key, num, dateline');
		$list = array();

		foreach ($vialist as $key => $value) {
			$list[$value['dateline']][$value['key']] = $value['num'];
		}
		$weekarray = array("日","一","二","三","四","五","六"); 	
		include template('report', 'data_via_stat');
	}

	/**
	 * 开服前N天数据统计
	 * @return [type] [description]
	 */
	public function openndays(){
		$this->pubdb->table_name = 'servers';
		if (isset($_GET['dogetSubmit']) && intval($_GET['dogetSubmit']) == 1) {
			$date  = isset($_GET['date']) && !empty($_GET['date']) ? trim($_GET['date']) : '';
			$ndays = isset($_GET['ndays']) && intval($_GET['ndays']) > 0? intval($_GET['ndays']): 3;
			if (!empty($date) && $ndays > 0) {
				$serverlist = $this->pubdb->select("date_format(open_date, '%Y-%m-%d')='$date' AND open=1" , 'sid,name,o_name');
				$list = $server = array();
				if ($serverlist){
					$sids = '';
					foreach ($serverlist as $key => $value) {
						$sids = $value['sid'].',';
						$server[$value['sid']] = !empty($value['name']) ? $value['name'] : $value['o_name'];
					}
					$sdis = trim($sids,',');
					$topopendate = date('Y-m-d',strtotime($date) + $ndays * 24 * 3600);
					$sql = "SELECT sid,gdate,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS new_player, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
						   ", SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count".
						   ", SUM(`avg_online_count`) AS avg_online_count, SUM(`max_online_count`) AS max_online_count, SUM(`out_count`) AS out_count, SUM(`consume`) AS consume ".
						   " FROM game_data WHERE sid IN ($sdis) AND gdate>='$date' AND gdate<'$topopendate' GROUP BY gdate, sid;";
					 $list = $this->pubdb->get_list($sql);
				}
			   
			    if (count($list) > 0){
			   	    foreach ($list as $key => $value) {
			   	    	$list[$key]['order'] = $key;
			   	    	$list[$key]['name'] = $server[$value['sid']];
			   			$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
				   		$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
				   		$list[$key]['user_loss_per'] = $list[$key-1]['create_count'] > 0 ? round($value['out_count']/$list[$key-1]['create_count'], 2) *100 : 0;
				   		$list[$key]['consume_pay_per'] = $value['pay_amount'] > 0 ? abs(round($value['consume']/$value['pay_amount'], 2)) : 0;
						$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
						$list[$key]['consume'] = abs(round($value['consume'], 2));
				   	}
			    }
				$data['list'] = $list;

				output_json(0, '', $data);
			}
		}else {
			$serverlist = $this->pubdb->select("open=1 AND open_date<='".date('Y-m-d 00:00:00')."'","COUNT(sid) AS servernum,DATE_FORMAT(open_date, '%Y-%m-%d') AS opendate", '', 'open_date DESC', 'opendate');

			$data['list'] = json_encode($serverlist);
			include template('report', 'data_openndays');
		}
	}
	/**
	 * 单服数据统计
	 * @return [type] [description]
	 */
	public function server_total(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cpp'] = isset($_GET['cpp']) ? intval($_GET['cpp']) : 0;
		$data['name'] = isset($_GET['name']) ? urldecode(trim($_GET['name'])): Lang('single_server_report');
		include template('report', 'data_server_total');
	}
	/**
	 * 单服数据统计报表
	 * @return [type] [description]
	 */
	public function ajax_server_total_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? trim($_GET['starttime']) : date('Y-m-01');
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? trim($_GET['endtime']) : date('Y-m-d');
		if ($sid <= 0){
			output_json(1, '');
		}
		$wherestr = parent::check_pf_priv('server', 0, $sid);
		$wherestr = !empty($wherestr) ? $wherestr." AND sid='$sid'" : "WHERE sid='$sid'";
		$wherestr .= !empty($starttime) ? " AND gdate>='$starttime'" : '';
		$wherestr .= !empty($endtime) ? " AND gdate<='$endtime'" : '';

		$sql = "SELECT gdate,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS new_player, SUM(`pay_player_count`) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
			   ", SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count".
			   ", SUM(`avg_online_count`) AS avg_online_count, SUM(`max_online_count`) AS max_online_count, SUM(`out_count`) AS out_count, SUM(`consume`) AS consume ".
			   " FROM game_data $wherestr GROUP BY gdate ORDER BY gdate DESC;";
	    $list = $this->pubdb->get_list($sql);
	    if (count($list) > 0){
	   	    foreach ($list as $key => $value) {
	   			$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
		   		$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
		   		$list[$key]['user_loss_per'] = $list[$key+1]['create_count'] > 0 ? round($value['out_count']*100/$list[$key+1]['create_count'], 2) : 0;
		   		$list[$key]['consume_pay_per'] = $value['pay_amount'] > 0 ? round(abs($value['consume'])/$value['pay_amount'], 2) : 0;
				$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
		   		$list[$key]['consume'] = abs(round($value['consume'], 2));
		   	}
	    }
		$data['list'] = $list;
		unset($list);		
		output_json(0, '', $data);
	}
	/**
	 * 当前在线
	 * @return [type] [description]
	 */
	public function online() {
		if (file_exists(ROOT_PATH.'../online_data.php')){
			include_once(ROOT_PATH.'../online_data.php');
		}else {
			$total_online_num = 0;
		}
		echo $total_online_num ? $total_online_num : '-';
		exit;
	}
	/**
	 * 数据列表
	 * @return [type] [description]
	 */
	public function ajax_total_list(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		if ($cid <= 0){
			output_json(1, Lang('company_no_selected'));
		}
/*
		$wherestr = parent::check_pf_priv('server', $cid);
		
		$memkey = md5('total_list_'.$wherestr.$cid);
		//delcache($memkey);
		$lifttime = strtotime(date('Y-m-d 23:59:59')) - time();
		$list = getcache($memkey);
		$wherestr = str_ireplace('cid', 'a.cid', $wherestr);
		$wherestr = str_ireplace('sid', 'a.sid', $wherestr);
		if (!$list) {
			$wherestr =  "WHERE a.cid='$cid' AND  open_date<'".date('Y-m-d 00:00:00')."'";

			$sql = "SELECT a.sid,name,o_name,open_date,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS newer_count, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
			   ", SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count".
			   ", SUM(`avg_online_count`) AS online_count, SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume".
			   " FROM servers a LEFT JOIN game_data b ON a.sid=b.sid $wherestr GROUP BY a.sid ORDER BY a.sid DESC";
			$list = $this->pubdb->get_list($sql);
			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$list[$key]['opendate'] = ceil((time() -strtotime($value['open_date'])) / (24*3600));
					$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
				}
			}
			setcache($memkey, $list, '', 'memcache', 'memcache', $lifttime);
		}
		$wherestr =  "WHERE a.cid='$cid' AND open_date>='".date('Y-m-d 00:00:00')."' AND open_date<'".date('Y-m-d 23:59:59')."'";
		$sql = "SELECT a.sid,name,o_name,open_date,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS newer_count, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
		   ", SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count".
		   ", SUM(`avg_online_count`) AS online_count, SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume".
		   " FROM servers a LEFT JOIN game_data b ON a.sid=b.sid $wherestr GROUP BY a.sid ORDER BY a.sid DESC";
		$otherlist = $this->pubdb->get_list($sql);
		if (count($otherlist) > 0){
			foreach ($otherlist as $key => $value) {
				$otherlist[$key]['opendate'] = ceil((time() -strtotime($value['open_date'])) / (24*3600));
				$otherlist[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
				$otherlist[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
				$otherlist[$key]['pay_amount'] = round($value['pay_amount'], 2);
			}
		}
		$list = array_merge($otherlist, $list);
		$data['list'] = $list;
		unset($list, $otherlist);
*/
		$wherestr = parent::check_pf_priv('server', $cid);
		$wherestr = stripos($wherestr, 'where') !== false ? $wherestr." AND cid='$cid'" : "WHERE cid='$cid'";

		$sql = "SELECT sid,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS newer_count, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num, SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count, SUM(`avg_online_count`) AS online_count, SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume FROM game_data $wherestr GROUP BY sid";
		$gamelist = $this->pubdb->get_list($sql);
		$glist = array();
		if (count($gamelist) > 0){
			foreach ($gamelist as $key => $value) {
				$gamelist[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
				$gamelist[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
				$gamelist[$key]['pay_amount'] = round($value['pay_amount'], 2);
				$glist[$value['sid']] = $gamelist[$key]; 
			}
		}
		$wherestr = str_ireplace('cid', 'a.cid', $wherestr);
		$wherestr = str_ireplace('sid', 'a.sid', $wherestr);
		$sql = "SELECT a.sid, a.name, a.o_name, a.open_date, b.max_player_level FROM servers a LEFT JOIN servers_data b ON a.sid=b.sid $wherestr AND a.open_date<='".date('Y-m-d 00:00:00')."' ORDER BY a.sid DESC";
		$list = $this->pubdb->get_list($sql);
		foreach ($list as $skey => $svalue) {
			if (!isset($glist[$svalue['sid']]))	continue;
			$svalue['opendate'] = ceil((time() -strtotime($svalue['open_date'])) / (24*3600));
			$list[$skey] = array_merge($svalue, $glist[$svalue['sid']]);
		}
		$data['list'] = $list;
		unset($list, $glist, $gamelist);
		output_json(0, '', $data);
	}
	/**
	 * 每日数据
	 * @return [type] [description]
	 */
	public function ajax_daily_list(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$isall = isset($_GET['isall']) ? intval($_GET['isall']) : 0;

		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? trim($_GET['starttime']) : date('Y-m-d', time()-30*24*3600);
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? trim($_GET['endtime']) : date('Y-m-d');

		$wherestr = parent::check_pf_priv('server', $cid);
		if ($cid > 0){
			$wherestr = !empty($wherestr) ? $wherestr." AND cid='$cid'" : "WHERE cid='$cid'";
			$wherestr .= " AND gdate>='$starttime'";
		}else {
			$wherestr = !empty($wherestr) ? $wherestr.' AND ' : ' WHERE';
			$wherestr .= " gdate>='$starttime'";
		}
		
		$wherestr .= " AND gdate<='$endtime'";
		$list = array();
		if ($isall == 1){
			$sql =  "SELECT gdate,SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(`max_online_count`) AS max_online_count ".
				"FROM game_data GROUP BY gdate";
			$list = $this->pubdb->get_list($sql);

			$register = $create = $maxonline = $avgcreate = array();
			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$dateline = strtotime(date('Y-m-d 08:00:00', strtotime($value['gdate'])))*1000;
					$avg = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$avgcreate[$key] = array($dateline, $avg);
					$register[$key] = array($dateline, intval($value['register_count']));
					$create[$key] = array($dateline, intval($value['create_count']));
					$maxonline[$key] = array($dateline, intval($value['max_online_count']));
				}
			}

			$data['list']['register'] = $register;
			$data['list']['create'] = $create;
			$data['list']['maxonline'] = $maxonline;
			$data['list']['avgcreate'] = $avgcreate;
			unset($register, $create, $maxonline);
		}else {
			$sql =  "SELECT gdate,COUNT(1) AS servernum,SUM(`pay_amount`) AS pay_amount,SUM(`pay_player_count`) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
				",SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(login_count) AS login_count,SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume ".
				"FROM game_data $wherestr GROUP BY gdate ORDER BY gdate DESC";
			$list = $this->pubdb->get_list($sql);
			if (count($list) > 0){
				$weekarray = array("日","一","二","三","四","五","六"); 
				foreach ($list as $key => $value) {
					$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
					$list[$key]['week'] = '周'.$weekarray[date('w', strtotime($value['gdate']))];
				}
			}

			$data['list'] = $list;
		}
		
		unset($list);
		output_json(0, '', $data);
	}
	/**
	 * 单日数据汇总
	 * @return [type] [description]
	 */
	public function ajax_day_list(){
		$day = isset($_GET['day']) ? trim($_GET['day']) : '';

		if (!empty($day)){
			$wherestr = "WHERE gdate='$day'";
			$sql =  "SELECT a.sid,b.name, o_name,SUM(`pay_amount`) AS pay_amount,SUM(`pay_player_count`) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
				",SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(login_count) AS login_count,SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume ".
				"FROM game_data a LEFT JOIN servers b ON a.sid=b.sid $wherestr GROUP BY a.sid ORDER BY a.sid DESC";
			$list = $this->pubdb->get_list($sql);
			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
				}
			}
			$data['list'] = $list;

			unset($list);
			output_json(0, '', $data);
		}

		output_json(1, Lang('error'));
	}

	/**
	 * 时段充值对比
	 * @return [type] [description]
	 */
	public function compare(){
		$data['monthlist'] = array();
		for ($year=date('Y');$year>=2011;$year--){
			$month = $year == date('Y') ? date('n') : 12;
			for($month; $month >= 1; $month--){
			    $data['monthlist'][] = $year.'-'.($month < 10? '0'.$month: $month);
			}
		}

		include template('report', 'data_online_compare');
	}
	/**
	 * 时段在线对比
	 * @return [type] [description]
	 */
	public function ajax_online_compare_list(){
		$date1 = isset($_GET['date1']) && !empty($_GET['date1']) ? trim($_GET['date1']) : '';
		$date2 = isset($_GET['date2']) && !empty($_GET['date2']) ? trim($_GET['date2']) : '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$date = date('Y-m-d');

		$pubdb = common::load_model('public_model');
		$listday = $listday1 = $listday2 = array();
		$lifttime = strtotime(date('Y-m-d 23:59:59')) - time();
		//在线
		if (!empty($date1)){
			$memkey = md5('online_compare_listday1');
			$listday1 = getcache($memkey);
			if (!$listday1) {
				$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date1' GROUP BY hour";
				$day1 = $pubdb->get_list($sql);
				foreach ($day1 as $key => $value) {
					$listday1[$key] = array($value['hour'].':00', intval($value['online_count']));
				}
				setcache($memkey, $listday1, '', 'memcache', 'memcache', $lifttime);
			}
		}
		if (!empty($date2)){
			$memkey = md5('online_compare_listday2');
			$listday2 = getcache($memkey);
			if (!$listday2) {
				$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date2' GROUP BY hour";
				$day2 = $pubdb->get_list($sql);
				foreach ($day2 as $key => $value) {
					$listday2[$key] = array($value['hour'].':00', intval($value['online_count']));
				}
				setcache($memkey, $listday2, '', 'memcache', 'memcache', $lifttime);
			}
		}

		$memkey = md5('online_compare_listday');
		$listday = getcache($memkey);
		if (!$listday) {
			$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date' GROUP BY hour";
			$day = $pubdb->get_list($sql);
			foreach ($day as $key => $value) {
				$listday[$key] = array($value['hour'].':00', intval($value['online_count']));
			}
			setcache($memkey, $listday, '', 'memcache', 'memcache', 300);
		}

		$data['list']['online']['today'] = $listday;
		$data['list']['online']['day1']  = $listday1;
		$data['list']['online']['day2']  = $listday2;
		unset($listday, $listday1, $listday2);

		$listday = $listday1 = $listday2 = array();
		//充值次数
		if (!empty($date1)){
			$memkey = md5('paycount_compare_listday1');
			$listday1 = getcache($memkey);
			if (!$listday1) {
				$stime = strtotime($date1);
				$etime = strtotime('+1 day', $stime);
				$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,COUNT(DISTINCT username) AS paycount FROM pay_data WHERE dtime_unix>=$stime AND dtime_unix<$etime  GROUP BY hour";
				$day1 = $pubdb->get_list($sql);
				foreach ($day1 as $key => $value) {
					$listday1[$key] = array($value['hour'].':00', intval($value['paycount']));
				}
				setcache($memkey, $listday1, '', 'memcache', 'memcache', $lifttime);
			}
		}
		if (!empty($date2)){
			$memkey = md5('paycount_compare_listday2');
			$listday2 = getcache($memkey);
			if (!$listday2) {
				$stime = strtotime($date2);
				$etime = strtotime('+1 day', $stime);
				$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,COUNT(DISTINCT username) AS paycount FROM pay_data WHERE dtime_unix>=$stime AND dtime_unix<$etime GROUP BY hour";
				$day2 = $pubdb->get_list($sql);
				foreach ($day2 as $key => $value) {
					$listday2[$key] = array($value['hour'].':00', intval($value['paycount']));
				}
				setcache($memkey, $listday2, '', 'memcache', 'memcache', $lifttime);
			}
		}


		$memkey = md5('paycount_compare_listday');
		$listday = getcache($memkey);
		if (!$listday) {
			$stime = strtotime($date);
			$etime = strtotime('+1 day', $stime);
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,COUNT(DISTINCT username) AS paycount FROM pay_data WHERE dtime_unix>=$stime AND dtime_unix<$etime GROUP BY hour";
			$day = $pubdb->get_list($sql);
			foreach ($day as $key => $value) {
				$listday[$key] = array($value['hour'].':00', intval($value['paycount']));
			}
			setcache($memkey, $listday, '', 'memcache', 'memcache', 300);
		}
		$data['list']['paycount']['today'] = $listday;
		$data['list']['paycount']['day1']  = $listday1;
		$data['list']['paycount']['day2']  = $listday2;
		unset($listday, $listday1, $listday2);

		output_json(0, '', $data);
	}
	/**
	 * 分时段详细数据
	 * @return [type] [description]
	 */
	public function ajax_online_list(){
		$month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m');
		$datetime = isset($_GET['day']) ? $_GET['day'] : 0;

		$pubdb = common::load_model('public_model');

		$listday = array();

		if ($datetime > 0){
			$datetime = date('Y-m-d', $datetime/1000);
			$sql = "SELECT online_time,online_count FROM online_detail WHERE from_unixtime(online_time, '%Y-%m-%d')='$datetime'";
			$day = $pubdb->get_list($sql);
			foreach ($day as $key => $value) {
				$dateline = $value['online_time'] * 1000;
				$listday[$key] = array($dateline, intval($value['online_count']), date('Y-m-d H:i', $value['online_time']));
			}
			$data['list'] = $listday;
			unset($day, $listday);
		}else {
			$sql = "SELECT online_time,from_unixtime(online_time, '%Y-%m-%d') AS day, from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m')='$month' GROUP BY day, hour";
			$day = $pubdb->get_list($sql);

			foreach ($day as $key => $value) {
				$dateline = $value['online_time'] * 1000;
				$listday[$key] = array($dateline, intval($value['online_count']));
			}
			$data['list']['online'] = $listday;
			unset($day, $listday);

			$listday = array();
			$stime = strtotime($month.'-01');
			$etime = strtotime('+1 month', $stime);
			$sql = "SELECT date_format(dtime, '%Y-%m-%d %H:00') AS dtime,from_unixtime(dtime_unix, '%Y-%m-%d') AS day,from_unixtime(dtime_unix, '%H') AS hour,COUNT(DISTINCT username) AS paycount FROM pay_data WHERE dtime_unix>=$stime AND dtime_unix<$etime GROUP BY day, hour";
			$day = $pubdb->get_list($sql);

			foreach ($day as $key => $value) {
				$dateline = strtotime($value['dtime']) * 1000;
				$listday[$key] = array($dateline, intval($value['paycount']));
			}
			$data['list']['paycount'] = $listday;
			unset($day, $listday);
		}

		output_json(0, '', $data);
	}
}
