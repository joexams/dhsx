<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class company extends admin {
	private $companydb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->companydb = common::load_model('public_model');
		$this->companydb->table_name = 'company';
		$this->pagesize = 20;
	}

	public function init(){


	}
	/**
	 * 运营商详情
	 * 
	 */ 
	public function ajax_setting_info(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$format = isset($_GET['format']) ? trim($_GET['format']) : '';
		if ($cid > 0){
			$data['info'] = $this->companydb->get_one(array('cid'=>$cid));
			$money_type = array('人民币', '美元', '港币', '台币', '澳币', '新加坡元', '韩币');
			$data['money_type_select'] = '';
			foreach ($money_type as $value) {
				$selected = $value == $data['info']['money_type'] ? ' selected' : '';
				$data['money_type_select'] .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
			}
			if (strpos($data['info']['link'], '|') !== false){
				$data['info']['link'] = explode('|', $data['info']['link']);
			}
			if (strpos($data['info']['charge_ips'], '|') !== false){
				$data['info']['charge_ips'] = str_replace('|', "\r\n",$data['info']['charge_ips']);
			}
			unset($money_type);

			if ($format == 'json'){
				$jsondata['info']['game_text'] = $data['info']['game_text'];
				$jsondata['info']['link'] = $data['info']['link'];
				output_json(0, Lang('success'), $jsondata);
			}
			include template('develop', 'company_info');
		}else {
			output_json(1);
		}
	}
	/**
	 * 运营商列表
	 * 
	 */ 
	public function ajax_setting_list(){
		$isall = isset($_GET['all']) ? 1 : 0;
		$wherestr = '';
		$wherestr = parent::check_pf_priv('company');

		if ($isall == 1){
			$list = $this->companydb->select(str_ireplace('where', '', $wherestr), 'cid, name', '', 'corder ASC');
			$recordnum = count($list);
		}else {
			$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			
			if (isset($_GET['dogetSubmit'])){
				if (isset($_GET['type']) && intval($_GET['type']) > 0){
					$wherestr .= !empty($wherestr) ? ' AND type='.intval($_GET['type']).'' : 'type='.intval($_GET['type']).'';
				}
				if (isset($_GET['name']) && !empty($_GET['name'])){
					$name = trim(safe_replace($_GET['name']));
					$wherestr .= !empty($wherestr) ? " AND name like '%".$name."%'" : "name like '%".$name."%'";
				}
				if (isset($_GET['web']) && !empty($_GET['web'])){
					$web = trim(safe_replace($_GET['web']));
					$wherestr .= !empty($wherestr) ? " AND web like '%".$web."%'" : "web like '%".$web."%'";
				}
			}

			$list = $this->companydb->get_list_page($wherestr, 'cid,type,name,slug,web,game_name,corder', 'corder ASC', $page, $this->pagesize);
			if ($recordnum <= 0){
				$recordnum = $this->companydb->count($wherestr, 'cid');
			}
		}

		$data['count']  = $recordnum;
		$data['list']   = $list;
		output_json(0, '', $data);
	}
	/**
	 * 运营商设置
	 * 
	 */ 
	public function setting(){
		if (isset($_POST['doSubmit'])){
			$info = $_POST;
			$cid  = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			unset($info['cid'], $info['doSubmit']);

			if (isset($info['charge_ips']) && !empty($info['charge_ips'])){
				$info['charge_ips'] = str_replace(array("\r\n", "\n", "\r"), '|', $info['charge_ips']);
			}
			if (isset($info['link']) && !empty($info['link'])){
				$info['link'] = implode($info['link'], '|');
			}

			if ($cid > 0){
				$rtn = $this->companydb->update($info, array('cid'=>$cid));
				if ($rtn){
					output_json(0, Lang('success'));
				}

				output_json(1, Lang('error'));
			}else {
				$cid = $this->companydb->insert($info, true);				
			}

			if ($cid){
				$data['info']['name']      = $info['name'];
				$data['info']['slug']      = $info['slug'];
				$data['info']['web']       = $info['web'];
				$data['info']['game_name'] = $info['game_name'];
				$data['info']['type']      = $info['type'];
				$data['info']['cid']       = $cid;
				$data['info']['corder']    = 0;
				
				output_json(0, Lang('success'), $data);
			}

			output_json(1, Lang('error'));
		}else {
			include template('develop', 'company');
		}
	}
}
