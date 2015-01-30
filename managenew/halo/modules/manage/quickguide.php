<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class quickguide extends admin {
	private $qguidedb;
	function __construct(){
		parent::__construct();
		$this->qguidedb = common::load_model('quickguide_model');
	}
	/**
	 * 添加快捷导航
	 */ 
	public function setting(){
		if (isset($_POST['doSubmit'])){
			$userid = $_SESSION['userid'];
			$info['qname']  = isset($_POST['qname']) ? trim($_POST['qname']) : '';
			$info['qurl']   = isset($_POST['qurl']) ? trim($_POST['qurl']) : '';
			$info['userid'] = $userid;
			$qcount = $this->qguidedb->count(array('userid'=>$userid), 'qid');
			if ($qcount >= 10){
				output_json(1, Lang('quickguide_tips'));
			}
			if ($userid > 0 && !empty($info['qname']) && !empty($info['qurl'])){
				$qid = $this->qguidedb->insert($info, true);
				$data['info']['qid']  = $qid;
				$data['info']['qurl'] = $info['qurl'];
				$data['info']['qname'] = $info['qname'];
				$data['info']['qcount'] = $qcount;
				if ($qid){
					output_json(0, Lang('success'), $data);
				}
			}
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 删除
	 */ 
	public function delete(){
		$qid = isset($_GET['qid']) ? intval($_GET['qid']) : 0;
		$userid = $_SESSION['userid'];
		if ($qid > 0 && $userid >0){
			$rtn = $this->qguidedb->delete(array('qid'=>$qid, 'userid'=>$userid));
			if ($rtn){
				output_json(0, Lang('success'));
			}
		}
		output_json(1, Lang('error'));
	}
}