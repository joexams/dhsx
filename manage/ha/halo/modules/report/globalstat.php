<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class globalstat extends admin {
	private $pubdb, $getdb;
	function __construct(){
		parent::__construct();

		$this->pubdb = common::load_model('public_model');
	}
	/**
	 * 关卡进度
	 * @return [type] [description]
	 */
	public function mission(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if (isset($_GET['dogetSubmit'])) {
			$this->getdb = $this->pubdb->set_db($sid);
			if ($this->getdb !==  false){
				$type = isset($_GET['type']) ? intval($_GET['type']) : 0;
				$wherestr = "WHERE b.type='$type' AND is_disable=0 ";
				$this->getdb->table_name = 'player';
				$players = $this->getdb->select('is_tester<>0', 'id');
				$idstr = '';
				if ($players){
					foreach ($players as $key => $player) {
						$ids[] = $player['id'];
					}
					$idstr = implode($ids, ',');
					$wherestr .= " AND a.player_id NOT IN ($idstr)";
				}
				
				$sql = "SELECT a.mission_id, d.name AS town, b.name AS mission, SUM(times) AS pktimes,SUM(failed_challenge) AS pkfailedtimes,COUNT(CASE WHEN is_finished=1 THEN 1 ELSE NULL END) AS finished , COUNT(CASE WHEN is_finished=0 THEN 1 ELSE NULL END) AS notfinished FROM player_mission_record a 
						LEFT JOIN mission b ON a.mission_id=b.id 
						LEFT JOIN mission_section c ON b.mission_section_id=c.id 
						LEFT JOIN town d ON c.town_id=d.id 
						$wherestr GROUP BY b.lock ORDER BY b.lock DESC";

				$data['list'] = $this->getdb->get_list($sql);
				$max = 1;
				foreach ($data['list'] as $key => $value) {
					$max = max($max, $value['pktimes'], $value['pkfailedtimes']);
				}
				$data['max'] = $max;

				output_json(0, '', $data);
			}
			output_json(1, Lang('error'));
		}

		include template('report', 'globalstat_mission');
	}
	/**
	 * vip统计
	 * @return [type] [description]
	 */
	public function vip(){
		if (isset($_GET['doSubmit'])) {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$arrsid = isset($_GET['sid']) ? $_GET['sid'] : array();

			if ($cid > 0 && count($arrsid) > 0){
				$chartData = array();

				$sql = "SELECT vip_level, COUNT(id) AS num FROM player WHERE is_tester=0 AND nickname<>'' AND vip_level>0 GROUP BY vip_level";
				$list = $vipstat = $categories = array();
				$sum = $max = 0;
				foreach ($arrsid as $key => $sid) {
					$sid = intval($sid);
					$this->getdb = $this->pubdb->set_db($sid);
					if ($sid > 0 && $this->getdb !== false){
						$list = $this->getdb->get_list($sql);
						foreach ($list as $key => $value) {
							$chartData['categories'][$value['vip_level']] = $value['vip_level'];
							$y = $vipstat[$value['vip_level']]['y']+intval($value['num']);
							$vipstat[$value['vip_level']] = array(
								'name' => $value['vip_level'],
								'y' => $y,
							);

							if ($y > $max) {
								$max = $y;
							}
							$sum += intval($value['num']);
						}
					}
				}

				if (!empty($vipstat)){

					$chartData['categories'] = array_values($chartData['categories']);
					$chartData['series'][] = array('name' => 'VIP统计', 'data' => array_values($vipstat));

					$chartData['chartOptions'] = array(
						'title' => array('text' => ''),
						'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 1),
						'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=> round($max/4), 'min'=>0, 'max' => $max+10, 'name'=>''),
						'chart' => array('type' => 'column')
					);
				}
				$data['list'] = $chartData;
				$data['sum'] = $sum;
				$data['viplist'] = array_values($vipstat);
				$data['categories'] = $chartData['categories'];
				unset($chartData, $vipstat);
				output_json(0, '', $data);
			}
			output_json(1, Lang('error'));
		}else {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			include template('report', 'globalstat_vip');
		}
	}
	/**
	 * 等级分布
	 * @return [type] [description]
	 */
	public function level(){
		if (isset($_GET['doSubmit'])) {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$arrsid = isset($_GET['sid']) ? $_GET['sid'] : array();
			$startlevel = isset($_GET['startlevel']) ? intval($_GET['startlevel']) : 0;
			$endlevel = isset($_GET['endlevel']) ? intval($_GET['endlevel']) : 0;

			$chartData = array();

			if ($cid > 0 && count($arrsid) > 0){
				$wherest = '';
				$sql = 'SELECT gender,';
				if ($startlevel>0 && $endlevel > 0){
					$wherestr = " AND level>=$startlevel AND level<=$endlevel";
					for($i=$startlevel; $i <= $endlevel; $i++){
						$chartData['categories'][] = $i;
						$sql .= "COUNT(CASE WHEN b.level=$i THEN 1 ELSE NULL END) AS level_$i,";
					}
				}else {
					$j = 1;
					$maxlevel = common::load_config('system','maxlevel');
					for($i=1; $i < $maxlevel; $i+=5){
						$m = 5 * $j;
						$chartData['categories'][] = $i.'~'.$m;
						$sql .= "COUNT(CASE WHEN b.level BETWEEN $i AND $m THEN 1 ELSE NULL END) AS level_$m,";
						$j++;
					}
				}
				$sql = trim($sql, ',');
				$sql .= " FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id LEFT JOIN role c ON b.role_id=c.id WHERE is_tester=0 AND c.lock=0 $wherestr GROUP BY gender";
				$levels = $man = $female = $level =array();
				$max = $sum = 0;
				foreach ($arrsid as $key => $sid) {
					$sid = intval($sid);
					$this->getdb = $this->pubdb->set_db($sid);
					if ($sid > 0 && ($this->getdb !== false)){
						$level = $this->getdb->get_list($sql);
						foreach ($level as $kl => $value) {
							if ($value['gender'] == 0){
								unset($value['gender']);
								foreach ($value as $k => $val) {
									$lv = intval(str_replace('level_', '', $k));
									$lv = ($lv-4).'~'.$lv;
									$man[$k] = array(
										'name' => $lv,
										'y' => $man[$k]['y']+intval($val),
									);

									$levels[$k] = array(
										'name' => $lv,
										'y' => $levels[$k]['y']+intval($val),
									);

									if (($levels[$k]['y']+intval($val)) > $max) {
										$max = $levels[$k]['y']+intval($val);
									}
									$sum += intval($val);
								}
							}else {
								unset($value['gender']);
								foreach ($value as $k => $val) {
									$lv = intval(str_replace('level_', '', $k));
									$lv = ($lv-4).'~'.$lv;
									$female[$k] = array(
										'name' => $lv,
										'y' => $female[$k]['y']+intval($val),
									);

									$levels[$k] = array(
										'name' => $lv,
										'y' => $levels[$k]['y']+intval($val),
									);
									if (($levels[$k]['y']+intval($val))> $max) {
										$max = $levels[$k]['y']+intval($val);
									}
									$sum += intval($val);
								}
							}
						}
					}
				}

				if (!empty($levels)) {
					$chartData['series'][] = array('name' => '等级分布', 'data' => array_values($levels));
				}
				if (!empty($man)) {
					$chartData['series'][] = array('name' => '男性', 'data' => array_values($man));
				}
				if (!empty($female)) {
					$chartData['series'][] = array('name' => '女性', 'data' => array_values($female));
				}

				$chartData['chartOptions'] = array(
					'title' => array('text' => ''),
					'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 2),
					'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=> round($max/4), 'min'=>0, 'max' => $max+10, 'name'=>'')
				);

				if ($startlevel>0 && $endlevel > 0){
					$chartData['chartOptions']['chart'] = array('type' => 'column');
					$chartData['chartOptions']['xAxis']['tickInterval'] = 1;
				}else {
					$data['man'] = array_values($man);
					$data['female'] = array_values($female);
					$data['levels'] = array_values($levels);
					$data['categories'] = $chartData['categories'];
				}

				$data['list'] = $chartData;
				$data['count'] = $max;
				$data['max'] = $max;

				unset($levels, $female, $man, $level, $chartData);
				output_json(0, '', $data);
			}
			output_json(1, Lang('error'));
		}else {
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			include template('report', 'globalstat_level');
		}
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
		$max = $sum = 0;
		$sum = $testerdb->count('', '1');

		$categories = $level = array();
		foreach ($list as $key => $value) {
			$categories[] = $value['max_level'];
			$level[] = intval($value['num']);
			$levels[] = array(
				'name' => $value['max_level'],
				'y' => intval($value['num']),
			);
			if (intval($value['num']) > $max) {
				$max = intval($value['num']);
			}
		}

		$chartData['categories'] = array_values($categories);
		$chartData['series'][] = array('name' => '最高等级统计', 'data' => array_values($levels));

		$chartData['chartOptions'] = array(
			'title' => array('text' => ''),
			'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 1),
			'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=> round($max/4), 'min'=>0, 'max' => $max+10, 'name'=>''),
			'chart' => array('type' => 'column')
		);

		$data['categories'] = $categories;
		$data['list'] = $level;
		$data['sum'] = $sum;
		unset($list, $categories, $level);
		include template('report', 'globalstat_maxlevel');
	}
}