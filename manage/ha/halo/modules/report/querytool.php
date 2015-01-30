<?php 
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class querytool extends admin {
	private $pubdb;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
	}

	/**
	 * 查询注收比
	 * @return [type] [description]
	 */
	public function regincome() {
		if (isset($_GET['doSubmit'])) {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
			$regdate = isset($_GET['regdate']) ? trim($_GET['regdate']) : '';
			$enddate = isset($_GET['enddate']) && !empty($_GET['enddate']) ? strtotime(trim($_GET['enddate'])) : strtotime(date('Y-m-d'));
			$source = isset($_GET['source']) ? trim($_GET['source']) : '';

			if ($cid < 1 || count($sid) < 1 || empty($regdate)) {
				output_json(1, Lang('error'));
			}
			$wherestr = parent::check_pf_priv('server');
			$wherestr .= !empty($wherestr) ? ' AND ' : '';
			$wherestr .= 'sid IN ('.implode($sid, ',').')';
			$wherestr = str_ireplace('where', '', $wherestr);

			$where = "FROM_UNIXTIME(first_login_time, '%Y-%m-%d')='$regdate'";
			$where .= !empty($source) ? " AND source='$source'" : '';
			
			$this->pubdb->table_name = 'pay_data';
			$paywhere = "dtime_unix>".strtotime($regdate);
			if ($enddate > 0) {
				$enddate += 24*3600;
				$paywhere .=  " AND dtime_unix<$enddate";
			}

			$this->pubdb->table_name = 'servers';
			common::load_model('getdb_model', 0);
			$serverlist = $this->pubdb->select($wherestr, 'sid,db_server,db_root,db_pwd,db_name,server_ver');
			$this->pubdb->table_name = 'pay_data';
			$data['regcount'] = $data['paycount'] = $data['amount'] = 0;
			foreach ($serverlist as $key => $server) {
				if (empty($server['db_server']) || empty($server['db_root']) || empty($server['db_pwd']) || empty($server['db_name'])){
					continue;
				}
				$dbconfig = array(
					'game' => array(
							'hostname' => $server['db_server'],
							'database' => $server['db_name'],
							'username' => $server['db_root'],
							'password' => $server['db_pwd'],
							'tablepre' => '',
							'charset' => 'utf8',
							'type' => 'mysql',
							'debug' => false,
							'pconnect' => 0,
							'autoconnect' => 0
						)
					);
				$getdb = new getdb_model($dbconfig, 'game');
				$getdb->table_name = 'player_trace';
				$reglist = $getdb->select($where, 'player_id');
				$list1 = $list2 = array();
				foreach ($reglist as $key => $value) {
					$list1[$value['player_id']] = 0;
				}
				$paywhere = 'sid='.$server['sid'].''.(!empty($paywhere) ? ' AND '.$paywhere : '');
				$paylist = $this->pubdb->select($paywhere, 'player_id, amount');
				foreach ($paylist as $key => $value) {
					$list2[$value['player_id']] += $value['amount'];
				}

				$lastpaylist = array_intersect_key($list2, $list1);

				$data['regcount'] += count($reglist);
				$data['paycount'] += count($lastpaylist);
				$data['amount'] += array_sum($lastpaylist);
			}

            $data['arpu'] = $data['paycount'] > 0 ? $data['amount'] * 100/$data['paycount'] : 0;
            $data['regincome'] = $data['regcount'] > 0 ? $data['amount'] * 100/$data['regcount'] : 0;
            $data['penetration'] = $data['regcount'] > 0 ? $data['paycount'] * 100/$data['regcount'] : 0;

            $data['arpu'] = sprintf('%.2f%%', $data['arpu']);
            $data['regincome'] = sprintf('%.2f%%', $data['regincome']);
            $data['penetration'] = sprintf('%.2f%%', $data['penetration']);
			output_json(0, '', $data);
		}else {
			include template('report', 'querytool_regincome');
		}
	}
	/**
	 * 元宝存量
	 * @return [type] [description]
	 */
	public function ingotstock() {
		if (isset($_GET['doSubmit'])) {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
			if ($cid < 1 || count($sid) < 1) {
				output_json(1, Lang('error'));
			}
			$wherestr = parent::check_pf_priv('server');
			$wherestr .= !empty($wherestr) ? ' AND ' : '';
			$wherestr .= 'sid IN ('.implode($sid, ',').')';
			$wherestr = str_ireplace('where', '', $wherestr);

			$this->pubdb->table_name = 'pay_data';
			$amount = $this->pubdb->get_one($wherestr, 'SUM(amount) AS amount');
			$data['totalpayingot'] = intval($amount['amount']);
			$data['overpayingot'] = 0;
			$data['overgiveingot'] = 0;

			$this->pubdb->table_name = 'servers';
			common::load_model('getdb_model', 0);
			$serverlist = $this->pubdb->select($wherestr, 'sid,db_server,db_root,db_pwd,db_name,server_ver');
			foreach ($serverlist as $key => $server) {
				if (empty($server['db_server']) || empty($server['db_root']) || empty($server['db_pwd']) || empty($server['db_name'])){
					continue;
				}
				$dbconfig = array(
					'game' => array(
							'hostname' => $server['db_server'],
							'database' => $server['db_name'],
							'username' => $server['db_root'],
							'password' => $server['db_pwd'],
							'tablepre' => '',
							'charset' => 'utf8',
							'type' => 'mysql',
							'debug' => false,
							'pconnect' => 0,
							'autoconnect' => 0
						)
					);
				$getdb = new getdb_model($dbconfig, 'game');
				$getdb->table_name = 'player';
				$testerlist = $getdb->select('is_tester<>0', 'id');
				$tester = array();
				$where = '';
				if ($testerlist) {
					foreach ($testerlist as $pkey => $pval) {
						$tester[] = $pval['id'];
					}
					
					if (count($tester) > 0) {
						$where = 'player_id NOT IN ('.implode($tester, ',').')';
					}
				}
				
				$getdb->table_name = 'player_data';
				$playeramount = $getdb->get_one($where, 'SUM(ingot) AS ingot, SUM(charge_ingot) AS charge_ingot');
				$data['overpayingot'] += intval($playeramount['charge_ingot']);
				$data['overgiveingot'] += intval($playeramount['ingot']);
			}

			output_json(0, '', $data);
		}else {
			include template('report', 'querytool_ingotstock');
		}
	}
}