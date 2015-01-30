<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class source extends admin {
	private $tmpldb;
	function __construct(){
		parent::__construct();
		$this->tmpldb = common::load_model('template_model');
	}

	public function init(){
		$data['key'] = isset($_GET['data']) ? trim($_GET['data']) : '';

		include template('operation', 'source');
	}
	/**
	 * 游戏设置
	 * @return [type] [description]
	 */
	public function game() {
		if (isset($_POST['doSubmit'])) {
			$sid  = isset($_POST['sid']) > 0 ? $_POST['sid'] : array();
			$cid  = isset($_POST['cid']) > 0 ? intval($_POST['cid']) : 0;
			//Boss
			$level = isset($_POST['level']) ? intval($_POST['level']) : 0;
			$world_boss_id = isset($_POST['world_boss_id']) ? intval($_POST['world_boss_id']) : 0;
			$bossoptype = isset($_POST['bossoptype']) ? intval($_POST['bossoptype']) : 0;
			//帮派战
			$faction_war_id = isset($_POST['faction_war_id']) ? intval($_POST['faction_war_id']) : 0;
			$factionoptype = isset($_POST['factionoptype']) ? intval($_POST['factionoptype']) : 0;
			//阵营战
			$campoptype = isset($_POST['campoptype']) ? intval($_POST['campoptype']) : -1;
			//魔王
			$optype = isset($_POST['optype']) ? intval($_POST['optype']) : -1;
			
			parent::check_pf_priv('company', $cid);
			if ($cid > 0 && count($sid) > 0) {
				$serverdb  = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$msg = array();
				foreach ($sid as $key => $value) {
					$server = $serverdb->get_one(array('sid' => $value), 'name,o_name,api_server,api_port,api_pwd,server_ver');
					if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])) {
						$version = trim($server['server_ver']);
						$api_admin = common::load_api_class('api_admin', $version);
						if ($api_admin !== false) {
							$api_admin::$SERVER    = $server['api_server'];
							$api_admin::$PORT      = $server['api_port'];
							$api_admin::$ADMIN_PWD = $server['api_pwd'];
							$servername = $server['name'].'-'.$server['o_name'];
							if ($world_boss_id > 0 && $level > 0 && method_exists($api_admin, 'set_world_boss_level')) {
								$level = $level - 30;
								$callback = call_user_func_array(array($api_admin, 'set_world_boss_level'), array($world_boss_id, $level));
								if ($callback['result'] == 1) {
									$msg[] = $servername.'  设置Boss等级成功！';
								}else {
									$msg[] = $servername.'  设置Boss等级失败！';
								}
							}
							if ($world_boss_id > 0 && $bossoptype > 0) {
								if ($bossoptype == 1 && method_exists($api_admin, 'open_world_boss')) {
									$callback = call_user_func_array(array($api_admin, 'open_world_boss'), array($world_boss_id));
								}else if (method_exists($api_admin, 'close_world_boss')) {
									$callback = call_user_func_array(array($api_admin, 'close_world_boss'), array($world_boss_id));
								}
								if ($callback['result'] == 1) {
									$msg[] = $servername.($bossoptype == 1 ? '  Boss挑战开启成功！' : '  Boss挑战关闭成功！');	
								}else {
									$msg[] = $servername.($bossoptype == 1 ? '  Boss挑战开启失败！' : '  Boss挑战关闭失败！');
								}
							}

							if ($faction_war_id > 0 && $factionoptype > 0) {
								if ($factionoptype == 1 && method_exists($api_admin, 'open_faction_war')) {
									$callback = call_user_func_array(array($api_admin, 'open_faction_war'), array($world_boss_id));
								}else if (method_exists($api_admin, 'close_faction_war')) {
									$callback = call_user_func_array(array($api_admin, 'close_faction_war'), array($world_boss_id));
								}
								if ($callback['result'] == 1) {
									if ($callback['result'] == 1) {
										$msg[] = $servername.($bossoptype == 1 ? '  帮派战开启成功！' : '  帮派战关闭成功！');
									}else {
										$msg[] = $servername.($bossoptype == 1 ? '  帮派战开启失败！' : '  帮派战关闭失败！');
									}
								}
							}

							if ($campoptype >= 0 && method_exists($api_admin, 'control_camp_war')) {
								$callback = call_user_func_array(array($api_admin, 'control_camp_war'), array($campoptype));
								if ($callback['result'] == 1) {
									$msg[] = $servername.($bossoptype == 1 ? '  阵营战开启成功！' : '  阵营战关闭成功！');
								}else {
									$msg[] = $servername.($bossoptype == 1 ? '  阵营战开启失败！' : '  阵营战关闭失败！');
								}
							}

							if ($optype >= 0) {

							}

							$content['content']  = implode($msg, PHP_EOL);
							$content['key']      = 'game_setting';
							$content['sid']      = $value;
							$content['playerid'] = 0;
							parent::op_log($content, 'source');
						}
					}
				}
				$msg = implode($msg, '<br>');
				output_json(0, $msg);
			}
			output_json(1, '');
		}else {
			include template('operation', 'source_game');
		}
	}
	/**
	 * 送资源
	 * 
	 */ 
	public function give(){
		if (isset($_POST['doSubmit'])){
			if (isset($_POST['seconds']))	$_POST['seconds'] = intval($_POST['seconds']) * 60;
			$info = $_POST;
			$sid  = intval($_POST['sid']) > 0 ? intval($_POST['sid']) : 0;
			$cid  = intval($_POST['cid']) > 0 ? intval($_POST['cid']) : 0;
			$tid  = intval($_POST['tid']) > 0 ? intval($_POST['tid']) : 0;
			$func = isset($_POST['key']) ? trim(safe_replace($_POST['key'])) : '';
			parent::check_pf_priv('server', $cid, $sid);

			if ($sid > 0 && !empty($func) && $tid > 0) {
				$tmpl = $this->tmpldb->get_one(array('tid'=>$tid));
				$targs = unserialize($tmpl['args']);

				$serverdb  = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$server = $serverdb->get_one(array('sid' => $sid), 'name,o_name,api_server,api_port,api_pwd,server_ver');
				if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])){
					$version = trim($server['server_ver']);
					$api_admin = common::load_api_class('api_admin', $version);
					if ($api_admin !== false && method_exists($api_admin, $func)){
						$api_admin::$SERVER    = $server['api_server'];
						$api_admin::$PORT      = $server['api_port'];
						$api_admin::$ADMIN_PWD = $server['api_pwd'];
						
						$content['playername']  = $content['playernickname']  = '';
						//通过昵称找玩家ID
						if (intval($info['player_type']) == 2){
							$player  = $api_admin::find_player_by_nickname($info['player']);
							$player1 = $api_admin::get_username_by_nickname($info['player']);
							$content['playername']     = $player1['username'][1];
							$content['playernickname'] = $info['player'];
						}else {
						//通过玩家名称找玩家ID
							$player = $api_admin::get_nickname_by_username($info['player']);
							$content['playername']     = $info['player'];
							$content['playernickname'] = $player['nickname'][1];
						}
						
						if ($player['result'] == 1){
							$playerid = $player['player_id'];
							$args[] = $playerid;
							foreach ($targs as $key => $value) {
								if (array_key_exists($value['arg'], $info) && $value['arg'] != 'key'){
									if ($func == 'give_soul' && stripos($value['arg'], 'attributevalue') !== false) {
										$info[$value['arg']] = $info[$value['arg']] * 10;
									}
									$args[] = $info[$value['arg']];

									if (is_array($info[$value['arg']])) {
										$loginfo[] = $value['arg'].' = '.array2string($info[$value['arg']]);
									}else {
										$loginfo[] = $value['arg'].' = '.$info[$value['arg']];
									}
								}
							}

							if ($func == 'give_soul') {
                                $attributekey = 0;
								for($i=1; $i<=4; $i++) {
									if (isset($_POST['attributeid'.$i]) && intval($_POST['attributeid'.$i]) > 0) {
										$attributekey += 1;
									}
								}
								$args[] = $attributekey;
								$loginfo[] = 'key = '.$attributekey;
							}
							$callback = call_user_func_array(array($api_admin, $func), $args);
							$content['content']  = Lang('log_op_reason').$_POST['reason'];
							$content['key']      = $func;
							$content['sid']      = $sid;
							$content['playerid'] = $playerid;
							if ($func == 'give_item' && $callback['success_number'] > 0) {
								$callback['result'] = 1;
							}
							if ($callback['result'] == 1){
								if ($func == 'set_player_vip_level') {
									$charge_ingot  = isset($_POST['ingot ']) && intval($_POST['ingot ']) > 0 ? intval($_POST['ingot ']) : 0;
									$is_tester = isset($_POST['is_tester']) && intval($_POST['is_tester']) > 0 ? intval($_POST['is_tester']) : 0;
									if ($charge_ingot  > 0) {
										$callback = call_user_func_array(array($api_admin, 'system_send_ingot'), array($playerid, $charge_ingot ));
										if ($callback['result'] == 1) {
											$loginfo[] = 'ingot  = '.$charge_ingot ;
										}
									}
									if ($is_tester > 0)	{
										$callback = call_user_func_array(array($api_admin, 'set_tester'), array($playerid, $is_tester));
										if ($callback['result'] == 1) {
											$loginfo[] = 'is_tester = '.$is_tester;
										}
									}
								}

								$content['content'] = Lang($func).' '.Lang('success').PHP_EOL
													.$content['content'].PHP_EOL
													.implode($loginfo, '，');

								parent::op_log($content, 'source');
								output_json(0, Lang('success'));
							}
							output_json(1, Lang('error'), $args);
						}

						output_json(1, Lang('player_no_exist'));
					}
				}
			}
			output_json(1, Lang('error'));
		}
	}

	/**
	 * 批量赠送
	 * @return [type] [description]
	 */
	public function give_more(){
		$ingot = isset($_POST['ingot']) ? intval($_POST['ingot']) : 0;
		$coins = isset($_POST['coins']) ? intval($_POST['coins']) : 0;
		$vip_level = isset($_POST['vip_level']) ? intval($_POST['vip_level']) : 0;
		$op_type = isset($_POST['op_type']) ? intval($_POST['op_type']) : 0;
		$sid = isset($_POST['sid']) ? $_POST['sid'] : 0;
		$cid = isset($_POST['cid']) ? $_POST['cid'] : 0;

		if (!is_array($sid)){
			unset($sid);
			$sid[] = intval($_POST['sid']);
		}
		if (!is_array($cid)){
			unset($cid);
			$cid[] = intval($_POST['cid']);
		}
		$starttime = time();
		//取消测试号
		if (count($sid) > 0 && count($cid) > 0){
			foreach ($sid as $key => $value) {
				$svalue = intval($value);
				$cvalue = intval($cid[$key]);
				if ($svalue > 0 && $cvalue > 0){
					$serverdb  = common::load_model('public_model');

					$serverdb->table_name = 'ho_pf_game_tester';
					$info = $serverdb->get_one(array('cid'=>$cvalue, 'sid'=>$svalue));
					if (!$info) continue;
					$testerlist = unserialize($info['testers']);
					$serverdb->table_name = 'servers';
					$server = $serverdb->get_one(array('sid' => $svalue), 'name,o_name,api_server,api_port,api_pwd,server_ver');
					if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])){
						$version = trim($server['server_ver']);
						$api_admin = common::load_api_class('api_admin', $version);
						if ($api_admin !== false && (method_exists($api_admin, 'set_tester') || method_exists($api_admin, 'set_player_vip_level'))){
							$api_admin::$SERVER    = $server['api_server'];
							$api_admin::$PORT      = $server['api_port'];
							$api_admin::$ADMIN_PWD = $server['api_pwd'];
							
							$i = 0;
							foreach ($testerlist as $tk => $tester) {
								if ($op_type == 2){
									$rtn = call_user_func_array(array($api_admin, 'set_tester'), array($tester['id'], 0));
									if ($rtn['result'] == 1) $i++;
								}else {
									if ($vip_level >= 0) call_user_func_array(array($api_admin, 'set_player_vip_level'), array($tester['id'], $vip_level));
									if ($ingot > 0)		 call_user_func_array(array($api_admin, 'increase_player_ingot'), array($tester['id'], $ingot));
									if ($coins > 0) 	 call_user_func_array(array($api_admin, 'increase_player_coins'), array($tester['id'], $coins));
								}
							}
							if ($i > 0 && $op_type == 2){
								$serverdb->table_name = 'ho_pf_game_tester';
								$serverdb->delete(array('cid'=>$cvalue, 'sid'=>$svalue));
							}
						}
					}
				}
			}
			$endtime = time();
			$data['total'] = $endtime - $starttime;

			output_json(0, Lang('success'), $data);
		}

		output_json(1, Lang('error'));
 	}
}
