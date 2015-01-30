<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class server extends admin {
	private $serverdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->serverdb  = common::load_model('public_model');
		$this->serverdb->table_name = 'servers';
		$this->pagesize = 15;
	}

	public function init(){

	}
	/**
	 * 服务器列表
	 * 
	 */ 
	public function public_server_list(){
		$cid         = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$all		 = isset($_GET['all']) ? 1 : 0;
		$wherestr = $where  = '';
		$wherestr = $where  = parent::check_pf_priv('server');
		
		if ($all > 0){
			if ($cid > 0){
				$wherestr .= !empty($wherestr) ? " AND cid='$cid'": "cid='$cid'";
			}
			$wherestr .= !empty($wherestr) ? " AND open=1 AND open_date<'".date('Y-m-d H:i:s')."'" :  "open=1 AND open_date<'".date('Y-m-d H:i:s')."'";
			$list = $this->serverdb->select(str_ireplace('where', '', $wherestr), 'cid,sid,name,o_name,server,server_ver,api_server,combined_to,unix_timestamp(open_date) as opendate,unix_timestamp(open_date_old) as oldopendate,is_combined');
		}else {
			$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$type 		 = isset($_GET['type']) && !empty($_GET['type']) ? trim($_GET['type']) : ''; 

			if ($cid > 0){
				$wherestr .= !empty($wherestr) ? " AND cid='$cid'": " cid='$cid'";
			}else if ($cid == 0){
				$wherestr .= !empty($wherestr) ? " AND open_date>='".date('Y-m-d 00:00:00')."' AND open_date<'".date('Y-m-d 23:59:59')."'": "  open_date>='".date('Y-m-d 00:00:00')."' AND open_date<'".date('Y-m-d 23:59:59')."'";
			}
            $data['scount'] = array(
                'wait' => 0,
                'notsetting' => 0,
                'today' => 0,
                'open' => 0,
            );
			$where = str_ireplace('where', '', $where);
            $statuswhere1 =  $where.(!empty($where) ? ' AND ' : '')." open_date > now() AND open = 1";
            $statuswhere2 =  $where.(!empty($where) ? ' AND ' : '')." open_date > now() AND open = 0";
            $statuswhere3 =  $where.(!empty($where) ? ' AND ' : '')." date_format(open_date, '%Y-%m-%d') = curdate()";
            $statuswhere4 =  $where.(!empty($where) ? ' AND ' : '')." open = 1 AND test = 0 AND open_date <= now()";

			if (isset($_GET['dogetSubmit'])){
				if (isset($_GET['sid']) && intval($_GET['sid']) > 0){
					$wherestr .= !empty($wherestr) ? ' AND sid='.intval($_GET['sid']).'' : ' sid='.intval($_GET['sid']).'';
				}
				if (isset($_GET['name']) && !empty($_GET['name'])){
					$name = trim(safe_replace($_GET['name']));
					$wherestr .= !empty($wherestr) ? " AND name LIKE '%".$name."%'" : " name LIKE '%".$name."%'";
				}
				if (isset($_GET['sname']) && (!empty($_GET['sname']) || $_GET['sname'] === '0' )){
					$sname = trim(safe_replace($_GET['sname']));
					$wherestr .= !empty($wherestr) ? " AND name='qq_s$sname'" : " name='qq_s$sname'";
				}
				if (isset($_GET['apis']) && !empty($_GET['apis'])){
					$apis = trim(safe_replace($_GET['apis']));
					$wherestr .= !empty($wherestr) ? " AND api_server='".$apis."'" : " api_server='".$apis."'";
				}
				if (isset($_GET['dbs']) && !empty($_GET['dbs'])){
					$dbs = trim(safe_replace($_GET['dbs']));
					$wherestr .= !empty($wherestr) ? " AND db_server='".$dbs."'": " db_server='".$dbs."'";
				}
				if (isset($_GET['vers']) && !empty($_GET['vers'])){
					$vers = trim(safe_replace($_GET['vers']));
					$wherestr .= !empty($wherestr) ? " AND server_ver='".$vers."'": " server_ver='".$vers."'";
				}
				if (isset($_GET['combined_to']) && intval($_GET['combined_to']) == 1){
					$wherestr .= !empty($wherestr) ? " AND is_combined=1": " is_combined=1";
				}
                
                //$statuswhere1 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere1;
                //$statuswhere2 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere2;
                //$statuswhere3 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere3;
                //$statuswhere4 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere4;

                if (isset($_GET['status']) && intval($_GET['status']) > 0) {
                    switch (intval($_GET['status'])) {
                        case 1:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open_date > now() AND open = 1";
                            break;
                        case 2:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open_date > now() AND open = 0";
                            break;
                        case 3:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." date_format(open_date, '%Y-%m-%d') = curdate()";
                            break;
                        case 4:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open = 1 AND test = 0 AND open_date <= now()";
                            break;
                    }
                }
			}
			if (ROUTE_M == 'develop') {
            	$data['scount']['wait'] = $this->serverdb->count($statuswhere1, '*');
            	$data['scount']['notsetting'] = $this->serverdb->count($statuswhere2, '*');
            	$data['scount']['today'] = $this->serverdb->count($statuswhere3, '*');
            	$data['scount']['open'] = $this->serverdb->count($statuswhere4, '*');
			}
			$column = 'cid,sid,name,o_name,api_server,api_port,server,db_server,db_name,open_date,unix_timestamp(open_date) as opendate,server_ver,open,test,private';
			if ($type == 'list'){
				$column = 'cid,sid,name,o_name, server_ver,api_server,combined_to,server,open_date,unix_timestamp(open_date) as opendate';
			}
		    $wherestr = str_ireplace('where', '', $wherestr);	
			$list = $this->serverdb->get_list_page($wherestr, $column, 'sid DESC', $page, $this->pagesize);
			if ($recordnum <= 0){
				$recordnum = $this->serverdb->count($wherestr, 'sid');
			}
		}

		foreach ($list as $key => $value) {
			$list[$key]['sufserver'] = str_replace('.app100616996.qqopenapp.com', '', $value['server']);
			$list[$key]['distancedate'] = ceil((time() - $value['opendate']) / (24*3600));
		}
		
		$data['cid']   = $cid;
		$data['count'] = $recordnum;
		$data['list']  = $list;
		$data['todaytime'] = strtotime(date('Y-m-d 23:59:59'));
		unset($list);
		output_json(0, '', $data);
	}
	/**
	 * 服务器设置
	 * 
	 */ 
	public function setting() {
		if (isset($_POST['doSubmit'])){
			$info = $_POST;
			$sid  = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
			$cid  = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			unset($info['sid'], $info['doSubmit']);
			if (isset($_POST['doflag']) && $_POST['doflag'] == 'quick'){
				if (empty($info['server'])) {
					output_json(1, '游戏地址不能为空，请重新填写！');
				}
				if (is_array($info['server']) && count($info['server']) > 0){		
					foreach ($info['server'] as $key => $value) {
						$rtn = $this->serverdb->get_one("server LIKE '%$value%'".($sid>0? " AND sid<>'$sid'": ''), 'sid, cid');
						if ($rtn) {
							output_json(1, '您填写的游戏地址：'.$value.' 已经被使用');
						}
					}
					
					$info['server'] = implode($info['server'], '|');
				}else {
					$rtn = $this->serverdb->get_one("server LIKE '%".$info['server']."%'".($sid>0? " AND sid<>'$sid'": ''), 'sid, cid');
					if ($rtn) {
						output_json(1, '您填写的游戏地址已经被使用');
					}
				}
				unset($info['doflag']);

				$info['api_server'] = '';
				$info['api_port']   = '';
				$info['db_server']  = '';
				$info['db_name']    = '';
				$info['db_server_2'] = '';
				$info['db_root_2'] = '';
				$info['db_pwd_2'] = '';
				$info['db_name_2'] = '';
				$info['pay_item'] = '';
				$info['server_ver'] = '';
				$info['private']    = 1;
				$info['open']       = 0;
				$info['test']       = 0;
				$info['first_pay_act'] = 0;
				$info['new_card_act']  = 0;

			}else{
				$info['server'] = str_replace(array("\r\n", "\n", "\r"), '|', $info['server']);
			}

			if (isset($_POST['sid']) && $sid > 0){

				if (!empty($info['server_ver'])) {
					$floderdir = CORE_PATH.'api'.DIRECTORY_SEPARATOR.$info['server_ver'].DIRECTORY_SEPARATOR;
					$filelist = $this->getFolders($floderdir, 'api_');
					if (count($filelist) <= 0) {
						output_json(1, Lang('version').Lang('file_not_exists'));
					}
				}

				$info['server_ver'] = empty($info['server_ver']) ? '000' : $info['server_ver'];
				$info['api_server'] = empty($info['api_server']) ? '000' : $info['api_server'];
				$info['db_server'] = empty($info['db_server']) ? '000' : $info['db_server'];

				$rtn = $this->serverdb->update($info, array('sid'=>$sid));
				if ($rtn){
					$content = Lang('success').' 修改服务器配置(SID='.$sid.')。';
					parent::op_log($content);
					output_json(0, Lang('success'));
				}

				output_json(1, Lang('error'));
			}else {
				$sid = $this->serverdb->insert($info, true);
				if ($sid){
					//成功增加新手卡
					// $batch = array(
					// 	'cid' => $cid,
					// 	'sid' => $sid,
					// 	'name' => '新手卡大放送',
					// 	'ingot' => 0,
					// 	'coins' => 0,
					// 	'num' => 0,
					// 	'item_id' => 520,
					// 	'item_name' => '神秘礼包',
					// 	'item_val' => 1,
					// 	'juche' => 1,
					// 	'userid' => intval($_SESSION['userid']),
					// 	'edate' => '9999-01-01',
					// 	'ctime' => date('Y-m-d H:i:s')
					// );
					// $this->serverdb->table_name = 'code_batch';
					// $this->serverdb->insert($batch);

					$info['sid']  = $sid;
					$info['opendate'] = strtotime($info['open_date']);
					$data['info'] = $info;
					$content = Lang('success').' 添加服务器配置(SID='.$sid.')。';
					parent::op_log($content);
					output_json(0, Lang('success'), $data);
				}

				output_json(1, Lang('error'));
			}
		}else {
			if (isset($_GET['cid'])){
				$data['url']['cid'] = intval($_GET['cid']);
			}else {
				$this->serverdb->table_name = 'company';
				$cr = $this->serverdb->get_one('', 'cid', 'corder ASC');
				$data['url']['cid'] = $cr['cid'];
			}
			$data['url']['m'] = ROUTE_M;
			$data['url']['c'] = ROUTE_C;
			$data['url']['v'] = ROUTE_V;
			include template('develop', 'server');
		}
	}
	/**
	 * 服务器列表
	 * 
	 */ 
	public function ajax_setting_list(){
		$cid         = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$all		 = isset($_GET['all']) ? 1 : 0;
		$wherestr = $where  = '';
		$wherestr = $where  = parent::check_pf_priv('server');
		
		if ($all > 0){
			if ($cid > 0){
				$wherestr .= !empty($wherestr) ? " AND cid='$cid'": "cid='$cid'";
			}
			$wherestr .= !empty($wherestr) ? " AND open=1 AND open_date<'".date('Y-m-d H:i:s')."'" :  "open=1 AND open_date<'".date('Y-m-d H:i:s')."'";
			$list = $this->serverdb->select(str_ireplace('where', '', $wherestr), 'cid,sid,name,o_name,server,server_ver,api_server,combined_to,unix_timestamp(open_date) as opendate,unix_timestamp(open_date_old) as oldopendate,is_combined');
		}else {
			$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$type 		 = isset($_GET['type']) && !empty($_GET['type']) ? trim($_GET['type']) : ''; 

			if ($cid > 0){
				$wherestr .= !empty($wherestr) ? " AND cid='$cid'": " cid='$cid'";
			}else if ($cid == 0){
				$wherestr .= !empty($wherestr) ? " AND open_date>='".date('Y-m-d 00:00:00')."' AND open_date<'".date('Y-m-d 23:59:59')."'": "  open_date>='".date('Y-m-d 00:00:00')."' AND open_date<'".date('Y-m-d 23:59:59')."'";
			}
            $data['scount'] = array(
                'wait' => 0,
                'notsetting' => 0,
                'today' => 0,
                'open' => 0,
            );
			$where = str_ireplace('where', '', $where);
            $statuswhere1 =  $where.(!empty($where) ? ' AND ' : '')." open_date > now() AND open = 1";
            $statuswhere2 =  $where.(!empty($where) ? ' AND ' : '')." open_date > now() AND open = 0";
            $statuswhere3 =  $where.(!empty($where) ? ' AND ' : '')." date_format(open_date, '%Y-%m-%d') = curdate()";
            $statuswhere4 =  $where.(!empty($where) ? ' AND ' : '')." open = 1 AND test = 0 AND open_date <= now()";

			if (isset($_GET['dogetSubmit'])){
				if (isset($_GET['sid']) && intval($_GET['sid']) > 0){
					$wherestr .= !empty($wherestr) ? ' AND sid='.intval($_GET['sid']).'' : ' sid='.intval($_GET['sid']).'';
				}
				if (isset($_GET['name']) && !empty($_GET['name'])){
					$name = trim(safe_replace($_GET['name']));
					$wherestr .= !empty($wherestr) ? " AND name LIKE '%".$name."%'" : " name LIKE '%".$name."%'";
				}
				if (isset($_GET['sname']) && (!empty($_GET['sname']) || $_GET['sname'] === '0' )){
					$sname = trim(safe_replace($_GET['sname']));
					$wherestr .= !empty($wherestr) ? " AND name='qq_s$sname'" : " name='qq_s$sname'";
				}
				if (isset($_GET['apis']) && !empty($_GET['apis'])){
					$apis = trim(safe_replace($_GET['apis']));
					$wherestr .= !empty($wherestr) ? " AND api_server='".$apis."'" : " api_server='".$apis."'";
				}
				if (isset($_GET['dbs']) && !empty($_GET['dbs'])){
					$dbs = trim(safe_replace($_GET['dbs']));
					$wherestr .= !empty($wherestr) ? " AND db_server='".$dbs."'": " db_server='".$dbs."'";
				}
				if (isset($_GET['vers']) && !empty($_GET['vers'])){
					$vers = trim(safe_replace($_GET['vers']));
					$wherestr .= !empty($wherestr) ? " AND server_ver='".$vers."'": " server_ver='".$vers."'";
				}
				if (isset($_GET['combined_to']) && intval($_GET['combined_to']) == 1){
					$wherestr .= !empty($wherestr) ? " AND is_combined=1": " is_combined=1";
				}
                
                //$statuswhere1 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere1;
                //$statuswhere2 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere2;
                //$statuswhere3 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere3;
                //$statuswhere4 = $wherestr.(!empty($wherestr) ? ' AND ' : '').$statuswhere4;

                if (isset($_GET['status']) && intval($_GET['status']) > 0) {
                    switch (intval($_GET['status'])) {
                        case 1:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open_date > now() AND open = 1";
                            break;
                        case 2:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open_date > now() AND open = 0";
                            break;
                        case 3:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." date_format(open_date, '%Y-%m-%d') = curdate()";
                            break;
                        case 4:
                            $wherestr .= (!empty($wherestr) ? ' AND ' : '')." open = 1 AND test = 0 AND open_date <= now()";
                            break;
                    }
                }
			}
			if (ROUTE_M == 'develop') {
            	$data['scount']['wait'] = $this->serverdb->count($statuswhere1, '*');
            	$data['scount']['notsetting'] = $this->serverdb->count($statuswhere2, '*');
            	$data['scount']['today'] = $this->serverdb->count($statuswhere3, '*');
            	$data['scount']['open'] = $this->serverdb->count($statuswhere4, '*');
			}
			$column = 'cid,sid,name,o_name,api_server,api_port,server,db_server,db_name,open_date,unix_timestamp(open_date) as opendate,server_ver,open,test,private';
			if ($type == 'list'){
				$column = 'cid,sid,name,o_name,api_server,server,open_date,unix_timestamp(open_date) as opendate';
			}
		    $wherestr = str_ireplace('where', '', $wherestr);	
			$list = $this->serverdb->get_list_page($wherestr, $column, 'sid DESC', $page, $this->pagesize);
			if ($recordnum <= 0){
				$recordnum = $this->serverdb->count($wherestr, 'sid');
			}
		}

		foreach ($list as $key => $value) {
			$list[$key]['sufserver'] = str_replace('.app100616996.qqopenapp.com', '', $value['server']);
		}
		
		$data['cid']   = $cid;
		$data['count'] = $recordnum;
		$data['list']  = $list;
		$data['todaytime'] = strtotime(date('Y-m-d 23:59:59'));
		unset($list);
		output_json(0, '', $data);
	}
	/**
	 * 服务器详细信息
	 * 
	 */ 
	public function ajax_setting_info(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid > 0){
			$info = $this->serverdb->get_one(array('sid' => $sid), '*');

			if ($info['name']) {
				$data['slug'] = $info['name'];
			}else {
				$this->serverdb->table_name = 'company';
				$cr = $this->serverdb->get_one(array('cid'=>$info['cid']), 'cid, slug');
				$data['slug'] = $cr['slug'].'_s';
			}

			$this->serverdb->table_name = 'servers_address';
			$addresslist = $this->serverdb->select('');
			$data['apistring'] = $data['dbstring'] = $data['versionstring'] = '';

			if (strpos($info['server'], '|') !== false){
				$info['server'] = str_replace('|', PHP_EOL,$info['server']);
			}

			if ($addresslist){
				foreach ($addresslist as $key => $value) {
					if ($value['type'] == 0){
						$selected = $value['name'] == $info['api_server'] ? ' selected' : '';
						$data['apistring'] .= '<option value="'.$value['name'].'"'.$selected.'>'.$value['name'].'</option>';  //api 取地址1
					}else if ($value['type'] == 1){
						$selected = $value['name'] == $info['db_server'] ? ' selected' : '';
						$data['dbstring']  .= '<option value="'.$value['name'].'"'.$selected.'>'.$value['name'].'</option>';;  //数据库 取从库
					}else if ($value['type'] == 2 && $value['name2'] == 1){
						$selected = $value['name'] == $info['server_ver'] ? ' selected' : '';
						$data['versionstring']  .= '<option value="'.$value['name'].'"'.$selected.'>'.$value['name'].'</option>';;  //版本 取地址1
					}
				}
			}

			$info['db_root'] = !empty($info['info']) ? $info['info'] : 'root';
			$info['db_pwd'] = !empty($info['db_pwd']) ? $info['db_pwd'] : 'YuUkD<%PsB(0u]!x';

			if ($info['combined_to'] > 0) {
				$this->serverdb->table_name = 'servers';
				$combined = $this->serverdb->get_one(array('sid' => $info['combined_to']), 'o_name, name, combined_to');
				$info['combined_name'] = $combined['name'];
				$info['combined_o_name'] = $combined['o_name'];
			}

			$data['info'] = $info;
			unset($addresslist, $info);
			include template('develop', 'server_info');
		}
	}
	/**
	 * 机器地址列表
	 * 
	 */ 
	public function ajax_address_list(){
		$type        = isset($_GET['type']) ? intval($_GET['type']) : 0;
		$this->serverdb->table_name = 'servers_address';
		if (isset($_GET['all'])){
			$wherestr = $type > 0 ? 'type='.$type : '';
			$list = $this->serverdb->select($wherestr, '*', '', 'id DESC');
		}else {
			$page        = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$this->pagesize    = 50;

			$list = $this->serverdb->get_list_page("type='$type'", '*', 'id DESC', $page, $this->pagesize);

			if (count($list) > 0){
				foreach ($list as $key => $value) {
					$str_names .= ",'".$value['name']."'";
					$list[$key]['count'] = 0;
				}
				$str_names = trim($str_names, ',');
				$wherestr = $column = '';
				switch ($type) {
					case 0:
						$wherestr = ' api_server IN ('.$str_names.')';
						$column   = 'api_server';
						break;
					case 1:
						$wherestr = 'db_server IN ('.$str_names.')';
						$column   = 'db_server';
						break;
					case 2:
						$wherestr = ' server_ver IN ('.$str_names.')';
						$column   = 'server_ver';
						break;
				}

				$this->serverdb->table_name = 'servers';
				$typelist = $this->serverdb->select($wherestr, "count(sid) as num,$column", '', '', $column);

				foreach ($list as $key => $value) {
					foreach ($typelist as $tval) {
						if ($tval[$column] == $value['name']){
							$list[$key]['count'] = $tval['num'];
							break;
						}
					}
				}

				unset($str_name, $typelist);
			}
			if ($recordnum <= 0){
				$this->serverdb->table_name = 'servers_address';
				$recordnum = $this->serverdb->count('type='.$type, 'id');
			}
		}

		$data['count']  = $recordnum;
		$data['list']   = $list;
		$data['type']   = $type;

		output_json(0, '', $data);
	}

	/**
	 * 机器地址设置
	 * 
	 */ 
	public function address(){
		if (isset($_POST['doSubmit'])){
			$info['name']  = isset($_POST['name']) ? trim(safe_replace($_POST['name'])) : '';
			$info['name2'] = isset($_POST['name2']) ? trim(safe_replace($_POST['name2'])) : '';
			$info['name3'] = isset($_POST['name3']) ? trim(safe_replace($_POST['name3'])) : '';
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$info['type']  = isset($_POST['type']) ? intval($_POST['type']) : 0;
			$this->serverdb->table_name = 'servers_address';

			if ($info['type'] == 2) {
				$floderdir = CORE_PATH.'api'.DIRECTORY_SEPARATOR.$info['name'].DIRECTORY_SEPARATOR;
				$filelist = $this->getFolders($floderdir, 'api_');
				if (count($filelist) <= 0) {
					output_json(1, Lang('version').Lang('file_not_exists'));
				}
			}

			if ($id > 0){
				unset($info['type']);
				$rtn = $this->serverdb->update($info, array('id'=>$id));
				$id  = $rtn ? $id : 0;
			}else {
				
				$id = $this->serverdb->insert($info, true);
			}
			
			$data = array();
			if ($id > 0){
				$content        = $msg = Lang('success');
				$info['id']     = $id;
				$info['count']  = 0;
				$data['info']   = $info;
				$status         = 0;
			}else {
				$status         = 1;
				$content        = $msg = Lang('error');
			}

			if ($info['type'] == 1){
				$content .= '  数据库';
				$content .= trim($info['name2']) ? '  主库：'.$info['name2'] : '';
				$content .= trim($info['name']) ? '  从库：'.$info['name'] : '';
			}else if ($info['type'] == 2){
				$content .= '  版本号';
				$content .= trim($info['name']) ? '  机器地址1：'.$info['name'] : '';
			}else {
				$content .= '  API地址';
				$content .= trim($info['name']) ? '  机器地址1：'.$info['name'] : '';
				$content .= trim($info['name2']) ? '  机器地址2：'.$info['name2'] : '';
				$content .= trim($info['name3']) ? '  机器地址3：'.$info['name3'] : '';
			}

			parent::op_log($content);
			output_json($status, $msg, $data);

		}else {
			include template('develop', 'server_address');
		}
	}

	/**
	 * 机器地址删除
	 * 
	 */ 
	public function ajax_address_delete(){
		$id   = intval($_POST['id']);
		$this->serverdb->table_name = 'servers_address';
		$status = $this->serverdb->delete(array('id' => $id));

		if ($status){
			$status  = 0;
			$content = $msg = Lang('delete_server_address_success');
		}else {
			$status  = 1;
			$content = $msg = Lang('delete_server_address_error');
		}
		$content .= trim($_POST['name']) ? '  机器地址1：'.$_POST['name'] : '';
		$content .= trim($_POST['name2']) ? '  机器地址2：'.$_POST['name2'] : '';
		parent::op_log($content);
		output_json($status, $msg);
	}
	/**
	 * 查看合服排期
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
			$serverid = 0;

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
						'first_pay_act' => 0,
						'level_act' => 1,
						'mission_act' => 1,
						'new_card_act' => 0,
						'is_combined' => 1,
						'open_date_old' => min($odate)
					);
					$serverid = $pubdb->insert($serverinfo, true);

					$data['info']['combined_to'] = $serverid;
					$this->mergedb->update(array('combined_to' => $serverid), array('id' => $id, 'combined_to' => 0));

					//foreach ($sid as $key => $value) {
						//$pubdb->update(array('combined_to' => $serverid), array('sid'=>$value));
					//}
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
				$data['info']['combined_to'] = 0;
				output_json(0, $msg, $data);
			}
			$msg = $editflag ? Lang('edit_error') : Lang('add_error');
			output_json(1, $msg);
		}else {
			include template('develop', 'server_combine');
		}
	}

	public function combined_add()
	{
		include template('ajax', 'combined_add');
	}
        /**
     * 合服指向
     * @return [type] [description]
     */
    public function combined_point() {
        $mergedb = common::load_model('server_merge_model');
        $combinedid = $_GET['combinedid'] ? intval($_GET['combinedid']) : 0;
        if ($combinedid) {
            $combined = $mergedb->get_one(array('id' => $combinedid));
            $sids = trim($combined['sids'], ',');
            if (!empty($sids)) {
                $sidarr = explode(',', $sids);
                $serverid = intval($combined['combined_to']);
                foreach ($sidarr as $key => $value) {
                    $this->serverdb->update(array('combined_to' => $serverid), array('sid'=>$value));
                }
                $mergedb->update(array('status' => 1), array('id' => $combinedid));

                output_json(0, Lang('success_combined_point'));
            }
        }

        output_json(1, Lang('combined_id_not_exists'));
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
	/**
	 * 查看开服排期
	 * @return [type] [description]
	 */
	public function open() {
		include template('develop', 'server_open');
	}
	/**
	 * 测试连接数据库
	 * 
	 */ 
	public function test_db_connect(){
		$hostname = isset($_GET['dbhost']) ? trim($_GET['dbhost']) : '';
		$username = isset($_GET['dbroot']) ? trim($_GET['dbroot']) : '';
		$password = isset($_GET['dbpwd']) ? trim($_GET['dbpwd']) : '';
		$dbname   = isset($_GET['dbname']) ? trim($_GET['dbname']) : '';
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid > 0) {
			$server = $this->serverdb->get_one(array('sid'=>$sid));
			$hostname = $server['db_server'];
			$username = $server['db_root'];
			$password = $server['db_pwd'];
			$dbname = $server['db_name'];
		}  
		if (!empty($hostname) && !empty($username) && !empty($password) && !empty($dbname)){
			$linkflag = 1;
			if (!$link = @mysql_connect($hostname, $username, $password, 1)){
				$status   = 1;
				$msg      = Lang('connect_mysql_error');
				$linkflag = 0;
			}
			if($linkflag && $dbname && !@mysql_select_db($dbname, $link)) {
				$status   = 1;
				$msg      = Lang('connect_database_error');
				$linkflag = 0;
			}
			if ($linkflag){
				$status = 0;
				$msg    = Lang('connect_database_success');
			}
		}else {
			$status = 1;
			$msg    = Lang('connect_mysql_arg_not_enough');
		}
		
		output_json($status, $msg, array('sid' => $sid));
	}
	/**
	 * 获取目录信息
	 * @param  string $folderdir [description]
	 * @param  string $action    [description]
	 * @return [type]            [description]
	 */
	private function getFolders($folderdir = '', $action = '') {
		$dir = @opendir($folderdir);
		$files = array();
		while($entry = readdir($dir)) {
			if ($entry != "." && $entry != "..") {
				$files[] = $entry;
			}
		}
		closedir($dir);
		if ($action == '') {
			return $files;
		}

		if($files) {
			arsort($files);
			$logfile = $action;
			$logfiles = array();
			foreach($files as $file) {
				if(strpos($file, $logfile) !== FALSE) {
					$logfiles[] = $file;
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
