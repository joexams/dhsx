<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
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
			$server = $serverdb->get_one(array('sid' => $sid), 'db_server,db_root,db_pwd,db_name,server_ver,api_server,api_port,api_pwd');
			
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
	 * 获取英雄列表
	 * 
	 */ 
	public function hero_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);
		if ($dbflag){
			$sql = 'select a.id,concat(substr(a.pinyin,1,1),b.text) name from hero a,chinese_text b where a.name_text_id=b.id and a.`vip`<3 order by name';
			$data['list'] = $this->getdb->get_list($sql);
			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 获取伙伴列表
	 * 
	 */ 
	public function role_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$player = isset($_GET['player']) ? trim($_GET['player']) : 0;
		$player_type = isset($_GET['player_type']) ? intval($_GET['player_type']) : 0;
		switch ($player_type) {
			case 2:
				$param = "nickname = '$player'";
				break;
			case 1:
				$param = "username = '$player'";
				break;		
			default:
				$param = "nickname = '$player'";
				break;
		}
		$public = common::load_model('public_model');
		$playerinfo = $public->get_player_id($sid,$param);
		$player_id = $playerinfo['id'];
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$sql = "select b.id,c.text name from player_role a,role b,chinese_text c,hero d where a.player_id='$player_id' and a.role_id=b.id and b.hero_id=d.id and d.name_text_id=c.id";
			$data['list'] = $this->getdb->get_list($sql);
			$data['count'] = count($data['list']);
			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 获取玩家信息列表
	 * 
	 */ 
	public function public_player_list() {
		$sid   = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$order = isset($_GET['order']) ? trim($_GET['order']) : '';
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
			$pagesize = 20;
			$page = max(intval($page), 1);
			$offset = $pagesize*($page-1);
			$str_order = '';
			switch ($order) {
				case 'vip':
					$str_order = 'a.vip_level DESC, a.id DESC';
					break;
				case 'ingot':
					$str_order = 'b.ingot DESC';
					break;
				case 'coin':
					$str_order = 'b.coin DESC';
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
		
			$sql = "SELECT 
						a.id,a.username,a.nickname,a.vip_level,b.ingot,b.coin,b.power,c.last_login_ip,c.last_login_time,c.last_offline_time
					FROM player a 
					LEFT JOIN player_data b ON a.id=b.player_id
					left join player_trace c on a.id=c.player_id
					$wherestr 
					ORDER BY $str_order  
					LIMIT $offset,$pagesize;";
			$list = $this->getdb->get_list($sql);
			if ($recordnum <= 0 && $list) {
				$countsql = "SELECT COUNT(a.id) as num FROM player a $wherestr;";
				$count = $this->getdb->get_count($countsql);
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
		if (isset($_GET['doSubmit'])) {
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
		$key_array = array('role');
		if (!in_array($key, $key_array))	output_json(1);

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
							$items[] = $value['item_id'];
						}
						$this->getdb->table_name = 'item';
						$data['type']['item'] = $this->getdb->select('id IN ('.implode($items, ',').')');
						$this->getdb->table_name = 'backpack_type';
						$data['type']['backpack_type'] = $this->getdb->select();
						$this->getdb->table_name = 'skill_base';
						$data['type']['skill_base'] = $this->getdb->select();
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
			$data['key'] = $key;
			output_json(0, '', $data);
		}
		output_json(1);
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

		$key_array = array('coin', 'ingot', 'soul', 'soul_stone', 'item', 'fame', 'fate', 'power', 'take_bible', 'skill', 'farmland', 'flower_count', 'coin_tree_count', 'elixir', 'state_point', 'peach', 'defeat_world_boss', 'level_up', 'mission', 'item_attribute_stone', 'faction_contribution', 'spirit', 'xian_ling', 'xianling_tree', 'long_yu_ling', 'deploy_start', 'crystal', 'marry_favor', 'dragonball','pearl' ,'feats','blood_pet_chip','blood_pet','ling_yun','neidan','ba_xian_ling','marry_gold');
		if ($dbflag && in_array($key, $key_array)){
			$rtn = $this->player_key_record($key, $_GET);
			$data['list']  = $rtn['list'];
			$data['count'] = $rtn['count'];
			if (in_array($key, array('ingot', 'coin','item', 'power', 'item_attribute_stone', 'crystal', 'marry_favor'))){
				$data['allnum'] = $rtn['allnum'];
			}

			if ($data['count'] > 0){
				//只读一次
				if ($typeflag == 0){
					if (!in_array($key, array('farmland', 'flower_count', 'peach', 'defeat_world_boss', 'level_up', 'mission'))){
						$trtn = $this->player_key_record_type($key);
						$data['type']['cons'] = $trtn['cons'];
						$data['type']['get']  = $trtn['get'];
						unset($trtn);
					}
					
					switch ($key) {
						case 'deploy_start':
							$srtn = $this->player_deploy_start_extend();
							$data['type']['soul']      = $srtn['soul'];
							$data['type']['attribute'] = $srtn['attribute'];
							unset($srtn);
							break;
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
						case 'elixir':
							$this->getdb->table_name = 'item';
							$data['type']['item'] = $this->getdb->select('', 'id,name');
							break;
						case 'defeat_world_boss':
							$sql = "SELECT a.id,b.name FROM world_boss a LEFT JOIN town b ON b.id=a.town_id";
							$data['type']['boss'] = $this->getdb->get_list($sql);
							break;
						case 'dragonball':
							$this->getdb->table_name = 'dragonball';
							$data['type']['dragonball'] = $this->getdb->select('', 'id,name');
							break;
						case 'blood_pet_chip':
							$this->getdb->table_name = 'item';
							$data['type']['item'] = $this->getdb->select('type_id=45000', 'id,name');
							break;
						case 'blood_pet':
							$this->getdb->table_name = 'item';
							$data['type']['item'] = $this->getdb->select('type_id=45000', 'id,name');
							break;
						case 'mission':
							$this->getdb->table_name = 'mission';
							$data['type']['mission'] = $this->getdb->select('', 'id,name');
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
								$this->getdb->table_name = 'player';
								$data['type']['players'] = $this->getdb->select('id IN ('.implode($be_rob_players, ',').')', 'id,username,nickname');
							}
						}						
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
					case 'mission':
						foreach ($data['list'] as $key => $value) {
							$missions[] = $value['mission_id'];
						}
						$sql = "SELECT a.id, a.name as missionname, b.name as sectionname FROM mission a LEFT JOIN mission_section b ON a.mission_section_id=b.id WHERE a.id IN (".implode($missions, ',').")";
						$sections = $this->getdb->get_list($sql);
						foreach ($sections as $key => $value) {
							$data['mission'][$value['id']] = $value;
						}
						break;
				}
			}
			unset($rtn);
			output_json(0, '', $data);
		}
		output_json(1);
	}
	/**
	 * 服务器玩家统计
	 * @return [type] [description]
	 */
	public function server_player_stat(){
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$name= isset($_GET['name']) ? trim($_GET['name']) : '';
		$combined_to = isset($_GET['combined_to']) ? intval($_GET['combined_to']) : 0;
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
		
		$combined_to == 0 ? $dbflag = $this->set_db($sid) : '';
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
		$data['list'] = $this->getdb->get_list("select a.id, b.text as name from item a,chinese_text b where a.name_text_id=b.id and a.item_type_id=$typeid");

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
	 * 获取游戏服城镇在线人数
	 */
	public function player_townonline()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) {
			output_json(1, Lang('error'));
		}
		$dbflag = $this->set_db($sid);
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}

		$sql = "SELECT id, name FROM town WHERE type IN (0, 2) ORDER BY id ASC";
		$list = $this->getdb->get_list($sql);
		$serverdb  = common::load_model('public_model');
		$serverdb->table_name = 'servers';
		$server = $serverdb->get_one(array('sid' => $sid), 'name,o_name,api_server,api_port,api_pwd,server_ver');
		if (!empty($server['api_server']) && !empty($server['api_port']) && !empty($server['api_pwd']) && !empty($server['server_ver'])){
			$version = trim($server['server_ver']);
			$api_admin = common::load_api_class('api_admin', $version);
			if ($api_admin !== false){
				$api_admin::$SERVER    = $server['api_server'];
				$api_admin::$PORT      = $server['api_port'];
				$api_admin::$ADMIN_PWD = $server['api_pwd'];
				$town_list = array();
				$i = 0;
				foreach ($list as $key => $value) {
					$town_list[$i]['name'] = $value['name'];
					$count  = $api_admin::get_town_player_count($value['id']);
					$town_list[$i]['player_count'] = $count ? $count['player_count'] : 0;
					$town_list[$i]['rank'] = $i+1;
					$i++;
				}
				$online  = $api_admin::get_all_town_player_count();
				$allonline = $online ? $online['player_count'] : 0;
				$data['list'] = $town_list;
				$data['count'] = $allonline;
				output_json(0, '', $data);
			}
		}
	}
	/**
	 * 获取游戏服新用户留存
	 */
	public function player_stay()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) {
			output_json(1, Lang('error'));
		}
		$serverdb  = common::load_model('public_model');
		$serverdb->table_name = 'servers';
		$server = $serverdb->get_one(array('sid' => $sid), 'open_date');
		$server_date = substr($server['open_date'],0,10);
		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) {
			output_json(1, Lang('error'));
		}
		for ($i=0;$i<7;$i++){
			$open_date = date("Y-m-d",strtotime("$server_date +$i day"));		
			$regist_sql = "select player_id from player_trace where FROM_UNIXTIME(first_login_time,'%Y-%m-%d') = '$open_date'";
			$sql = "select 
					COUNT(case when FROM_UNIXTIME(first_login_time,'%Y-%m-%d') = '$open_date' then player_id end) AS regist_num,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 1 day) and player_id in ($regist_sql) then player_id end) AS two_day_stay,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 2 day) and player_id in ($regist_sql) then player_id end) AS three_day_stay,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 3 day) and player_id in ($regist_sql) then player_id end) AS four_day_stay,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 4 day) and player_id in ($regist_sql) then player_id end) AS five_day_stay,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 5 day) and player_id in ($regist_sql) then player_id end) AS six_day_stay,
					COUNT(case when FROM_UNIXTIME(last_login_time,'%Y-%m-%d') = date_add('$open_date', interval 6 day) and player_id in ($regist_sql) then player_id end) AS seven_day_stay
					from player_trace";
			$list = $this->getdb->get_list($sql);
			$stay_list = $list[0];
			$data_list[$i]['regist_date'] = $open_date;
			$data_list[$i]['regist_num'] = $stay_list['regist_num'];
			$data_list[$i]['two_day_stay_per'] = $stay_list['two_day_stay']>0?round($stay_list['two_day_stay']/$stay_list['regist_num'],2)*100:0;
			$data_list[$i]['three_day_stay_per'] = $stay_list['three_day_stay']>0?round($stay_list['three_day_stay']/$stay_list['regist_num'],2)*100:0;
			$data_list[$i]['four_day_stay_per'] = $stay_list['four_day_stay']>0?round($stay_list['four_day_stay']/$stay_list['regist_num'],2)*100:0;
			$data_list[$i]['five_day_stay_per'] = $stay_list['five_day_stay']>0?round($stay_list['five_day_stay']/$stay_list['regist_num'],2)*100:0;
			$data_list[$i]['six_day_stay_per'] = $stay_list['six_day_stay']>0?round($stay_list['six_day_stay']/$stay_list['regist_num'],2)*100:0;
			$data_list[$i]['seven_day_stay_per'] = $stay_list['seven_day_stay']>0?round($stay_list['seven_day_stay']/$stay_list['regist_num'],2)*100:0;
		}
		$data['list'] = $data_list;
		$data['count'] = count($data_list);
		output_json(0, '', $data);
	}
	/**
	 * 获取新手流失
	 */
	public function player_lossnewer()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));

		$sql = "SELECT 
			COUNT(case when E.level < 2 then A.id end) AS player_1_level,
			COUNT(case when C.state = 0 then A.id end) AS quser_1,
			COUNT(case when C.state = 1 then A.id end) AS quser_3,
			COUNT(case when C.state = 2 then A.id end) AS quser_1_no,
			COUNT(case when D.state = 0 then A.id end) AS quser_2,
			COUNT(case when E.experience = 220 then A.id end) AS no_kill,
			COUNT(case when H.type_id = 2 then A.id end) AS no_item,
			COUNT(case when G.x = 200 AND G.y = 450 then A.id end) AS no_move
		FROM 
			player A
			LEFT JOIN player_quest C ON A.id = C.player_id AND C.quest_id = 1
			LEFT JOIN player_quest D ON A.id = D.player_id AND D.quest_id = 2
			LEFT JOIN player_role E ON A.id = E.player_id AND A.main_role_id = E.id
			LEFT JOIN player_item F ON A.id = F.player_id AND F.grid_id = 1
			LEFT JOIN player_last_pos G ON A.id = G.player_id AND G.town_id = 1
			LEFT JOIN item H ON F.item_id = H.id
		WHERE
			A.nickname <> '' 
			AND A.is_tester = 0 
			AND E.level <= 2";
		$list = $this->getdb->get_list($sql);
		$data['list'] = $list[0];

		$sql = "SELECT 
			COUNT(A.id) AS player_num,
			COUNT(case when A.nickname = '' then A.id end) AS player_no_role,
			COUNT(case when B.level >= 2 then A.id end) AS player_2_level
		FROM 
			player A 
			LEFT JOIN player_role B ON A.id = B.player_id AND A.main_role_id = B.id
		WHERE 
			A.id <> 0 
			AND A.is_tester = 0";
		$alist = $this->getdb->get_list($sql);
		$data['list'] = array_merge($data['list'], $alist[0]); 

		$sql = "SELECT COUNT(player_id) AS num FROM player_mission_record WHERE mission_id = 1 AND current_monster_team_lock = 0";
		$data['list']['mission'] = $this->getdb->get_count($sql);

		output_json(0, '', $data);
	}
	public function player_asset()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));

		$sql ="SELECT 		
			sum(A.ingot) as ingot_num,
			count(case when A.ingot <> '' then A.player_id end) as ingot_player,
			sum(A.coins) as coins_num,
			count(case when A.coins <> '' then A.player_id end) as coins_player
		FROM 
			player_data A
			LEFT JOIN player B on A.player_id = B.id
		WHERE 
			B.is_tester = 0
		";
		$all = $this->getdb->get_row($sql);

		$sql = "SELECT 		
			count(case when A.ingot >= 1 and A.ingot <= 100 then A.player_id end) as ingot_player_1,
			count(case when A.ingot >= 101 and A.ingot <= 500 then A.player_id end) as ingot_player_2,
			count(case when A.ingot >= 501 and A.ingot <= 1000 then A.player_id end) as ingot_player_3,
			count(case when A.ingot >= 1001 and A.ingot <= 5000 then A.player_id end) as ingot_player_4,
			count(case when A.ingot >= 5001 and A.ingot <= 10000 then A.player_id end) as ingot_player_5,
			count(case when A.ingot >= 10001 and A.ingot <= 50000 then A.player_id end) as ingot_player_6,
			count(case when A.ingot >= 50001 and A.ingot <= 100000 then A.player_id end) as ingot_player_7,
			count(case when A.ingot >= 100001 and A.ingot <= 500000 then A.player_id end) as ingot_player_8,
			count(case when A.ingot >= 500001 then A.player_id end) as ingot_player_9,
			
			sum(if(A.ingot >= 1 and A.ingot <= 100,A.ingot,0)) as ingot_num_1 ,
			sum(if(A.ingot >= 101 and A.ingot <= 500,A.ingot,0)) as ingot_num_2 ,
			sum(if(A.ingot >= 501 and A.ingot <= 1000,A.ingot,0)) as ingot_num_3 ,
			sum(if(A.ingot >= 1001 and A.ingot <= 5000,A.ingot,0)) as ingot_num_4 ,
			sum(if(A.ingot >= 5001 and A.ingot <= 10000,A.ingot,0)) as ingot_num_5 ,
			sum(if(A.ingot >= 10001 and A.ingot <= 50000,A.ingot,0)) as ingot_num_6 ,
			sum(if(A.ingot >= 50001 and A.ingot <= 100000,A.ingot,0)) as ingot_num_7 ,
			sum(if(A.ingot >= 100001 and A.ingot <= 500000,A.ingot,0)) as ingot_num_8 ,
			sum(if(A.ingot >= 500001,A.ingot,0)) as ingot_num_9,
			
			count(case when A.coins >= 1 and A.coins <= 100 then A.player_id end) as coins_player_1,
			count(case when A.coins >= 101 and A.coins <= 500 then A.player_id end) as coins_player_2,
			count(case when A.coins >= 501 and A.coins <= 1000 then A.player_id end) as coins_player_3,
			count(case when A.coins >= 1001 and A.coins <= 5000 then A.player_id end) as coins_player_4,
			count(case when A.coins >= 5001 and A.coins <= 10000 then A.player_id end) as coins_player_5,
			count(case when A.coins >= 10001 and A.coins <= 50000 then A.player_id end) as coins_player_6,
			count(case when A.coins >= 50001 and A.coins <= 100000 then A.player_id end) as coins_player_7,
			count(case when A.coins >= 100001 and A.coins <= 500000 then A.player_id end) as coins_player_8,
			count(case when A.coins >= 500001 and A.coins <= 1000000 then A.player_id end) as coins_player_9,
			count(case when A.coins >= 1000001 and A.coins <= 5000000 then A.player_id end) as coins_player_10,
			count(case when A.coins >= 5000001 and A.coins <= 10000000 then A.player_id end) as coins_player_11,
			count(case when A.coins >= 10000001 then A.player_id end) as coins_player_12,

			sum(if(A.coins >= 1 and A.coins <= 100,A.coins,0)) as coins_num_1 ,
			sum(if(A.coins >= 101 and A.coins <= 500,A.coins,0)) as coins_num_2 ,
			sum(if(A.coins >= 501 and A.coins <= 1000,A.coins,0)) as coins_num_3 ,
			sum(if(A.coins >= 1001 and A.coins <= 5000,A.coins,0)) as coins_num_4 ,
			sum(if(A.coins >= 5001 and A.coins <= 10000,A.coins,0)) as coins_num_5 ,
			sum(if(A.coins >= 10001 and A.coins <= 50000,A.coins,0)) as coins_num_6 ,
			sum(if(A.coins >= 50001 and A.coins <= 100000,A.coins,0)) as coins_num_7 ,
			sum(if(A.coins >= 100001 and A.coins <= 500000,A.coins,0)) as coins_num_8 ,
			sum(if(A.coins >= 500001 and A.coins <= 1000000,A.coins,0)) as coins_num_9 ,
			sum(if(A.coins >= 1000001 and A.coins <= 5000000,A.coins,0)) as coins_num_10 ,
			sum(if(A.coins >= 5000001 and A.coins <= 10000000,A.coins,0)) as coins_num_11 ,
			sum(if(A.coins >= 10000001,A.coins,0)) as coins_num_12
		FROM 
			player_data A
			left join player B on A.player_id = B.id
		WHERE 
			B.is_tester = 0
		";
		$section = $this->getdb->get_row($sql);

		$data['list']['ingot_all'] = intval($all['ingot_num']);
		$data['list']['coins_all'] = intval($all['coins_num']);
		$data['list']['ingot_hold'] = intval($all['ingot_player']);
		$data['list']['coins_hold'] = intval($all['coins_player']);

		if($section){
			$data['list']['ingot'] = array(
				array('name' => '1 - 100', 'player' => $section['ingot_player_1'],'num' => $section['ingot_num_1']),
				array('name' => '101 - 500', 'player' => $section['ingot_player_2'],'num' => $section['ingot_num_2']),
				array('name' => '501 - 1000', 'player' => $section['ingot_player_3'],'num' => $section['ingot_num_3']),
				array('name' => '1001 - 5000', 'player' => $section['ingot_player_4'],'num' => $section['ingot_num_4']),
				array('name' => '5001 - 10000', 'player' => $section['ingot_player_5'],'num' => $section['ingot_num_5']),
				array('name' => '10001 - 50000', 'player' => $section['ingot_player_6'],'num' => $section['ingot_num_6']),
				array('name' => '50001 - 100000', 'player' => $section['ingot_player_7'],'num' => $section['ingot_num_7']),
				array('name' => '100001 - 500000', 'player' => $section['ingot_player_8'],'num' => $section['ingot_num_8']),
				array('name' => '500001 - ∞', 'player' => $section['ingot_player_9'],'num' => $section['ingot_num_9'])
			);
			$data['list']['coins'] = array(
				array('name' => '1 - 100', 'player' => $section['coins_player_1'],'num' => $section['coins_num_1']),
				array('name' => '101 - 500', 'player' => $section['coins_player_2'],'num' => $section['coins_num_2']),
				array('name' => '501 - 1000', 'player' => $section['coins_player_3'],'num' => $section['coins_num_3']),
				array('name' => '1001 - 5000', 'player' => $section['coins_player_4'],'num' => $section['coins_num_4']),
				array('name' => '5001 - 10000', 'player' => $section['coins_player_5'],'num' => $section['coins_num_5']),
				array('name' => '10001 - 50000', 'player' => $section['coins_player_6'],'num' => $section['coins_num_6']),
				array('name' => '50001 - 100000', 'player' => $section['coins_player_7'],'num' => $section['coins_num_7']),
				array('name' => '100001 - 500000', 'player' => $section['coins_player_8'],'num' => $section['coins_num_8']),
				array('name' => '500001 - 1000000', 'player' => $section['coins_player_9'],'num' => $section['coins_num_9']),
				array('name' => '1000001 - 5000000', 'player' => $section['coins_player_10'],'num' => $section['coins_num_10']),
				array('name' => '5000001 - 10000000', 'player' => $section['coins_player_11'],'num' => $section['coins_num_11']),
				array('name' => '10000001 - ∞', 'player' => $section['coins_player_12'],'num' => $section['coins_num_12'])
			);		
		}

		unset($all, $section);
		output_json(0, '', $data);
	}
	/**
	 * 装备统计
	 */
	public function player_item()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));

		$sql = "SELECT 
			count(A.id) as num,
			count(case when B.quality = 1 then A.id end) as num_1,
			count(case when B.quality = 2 then A.id end) as num_2,
			count(case when B.quality = 3 then A.id end) as num_3,
			count(case when B.quality = 4 then A.id end) as num_4,
			count(case when B.quality = 5 then A.id end) as num_5,
			
			count(distinct(A.player_id)) as player,

			count(distinct(case when B.quality = 1 then A.player_id end)) as player_1,
			count(distinct(case when B.quality = 2 then A.player_id end)) as player_2,
			count(distinct(case when B.quality = 3 then A.player_id end)) as player_3,
			count(distinct(case when B.quality = 4 then A.player_id end)) as player_4,
			count(distinct(case when B.quality = 5 then A.player_id end)) as player_5
		FROM 
			player_item A,
			item B,
			player C
		WHERE 
			A.item_id = B.id
			and B.type_id <= 6
			and A.player_id = C.id
			and C.is_tester = 0			
		";
		$total = $this->getdb->get_row($sql);
		$total['upgrade_level'] = 0;

		$sql = "SELECT 
			A.upgrade_level,
			count(A.id) as num,
			count(case when B.quality = 1 then A.id end) as num_1,
			count(case when B.quality = 2 then A.id end) as num_2,
			count(case when B.quality = 3 then A.id end) as num_3,
			count(case when B.quality = 4 then A.id end) as num_4,
			count(case when B.quality = 5 then A.id end) as num_5,
			
			count(distinct(A.player_id)) as player,
			count(distinct(case when B.quality = 1 then A.player_id end)) as player_1,
			count(distinct(case when B.quality = 2 then A.player_id end)) as player_2,
			count(distinct(case when B.quality = 3 then A.player_id end)) as player_3,
			count(distinct(case when B.quality = 4 then A.player_id end)) as player_4,
			count(distinct(case when B.quality = 5 then A.player_id end)) as player_5
		FROM 
			player_item A,
			item B,
			player C
		WHERE 
			A.item_id = B.id
			and B.type_id <= 6
			and A.player_id = C.id
			and C.is_tester = 0
		GROUP BY 
			A.upgrade_level	
		";
		$list = $this->getdb->get_list($sql);
		$list = array_merge(array($total), $list);
		$data['list'] = $list;
		output_json(0, '', $data);
	}
	/**
	 * 命格统计
	 */
	public function player_fate()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));

		$vip_type = isset($_GET['vip_type']) ? intval($_GET['vip_type']) : 0;
		switch ($vip_type) {
			case 1:
				$wherestr = " AND vip_level>=6";
				break;
			case 2:
				$wherestr = " AND vip_level>=1 AND vip_level < 6";
				break;
			default:
				$wherestr = " AND vip_level<1 AND nickname<>''";
				break;
		}

		$fate_list = array();
		$sql = "SELECT 		
				COUNT(distinct(b.player_id)) AS player_count,
				COUNT(b.fate_id) AS num
				FROM 
					fate a
					left join player_fate b on a.id = b.fate_id
					left join player c on b.player_id = c.id
				WHERE a.fate_quality_id > 1 and c.is_tester = 0 $wherestr";
		$list = $this->getdb->get_row($sql);
		$fate_list[0] = $list;
		$fate_list[0]['id'] = 0;
		$fate_list[0]['fate_name'] = '总计';
		$fate_list[0]['quality_name'] = '';

		$sql = "SELECT a.id, a.name as fate_name, d.name as quality_name,
					   COUNT(DISTINCT(player_id)) AS player_count,
					   COUNT(fate_id) AS num
				FROM fate a 
				LEFT JOIN player_fate b ON a.id=b.fate_id 
				LEFT JOIN player c ON b.player_id=c.id 
				LEFT JOIN fate_quality d on a.fate_quality_id = d.id
				WHERE a.fate_quality_id>1 AND c.is_tester=0 $wherestr 
				GROUP BY a.id ORDER BY num DESC, a.id DESC";
		$list = $this->getdb->get_list($sql);
		foreach ($list as $key => $value) {
			$fate_ids[] = $value['id'];
		}
		$fate_list = array_merge($fate_list, $list);
		if ($fate_ids) {
			$sql = "SELECT a.id, a.name AS fate_name, b.name AS quality_name 
					FROM fate a 
					LEFT JOIN fate_quality b ON a.fate_quality_id=b.id 
					WHERE a.fate_quality_id>1 AND a.id NOT IN (".implode(',', $fate_ids).") 
					ORDER BY a.id DESC";
			$list = $this->getdb->get_list($sql);
			foreach ($list as $key => $value) {
				$value['player_count'] = 0;
				$value['num'] = 0;
				$fate_list[] = $value;
			}
		}

		$data['list'] = $fate_list;
		unset($fate_list, $list);
		output_json(0, '', $data);
	}
	/**
	 * 伙伴统计
	 */
	public function player_role()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));

		$role_list = array();
		$sql = "SELECT A.id,A.name AS role_name, A.fame,
					   COUNT(B.role_id) AS role_count,
					   COUNT(CASE WHEN B.state = 0 THEN B.role_id END) AS role_in_count,
					   COUNT(CASE WHEN B.state = 1 THEN B.role_id END) AS role_out_count
				FROM role A
				LEFT JOIN player_role B on A.id = B.role_id
				WHERE A.lock >= 5 
				GROUP BY A.id
				ORDER BY role_count DESC, A.id DESC";
		$list = $this->getdb->get_list($sql);

		foreach ($list as $key => $value) {
			$role_list[] = $value;
			$role_ids[] = $value['id'];
		}

		if ($role_ids) {
			$sql = "SELECT id, name as role_name, fame 
					FROM role
					WHERE `lock` >= 5 AND `id` not in (".implode(',', $role_ids).") 
					ORDER BY id DESC";
			$list = $this->getdb->get_list($sql);
			foreach ($list as $key => $value) {
				$value['role_count'] = 0;
				$value['role_in_count'] = 0;
				$value['role_out_count'] = 0;
				$role_list[] = $value;
			}
		}

		$data['list'] = $role_list;
		output_json(0, '', $data);
	}
	/**
	 * 体力统计
	 */
	public function player_power()
	{
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		if ($sid <= 0) output_json(1, Lang('error'));

		$dbflag = $this->set_db($sid, true);
		if (!$dbflag) output_json(1, Lang('error'));
		$wherestr = '';
		$level_type = isset($_GET['level_type']) ? intval($_GET['level_type']) : 0;
		if ($level_type > 0) {
			$wherestr = " AND  level<=20";
		}else {
			$wherestr = " AND level>20";
		}

		$sql = "SELECT COUNT(CASE when A.nickname <> '' then A.id end) AS power_player,
					COUNT(CASE when A.nickname <> '' AND C.total_ingot > 0 then A.id end) AS power_player_pay,
					COUNT(CASE when A.nickname <> '' AND DATE_FORMAT(FROM_UNIXTIME(B.last_login_time), '%Y-%m-%d') = CURDATE() then A.id end) AS power_player_today
				FROM 
					player A 
				LEFT JOIN player_trace B ON A.id = B.player_id
				LEFT JOIN player_charge_record C ON A.id = C.player_id
				LEFT JOIN player_role D ON A.id = D.player_id AND A.main_role_id = D.id
				WHERE A.is_tester = 0 $wherestr";
		$total = $this->getdb->get_row($sql);

		$sql = "SELECT 	
				COUNT(case when A.power = 0 and A.power <= 5  then A.player_id end) as power_player_0,
				COUNT(case when A.power >= 6 and A.power <= 20 then A.player_id end) as power_player_1,
				COUNT(case when A.power >= 21 and A.power <= 50 then A.player_id end) as power_player_2,
				COUNT(case when A.power >= 51 and A.power <= 100 then A.player_id end) as power_player_3,
				COUNT(case when A.power >= 101 and A.power <= 200 then A.player_id end) as power_player_4,
				COUNT(case when A.power >= 201 and A.power <= 300 then A.player_id end) as power_player_5,
				COUNT(case when A.power >= 301 and A.power <= 400 then A.player_id end) as power_player_6,
				COUNT(case when A.power >= 401 and A.power <= 500 then A.player_id end) as power_player_7,
				COUNT(case when A.power >= 501 and A.power <= 600 then A.player_id end) as power_player_8,
				COUNT(case when A.power >= 601 and A.power <= 700 then A.player_id end) as power_player_9,
				COUNT(case when A.power >= 701 and A.power <= 800 then A.player_id end) as power_player_10,
				COUNT(case when A.power >= 801 and A.power <= 900 then A.player_id end) as power_player_11,
				COUNT(case when A.power >= 901 and A.power <= 1000 then A.player_id end) as power_player_12,
				COUNT(case when A.power >= 1001 then A.player_id end) as power_player_13,
				
				COUNT(case when A.power = 0 and A.power <= 5 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_0,
				COUNT(case when A.power >= 6 and A.power <= 20 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_1,
				COUNT(case when A.power >= 21 and A.power <= 50 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_2,
				COUNT(case when A.power >= 51 and A.power <= 100 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_3,
				COUNT(case when A.power >= 101 and A.power <= 200 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_4,
				COUNT(case when A.power >= 201 and A.power <= 300 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_5,
				COUNT(case when A.power >= 301 and A.power <= 400 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_6,
				COUNT(case when A.power >= 401 and A.power <= 500 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_7,
				COUNT(case when A.power >= 501 and A.power <= 600 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_8,
				COUNT(case when A.power >= 601 and A.power <= 700 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_9,
				COUNT(case when A.power >= 701 and A.power <= 800 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_10,
				COUNT(case when A.power >= 801 and A.power <= 900 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_11,
				COUNT(case when A.power >= 901 and A.power <= 1000 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_12,
				COUNT(case when A.power >= 1001 and DATE_FORMAT(FROM_UNIXTIME(E.last_login_time), '%Y-%m-%d') = CURDATE() then A.player_id end) as power_player_today_13,

				COUNT(case when A.power = 0 and A.power <= 5 and C.total_ingot > 0  then A.player_id end) as power_player_pay_0,
				COUNT(case when A.power >= 6 and A.power <= 20 and C.total_ingot > 0 then A.player_id end) as power_player_pay_1,
				COUNT(case when A.power >= 21 and A.power <= 50 and C.total_ingot > 0 then A.player_id end) as power_player_pay_2,
				COUNT(case when A.power >= 51 and A.power <= 100 and C.total_ingot > 0 then A.player_id end) as power_player_pay_3,
				COUNT(case when A.power >= 101 and A.power <= 200 and C.total_ingot > 0 then A.player_id end) as power_player_pay_4,
				COUNT(case when A.power >= 201 and A.power <= 300 and C.total_ingot > 0 then A.player_id end) as power_player_pay_5,
				COUNT(case when A.power >= 301 and A.power <= 400 and C.total_ingot > 0 then A.player_id end) as power_player_pay_6,
				COUNT(case when A.power >= 401 and A.power <= 500 and C.total_ingot > 0 then A.player_id end) as power_player_pay_7,
				COUNT(case when A.power >= 501 and A.power <= 600 and C.total_ingot > 0 then A.player_id end) as power_player_pay_8,
				COUNT(case when A.power >= 601 and A.power <= 700 and C.total_ingot > 0 then A.player_id end) as power_player_pay_9,
				COUNT(case when A.power >= 701 and A.power <= 800 and C.total_ingot > 0 then A.player_id end) as power_player_pay_10,
				COUNT(case when A.power >= 801 and A.power <= 900 and C.total_ingot > 0 then A.player_id end) as power_player_pay_11,
				COUNT(case when A.power >= 901 and A.power <= 1000 and C.total_ingot > 0 then A.player_id end) as power_player_pay_12,
				COUNT(case when A.power >= 1001 and C.total_ingot > 0 then A.player_id end) as power_player_pay_13		
			FROM player_data A
			LEFT JOIN player B on A.player_id = B.id
			LEFT JOIN player_charge_record C on A.player_id = C.player_id
			LEFT JOIN player_role D on B.id = D.player_id and B.main_role_id = D.id
			LEFT JOIN player_trace E on A.player_id = E.player_id
			WHERE B.is_tester = 0 $wherestr";
		$level_list = $this->getdb->get_row($sql);

		$power_list = array(
			0 => '0 - 5',
			1 => '6 - 20',
			2 => '21 - 50',
			3 => '51 - 100',
			4 => '101 - 200',
			5 => '201 - 300',
			6 => '301 - 400',
			7 => '401 - 500',
			8 => '501 - 600',
			9 => '601 - 700',
			10 => '701 - 800',
			11 => '801 - 900',
			12 => '901 - 1000',
			13 => '1001 - ∞',
		);
		$list = array();
		foreach ($level_list as $key => $value) {
			$key_arr = explode('_', $key);
			$new_key = '';
			if (count($key_arr) == 3) {
				$new_key = 'all';
			}else {
				if (strpos($key, 'pay') !==  false) {
					$new_key = 'pay';
				}else {
					$new_key = 'today';
				}
			}
			$k = intval(end($key_arr));

			$list[$k][$new_key] = intval($value);
			if (!isset($list[$k]['name']))	{
				$list[$k]['name'] = $power_list[$k];
			}
			if (isset($list[$k][$new_key.'_rate']))	continue;

			if ($new_key == 'all') {
				$list[$k][$new_key.'_rate'] = intval($value) > 0 ? round(intval($value)*100/$total['power_player'], 2) : 0;
			}else {
				$list[$k][$new_key.'_rate'] = intval($value) > 0 ? round(intval($value)*100/$total['power_player_'.$new_key], 2) : 0;
			}
		}
		$data['list'] = $list;
		output_json(0, '', $data);
	}
	/**
	 * 设置查询玩家信息条件
	 * 
	 */ 
	private function set_player_search($get){
		$wherestr     = '';
		$username     = isset($get['username']) ? trim(safe_replace($get['username'])) : '';

		if (!empty($username)){
			$username = str_replace(array("\r\n", "\n", "\r"), ',', $username);
			$username = explode(',', $username);
			if (count($username) > 1) {
				$wherestr = 'WHERE '.to_sqls($username, '', 'username');
			}else {
				if (is_numeric($username[0])) {
					$wherestr = " WHERE id='$username[0]'";
				}else if (preg_match("/^([a-zA-Z0-9]|\.)+$/",$username[0]) > 0 && strlen($username[0]) >= 32) {
					$wherestr = " WHERE username='$username[0]' OR nickname='$username[0]' ";
				}else {
					$wherestr = " WHERE username LIKE '%$username[0]%' OR nickname LIKE '%$username[0]%'";
				}
				return $wherestr;
			}
		}

		$vip_level    = isset($get['vip']) ? intval($get['vip']) : 0;

		if ($vip_level > 0) $wherestr .= !empty($wherestr) ? " AND vip_level>='$vip_level'" : " WHERE vip_level>='$vip_level'";
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
		if ($starttime == 0 && $endtime == 0 && $tbl_key != 'defeat_world_boss'){
			$starttime = strtotime('-30 day');
			$endtime = time();
		}
		$datetime  = isset($get['datetime']) && !empty($get['datetime']) ? strtotime($get['datetime']) : 0;
		$playertype = isset($get['playertype']) ? intval($get['playertype']) : 0;
		$playername = isset($get['playername']) && !empty($get['playername']) ? trim($get['playername']) : ''; 
		$coins = isset($get['coins']) && intval($get['coins']) > 0 ? intval($get['coins']) : 0; 
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
			$player = $this->getdb->get_one($where, 'id, username, nickname');
			$id = intval($player['id']);
			$username = $player['username'];
			$nickname = $player['nickname'];
			if ($id <= 0){
				return array('list'=>array(), 'count'=>0, 'allnum'=>array('getnum'=>0, 'connum'=>0));
			}
		}


		$where = '';
		if ($id > 0){
			$wherestr = 'player_id='.$id;
			if ($tbl_key == 'marry_favor' or $tbl_key == 'marry_gold') {
				$this->getdb->table_name = 'player_marry';
				$marry_info = $this->getdb->get_one($wherestr, 'player_id, marry_id');
				$marry_id = $marry_info['marry_id'];
				if (!$marry_id)	{
					return array('list'=>array(), 'count'=>0, 'allnum'=> array('getnum'=>0, 'connum'=>0));
				}
				$wherestr = 'marry_id='.intval($marry_id);
				unset($marry_info);
			}

			if (isset($get['player_type']) && intval($get['player_type']) == 1){
				$wherestr = 'from_player_id='.$id;
			}
		}

		if (!empty($typeid)){
			$type = in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'coin_tree_count', 'long_yu_ling', 'xian_ling', 'xianling_tree', 'crystal', 'marry_favor', 'pearl', 'feats','blood_pet_chip','blood_pet','ling_yun','neidan','ba_xian_ling','marry_gold')) ? 'op_type' : 'type';
			if ($tbl_key == 'dragonball') {
				$type = 'change_type';
			}

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
			case 'dragonball': $time_column = 'change_timestamp'; break;
			case 'peach':
			case 'state_point': $time_column = 'date'; break;
			case 'xianling_tree':
			case 'xian_ling':
			case 'long_yu_ling':
			case 'crystal':
			case 'marry_favor':
				$time_column = 'op_time';
				break;
			case 'mission': $time_column = 'first_challenge_time'; break;
			default:
			    $time_column = 'change_time';
				break;
		}
		if (in_array($tbl_key, array('fame','power','blood_pet_chip','feats','ling_yun','neidan','blood_pet','take_bible','skill', 'coin_tree_count','pearl','ba_xian_ling','marry_gold'))){
			$time_column = 'op_time';
		}
		if ($tbl_key == 'fate' && $tbl == 2) {
			$time_column = 'op_time';
		}


		if ($coins > 0) {
			$wherestr .= empty($wherestr) ? "abs(value)={$coins}" : " AND abs(value)={$coins}";
		}
		if ($starttime > 0){
			$wherestr .= empty($wherestr) ? $time_column.'>'.$starttime : ' AND '.$time_column.'>'.$starttime;
		}
		if ($endtime > 0){
			$wherestr .= empty($wherestr) ? $time_column.'<'.$endtime : ' AND '.$time_column.'<'.$endtime;
		}

		if ($datetime > 0){
			$datestr = "year='".date('Y', $datetime)."' AND month='".date('n', $datetime)."' AND day='".date('j', $datetime)."'";
			$wherestr .=  empty($wherestr) ? $datestr : ' AND '.$datestr;
		}

		if (!empty($name)) {
			$namewhere = "name LIKE '%$name%'";
			$nameid = $tbl_key.'_id';
			if ($tbl_key == 'item_attribute_stone') {
				$nameitem = $this->getdb->get_list("SELECT a.id FROM item a LEFT JOIN attribute_stone b ON a.id=b.item_id WHERE ".$namewhere);
				$nameid = 'item_id';
			}elseif($tbl_key == 'dragonball'){
				$nameitem = $this->getdb->get_list("SELECT a.id FROM dragonball a LEFT JOIN player_dragonball_log b ON a.id=b.dragonball_id WHERE ".$namewhere);
				$nameid = 'dragonball_id';
			}else {
				
				$this->getdb->table_name = $tbl_key;
				if ($tbl_key == 'blood_pet_chip'){
					$this->getdb->table_name = 'item';
					$nameid = 'item_id';
				}
				$nameitem = $this->getdb->select($namewhere, 'id, name');
			}
			if (!$nameitem) {
				return array('list'=>array(), 'count'=>0, 'allnum'=>array('getnum'=>0, 'connum'=>0));
			}
			$ids = array();
			foreach ($nameitem as $nkey => $nvalue) {
				$ids[] = $nvalue['id'];
			}
			$ids = array_unique($ids);
			$wherestr .=  empty($wherestr) ? "{$nameid} IN (".implode(',', $ids).")" : ' AND '."{$nameid} IN (".implode(',', $ids).")";
		}

		if ($tbl_key == 'mission') {
			$order = 'mission_id DESC';
		}else {
			$order    = 'id DESC';
		}
		
		$table_name = 'player_'.$tbl_key.'_change_record'.$tbl;

		if (in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'farmland', 'flower_count', 'coin_tree_count', 'elixir', 'item_attribute_stone', 'faction_contribution', 'spirit','xian_ling', 'xianling_tree', 'long_yu_ling', 'crystal', 'marry_favor', 'pearl', 'feats', 'dragonball','blood_pet_chip','blood_pet','ling_yun','neidan','ba_xian_ling','marry_gold'))){
			$table_name = 'player_'.$tbl_key.'_log'.$tbl; 
		}else if (in_array($tbl_key, array('peach', 'defeat_world_boss', 'mission'))) {
			$table_name = 'player_'.$tbl_key.'_record'.$tbl;
		}else if (in_array($tbl_key, array('level_up'))){
			$table_name = $tbl_key.'_record'.$tbl;
		}

		$this->getdb->table_name = $table_name;
		//$list = $this->getdb->get_list_page($wherestr, '*', $order, $page, $pagesize);
		$pagesize 	 = intval($pagesize);
		$page = max(intval($page), 1);
		$offset = $pagesize*($page-1);
		if (empty($wherestr))	$wherestr = '1=1';
		if ($tbl_key == 'marry_favor' or $tbl_key == 'marry_gold') {
			$sql = "SELECT a.value,a.op_type,a.op_time,d.nickname as m_nickname,c.nickname as f_nickname FROM {$table_name} a,player_marry_info b,player c,player d WHERE $wherestr and a.marry_id=b.id and b.f_player_id=c.id and b.m_player_id=d.id ORDER BY a.id desc LIMIT $offset, $pagesize";
		}else {
			$sql = "SELECT username, nickname, a.* FROM {$table_name} a LEFT JOIN player b ON a.player_id=b.id WHERE $wherestr ORDER BY {$order} LIMIT $offset, $pagesize";
		}
		$list = $this->getdb->get_list($sql);
//		$allnum = array('getnum'=>0, 'connum'=>0);
		if ($recordnum <= 0) {
			$recordnum = $this->getdb->count($wherestr, '*');

//			if (in_array($tbl_key, array('ingot', 'coin', 'item', 'power', 'item_attribute_stone', 'crystal', 'marry_favor'))){
//				$wherestr = !empty($wherestr) ? $wherestr.' AND ' : '';
//				$rs1 = $this->getdb->get_one($wherestr.'value>0', 'SUM(value) AS getnum');
//				$rs2 = $this->getdb->get_one($wherestr.'value<0', 'SUM(value) AS connum');
//				if ($tbl_key == 'item_attribute_stone'){
//					$rs1 = $this->getdb->get_list("select sum(value) as getnum from player_item_attribute_stone_log a,attribute_stone_change_type b where player_id='$id' and a.type=b.id and b.type=1");
//					$rs1 = $rs1[0];
//					$rs2 = $this->getdb->get_list("select sum(value) as connum from player_item_attribute_stone_log a,attribute_stone_change_type b where player_id='$id' and a.type=b.id and b.type=0");
//					$rs2 = $rs2[0];
//				}
//				$allnum = array('getnum' => intval($rs1['getnum']), 'connum' => intval($rs2['connum']));
//			}
		}
		foreach ($list as $key => $value){
			if ($_SESSION['roleid'] > 2 && !in_array($tbl_key, array('fate', 'item', 'soul'))){
				unset($value['id']);
			}
			$newlist[] = $value; 
		}
		return array('list'=>$newlist, 'count'=>$recordnum);
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
		$data = '*';
		$limit = '';
		$order = '';

		$list = $this->getdb->select($wherestr, $data, $limit, $order);
		foreach ($list as $key => $value){
			if ($_SESSION['roleid'] > 2 and $tbl_key != 'item'){
				unset($value['id']);
			}
			$newlist[] = $value; 
		}
		return $newlist;
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
			case 'spirit':
			case 'state_point':
				$column = 'id, name, type';
				break;
			case 'xian_ling':
				$column = 'id, name, type';
				break;
			case 'crystal':
				$column = 'id, name, type';
				break;
			case 'marry_favor':
				$column = 'id, name, type';
				break;
			case 'blood_pet_chip':
				$column = 'id, name, type';
				break;
			case 'item_attribute_stone':
				$column = 'id, name, type';
				$tbl_key = 'attribute_stone';
				break;
			case 'faction_contribution':
				$column = 'id, type_name as name';
				break;
			case 'ling_yun':
				$column = 'id, name, type';
				break;
			case 'neidan':
				$column = 'id, name, type';
				break;
			case 'marry_gold':
				$column = 'id, name, type';
				break;
			default:
				$column = 'id, name';
				break;
		}
		if (in_array($tbl_key, array('fame', 'fate', 'power', 'take_bible', 'skill', 'coin_tree_count', 'crystal', 'marry_favor','neidan','marry_gold')) ){
			$this->getdb->table_name = $tbl_key.'_log_type';
		}else if (in_array($tbl_key, array('faction_contribution', 'xianling_tree', 'pearl', 'feats', 'xian_ling','blood_pet_chip','blood_pet','ling_yun','ba_xian_ling'))) {
			$this->getdb->table_name = $tbl_key.'_type';
		}else {
			$this->getdb->table_name = $tbl_key.'_change_type';
		}

		if (in_array($tbl_key, array('fate', 'take_bible', 'elixir', 'faction_contribution', 'dragonball')) ){
			$list['cons'] = array();
			$list['get']  = $this->getdb->select('', $column);
		}elseif(in_array($tbl_key, array('soul_stone')) ) {
			$list['cons'] = $this->getdb->select('type=1', $column);
			$list['get']  = $this->getdb->select('type=0', $column);
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
	/**
	 * 获取称号列表
	 * 
	 */ 
	public function title_list(){
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$dbflag = $this->set_db($sid);

		if ($dbflag){
			$sql = 'select id,name from title';
			$data['list'] = $this->getdb->get_list($sql);

			output_json(0, '', $data);
		}
		output_json(1);
	}
}
