<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class interactive extends admin {
	private $pubdb, $getdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
		$this->pagesize = 20;
	}

	public function init(){

	}
	/**
	 * 玩家反馈
	 * 
	 */ 
	public function gm(){

		include template('operation', 'player_bug');
	}
	/**
	 * 测试号 
	 */ 
	public function tester(){

		include template('operation', 'tester');
	}
	/**
	 * 物品申请
	 * @return [type] [description]
	 */
	public function itemapply() {
		$islimit = true;
		if ($_SESSION['userid'] == 90 || $_SESSION['roleid'] <= 3) {
			$islimit = false;
		}
		include template('operation', 'itemapply');
	}
	/**
	 * 盗号找回
	 * @return [type] [description]
	 */
	public function retrieve() {
		if (isset($_POST['doSubmit'])) {
			$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			if ($sid > 0 && $id > 0) {
				parent::check_pf_priv('server', 0, $sid);
				$info['sid'] = $sid;
				$info['player_id'] = $id;
				$info['userid'] = intval($_SESSION['userid']);
				$info['username'] = param::get_cookie('username');
				$info['dateline'] = time();
				$item = array();
				if (isset($_POST['soul'])) {
					$info['key'] = 'soul';
					$_POST['soul'] = ext_stripslashes($_POST['soul']);
					foreach ($_POST['soul'] as $key => $value) {
						$item[] = json_decode($value, true);
					}
				}
				if (isset($_POST['item'])) {
					$info['key'] = 'item';
					$_POST['item'] = ext_stripslashes($_POST['item']);
					foreach ($_POST['item'] as $key => $value) {
						$item[] = json_decode($value, true);
					}
				}
				if (isset($_POST['fate'])) {
					$info['key'] = 'fate';
					$_POST['fate'] = ext_stripslashes($_POST['fate']);
					foreach ($_POST['fate'] as $key => $value) {
						$item[] = json_decode($value, true);
					}
				}
				$info['data'] = serialize($item);

				$info['content'] = isset($_POST['content']) ? trim($_POST['content']) : '';
				$info['playername'] = isset($_POST['playername']) ? trim($_POST['playername']) : '';
				$info['nickname'] = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';

				$serverdb  = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$server = $serverdb->get_one(array('sid' => $sid), 'sid,cid');
				$info['cid'] = $server['cid'];
				unset($server);

				$retrievedb = common::load_model('retrieve_model');
				$retid = $retrievedb->insert($info, true);
				if ($retid > 0) {
					output_json(0, Lang('success'));
				}
			}
			output_json(1, Lang('error'));
		}else {
			$islimit = true;
        	if ($_SESSION['userid'] == 90 || $_SESSION['roleid'] <= 3) {
            	$islimit = false;
        	}
			include template('operation', 'retrieve');
		}
	}
	/**
	 * 被盗找回列表
	 * @return [type] [description]
	 */
	public function ajax_retrieve_list() {
		$page = isset($_GET['top']) ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$wherestr = parent::check_pf_priv('server');
		$key = isset($_GET['key']) ? trim($_GET['key']) : '';
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

		$userid = $_SESSION['userid'];
		$roleid = $_SESSION['roleid'];
		if ($roleid > 3) {
			$wherestr .= !empty($wherestr) ? " AND userid='$userid'" : " userid='$userid'";
		}
		if (!empty($key)) {
			$wherestr .= !empty($wherestr) ? " AND `key`='$key'" : "`key`='$key'";
		}
		if ($cid > 0) {
			$wherestr .= !empty($wherestr) ? " AND cid='$cid'" : "cid='$cid'";
		}
		if ($sid > 0) {
			$wherestr .= !empty($wherestr) ? " AND sid='$sid'" : "sid='$sid'";
		}
		if ($status > 0) {
			$wherestr .= !empty($wherestr) ? " AND status='$status'" : "status='$status'";
		} 
		$wherestr = str_ireplace('where', '', $wherestr);
		$retrievedb = common::load_model('retrieve_model');
		$list = $retrievedb->get_list_page($wherestr, '*', 'id DESC', $page, $this->pagesize);
		if ($recordnum <= 0){
			$recordnum = $retrievedb->count($wherestr, 'id');
		}
		foreach ($list as $key => $value) {
			$temp = array();
			$list[$key]['data'] = array_merge($temp, unserialize($value['data']));
		}
		$data['count'] = $recordnum;
		$data['list'] = $list;
		unset($list);

		output_json(0, '', $data);
	}
	/**
	 * 被盗号找回撤销
	 * @return [type] [description]
	 */
	public function delete_retrieve() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id > 0) {
			$retrievedb = common::load_model('retrieve_model');
			$rtn = $retrievedb->delete(array('id' => $id));
			if ($rtn) {
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 审批撤销重新审批
	 * @return [type] [description]
	 */
	public function again_retrieve() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id > 0) {
			$retrievedb = common::load_model('retrieve_model');
			$rtn = $retrievedb->update(array('status'=>1), array('id' => $id));
			if ($rtn) {
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 被盗找回审批
	 * @return [type] [description]
	 */
	public function check_retrieve() {
		if (isset($_POST['doSubmit'])) {
			$retid = isset($_POST['retid']) && !empty($_POST['retid']) ? $_POST['retid'] : array();
			$checktype = isset($_POST['checktype']) ? intval($_POST['checktype']) : 0;
			if (count($retid) > 0 && $checktype > 1) {
				$wherestr = parent::check_pf_priv('server');
				$retids = implode($retid, ',');
				$wherestr .= !empty($wherestr) ? " AND id IN ($retids)" : " id IN ($retids)";
				$retrievedb = common::load_model('retrieve_model');
				
				if ($checktype == 2) {
					$wherestr = str_ireplace('where', '', $wherestr);
					$list = $retrievedb->select($wherestr);

					$serverdb  = common::load_model('public_model');
					$serverdb->table_name = 'servers';
					$num = 0;
					foreach ($list as $key => $value) {
						if ($value['sid'] <= 0 || $value['player_id'] <= 0) {
							continue;
						}

						$server = $serverdb->get_one(array('sid' => $value['sid']), 'name,o_name,api_server,api_port,api_pwd,server_ver');
						if (empty($server['api_server']) || empty($server['api_port']) || empty($server['api_pwd']) || empty($server['server_ver'])) {
							continue;
						}
							
						$version = trim($server['server_ver']);
						$api_admin = common::load_api_class('api_admin', $version);
						if ($api_admin !== false && method_exists($api_admin, 'add_player_super_gift')){
							$api_admin::$SERVER    = $server['api_server'];
							$api_admin::$PORT      = $server['api_port'];
							$api_admin::$ADMIN_PWD = $server['api_pwd'];

							$item_list = $fate_list = $soul_list = $loginfo = array();
							if ($value['key'] == 'item') {
								$item_list = unserialize($value['data']);
								foreach ($item_list as $ikey => $val) {
									$loginfo[] = $item_list[$ikey]['id'].' = '.$item_list[$ikey]['name'];
									unset($item_list[$ikey]['id'], $item_list[$ikey]['name']);
								}
							}
							if ($value['key'] == 'soul') {
								$soul_list = unserialize($value['data']);
								foreach ($soul_list as $ikey => $val) {
									$loginfo[] = $soul_list[$ikey]['id'].' = '.$soul_list[$ikey]['name'];
									unset($soul_list[$ikey]['id'], $soul_list[$ikey]['name']);
								}
							}
							if ($value['key'] == 'fate') {
								$fate_list = unserialize($value['data']);
								foreach ($fate_list as $ikey => $val) {
									$loginfo[] = $fate_list[$ikey]['id'].' = '.$fate_list[$ikey]['name'];
									unset($fate_list[$ikey]['id'], $fate_list[$ikey]['name']);
								}
							}

							$rtn = $api_admin::add_player_super_gift($value['player_id'], common::load_config('system', 'gift_type'), 0, 0, 0, 0, common::load_config('system', 'gift_id'), Lang('retrieve_message'), $item_list, $fate_list, $soul_list);
							if ($rtn['result'] == 1) {
								$retrievedb->update(array('status'=>2), array('id' => $value['id']));
								$num += 1;

								$content['playername']     = $value['playername'];
								$content['playernickname'] = $value['nickname'];
								$content['content']  = Lang('log_op_reason').$value['content'];
								$content['key']      = 'retrieve_'.$value['key'];
								$content['sid']      = $value['sid'];
								$content['playerid'] = $value['player_id'];
								$content['content'] = Lang($content['key']).' '.Lang('success').PHP_EOL
													.$content['content'].PHP_EOL
													.implode($loginfo, '，');
								parent::op_log($content, 'source');
							}
						}
					}
					if ($num > 0) {
						output_json(0, Lang('success'), $data);
					}
				}else if ($checktype > 2) {
					$rtn = $retrievedb->update(array('status'=>$checktype), $wherestr);
					if ($rtn) {
						output_json(0, Lang('success'));
					}
				}	
				
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 添加物品申请
	 */
	public function add_itemapply() {
		if (!isset($_POST['doSubmit'])) {
			output_json(1, '');
		}

		$cid = isset($_POST['cid']) && intval($_POST['cid']) > 0 ? intval($_POST['cid']) : 0;
		$sid = isset($_POST['sid']) && intval($_POST['sid']) > 0 ? intval($_POST['sid']) : 0;
		$type = isset($_POST['type']) && !empty($_POST['type']) ? trim($_POST['type']) : '';
		$playername = isset($_POST['playername']) && !empty($_POST['playername']) ? trim($_POST['playername']) : '';
		if ($cid > 0 && $sid > 0 && !empty($type) && !empty($playername)) {
			$serverdb  = common::load_model('public_model');
			$serverdb->table_name = 'servers';
			$server = $serverdb->get_one(array('sid' => $sid), 'name,o_name,api_server,api_port,api_pwd,server_ver');
			if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])){
				$version = trim($server['server_ver']);
				$api_admin = common::load_api_class('api_admin', $version);
				if ($api_admin !== false && method_exists($api_admin, $type)){
					$api_admin::$SERVER    = $server['api_server'];
					$api_admin::$PORT      = $server['api_port'];
					$api_admin::$ADMIN_PWD = $server['api_pwd'];

					$player = $api_admin::find_player_by_username($playername);
					if ($player['result'] == 1){
						$info['player_id'] = $player['player_id'];
						$info['player_name'] = $playername;
					}else {
						output_json(1, Lang('player_no_exist'));
					}
				}else {
					output_json(1, Lang('server_api_no_exist'));
				}
			}else {
				output_json(1, Lang('server_no_exist'));
			}


			$tmpldb = common::load_model('template_model');
			$keyrs = $tmpldb->get_one(array('key'=>$type), 'key,args');
			if (!$keyrs) {
				output_json(1, '', Lang('template_no_exist'));
			}
			$args = unserialize($keyrs['args']);
			$keyvalue = array();
			foreach ($args as $key => $value) {
				if (array_key_exists($value['arg'], $_POST)) {
					$keyvalue[$value['arg']] = $_POST[$value['arg']];
					$info['content'] .= $value['tips'].'：'.$_POST[$value['arg']].PHP_EOL;
				}
			}

			$info['cid'] = $cid;
			$info['sid'] = $sid;
			$info['key'] = $type;
			$info['values'] = serialize($keyvalue);
			$info['userid'] = param::get_cookie('userid');
			$info['username'] = param::get_cookie('username');
			$info['case_content'] = isset($_POST['case_content']) ? trim($_POST['case_content']) : '';
			$info['dateline'] = time();
			$info['reply_content'] = '';
			$info['status'] = 1;

			$itemapplydb = common::load_model('itemapply_model');
			$aid = $itemapplydb->insert($info, true);
			$msg = $aid > 0 ? Lang('success') : Lang('error');
			$data['info'] = $info;
			output_json(0, $msg, $data);
		}
		output_json(1, '');
	}
	/**
	 * 物品申请审批
	 * @return [type] [description]
	 */
	public function check_itemapply() {
		if (!isset($_POST['doSubmit'])) {
			output_json(1, Lang('error'));
		}

		$aid = isset($_POST['aid']) && !empty($_POST['aid']) ? $_POST['aid'] : array();
		$checktype = isset($_POST['checktype']) ? intval($_POST['checktype']) : 0;
		if (count($aid) <= 0 || $checktype < 1) {
			output_json(1, Lang('error'));
		}

		$wherestr = parent::check_pf_priv('server');
		$aids = implode($aid, ',');
		$wherestr .= !empty($wherestr) ? " AND aid IN ($aids)" : " aid IN ($aids)";
		$itemapplydb = common::load_model('itemapply_model');

		if ($checktype == 2) {
			$wherestr = str_ireplace('where', '', $wherestr);
			$list = $itemapplydb->select($wherestr);

			$serverdb  = common::load_model('public_model');
			$serverdb->table_name = 'servers';
			$num = 0;

			foreach ($list as $key => $value) {
				if ($value['sid'] <= 0 || empty($value['key']) || $value['player_id'] <= 0) {
					continue;
				}
				$server = $serverdb->get_one(array('sid' => $value['sid']), 'name,o_name,api_server,api_port,api_pwd,server_ver');
				if (empty($server['api_server']) || empty($server['api_port']) || empty($server['api_pwd']) || empty($server['server_ver'])){
					continue;
				}
				$version = trim($server['server_ver']);
				$api_admin = common::load_api_class('api_admin', $version);
				if ($api_admin == false && !method_exists($api_admin, $value['key'])){
					continue;
				}
				$args = $loginfo = array();
				$api_admin::$SERVER    = $server['api_server'];
				$api_admin::$PORT      = $server['api_port'];
				$api_admin::$ADMIN_PWD = $server['api_pwd'];

				$args[] = $value['player_id'];
				$targs = unserialize($value['values']);
				foreach ($targs as $tkey => $val) {
					$args[] = $val;
					if (is_array($val)) {
						$loginfo[] = $tkey.' = '.array2string($val);
					}else {
						$loginfo[] = $tkey.' = '.$val;
					}
				}
				$callback = call_user_func_array(array($api_admin, $value['key']), $args);
				if ($callback['result'] == 1){
					$itemapplydb->update(array('status'=>2), array('aid'=>$value['aid']));
					$num += 1;

					$player = $api_admin::get_nickname_by_username($value['player_name']);
					$content['playername']     = $value['player_name'];
					$content['playernickname'] = $player['nickname'][1];
					$content['content']  = Lang('log_op_reason').$value['case_content'];
					$content['key']      = $value['key'];
					$content['sid']      = $value['sid'];
					$content['playerid'] = $value['player_id'];
					$content['content'] = '物品申请 '.Lang($value['key']).' '.Lang('success').PHP_EOL
										.$content['content'].PHP_EOL
										.implode($loginfo, '，');
					parent::op_log($content, 'source');
				}
			}
			if ($num > 0) {
				output_json(0, Lang('success'));
			}

		}else {
			$rtn = $itemapplydb->update(array('status'=>$checktype), $wherestr);
			if ($rtn) {
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 物品申请列表
	 * @return [type] [description]
	 */
	public function ajax_itemapply_list() {
		$itemapplydb = common::load_model('itemapply_model');
		$wherestr = parent::check_pf_priv('server');

		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$cid = isset($_GET['cid']) && intval($_GET['cid']) > 0 ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) && intval($_GET['sid']) > 0 ? intval($_GET['sid']) : 0;
		$key = isset($_GET['type']) && !empty($_GET['type']) ? trim($_GET['type']) : '';
		$status = isset($_GET['status']) && intval($_GET['status']) > 0 ? intval($_GET['status']) : 0;
		$keyword = isset($_GET['keyword']) && !empty($_GET['keyword']) ? trim($_GET['keyword']) : '';
		$userid = $_SESSION['userid'];
		$roleid = $_SESSION['roleid'];
		if ($roleid > 3) {
			$wherestr .= !empty($wherestr) ? " AND userid='$userid'" : " userid='$userid'";
		}
		if ($cid > 0) {
			$wherestr .= !empty($wherestr) ? " AND cid='$cid'" : " cid='$cid'";
		}
		if ($sid > 0) {
			$wherestr .= !empty($wherestr) ? " AND sid='$sid'" : " sid='$sid'";
		}
		if (!empty($key)) {
			$wherestr .= !empty($wherestr) ? " AND `key`='$key'" : " `key`='$key'";
		}
		if ($status > 0) {
			$wherestr .= !empty($wherestr) ? " AND status='$status'" : " status='$status'";
		}

		if (!empty($keyword)) {
			$wherestr .= !empty($wherestr) ? " AND case_content LIKE '%$keyword%'" : " case_content LIKE '%$keyword%'";
		}
		
		$wherestr = !empty($wherestr) ? str_ireplace('where', '', $wherestr) : '';
		$list = $itemapplydb->get_list_page($wherestr, '*', '', $page);

		if ($recordnum <= 0) {
			$recordnum = $itemapplydb->count($wherestr, 'aid');
		}
		$data['list'] = $list;
		$data['count'] = $recordnum;

		unset($list);
		output_json(0, '', $data);
	}
	/**
	 * 玩家反馈列表
	 * 
	 */ 
	public function ajax_bug_list(){
		$sid   = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$cid   = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		
		if ($sid > 0){
			$wherestr = parent::check_pf_priv('server', $cid, $sid);
		}else if ($cid > 0){
			$wherestr = parent::check_pf_priv('company', $cid, $sid);
		}
		$playername = isset($_GET['playername']) ? trim(safe_replace($_GET['playername'])) : '';
		$type		= isset($_GET['type']) ? intval($_GET['type']) : 0;
		$status		= isset($_GET['status']) ? intval($_GET['status']) : 0;

		if (!empty($playername)){
			$wherestr  .= !empty($wherestr) ? " AND username LIKE '%$playername%'": "username LIKE '%$playername%%'";
		}
		if ($status != 2){
			$wherestr  .= !empty($wherestr) ? " AND status='$status'": "status='$status'";
		}
		
		
		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$wherestr = str_ireplace('where', '', $wherestr);
		$this->pubdb->table_name = 'gm_bug';
		$list = $this->pubdb->get_list_page($wherestr, '*', 'id DESC', $page, $this->pagesize);
		if ($recordnum <= 0){
			$recordnum = $this->pubdb->count($wherestr, 'id');
		}
		
		if ($list){
			$arrsid = array();
			foreach ($list as $key => $svalue) {
				$arrsid[$key] = $svalue['sid'];
			}
			$sids = implode($arrsid, ',');
			$this->pubdb->table_name = 'servers';
			$slist = $this->pubdb->select('sid IN ('.$sids.')', 'sid,name,o_name');
			foreach ($slist as $key => $value) {
				$skey = array_search($value['sid'], $arrsid);
				$list[$skey]['server_name']   = $value['name'];
				$list[$skey]['server_o_name'] = $value['o_name'];
			}
		}
		$data['count'] = $recordnum;
		$data['list'] = $list;

		output_json(0, '', $data);
	}
	/**
	 * 删除
	 * 
	 */ 
	public function delete_bug(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id > 0){
			$this->pubdb->table_name = 'gm_bug';
			$rtn = $this->pubdb->delete(array('id'=>$id));
			if ($rtn){
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 屏蔽
	 * 
	 */ 
	public function screen_bug(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$flag = isset($_GET['flag']) ? intval($_GET['flag']) : 2;
		if ($id > 0 && $flag < 2){
			$this->pubdb->table_name = 'gm_bug';
			$rtn = $this->pubdb->update(array('status'=>$flag), array('id'=>$id));
			if ($rtn){
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 回复
	 * 
	 */ 
	public function reply_bug(){
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

		if (isset($_POST['doSubmit']) && $id > 0){
			$info['reply_content'] = isset($_POST['reply_content']) ? trim(safe_replace($_POST['reply_content'])) : '';
			$info['reply_user']    = param::get_cookie('username');
			$info['reply_time']    = time();
			$info['status']        = 1;
			if (!empty($info['reply_content'])) {
				$this->pubdb->table_name = 'gm_bug';
				$rtn = $this->pubdb->update($info, array('id'=>$id));
				if ($rtn){
					$data['info'] = $info;
					output_json(0, Lang('success'), $data);
				}
			}
		}

		output_json(1, Lang('error'));
	}
	/**
	 * 刷新测试号
	 * 
	 */ 
	public function tester_refresh(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		if ($sid){
			$dbflag = $this->set_db($sid);
			if ($dbflag){
				sleep(0.5);
				$this->getdb->table_name = 'player';
				$list = $this->getdb->select('is_tester=1', 'id,username, nickname');
				if ($list){
					$info['testers'] = serialize($list);
					$info['cid']	 = $cid;
					$info['sid']     = $sid;
					$this->getdb->table_name = 'player';
					$list = $this->getdb->select('is_tester=1', 'id,username, nickname');
					$this->pubdb->table_name = 'ho_pf_game_tester';
					//更新最高级
					$sql = 'SELECT MAX(level) AS num FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id';
					$info['max_level'] = $this->getdb->get_count($sql);

					//用replace into更新
					$rtn = $this->pubdb->insert($info, false, true);
					if ($rtn){
						$data['list'] = $list;
						output_json(0, Lang('success'), $data);
					}
				}
				unset($list);
				output_json(1, Lang('no_exists'));
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 测试号列表
	 * @return [type] [description]
	 */
	public function ajax_tester_list(){
		$sid = isset($_GET['sid']) && !empty($_GET['sid']) ? trim($_GET['sid']) : '';
		$cid = isset($_GET['sid']) && !empty($_GET['cid']) ? trim($_GET['cid']) : ''; 

		$arrsid = explode(',', $sid);
		$arrcid = explode(',', $cid);
		$wherestr = '';
		$list = array();
		if (count($arrsid) > 0 && count($arrcid) > 0){
			foreach ($arrsid as $key => $value) {
				$svalue = intval($value);
				$cvalue = intval($arrcid[$key]);
				if ($svalue <=0 || $cvalue <= 0){
					continue;
				}
				$wherestr .= !empty($wherestr) ? ' OR (cid='.$cvalue.' AND sid='.$svalue.')' :  ' (cid='.$cvalue.' AND sid='.$svalue.')';
			}
			$this->pubdb->table_name = 'ho_pf_game_tester';
			$list = $this->pubdb->select($wherestr);
			foreach ($list as $key => $value) {
				$list[$key]['testers'] = unserialize($value['testers']);
			}
		}
		$data['list'] = $list;

		output_json(0, '', $data);
	}
	
	/**
	 * 设置远程数据库连接
	 * 
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
