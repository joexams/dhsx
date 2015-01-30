<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class index extends admin {
	private $userdb;
	function __construct(){
		parent::__construct();
		$this->userdb = common::load_model('user_model');
	}
	
	public function init(){
		$mainmenu = admin::admin_menu(0);
		$roleid   = $_SESSION['roleid'];
		$userid   = $_SESSION['userid'];
	    $privdb = common::load_model('priv_model');	
		$privdb->set_model(1);
		$privcount = $privdb->count(array('userid'=>$userid), '*');
		if ($privcount > 0) {
			$privwhere = 'userid='.$userid;
		}else {
			$privwhere = 'roleid='.$roleid;
			$privdb->set_model(0);
		}
		if ($roleid > 1) {
		$r = $privdb->get_one($privwhere, '*', 'mid ASC');
			$data['default'] = $r['mid'];
		}else {
			$data['default'] = 1;
		}

		$menuwhere = '';
		if ($roleid > 1) {
			$midlist = $privdb->select($privwhere, 'mid');
			if ($midlist) {
				foreach ($midlist as $key => $value) {
					$midarr[] = $value['mid'];
				}
				$menuwhere = ' AND mid IN ('.implode($midarr, ',').')';
			}
		} 

		$menudb   = common::load_model('menu_model');
		$list 	  = $menudb->select('depth < 4 AND display=0'.$menuwhere, 'mid,mname,parentid,m,v,c,parentpath,data,islink,urllink', '', 'listorder DESC');
		$menudb->tree = $list;
		$submenu = array();

		foreach ($mainmenu as $key => $pmenu) {
			$submenu[$pmenu['mid']] = $menudb->get_child($pmenu['mid']);
		}

		if (count($submenu) > 0){
			foreach ($submenu as $key => $value) {
				if ($value === false){
					continue;
				}
				
				foreach ($value as $tkey => $tvalue) {
					if ($roleid != 1){
						/*$pr = $privdb->get_one($privwhere.' AND mid='.$tvalue['mid']);
						if (!$pr) {
							unset($submenu[$key][$tkey]);
							continue;
						}*/

						$thirdmenu = $menudb->get_child($tvalue['mid']);
						if ($thirdmenu === false) {
							continue;
						}
						/*foreach ($thirdmenu as $thkey => $thvalue) {
							$pr = $privdb->get_one($privwhere.' AND mid='.$tvalue['mid']);
							if (!$pr){
								unset($thirdmenu[$thkey]);
								continue;
							}
						}*/
					}else {
						$thirdmenu = $menudb->get_child($tvalue['mid']);
					}
					$submenu[$key][$tkey]['thirdmenu'] = count($thirdmenu) > 0 ? $thirdmenu : false;
				}
				
			}
			
			foreach ($submenu as $key => $value) {
				if ($value !== false) {
					sort($submenu[$key]);
				}
			}
		}
		
		$qguidedb = common::load_model('quickguide_model');
		$quicklist = $qguidedb->select(array('userid'=>$userid), 'qid, qname, qurl', 10);

		$data['menu']['main'] = json_encode($mainmenu);
		$data['menu']['sub']  = json_encode($submenu);
		$data['quicklist']    = json_encode($quicklist); 
		unset($mainmenu, $submenu, $list, $menudb->tree, $thirdmenu, $quicklist, $qguidedb);

        $serverdb  = common::load_model('public_model');
        $serverdb->table_name = 'servers';
        $wherestr = '';
        $wherestr = parent::check_pf_priv('server');
        $wherestr .= !empty($wherestr) ? " AND combined_to=0 AND open=1 AND api_server<>'000'" :  "open=1 AND combined_to=0 AND api_server<>''";
		if ($roleid > 3) {
        	$serverlist = $serverdb->select(str_ireplace('where', '', $wherestr), 'cid,sid,name,o_name,server,unix_timestamp(open_date) as opendate,unix_timestamp(open_date_old) as oldopendate');
		}else {
        	$serverlist = $serverdb->select(str_ireplace('where', '', $wherestr), 'cid,sid,name,o_name,server,server_ver,api_server,combined_to,unix_timestamp(open_date) as opendate,unix_timestamp(open_date_old) as oldopendate,is_combined');
		}
        $serverlist = json_encode($serverlist);

        $serverdb->table_name = 'company';
        $wherestr = '';
        $wherestr = parent::check_pf_priv('company');
        $companylist = $serverdb->select(str_ireplace('where', '', $wherestr), 'cid, name', '', 'corder ASC');
        $companylist = json_encode($companylist);
		include template('manage', 'index');
	}
	/**
	 * 管理首页
	 * @return [type] [description]
	 */
	public function main() {
		$pubdb = common::load_model('public_model');
		$wherestr = parent::check_pf_priv('server');
		$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';

		$where = $where2 = $where3 = $dateHtml = '';
		$totalamount = $totalamount2 = $totalamount3 = array();
		$memcachecount = $memcachedate = 0;

		//近7天时段充值统计 
		$stardate = strtotime(date('Y-m-d 00:00:00'));
		for($i=0; $i < 8; $i++) {
			$curdate = $stardate - $i * 24 * 3600;
			$enddate = $curdate + 24 * 3600;
			$enddate2 = strtotime(date('Y-m-d ', $curdate).date('H:i:00'));
			if ($i > 0) {
				$dateHtml .= '<th style="width:100px;">'.date('Y-m-d', $curdate).'</th>';
				$memkey = md5($wherestr.'main_totalamount_'.$curdate);
				$totalamount[$i] = getcache($memkey);
				if (!$totalamount[$i]) {
					$pubdb->table_name = 'game_data';
					$where = !empty($wherestr) ? $wherestr." AND gdate='".date('Y-m-d', $curdate)."'" : " gdate='".date('Y-m-d', $curdate)."'";
					$amount = $pubdb->get_one($where, 'SUM(pay_amount) AS amount');
					$totalamount[$i] = round($amount['amount'], 2);
					setcache($memkey, $totalamount[$i], '', 'memcache', 'memcache', 5*24*3600);
				}

				$memkey = md5($wherestr.'main_totalamount3_'.$curdate);
				$totalamount3[$i] = getcache($memkey);
				if (!$totalamount3[$i]) {
					$pubdb->table_name = 'pay_data_detail';
					$where3 = "dateline>=$curdate AND dateline<=$enddate";
					$amount3 = $pubdb->get_one($where3, 'SUM(payamt_coins+pubacct_payamt_coins+goldcoupon+slivercoupon+coppercoupon) AS amount');
					$totalamount3[$i] = $amount3['amount']/10;
					setcache($memkey, $totalamount3[$i], '', 'memcache', 'memcache', 5*24*3600);
				}
			}else {
				$memkey = md5($wherestr.'main_totalamount_'.$curdate);
				$totalamount[$i] = getcache($memkey);
				if (!$totalamount[$i]) {
					$pubdb->table_name = 'pay_data';
					$where = !empty($wherestr) ? $wherestr." AND dtime_unix>=$curdate AND dtime_unix<$enddate AND status<>1 AND success<>0" : " dtime_unix>=$curdate AND dtime_unix<$enddate AND status<>1 AND success<>0";
					$amount = $pubdb->get_one($where, 'SUM(amount) AS amount');
					$totalamount[$i] = round($amount['amount'], 2);
					setcache($memkey, $totalamount[$i], '', 'memcache', 'memcache', 5*60);
				}

				$memkey = md5($wherestr.'main_totalamount3_'.$curdate);
				$totalamount3[$i] = getcache($memkey);
				if (!$totalamount3[$i]) {
					$pubdb->table_name = 'pay_data_detail';
					$where3 = "dateline>=$curdate AND dateline<=$enddate";
					$amount3 = $pubdb->get_one($where3, 'SUM(payamt_coins+pubacct_payamt_coins+goldcoupon+slivercoupon+coppercoupon) AS amount');
					$totalamount3[$i] = $amount3['amount']/10;
					setcache($memkey, $totalamount3[$i], '', 'memcache', 'memcache', 15*60);
				}
			}

			$memkey = md5($wherestr.'main_totalamount2_'.$curdate);
			$totalamount2[$i] = getcache($memkey);
			if (!$totalamount2[$i]) {
				$pubdb->table_name = 'pay_data';
				$where2 = !empty($wherestr) ? $wherestr." AND dtime_unix>=$curdate AND dtime_unix<=$enddate2 AND status<>1 AND success<>0" : "dtime_unix>=$curdate AND dtime_unix<=$enddate2 AND status<>1 AND success<>0";
				$amount2 = $pubdb->get_one($where2, 'SUM(amount) AS amount');
				$totalamount2[$i] = $amount2['amount'];
					$memcachedate += 1;
				setcache($memkey, $totalamount2[$i], '', 'memcache', 'memcache', 5*60);
			}
			$memkey = md5($wherestr.'main_totalpaynum'.$curdate);
			$totalpaynum[$i] = getcache($memkey);
			if (!$totalpaynum[$i]) {
				$where4 = !empty($wherestr) ? $wherestr." AND dtime_unix>=$curdate AND dtime_unix<=$enddate2 AND status<>1 AND success<>0" : "dtime_unix>=$curdate AND dtime_unix<=$enddate2 AND status<>1 AND success<>0";
				$totalpaynum[$i] = $pubdb->count($where4, 'distinct(username)');
				setcache($memkey, $totalpaynum[$i], '', 'memcache', 'memcache', 5*60);
			}
		}

		//收入情况
		$curmonth = date('Y-m-01');
		$yestermonth = date('Y-m-d', mktime(0, 0, 0, date('m')-1, 1, date('Y')));
		$yesterday = date('Y-m-d', strtotime('-1 day'));
		$pubdb->table_name = 'game_data';
		$where = !empty($wherestr) ? $wherestr." AND gdate>='$curmonth'" : "gdate>='$curmonth'";

		$pmemkey = md5('income_'.$wherestr);
		$income = getcache($pmemkey);
		if (!$income) {	
			$amount1 = $pubdb->get_one($where, 'SUM(pay_amount) AS amount');
			$income['month'] = $amount1['amount'] ? sprintf('%.2f', $amount1['amount']) : '0';

			$where = !empty($wherestr) ? $wherestr." AND gdate>='$yestermonth' AND gdate<'$curmonth'" : "gdate>='$yestermonth' AND gdate<'$curmonth'";
			$amount1 = $pubdb->get_one($where, 'SUM(pay_amount) AS amount');
			$income['yestermonth'] = $amount1['amount'] ? sprintf('%.2f', $amount1['amount']) : '0';
			$income['today'] = $totalamount[0] ? sprintf('%.2f', $totalamount[0]) : '0';
			$income['yesterday'] = $totalamount[1] ? sprintf('%.2f', $totalamount[1]) : '0';

			$amount1 = $pubdb->get_one($wherestr, 'SUM(pay_amount) AS amount');
			$income['total'] = $amount1['amount'] ? sprintf('%.2f', $amount1['amount']) : '0';

			$income['month'] += $income['today'];
			$income['total'] += $income['today'];
			$memcachecount += 1;
			setcache($pmemkey, $income, '', 'memcache', 'memcache', 5*60);
		}
		//在线情况
		$pmemkey = md5('online_'.$wherestr);
		$online = getcache($pmemkey);
		if (!$online) {
			$createcount = $pubdb->get_one($wherestr, 'SUM(register_count) AS  register_count, SUM(create_count) AS create_count');
			$online['register'] = $createcount['register_count'] ? $createcount['register_count'] : 0;
			$online['create'] = $createcount['create_count'] ? $createcount['create_count'] : 0;
			$online['createpercent'] = $online['create'] > 0 ? sprintf('%.2f', $online['create']*100/$online['register']) : 0; 
			$onlinecount = $pubdb->get_one(!empty($wherestr) ? $wherestr." AND gdate='$yesterday'" : "gdate='$yesterday'", 'SUM(login_count) AS login_count');
			$online['login'] = $onlinecount['login_count'] ? $onlinecount['login_count'] : 0;
			$memcachecount += 1;
			setcache($pmemkey, $online, '', 'memcache', 'memcache', 10*60);
		}
		//充值排行
		$pmemkey = md5($wherestr.'main_personrank');
		$smemkey = md5($wherestr.'main_serverrank');
		$personrank = getcache($pmemkey);
		$serverrank = getcache($smemkey);
		if (!$personrank) {

			$pubdb->table_name = 'pay_data';
			$wherestr .= !empty($wherestr) ? ' AND dtime_unix>='.strtotime(date('Y-m-d 00:00:00')) :' dtime_unix>='.strtotime(date('Y-m-d 00:00:00'));
			$personrank = $serverrank = array();
			$personrank = $pubdb->select($wherestr, 'sid, username, nickname, cast(SUM(amount) as   decimal(10,   2)) as amount, COUNT(pid) AS pay_num', 20, 'amount DESC', 'username');
			$serverrank = $pubdb->select($wherestr, 'sid, cast(SUM(amount) as   decimal(10,   2)) as amount, COUNT(distinct username) as pay_num', 20, 'amount DESC', 'sid');

			$personrank = json_encode($personrank);
			$serverrank = json_encode($serverrank);
			$memcachecount += 1;
			setcache($pmemkey, $personrank, '', 'memcache', 'memcache', 10*60);
			setcache($smemkey, $serverrank, '', 'memcache', 'memcache', 10*60);
		}

		$sevenlist = getcache('main_sevenlist');
		if (!$sevenlist) {
			$sql = "select from_unixtime(dtime_unix, '%H') as hours, sum(amount)/7 as amount from pay_data where dtime_unix>=".strtotime(date('Y-m-d 00:00:00', strtotime('-7 day')))." and dtime_unix<".strtotime(date('Y-m-d 00:00:00'))." group by hours";
        	$sevenlist = $pubdb->get_list($sql);
			$seven = array();
			$temp = 0;
			foreach($sevenlist as $value) {
				$temp += round($value['amount'], 2);
				$seven[] = array($value['hours'], $temp);
			}
			$sevenlist = json_encode($seven);
			$memcachecount += 1;
			setcache('main_sevenlist', $sevenlist, '', 'memcache', 'memcache', 24*3600);
		}

		$thirtylist = getcache('main_thirtylist');
		if (!$thirtylist) {
			$sql = "select from_unixtime(dtime_unix, '%H') as hours, sum(amount)/30 as amount from pay_data where dtime_unix>=".strtotime(date('Y-m-d 00:00:00', strtotime('-30 day')))." and dtime_unix<".strtotime(date('Y-m-d 00:00:00'))." group by hours";
        	$thirtylist = $pubdb->get_list($sql);
			$thirty = array();
			$temp = 0;
			foreach($thirtylist as $value) {
				$temp += round($value['amount'], 2);
				$thirty[] = array($value['hours'], $temp);
			}
			$thirtylist = json_encode($thirty);
			$memcachecount += 1;
			setcache('main_thirtylist', $thirtylist, '', 'memcache', 'memcache', 24*3600);
		}

		$todaylist = getcache('main_todaylist');
		if (!$todaylist) {
			$sql = "select from_unixtime(dtime_unix, '%H') as hours, sum(amount) as amount from pay_data where dtime_unix>=".strtotime(date('Y-m-d 00:00:00'))." and dtime_unix<".strtotime(date('Y-m-d 23:59:59'))." group by hours";
        	$todaylist = $pubdb->get_list($sql);
			$todaygp = array();
			$temp = 0;
			foreach($todaylist as $value) {
				$temp += round($value['amount'], 2);
				$todaygp[] = array($value['hours'], $temp);
			}
			$todaylist = json_encode($todaygp);
			$memcachecount += 1;
			setcache('main_todaylist', $todaylist, '', 'memcache', 'memcache', 20*65);
		}

		$sevenonline = getcache('main_sevenonline');
		if (!$sevenonline) {
			$sql = "select from_unixtime(online_time, '%H') as hours, sum(online_count)/7 as onlinecount from online where online_time>=".strtotime(date('Y-m-d 00:00:00', strtotime('-7 day')))." and online_time<".strtotime(date('Y-m-d 00:00:00'))." group by hours";
			$sevenonline = $pubdb->get_list($sql);
			$seven = array();
        	$temp = 0;
        	foreach ($sevenonline as $value) {
       			$seven[] = array($value['hours'], intval($value['onlinecount']));
			}
			$sevenonline = json_encode($seven);
			$memcachecount += 1;
			setcache('main_sevenonline', $sevenonline, '', 'memcache', 'memcache', 24*3600);
		}


		$thirtyonline = getcache('main_thirtyonline');
		if (!$thirtyonline) {
			$thirtyonline = getcache('main_thirtyonline');
			$sql = "select from_unixtime(online_time, '%H') as hours, sum(online_count)/30 as onlinecount from online where online_time>=".strtotime(date('Y-m-d 00:00:00', strtotime('-30 day')))." and online_time<".strtotime(date('Y-m-d 00:00:00'))." group by hours";
			$thirtyonline = $pubdb->get_list($sql);
			$thirty = array();
        	$temp = 0;
        	foreach ($thirtyonline as $value) {
       			$thirty[] = array($value['hours'], intval($value['onlinecount']));
			}
			$thirtyonline = json_encode($thirty);
			$memcachecount += 1;
			setcache('main_thirtyonline', $thirtyonline, '', 'memcache', 'memcache', 24*3600);
		}

		$todayonline = getcache('main_todayonline');
		if (!$todayonline) {
			$sql = "select from_unixtime(online_time, '%H') as hours, sum(online_count) as onlinecount from online where online_time>=".strtotime(date('Y-m-d 00:00:00'))." and online_time<".strtotime(date('Y-m-d 23:59:59'))." group by hours";
			$todayonline = $pubdb->get_list($sql);
			$todaygp = array();
			$temp = 0;
			foreach ($todayonline as $value) {
				$todaygp[] = array($value['hours'], intval($value['onlinecount']));
			}
			$todayonline = json_encode($todaygp);
			$memcachecount += 1;
			setcache('main_todayonline', $todayonline, '', 'memcache', 'memcache', 15*60);
		}
		$pubdb->table_name = 'max_online';
		$max_online = $pubdb->get_one();
		include template('manage', 'main');
	}
	/**
	 * 登录实现
	 * 
	 */
	public function login(){
		if (isset($_POST['doSubmit'])){
			$username = isset($_POST['username']) ? trim($_POST['username']) : '';
			$password = isset($_POST['password']) ? trim($_POST['password']) : '';

			$info['username'] = $username;
			$info['password'] = $password;

			//密码错误剩余重试次数
			$timesdb = common::load_model('times_model');
			$rtime = $timesdb->get_one(array('username'=>$username));
			$maxloginfailedtimes = 5;
			if($rtime['times'] > $maxloginfailedtimes) {
				$minute = 15-floor((TIME-$rtime['dateline'])/60);
				if ($minute > 0){
					output_json(1, Lang('wait_15_minutes', array('minute' => $minute)));
				}
			}

			$user_r = $this->userdb->get_one(array('username' => $username), 'userid,username,password,roleid,encrypt,status');
			if (empty($user_r)){
				$info['content'] = Lang('username_no_exist');
				$info['userid']  = 0;
				parent::op_log($info, 'login');
				output_json(1, Lang('username_no_exist'));
			}
			
			if ($user_r['status'] == 1) {
				output_json(1, '用户账号已被冻结');
			}

			$info['userid']  = $user_r['userid'];
			$ip = ip();
			$password = md5(md5($password).$user_r['encrypt']);
			if ($user_r['password'] != $password){

				if($rtime && $rtime['times'] <= $maxloginfailedtimes) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$timesdb->update(array('ip'=>$ip,'times'=>'+=1', 'dateline'=>TIME),array('username'=>$username));
				} else {
					$timesdb->delete(array('username'=>$username));
					$timesdb->insert(array('username'=>$username,'ip'=>$ip, 'dateline'=>TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}

				if ($time === 0){
					$info['content'] = Lang('log_wait_15_minutes');
					$msg    = Lang('wait_15_minutes', array('minute' => 15));
				}else {
					$info['content'] = $msg    = Lang('error_password', array('times'=>$times));
				}
				parent::op_log($info, 'login');

				output_json(1, $msg);
			}
			$timesdb->delete(array('username'=>$username));

			$this->userdb->update("`lastloginip`='$ip', `lastlogintime`='".TIME."',`logintimes`=logintimes+1", array('userid'=>$user_r['userid']));
			$_SESSION['userid'] = $user_r['userid'];
			$_SESSION['roleid'] = $user_r['roleid'];
			$cookie_time = TIME+86400*30;
			if(!$user_r['lang']){
				$user_r['lang'] = 'zh-cn';
			}
			param::set_cookie('username', $username, $cookie_time);
			param::set_cookie('rolename', $rolename, $cookie_time);
			param::set_cookie('userid', $user_r['userid'], $cookie_time);
			param::set_cookie('ho_lang', $user_r['lang'], $cookie_time);

			$info['content'] = Lang('login_success');
			parent::op_log($info, 'login');

			$data['url']    = './'.INDEX;
			output_json(0, Lang('login_success'), $data);
			exit;
		}else {
			include template('manage', 'login');
		}
	}

	/**
	 * 退出登录
	 * 
	 */ 
	public function logout(){
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		param::set_cookie('username','');
		param::set_cookie('userid',0);
		param::set_cookie('ho_lang', '');
		
		header('Location: '.INDEX.'?m=manage&c=index&v=login');
	}
}
