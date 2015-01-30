<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class server extends admin {
	private $mergedb, $pagesize;
	public function __construct(){
		parent::__construct();
		$this->pagesize = 15;
	}
	public function init() {

	}
	/**
	 * 开服排期
	 * @return [type] [description]
	 */
	public function open() {
		if (isset($_POST['doSubmit'])){

			
		}else {
			include template('operation', 'server_open');
		}
	}
	/**
	 * 合服排期
	 * @return [type] [description]
	 */
	public function combine() {
		$this->mergedb = common::load_model('server_merge_model');
		if (isset($_POST['doSubmit'])) {
			$cid = isset($_POST['cid']) && intval($_POST['cid']) > 0 ? intval($_POST['cid']) : 0;
			$sid = isset($_POST['sid']) && !empty($_POST['sid']) ? $_POST['sid'] : array();
			if ($cid <=0 && count($sid) < 1){
				output_json(1, Lang('error'));
			}
			$combineid = isset($_POST['combineid']) && intval($_POST['combineid']) > 0 ? intval($_POST['combineid']) : 0;
			$info['cid'] = $cid;
			$info['sids'] = ','.implode($sid, ',').',';
			$info['opendate'] = isset($_POST['opendate']) && !empty($_POST['opendate']) ? strtotime(trim($_POST['opendate'])) : 0;
			$info['userid'] = param::get_cookie('userid');
			$info['username'] =  param::get_cookie('username');
			$info['dateline'] = time();
			$info['content'] = isset($_POST['content']) && !empty($_POST['content']) ? trim($_POST['content']) : '';
			$editflag = 0;

			if ($combineid > 0){
				$editflag = 1;
				$rtn = $this->mergedb->update($info, array('id' => $combineid));
				$id = $rtn ? $combineid : 0;
			}else {
				$id = $this->mergedb->insert($info, true);

				//增加的时候，同时添加一条开服计划
				if ($id > 0) {
					$pubdb = common::load_model('public_model');
					$pubdb->table_name = 'servers';
					$serverlist = $pubdb->select("sid IN (".implode($sid, ',').")", 'name,open_date,open_date_old', '', 'open_date ASC');
					foreach ($serverlist as $key => $value) {
						$odate[] = $value['open_date_old'] != '0000-00-00 00:00:00' ? $value['open_date_old'] : $value['open_date'];//如果查旧的开服时间判断是否之前合过
						if(strpos($value['name'],'_',0)){
							$s = explode('_',$value['name']);
							$ss = strtoupper($s[1]);
						}else{
							$ss = strtoupper($value['name']);
						}
						$sname .= $sname ? ' + '.$ss : $ss ;
					}
					
					$serverinfo = array(
						'cid' => $cid,
						'o_name' => $sname,
						'open_date' => trim($_POST['opendate']),
						'open' => 0,
						'private' => 0,
						'test' => 0,
						'first_pay_act' => 1,
						'level_act' => 0,
						'mission_act' => 0,
						'new_card_act' => 0,
						'is_combined' => 1,
						'open_date_old' => min($odate)
					);
					$serverid = $pubdb->insert($serverinfo, true);
					$data['info']['combined_to'] = $serverid;
				}
			}
			$data['editflag'] = $editflag;
			$msg = $editflag ? Lang('edit_success') : Lang('add_success');
			if ($id > 0){
				$data['info']['id'] = $id; 
				$data['info']['sids'] = $info['sids']; 
				$data['info']['cid'] = $cid; 
				$data['info']['opendate'] = $info['opendate']; 
				$data['info']['content'] = $info['content'];
				output_json(0, $msg, $data);
			}
			$msg = $editflag ? Lang('edit_error') : Lang('add_error');
			output_json(1, $msg);
		}else {
			include template('operation', 'server_combine');
		}
	}
	/**
	 * 合服排期列表
	 * @return [type] [description]
	 */
	public function ajax_combine_list() {
		$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

		$this->mergedb = common::load_model('server_merge_model');
		$list = $this->mergedb->get_list_page('', '*', 'id DESC', $page, $this->pagesize);
		if ($recordnum <= 0){
			// $wherestr = str_ireplace('where', '', $wherestr);
			$recordnum = $this->mergedb->count($wherestr, 'id');
		}

		$data['count'] = $recordnum;
		$data['list']  = $list;

		output_json(0, '', $data);
	}
}