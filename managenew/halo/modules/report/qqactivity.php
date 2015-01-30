<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class qqactivity extends admin {
	private $paydb;
	function __construct(){
		parent::__construct();
	}

	public function lottery() {
		$activitylist = array(
			'1001' => '蓝钻充值',
			'1002' => '年费蓝钻充值',
			'1003' => '黄钻充值',
			'1006' => '11月黄钻充值',
			'1009' => '11月蓝钻充值',
			'1012' => '12月蓝钻充值',
			'1015' => '12月黄钻充值',
			'1019' => '2013年1月黄钻充值',
			'1021' => '2013年1月会员充值',
			'1022' => '2013年1月年费会员充值',
			'1024' => '2013年1月蓝钻充值',
			'1026' => '2013年2月豪华黄钻充值',
			'1028' => '2013年2月豪华蓝钻充值',
			'1029' => '2013年2月会员充值',
			'1031' => '2013年3月蓝钻充值',
			'1032' => '2013年3月会员充值',
			'1033' => '2013年3月豪华黄钻充值',
		);

		if (isset($_GET['dogetSubmit'])) {
			$this->paydb = common::load_model('pay_model');
			$this->paydb->table_name = 'pay_lottery_data';
			$packageid = isset($_GET['packageid']) ? intval($_GET['packageid']) : 0;
			if ($packageid <= 0 || !array_key_exists($packageid, $activitylist))	output_json(1, '活动不存在');

			$paylist = $this->paydb->select(array('packageid' => $packageid), "FROM_UNIXTIME(dateline, '%Y-%m-%d') AS ldate, COUNT(*) AS num", 100, '', 'ldate');
			if (!$paylist)	output_json(1, '');

			$categorys = $datas = array();
			foreach ($paylist as $key => $value) {
				$categorys[$key] = $value['ldate']; 
				$datas[$key] = intval($value['num']); 
			}

			$data['categories'] = $categorys;
			$data['datas'] = $datas;
			output_json(0, '', $data);
		}else {
			include template('report', 'qqactivity_lottery');
		}
	}
}
