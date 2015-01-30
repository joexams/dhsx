<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class shopad extends admin {
	public function __construct(){
		parent::__construct();
	}

	public function setting() {
		$pubdb = common::load_model('public_model');
		$pubdb->table_name = 'servers';

		if (isset($_POST['doSubmit'])) {
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$adlist = isset($_POST['ads']) ? ext_addslashes($_POST['ads']) : array();
			$servers = isset($_POST['sid']) ? ext_addslashes($_POST['sid']) : array();

			$ads  = array();
			foreach ($adlist as $key => $value) {
				$ads[]['ad_id'] = $value;
			}

			if (!$servers) output_json(1, Lang('not_selected_company_or_server'));
			if (!$ads)  output_json(1, '未选择商城广告');

			$sids = implode(",",$servers);
			$serverlist = $pubdb->select("sid IN ($sids)", 'sid, name, server_ver, api_server, api_port, api_pwd');
			foreach ($serverlist as $key => $srs) {
				$api_admin = common::load_api_class('api_admin', $srs['server_ver']);
				if ($api_admin !== false) {
					$api_admin::$SERVER = $srs['api_server'];
			        $api_admin::$PORT   = $srs['api_port'];
			        $api_admin::$ADMIN_PWD   = $srs['api_pwd'];

			        $msg = $api_admin::change_ad($ads);
			        if($msg['result'] == 1) {
			            $msg .= $srs['name'].' - OK!<br>';
			        }else{
			            $msg .= $srs['name'].' - ERR!<br>';
			        }
			    }
			}
			$content['content']  = '设置商城广告：'.implode($adlist, ',').PHP_EOL.Lang('server').' ID：'.$sids;
			$content['key']      = 'shopad_setting';
			$content['sid']      = 0;
			$content['playerid'] = 0;
			parent::op_log($content, 'source');

			output_json(0, $msg);
		}else {
			$shopitemlist =  array();
			$server = $pubdb->get_one('test=0 and open=1 and combined_to=0', '*');
			if($server['db_server'] && $server['db_root'] && $server['db_pwd'] && $server['db_name']) {
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
				$getdb = new getdb_model($dbconfig, 'game');
				$getdb->tablepre = '';
				$getdb->table_name = 'online_shop_advertisement';
				$shopitemlist = $getdb->select();
			}
			include template('operation', 'shopad');
		}
	}
}