<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class premium extends admin {
	private $pubdb, $getdb;
	public function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
	}

	public function init(){

	}
	/**
	 * 充值补单
	 * @return [type] [description]
	 */
	public function pay(){
		if (isset($_POST['doSubmit'])){
			$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$playername = isset($_POST['playername']) ? trim($_POST['playername']) : '';
			$ingot = isset($_POST['ingot']) ? intval($_POST['ingot']) : 0;
			$amout = isset($_POST['amout']) ? intval($_POST['amout']) : 0;
			$oid = isset($_POST['oid']) ? trim($_POST['oid']) : '';
			$dtime_unix = isset($_POST['dtime_unix']) && !empty($_POST['dtime_unix']) ? strtotime($_POST['dtime_unix']) : time();

			if ($sid <= 0 || $cid <= 0 || empty($playername) || $ingot <= 0 || $amout <= 0 || $oid <= 0){
				output_json(1, Lang('args_no_enough'));
			}

			$dbflag = $this->set_db($sid);
			if (!$dbflag)  output_json(1, Lang('error'));
			
			$this->getdb->table_name = 'player';
			$player = $this->getdb->get_one(array('username'=>$playername), 'id,username,nickname');
			if (!$player) output_json(1, Lang('player_no_exist'));

			$this->pubdb->table_name = 'pay_data';
			$payrs = $this->pubdb->get_one(array('oid' => $oid, 'sid'=> $sid, 'cid' => $cid), 'player_id, cid, sid, username, nickname, amount, coins');
			
			
		}else {
			include template('operation', 'premium_pay');
		}
	}
	/**
	 * 订单转移
	 * @return [type] [description]
	 */
	public function transfer(){
		if (isset($_POST['doSubmit'])){
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$source_sid = isset($_POST['source_sid']) ? intval($_POST['source_sid']) : 0;
			$target_sid = isset($_POST['target_sid']) ? intval($_POST['target_sid']) : 0;
			$playername = isset($_POST['playername']) ? trim($_POST['playername']) : 0;
			$oid 		= isset($_POST['oid']) ? trim($_POST['oid']) : '';
			if ($cid <= 0 || $source_sid <= 0 || $target_sid <= 0 || empty($playername) || empty($oid)){
				output_json(1, Lang('error'));
			}
			$this->pubdb->table_name = 'pay_data';
			$payrs = $this->pubdb->get_one(array('oid' => $oid, 'sid'=> $source_sid, 'cid' => $cid), 'player_id, cid, sid, username, nickname, amount, coins');
			if (!$payrs) output_json(1, Lang('order_no_exist'));
			if ($payrs['username'] == $playername && $source_sid == $target_sid) output_json(1, Lang('this_order_on_change_should_not_transfer'));
			$loadflag = $this->set_db($target_sid);

			if ($loadflag === false) output_json(1, Lang('remote_db_unconnect'));
			$this->getdb->table_name = 'player';
			$userrs = $this->getdb->get_one(array('username'=>$playername));
			if (!$userrs) output_json(1, Lang('transfer_server_no_exist_this_player'));

		}else {
			include template('operation', 'premium_transfer');
		}
	}
	/**
	 * 设置远程数据库连接
	 * @param [type] $sid [description]
	 */
	private function set_db($sid){
		$sid = intval($sid);
		if ($sid > 0){
			parent::check_pf_priv('server', 0, $sid);
			
			$serverdb  = common::load_model('public_model');
			$serverdb->table_name = 'servers';
			$server = $serverdb->get_one(array('sid' => $sid), 'db_server,db_root,db_pwd,db_name');
			if (!empty($server['db_server']) && !empty($server['db_root']) && !empty($server['db_pwd']) && !empty($server['db_name'])){
				common::load_model('getdb_model', 0);
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
				$this->getdb = new getdb_model($dbconfig, 'game');
				return true;
			}
		}
		return false;
	}
}