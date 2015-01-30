<?php 
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class data extends admin {
	private $pubdb, $getdb;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
	}
	/**
	 * 数据列表
	 * @return [type] [description]
	 */
	public function total(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$list = array();
		if ($cid > 0){
			$wherestr = parent::check_pf_priv('server', $cid);
			$wherestr = stripos($wherestr, 'where') !== false ? $wherestr." AND cid='$cid'" : "WHERE cid='$cid'";

			$sql = "SELECT sid,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS newer_count, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num, SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count, SUM(`avg_online_count`) AS online_count, SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume FROM game_data $wherestr GROUP BY sid";
			$gamelist = $this->pubdb->get_list($sql);
			$glist = array();
			if (count($gamelist) > 0) {
				foreach ($gamelist as $key => $value) {
					$gamelist[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$gamelist[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$gamelist[$key]['pay_amount'] = round($value['pay_amount'], 2);
					$gamelist[$key]['url'] = '#app=4&cpp=41&url='.urlencode(WEB_URL.INDEX.'?m=report&c=data&v=servertotal&cpp=41&sid='.$value['sid'].'&name=');
					$gamelist[$key]['consume'] = round($value['consume'], 2);
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
			$yesterday = date('Y-m-d', strtotime('-1 day'));
			$yes_sql = "SELECT a.avg_online_count FROM game_data a $wherestr and a.gdate='".$yesterday."' order by a.sid desc";
			$yes_list = $this->pubdb->get_list($yes_sql);
			foreach ($yes_list as $yes_key => $yes_value) {
				if (!isset($list[$yes_key]))	continue;
				$yes_list[$yes_key] = array_merge($yes_value, $list[$yes_key]);
			}
			$list = $yes_list;
			unset($glist, $gamelist);
		}

		include template('report', 'data_total');
	}
	/**
	 * 每日数据
	 * @return [type] [description]
	 */
	public function daily(){
		$month_arr[0] = '2012-02';
		for ($i=1;$i<100;$i++){
			$month_arr[$i] = date('Y-m',strtotime('+'.$i.' month', strtotime("2012-02-01")));
			if ($month_arr[$i] == date("Y-m",time())) break;
		}
		$month_arr = array_reverse($month_arr);
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
		$month = isset($_GET['month']) && !empty($_GET['month']) ? trim($_GET['month']) : date('Y-m');
		$startDate = $month.'-01';
		$endDate = date('Y-m-d',strtotime('+1 month', strtotime($startDate)));

		$sql =  "SELECT gdate,SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(`max_online_count`) AS max_online_count ".
			"FROM game_data WHERE gdate>='$startDate' AND gdate<'$endDate' GROUP BY gdate order by gdate desc";
		$list = $this->pubdb->get_list($sql);

		$chartData = $register = $create = $maxonline = $avgcreate = array();
		$max = 0;
		if (count($list) > 0){
			foreach ($list as $key => $value) {
				$chartData['categories'][] = $value['gdate'];
				$register[$key] = array(
					'name' => $value['gdate'],
					'y' => intval($value['register_count']),
				);

				$create[$key] = array(
					'name' => $value['gdate'],
					'y' => intval($value['create_count']),
				);

				if (date('w', strtotime($value['gdate'])) == 0 || date('w', strtotime($value['gdate'])) == 6){
					$register[$key]['marker'] = array(
						'fillColor' => 'red',
						'states' => array('hover' => array('fillColor' => 'red'))
					);
					$create[$key]['marker'] = array(
						'name' => $value['gdate'],
						'y' => intval($value['create_count']),
					);
					$list[$key]['fill'] = 1;
				}

				$max = max($max, intval($value['create_count']), intval($value['register_count']));
			}

			$chartData['chartOptions'] = array(
				'title' => array('text' => ''),
				'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 6),
				'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=>round($max/4), 'min'=>0, 'max' => $max, 'name'=>''),
				'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
			);
		}

		$chartData['series'][] = array('name' => '注册数', 'data' => $register);
		$chartData['series'][] = array('name' => '创建数', 'data' => $create);

		$chartData = json_encode($chartData);
		include template('report', 'data_active');
	}
	/**
	 * 运营状态
	 * @return [type] [description]
	 */
	public function companystatus() {
		if (isset($_GET['doget'])){
			$list = array();
			$sql = "SELECT b.cid, b.name, COUNT(sid) AS servernum, COUNT(CASE WHEN is_combined=1 THEN 1 ELSE NULL END) AS combinednum, COUNT(CASE WHEN open=1 THEN 1 ELSE NULL END) AS openednum, COUNT(CASE WHEN open_date>='".date('Y-m-d')."' AND open=1 AND is_combined=0 THEN 1 ELSE NULL END) AS todayopenednum, COUNT(CASE WHEN open_date>='".date('Y-m-d')."' AND is_combined=0 THEN 1 ELSE NULL END) AS waitopennum FROM servers a LEFT JOIN company b ON a.cid=b.cid and b.cid=1 GROUP BY a.cid";
			$company = $this->pubdb->get_list($sql);
			
			$memkey = md5('data_companystatus');
			$income = getcache($memkey);
			if (!$income) {
				$pubdb = common::load_model('public_model');
				$wherestr = parent::check_pf_priv('server');
				$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';
				$stardate = strtotime(date('Y-m-d 00:00:00'));
				$enddate = $stardate + 24 * 3600;
				$yesterday = date('Y-m-d',time()-24*60*60);
				$curmonth  = date('Y-m-01');
				//todayincome
				$pubdb->table_name = 'pay_data';
				$where = !empty($wherestr) ? $wherestr." AND dtime_unix>=$stardate AND dtime_unix<$enddate AND status<>1 AND success<>0" : " dtime_unix>=$stardate AND dtime_unix<$enddate AND status<>1 AND success<>0";
				$amount = $pubdb->get_one($where, 'SUM(amount) AS amount');
				$income['todayincome'] = round($amount['amount'], 2);
				//totalincome
				$pubdb->table_name = 'game_data';
				$amount1 = $pubdb->get_one($wherestr, 'SUM(pay_amount) AS amount');
				$income['totalincome'] = $amount1['amount'] ? round($amount1['amount'], 2) : '0';
				$income['totalincome'] += $income['todayincome'];
				//yesterdayincome
				$where = !empty($wherestr) ? $wherestr." AND gdate='$yesterday'" : " gdate='$yesterday'";
				$amount1 = $pubdb->get_one($where, 'SUM(pay_amount) AS amount');
				$income['yesterdayincome'] = $amount1['amount'] ? round($amount1['amount'], 2) : '0';
				//curmonthincome
				$where = !empty($wherestr) ? $wherestr." AND gdate>='$curmonth'" : " gdate>='$curmonth'";
				$amount1 = $pubdb->get_one($where, 'SUM(pay_amount) AS amount');
				$income['curmonthincome'] = $amount1['amount'] ? round($amount1['amount'], 2) : '0';
				$income['curmonthincome'] += $income['todayincome'];
//				$sql = "SELECT cid, SUM(amount) AS totalincome, SUM(CASE WHEN TO_DAYS(dtime)=TO_DAYS(NOW()) THEN amount ELSE 0 END) AS todayincome, SUM(CASE WHEN TO_DAYS(NOW())-TO_DAYS(dtime)<= 1 THEN amount ELSE 0 END) AS yesterdayincome, SUM(CASE WHEN DATE_FORMAT(dtime, '%Y%m')=DATE_FORMAT(CURDATE(), '%Y%m') THEN amount ELSE 0 END) AS curmonthincome FROM pay_data WHERE status<>1 AND success<>0 GROUP BY cid ORDER BY todayincome DESC";
//				$income = $this->pubdb->get_list($sql);
				setcache($memkey, $income, '', 'memcache', 'memcache', 30*60);
			}
//			$list = array();
//			foreach ($company as $key => $value) {
//				$flag = false;
//				foreach ($income as $ikey => $ivalue) {
//					if ($value['cid'] == $ivalue['cid']){
//						$list[$key] = array_merge($value, $ivalue, array('rank'=>$ikey+1));
//						$flag = true;
//						break;
//					}
//				}
//				if (!$flag){
//					$list[$key] = array_merge($value, array('totalincome'=>0, 'todayincome'=>0, 'yesterdayincome'=>0, 'curmonthincome'=>0, 'rank'=>0));
//				}
//			}
			$list = array_merge($company[0],$income);
			$data['list'] = $list;
			unset($income, $company, $list);
			output_json(0, '', $data);
		}else {
			include template('report', 'data_companystatus');
		}
	}
	/**
	 * 登录分级流失统计
	 * @return [type] [description]
	 */
	public function lossrate() {
		if (isset($_GET['doSubmit']) && $_GET['doSubmit'] == 1){
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
			$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
			$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime']) : 0;
			$start_level = isset($_GET['start_level']) && intval($_GET['start_level'])>0 ? intval($_GET['start_level'])  : 1;
			$end_level = isset($_GET['end_level']) && intval($_GET['end_level'])>0 ? intval($_GET['end_level'])  : 0;
			$daynum = isset($_GET['daynum']) && intval($_GET['daynum'])>0 ? intval($_GET['daynum']) : 0;
			$deadline = isset($_GET['deadline']) && !empty($_GET['deadline']) ? trim($_GET['deadline']) : '';

			$wherestr = '';
			$wherestr = $starttime > 0 ? " first_login_time>'$starttime'" : '';
			if ($endtime > 0){
				$wherestr .= !empty($wherestr) ? " AND first_login_time<'$endtime'" : "first_login_time<'$endtime'";
			}
			if ($start_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND level>='.$start_level.'': ' level>='.$start_level.'';
			}
			if ($end_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND level<='.$end_level.'': ' level<='.$end_level.'';
			}
			$wherestr .= !empty($wherestr) ? ' AND is_tester=0': 'is_tester=0';
			$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : $wherestr;

			if ($daynum > 0) {
				$daytimes = time() - $daynum * 24 * 3600;
			}else {
				$daytimes = strtotime($deadline.' 23:59:59');
			}
			
			$list = array();
			if ($cid > 0 && count($sid) > 0) {
				foreach ($sid as $key=>$value) {
					$this->getdb = $this->pubdb->set_db($value);
					if ($this->getdb !== false){
						$sql = "SELECT 
								level, 
								COUNT(a.id) AS num, 
								COUNT(CASE WHEN d.total_ingot > 0 THEN 1 ELSE NULL END) AS paynum, 
								COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time > 0 THEN 1 ELSE NULL END) AS lossnum, 
								COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time>0 AND d.total_ingot>0 THEN 1 ELSE NULL END) AS losspaynum,
								COUNT(CASE WHEN vip_level>0 THEN 1 ELSE NULL END) AS vipnum,
								COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time>0 AND vip_level>0 AND d.total_ingot>0 THEN 1 ELSE NULL END) AS lossvipnum
								FROM player a 
								LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
								LEFT JOIN player_trace c ON a.id=c.player_id 
								LEFT JOIN player_charge_record d ON a.id=d.player_id 
								$wherestr GROUP BY level";
						$list[] = $this->getdb->get_list($sql);

						$sql = "SELECT 
								vip_level, level,
								COUNT(CASE WHEN vip_level>0 THEN 1 ELSE NULL END) AS vipnum,
								COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time>0 AND vip_level>0 THEN 1 ELSE NULL END) AS lossvipnum
								FROM player a 
								LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
								LEFT JOIN player_trace c ON a.id=c.player_id 
								$wherestr GROUP BY level";
						$viplist[] = $this->getdb->get_list($sql);
					}
				}
				$alllist = $allviplist = array();

				$allviplist[0]['level'] = 0;
				foreach ($viplist as $key => $value) {
					foreach ($value as $k => $val) {
						$allviplist[0]['vipnum'] += intval($val['vipnum']);
						$allviplist[0]['lossvipnum'] += intval($val['lossvipnum']);

						$allviplist[$val['level']]['level'] = $val['vip_level'];
						$allviplist[$val['level']]['vipnum'] += intval($val['vipnum']);
						$allviplist[$val['level']]['lossvipnum'] += intval($val['lossvipnum']);
					}
				}

				$alllist[0]['level'] = 0;
				foreach ($list as $key => $value) {
					foreach ($value as $k => $val) {
						$alllist[0]['num'] += intval($val['num']);
						$alllist[0]['paynum'] += intval($val['paynum']);
						$alllist[0]['lossnum'] += intval($val['lossnum']);
						$alllist[0]['losspaynum'] += intval($val['losspaynum']);
						$alllist[0]['vipnum'] = intval($allviplist[0]['vipnum']);
						$alllist[0]['lossvipnum'] = intval($allviplist[0]['lossvipnum']);

						$alllist[$val['level']]['level'] = $val['level'];
						$alllist[$val['level']]['num'] += intval($val['num']);
						$alllist[$val['level']]['paynum'] += intval($val['paynum']);
						$alllist[$val['level']]['lossnum'] += intval($val['lossnum']);
						$alllist[$val['level']]['losspaynum'] += intval($val['losspaynum']);
						
						$alllist[$val['level']]['vipnum'] = intval($allviplist[$val['level']]['vipnum']);
						$alllist[$val['level']]['lossvipnum'] = intval($allviplist[$val['level']]['lossvipnum']);
					}
				}
				sort($alllist);
				$data['list'] = $alllist;
				output_json(0, '', $data);
			}

			output_json(1, '');
		}else {
			$type = 2;
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			$minlevel = 1;
			$maxlevel = common::load_config('system', 'maxlevel');
			include template('report', 'data_lossrate');			
		}
	}


	/**
	 * 分级流失
	 */
	public function losslevel()
	{
		$type = 1;
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$minlevel = 1;
		$maxlevel = common::load_config('system', 'maxlevel');
		include template('report', 'data_lossrate');
	}
	/**
	 * 新手流失
	 */
	public function lossnewer()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;

		include template('report', 'data_lossnewer');
	}
	/**
	 * 渠道统计
	 * @return [type] [description]
	 */
	public function channel() {
		if (isset($_GET['dogetSubmit'])) {
				$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
				$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
				$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
				$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime'].' 23:59:59') : 0;
				$source = isset($_GET['source']) && !empty($_GET['source']) ? trim($_GET['source']) : '';
				$type =  isset($_GET['type']) ? intval($_GET['type']) : 0;

				if ($cid > 0) {
					$wherestr = '';
					 if ($type == 1){
		                    $wherestr = $starttime > 0 ? " regdate>='$starttime'" : '';
		                    if ($endtime > 0){
		                        $wherestr .= !empty($wherestr) ? " AND regdate<='$endtime'" : " regdate<='$endtime'";
		                    }
		                }else {
		                    $wherestr = $starttime > 0 ? " first_login_time>='$starttime'" : '';
		                    if ($endtime > 0){
		                        $wherestr .= !empty($wherestr) ? " AND first_login_time<='$endtime'" : " first_login_time<='$endtime'";
		                    }
		                }
		                if (!empty($source)){
		                    $wherestr = "source LIKE '%$source%'".(!empty($wherestr) ? ' AND '.$wherestr : '');
		                }
		                $wherestr = !empty($wherestr) ? ' WHERE '.$wherestr.' AND is_tester=0': 'WHERE is_tester=0';
					if ($sid > 0) {
						$this->getdb = $this->pubdb->set_db($sid);
						if ($this->getdb !== false){
							$sql = "SELECT
							source,
		                    COUNT(a.id) AS num,
		                    COUNT(CASE WHEN nickname<>'' THEN 1 ELSE NULL END) AS createnum,
		                    COUNT(CASE WHEN level>=2 THEN 1 ELSE NULL END) AS level2,
		                    COUNT(CASE WHEN level>=10 THEN 1 ELSE NULL END) AS level10,
		                    COUNT(CASE WHEN level>=20 THEN 1 ELSE NULL END) AS level20,
		                    COUNT(CASE WHEN level>=30 THEN 1 ELSE NULL END) AS level30,
		                    COUNT(CASE WHEN level>=40 THEN 1 ELSE NULL END) AS level40,
		                    COUNT(CASE WHEN d.total_ingot>0 THEN 1 ELSE NULL END) AS paynum,
		                    SUM(total_ingot) AS amount
		                    FROM player a 
		                    LEFT JOIN player_trace b ON a.id=b.player_id 
		                    LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
		                    LEFT JOIN player_charge_record d ON a.id=d.player_id
		                    $wherestr GROUP BY source;";
							$data['list'] = $this->getdb->get_list($sql);
							output_json(0, '', $data);
						}
					}else {
						$sql = "SELECT
		                    COUNT(a.id) AS num,
		                    COUNT(CASE WHEN nickname<>'' THEN 1 ELSE NULL END) AS createnum,
		                    COUNT(CASE WHEN level>=2 THEN 1 ELSE NULL END) AS level2,
		                    COUNT(CASE WHEN level>=10 THEN 1 ELSE NULL END) AS level10,
		                    COUNT(CASE WHEN level>=20 THEN 1 ELSE NULL END) AS level20,
		                    COUNT(CASE WHEN level>=30 THEN 1 ELSE NULL END) AS level30,
		                    COUNT(CASE WHEN level>=40 THEN 1 ELSE NULL END) AS level40,
		                    COUNT(CASE WHEN d.total_ingot>0 THEN 1 ELSE NULL END) AS paynum,
		                    SUM(total_ingot) AS amount
		                    FROM player a 
		                    LEFT JOIN player_trace b ON a.id=b.player_id 
		                    LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
		                    LEFT JOIN player_charge_record d ON a.id=d.player_id
		                    $wherestr;";
						$servdb = common::load_model('public_model');
						$servdb->table_name = 'servers';
						$serverlist = $servdb->select("cid=$cid AND combined_to=0 AND open=1 AND open_date<'".date('Y-m-d H:i:s')."'", 'sid,db_server,db_root,db_pwd,db_name,server_ver');
						$list = array();
						foreach ($serverlist as $key => $value) {
							$db_host = explode(':', $value['db_server']);
		    				$gdb = new mysqli($db_host[0], $value['db_root'], $value['db_pwd'], $value['db_name'], $db_host[1]);
					    	if ($gdb->connect_error) continue;
							if ($result = $gdb->query($sql)) {
		    					while ($row = $result->fetch_assoc()) {
		                			$list[] = $row;
		    					}
		    					$result->close();
		    				}
						}
						$arr = array();
						foreach ($list as $key => $value) {
							$arr['source'] = $source;
							$arr['num'] += $value['num'];
							$arr['createnum'] += $value['createnum'];
							$arr['level2'] += $value['level2'];
							$arr['level10'] += $value['level10'];
							$arr['level20'] += $value['level20'];
							$arr['level30'] += $value['level30'];
							$arr['level40'] += $value['level40'];
							$arr['paynum'] += $value['paynum'];
							$arr['amount'] += $value['amount'];
						}
						$data['list'] = array(0=>$arr);
						output_json(0, '', $data);
					}
				}

				output_json(1, '');
		}else {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			include template('report', 'data_channel');
		}
	}
	/**
	 * 财富统计
	 */
	public function asset()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_asset');
	}
	/**
	 * 装备统计
	 */
	public function item()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_item');
	}
	
	/**
	 * 命格统计
	 */
	public function fate()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_fate');
	}
	/**
	 * 命格统计
	 */
	public function role()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_role');
	}
	/**
	 * 命格统计
	 */
	public function power()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_power');
	}
	/**
	 * via统计
	 */
	public function via_stat() {
		$viaid = isset($_GET['viaid']) ? intval($_GET['viaid']) : 1;
		if (isset($_GET['statdate']) && $_GET['statdate'] != date('Y-m')) {
			$startTime = $_GET['statdate'].'-01';
			$endTime = date('Y-m-d' ,strtotime($startTime) + 30 * 24 * 3600);
		}else {
			$startTime = date('Y-m-01');
			$endTime = date('Y-m-d');
		}

		$viadb = common::load_model('via_model');
		$vialist = $viadb->select("viaid='$viaid' and dateline>=$startTime and dateline<=$endTime", 'key, num, dateline, createtime');
		$list = array();

		$year = date('Y');
		$month = intval(date('m'));
		for ($i=$month; $i > 0; $i--) { 
			$dateArr[] = $year .'-'. str_pad($i, 2, '0', STR_PAD_LEFT);
		}
		if ($viaid == 4 || $viaid == 5 || $viaid == 6) {
			foreach ($vialist as $key => $value) {
				$datetime = date('Y-m-d',  ($value['createtime'] - 24 * 3600));
				switch ($value['key']) {
					case 'create': $list['create'] = $value['num']; break;
					case 'register': $list['register'] = $value['num']; break;
					case 'keep': $list['keep'][$datetime] = $value['num']; break;
					case 'paynum': $list['paynum'][$datetime] = $value['num']; break;
					case 'amount': $list['amount'][$datetime] = $value['num']; break;
				}
				$date_list[$datetime] = 1;
				$list['dateline'] = $value['dateline'];
			}


		}else {
			foreach ($vialist as $key => $value) {
				$list[$value['dateline']][$value['key']] = $value['num'];
			}
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
				$data['count'] = count($list);
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
	public function servertotal(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cpp'] = isset($_GET['cpp']) ? intval($_GET['cpp']) : 0;
		$data['name'] = isset($_GET['name']) ? urldecode(trim($_GET['name'])): Lang('single_server_report');
		include template('report', 'data_server_total');
	}
	/**
	 * 单服数据统计报表
	 * @return [type] [description]
	 */
	public function ajax_servertotal_list(){
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
		$data['count'] = count($list);
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
		echo $total_online_num ? $total_online_num : '0';
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
	 * 新注册用户
	 */
	public function ajax_active_list()
	{
		$month = isset($_GET['month']) && !empty($_GET['month']) ? trim($_GET['month']) : date('Y-m');
		$startDate = $month.'-01';
		$endDate = date('Y-m-d',strtotime('+1 month', strtotime($startDate)));

		$sql =  "SELECT gdate,SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(`max_online_count`) AS max_online_count ".
			"FROM game_data WHERE gdate>='$startDate' AND gdate<'$endDate' GROUP BY gdate";
		$list = $this->pubdb->get_list($sql);

		$chartData = $register = $create = $maxonline = $avgcreate = array();
		$max = 0;
		if (count($list) > 0){
			foreach ($list as $key => $value) {
				$chartData['categories'][] = $value['gdate'];
				$register[$key] = array(
					'name' => $value['gdate'],
					'y' => intval($value['register_count']),
				);

				$create[$key] = array(
					'name' => $value['gdate'],
					'y' => intval($value['create_count']),
				);
				$list[$key]['fill'] = 0;
				if (date('w', strtotime($value['gdate'])) == 0 || date('w', strtotime($value['gdate'])) == 6){
					$register[$key]['marker'] = array(
						'fillColor' => 'red',
						'states' => array('hover' => array('fillColor' => 'red'))
					);
					$create[$key]['marker'] = array(
						'name' => $value['gdate'],
						'y' => intval($value['create_count']),
					);
					$list[$key]['fill'] = 1;
				}

				$max = max($max, intval($value['create_count']), intval($value['register_count']));
			}

			$chartData['chartOptions'] = array(
				'title' => array('text' => ''),
				'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 6),
				'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=>round($max/4), 'min'=>0, 'max' => $max, 'name'=>''),
				'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
			);
		}

		$chartData['series'][] = array('name' => '注册数', 'data' => $register);
		$chartData['series'][] = array('name' => '创建数', 'data' => $create);

		$data['list'] = $chartData;
		$data['detailed_list'] = $list;
		unset($chartData);
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
//		$month = trim($_GET['month']);
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
				"FROM game_data $wherestr GROUP BY gdate";
			$list = $this->pubdb->get_list($sql);

			$chartData = $register = $create = $maxonline = $avgcreate = array();

			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$chartData['categories'][] = $value['gdate'];
					$register[$key] = array(
						'name' => $value['gdate'],
						'y' => intval($value['register_count']),
					);

					$create[$key] = array(
						'name' => $value['gdate'],
						'y' => intval($value['create_count']),
					);

					if (date('w', strtotime($value['gdate'])) == 0 || date('w', strtotime($value['gdate'])) == 6){
						$register[$key]['marker'] = array(
							'fillColor' => 'red',
							'states' => array('hover' => array('fillColor' => 'red'))
						);
						$create[$key]['marker'] = array(
							'name' => $value['gdate'],
							'y' => intval($value['create_count']),
						);
					}
				}

				$chartData['chartOptions'] = array(
					'title' => array('text' => ''),
					'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 6),
					'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=>140, 'min'=>0, 'max' => '700', 'name'=>''),
					'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
				);
			}

			$chartData['series'][] = array('name' => '注册数', 'data' => $register);
			$chartData['series'][] = array('name' => '创建数', 'data' => $create);

			$data['list'] = $chartData;
			$data['count'] = count($chartData);
			unset($chartData);
			output_json(0, '', $data);

		}else {
			$sql =  "SELECT gdate,COUNT(1) AS servernum,SUM(`pay_amount`) AS pay_amount,SUM(`pay_player_count`) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
				",SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(login_count) AS login_count,SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume,sum(`new_player`) as new_player".
				"FROM game_data $wherestr GROUP BY gdate ORDER BY gdate DESC";
			$list = $this->pubdb->get_list($sql);
			if (count($list) > 0){
				$weekarray = array("日","一","二","三","四","五","六"); 
				foreach ($list as $key => $value) {
					$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
					$list[$key]['consume'] = round($value['consume'], 2);
					$list[$key]['week'] = '周'.$weekarray[date('w', strtotime($value['gdate']))];
				}
			}

			$data['list'] = $list;
			$data['count'] = count($list);
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
				",SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count,SUM(login_count) AS login_count,SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume,sum(`new_player`) as new_player ".
				"FROM game_data a LEFT JOIN servers b ON a.sid=b.sid $wherestr and b.combined_to=0 GROUP BY a.sid ORDER BY a.sid DESC";
			$list = $this->pubdb->get_list($sql);
			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
					$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
					$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
				}
			}
			$data['list'] = $list;
			$data['count'] = count($list);
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

		$month = isset($_GET['month']) && !empty($_GET['month']) ? trim($_GET['month']) : date("Y-m");
		$this_time = getdate();
		$day_num = $month != date("Y-m") ? date("t",strtotime($month.'-01')) : $this_time["mday"];//计算本月天数
		for ($i=$day_num;$i>0;$i--)
		{
			$day_list[] = $month.'-'.str_pad($i,2,"0",STR_PAD_LEFT);
			
		}
		for ($i=0;$i<=23;$i++){
			$hour_list[] = str_pad($i,2,"0",STR_PAD_LEFT);		
		}

		$startTime = strtotime($month.'-01');
		$endTime = strtotime($month.'-'.str_pad($day_num, 2, '0', STR_PAD_LEFT).' 23:59:59');
		
		$pubdb = common::load_model('public_model');
		$list = array();
		$list = $pubdb->get_list("SELECT DISTINCT(FROM_UNIXTIME(online_time, '%Y-%m')) AS omonth FROM online ORDER BY omonth DESC");
		foreach ($list as $key => $value) {
			$monthlist[]=$value['omonth'];
		}
		if (empty($monthlist)) {
			$monthlist[] = $month;
		}
		if (isset($_GET['doget'])) {
			$list = array();
			$list = $pubdb->get_list("SELECT online_count, FROM_UNIXTIME(`online_time`, '%Y-%m-%d') as day,FROM_UNIXTIME(`online_time`, '%H') as hour FROM online WHERE online_time >= '$startTime' AND online_time <= '$endTime' GROUP BY day, hour");
			foreach ($list as $key => $value) {
				$online[$value['day']][$value['hour']] =  intval($value['online_count']);
			}

			$list = array();
			$list = $pubdb->get_list("SELECT COUNT(1) as pay_count, FROM_UNIXTIME(`dtime_unix`, '%Y-%m-%d') as day,FROM_UNIXTIME(`dtime_unix`, '%H') as hour FROM pay_data WHERE dtime_unix >= '$startTime' AND dtime_unix <= '$endTime'AND success <> 0 AND status <> 1  GROUP BY day, hour");
			foreach ($list as $key => $value) {
				$pay[$value['day']][$value['hour']] =  intval($value['pay_count']);
			}

			include template('ajax', 'online_compare_detail');
		}else {
			include template('report', 'data_online_compare');
		}
	}
	/**
	 * 时段在线对比
	 * @return [type] [description]
	 */
	public function ajax_compare_list(){
		$date1 = isset($_GET['date1']) && !empty($_GET['date1']) ? trim($_GET['date1']) : '';
		$date2 = isset($_GET['date2']) && !empty($_GET['date2']) ? trim($_GET['date2']) : '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$date = date('Y-m-d');

		$pubdb = common::load_model('public_model');
		$listday = $listday1 = $listday2 = array();
		$lifttime = strtotime(date('Y-m-d 23:59:59')) - time();

		for($i=0; $i<24; $i++) {
			$categories[] = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
		}

		$max = 0;
		$listday = $listday1 = $listday2 = $chartData = array();
		//在线
		if (!empty($date1)){
			$memkey = md5('online_compare_listday1');
			$listday1 = getcache($memkey);
			if (!$listday1) {
				$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date1' GROUP BY hour";
				$day1 = $pubdb->get_list($sql);
				foreach ($day1 as $key => $value) {
					//$listday1[$key] = array($value['hour'].':00', intval($value['online_count']))
					$listday1[$key] = array(
						'name' => $value['hour'].':00',
						'y' => intval($value['online_count']),
					);
					$max = max($max, intval($value['online_count']));
				}
				setcache($memkey, $listday1, '', 'memcache', 'memcache', $lifttime);
			}
			$chartData['series'][] = array('name' => $date1, 'data' => array_values($listday1));
		}
		if (!empty($date2)){
			$memkey = md5('online_compare_listday2');
			$listday2 = getcache($memkey);
			if (!$listday2) {
				$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date2' GROUP BY hour";
				$day2 = $pubdb->get_list($sql);
				foreach ($day2 as $key => $value) {
					//$listday2[$key] = array($value['hour'].':00', intval($value['online_count']));
					$listday2[$key] = array(
						'name' => $value['hour'].':00',
						'y' => intval($value['online_count']),
					);
					$max = max($max, intval($value['online_count']));
				}
				
				setcache($memkey, $listday2, '', 'memcache', 'memcache', $lifttime);
			}
			$chartData['series'][] = array('name' => $date2, 'data' => array_values($listday2));
		}
		$memkey = md5('online_compare_listday');
		$listday = getcache($memkey);
		if (!$listday) {
			$sql = "SELECT from_unixtime(online_time, '%H') AS hour,SUM(online_count) AS online_count FROM online WHERE from_unixtime(online_time, '%Y-%m-%d')='$date' GROUP BY hour";
			$day = $pubdb->get_list($sql);
			foreach ($day as $key => $value) {
				//$listday[$key] = array($value['hour'].':00', intval($value['online_count']));
				$listday[$key] = array(
					'name' => $value['hour'].':00',
					'y' => intval($value['online_count']),
				);
				$max = max($max, intval($value['online_count']));
			}
			
			setcache($memkey, $listday, '', 'memcache', 'memcache', 300);
		}
		$chartData['series'][] = array('name' => '今天', 'data' => array_values($listday));
		$list = array_merge($listday,$listday1,$listday2);
		foreach ($list as $key=> $val){
			$max = max($max,intval($val['y']));
		}
		$chartData['categories'] =  $categories;
		$chartData['chartOptions'] = array(
			'title' => array('text' => '时段在线趋势图'),
			'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 6),
			'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=>round($max/4), 'min'=>0, 'max' => $max, 'name'=>''),
			'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
		);
		$data['list']['online'] = $chartData;
		unset($listday, $listday1, $listday2, $chartData, $list);

		$max = 0;
		$listday = $listday1 = $listday2 = $chartData = array();
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
					//$listday1[$key] = array($value['hour'].':00', intval($value['paycount']));
					$listday1[$key] = array(
						'name' => $value['hour'].':00',
						'y' => intval($value['paycount']),
					);
					$max = max($max, intval($value['paycount']));
				}
				
				setcache($memkey, $listday1, '', 'memcache', 'memcache', $lifttime);
			}
			$chartData['series'][] = array('name' => $date1, 'data' => array_values($listday1));
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
					//$listday2[$key] = array($value['hour'].':00', intval($value['paycount']));
					$listday2[$key] = array(
						'name' => $value['hour'].':00',
						'y' => intval($value['paycount']),
					);
					$max = max($max, intval($value['paycount']));
				}
				
				setcache($memkey, $listday2, '', 'memcache', 'memcache', $lifttime);
			}
			$chartData['series'][] = array('name' => $date2, 'data' => array_values($listday2));
		}

		$memkey = md5('paycount_compare_listday');
		$listday = getcache($memkey);
		if (!$listday) {
			$stime = strtotime($date);
			$etime = strtotime('+1 day', $stime);
			$sql = "SELECT from_unixtime(dtime_unix, '%H') AS hour,COUNT(DISTINCT username) AS paycount FROM pay_data WHERE dtime_unix>=$stime AND dtime_unix<$etime GROUP BY hour";
			$day = $pubdb->get_list($sql);
			foreach ($day as $key => $value) {
				//$listday[$key] = array($value['hour'].':00', intval($value['paycount']));
				$listday[$key] = array(
					'name' => $value['hour'].':00',
					'y' => intval($value['paycount']),
				);
				$max = max($max, intval($value['paycount']));
			}
			
			setcache($memkey, $listday, '', 'memcache', 'memcache', 300);
		}
		$chartData['series'][] = array('name' => '今天', 'data' => array_values($listday));
		$list = array_merge($listday,$listday1,$listday2);
		foreach ($list as $key=> $val){
			$max = max($max,intval($val['y']));
		}
		$chartData['categories'] =  $categories;
		$chartData['chartOptions'] = array(
			'title' => array('text' => '充值次数趋势图'),
			'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 6),
			'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=>round($max/4), 'min'=>0, 'max' => $max, 'name'=>''),
			'plotOptions' => array('series' => array('marker' => array('enabled' => false))),
		);
		$data['list']['pay'] = $chartData;
		unset($listday, $listday1, $listday2, $chartData, $list);

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

	public function townonline()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_townonline');
	}
	public function stay()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		include template('report', 'data_stay');
	}
	/**
	 * 今日收入
	 * @return [type] [description]
	 */
	public function today_amount() {
		$has_check_priv = $this->has_priv('manage', 'main', 'index');
		if ($has_check_priv === false)	parent::exitHtml(1);
		$pubdb = common::load_model('public_model');
		$wherestr = parent::check_pf_priv('server');
		$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';
		$stardate = strtotime(date('Y-m-d 00:00:00'));
		$enddate = $stardate + 24 * 3600;
		$pubdb->table_name = 'pay_data';
		$where = !empty($wherestr) ? $wherestr." AND dtime_unix>=$stardate AND dtime_unix<$enddate AND status<>1 AND success<>0" : " dtime_unix>=$stardate AND dtime_unix<$enddate AND status<>1 AND success<>0";
		$amount = $pubdb->get_one($where, 'SUM(amount) AS amount');
		$today_amount = round($amount['amount'], 2);
		echo $today_amount ? $today_amount : '0';
		exit;
	}
}
