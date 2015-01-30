<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class qqactivity extends admin {
	private $paydb;
	function __construct(){
		parent::__construct();
	}

	public function lottery() {
		$activitylist = array(
			'UM130703105525492' => '豪华蓝钻活动',
			'UM130329142426527' => '会员坐骑',
			'UM130315170222509' => '蓝钻坐骑',
			'UM13031517012766'  => '黄钻坐骑',
			'UM121128141251192' => '会员活动',
			'UM121012173755347' => '黄钻活动',
			'UM121012173440423' => '蓝钻活动',
		);

		if (isset($_GET['dogetSubmit'])) {
			$this->paydb = common::load_model('pay_model');
			$this->paydb->table_name = 'pay_lottery_data';
			$discountid = isset($_GET['discountid']) ? $_GET['discountid'] : '';
			$starttime = $_GET['starttime']!='' ? strtotime($_GET['starttime']) : 0;
			$endtime = $_GET['endtime']!='' ? strtotime($_GET['endtime']) : time();
			if ($discountid == '' || !array_key_exists($discountid, $activitylist))	output_json(1, '活动不存在...');
			$sql = "select FROM_UNIXTIME(dateline, '%Y-%m-%d') AS ldate, COUNT(*) AS num from pay_lottery_data where discountid='$discountid' and dateline between '$starttime' and '$endtime' group by ldate";
			$paylist = $this->paydb->get_list($sql);
			$charttype = 'line';
			if (!$paylist)	output_json(1, '没有找到数据...');

			$chartData = $categorys = $datas = array();
//			foreach ($paylist as $key => $value) {
//				$categorys[$key] = $value['ldate']; 
//				$datas[$key] = intval($value['num']); 
//			}
			$sum = $max = 0;
			foreach ($paylist as $key => $value) {
							$chartData['categories'][$value['ldate']] = $value['ldate'];
							$y = $vipstat[$value['ldate']]['y']+intval($value['num']);
							$vipstat[$value['ldate']] = array(
								'name' => $value['ldate'],
								'y' => $y,
							);

							if ($y > $max) {
								$max = $y;
							}
							$sum += intval($value['num']);
						}
			$chartData['categories'] = array_values($chartData['categories']);
					$chartData['series'][] = array('name' => $activitylist[$discountid], 'data' => array_values($vipstat));

					$chartData['chartOptions'] = array(
						'title' => array('text' => ''),
						'xAxis' => array('showFirstLabel' => true, 'tickmarkPlacement' => 'on', 'showLastLabel' => true, 'tickInterval' => 1),
						'yAxis' => array('title' => array('enabled' => false), 'dataFormat'=>null, 'tickInterval'=> round($max/4), 'min'=>0, 'max' => $max+10, 'name'=>''),
						'chart' => array('type' => $charttype)
					);
					$data['list'] = $chartData;
			$data['categories'] = $categorys;
			$data['datas'] = $datas;
			output_json(0, '', $data);
		}else {
			include template('report', 'qqactivity_lottery');
		}
	}
}
