<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class globalstat extends admin {
	function __construct(){
		parent::__construct();
	}
	/**
	 * 关卡进度
	 * @return [type] [description]
	 */
	public function mission(){
		
		include template('report', 'globalstat_mission');
	}
	/**
	 * vip统计
	 * @return [type] [description]
	 */
	public function vip(){

		include template('report', 'globalstat_vip');
	}
	/**
	 * 等级分布
	 * @return [type] [description]
	 */
	public function level(){

		include template('report', 'globalstat_level');
	}
	/**
	 * 最高等级统计
	 * @return [type] [description]
	 */
	public function maxlevel(){
		$testerdb = common::load_model('tester_model');
		$wherestr = parent::check_pf_priv('server');
		$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';
		$list = $testerdb->select($wherestr, 'max_level,COUNT(1) AS num', '', '', 'max_level');
		$servernum = $testerdb->count('', '1');

		$categories = $level = array();
		foreach ($list as $key => $value) {
			$categories[] = intval($value['max_level']);
			$level[] = intval($value['num']);
		}

		$data['categories'] = $categories;
		$data['list'] = $level;
		$data['sum'] = $servernum;
		unset($list, $categories, $level);
		include template('report', 'globalstat_maxlevel');
	}
}