<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class giftsetting extends admin {
	private $pubdb, $getdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->pubdb = common::load_model('public_model');
		$this->pubdb->table_name = 'gift_setting';
		$this->pagesize = 50;
	}

	public function init() {
		$starttime = date('Y-m-d');
		$endtime = date('Y-m-d', time()+365*24*3600);
		include template('operation', 'giftsetting');
	}

	public function ajax_list(){
		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$giftname = isset($_GET['giftname']) && !empty($_GET['giftname']) ? safe_replace($_GET['giftname']) : '';
		$giftid = isset($_GET['giftid']) ? intval($_GET['giftid']) : '';
		$wherestr = '';
		if (!empty($giftname)) {
			$wherestr = " giftname LIKE '%".trim($giftname)."%'";
		}
		if ($giftid > 0) {
			$wherestr .= !empty($wherestr) ? " OR giftid=$giftid" : " giftid=$giftid";
		}
		$list = $this->pubdb->get_list_page($wherestr, '*', 'dateline DESC', $page, $this->pagesize);
			$recordnum = $this->pubdb->count($wherestr, '*');

		$giftlist = array();
		foreach ($list as $k => $rs) {
			$items = unserialize($rs['items']);
	        $awardlist = $items['awardlist'];
	        $itemlist = $items['itemlist'];
	        $fatelist = $items['fatelist'];
	        $soullist = $items['soullist'];
	        foreach ($awardlist as $key => $value) {
	        	$giftlist[$value['award_type']] = $value['value'];
	        }
	        $giftlist['itemlist'] = $itemlist;
	        $giftlist['fatelist'] = $fatelist;
	        $giftlist['soullist'] = $soullist;
	        $giftlist['giftid'] = $rs['giftid'];
	        $giftlist['giftname'] = $rs['giftname'];
	        $giftlist['gifttype'] = $rs['gifttype'];
	        $giftlist['limitnumber'] = $rs['limitnumber'];
	        $giftlist['starttime'] = date('Y-m-d', $rs['starttime']);
	        $giftlist['endtime'] = date('Y-m-d', $rs['endtime']);
	        $giftlist['message'] = $rs['message'];
	        $list[$k] = $giftlist;
		}

		$data['count'] = $recordnum;
		$data['list']  = $list;
		unset($list);
		output_json(0, '', $data);
	}

	public function ajax_info(){

		$giftid = isset($_GET['giftid']) ? intval($_GET['giftid']) : 0;
		if ($giftid > 0) {
			$gift = $this->pubdb->get_one("giftid='$giftid'", '*');

			$items = unserialize($gift['items']);
			$awardlist = $items['awardlist'];
			$itemlist = $items['itemlist'];
			$fatelist = $items['fatelist'];
			$soullist = $items['soullist'];
			foreach ($awardlist as $key => $value) {
				$giftarr[$value['award_type']] = $value['value'];
			}
			$giftarr['itemlist'] = $itemlist;
			$giftarr['fatelist'] = $fatelist;
			$giftarr['soullist'] = $soullist;
			$giftarr['giftid'] = $gift['giftid'];
			$giftarr['giftname'] = $gift['giftname'];
			$giftarr['gifttype'] = $gift['gifttype'];
			$giftarr['limitnumber'] = $gift['limitnumber'];
			$giftarr['starttime'] = date('Y-m-d', $gift['starttime']);
			$giftarr['endtime'] = date('Y-m-d', $gift['endtime']);
			$giftarr['message'] = $gift['message'];
		}else {
			output_json(1, Lang('gift_no_exits'));
		}
		$data['info'] = $giftarr;
		output_json(0, '', $data);
	}

	public function ajax_giftsetting_log() {
		$giftid = isset($_GET['giftid']) ? intval($_GET['giftid']) : 0;
		if ($giftid <= 0)  showmessage(Lang('gift_no_exits'), 'dialog');
		$data['giftid'] = $giftid;
		include template('operation', 'giftsetting_log');
	}

	public function ajax_giftsetting_log_list() {
		$giftid = isset($_GET['giftid']) ? intval($_GET['giftid']) : 0;
		if ($giftid <= 0)  output_json(1, Lang('gift_no_exits'));

		$tablename = 'active_gift_'.$giftid;

		$extenddb = common::load_model('extend_model');
		$extenddb->db_tablepre = '';
		$tbl_exists =  $extenddb->table_exists($tablename);
		if (!$tbl_exists)	output_json(1, Lang('gift_no_exits'));

		$wherestr = '';
		$stype = isset($_GET['stype']) ? intval($_GET['stype']) : 0;
		$sname = isset($_GET['sname']) ? safe_replace($_GET['sname']) : '';
		$wherestr = '';
		if (!empty($sname)) {
			switch ($stype) {
				case 1:
					$wherestr = "nickname='$sname'";
					break;
				case 3:
					$wherestr = "username='$sname'";
					break;
				case 2:
					$wherestr = "player_id=".intval($sname);
					break;
			}
		}


		$page = isset($_GET['top']) ? intval($_GET['top']) : 1;
		$recordnum   = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$pagesize = 20;
		$extenddb->table_name = $tablename;
		$list = $extenddb->get_list_page($wherestr, '*', '', $page, $pagesize);
		if ($list && $recordnum <= 0) {
			$recordnum = $extenddb->count($wherestr, 'id');
		}
		$data['list'] = $list;
		$data['count'] = $recordnum;
		unset($list);
		output_json(0, Lang('success'), $data);
	}


	public function add() {
		$giftname = isset($_POST['giftname']) ? ext_addslashes($_POST['giftname']) : '';
		$gifttype = isset($_POST['gifttype']) ? intval($_POST['gifttype']) : 0;
		$message  = isset($_POST['message']) ? ext_addslashes($_POST['message']) : '';
		$limitnumber = isset($_POST['limitnumber']) ? intval($_POST['limitnumber']) : 0;

		$coins = isset($_POST['coins']) ? intval($_POST['coins']) : 0;
		$fame  = isset($_POST['fame']) ? intval($_POST['fame']) : 0;
		$skill = isset($_POST['skill']) ? intval($_POST['skill']) : 0;
		$ingot = isset($_POST['ingot']) ? intval($_POST['ingot']) : 0;

		$starttime = isset($_POST['starttime']) && !empty($_POST['starttime']) ? strtotime($_POST['starttime']) : time();
		$endtime   = isset($_POST['endtime']) && !empty($_POST['endtime']) ? strtotime($_POST['endtime']) : (time() + 30 * 24 * 3600);

		$item = isset($_POST['item']) ? $_POST['item'] : array();
		$fate = isset($_POST['fate']) ? $_POST['fate'] : array();
		$soul = isset($_POST['soul']) ? $_POST['soul'] : array();

		if (empty($giftname)) output_json(1, '礼包名不能为空');
		if ($limitnumber < 1) output_json(1, '限制次数不能小于1');

		$itemlist = $fatelist = $soullist = array();
		if (!empty($item)) {
			foreach ($item['id'] as $key => $ivalue) {
				if (intval($ivalue) < 1) continue;
				$itemlist[] = array(
					'item_id' => intval($ivalue),
					'level' => intval($item['level'][$key]) > 0 ? intval($item['level'][$key]) : 1,
					'number' => intval($item['number'][$key]) > 0 ? intval($item['number'][$key]) : 1,
				);
			}
		}
		if (!empty($fate)) {
			foreach ($fate['id'] as $key => $fvalue) {
				if (intval($fvalue) < 1) continue;
				$fatelist[] = array(
					'fate_id' => intval($fvalue),
					'level' => intval($fate['level'][$key]) > 0 ? intval($fate['level'][$key]) : 1,
					'number' => intval($fate['number'][$key]) > 0 ? intval($fate['number'][$key]) : 1,
				);
			}
		}
		if (!empty($soul)) {
			foreach ($soul['id'] as $key => $svalue) {
				if (intval($svalue) < 1) continue;
				$soullist[] = array(
					'soul_id' => intval($svalue),
					'number' => intval($soullist['number'][$key]) > 0 ? intval($soullist['number'][$key]) : 1,
				);
			}
		}

		$awardlist = array(
			array('award_type'=>"coin", 'value'=>$coins),
			array('award_type'=>"skill", 'value'=>$skill),
			array('award_type'=>"fame", 'value'=>$fame),
			array('award_type'=>"ingot", 'value'=>$ingot),
		);

		$award = array(
			'awardlist' => $awardlist,
			'itemlist' => $itemlist,
			'fatelist' => $fatelist,
			'soullist' => $soullist,
		);

		$maxarr = $this->pubdb->get_one('', 'max(giftid) as maxid');
		$maxid = $maxarr['maxid'];
		if ($maxid == 0) {
			$maxid = 10001;
		}else if ($maxid>0 && $maxid<10000) {
			$maxid = 10000 + $maxid;
		}else {
			$maxid = $maxid + 1;
		}

		$giftid = isset($_POST['giftid']) ? intval($_POST['giftid']) : 0;

		$items = serialize($award);
		$editflag = 0;
		if ($giftid > 0) {
			$updatearr = array(
				'giftname' => $giftname,
				'gifttype' => $gifttype,
				'limitnumber' => $limitnumber,
				'items' => $items,
				'starttime' => $starttime,
				'endtime' => $endtime,
				'message' => $message,
			);
			$ret = $this->pubdb->update($updatearr, "giftid=$giftid");
			$msg = '修改礼包：'.$giftname;
			$maxid = $giftid;
			$editflag = 1;
		}else {
			$exists = $this->pubdb->count("giftname='$giftname'", '*');
			if ($exists)  output_json(1, '不能重复添加相同的礼包');

			$dateline = time();
			$insertarr = array(
				'giftid' => $maxid,
				'giftname' => $giftname,
				'gifttype' => $gifttype,
				'limitnumber' => $limitnumber,
				'items' => $items,
				'starttime' => $starttime,
				'endtime' => $endtime,
				'message' => $message,
				'dateline' => $dateline,
			);
			$ret = $this->pubdb->insert($insertarr);
			$msg = '添加礼包：'.$giftname;

			$data['info'] = array(
					'giftid' => $maxid,
					'starttime' => date('Y-m-d', $starttime),
					'endtime' => date('Y-m-d', $endtime),
					'message' => $message,
					'limitnumber' => $limitnumber,
					'giftname' => $giftname,
					'gifttype' => $gifttype,

				);

			if ($ret) {
				$tablename = 'active_gift_'.$maxid;

				$extenddb = common::load_model('extend_model');
				$extenddb->db_tablepre = '';
				$tbl_exists =  $extenddb->table_exists($tablename);
				if ($tbl_exists) output_json(1, '数据表 '.$tablename.' 已存在，请重新填写！');

				if ($gifttype == 1) {
					$createsql = "
						CREATE TABLE `$tablename` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `cid` int(11) unsigned NOT NULL DEFAULT '0',
						  `sid` int(11) unsigned NOT NULL DEFAULT '0',
						  `player_id` int(11) unsigned NOT NULL DEFAULT '0',
						  `username` varchar(50) NOT NULL,
						  `nickname` varchar(50) NOT NULL,
						  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
						  `lastdotime` int(10) unsigned NOT NULL DEFAULT '0',
						  `daytimes` tinyint(3) unsigned NOT NULL DEFAULT '0',
						  `times` smallint(5) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `cid_sid_username` (`cid`,`sid`,`username`) USING BTREE,
						  KEY `dateline` (`createtime`),
						  KEY `lastdotime` (`lastdotime`),
						  KEY `times` (`times`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='$giftname';
					";
				}else {
					$createsql = "
						CREATE TABLE `$tablename` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `cid` int(11) unsigned NOT NULL DEFAULT '0',
						  `sid` int(11) unsigned NOT NULL DEFAULT '0',
						  `player_id` int(11) unsigned NOT NULL DEFAULT '0',
						  `username` varchar(50) NOT NULL,
						  `nickname` varchar(50) NOT NULL,
						  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
						  `lastdotime` int(10) unsigned NOT NULL DEFAULT '0',
						  `times` tinyint(3) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `cid_sid_username` (`cid`,`sid`,`username`) USING BTREE,
						  KEY `dateline` (`createtime`),
						  KEY `lastdotime` (`lastdotime`),
						  KEY `times` (`times`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='$giftname';
					";	
				}

				$table = $extenddb->query($createsql);
				if ($table) {
					$msg .= '，创建礼包记录表['.$tablename.']';
				}else {
					$msg .= '，但无法创建礼包记录表['.$tablename.']';
				}
			}
		}

		$data['editflag'] = $editflag;
		
		if ($ret) {
			parent::op_log($msg.'，礼包ID：'.$maxid);
			$msg .= ' 成功';
			output_json(0, $msg, $data);
		}

		$msg .= ' 失败';
		output_json(1, $msg);
	}
}
