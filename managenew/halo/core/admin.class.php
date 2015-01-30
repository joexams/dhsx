<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('session');
if(param::get_cookie('ho_lang')) {
	define('L_STYLE',param::get_cookie('ho_lang'));
} else {
	define('L_STYLE','zh-cn');
}
class admin {
	public $userid;
	public $username;
	
	public function __construct() {
		self::check_admin();
		//self::check_priv();
		// self::check_ip();
		// self::check_hash();
		
		//绑定域名
		if(common::load_config('system','admin_url') && $_SERVER["SERVER_NAME"]!= common::load_config('system','admin_url')) {
			Header("http/1.1 403 Forbidden");
			exit('No permission resources.');
		}
	}
	
	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin() {
		if(ROUTE_M =='manage' && ROUTE_C =='index' && in_array(ROUTE_V, array('login', 'public_card'))) {
			return true;
		} else {
			if(!isset($_SESSION['userid']) || !isset($_SESSION['roleid']) || !$_SESSION['userid'] || !$_SESSION['roleid']) {
				if (ROUTE_M == 'ajax'){
					
				}else {
					header('Location: '.INDEX.'?m=manage&c=index&v=login');					
				}
			}
		}
	}
	
	/**
	 * 按父ID查找菜单子项
	 * @param integer $parentid   父菜单ID  
	 * @param integer $with_self  是否包括他自己
	 */
	final public static function admin_menu($parentid, $with_self = 0) {
		$parentid = intval($parentid);
		$menudb = common::load_model('menu_model');
		$result =$menudb->select(array('parentid'=>$parentid,'display'=>0), 'mid,mname,parentid,m,v,c,parentpath,data,depth,islink,urllink',10,'listorder ASC');
		if($with_self) {
			$result2[] = $menudb->get_one(array('mid'=>$parentid));
			$result = array_merge($result2,$result);
		}
		//权限检查
		if($_SESSION['roleid'] == 1) return $result;
		$array = array();
		$privdb = common::load_model('priv_model');
		$userid = $_SESSION['userid'];
		$roleid = $_SESSION['roleid'];

		$privdb->set_model(1);
		$privcount = $privdb->count(array('userid'=>$userid), '*');
		if ($privcount > 0) {
			$privwhere['userid'] = $userid;
		}else {
			$privwhere['roleid'] = $roleid;
			$privdb->set_model(0);
		}

		foreach($result as $v) {
			$action = $v['v'];
			if(preg_match('/^public_/',$action)) {
				$array[] = $v;
			} else {
				if(preg_match('/^ajax_([a-z]+)_/',$action,$_match)) $action = $_match[1];
				$r = $privdb->get_one(array_merge(array('m'=>$v['m'],'c'=>$v['c'],'v'=>$action),$privwhere));
				if($r) $array[] = $v;
			}
		}
		return $array;
	}
	/**
	 * 权限判断
	 */
	final public function check_priv() {
		if(ROUTE_M =='manage' && ROUTE_C =='index' && in_array(ROUTE_V, array('login','logout'))) return true;
		if($_SESSION['roleid'] == 1) return true;

		if(preg_match('/^ajax_([a-z]+)_/',ROUTE_V,$_match)) {
			$action = $_match[1];
		}else {
			$action = ROUTE_V;
		}

		if (ROUTE_M != 'develop')	return ;

        $privdb = common::load_model('priv_model');
        $userid = $_SESSION['userid'];
        $roleid = $_SESSION['roleid'];

        $privdb->set_model(1);
        $privcount = $privdb->count(array('userid'=>$userid), '*');
        if ($privcount > 0) {
        	$privwhere['userid'] = $userid;
        }else {
        	$privwhere['roleid'] = $roleid;
        	$privdb->set_model(0);
        }

        $wherearr = array('m'=>ROUTE_M, 'c'=>ROUTE_C, 'v'=>$action);
        $wherearr = array_merge($wherearr, $privwhere);

        $r = $privdb->get_one($wherearr);
		if(!$r) showmessage('您没有权限操作该项','blank');
	}
	/**
	 * 判断平台权限
	 * @param $string key server=服务器， company=运营商
	 * @param $int    cid  运营商CID
	 * @param $int    sid  服务器SID  
	 * @return  string 返回权限不足 或 查询字符串
	 * 
	 */ 
	final public function check_pf_priv($key='', $cid=0, $sid=0){
		$roleid = $_SESSION['roleid'];
		$userid = $_SESSION['userid'];
		if ($roleid < 2) return '';

		$key = $key == 'server' ? 'server' : 'company';
		$cid  = intval($cid) > 0 ? intval($cid) : 0;
		$sid  = intval($sid) > 0 ? intval($sid) : 0;
		$platformdb = common::load_model('priv_platform_model');
		$platformdb->set_model(0);
		$pr = $platformdb->get_one(array('userid'=>$userid));

		$wherestr = '';
		if ($roleid == 5){
			$pcid  = $pr['cids'];
			$sids = $pr['sids'];
			if (!empty($pcid)) {
				if ($key == 'company'){
					if ($cid > 0){
						return strpos($pcid, ','.$cid.',') !== false ? true : output_json(1, Lang('no_permission'));
					}
					$pcid = trim($pcid, ',');
					return "WHERE cid IN ($pcid)";
				}

				if (strpos($sids, ',') !== false){	
					if ($sid > 0){
						return strpos($sids, ','.$sid.',') !== false ? true : output_json(1, Lang('no_permission'));
					}
					$sids = trim($sids, ',');
					return !empty($sids) ? 'WHERE sid IN ('.$sids.')' : '';
				}
				$pcid = trim($pcid, ',');
				return "WHERE cid IN ($pcid)";
			}

			output_json(1, Lang('no_permission'));
		}else if ($roleid > 2 && $roleid != 5){
			$gid = $pr['gid'];
			if ($gid > 0){
				$platformdb->set_model(1);
				$gr = $platformdb->get_one(array('gid'=>$gid));
				if ($gr && strpos($gr['cids'], ',') !== false){
					if ($cid > 0){
						return strpos($gr['cids'], ','.$cid.',') !== false ? true : output_json(1, Lang('no_permission'));
					}

					$gr['cids'] = trim($gr['cids'], ',');
					return !empty($gr['cids']) ? 'WHERE cid IN ('.$gr['cids'].')' : '';
				}
				output_json(1, Lang('no_permission'));
			}

			output_json(1, Lang('no_permission'));
		}
	}
	/**
	 * 
	 * 后台IP禁止判断 ...
	 */
	final private function check_ip(){
		$this->ipbanned = common::load_model('ipbanned_model');
		$this->ipbanned->check_ip();
 	}
 	/**
 	 * 日志记录 
 	 * 
 	 */
 	final public static function op_log($content, $tablename='operation'){
 		$logdb = common::load_model('log_model');
 		if ($tablename == 'login'){
 			$log['userid']   = $content['userid'];
 			$log['username'] = $content['username'];
 			$log['password'] = substr_replace($content['password'],'****',1,4);
			if (strlen($log['password']) > 7) {
				$log['password'] = substr_replace($log['password'], '****', 7, 4);
			}
 			$log['content']  = $content['content'];
 			$logdb->set_model('login');
 		}else if ($tablename == 'source'){
 			$log['userid']   = $_SESSION['userid'];
 			$log['username'] = param::get_cookie('username');
 			$log['content']  = $content['content'];
 			$log['key']	     = $content['key'];
 			$log['playerid'] = $content['playerid'];
 			$log['playername'] = $content['playername'];
 			$log['sid']      = $content['sid'];
 			$log['playernickname'] = $content['playernickname'];
 			$logdb->set_model('source');
 		}else {
			$log['userid']   = $_SESSION['userid'];
			$log['username'] = param::get_cookie('username');
			$log['content']  = $content;
			$log['module']   = ROUTE_M;
			$log['action']   = ROUTE_C;
			$log['view']     = ROUTE_V;
 		}
 		$log['ip']       = ip();
		$log['dateline'] = TIME;
		$logdb->insert($log);
 	}

	/**
 	 * 检查hash值，验证用户数据安全性
 	 */
	final private function check_hash() {
		if(preg_match('/^public_/', ROUTE_V) || ROUTE_M =='admin' && ROUTE_C =='index' || in_array(ROUTE_V, array('login'))) {
			return true;
		}
		if(isset($_GET['ho_hash']) && $_SESSION['ho_hash'] != '' && ($_SESSION['ho_hash'] == $_GET['ho_hash'])) {
			return true;
		} elseif(isset($_POST['ho_hash']) && $_SESSION['ho_hash'] != '' && ($_SESSION['ho_hash'] == $_POST['ho_hash'])) {
			return true;
		} else {
			showmessage(Lang('hash_check_false'),HTTP_REFERER);
		}
	}
}
