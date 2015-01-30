<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class notice extends admin {
	private $noticedb;
	public function __construct(){
		parent::__construct();
		$this->noticedb = common::load_model('notice_model');
	}

	public function init(){

		include template('operation', 'notice');
	}
	/**
	 * 发布公告
	 * @return [type] [description]
	 */
	public function setting(){
		if (isset($_POST['doSubmit'])){
			$type = isset($_POST['type']) ? intval($_POST['type']) : 0;

			$sid    		  = isset($_POST['sid']) && is_array($_POST['sid']) ? $_POST['sid'] : array();
			$info['cid']	  = $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 1;
			$info['content']  = isset($_POST['content']) ? trim($_POST['content']) : '';
			$info['lastdate'] = isset($_POST['lastdate']) ? strtotime($_POST['lastdate']) : 0;
			$info['urllink']  = isset($_POST['urllink']) ? trim($_POST['urllink']) : '';
			$info['pf_id']	  = isset($_POST['pf_id']) ? trim($_POST['pf_id']) : 0;
			$info['dateline'] = time();

			if (!empty($info['content']) && strlen($info['content']) <= 50 && count($sid) > 0){
				$wherestr = parent::check_pf_priv();
				$wherestr .= !empty($wherestr) ? ' AND cid='.$cid.'' : 'cid='.$cid.''; 
				$wherestr .= !empty($wherestr) ? ' AND sid IN ('.implode(',', $sid).')' : 'sid IN ('.implode(',',$sid).')';
				$serverdb = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$wherestr = str_ireplace('where', '', $wherestr);
				$serverlist = $serverdb->select($wherestr, 'sid,name,o_name,api_server,api_port,api_pwd,server_ver');

				if (!empty($info['urllink'])){
					$content = '<a href="'.$info['urllink'].'" target="_blank">'.$info['content'].'</a>';
					$info['md5content'] = md5($content);
				}else {
					$info['md5content'] = md5($info['content']);
					$content = $info['content'];
				}
				$success = $arrsid = array();
				if ($serverlist){
					$i = 0;
					foreach ($serverlist as $key => $server) {
						$version = trim($server['server_ver']);
						$api_admin = common::load_api_class('api_admin', $version);
						if ($api_admin !== false && method_exists($api_admin, 'add_affiche')){
							$api_admin::$SERVER    = $server['api_server'];
							$api_admin::$PORT      = $server['api_port'];
							$api_admin::$ADMIN_PWD = $server['api_pwd'];

							$callback = call_user_func_array(array($api_admin, 'add_affiche'), array($content, $info['pf_id'], $info['lastdate']));
							if ($callback['result'] == 1){
								$arrsid[] = $server['sid'];
								$success[$i]['sid']    = $server['sid'];
								$success[$i]['name']   = $server['name'];
								$success[$i]['o_name'] = $server['o_name']; 
								$i++;
							}
						}
					}
				}
				if (count($success) > 0){
					$data['list'] = $success;
					$info['sids'] = ','.implode(',', $arrsid).',';
					$info['sidseri'] = serialize($success);
					$nid = $this->noticedb->insert($info, true);		
					
					$content['content']  = '发布游戏公告 成功';
					$content['key']      = 'add_affiche';
					$content['sid']      = count($arrsid) > 1 ? 0 : $arrsid[0];
					$content['playerid'] = 0;
					parent::op_log($content, 'source');	
		
					output_json(0, Lang('success'), $data);
				}
			}
			output_json(1, Lang('error'));
 		}
	}
	/**
	 * 公告详情
	 * @return [type] [description]
	 */
	public function ajax_info(){
		$nid = isset($_GET['nid']) ? intval($_GET['nid']) : 0;
		if ($nid > 0){
			$info = $this->noticedb->get_one(array('nid'=>$nid), 'nid, sidseri, sids');
			$data['list'] = unserialize($info['sidseri']);

			output_json(0, '', $data);
		}
		output_json(1, '');
	}
	/**
	 * 公告列表
	 * @return [type] [description]
	 */
	public function ajax_list(){
		$list  = array();
		$recordnum = 0;

		$sid   = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$cid   = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$wherestr = '';
		$wherestr = parent::check_pf_priv('company', $cid, $sid);
		$wherestr = str_ireplace('cid', 'a.cid', $wherestr);
		if ($sid > 0){
			$wherestr .= !empty($wherestr) ? " sids LIKE '%,$sid,%'" : "WHERE sids LIKE '%,$sid,%'";
		}
		if ($cid > 0){
			$wherestr .= !empty($wherestr) ? ' AND a.cid='.$cid.'' : 'WHERE a.cid='.$cid.'';
		}

		$page      = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$pagesize  = 20;
		$page      = max(intval($page), 1);
		$offset    = $pagesize*($page-1);

		$sql = "SELECT nid, pf_id,content,urllink, lastdate, dateline, name FROM {$this->noticedb->db_tablepre}game_notice a
				LEFT JOIN company b ON a.cid=b.cid 
				$wherestr 
				ORDER BY nid DESC 
				LIMIT $offset,$pagesize;";
		$list = $this->noticedb->get_list($sql);
		if ($recordnum <= 0){
			$wherestr = str_ireplace('where', '', $wherestr);
			$wherestr = str_ireplace('a.cid', 'cid', $wherestr);
			$recordnum = $this->noticedb->count($wherestr, 'nid');
		}
		
		$data['ts'] = time();
		$data['list']  = $list;
		$data['count'] = $recordnum;
		output_json(0, '', $data);
	}
	/**
	 * 清除公告
	 * @return [type] [description]
	 */
	public function clear(){
		$nid  = isset($_GET['nid']) ? intval($_GET['nid']) : 0;
		$gsid = isset($_GET['sid']) ? $_GET['sid'] : array();
		if ($nid > 0){
			$notice = $this->noticedb->get_one(array('nid'=>$nid), 'nid, cid, sids, md5content');
			if (!empty($notice['sids'])){
				$sid = trim($notice['sids'], ',');
				$cid = intval($notice['cid']);
				$md5content = $notice['md5content'];

				$wherestr = parent::check_pf_priv();
				$wherestr .= !empty($wherestr) ? ' AND cid='.$cid.'' : 'cid='.$cid.''; 
				$wherestr .= !empty($wherestr) ? ' AND sid IN ('.$sid.')' : 'sid IN ('.$sid.')';

				if (count($gsid) > 0){
					$wherestr .= !empty($wherestr) ? ' AND sid IN ('.implode(',', $gsid).')' : ' sid IN ('.implode(',', $gsid).')';
				}
				
				$serverdb = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$wherestr = str_ireplace('where', '', $wherestr);
				$serverlist = $serverdb->select($wherestr, 'sid,name,o_name,api_server,api_port,api_pwd,server_ver');

				if ($serverlist){
					$i = 0;
					$success = array();
					foreach ($serverlist as $key => $server) {
						$version = trim($server['server_ver']);
						$api_admin = common::load_api_class('api_admin', $version);
						if ($api_admin !== false && method_exists($api_admin, 'get_affiche_list')){
							api_base::$SERVER    = $server['api_server'];
							api_base::$PORT      = $server['api_port'];
							api_base::$ADMIN_PWD = $server['api_pwd'];

							$callback = call_user_func_array(array($api_admin, 'get_affiche_list'), array());

							if (count($callback['affiche_list']) > 0){
								foreach ($callback['affiche_list'] as $key => $value) {
									if ($md5content == md5($value['content'][1])){
										$rtn = call_user_func_array(array($api_admin, 'delete_affiche'), array($value['id']));
										if ($rtn['result'] == 1){
											$arrsid[] = $server['sid'];
											$success[$i]['sid']    = $server['sid'];
											$success[$i]['name']   = $server['name'];
											$i++;
										}
										break;
									}
								}
							}
						}
					}

					if (count($success) > 0){
						$this->noticedb->delete(array('nid'=>$nid));
						output_json(0, Lang('success'));
					}
				}
			}
		}

		output_json(1, Lang('error'));
	}
}
