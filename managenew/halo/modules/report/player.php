<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class player extends admin {
	private $block_player_log, $block_player_info;
	function __construct(){
		parent::__construct();
		$this->block_player_log  = 'player_log';
		$this->block_player_info = 'player_info';
	}

	public function init(){
		$data['type'] = isset($_GET['type']) ? 'list' : '';
		$data['url']['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

		$roleid = $_SESSION['roleid'];
		//判断是否限制字段， 除非管理员及开发外，其他都受限制
		$data['limit'] = false;
		if ($roleid > 2){
			$data['limit'] = true;
		}
		
		$data['url']['m']   = ROUTE_M;
		$data['url']['v']   = ROUTE_V;
		$data['url']['c']   = ROUTE_C;
		include template('report', 'player');
	}
	/**
	 * 玩家信息列表
	 * 
	 */ 
	public function detail_list(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v='.ROUTE_V.'&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		
		$url1 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl1);
		$url2 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl2);
		$url3 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl3);
		$url4 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl4);
		
		include template('report', 'player_list');
	}
	/**
	 * 帮派
	 * @return [type] [description]
	 */
	public function faction(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl5 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];

		$url1 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl1);
		$url2 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl2);
		$url3 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl3);
		$url4 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl4);
		$url5 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl5);

		include template('report', 'player_faction');
	}
	/**
	 * 竞技
	 * @return [type] [description]
	 */
	public function arena(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl5 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];

		$url1 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl1);
		$url2 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl2);
		$url3 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl3);
		$url4 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl4);
		$url5 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl5);

		include template('report', 'player_arena');
	}
	/**
	 * 游戏记录
	 */
	public function gamelog(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl5 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];

		$url1 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl1);
		$url2 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl2);
		$url3 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl3);
		$url4 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl4);
		$url5 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl5);


		$blockdb = common::load_model('block_model');
		$logblock  = $blockdb->get_one(array('key' => $this->block_player_log), 'bid, key');
		//玩家记录
		if ($logblock['bid'] > 0){
			$where = '';
			if ($_SESSION['roleid'] > 2) {
				$privdb = common::load_model('priv_model');
				$r =$privdb->get_one(array('m'=>'report','c'=>'pay','v'=>'log','roleid'=>$_SESSION['roleid']));
				if (!$r) {
					$where = " AND `key` not in ('pay')";
				}
			}
			$blocklist = $blockdb->select("parentid=".$logblock['bid']."".$where);
			if (!empty($server['server_ver'])){
				$blocklist1 = $blockdb->select("parentid='".$logblock['bid']."' AND version >= '".$server['server_ver']."'".$where);
				$blocklist = array_merge($blocklist,$blocklist1);
				unset($blocklist1);
			}
		}

		include template('report', 'player_gamelog');
	}

	public function gamewar() {
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';
		$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		$weburl5 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
		
		$url1 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl1);
		$url2 = WEB_URL.INDEX.'#app=5&cpp=24&cpp=24&url='.urlencode($weburl2);
		$url3 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl3);
		$url4 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl4);
		$url5 = WEB_URL.INDEX.'#app=5&cpp=24&url='.urlencode($weburl5);

		$serverdb  = common::load_model('public_model');
		$serverdb->table_name = 'servers';
		$server = $serverdb->get_one(array('sid' => $data['sid']), 'db_server,db_root,db_pwd,db_name,server_ver');
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
			$getdb = new getdb_model($dbconfig, 'game');
			$getdb->table_name = 'player_world_war_x';
			$sky_warlist = $getdb->select('`group`=2', 'world_war_type, win_player_id');
			$ground_warlist = $getdb->select('`group`=1', 'world_war_type, win_player_id');

			$player = array();
			if ($sky_warlist) {
				foreach ($sky_warlist as $key => $value) {
					$player[] = $value['win_player_id'];
					switch ($value['world_war_type']) {
						case 10: $sky_war[10][] = $value['win_player_id'];	break;
						case 8:  $sky_war[8][] = $value['win_player_id'];	break;
						case 7:  $sky_war[7][] = $value['win_player_id'];	break;
						case 6:  $sky_war[6][] = $value['win_player_id'];	break;
						case 5:  $sky_war[5][] = $value['win_player_id'];	break;
						case 4:  $sky_war[4][] = $value['win_player_id'];	break;
					}
				}

				$sky_war[4] = array_diff($sky_war[4], $sky_war[5]);
				$sky_war[5] = array_diff($sky_war[5], $sky_war[6]);
				$sky_war[6] = array_diff($sky_war[6], $sky_war[7]);
				$sky_war[7] = array_diff($sky_war[7], $sky_war[8);
				$sky_war[8] = array_diff($sky_war[8], $sky_war[10]);
				krsort($sky_war);
			}
			
			if ($ground_warlist) {
				foreach ($ground_warlist as $key => $value) {
					$player[] = $value['win_player_id'];
					switch ($value['world_war_type']) {
						case 10: $gr_war[10][] = $value['win_player_id'];	break;
						case 8:  $gr_war[8][] = $value['win_player_id'];	break;
						case 7:  $gr_war[7][] = $value['win_player_id'];	break;
						case 6:  $gr_war[6][] = $value['win_player_id'];	break;
						case 5:  $gr_war[5][] = $value['win_player_id'];	break;
						case 4:  $gr_war[4][] = $value['win_player_id'];	break;
					}
				}

				$gr_war[4] = array_diff($gr_war[4], $gr_war[5]);
				$gr_war[5] = array_diff($gr_war[5], $gr_war[6]);
				$gr_war[6] = array_diff($gr_war[6], $gr_war[7]);
				$gr_war[7] = array_diff($gr_war[7], $gr_war[8);
				$gr_war[8] = array_diff($gr_war[8], $gr_war[10]);

				krsort($gr_war);
			}

			if ($player) {
				$playerlist = $getdb->select('id IN ('.implode($player, ',').')', 'id, username, nickname, is_tester, vip_level');
				foreach ($playerlist as $key => $value) {
					$list[$value['id']] = $value;
				}
			}
			$ranking = array(
				4 => '32强',
				5 => '16强',
				6 => '8强',
				7 => '4强',
				8 => '亚军',
				10 => '冠军',
			);
		}
		include template('report', 'player_gamewar');
	}

	/**
	 * 玩家详情
	 * 
	 */ 
	public function detail_info(){
		$data['id']  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$sid         = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$playername  = isset($_GET['playername']) ? trim($_GET['playername']) : '';
		$data['title'] = isset($_GET['sname']) ? trim($_GET['sname']) : '';
		$data['sid'] = $sid;
		$block = $block1 = array();
		if ($sid > 0) {
			$blockdb = common::load_model('block_model');
			
			$infoblock = $blockdb->get_one(array('key' => $this->block_player_info), 'bid,key');
			$logblock  = $blockdb->get_one(array('key' => $this->block_player_log), 'bid, key');

			//获得版本号
			$serverdb  = common::load_model('public_model');
			$serverdb->table_name = 'servers';
			$server = $serverdb->get_one(array('sid' => $sid), 'name, api_server,api_port,api_pwd, server_ver');
			$data['title'] = empty($data['title']) ? $data['title'] : $server['name'];
			$data['version'] = $server['server_ver'];
			if ($data['id'] <= 0 && !empty($playername)) {
				$api_admin = common::load_api_class('api_admin', $data['version']);
				if ($api_admin !== false) {
					$api_admin::$SERVER    = $server['api_server'];
					$api_admin::$PORT      = $server['api_port'];
					$api_admin::$ADMIN_PWD = $server['api_pwd'];
					$player = $api_admin::get_nickname_by_username($playername);
					$data['id'] = $player['player_id'];

					unset($api_admin);
				}
			}
			
			//玩家信息
			if ($infoblock['bid'] > 0){
				$blocklist = $blockdb->select(array('parentid'=>$infoblock['bid']));
				if (!empty($server['server_ver'])){
					$blocklist1 = $blockdb->select("parentid='".$infoblock['bid']."' AND version >= '".$server['server_ver']."'");
					$blocklist = array_merge($blocklist,$blocklist1);
					unset($blocklist1);
				}
			}
			if (!empty($blocklist)){
				foreach ($blocklist as $key => $value) {
					if ($_SESSION['roleid'] > 2 && in_array($value['key'], array('key')))	continue;
					$block[$value['key']]['name']    = $value['bname'];
					$block[$value['key']]['key']     = $value['key'];
					$block[$value['key']]['block']   = $infoblock['key'];
				}
				unset($blocklist);
			}
			$block = array_values($block);
			//玩家记录
			if ($logblock['bid'] > 0){
				$blocklist = $blockdb->select(array('parentid'=>$logblock['bid']));
				if (!empty($server['server_ver'])){
					$blocklist1 = $blockdb->select("parentid='".$logblock['bid']."' AND version >= '".$server['server_ver']."'");
					$blocklist = array_merge($blocklist,$blocklist1);
					unset($blocklist1);
				}
			}
			if (!empty($blocklist)){
				foreach ($blocklist as $key => $value) {
					if ($_SESSION['roleid'] > 2 && in_array($value['key'], array('pay'))) {
						$privdb = common::load_model('priv_model');
						$r =$privdb->get_one(array('m'=>'report','c'=>'pay','v'=>'log','roleid'=>$_SESSION['roleid']));
						if (!$r)	continue;
					}
					$block1[$value['key']]['name']    = $value['bname'];
					$block1[$value['key']]['key']     = $value['key'];
					$block1[$value['key']]['block']     = $logblock['key'];
				}
				unset($blocklist);
			}
			$block1 = array_values($block1);
		}
		
		$data['blockloglist'] = json_encode($block1);
		$data['blockinfolist'] = json_encode($block);
		$data['blocklog']  = $this->block_player_log;
		$data['blockinfo'] = $this->block_player_info;
		unset($block, $block1);

		$loadflag = common::load_api_template('player_info', $version);
		if ($loadflag === false){
			include template('report', 'player_info');
		}
	}
	/**
	 * 玩家各种游戏记录对应的模板
	 * 
	 */
	public function record(){
		$key         = isset($_GET['key']) ? trim(safe_replace($_GET['key'])) : '';
		$version     = isset($_GET['version']) ? trim(safe_replace($_GET['version'])) : '';
		$data['id']  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['player_role_id'] = isset($_GET['player_role_id']) ? intval($_GET['player_role_id']) : 0;

		if ($key == 'pay'){
			include template('report', 'pay');
			return ;
		}
		$loadflag = common::load_api_template($key, $version);
		if ($loadflag === false){
			include template('player', 'log_'.$key, 'block');
		}
	}
	/**
	 * 玩家各种信息对应的模板
	 * 
	 */ 
	public function info(){
		$key         = isset($_GET['key']) ? trim(safe_replace($_GET['key'])) : '';
		$version     = isset($_GET['version']) ? trim(safe_replace($_GET['version'])) : '';
		$data['id']  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['player_role_id'] = isset($_GET['player_role_id']) ? intval($_GET['player_role_id']) : 0;

		$loadflag = common::load_api_template($key, $version);
		if ($loadflag === false){
			include template('player', $key, 'block');
		}
	}
}
