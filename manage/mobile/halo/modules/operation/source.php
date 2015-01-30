<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class source extends admin {
	private $tmpldb;
	function __construct(){
		parent::__construct();
		$this->tmpldb = common::load_model('template_model');
	}

	public function init(){	
		$data['key'] = isset($_GET['data']) ? trim($_GET['data']) : '';
		$key = $data['key'];
		if (admin::has_priv('','','','&data='.$key)){
		$tmpldb = common::load_model('template_model');
		$tmpllist = $tmpldb->select(array('key' => $key), 'tid, version, content');

		$sourcetmplArr = array();
		if (count($tmpllist) >= 1){
			foreach ($tmpllist as $key => $value) {
				$sourcetmplArr = $value;
			}
		}else {
			$sourcetmplArr = $tmpllist[0];
		}

		$data['tid'] = $sourcetmplArr['tid'];

		$sourcetmpl = $sourcetmplArr['content'] ? $sourcetmplArr['content'] : '';
		unset($sourcetmplArr);
		include template('operation', 'source');
		}else{
			admin::exitHtml(0);
		}
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
									$callback = call_user_func_array(array($api_admin, 'open_faction_war'), array($faction_war_id));
								}else if (method_exists($api_admin, 'close_faction_war')) {
									$callback = call_user_func_array(array($api_admin, 'close_faction_war'), array($faction_war_id));
								}
								if ($callback['result'] == 1) {
									$msg[] = $servername.($factionoptype == 1 ? '  帮派战开启成功！' : '  帮派战关闭成功！');
								}else {
									$msg[] = $servername.($factionoptype == 1 ? '  帮派战开启失败！' : '  帮派战关闭失败！');
								}
							}
							if ($campoptype!=2 && method_exists($api_admin, 'control_camp_war')) {
								$callback = call_user_func_array(array($api_admin, 'control_camp_war'), array($campoptype));
								if ($callback['result'] == 1) {
									$msg[] = $servername.($campoptype == 1 ? '  阵营战开启成功！' : '  阵营战关闭成功！');
								}else {
									$msg[] = $servername.($campoptype == 1 ? '  阵营战开启失败！' : '  阵营战关闭失败！');
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
		$key = isset($_POST['key']) ? trim(safe_replace($_POST['key'])) : '';
		if (admin::has_priv('operation','init','source','&data='.$key)){
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
				$serverdb = common::load_model('public_model');
				$serverdb->table_name = 'servers';
				$server = $serverdb->get_one(array('sid' => $sid), 'name,o_name,api_server,api_port,api_pwd,server_ver');
				if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])){
					$version = trim($server['server_ver']);
					$api_admin = common::load_api_class('api_admin', $version);
					if ($api_admin !== false && method_exists($api_admin, $func)){
						$api_admin::$SERVER    = $server['api_server'];
						$api_admin::$PORT      = $server['api_port'];
						$api_admin::$ADMIN_PWD = $server['api_pwd'];
						$playername = $info['player'];
						$sdb = $serverdb->set_db($sid);
						if ($info['player_type'] == 2){
							$param = "nickname='$playername'"; 
						}
						if ($info['player_type'] == 1){
							$param = "username='$playername'"; 
						}
						$playerinfo = $serverdb->get_player_id($sid,$param);
						$playerid = $playerinfo['id'];
						$nickname = $playerinfo['nickname'];
						$username = $playerinfo['username'];
						if ($playerid>0){
							$args = array();
							$args[] = $playerid;
							foreach ($targs as $key => $value) {
								if (array_key_exists($value['arg'], $info) && $func != 'give_role'){
									$args[] = $info[$value['arg']];
								}
							}
							if ($func == 'give_role'){
								$hero_array = $info['hero_id'];
								$jue_xing = $info['jue_xing'];
								$star_lv = $info['star_lv'];
								$sdb->table_name = 'role';
								foreach ($hero_array as $hk => $hv){
									$hero_id = $hv;
									$role = $sdb->get_one(array('hero_id' => $hero_id,'jue_xing' => $jue_xing,'xin_ji' => $star_lv), 'id');
									$args = array();
									$args[] = $playerid;
									$args[] = $role['id'];
									$role_info = $sdb->get_row("select c.text from hero b,chinese_text c where  b.id=".$hero_id." and b.name_text_id=c.id");
									$role_name = $role_info['text'];
									$callback = call_user_func_array(array($api_admin, $func), $args);
									$content['content']  = Lang('log_op_reason').$_POST['reason'];
									$content['key']      = $func;
									$content['sid']      = $sid;
									$content['playerid'] = $playerid;
									$content['playernickname'] = $nickname;
									$content['playername']     = $username;
									$loginfo = "伙伴:".$role_name;
									if ($callback['result'] == 1){
										$content['content'] = Lang($func).' '.Lang('success').PHP_EOL
															.$content['content'].PHP_EOL
															.$loginfo;
										parent::op_log($content, 'source');
										$msg[] = '赠送伙伴:'.$role_name.'成功';
									}else{
										$content['content'] = Lang($func).' '.Lang('error').PHP_EOL
															.$content['content'].PHP_EOL
															.$loginfo;
										parent::op_log($content, 'source');
										$msg[] = '赠送伙伴:'.$role_name.'失败';
										$false = 1;
									}
								}
								$msg = implode($msg, '<br>');
								output_json($false?1:0, $msg);
							}elseif ($func == 'delete_role'){
								foreach ($info['player_role_ids'] as $k => $v){
									$args = array();
									$args[] = $playerid;
									$args[] = $v['id'];
									$role_info = $sdb->get_row("select c.text from role a,hero b,chinese_text c where a.id=".$v['id']." and a.hero_id=b.id and b.name_text_id=c.id");
									$role_name = $role_info['text'];
									$callback = call_user_func_array(array($api_admin, $func), $args);
									$content['content']  = Lang('log_op_reason').$_POST['reason'];
									$content['key']      = $func;
									$content['sid']      = $sid;
									$content['playerid'] = $playerid;
									$content['playernickname'] = $nickname;
									$content['playername']     = $username;
									$loginfo = "伙伴:".$role_name;
									if ($callback['result'] == 1){
										$content['content'] = Lang($func).' '.Lang('success').PHP_EOL
															.$content['content'].PHP_EOL
															.$loginfo;
										parent::op_log($content, 'source');
										$msg[] = '删除伙伴:'.$role_name.'成功';
									}else{
										$content['content'] = Lang($func).' '.Lang('error').PHP_EOL
															.$content['content'].PHP_EOL
															.$loginfo;
										parent::op_log($content, 'source');
										$msg[] = '删除伙伴:'.$role_name.'失败';
										$false = 1;
									}
								}
								$msg = implode($msg, '<br>');
								output_json($false?1:0, $msg);
							}else{
								$callback = call_user_func_array(array($api_admin, $func), $args);
								$content['content']  = Lang('log_op_reason').$_POST['reason'];
								$content['key']      = $func;
								$content['sid']      = $sid;
								$content['playerid'] = $playerid;
								$content['playernickname'] = $nickname;
								$content['playername']     = $username;
								if ($callback['result'] == 1){
									$content['content'] = Lang($func).' '.Lang('success').PHP_EOL
														.$content['content'].PHP_EOL;
	
									parent::op_log($content, 'source');
									output_json(0, Lang('success'));
								}
							}
							output_json(1, Lang('error'), $args);
						}

						output_json(1, Lang('player_no_exist'));
					}
				}
			}
			output_json(1, Lang('error'));
		}
		}else{
			output_json(1, Lang('no_permission'));
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
 	public function hire_role (){
 		$pubdb = common::load_model('public_model');
		$pubdb->table_name = 'servers';
 		if (isset($_POST['doSubmit'])) {
 			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
 			$servers = isset($_POST['sid']) ? ext_addslashes($_POST['sid']) : array();
 			$partnerlist = isset($_POST['partners']) ? ext_addslashes($_POST['partners']) : array();
 			$starttime = isset($_POST['starttime']) ? strtotime($_POST['starttime']) : 0;
 			$endtime = isset($_POST['endtime']) ? strtotime($_POST['endtime']) : 0;
 			$partners  = array();
			foreach ($partnerlist as $key => $value) {
				$partners[]['role_id'] = $value;
			}

			if (!$servers) output_json(1, Lang('not_selected_company_or_server'));
			if (!$partners)  output_json(1, Lang('not_selected_hire_role'));
			if ($starttime == 0 || $endtime==0)  output_json(1, Lang('not_selected_between_date'));

			$sids = implode(",",$servers);
			$serverlist = $pubdb->select("sid IN ($sids)", 'sid, name, server_ver, api_server, api_port, api_pwd');
			foreach ($serverlist as $key => $srs) {
				$api_admin = common::load_api_class('api_admin', $srs['server_ver']);
				if ($api_admin !== false) {
					$api_admin::$SERVER = $srs['api_server'];
			        $api_admin::$PORT   = $srs['api_port'];
			        $api_admin::$ADMIN_PWD   = $srs['api_pwd'];
			        $msg = $api_admin::publish_hire_role($partners,$starttime,$endtime);
			        if($msg['result'] == 1) {
			            $outmsg .= $srs['name'].' - OK!<br>';
			        }else{
			            $outmsg .= $srs['name'].' - ERR!<br>';
			        }
				}
			}
			output_json(0, $outmsg);
 		}else{
 			$serverdb  = common::load_model('public_model');
			$this->getdb = $serverdb->set_db(DEFAULT_SID);
			$this->getdb->table_name  = 'role';
			$typelist = $this->getdb->select('role_type=3', 'id,name');	
			include template('operation', 'hire_role');
 		}
 	}
 	/**
	 * 发放补偿
	 */
	public function buchang() {
		if (isset($_POST['doSubmit'])) {
			$sid  = isset($_POST['sid']) > 0 ? $_POST['sid'] : array();
			$cid  = isset($_POST['cid']) > 0 ? intval($_POST['cid']) : 0;
			$msginfo = isset($_POST['msg']) ? $_POST['msg'] : 0;
			$ver = isset($_POST['verinfo']) ? intval($_POST['verinfo']) : 0;
			$minlv = isset($_POST['minlv']) ? intval($_POST['minlv']) : 0;
			$maxlv = isset($_POST['maxlv']) ? intval($_POST['maxlv']) : 0;
			$ingot = isset($_POST['ingot']) ? intval($_POST['ingot']) : 0;
			$coin = isset($_POST['coin']) ? intval($_POST['coin']) : 0;
			$fame = isset($_POST['fame']) ? intval($_POST['fame']) : 0;
			$skill = isset($_POST['skill']) ? intval($_POST['skill']) : 0;
			$feat = isset($_POST['feat']) ? intval($_POST['feat']) : 0;
			$item1 = isset($_POST['item1']) ? intval($_POST['item1']) : 0;
			$item2 = isset($_POST['item2']) ? intval($_POST['item2']) : 0;
			$item3 = isset($_POST['item3']) ? intval($_POST['item3']) : 0;
			$num1 = isset($_POST['num1']) ? intval($_POST['num1']) : 0;
			$num2 = isset($_POST['num2']) ? intval($_POST['num2']) : 0;
			$num3 = isset($_POST['num3']) ? intval($_POST['num3']) : 0;
			
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
							if (method_exists($api_admin, 'buchang')) {
								$callback = call_user_func_array(array($api_admin, 'buchang'), array($ver, $minlv, $maxlv, $msginfo, $ingot, $coin, $fame, $skill, $feat, $item1, $num1, $item2, $num2, $item3, $num3));
								if ($callback['result'] == 1) {
									$msg[] = $servername.'  发放补偿成功！';
									$content['content'] = $servername.'  发放补偿成功！';
								}else {
									$msg[] = $servername.'  发放补偿失败！';
									$content['content'] = $servername.'  发放补偿失败！';
								}
							}
							$content['key']      = 'buchang_setting';
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
			include template('operation', 'source_buchang');
		}
	}
}
