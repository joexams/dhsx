<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class player extends admin {
	private $pubdb, $getdb, $block_player_log, $block_player_info, $sql, $backSql;
	private $sid, $cid;
	function __construct(){
		parent::__construct();
		$this->block_player_log  = 'player_log';
		$this->block_player_info = 'player_info';
		$this->pubdb = common::load_model('public_model');

		$this->sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$this->cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
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
	public function player_list() {
		$data['sid'] = $this->sid;
		$data['cid'] = $this->cid;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		$limit = 0;
		if ($_SESSION['roleid'] > 3)    $limit = 1;
		include template('report', 'player_list');
	}
	/**
	 * 帮派
	 * @return [type] [description]
	 */
	public function faction() {
		$sid = $data['sid'] = $this->sid;
		if (isset($_GET['dogetSubmit']) && intval($_GET['dogetSubmit']) > 0) {
			$this->getdb = $this->pubdb->set_db($sid);

			if ($this->getdb !== false){
				$level = isset($_GET['level']) ? intval($_GET['level']) : 0;
				$name = isset($_GET['name']) && !empty($_GET['name']) ? safe_replace(trim($_GET['name'])) : '';
				$wherestr = '';
				if ($level > 0) {
					$wherestr = 'level>='.$level;
				}
				if (!empty($name)) {
					$wherestr .= !empty($wherestr) ? " AND a.name LIKE '%$name%'" : "a.name LIKE '%$name%'";
				}
				$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : '';
				$sql = "SELECT a.id,camp_id,a.name,level,member_count,coins,description,master_name,notice,exp,group_number,god_level,god_exp,now_week_con,today_con,b.name as campname
						FROM player_faction a 
						LEFT JOIN camp b ON a.camp_id=b.id 
						$wherestr
						ORDER BY level DESC, exp DESC, a.id DESC";
				$list = $this->getdb->get_list($sql);
				$data['count'] = count($list);
				$data['list']  = $list;
				output_json(0, '', $data);
			}

			output_json(1, Lang('error'));
		}
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';
		include template('report', 'player_faction');
	}
	/**
	 * 帮派成员
	 * @return [type] [description]
	 */
	public function faction_member()
	{
		$faction_id = isset($_GET['faction_id']) ? intval($_GET['faction_id']) : 0;
		$this->getdb = $this->pubdb->set_db($this->sid);

		if ($this->getdb === false)	output_json(1, Lang('error'));

		$this->getdb->table_name = 'player_faction_member';
		$list = $this->getdb->select(array('faction_id'=>$faction_id), 'id,player_id,add_time,contribution,today_con,last_con_time');

		$sql = "SELECT player_id,name FROM player_faction_job a LEFT JOIN faction_job b ON a.job_id=b.id WHERE faction_id='$faction_id'";
		$joblist = $this->getdb->get_list($sql);

		$playerids = $joblists = array();
		foreach ($list as $key => $value) {
			$playerids[] = $value['player_id'];
		}
		foreach ($joblist as $jkey => $jvalue) {
			$joblists[$jvalue['player_id']] = $jvalue['name'];
		}

		$this->getdb->table_name = 'player';
		$playerlist = $this->getdb->select('id IN ('.implode($playerids, ',').')', 'id,username,nickname');
		foreach ($playerlist as $pkey => $pvalue) {
			$playerlists[$pvalue['id']]['username'] = $pvalue['username'];
			$playerlists[$pvalue['id']]['nickname'] = $pvalue['nickname']; 
		}

		foreach ($list as $key => $value) {
			$list[$key]['jobname']  = $joblists[$value['player_id']];
			$list[$key]['username'] = $playerlists[$value['player_id']]['username'];
			$list[$key]['nickname'] = $playerlists[$value['player_id']]['nickname'];
		}
		
		$data['count'] = count($list);
		$data['list']  = $list;
		output_json(0, '', $data);
	}
	/**
	 * 帮派申请
	 * @return [type] [description]
	 */
	public function faction_apply()
	{
		$faction_id = isset($_GET['faction_id']) ? intval($_GET['faction_id']) : 0;
		$this->getdb = $this->pubdb->set_db($this->sid);

		if ($this->getdb === false)	output_json(1, Lang('error'));

		$this->getdb->table_name = 'player_faction_request';
		$list = $this->getdb->select(array('faction_id'=>$faction_id), 'id,player_id,req_time');

		if (count($list)){
			$playerids = array();
			foreach ($list as $key => $value) {
				$playerids[] = $value['player_id'];
			}

			$this->getdb->table_name = 'player';
			$playerlist = $this->getdb->select('id IN ('.implode($playerids, ',').')', 'id,username,nickname');
			foreach ($playerlist as $pkey => $pvalue) {
				$playerlists[$pvalue['id']]['username'] = $pvalue['username'];
				$playerlists[$pvalue['id']]['nickname'] = $pvalue['nickname']; 
			}
			
			foreach ($list as $key => $value) {
				$list[$key]['username'] = $playerlists[$value['player_id']]['username'];
				$list[$key]['nickname'] = $playerlists[$value['player_id']]['nickname'];
			}
		}
		$data['count'] = count($list);
		$data['list']  = $list;
		output_json(0, '', $data);
	}
	/**
	 * 竞技
	 * @return [type] [description]
	 */
	public function arena(){
		$data['sid'] = $this->sid;

		if (isset($_GET['format']) && $_GET['format'] == 'json') {
			$this->getdb = $this->pubdb->set_db($this->sid);
			if ($this->getdb === false)	output_json(1, Lang('error'));
			$sql = "SELECT id,username,nickname,is_tester,vip_level,ranking,last_ranking,challenged_times_today,last_challenge_time,buy_times_today,last_buy_time 
					FROM player_super_sport_ranking a 
					LEFT JOIN player_super_sport b ON a.player_id=b.player_id 
					LEFT JOIN player c ON a.player_id=c.id 
					ORDER BY ranking ASC limit 50";
			$list = $this->getdb->get_list($sql);
			$data['count'] = 50;
			$data['list']  = $list;
			output_json(0, '', $data);
		}

		$data['cid'] = $this->cid;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';
		include template('report', 'player_arena');
	}
	/**
	 * 游戏记录
	 */
	public function gamelog(){
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

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
	/**
	 * 数据报表
	 * @return [type] [description]
	 */
	public function gamereport()
	{
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';

		include template('report', 'player_gamereport');
	}
	/**
	 * 群仙会
	 * @return [type] [description]
	 */
	public function gamewar() {
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['cid'] = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$data['title'] = isset($_GET['title']) ? trim($_GET['title']) : '';
		
		$serverdb  = common::load_model('public_model');
		$serverdb->table_name = 'servers';
		$server = $serverdb->get_one(array('sid' => $data['sid']), 'combined_to, db_server,db_root,db_pwd,db_name,server_ver');
		if ($server['combined_to'] > 0) {
			$data['sid'] = $server['combined_to'];
			$server = $serverdb->get_one(array('sid' => $data['sid']), 'db_server,db_root,db_pwd,db_name,server_ver');
		}
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
				$sky_war = $sky_war[4] = $sky_war[5] = $sky_war[6] = $sky_war[7] = $sky_war[8] = $sky_war[10] = array();
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
				$sky_war[7] = array_diff($sky_war[7], $sky_war[8]);
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
				$gr_war[7] = array_diff($gr_war[7], $gr_war[8]);
				$gr_war[8] = array_diff($gr_war[8], $gr_war[10]);
				krsort($gr_war);
			}

			if ($player) {
				$getdb->table_name = 'player';
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
	public function player_info(){
		$data['id'] = $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$playername  = isset($_GET['playername']) ? trim($_GET['playername']) : '';
		$data['sid'] = $sid = $this->sid;

		$block = $block1 = array();
		if ($sid > 0) {
			$server = $this->pubdb->get_server($sid);
			$data['title'] = $server['name'];
			$data['version'] = $version = $server['server_ver'];
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

			$blockdb = common::load_model('block_model');
			//玩家信息
			$infolist = $blockdb->select(array('parentid'=>5));
			//玩家记录
			$loglist = $blockdb->select(array('parentid'=>1));
			
			$this->getdb = $this->pubdb->set_db($sid);
			if ($this->getdb !== false){
				$sql = "SELECT a.*,b.*,t.*,c.total_ingot,c.level_up_time,c.ingot AS ingot_vip,d.ranking,e.state_point,f.name AS factionname,g.peach_lv,h.barrier,h.zodiac_level FROM player a 
						LEFT JOIN player_data b ON a.id=b.player_id 
						LEFT JOIN player_charge_record c ON c.player_id=a.id 
						LEFT JOIN player_super_sport_ranking d ON d.player_id=a.id 
						LEFT JOIN player_state_point e ON e.player_id=a.id 
						LEFT JOIN player_faction f ON f.player_id=a.id 
						LEFT JOIN player_peach_data g ON g.player_id=a.id 
						LEFT JOIN player_zodiac_data h ON h.player_id=a.id 
						LEFT JOIN player_trace t ON t.player_id=a.id 
						WHERE a.id='$id'";
				$player = $this->getdb->get_list($sql);
				$baseinfo = $player[0];
				$baseinfo['peach_lv'] = 70+$baseinfo['peach_lv']*5;
				$data['info'] = $baseinfo;
				unset($player);

				$deploy_mode_id = $data['info']['deploy_mode_id'];
				$sql = 'SELECT a.*,b.*,c.level as spirit_lv,d.name as spirit_name,e.deploy_mode_id FROM player_role a 
						LEFT JOIN player_role_data b ON a.id=b.player_role_id 
						LEFT JOIN player_role_spirit_state c ON a.id=c.player_role_id 
						LEFT JOIN spirit_state d ON c.spirit_state_id=d.id 
						LEFT JOIN player_deploy_grid e ON a.id=e.player_role_id AND e.deploy_mode_id='.$deploy_mode_id.' 
						WHERE a.player_id='.$id.' ORDER BY state ASC,a.id ASC';
				$data['partner'] = $this->getdb->get_list($sql);
				foreach ($data['partner'] as $key => $value) {
					$data['player'][$value['id']] = $value['name'];
				}

				$this->getdb->table_name = 'role';
				$rolelist = $this->getdb->select('', 'id, name');
				foreach ($rolelist as $key => $value) {
					$data['role'][$value['id']] = $value['name'];
				}
				$this->getdb->table_name = 'deploy_mode';
				$deploy = $this->getdb->select('', 'id, name');
				foreach ($deploy as $key => $value) {
					$data['deploy'][$value['id']] = $value['name'];
				}
			}
		}
		
		$loadflag = common::load_api_template('player_info', $version);
		if ($loadflag === false){
			include template('report', 'player_info');
		}
	}
	/**
	 * 设置昵称
	 */
	public function set_nickname()
	{
		$player_id  = isset($_POST['player_id']) ? intval($_POST['player_id']) : 0;
		$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
		$nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
		$new_nickname = isset($_POST['new_nickname']) ? trim($_POST['new_nickname']) : '';
		
		if (empty($new_nickname) || $player_id <= 0 || $sid <= 0)	output_json(1, '昵称修改失败！');

		$apiadmin = $this->pubdb->get_server($sid, true);
		if ($apiadmin === false)	output_json(1, '昵称修改失败！');

		$callback = $apiadmin::set_nickname($player_id, $new_nickname);
		if ($callback['result'] == 1) {
			$content['content']  = '修改昵称成功：（'.$nickname.'）修改为 （'.$new_nickname.'）';
			$content['key']      = 'set_nickname';
			$content['sid']      = $sid;
			$content['playerid'] = $player_id;
			parent::op_log($content, 'source');
			output_json(0, '昵称修改成功！');
		}

		output_json(1, '昵称修改失败！');
	}
	/**
	 * 获取玩家ID
	 * @return [type] [description]
	 */
	public function public_player_id()
	{
		$sid  = $this->sid;
		$playername = isset($_GET['playername']) ? safe_replace(trim($_GET['playername'])) : '';
		$nickname = isset($_GET['nickname']) ? safe_replace(trim($_GET['nickname'])) : '';
		$username = isset($_GET['username']) ? safe_replace(trim($_GET['username'])) : '';

		if (empty($username) || $sid <= 0)	output_json(1, Lang('player_no_exist'));

		$this->getdb = $this->pubdb->set_db($sid);
		if ($this->getdb === false)	output_json(1, Lang('player_no_exist'));
		//通过昵称找玩家ID
		$this->getdb->table_name = 'player';
		if (is_numeric($username)) {
			$player = $this->getdb->get_one(array('id'=>intval($username)));
		}else if (preg_match("/^([a-zA-Z0-9]|\.)+$/",$username) > 0 && strlen($username) >= 32) {
			$player = $this->getdb->get_one(array('username' => $username));
		}else {
			$wherestr = "nickname='$username' OR username='$username'";
			$player = $this->getdb->get_one($wherestr);
		}

		if ($player) {
			$data['id'] = $player['id'];
			output_json(0, '', $data);
		}
		output_json(1, Lang('player_no_exist'));
	}
	/**
	 * 玩家各种游戏记录对应的模板
	 * 
	 */
	public function record(){
		$key         = isset($_GET['key']) ? trim(safe_replace($_GET['key'])) : '';
		$version     = isset($_GET['version']) ? trim(safe_replace($_GET['version'])) : '';
		$data['playername']     = isset($_GET['playername']) ? trim(safe_replace($_GET['playername'])) : '';
		$data['id']  = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$data['sid'] = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$data['player_role_id'] = isset($_GET['player_role_id']) ? intval($_GET['player_role_id']) : 0;

		if ($key == 'pay'){
			include template('report', 'pay');
			return ;
		}
		if ($key == 'attrbute_stone_stat') {
			$this->attrbuteStoneStat($data['id'], $data['sid']);
			return;
		}

		$tbl_default = 0;
		if (in_array($key, array('item', 'soul', 'fate'))) {
			$tbl_default = 1;
		}

		$loadflag = common::load_api_template($key, $version);
		if ($loadflag === false){
			// include template('player', 'log_'.$key, 'block');
			include template('report', 'player_log');
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
			include template('report', 'player_data');
		}
	}
}
