<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class log extends admin {
	private $logdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->logdb  = common::load_model('log_model');
		$this->pagesize = 15;
	}

	public function init(){

		/*$this->impdb = common::load_model('public_model');
		$this->impdb->table_name = 'pay_gift_data_super';
		$array = $this->impdb->select();

		$this->impdb->table_name = 'pay_gift_super_no';
		foreach($array as $key => $val) {
			$this->impdb->delete(array('openid'=>$val['openid']));
		}*/
		/*$file = CACHE_PATH.'log/super_compens.php';
		$array = file($file);
		$this->impdb = common::load_model('public_model');
		$num = 1;
		foreach($array as $key => $val) {
			$row = explode("\t", $val);
			if($row[3] != 'openid null') continue;
			$openid = $row[4];
			$payitem = $row[5];
			$this->impdb->table_name = 'pay_gift_data_temp_super';
			$insertarr = $this->impdb->get_one(array('openid'=>$openid, 'payitem'=>$payitem));
			$this->impdb->table_name = 'pay_gift_data_temp_super_no';
			$ret = $this->impdb->insert($insertarr, 1, 1);
			if ($ret>0) $num++;
		}
		echo $num;*/
		
		/*$file = CACHE_PATH.'log/1348884998199595000.result';
		$array = file($file);
		$this->impdb = common::load_model('public_model');
		$this->impdb->table_name = 'pay_gift_data_temp_all';
		$num = 1;
		foreach($array as $key => $val) {
			parse_str($val, $thisarr);
			$pattern = '/([0-9]+)个月/';
			preg_match($pattern, iconv('gb2312', 'utf-8', urldecode($thisarr['PayInfo'])), $matches);
			$payitem = $matches[1] >= 12 ? 1444 : 1443;
			$thisstr = urldecode($thisarr['PortalExtendField']);
			parse_str($thisstr, $thisarr2);
			
			$insertarr['ip'] = $thisarr['LogIP'];
			$insertarr['dateline'] = strtotime(substr($thisarr['TradeTime'], 0, 19));
			$insertarr['billno'] = $thisarr['PortalSerialNo'];
			$insertarr['payitem'] = $payitem;
			$insertarr['providetype']  = $thisarr['PayChannelSubId'];
			$insertarr['token'] = $thisarr2['uni_apptoken'];
			$insertarr['openid'] = $thisarr2['uni_openid'];
			$insertarr['discountid'] = $thisarr2['MPRuleID'];

			$ret = $this->impdb->insert($insertarr, 1, 1);
			if ($ret>0) $num++;
		}
		echo $num;

		
		$file = CACHE_PATH.'log/20120927_mplog.php';
		$array = file($file);
		$this->impdb = common::load_model('public_model');
		$this->impdb->table_name = 'pay_gift_data_temp';
		$num = 1;
		foreach($array as $key => $val) {
			$row = explode("\t", $val);
			if($row[0] != '<?php exit;?>') continue;
			$insertarr = array();
			parse_str($row[4]);
			$insertarr['openid'] = $openid;
			$insertarr['dateline'] = $ts ? $ts : $strtotime($row[1]);
			$insertarr['billno'] = $billno;
			$insertarr['discountid'] = $discountid;
			$payitem = explode('*', $payitem);
			$insertarr['payitem'] = $payitem[0];
			$insertarr['providetype']  = $providetype;
			$insertarr['token'] = $token;
			$insertarr['sig'] = $sig;
			$insertarr['ip'] = $row[5];
			$ret = $this->impdb->insert($insertarr, 1, 1);
			if ($ret>0) $num++;
		}
		echo $num;*/
	}
	/**
	 * 日志列表
	 * 
	 */ 
	public function ajax_list(){
		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$totalrecord = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$op = isset($_GET['op']) ? trim($_GET['op']) : 'operation';

		$wherestr = '';
		switch ($op) {
			case 'login':
				
				if (isset($_GET['username']) && !empty($_GET['username'])){
					$username = trim(safe_replace($_GET['username']));
					$wherestr = !empty($wherestr) ? " AND username LIKE '%".$username."%'" : " username LIKE '%".$username."%'";
				}
				if ($_SESSION['roleid'] > 1) {
                    $wherestr .= !empty($wherestr) ? ' AND ' : '';
                    $wherestr .= 'userid='.$_SESSION['userid'];
                }
				if (isset($_GET['ip']) && !empty($_GET['ip'])){
					$ip = trim(safe_replace($_GET['ip']));
					$wherestr .= !empty($wherestr) ? " AND ip='".$ip."'" : " ip='".$ip."'";
				}
				$this->logdb->set_model('login');
				break;
			case 'source':
				$this->logdb->set_model('source');
				$wherestr = parent::check_pf_priv('server');
				$wherestr = !empty($wherestr) && strpos($wherestr, 'cid') === false ? str_ireplace('where', '', $wherestr) : '';
				$playername = isset($_GET['playername']) ? safe_replace(trim($_GET['playername'])) : '';
				$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
				$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
				$key = isset($_GET['key']) ? safe_replace(trim($_GET['key'])) : '';

				if ($sid > 0) {
					$wherestr .= !empty($wherestr) ? ' AND ' : '';
					$wherestr .= 'sid='.$sid;
				}
				if (!empty($playername)) {
					$wherestr .= !empty($wherestr) ? ' AND ' : '';
					$wherestr .= "playername='$playername' OR playernickname='$playername'";
				}
				if (!empty($key)) {
					$wherestr .= !empty($wherestr) ? ' AND ' : '';
					$wherestr .= "`key`='$key'";
				}
				if ($_SESSION['roleid'] > 3) {
					$wherestr .= !empty($wherestr) ? ' AND ' : '';
					$wherestr .= 'userid='.$_SESSION['userid'];
				}
				break;
			case 'active':
				$this->logdb->set_model('activity');
				$wherestr = parent::check_pf_priv('server');
				$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';
				break;
			case 'error':
				$day = !empty($_GET['day']) ? trim($_GET['day']) : date('Ym');
				$file = CACHE_PATH.'log/'.$day.'_error_log.php';
				$list = array();
				$recordnum = 0;
				if (file_exists($file)){
					$array = file($file);
					if(!empty($array) && is_array($array)) {
						$recordnum = count($array);
						$array = array_reverse($array);
						$offset = $this->pagesize * ($page-1);
						$array = array_slice($array, $offset, $this->pagesize);
						foreach($array as $key => $val) {
							$row = explode("\t", $val);
							if($row[0] != '<?php exit;?>') continue;
							$list[$key]['datetime']     = $row[1];
							$list[$key]['errorno'] = $row[2];
							$list[$key]['errorcontent']  = $row[3];
							$list[$key]['errorfilepath'] = $row[4];
							$list[$key]['errorline'] = $row[5];
						}
					}
				}
				break;
			default:
				if ($_SESSION['roleid'] > 1) {
                    $wherestr .= !empty($wherestr) ? ' AND ' : '';
                    $wherestr .= 'userid='.$_SESSION['userid'];
                }
		}

		if ($op != 'error'){
			$list = $this->logdb->get_list_page($wherestr, '*', 'logid DESC', $page, $this->pagesize);
			if ($recordnum <= 0 || $recordnum != $totalrecord){
				$recordnum = $this->logdb->count($wherestr, 'logid');
			}
		}

		$jsonret['count']  = $recordnum;
		$jsonret['list']   = $list;
		$jsonret['status'] = 0;
		$jsonret['msg']	   = '';

		echo json_encode($jsonret);
	}
	/**
	 * 登录日志
	 * 
	 */ 
	public function login(){
		$op = 'login';
		include template('manage', 'log');
	}
	/**
	 * 操作日志
	 * 
	 */ 
	public function operation(){
		$op = 'operation';
		include template('manage', 'log');
	}
	/**
	 * 运营日志
	 * 
	 */ 
	public function source(){
		$op = 'source';
		$tmpldb = common::load_model('template_model');
		$keylist = $tmpldb->select('', 'key, title');

		include template('manage', 'log');
	}
	/**
	 * 错误日志
	 * 
	 */ 
	public function error(){
		$op = 'error';
		$data['filelist'] = $this->get_log_files(CACHE_PATH.'log/', 'error_log');
		include template('manage', 'log_error');
	}
	/**
	 * 活动日志
	 * 
	 */ 
	public function active(){
		$op = 'active';
		include template('manage', 'log');
	}
	/**
	 * 系统执行日志
	 * 
	 */ 
	public function cron(){
		$op = 'cron';
		include template('manage', 'log');
	}
	/**
	 * 删除日志
	 * 
	 */ 
	public function delete(){
		/*$logid   = intval($_POST['logid']);
		$op      = trim($_POST['op']);

		if ($logid > 0){
			switch ($op) {
				case 'login':
					$this->logdb->set_model('login');
					break;
				case 'source':
					$this->logdb->set_model('source');
					break;
				case 'cron':
					$this->logdb->set_model('cron');
					break;
				default:
					break;
			}
			$status = $this->logdb->delete(array('logid' => $logid));

			if ($status){
				output_json(0, Lang('delete_log_success'));
			}

			output_json(1, Lang('delete_log_error'));
		}

		output_json(1, Lang('log_no_exist'));
		*/
	}
	/**
	 * 读取文件列表
	 * 
	 */ 
	private function get_log_files($logdir = '', $action = 'log') {
		$dir = opendir($logdir);
		$files = array();
		while($entry = readdir($dir)) {
			$files[] = $entry;
		}
		closedir($dir);

		if($files) {
			arsort($files);
			$logfile = $action;
			$logfiles = array();
			$ym = '';
			foreach($files as $file) {
				if(strpos($file, $logfile) !== FALSE) {
					if(substr($file, 0, 6) != $ym) {
						$ym = substr($file, 0, 6);
					}
					$logfiles[] = $ym;
				}
			}
		
			if($logfiles) {
				return $logfiles;
			}
			return array();
		}
		return array();
	}
}
