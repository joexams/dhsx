<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class consume extends admin {
	private $getdb;
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 消费统计
	 * @return [type] [description]
	 */
	public function total(){
		if (isset($_GET['doSubmit'])) {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
			$typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
			$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
			$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime'].' 23:59:59') : 0;
			$start_vip_level = isset($_GET['start_vip_level']) ? intval($_GET['start_vip_level'])  : 0;
			$end_vip_level = isset($_GET['end_vip_level']) ? intval($_GET['end_vip_level'])  : 0;
			$start_level = isset($_GET['start_level']) ? intval($_GET['start_level'])  : 0;
			$end_level = isset($_GET['end_level']) ? intval($_GET['end_level'])  : 0;

			$wherestr = '';
			if ($typeid > 0) {
				$wherestr .= !empty($wherestr) ? " AND type='$typeid'" : "type='$typeid'";
			}
			if ($starttime > 0){
				$wherestr .= !empty($wherestr) ? " AND change_time>'$starttime'" : "change_time>'$starttime'";
			}
			if ($endtime > 0){
				$wherestr .= !empty($wherestr) ? " AND change_time<'$endtime'" : "change_time<'$endtime'";
			}
			if ($start_vip_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND vip_level>='.$start_vip_level.'': ' vip_level>='.$start_vip_level.'';
			}
			if ($end_vip_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND vip_level<='.$end_vip_level.'': ' vip_level<='.$end_vip_level.'';
			}
			if ($start_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND level>='.$start_level.'': ' level>='.$start_level.'';
			}
			if ($end_level > 0){
				$wherestr .= !empty($wherestr) ? ' AND level<='.$end_level.'': ' level<='.$end_level.'';
			}
			$wherestr .= !empty($wherestr) ? ' AND value < 0 AND is_tester=0': 'value < 0 AND is_tester=0';

			$list = array();
			$data['allnum'] = $data['alltotal'] = $data['allingot'] = 0;
			$pubdb = common::load_model('public_model');
			foreach ($sid as $value) {
				$this->getdb = $pubdb->set_db($value);
				if ($this->getdb !== false){
					$this->getdb->table_name  = 'ingot_change_type';
					$data['type'] = $this->getdb->select('', 'id,name');

					if ($start_level > 0 || $end_level > 0){
						if ($typeid > 0) {
							$sql = 'SELECT type, COUNT(DISTINCT a.player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a 
								LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr;
						}else {
							$sql = 'SELECT type, COUNT(DISTINCT a.player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a 
								LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr.' GROUP BY type';
						}
						
						$numsql = 'SELECT COUNT(DISTINCT a.player_id) AS allnum, COUNT(a.id) AS alltotal, SUM(value) AS allingot FROM player_ingot_change_record a 
								LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr;	
					}else {
						if ($typeid > 0) {
							$sql = 'SELECT type, COUNT(DISTINCT player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr;
						}else {
							$sql = 'SELECT type, COUNT(DISTINCT player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr.' GROUP BY type';
						}
						$numsql = 'SELECT COUNT(DISTINCT player_id) AS allnum, COUNT(a.id) AS alltotal, SUM(value) AS allingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr;
					}
					$tmplist = $this->getdb->get_list($sql);
					$list = array_merge($list, $tmplist);
					$allcount = $this->getdb->get_list($numsql);

					$data['allnum'] += $allcount[0]['allnum'];
					$data['alltotal'] += $allcount[0]['alltotal'];
					$data['allingot'] += $allcount[0]['allingot'];
				}
			}
			$alllist = array();
			foreach ($list as $key => $value) {
				if (array_key_exists($value['type'], $list)) {
					$alllist[$value['type']]['num'] += $value['num'];
					$alllist[$value['type']]['total'] += $value['total'];
					$alllist[$value['type']]['ingot'] += $value['ingot'];
				}else {
					$alllist[$value['type']]['num'] = $value['num'];
					$alllist[$value['type']]['total'] = $value['total'];
					$alllist[$value['type']]['ingot'] = $value['ingot'];
				}
				$alllist[$value['type']]['type'] = $value['type'];
			}
			usort($alllist, 'self::cmp');
			$data['list'] = $alllist;

			output_json(0, '', $data);
		}else {
			// $serverdb  = common::load_model('public_model');
			// $serverdb->table_name = 'servers';
			// $server = $serverdb->get_one(array('sid' => 4745), 'db_server,db_root,db_pwd,db_name,server_ver');
			// common::load_model('getdb_model', 0);
			// $dbconfig = array(
			// 	'game' => array(
			// 			'hostname' => $server['db_server'],
			// 			'database' => $server['db_name'],
			// 			'username' => $server['db_root'],
			// 			'password' => $server['db_pwd'],
			// 			'tablepre' => '',
			// 			'charset' => 'utf8',
			// 			'type' => 'mysql',
			// 			'debug' => false,
			// 			'pconnect' => 0,
			// 			'autoconnect' => 0
			// 		)
			// 	);
			// $getdb = new getdb_model($dbconfig, 'game');
			// $getdb->table_name  = 'ingot_change_type';
			// $typelist = $getdb->select('', 'id,name');	
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			include template('report', 'consume_total');
		}
	}

	
	/**
	 * 数组按元宝大小排序
	 * @param  [type] $a [description]
	 * @param  [type] $b [description]
	 * @return [type]    [description]
	 */
	private static function cmp($a, $b)
	{
	    if ($a['ingot'] == $b['ingot']) {
	        return 0;
	    }
	    return ($a['ingot'] < $b['ingot']) ? -1 : 1;
	}
}
