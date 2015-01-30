<?php 
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class coupon extends admin {
	private $pubdb;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
		$this->extenddb = common::load_model('extend_model');
	}
	/**
	 * 兑换券 新手卡
	 * @return [type] [description]
	 */
	public function init() {
		if (isset($_GET['dogetSubmit'])) {
			$page = isset($_GET['top']) ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$pagesize = 20;

			$wherestr = '';
			$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
			$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			$wherestr = parent::check_pf_priv('server');

			if ($sid > 0) {
				$wherestr .= !empty($wherestr) ? ' AND sid='.$sid : 'sid='.$sid;
			}
			if ($cid > 0) {
				$wherestr .= !empty($wherestr) ? ' AND cid='.$cid : 'cid='.$cid;
			}
			$wherestr = str_ireplace('where', '', $wherestr);
			$this->pubdb->table_name = 'code_batch';
			$list = $this->pubdb->get_list_page($wherestr, '*', 'id DESC', $page, $pagesize);
			if ($recordnum <= 0){
				$recordnum = $this->pubdb->count($wherestr, 'id');
			}
			$data['count'] = $recordnum;
			$data['list']  = $list;
			unset($list);
			output_json(0, '', $data);
		}else {
			include template('report', 'coupon_novice');
		}
	}
	/**
	 * 查看
	 * @return [type] [description]
	 */
	public function show() {
		$data['id'] = isset($_GET['id']) ? intval($_GET['id']) : 0;
		include template('report', 'coupon_novice_show');
	}
	/**
	 * 查看列表
	 * @return [type] [description]
	 */
	public function ajax_show_list() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id > 0) {
			$page = isset($_GET['top']) ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$pagesize = 20;
			$this->pubdb->table_name = 'code';
			$list = $this->pubdb->get_list_page(array('batch_id' => $id), '*', '', $page, $pagesize);
			if ($list && $recordnum <= 0) {
				$recordnum = $this->pubdb->count(array('batch_id' => $id), 'id');
			}

			$data['list'] = $list;
			$data['count'] = $recordnum;
			unset($list);
			output_json(0, Lang('success'), $data);
		}
		output_json(1, Lang('error'));
	}
	
	/**
	 * 新手卡导出
	 * @return [type] [description]
	 */
	public function export() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$sname = isset($_GET['sname']) ? trim($_GET['sname']) : '';
		$name = isset($_GET['name']) ? trim($_GET['name']) : '';
		@Header('Content-type:  application/octet-stream'); 
		@Header('Accept-Ranges:   bytes'); 
		@Header('Content-type: text/plain');   
		@Header('Content-Disposition:attachment;filename='.$sname.'_'.$name.'.txt');
		$this->pubdb->table_name = 'code';
		$this->pubdb->select(array('batch_id' => $id, 'player_id'=> 0), 'id, code');   
		foreach ($list as $key => $value) {
			echo $value['code'].PHP_EOL;
		}
	}
	/**
	 * 新手卡删除
	 */
	public function delete() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id > 0) {
			$this->pubdb->table_name = 'code_batch';
			$rtn = $this->pubdb->delete(array('id' => $id));
			if ($rtn) {
				$this->pubdb->table_name = 'code';
				$this->pubdb->delete(array('batch_id' => $id));

				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}

	/**
	 * 活动兑换券
	 * @return [type] [description]
	 */
	public function active() {
		if (isset($_GET['dogetSubmit'])){
			$cid =  isset($_GET['cid']) && intval($_GET['cid']) > 0 ? intval($_GET['cid']) : 0;
			$sid =isset($_GET['sid']) && intval($_GET['sid']) > 0 ? intval($_GET['sid']) : 0;
			$playername = isset($_GET['playername']) && !empty($_GET['playername']) ? trim($_GET['playername']) : '';
			$table_name = isset($_GET['table_name']) && !empty($_GET['table_name']) ? trim($_GET['table_name']) : '';
			$usetype = isset($_GET['usetype']) && intval($_GET['usetype']) ? intval($_GET['usetype']) : 0;
			$times = isset($_GET['times']) && intval($_GET['times']) ? intval($_GET['times']) : 1;
			$code = isset($_GET['code']) && !empty($_GET['code']) ? trim($_GET['code']) : '';

			$page = isset($_GET['top']) ? intval($_GET['top']) : 1;
			$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

			$wherestr = '';
			$wherestr .= $cid > 0 ? "cid='$cid'" : '';
			$and = !empty($wherestr) ? ' AND ' : '';
			$wherestr .= $sid > 0 ? $and."sid='$sid'" : '';
			$and = !empty($wherestr) ? ' AND ' : '';
			$wherestr .= $times > 0 ? $and."number='$times'" : '';
			$and = !empty($wherestr) ? ' AND ' : '';
			$wherestr .= $usetype == 1 ? $and."ctime>0" : '';
			$wherestr .= $usetype == 2 ? $and."ctime=0" : '';
			$and = !empty($wherestr) ? ' AND ' : '';
			$wherestr .= $code > 0 ? $and."code='$code'" : '';
			$and = !empty($wherestr) ? ' AND ' : '';
			$wherestr .= $playername > 0 ? $and."username='$playername'" : '';

			$this->pubdb->table_name = $table_name;
			$list = $this->pubdb->get_list_page($wherestr, '*', '', $page);
			if ($list && $recordnum < 1) {
				$recordnum = $this->pubdb->count($wherestr, 'id');
			}

			$data['count'] = $recordnum;
			$data['list']  = $list;
			unset($list);
			output_json(0, '', $data);
		}else {
			$memkey = md5('coupon_active_list');
			$activelist = getcache($memkey);
			if (!$activelist) {
			$databases = common::load_config('database', 'default');
			$database = $databases['database'];
			$sql = "SELECT table_name,table_comment FROM INFORMATION_SCHEMA.TABLES  WHERE table_schema='$database' AND table_name LIKE 'code_party_%' order by CREATE_TIME desc;";
			$this->pubdb->query($sql);
			$activelist = $this->pubdb->fetch_array();
			foreach ($activelist as $key => $value) {
				$this->pubdb->table_name = $value['table_name'];
				$cptimes = $this->pubdb->get_one('', 'MAX(number) AS times');
				$activelist[$key]['times'] = $cptimes['times'];
			}
			setcache($memkey, $activelist,  '', 'memcache', 'memcache', 7*3600);
			}
			include template('report', 'coupon_active');
		}
	}

	/**
	 * 添加活动兑换券,创建活动表
	 */
	public function add_active() {
		$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
		$tbl_suffix = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
		$table_comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
		$num = isset($_POST['num']) ? intval($_POST['num']) : 0;
		if (empty($tbl_suffix) || empty($table_comment) || $cid < 1) {
			output_json(1, Lang('args_no_enough'));
		}

		$table_name = 'code_party_'.$tbl_suffix;
		$this->pubdb->db_tablepre = '';
		$tbl_exists =  $this->pubdb->table_exists($table_name);
		if ($tbl_exists) {
			output_json(1, '数据表 '.$table_name.' 已存在，请重新填写！');
		}

		$createsql = "CREATE TABLE `{$table_name}` (
			`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`code`  char(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`cid`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
			`sid`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
			`player_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
			`username`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`nickname`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`ctime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`number`  tinyint(2) UNSIGNED NOT NULL DEFAULT 1 ,
			PRIMARY KEY (`id`),
			UNIQUE INDEX `code` (`code`),
			INDEX `cid_sid_username` (`cid`, `sid`, `username`),
			INDEX `cid_code` (`cid`, `code`) 
		)
		COMMENT='{$table_comment}';";

		$ret = $this->pubdb->query($createsql);
		if ($ret) {
			//直接生成兑换券
			if ($num > 0) {
				$this->import_active($cid, $table_name, $num);
			}
			$memkey = md5('coupon_active_list');
			delcache($memkey);
			
			output_json(0, Lang('success'));
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 追加兑换券
	 */
	public function add_again_active() {
		$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
		$table_name = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
		$num = isset($_POST['num']) ? intval($_POST['num']) : 0;

		$this->import_active($cid, $table_name, $num, 1);
		output_json(0, Lang('success'));
	}
	/**
	 * 导出兑换券
	 * @return [type] [description]
	 */
	public function export_active() {
		$cid =  isset($_GET['cid']) && intval($_GET['cid']) > 0 ? intval($_GET['cid']) : 0;
		$sid =isset($_GET['sid']) && intval($_GET['sid']) > 0 ? intval($_GET['sid']) : 0;
		$playername = isset($_GET['playername']) && !empty($_GET['playername']) ? trim($_GET['playername']) : '';
		$table_name = isset($_GET['table_name']) && !empty($_GET['table_name']) ? trim($_GET['table_name']) : '';
		$usetype = isset($_GET['usetype']) && intval($_GET['usetype']) ? intval($_GET['usetype']) : 0;
		$times = isset($_GET['times']) && intval($_GET['times']) ? intval($_GET['times']) : 1;
		$code = isset($_GET['code']) && intval($_GET['code']) ? intval($_GET['code']) : 0;


		$wherestr = '';
		$wherestr .= $cid > 0 ? "cid='$cid'" : '';
		$and = !empty($wherestr) ? ' AND ' : '';
		$wherestr .= $sid > 0 ? " $and sid='$sid'" : '';
		$and = !empty($wherestr) ? ' AND ' : '';
		$wherestr .= $cid > 0 ? " $and times='$times'" : '';
		$and = !empty($wherestr) ? ' AND ' : '';
		$wherestr .= $usetype == 1 ? $and."ctime>0" : '';
		$wherestr .= $usetype == 2 ? $and."ctime=0" : '';
		$and = !empty($wherestr) ? ' AND ' : '';
		$wherestr .= $code > 0 ? " $and code='$code'" : '';
		$and = !empty($wherestr) ? ' AND ' : '';
		$wherestr .= $playername > 0 ? " $and username='$playername'" : '';

		$this->pubdb->table_name = $table_name;
		$list = $this->pubdb->select($wherestr, 'code');

		$tbl_suffix = str_replace('code_party_', '', $table_name);
		$filename = $tbl_suffix.'_code'.($times > 0 ? '_'.$times : '').'.txt';

		@Header('Content-type:   application/octet-stream'); 
		@Header('Accept-Ranges:   bytes'); 
		@Header('Content-type: text/plain');   
		@Header('Content-Disposition:attachment;filename='.$filename.'');
		foreach ($list as $key => $value) {
			echo $value['code'].PHP_EOL;
		}
	}
	/**
	 * 生成兑换券
	 * @param  int  $cid        [description]
	 * @param  string  $table_name [description]
	 * @param  int  $num        [description]
	 * @param  boolean $flag       [description]
	 * @return [type]              [description]
	 */
	private function import_active($cid, $table_name, $num, $flag = false) {
		if ($cid < 1 || empty($table_name) || $num < 1) {
			output_json(1, Lang('args_no_enough'), $_POST);
		}

		if ($flag) {
			$this->pubdb->db_tablepre = '';
			$tbl_exists =  $this->pubdb->table_exists($table_name);
			if (!$tbl_exists) {
				$data['tbl'] = $tbl_exists;
				output_json(1, '数据表 '.$table_name.' 不存在，无法导入！', $data);
			}
		}

		//直接生成兑换券
		$this->pubdb->table_name = $table_name;
		$max = $this->pubdb->get_one('', 'MAX(number) AS times');
		$info = array();
		$cid = $cid;
		$times = $max ? $max['times'] + 1 : 1;
		$tbl_array = explode('_',$table_name);
		$tbl_suffix = $tbl_array[2];
		$n = ceil($num/5000);
		$remainder = $num % 5000;
		for ($i=1; $i<=$n; $i++) {
			$sql = '';
			$values = array();

			$curnum = $i <= floor($num/5000)  ? 5000 : $remainder;

			for($j=0; $j < $curnum; $j++) {
				$code = md5(random(6)."-".random(6)."-".random(6)."-".random(6)."-".random(6)."-".microtime());
				$code = $tbl_suffix.'_'.$code;
				array_push($values, "($cid, $times, '$code')");
			}
			$sql = "INSERT INTO $table_name(cid, number, code) VALUES".implode($values, ',');
			$this->pubdb->query($sql);
		}
		
		return ;
	}
}
