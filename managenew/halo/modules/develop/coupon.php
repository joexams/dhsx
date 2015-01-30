<?php 
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class coupon extends admin {
	private $pubdb;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
		$this->extenddb = common::load_model('extend_model');
	}
/**
	 * 新手卡修改
	 * @return [type] [description]
	 */
	public function add() {
		if (isset($_POST['doSubmit'])) {
			$couponid = isset($_POST['couponid']) ? intval($_POST['couponid']) : 0;
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
			if ($cid > 0 && $sid > 0) {
				$info['cid'] = $cid;
				$info['sid'] = $sid;
				$info['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
				$info['item_id'] = isset($_POST['item_id']) ? intval($_POST['item_id']) : 520;
				$info['item_name'] = isset($_POST['item_name']) ? trim($_POST['item_name']) : '神秘礼包';
				$info['item_val'] = isset($_POST['item_val']) ? intval($_POST['item_val']) : 1;
				$info['juche'] = isset($_POST['juche']) ? intval($_POST['juche']) : 1;
				$info['edate'] = isset($_POST['edate']) ? trim($_POST['edate']) : '9999-01-01';
				$info['num'] = isset($_POST['num']) ? intval($_POST['num']) : 0;
				$editflag = 0;
				$this->pubdb->table_name = 'code_batch';
				if ($couponid > 0) {
					$editflag = 1;
					$rtn = $this->pubdb->update($info, array('id'=>$couponid));
					$couponid = $rtn ? $couponid : 0;
				}else {
					$num = $this->pubdb->count(array('sid' => $sid, 'juche' => 1), 'id');
					if ($num > 0) {
						output_json(1, '该服已发布过支持自动生成兑换券的活动，无法重复发布！');
					}
					$info['ctime'] = date('Y-m-d H:i:s');
					$couponid = $this->pubdb->insert($info, true);
				}

				if ($couponid > 0) {
					$msg                = $editflag ? Lang('edit_success') : Lang('add_success');
					$data['editflag']   = $editflag;
					$data['info']       = $info;
					$data['info']['id'] = $couponid;

					output_json(0, $msg, $data);
				}
			}
			output_json(1, Lang('not_selected_company_or_server'));
		}else {
			$databases = common::load_config('database', 'default_extend');
			$database = $databases['database'];
			$sql = "SELECT table_name,table_comment FROM INFORMATION_SCHEMA.TABLES  WHERE table_schema='$database' AND table_name LIKE 'code_party_%';";
			$this->extenddb->query($sql);
			$activelist = $this->extenddb->fetch_array();
			foreach ($activelist as $key => $value) {
				$this->extenddb->table_name = $value['table_name'];
				$cptimes = $this->extenddb->get_one('', 'MAX(times) AS times');
				$activelist[$key]['times'] = $cptimes['times'];
			}
			
			include template('develop', 'coupon');
		}
	}
}