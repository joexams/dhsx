<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class get extends admin {
	private $getdb, $version, $apiadmin;
	function __construct(){
		parent::__construct();
	}
	/**
	 * 设置数据库连接
	 * 
	 */ 
	private function set_db($sid, $api=false){
		$sid = intval($sid);
		if ($sid > 0){
			parent::check_pf_priv('server', 0, $sid);
			
			$serverdb  = common::load_model('public_model');
			$serverdb->table_name = 'servers';
			$server = $serverdb->get_one(array('sid' => $sid), 'db_server,db_root,db_pwd,db_name,server_ver');
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
				$this->version = $server['server_ver'];
				$this->getdb = new getdb_model($dbconfig, 'game');
				if ($api) {
					$version = trim($server['server_ver']);
					$this->apiadmin = common::load_api_class('api_admin', $version);
					if ($this->apiadmin !== false) {
						$this->apiadmin->SERVER    = $server['api_server'];
						$this->apiadmin->PORT      = $server['api_port'];
						$this->apiadmin->ADMIN_PWD = $server['api_pwd'];
					}
				}
				return true;
			}
		}
		return false;
	}
	/**
	 * 获取命格列表
	 * 
	 */ 
	public function fate_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$sql = 'SELECT a.id as fateid, a.type, a.name as fatename, b.name as quality FROM fate a 
					LEFT JOIN fate_quality b ON a.fate_quality_id=b.id 
					WHERE a.fate_quality_id > 1 
					ORDER BY a.fate_quality_id ASC;';
			$data['list'] = $this->getdb->get_list($sql);

			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 获取玩家信息列表
	 * 
	 */ 
	public function player_list() {
		$sid   = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$order = isset($_GET['order']) ? trim($_GET['order']) : '';
		$type  = isset($_GET['type']) ? trim(safe_replace($_GET['type'])) : '';
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$pagesize = 20;
			$page = max(intval($page), 1);
			$offset = $pagesize*($page-1);
			$str_order = '';
			switch ($order) {
				case 'level':
					$str_order = 'c.level DESC';
					break;
				case 'vip':
					$str_order = 'a.vip_level DESC, a.id DESC';
					break;
				case 'ingot':
					$str_order = 'b.ingot DESC';
					break;
				case 'coins':
					$str_order = 'b.coins DESC';
					break;
				case 'fame':
					$str_order = 'b.fame DESC';
					break;
				case 'skill':
					$str_order = 'b.skill DESC';
					break;
				default:
					$str_order = 'a.id DESC';
					break;
			}

			$wherestr = '';
			if (isset($_GET['dogetSubmit'])) {
				$wherestr = $this->set_player_search($_GET);
				if (strpos($wherestr, 'id') !== false) {
					$wherestr = str_replace('id', 'a.id', $wherestr);
				}
			}
		
			if (strpos($order, 'mission') !== false) {

				$this->getdb->table_name = 'player_data';
				$set_wyh = $set_lock_m = '';
				$wherestr .= !empty($wherestr) ? ' AND ' : ' where ';
				$str_order = "I.`lock` desc,J.first_challenge_time asc, id desc";
				if ($order == 'heromission') {
					$set_lock_m = "AND b.max_hero_mission_lock = I.`lock`";
					$lock = $this->getdb->get_one('', 'max(max_hero_mission_lock) AS mlock');
					$max_lock = $lock['mlock'];
					$wherestr .= " b.max_hero_mission_lock <= {$max_lock} and I.type = 1";
				}else if ($order == 'wyhmission') {
					$set_wyh = " AND J.mission_id = 314";	
					$set_lock_m = " AND I.id = 314";
					$lock = $this->getdb->get_one('', 'max(max_hero_mission_lock) AS mlock');
					$max_lock = $lock['mlock'];
					$wherestr .= " b.max_hero_mission_lock <= {$max_lock} and I.type = 1";
				}else {
					$set_lock_m = "AND b.max_mission_lock = I.`lock`";
					$lock = $this->getdb->get_one('', 'max(max_mission_lock) AS mlock');
					$max_lock = $lock['mlock'];
					$wherestr .= " b.max_mission_lock <= {$max_lock} and I.type = 0";
				}

				$sql = "SELECT 
							a.id,a.username,a.nickname,a.vip_level,b.ingot,b.charge_ingot,b.coins,c.level,d.last_login_ip,d.last_login_time,
							is_tester,is_yellow_vip,is_yellow_year_vip,yellow_vip_level,is_blue_vip,is_blue_year_vip,blue_vip_level,
							b.skill,b.fame,b.role_num,f.name,
							d.last_offline_time,d.source,e.total_ingot,
							J.first_challenge_time,I.name as mission_name,L.name as town_name 
						FROM player a 
						LEFT JOIN player_data b ON a.id=b.player_id 
						LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
						LEFT JOIN role f ON c.role_id=f.id 
						LEFT JOIN player_trace d ON a.id=d.player_id 
						LEFT JOIN player_charge_record e ON a.id=e.player_id 
						LEFT JOIN player_mission_record J on a.id = J.player_id and J.is_finished = 1
						LEFT JOIN mission I on I.id = J.mission_id $set_lock_m $set_wyh
						LEFT JOIN mission_section K on I.mission_section_id = K.id
						LEFT JOIN town L on K.town_id = L.id
						$wherestr 
						ORDER BY $str_order  
						LIMIT $offset,$pagesize;";
			}else {	
			$sql = "SELECT 
						a.id,a.username,a.nickname,a.vip_level,b.ingot,b.charge_ingot,b.coins,c.level,d.last_login_ip,d.last_login_time,
						is_tester,is_yellow_vip,is_yellow_year_vip,yellow_vip_level,is_blue_vip,is_blue_year_vip,blue_vip_level,
						b.skill,b.fame,b.role_num,f.name,
						d.last_offline_time,d.source,e.total_ingot 
					FROM player a 
					LEFT JOIN player_data b ON a.id=b.player_id 
					LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
					LEFT JOIN role f ON c.role_id=f.id 
					LEFT JOIN player_trace d ON a.id=d.player_id 
					LEFT JOIN player_charge_record e ON a.id=e.player_id 
					$wherestr 
					ORDER BY $str_order  
					LIMIT $offset,$pagesize;";
			}
			$list = $this->getdb->get_list($sql);
			if ($recordnum <= 0 && $list) {
				if (isset($_GET['ip']) && !empty($_GET['ip'])) {
					$countsql = "SELECT COUNT(a.id) as num FROM player a LEFT JOIN player_trace b ON a.id=b.player_id LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id  $wherestr;";
					$count = $this->getdb->get_count($countsql);
				}else if (isset($_GET['is_tester']) && !empty($_GET['is_tester'])){
					$countsql = "SELECT COUNT(a.id) as num FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id $wherestr;";
					$count = $this->getdb->get_count($countsql);
				}else if (strpos($order, 'mission') !== false) {
					$countsql = "SELECT COUNT(a.id) as num FROM player a LEFT JOIN player_data b ON a.id=b.player_id LEFT JOIN player_mission_record J on a.id = J.player_id and J.is_finished = 1 LEFT JOIN mission I on I.id = J.mission_id $set_lock_m $set_wyh $wherestr;";
					$count = $this->getdb->get_count($countsql);
				}else {
					$countsql = "SELECT COUNT(a.id) as num FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id LEFT JOIN player_trace c ON a.id=c.player_id $wherestr;";
					$count = $this->getdb->get_count($countsql);
				}
			}else {
				$count = $recordnum;
			}

			$data['count'] = $count;
			$data['list'] = $list;
			output_json(0, '', $data);
		}

		output_json(1, '');
	}
	/**
	 * 导出玩家信息
	 * @return [type] [description]
	 */
	public function export_player() {
		if (isset($_GET['dogetSubmit'])) {
			$sid   = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
			$dbflag = $this->set_db($sid);

			if ($dbflag){
				$ctype = isset($_GET['ctype']) ? intval($_GET['ctype']) : 0;
				$title = isset($_GET['sname']) ? trim($_GET['sname']) : '';
				$title = urldecode($title);
				$title .= $ctype == 1 ? Lang('already_create') : ($ctype == 2 ? Lang('no_create') : '');
				$title .= '玩家数据';
				$wherestr = $this->set_player_search($_GET);
				if (strpos($wherestr, 'id') !== false) {
					$wherestr = str_replace('id', 'a.id', $wherestr);
				}
				$sql = "SELECT 
							a.id,a.username,a.nickname,a.vip_level,b.level,c.last_login_time,
							c.first_login_time,c.source,d.total_ingot 
						FROM player a 
						LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
						LEFT JOIN player_trace c ON a.id=c.player_id 
						LEFT JOIN player_charge_record d ON a.id=d.player_id 
						$wherestr;";
				$list = $this->getdb->get_list($sql);
				@Header('Content-type:   application/octet-stream'); 
				@Header('Accept-Ranges:   bytes'); 
				@Header('Content-type:application/vnd.ms-excel');   
				@Header('Content-Disposition:attachment;filename=s'.$sid.'_'.date('Y-m-d').'.xls');   
				echo <<< HTML
<table>
<tr>
	<td colspan="8">{$title}</td>
</tr>
<tr>
	<td>角色名</td>
	<td>角色等级</td>
	<td>登陆名</td>
	<td>注册</td>
	<td>最后登陆</td>
	<td>渠道</td>
	<td>VIP</td>
	<td>充值元宝</td>
</tr>
HTML;
				foreach ($list as $key => $value) {
					$regdate = date('Y-m-d H:i:s', $value['first_login_time']);
					$logoutdate = date('Y-m-d H:i:s', $value['last_login_time']);
					echo <<< HTML
<tr>
	<td>{$value['nickname']}</td>
	<td>{$value['level']}</td>
	<td>{$value['username']}</td>
	<td>{$regdate}</td>
	<td>{$logoutdate}</td>
	<td>{$value['source']}</td>
	<td>{$value['vip_level']}</td>
	<td>{$value['total_ingot']}</td>
</tr>
HTML;
				}
				echo <<< HTML
</table>
HTML;
			}
		}
	}
	/**
	 * 获取玩家具体信息
	 * 
	 */ 
	public function player_detail_info(){
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$sql = "SELECT a.*,b.*,c.total_ingot,c.level_up_time,c.ingot AS ingot_vip,d.ranking,e.state_point,f.name AS factionname,g.peach_lv,h.barrier,h.zodiac_level FROM player a 
					LEFT JOIN player_data b ON a.id=b.player_id 
					LEFT JOIN player_charge_record c ON c.player_id=a.id 
					LEFT JOIN player_super_sport_ranking d ON d.player_id=a.id 
					LEFT JOIN player_state_point e ON e.player_id=a.id 
					LEFT JOIN player_faction f ON f.player_id=a.id 
					LEFT JOIN player_peach_data g ON g.player_id=a.id 
					LEFT JOIN player_zodiac_data h ON h.player_id=a.id 
					WHERE a.id='$id'";
			$player = $this->getdb->get_list($sql);
			$baseinfo = $player[0];
			$baseinfo['peach_lv'] = 70+$peach['peach_lv']*5;
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

			$this->getdb->table_name = 'role';
			$data['role'] = $this->getdb->select('', 'id, name');

			$this->getdb->table_name = 'deploy_mode';
			$data['deploy'] = $this->getdb->select('', 'id, name');

			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 获取具体玩家信息
	 * 
	 */ 
	public function player_info(){
		$key  = isset($_GET['key']) ? trim($_GET['key']) : '';
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$player_role_id = isset($_GET['player_role_id']) ? intval($_GET['player_role_id']):0;
		$typeflag = isset($_GET['typeflag']) ? intval($_GET['typeflag']) : 0;
		$playername = isset($_GET['playername']) ? trim($_GET['playername']) : '';
		$key_array = array('coin', 'ingot', 'soul', 'fate', 'item', 'gift', 'friends','item_attribute_stone', 'role_elixir', 'key', 'research');
		if (!in_array($key, $key_array)){	
			output_json(1);
		}

		$dbflag = $this->set_db($sid);
		if (!empty($playername)) {
			$player_type = isset($_GET['player_type']) ? intval($_GET['player_type']) : 2;
			$this->getdb->table_name = 'player';
			if ($player_type == 2) {
				$player = $this->getdb->get_one(array('nickname' => $playername));
			}else {
				$player = $this->getdb->get_one(array('username' => $playername));
			}
			$id = $player['id'];
		}	
	
		if ($dbflag && $id > 0){
			$data['list']  = $this->player_key_info($key, $id, $player_role_id);
			$data['count'] = count($data['list']);
			$srtn = array();
			if ($data['count'] > 0){
				switch ($key) {
					case 'soul':
						$srtn = $this->player_soul_extend();
						$data['type']['soul']      = $srtn['soul'];
						$data['type']['attribute'] = $srtn['attribute'];
						break;
					case 'fate':
						$srtn = $this->player_fate_extend($id);
						$data['type']['role'] = $srtn['role'];
						$data['type']['fate'] = $srtn['fate'];
						break;
					case 'item':
						foreach ($data['list'] as $key => $value) {
							$grids[] = $value['grid_id'];
							$items[] = $value['item_id'];
							$upgrades[] = $value['upgrade_level'];
						}
						$this->getdb->table_name = 'item';
						$data['type']['item'] = $this->getdb->select('id IN ('.implode($items, ',').') OR type_id IN (22000, 23000)');
						$this->getdb->table_name = 'item_pack_grid';
						$data['type']['packet'] = $this->getdb->select('id IN ('.implode($grids, ',').')');
						$this->getdb->table_name = 'item_upgrade';
						$data['type']['upgrade'] = $this->getdb->select('level IN ('.implode($upgrades, ',').')');
                        $this->getdb->table_name = 'gold_oil';
                        $data['type']['goldoil'] = $this->getdb->select();
						break;
					case 'gift':
						$this->getdb->table_name = 'super_gift_type';
						$data['type']['gift'] = $this->getdb->select();
						break;
					case 'friends':
						foreach ($data['list'] as $key => $value) {
							$friends[] = $value['friend_id'];
						}
						$this->getdb->table_name = 'player';
						$data['type']['friends'] = $this->getdb->select('id IN ('.implode($friends, ',').')', 'id,username,nickname');
						break;
					case 'item_attribute_stone':
						foreach ($data['list'] as $key => $value) {
							$grids[] = $value['grid_id'];
							$items[] = $value['item_id'];
						}
						$this->getdb->table_name = 'item';
						$data['type']['item'] = $this->getdb->select('id IN ('.implode($items, ',').')');
						$this->getdb->table_name = 'item_pack_grid';
						$data['type']['packet'] = $this->getdb->select('id IN ('.implode($grids, ',').')');
						$this->getdb->table_name = 'attribute_stone';
						$data['type']['attribute'] = $this->getdb->select('item_id IN ('.implode($items, ',').')');	
						break;
					case 'role_elixir':
						$this->getdb->table_name = 'item';
						$data['type']['item'] = $this->getdb->select('type_id IN (11001, 11002, 11003)', 'id, name');
						break;
					case 'key':						
						$this->getdb->table_name = 'town';
						$data['type']['town'] = $this->getdb->select('', 'lock, name');

						$this->getdb->table_name = 'quest';
						$data['type']['quest'] = $this->getdb->select('', 'lock, title');
						
						$this->getdb->table_name = 'mission_section';
						$data['type']['section'] = $this->getdb->select('', 'lock, name');

						$this->getdb->table_name = 'mission';
						$data['type']['mission'] = $this->getdb->select('', 'lock, name');

						// $this->getdb->table_name = 'research';
						// $data['research'] = $this->getdb->select();
						
						$this->getdb->table_name = 'item_pack_grid';
						$data['type']['pack_grid'] = $this->getdb->select('id >= 1 and id <= 100', 'unlock_level, name');
						$data['type']['role_equi'] = $this->getdb->select('id >= 201', 'unlock_level, name');
						$data['type']['warehouse'] = $this->getdb->select('id >= 101 and id <= 200', 'unlock_level, name');

						$this->getdb->table_name = 'game_function';
						$data['type']['game_function'] = $this->getdb->select('', 'lock, name');

						$this->getdb->table_name = 'role';
						$data['type']['role'] = $this->getdb->select('', 'lock, name');

						$data['list'] = $data['list'][0];
						break;
					case 'research':
						$this->getdb->table_name = 'research_type';
						$researchtype = $this->getdb->select();
						$researchtypelist = $researchlist = array();
						foreach ($researchtype as $key => $value) {
							$researchtypelist[$value['research_id']] = $value['name'];
						}

						foreach ($data['list'] as $key => $value) {
							$research[$value['research_id']] = $value;
						}
						$researchids = array_keys($research);
						$this->getdb->table_name = 'research';
						$researchlist = $this->getdb->select('id IN ('.implode($researchids, ',').')', 'id, name, research_type_id');
						$list = array();
						foreach ($researchlist as $key => $value) {
							$list[$key]['id']   = $value['id'];
							$list[$key]['name'] = $value['name'];
							$list[$key]['type'] = isset($researchtypelist[$value['id']]) ? $researchtypelist[$value['id']] : '';
							$list[$key]['level'] = $research[$value['id']]['level'];
						}
						$data['list'] = $list;
						unset($researchtypelist, $researchlist, $research);
						break;
				}
			}
			unset($srtn);
			output_json(0, '', $data);
		}
		output_json(1);
	}
	public function player_id() {
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$playername = isset($_GET['playername']) ? safe_replace(trim($_GET['playername'])) : '';
		$nickname = isset($_GET['nickname']) ? safe_replace(trim($_GET['nickname'])) : '';

		if ((!empty($playername) || !empty($nickname)) && $sid > 0) {
			$dbflag = $this->set_db($sid);
			if ($dbflag) {
				//通过昵称找玩家ID
				$this->getdb->table_name = 'player';
				if (!empty($nickname)) {
					//$player  = $this->apiadmin->find_player_by_nickname($nickname);
					$player = $this->getdb->get_one(array('nickname'=>$nickname));
				}else {
				//通过玩家名称找玩家ID
					//$player = $this->apiadmin->find_player_by_username($playername);
					$player = $this->getdb->get_one(array('username' => $playername));
				}
				if ($player) {
					$data['id'] = $player['id'];
					output_json(0, '', $data);
				}
			}
		}
		output_json(1, Lang('player_no_exist'));
	}
	/**
	 * 获取玩家记录
	 * 
	 */ 
	public function player_record(){
		$key  = isset($_GET['key']) ? trim($_GET['key']) : '';
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$typeflag = isset($_GET['typeflag']) ? intval($_GET['typeflag']) : 0;
		$dbflag = $this->set_db($sid);

		$key_array = array('coin', 'ingot', 'soul', 'soul_stone', 'item', 'fame', 'fate', 'power', 'take_bible', 'skill', 'farmland', 'flower_count', 'coin_tree_count', 'elixir', 'state_point', 'peach', 'defeat_world_boss', 'level_up', 'mission', 'item_attribute_stone', 'faction_contribution');
		if ($dbflag && in_array($key, $key_array)){
			$rtn = $this->player_key_record($key, $_GET);
			$data['list']  = $rtn['list'];
			$data['count'] = $rtn['count'];
			if (in_array($key, array('ingot', 'coin','item', 'power', 'item_attribute_stone'))){
				$data['allnum'] = $rtn['allnum'];
			}

			if ($data['count'] > 0){
				//只读一次
				if ($typeflag == 0){
					if (!in_array($key, array('farmland', 'flower_count', 'peach', 'defeat_world_boss', 'level_up'))){
						$trtn = $this->player_key_record_type($key);
						$data['type']['cons'] = $trtn['cons'];
						$data['type']['get']  = $trtn['get'];
						unset($trtn);
					}
					
					switch ($key) {
						case 'soul':
							$srtn = $this->player_soul_extend();
							$data['type']['soul']      = $srtn['soul'];
							$data['type']['attribute'] = $srtn['attribute'];
							unset($srtn);
							break;
						case 'fate':
							$sql = 'SELECT a.id,a.name,b.fate_quality_id,b.level,b.request_experience FROM fate a LEFT JOIN fate_quality_level b ON a.fate_quality_id=b.fate_quality_id';
							$data['type']['fate'] = $this->getdb->get_list($sql);
							break;
						case 'item':
							$this->getdb->table_name = 'item';
							$data['type']['item'] = $this->getdb->select('', 'id,name');
							break;
						case 'item_attribute_stone':
							$this->getdb->table_name = 'item';
							$data['type']['item'] = $this->getdb->select('', 'id,name');
							$this->getdb->table_name = 'attribute_stone';
							$data['type']['attribute'] = $this->getdb->select('', 'item_id,lv');
							break;
						case 'defeat_world_boss':
							$sql = "SELECT a.id,b.name FROM world_boss a LEFT JOIN town b ON b.id=a.town_id";
							$data['type']['boss'] = $this->getdb->get_list($sql);
						break;
					}

					$id = intval($_GET['id']);
					$data['chklist'] = $dchklist = array();
					if (in_array($key, array('fate', 'item', 'soul', 'item_attribute_stone')) && $id > 0) {
						$retrievedb = common::load_model('retrieve_model');
						$chklist = $retrievedb->select(array('player_id'=>$id, 'sid'=>$sid, 'key'=>$key), 'key,data');
						foreach ($chklist as $k => $val) {
							$dchklist = array_merge($dchklist, unserialize($val['data']));
						}
						$data['chklist'] = $dchklist;
						unset($chklist, $dchklist);
					}
				}
				//每次都读取
				switch ($key) {
					case 'take_bible':
						foreach ($data['list'] as $key => $value) {
							if ($value['be_rob_player_id'] > 0){
								$be_rob_players[] = $value['be_rob_player_id'];
							}
						}
						$this->getdb->table_name = 'player';
						$data['type']['players'] = $this->getdb->select('id IN ('.implode($be_rob_players, ',').')', 'id,username,nickname');
						break;
					case 'farmland':
						foreach ($data['list'] as $key => $value) {
							$herbs[] = $value['herbs_id'];
							$roles[] = $value['player_role_id'];
						}
						$this->getdb->table_name = 'herbs';
						$data['type']['herbs'] = $this->getdb->select('id IN ('.implode($herbs, ',').')', 'id,name');
						$sql = "SELECT a.id,b.name FROM player_role a LEFT JOIN role b ON a.role_id=b.id WHERE a.id IN (".implode($roles, ',').")";
						$data['type']['roles'] = $this->getdb->get_list($sql);
						break;
					case 'elixir':
						foreach ($data['list'] as $key => $value) {
							$roles[] = $value['player_role_id'];
							$items[] = $value['item_id'];
						}
						$this->getdb->table_name = 'item';
						$data['type']['items'] = $this->getdb->select('id IN ('.implode($items, ',').')', 'id,name');
						$sql = "SELECT a.id,b.name FROM player_role a LEFT JOIN role b ON a.role_id=b.id WHERE a.id IN (".implode($roles, ',').")";
						$data['type']['roles'] = $this->getdb->get_list($sql);
						break;
					case 'state_point':
						foreach ($data['list'] as $key => $value) {
							$data['list'][$key]['time'] = $value['date'];
							unset($data['list'][$key]['date']);
							$roles[] = $value['player_role_id'];
						}
						$sql = "SELECT a.id,b.name FROM player_role a LEFT JOIN role b ON a.role_id=b.id WHERE a.id IN (".implode($roles, ',').")";
						$data['type']['roles'] = $this->getdb->get_list($sql);
						break;
					case 'flower_count':
					    $players = array();
						foreach ($data['list'] as $key => $value) {
							if (!in_array($value['player_id'], $players)){
								$players[] = $value['player_id'];
							}
							if (!in_array($value['from_player_id'], $players)){
								$players[] = $value['from_player_id'];
							}
						}
						$this->getdb->table_name = 'player';
						$data['type']['players'] = $this->getdb->select('id IN ('.implode($players, ',').')', 'id,username,nickname,is_tester');
						break;
					case 'peach':
						foreach ($data['list'] as $key => $value) {
							$data['list'][$key]['time'] = $value['date'];
							unset($data['list'][$key]['date']);
						}
						break;
					case 'level_up':
						foreach ($data['list'] as $key => $value) {
							$roles[] = $value['player_role_id'];
						}
						$sql = "SELECT a.id,b.name FROM player_role a LEFT JOIN role b ON a.role_id=b.id WHERE a.id IN (".implode($roles, ',').")";
						$data['type']['roles'] = $this->getdb->get_list($sql);
						break;
				}
			}
			unset($rtn);
			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 竞技场排名
	 * @return [type] [description]
	 */
	public function arena_ranking(){
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
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

		output_json(1, Lang('error'));
	}
	/**
	 * 帮派列表
	 * @return [type] [description]
	 */
	public function player_faction_list(){
		$sid  = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
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
	/**
	 * 帮派成员列表
	 * @return [type] [description]
	 */
	public function player_faction_member(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$faction_id = isset($_GET['faction_id']) ? intval($_GET['faction_id']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
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

		output_json(1, Lang('error'));
	}
	/**
	 * 帮派申请列表
	 * @return [type] [description]
	 */
	public function player_faction_apply(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$faction_id = isset($_GET['faction_id']) ? intval($_GET['faction_id']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
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

		output_json(1, Lang('error'));
	}
	/**
	 * 关卡进度
	 * @return [type] [description]
	 */
	public function mission_stat(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);
		if ($dbflag){
			$type = isset($_GET['type']) ? intval($_GET['type']) : 0;
			$wherestr = "WHERE b.type='$type' AND is_disable=0 ";
			$this->getdb->table_name = 'player';
			$players = $this->getdb->select('is_tester<>0', 'id');
			$idstr = '';
			if ($players){
				foreach ($players as $key => $player) {
					$ids[] = $player['id'];
				}
				$idstr = implode($ids, ',');
				$wherestr .= " AND a.player_id NOT IN ($idstr)";
			}
			
			$sql = "SELECT a.mission_id, d.name AS town, b.name AS mission, SUM(times) AS pktimes,SUM(failed_challenge) AS pkfailedtimes,COUNT(CASE WHEN is_finished=1 THEN 1 ELSE NULL END) AS finished , COUNT(CASE WHEN is_finished=0 THEN 1 ELSE NULL END) AS notfinished FROM player_mission_record a 
					LEFT JOIN mission b ON a.mission_id=b.id 
					LEFT JOIN mission_section c ON b.mission_section_id=c.id 
					LEFT JOIN town d ON c.town_id=d.id 
					$wherestr GROUP BY b.lock ORDER BY b.lock DESC";

			$data['list'] = $this->getdb->get_list($sql);
			$max = 1;
			foreach ($data['list'] as $key => $value) {
				$max = max($max, $value['pktimes'], $value['pkfailedtimes']);
			}
			$data['max'] = $max;

			output_json(0, '', $data);
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 等级分布统计
	 * @return [type] [description]
	 */
	public function level_stat(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$arrsid = isset($_GET['sid']) ? $_GET['sid'] : array();
		$startlevel = isset($_GET['startlevel']) ? intval($_GET['startlevel']) : 0;
		$endlevel = isset($_GET['endlevel']) ? intval($_GET['endlevel']) : 0;

		if ($cid > 0 && count($arrsid) > 0){
			$wherest = '';
			$sql = 'SELECT ';
			if ($startlevel>0 && $endlevel > 0){
				$wherestr = " AND level>=$startlevel AND level<=$endlevel";
				for($i=$startlevel; $i <= $endlevel; $i++){
					$categories[] = $i;
					$sql .= "COUNT(CASE WHEN b.level=$i THEN 1 ELSE NULL END) AS level_$i,";
				}
			}else {
				$j = 1;
				for($i=1; $i < 150; $i+=5){
					$m = 5 * $j;
					$categories[] = $i.'~'.$m;
					$sql .= "COUNT(CASE WHEN b.level BETWEEN $i AND $m THEN 1 ELSE NULL END) AS level_$m,";
					$j++;
				}
			}
			$sql = trim($sql, ',');
			$sql .= " FROM player a LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id LEFT JOIN role c ON b.role_id=c.id WHERE is_tester=0 AND c.lock=0 $wherestr GROUP BY gender";
			$levels = $man = $female = $level =array();
			foreach ($arrsid as $key => $sid) {
				$sid = intval($sid);
				if ($sid > 0 && $this->set_db($sid)){
					$level = $this->getdb->get_list($sql);
					foreach ($level as $key => $value) {
						if ($key == 0){
							foreach ($value as $k => $val) {
								$man[$k] += intval($val);
								$levels[$k] += intval($val);
							}
						}else {
							foreach ($value as $k => $val) {
								$female[$k] += intval($val);
								$levels[$k] += intval($val);
							}
						}
					}
				}
			}

			$data['categories'] = $categories;
			$data['list']['man'] = array_values($man);
			$data['list']['female'] = array_values($female);
			$data['list']['level'] = array_values($levels);
			$data['sum'] = array_sum($data['list']['level']);
			$data['max'] = max($data['list']['level']);
			unset($levels, $female, $man, $level);
			output_json(0, '', $data);
		}
		output_json(1, Lang('error'));
	}
	/**
	 * VIP统计
	 * @return [type] [description]
	 */
	public function vip_stat(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$arrsid = isset($_GET['sid']) ? $_GET['sid'] : array();

		if ($cid > 0 && count($arrsid) > 0){
			$sql = 'SELECT vip_level, COUNT(id) AS num FROM player  GROUP BY vip_level';
			$list = $vipstat = $categories = array();
			foreach ($arrsid as $key => $sid) {
				$sid = intval($sid);
				if ($sid > 0 && $this->set_db($sid)){
					$list = $this->getdb->get_list($sql);
					foreach ($list as $key => $value) {
						$vipstat[$value['vip_level']] += intval($value['num']);
					}
				}
			}

			$sum = 0;
			if (!empty($vipstat)){
				$categories = array_keys($vipstat);
				$list = array_values($vipstat);
				$sum = array_sum($list);
			}

			$data['sum'] = $sum;
			$data['list'] = $list;
			$data['categories'] = $categories;
			unset($list, $categories, $vipstat);
			output_json(0, '', $data);
		}
		output_json(1, Lang('error'));
	}
	/**
	 * 服务器玩家统计
	 * @return [type] [description]
	 */
	public function server_player_stat(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$name= isset($_GET['name']) ? trim($_GET['name']) : '';
		if ($cid <= 0 || $sid <= 0){
			output_json(1, Lang('args_no_enough'));
		}
		$dateline = strtotime(date('Y-m-d'));
		//在线
		$data['online'] = $data['active'] = $data['income'] = $data['consume'] = 0;
		if (file_exists(ROOT_PATH.'../online_data.php')){
			include_once(ROOT_PATH.'../online_data.php');
			$data['online'] = isset($online_data[$name]) ? $online_data[$name] : 0;
		}
		//收入
		$memkey = md5('server_income_stat_'.$cid.'_'.$sid);
		$income = getcache($memkey);
		if (!$income) {
			$pubdb = common::load_model('public_model');
			$pubdb->table_name = 'pay_data';
			$wherestr = "dtime_unix>='$dateline' AND sid='$sid'";
			$income = $pubdb->get_one($wherestr, 'SUM(amount) AS amount');
			setcache($memkey, $income, '', 'memcache', 'memcache', 5*60);
		}
		$data['income'] = round($income['amount'], 2);
		
		$dbflag = $this->set_db($sid);
		if ($dbflag){
			//活跃
			$memkey = md5('server_active_stat_'.$cid.'_'.$sid);
			$data['active'] = getcache($memkey);
			if (!$data['active']) {
				$this->getdb->table_name = 'player_trace';
				$data['active'] = $this->getdb->count("last_login_time>='$dateline'", 'player_id');
				setcache($memkey, $data['active'], '', 'memcache', 'memcache', 5*60);
			}
			//消费
			$memkey = md5('server_consume_stat_'.$cid.'_'.$sid);
			$consume = getcache($memkey);
			if (!$consume) {
				$sql = "SELECT SUM(change_charge_value)/10 AS consume FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE vip_level > 0 AND type<>35 AND change_charge_value < 0 AND is_tester=0 AND change_time>='$dateline'";
				$consume = $this->getdb->get_list($sql);
				setcache($memkey, $consume, '', 'memcache', 'memcache', 5*60);
			}
			if ($consume){
				$data['consume'] = round($consume[0]['consume'], 2);
			}else {
				$data['consume'] = 0;
			}

		}
		$data['sid'] = $sid;
		output_json(0, '', $data);
	}
	/**
	 * 消费统计
	 * @return [type] [description]
	 */
	public function consume(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
		$typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime'].' 23:59:59') : 0;
		$start_vip_level = isset($_GET['start_vip_level']) ? intval($_GET['start_vip_level'])  : 0;
		$end_vip_level = isset($_GET['end_vip_level']) ? intval($_GET['end_vip_level'])  : 0;
		$start_level = isset($_GET['start_level']) ? intval($_GET['start_level'])  : 0;
		$end_level = isset($_GET['end_level']) ? intval($_GET['end_level'])  : 0;

		$wherestr = '';
		if ($typeid > 0) {
			$wherestr .= !empty($wherestr) ? " AND type='$typeid'" : "type='$typeid'";
		}
		if ($starttime > 0){
			$wherestr .= !empty($wherestr) ? " AND change_time>'$starttime'" : "change_time>'$starttime'";
		}
		if ($endtime > 0){
			$wherestr .= !empty($wherestr) ? " AND change_time<'$endtime'" : "change_time<'$endtime'";
		}
		if ($start_vip_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND vip_level>='.$start_vip_level.'': ' vip_level>='.$start_vip_level.'';
		}
		if ($end_vip_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND vip_level<='.$end_vip_level.'': ' vip_level<='.$end_vip_level.'';
		}
		if ($start_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND level>='.$start_level.'': ' level>='.$start_level.'';
		}
		if ($end_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND level<='.$end_level.'': ' level<='.$end_level.'';
		}
		$wherestr .= !empty($wherestr) ? ' AND value < 0 AND is_tester=0': 'value < 0 AND is_tester=0';

		$list = array();
		$data['allnum'] = $data['alltotal'] = $data['allingot'] = 0;
		foreach ($sid as $value) {
			$dbflag = $this->set_db($value);
			if ($dbflag){
				$this->getdb->table_name  = 'ingot_change_type';
				$data['type'] = $this->getdb->select('', 'id,name');

				if ($start_level > 0 || $end_level > 0){
					if ($typeid > 0) {
						$sql = 'SELECT type, COUNT(DISTINCT a.player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a 
							LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr;
					}else {
						$sql = 'SELECT type, COUNT(DISTINCT a.player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a 
							LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr.' GROUP BY type';
					}
					
					$numsql = 'SELECT COUNT(DISTINCT a.player_id) AS allnum, COUNT(a.id) AS alltotal, SUM(value) AS allingot FROM player_ingot_change_record a 
							LEFT JOIN player b ON a.player_id=b.id LEFT JOIN player_role c ON b.id=c.player_id AND b.main_role_id=c.id WHERE '.$wherestr;	
				}else {
					if ($typeid > 0) {
						$sql = 'SELECT type, COUNT(DISTINCT player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr;
					}else {
						$sql = 'SELECT type, COUNT(DISTINCT player_id) AS num, COUNT(a.id) AS total, SUM(value) AS ingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr.' GROUP BY type';
					}
					$numsql = 'SELECT COUNT(DISTINCT player_id) AS allnum, COUNT(a.id) AS alltotal, SUM(value) AS allingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE '.$wherestr;
				}
				$tmplist = $this->getdb->get_list($sql);
				$list = array_merge($list, $tmplist);
				$allcount = $this->getdb->get_list($numsql);

				$data['allnum'] += $allcount[0]['allnum'];
				$data['alltotal'] += $allcount[0]['alltotal'];
				$data['allingot'] += $allcount[0]['allingot'];
			}
		}
		$alllist = array();
		foreach ($list as $key => $value) {
			if (array_key_exists($value['type'], $list)) {
				$alllist[$value['type']]['num'] += $value['num'];
				$alllist[$value['type']]['total'] += $value['total'];
				$alllist[$value['type']]['ingot'] += $value['ingot'];
			}else {
				$alllist[$value['type']]['num'] = $value['num'];
				$alllist[$value['type']]['total'] = $value['total'];
				$alllist[$value['type']]['ingot'] = $value['ingot'];
			}
			$alllist[$value['type']]['type'] = $value['type'];
		}
		usort($alllist, 'self::cmp');
		$data['list'] = $alllist;

		output_json(0, '', $data);
	}
	/**
	 * 流失率统计
	 * @return [type] [description]
	 */
	public function ajax_lossrate_list(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? $_GET['sid'] : array();
		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime']) : 0;
		$start_level = isset($_GET['start_level']) && intval($_GET['start_level'])>0 ? intval($_GET['start_level'])  : 1;
		$end_level = isset($_GET['end_level']) && intval($_GET['end_level'])>0 ? intval($_GET['end_level'])  : 0;
		$daynum = isset($_GET['daynum']) && intval($_GET['daynum'])>0 ? intval($_GET['daynum']) : 5;

		$wherestr = '';
		$wherestr = $starttime > 0 ? " first_login_time>'$starttime'" : '';
		if ($endtime > 0){
			$wherestr .= !empty($wherestr) ? " AND first_login_time<'$endtime'" : "first_login_time<'$endtime'";
		}
		if ($start_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND level>='.$start_level.'': ' level>='.$start_level.'';
		}
		if ($end_level > 0){
			$wherestr .= !empty($wherestr) ? ' AND level<='.$end_level.'': ' level<='.$end_level.'';
		}
		$wherestr .= !empty($wherestr) ? ' AND is_tester=0': 'is_tester=0';
		$wherestr = !empty($wherestr) ? ' WHERE '.$wherestr : $wherestr;

		$daytimes = time() - $daynum * 24 * 3600;
		$list = array();
		if ($cid > 0 && count($sid) > 0) {
			foreach ($sid as $key=>$value) {
				$dbflag = $this->set_db($value);
				if ($dbflag){
					$sql = "SELECT 
							level, 
							COUNT(a.id) AS num, 
							COUNT(CASE WHEN d.total_ingot > 0 THEN 1 ELSE NULL END) AS paynum, 
							COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time > 0 THEN 1 ELSE NULL END) AS lossnum, 
							COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time>0 AND d.total_ingot>0 THEN 1 ELSE NULL END) AS losspaynum,
							COUNT(CASE WHEN vip_level>0 THEN 1 ELSE NULL END) AS vipnum,
							COUNT(CASE WHEN last_login_time<='$daytimes' AND last_login_time>0 AND vip_level>0 AND d.total_ingot>0 THEN 1 ELSE NULL END) AS lossvipnum
							FROM player a 
							LEFT JOIN player_role b ON a.id=b.player_id AND a.main_role_id=b.id 
							LEFT JOIN player_trace c ON a.id=c.player_id 
							LEFT JOIN player_charge_record d ON a.id=d.player_id 
							$wherestr GROUP BY level";
					$list[] = $this->getdb->get_list($sql);
				}
			}
			$alllist = array();
			if (count($list) > 1){
				$alllist[0]['level'] = '-';
				foreach ($list as $key => $value) {
					foreach ($value as $k => $val) {
						$alllist[0]['num'] += intval($val['num']);
						$alllist[0]['paynum'] += intval($val['paynum']);
						$alllist[0]['lossnum'] += intval($val['lossnum']);
						$alllist[0]['losspaynum'] += intval($val['losspaynum']);
						$alllist[0]['vipnum'] += intval($val['vipnum']);
						$alllist[0]['lossvipnum'] += intval($val['lossvipnum']);


						$alllist[$val['level']]['level'] = $val['level'];
						$alllist[$val['level']]['num'] += intval($val['num']);
						$alllist[$val['level']]['paynum'] += intval($val['paynum']);
						$alllist[$val['level']]['lossnum'] += intval($val['lossnum']);
						$alllist[$val['level']]['losspaynum'] += intval($val['losspaynum']);
						$alllist[$val['level']]['vipnum'] += intval($val['vipnum']);
						$alllist[$val['level']]['lossvipnum'] += intval($val['lossvipnum']);
					}
				}
				sort($alllist);
				$data['list'] = $alllist;
			}else {
				$data['list'] = $list[0];
			}
			output_json(0, '', $data);
		}

		output_json(1, '');
	}
	/**
	 * 渠道统计
	 * @return [type] [description]
	 */
	public function ajax_channel_list() {
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
		$starttime = isset($_GET['starttime']) && !empty($_GET['starttime']) ? strtotime($_GET['starttime']) : 0;
		$endtime = isset($_GET['endtime']) && !empty($_GET['endtime']) ? strtotime($_GET['endtime'].' 23:59:59') : 0;
		$source = isset($_GET['source']) && !empty($_GET['source']) ? trim($_GET['source']) : '';
		$type =  isset($_GET['type']) ? intval($_GET['type']) : 0;

		if ($cid > 0) {
			$wherestr = '';
			 if ($type == 1){
                    $wherestr = $starttime > 0 ? " regdate>='$starttime'" : '';
                    if ($endtime > 0){
                        $wherestr .= !empty($wherestr) ? " AND regdate<='$endtime'" : " regdate<='$endtime'";
                    }
                }else {
                    $wherestr = $starttime > 0 ? " first_login_time>='$starttime'" : '';
                    if ($endtime > 0){
                        $wherestr .= !empty($wherestr) ? " AND first_login_time<='$endtime'" : " first_login_time<='$endtime'";
                    }
                }
                if (!empty($source)){
                    $wherestr = "source LIKE '%$source%'".(!empty($wherestr) ? ' AND '.$wherestr : '');
                }
                $wherestr = !empty($wherestr) ? ' WHERE '.$wherestr.' AND is_tester=0': 'WHERE is_tester=0';
			if ($sid > 0) {
				$dbflag = $this->set_db($sid);
				if ($dbflag){
					$sql = "SELECT
					source,
                    COUNT(a.id) AS num,
                    COUNT(CASE WHEN nickname<>'' THEN 1 ELSE NULL END) AS createnum,
                    COUNT(CASE WHEN level>=2 THEN 1 ELSE NULL END) AS level2,
                    COUNT(CASE WHEN level>=10 THEN 1 ELSE NULL END) AS level10,
                    COUNT(CASE WHEN level>=20 THEN 1 ELSE NULL END) AS level20,
                    COUNT(CASE WHEN level>=30 THEN 1 ELSE NULL END) AS level30,
                    COUNT(CASE WHEN level>=40 THEN 1 ELSE NULL END) AS level40,
                    COUNT(CASE WHEN d.total_ingot>0 THEN 1 ELSE NULL END) AS paynum,
                    SUM(total_ingot) AS amount
                    FROM player a 
                    LEFT JOIN player_trace b ON a.id=b.player_id 
                    LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
                    LEFT JOIN player_charge_record d ON a.id=d.player_id
                    $wherestr GROUP BY source;";
					$data['list'] = $this->getdb->get_list($sql);
					output_json(0, '', $data);
				}
			}else {
				$sql = "SELECT
                    COUNT(a.id) AS num,
                    COUNT(CASE WHEN nickname<>'' THEN 1 ELSE NULL END) AS createnum,
                    COUNT(CASE WHEN level>=2 THEN 1 ELSE NULL END) AS level2,
                    COUNT(CASE WHEN level>=10 THEN 1 ELSE NULL END) AS level10,
                    COUNT(CASE WHEN level>=20 THEN 1 ELSE NULL END) AS level20,
                    COUNT(CASE WHEN level>=30 THEN 1 ELSE NULL END) AS level30,
                    COUNT(CASE WHEN level>=40 THEN 1 ELSE NULL END) AS level40,
                    COUNT(CASE WHEN d.total_ingot>0 THEN 1 ELSE NULL END) AS paynum,
                    SUM(total_ingot) AS amount
                    FROM player a 
                    LEFT JOIN player_trace b ON a.id=b.player_id 
                    LEFT JOIN player_role c ON a.id=c.player_id AND a.main_role_id=c.id 
                    LEFT JOIN player_charge_record d ON a.id=d.player_id
                    $wherestr;";
				$servdb = common::load_model('public_model');
				$servdb->table_name = 'servers';
				$serverlist = $servdb->select("cid=$cid AND combined_to=0 AND open=1 AND open_date<'".date('Y-m-d H:i:s')."'", 'sid,db_server,db_root,db_pwd,db_name,server_ver');
				$list = array();
				foreach ($serverlist as $key => $value) {
					$db_host = explode(':', $value['db_server']);
    				$gdb = new mysqli($db_host[0], $value['db_root'], $value['db_pwd'], $value['db_name'], $db_host[1]);
			    	if ($gdb->connect_error) continue;
					if ($result = $gdb->query($sql)) {
    					while ($row = $result->fetch_assoc()) {
                			$list[] = $row;
    					}
    					$result->close();
    				}
				}
				$arr = array();
				foreach ($list as $key => $value) {
					$arr['source'] = $source;
					$arr['num'] += $value['num'];
					$arr['createnum'] += $value['createnum'];
					$arr['level2'] += $value['level2'];
					$arr['level10'] += $value['level10'];
					$arr['level20'] += $value['level20'];
					$arr['level30'] += $value['level30'];
					$arr['level40'] += $value['level40'];
					$arr['paynum'] += $value['paynum'];
					$arr['amount'] += $value['amount'];
				}
				$data['list'] = array(0=>$arr);
				output_json(0, '', $data);
			}
		}

		output_json(1, '');
	}

	/**
	 * 物品类别列表
	 */
	public function item_type_list() {
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) {
			output_json(1, Lang('error'));
		}
		$dbflag = $this->set_db($sid);
		$wherestr = '';
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}

		$this->getdb->table_name = 'item_type';
		$data['list'] = $this->getdb->select();

		output_json(0, Lang('success'), $data);
	}
	/**
	 * 物品类别列表
	 */
	public function item_list() {
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
		if ($sid <= 0 || $typeid <= 0) {
			output_json(1, Lang('error'));
		}
		$dbflag = $this->set_db($sid);
		$wherestr = '';
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}

		$this->getdb->table_name = 'item';
		$data['list'] = $this->getdb->select(array('type_id' => $typeid), 'id, name, type_id, price_level');

		output_json(0, Lang('success'), $data);
	}
	/**
	 * 玩家成就列表
	 * @return [type] [description]
	 */
	public function achievement_list() {
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		// $typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
		if ($sid <= 0) {
			output_json(1, Lang('error'));
		}
		$dbflag = $this->set_db($sid);
		$wherestr = '';
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}

		$this->getdb->table_name = 'achievement';
		$data['list'] = $this->getdb->select('', 'id, name');

		output_json(0, Lang('success'), $data);
	}
	/**
	 * 灵件列表
	 * @return [type] [description]
	 */
	public function soul_list() {
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) {
			output_json(1, Lang('error'));
		}
		$dbflag = $this->set_db($sid);
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}
		$data = $this->player_soul_extend();

		output_json(0, '', $data);
	}
	/**
	 * 数组按元宝大小排序
	 * @param  [type] $a [description]
	 * @param  [type] $b [description]
	 * @return [type]    [description]
	 */
	private static function cmp($a, $b)
	{
	    if ($a['ingot'] == $b['ingot']) {
	        return 0;
	    }
	    return ($a['ingot'] < $b['ingot']) ? -1 : 1;
	}
	/**
	 * 设置查询玩家信息条件
	 * 
	 */ 
	private function set_player_search($get){
		$wherestr     = '';
		$ip           = isset($get['ip']) ? trim(safe_replace($get['ip'])) : '';
		$username     = isset($get['username']) ? trim(safe_replace($get['username'])) : '';
		$searchtype   = isset($get['searchtype']) ? intval($get['searchtype']) : 0;

		if (!empty($username)){
			$username = str_replace(array("\r\n", "\n", "\r"), ',', $username);
			$username = explode(',', $username);
			if (count($username) > 1) {
				$wherestr = 'WHERE '.to_sqls($username, '', 'username');
			}else {
				if ($searchtype == 1) {
					$wherestr = " WHERE username='$username[0]' OR nickname='$username[0]' ";
				}else if ($searchtype == 2) {
					$wherestr = " WHERE username LIKE '%$username[0]%' OR nickname LIKE '%$username[0]%'";
				}else if ($searchtype == 3) {
					$wherestr = " WHERE id='$username[0]'";
				}
				return $wherestr;
			}
		}

		if (!empty($ip)){
			$wherestr = " WHERE last_login_ip='$ip' ";
			return $wherestr;
		}

		$vip_level    = isset($get['vip']) ? intval($get['vip']) : 0;
		$yellow       = isset($get['yellow']) ? intval($get['yellow']) : 0;
		$yellow_level = isset($get['yellow_level']) ? intval($get['yellow_level']) : 0;
		$blue         = isset($get['blue']) ? intval($get['blue']) : 0;
		$blue_level   = isset($get['blue_level']) ? intval($get['blue_level']) : 0;
		$minlevel     = isset($get['minlevel']) ? intval($get['minlevel']) : 0;
		$maxlevel     = isset($get['maxlevel']) ? intval($get['maxlevel']) : 0;
		$is_tester    = 0;
		$start_vip_level = isset($get['start_vip_level']) ? intval($get['start_vip_level']) : 0;
		$end_vip_level = isset($get['end_vip_level']) ? intval($get['end_vip_level']) : 0;
		$starttime = isset($get['starttime']) && !empty($get['starttime']) ? strtotime($get['starttime']) : 0;
		$endtime = isset($get['endtime']) && !empty($get['endtime']) ? strtotime(($get['endtime'])) : 0;
		$source = isset($get['source']) && !empty($get['source']) ? trim(safe_replace($get['source'])) : '';
		$ctype = isset($get['ctype']) ? intval($get['ctype']) : 0;

		if (!empty($get['is_tester'])){
			if (strpos($get['is_tester'], ',')){
				$wherestr = ' WHERE (is_tester=1 OR is_tester=2)';
			}else {
				$is_tester = intval($get['is_tester']);
				$wherestr = ' WHERE is_tester='.$is_tester.'';
			}
		}
		if ($ctype == 1) {
			$wherestr .= !empty($wherestr) ? " AND nickname<>''": " WHERE nickname<>''";
		}else if ($ctype == 2) {
			$wherestr .= !empty($wherestr) ? " AND nickname<>''": " WHERE nickname=''";
		}
		if ($vip_level > 0) $wherestr .= !empty($wherestr) ? " AND vip_level>='$vip_level'" : " WHERE vip_level>='$vip_level'";
		if ($start_vip_level > 0) $wherestr .= !empty($wherestr) ? " AND vip_level>='$start_vip_level'" : " WHERE vip_level>='$start_vip_level'";
		if ($end_vip_level > 0) $wherestr .= !empty($wherestr) ? " AND vip_level<='$end_vip_level'" : " WHERE vip_level<='$end_vip_level'";
		if ($yellow > 0) $wherestr .= !empty($wherestr) ? ' AND is_yellow_vip=1' : ' WHERE is_yellow_vip=1';
		if ($yellow_level > 0) $wherestr .= !empty($wherestr) ? ' AND yellow_vip_level= '.$yellow_level : ' WHERE yellow_vip_level='.$yellow_level;
		if ($blue > 0) $wherestr .= !empty($wherestr) ? ' AND is_blue_vip=1 ' : ' WHERE is_blue_vip=1';
		if ($blue_level > 0) $wherestr .= !empty($wherestr) ? ' AND blue_vip_level= '.$blue_level : ' WHERE blue_vip_level='.$blue_level;
		if ($minlevel > 0) $wherestr .= !empty($wherestr) ? ' AND level>'.$minlevel.'' : ' WHERE level>'.$minlevel.'';
		if ($maxlevel > 0) $wherestr .= !empty($wherestr) ? ' AND level<'.$maxlevel.'' : ' WHERE level<'.$maxlevel.'';
		if ($starttime > 0) $wherestr .= !empty($wherestr) ? " AND first_login_time>='$starttime'" : " WHERE first_login_time>='$starttime'";
		if ($endtime > 0) $wherestr .= !empty($wherestr) ? " AND first_login_time<='$endtime'" : " WHERE first_login_time<='$endtime'";
		if ($source != '') $wherestr .= !empty($wherestr) ? " AND source='$source'" : " WHERE source='$source'";

		return $wherestr;
	}
	/**
	 * 玩家游戏记录
	 * 
	 */ 
	private function player_key_record($tbl_key, $get){
		if (empty($tbl_key)) return array();

		$page      = isset($get['top']) && intval($get['top']) > 0 ? intval($get['top']) : 1;
		$recordnum = isset($get['recordnum']) ? intval($get['recordnum']) : 0;
		$pagesize  = 20;

		$tbl       = intval($get['tbl']) > 0 ? '2' : '';

		$wherestr  = '';
		$id = intval($get['id']);
		$starttime = isset($get['starttime']) && !empty($get['starttime']) ? strtotime($get['starttime']) : 0;
		$endtime   = isset($get['endtime']) && !empty($get['endtime']) ? strtotime($get['endtime']) : 0;
		$datetime  = isset($get['datetime']) && !empty($get['datetime']) ? strtotime($get['datetime']) : 0;
		$playertype = isset($get['playertype']) ? intval($get['playertype']) : 0;
		$playername = isset($get['playername']) && !empty($get['playername']) ? trim($get['playername']) : ''; 
		$player_role_id = isset($get['player_role_id']) && intval($get['player_role_id']) > 0 ? intval($get['player_role_id']) : 0;
		$name = isset($get['name']) && !empty($get['name']) ? safe_replace($get['name']) : '';
		if (isset($get['typeid']) && is_array($get['typeid'])){
			$typeid = implode($get['typeid'], ',');
		}else {
			$typeid = intval($get['typeid']);
		}

		if (!empty($playername)){
			$where = $playertype > 0 ? "nickname='$playername'" : "username='$playername'";
			$this->getdb->table_name = 'player';
			$player = $this->getdb->get_one($where, 'id, username');
			$id = intval($player['id']);
			if ($id <= 0){
				return array('list'=>array(), 'count'=>0, 'allnum'=>array('getnum'=>0, 'connum'=>0));
			}
		}
		$where = '';


		if ($id > 0){
			$wherestr = 'player_id='.$id;
			if (isset($get['player_type']) && intval($get['player_type']) == 1){
				$wherestr = 'from_player_id='.$id;
			}
		}

		if (!empty($typeid)){
			$type = in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'coin_tree_count')) ? 'op_type' : 'type';

			$wherestr .= empty($wherestr) ? ''.$type.' IN ('.$typeid.')' : ' AND '.$type.' IN ('.$typeid.')';
		}

		switch ($tbl_key) {
			case 'farmland': $time_column = 'timestamp'; break;
			case 'flower_count': $time_column = 'send_time'; break;
			case 'faction_contribution':
			case 'level_up':
			case 'elixir': 
				$time_column = 'time'; 
				if ($player_role_id > 0) {
					$wherestr .= empty($wherestr) ? 'player_role_id='.$player_role_id.'' : ' AND player_role_id='.$player_id.'';
				}
				break;
			case 'peach':
			case 'state_point': $time_column = 'date'; break;
			default:
			    $time_column = 'change_time';
				break;
		}

		if ($tbl_key == 'fate' && $tbl == 2) {
			$time_column = 'op_time';
		}

		if ($starttime > 0){
			$wherestr .= empty($wherestr) ? $time_column.'>'.$starttime : ' AND '.$time_column.'>'.$starttime;
		}
		if ($endtime > 0){
			$wherestr .= empty($wherestr) ? $time_column.'<'.$endtime : ' AND '.$time_column.'<'.$endtime;
		}

		if ($datetime > 0){
			$datestr = "year='".date('Y', $datetime)."' AND month='".date('n', $datetime)."' AND day='".date('j', $datetime)."'";
			$wherestr .=  empty($datetime) ? $datestr : ' AND '.$datestr;
		}

		if (!empty($name)) {
			$namewhere = "name LIKE '%$name%'";
			$nameid = $tbl_key.'_id';
			if ($tbl_key == 'item_attribute_stone') {
				$nameitem = $this->getdb->get_list("SELECT a.id FROM item a LEFT JOIN attribute_stone b ON a.id=b.item_id WHERE ".$namewhere);
				$nameid = 'item_id';
			}else {
				$this->getdb->table_name = $tbl_key;
				$nameitem = $this->getdb->select($namewhere, 'id, name');
			}
			if (!$nameitem) {
				return array('list'=>array(), 'count'=>0, 'allnum'=>array('getnum'=>0, 'connum'=>0));
			}
			$ids = array();
			foreach ($nameitem as $nkey => $nvalue) {
				$ids[] = $nvalue['id'];
			}
			$wherestr .=  empty($wherestr) ? "{$nameid} IN (".implode(',', $ids).")" : ' AND '."{$nameid} IN (".implode(',', $ids).")";
		}

		$order    = 'id DESC';
		$table_name = 'player_'.$tbl_key.'_change_record'.$tbl;

		if (in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'farmland', 'flower_count', 'coin_tree_count', 'elixir', 'item_attribute_stone', 'faction_contribution'))){
			$table_name = 'player_'.$tbl_key.'_log'.$tbl; 
		}else if (in_array($tbl_key, array('peach', 'defeat_world_boss'))) {
			$table_name = 'player_'.$tbl_key.'_record'.$tbl;
		}else if (in_array($tbl_key, array('level_up'))){
			$table_name = $tbl_key.'_record'.$tbl;
		}

		$this->getdb->table_name = $table_name;
		$list = $this->getdb->get_list_page($wherestr, '*', $order, $page, $pagesize);

		$allnum = array('getnum'=>0, 'connum'=>0);
		if ($recordnum <= 0) {
			$recordnum = $this->getdb->count($wherestr, 'id');

			if (in_array($tbl_key, array('ingot', 'coin', 'item', 'power', 'item_attribute_stone'))){
				$wherestr = !empty($wherestr) ? $wherestr.' AND ' : '';
				$rs1 = $this->getdb->get_one($wherestr.'value>0', 'SUM(value) AS getnum');
				$rs2 = $this->getdb->get_one($wherestr.'value<0', 'SUM(value) AS connum');
				$allnum = array('getnum' => intval($rs1['getnum']), 'connum' => intval($rs2['connum']));
			}
		}
		return array('list'=>$list, 'count'=>$recordnum, 'allnum'=>$allnum);
	}
	/**
	 * 玩家游戏信息
	 * 
	 */ 
	private function player_key_info($tbl_key, $id, $player_role_id=0){
		if (empty($tbl_key) || $id <= 0) return array();

		$this->getdb->table_name = 'player_'.$tbl_key;
		$wherestr = '';
		$wherestr = 'player_id='.$id;
		if ($player_role_id > 0) {
			$wherestr .= ' AND player_role_id='.$player_role_id.'';
		}
		switch ($tbl_key) {
			case 'item':
				if ($player_role_id > 0) {
					$wherestr .= ' AND grid_id>200 AND grid_id<301';
				}else {
					$wherestr .= ' AND grid_id<201';
				}
				break;
		}

		$list = $this->getdb->select($wherestr);
		return $list;
	}
	/**
	 * 铜钱、元宝、灵件记录类别
	 * 消费类别 type=0 获得类别 type=1
	 * 
	 */ 
	private function player_key_record_type($tbl_key){
		if (empty($tbl_key)) return array();
		switch ($tbl_key) {
			case 'soul':
			case 'soul_stone':
				$column = 'id, description as name, type';
				break;
			case 'state_point':
				$column = 'id, name, type';
				break;
			case 'item_attribute_stone':
				$column = 'id, name, type';
				$tbl_key = 'attribute_stone';
				break;
			case 'faction_contribution':
				$column = 'id, type_name as name';
				break;
			default:
				$column = 'id, name';
				break;
		}
		if (in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'coin_tree_count')) ){
			$this->getdb->table_name = $tbl_key.'_log_type';
		}else if (in_array($tbl_key, array('faction_contribution'))) {
			$this->getdb->table_name = $tbl_key.'_type';
		}else {
			$this->getdb->table_name = $tbl_key.'_change_type';
		}

		if (in_array($tbl_key, array('fate', 'take_bible', 'elixir', 'faction_contribution')) ){
			$list['cons'] = array();
			$list['get']  = $this->getdb->select('', $column);
		}else {
			$list['cons'] = $this->getdb->select('type=0', $column);
			$list['get']  = $this->getdb->select('type=1', $column);
		}
		return $list;
	}
	/**
	 * 灵件属性
	 * 
	 */ 
	private function player_soul_extend(){
		if (isset($_GET['noall'])) {
			$sql  = "SELECT a.id, a.name, a.soul_quality_id, b.name as qualityname FROM soul a 
					LEFT JOIN soul_quality b ON a.soul_quality_id=b.id";
		}else {			
			$sql  = "SELECT a.id, a.name, a.soul_quality_id, b.name as qualityname,c.name as typename FROM soul a 
					LEFT JOIN soul_quality b ON a.soul_quality_id=b.id 
					LEFT JOIN soul_all_type c ON a.soul_all_type_id=c.soul_type_id;";
		}

        $soul = $this->getdb->get_list($sql);
        foreach ($soul as $key => $value) {
            if (strpos($value['name'], '_') ===  false) {
                $list['soul'][] = $value;
            }
        }

		$sql  = "SELECT a.id, b.name, unit, a.min, a.max, a.soul_quality_id, c.name as typename FROM soul_attribute a 
					LEFT JOIN war_attribute_type b ON a.war_attribute_type_id=b.id LEFT JOIN soul_quality c ON a.soul_quality_id=c.id;";
		$list['attribute'] = $this->getdb->get_list($sql);
        foreach ($list['attribute'] as $key => $value) {
            $list['soulattr'][$value['soul_quality_id']][] = $value;
        }

		return $list;
	}
	/**
	 * 命格属性
	 * 
	 */ 
	private function player_fate_extend($id){
		$id = intval($id);
		$list['role'] = array();
		$this->getdb->table_name = 'player_role';
		$player_rolelist = $this->getdb->select(array('player_id' => $id), 'id,role_id');
		if ($player_rolelist){
			$this->getdb->table_name = 'role';
			$rolelist = $this->getdb->select('', 'id,name');
			foreach ($rolelist as $key => $value) {
				$role[$value['id']] = $value['name'];
			}
			foreach ($player_rolelist as $key => $value) {
				$player_rolelist[$key]['name'] = $role[$value['role_id']];
			}
			$list['role'] = $player_rolelist;
			unset($rolelist, $role, $player_rolelist);
		}
		$sql  = "SELECT a.id, a.name, a.type, b.name as qualityname FROM fate a 
					LEFT JOIN fate_quality b ON a.fate_quality_id=b.id;";
		$list['fate'] = $this->getdb->get_list($sql);
		return $list;
	}
}
