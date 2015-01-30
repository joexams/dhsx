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
		self::check_priv();
		// self::check_hash();
		
		//绑定域名
		if(common::load_config('system','admin_url') && $_SERVER["SERVER_NAME"]!= common::load_config('system','admin_url')) {
			Header("http/1.1 403 Forbidden");
			exit('No permission resources.');
		}
	}
	
	public static function exitHtml($isLogout) 
	{
		if ($isLogout > 0) {
			$outHtml = '<div class="error-main">
			<div class="error-wrap">
			    <div class="error-text-title-wrap clearfix"><div class="error-number">500，</div><div class="error-text-title"></div></div>
			    <p class="error-text-content">登录状态已过期，请重新登录</p>
			    <ul class="handle-way-list">
			        <li class="handle-way-item"><a href="'.WEB_URL.INDEX.'?m=manage&c=index&v=login">重新登录</a></li>
			    </ul>
			</div>
			</div>';
		}else {
			$outHtml = '<div class="error-main">
			<div class="error-wrap">
			    <div class="error-text-title-wrap clearfix"><div class="error-number">500，</div><div class="error-text-title"></div></div>
			    <p class="error-text-content">没有权限，请联系管理员</p>
			    <ul class="handle-way-list">
			        <li class="handle-way-item">1.您可以<a href="javascript:history.go(-1);">返回上一个页面</a></li>
			        <li class="handle-way-item">2.您可以<a href="'.WEB_URL.INDEX.'">回到首页</a></li>
			        <li class="handle-way-item">3.您可以<a href="javascript:location.reload();">尝试刷新</a></li>
			    </ul>
			</div>
			</div>';
		}
		echo $outHtml;
		exit;
	}

	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin() {
		if(ROUTE_M =='manage' && ROUTE_C =='index' && in_array(ROUTE_V, array('login',))) {
			return true;
		} else {
			if(!isset($_SESSION['userid']) || !isset($_SESSION['roleid']) || !$_SESSION['userid'] || !$_SESSION['roleid']) {
				if (common::is_ajax()) {
					self::exitHtml(1);
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
	final public static function admin_menu($parentid, $with_self = 0, $isrolepriv = 0) {
		$parentid = intval($parentid);
		$menudb = common::load_model('menu_model');
		

		if($_SESSION['roleid'] == 1) {
			$result =$menudb->select(array('parentid'=>$parentid,'display'=>0), 'mid,mname,parentid,m,v,c,parentpath,data,depth,islink,urllink',10,'listorder ASC');
			if($with_self) {
				$result2[] = $menudb->get_one(array('mid'=>$parentid));
				$result = array_merge($result2,$result);
			}
			return $result;
		}

		$array = array();
		$privdb = common::load_model('priv_model');
		$userid = $_SESSION['userid'];
		$roleid = $_SESSION['roleid'];

		$privwhere = '';
		if($with_self) {
			$privwhere .= " AND (parentid={$parentid} OR mid={$parentid})";
		}else {
			$privwhere .= " AND parentid={$parentid}";
		}
		if ($isrolepriv == 1) {
			$privwhere .= ' AND userid='.$userid;
		}else {
			$privwhere .= ' AND roleid='.$roleid;
			$privdb->set_model(0);
		}

		$sql = "SELECT b.mid,b.mname,b.parentid,b.m,b.v,b.c,b.parentpath,b.data,b.depth,b.islink,b.urllink FROM {$privdb->table_name} a INNER JOIN {$menudb->table_name} b ON a.mid=b.mid WHERE display=0 $privwhere";
		$array = $privdb->get_list($sql);

		return $array;
	}
	/**
	 * 权限判断
	 */
	final public function check_priv() {
		if(ROUTE_M =='manage' && ROUTE_C =='index' && in_array(ROUTE_V, array('login','logout', 'init'))) return true;
		if($_SESSION['roleid'] == 1) return true;

		if(preg_match('/^public_/', ROUTE_V)) {
			return true;
		}elseif(preg_match('/^ajax_([a-z]+)_/',ROUTE_V,$_match)) {
			$action = $_match[1];
		}else {
			$action = ROUTE_V;
		}

        $privdb = common::load_model('priv_model');
        $userdb = common::load_model('user_model');
        $userid = $_SESSION['userid'];
        $roleid = $_SESSION['roleid'];

        $user = $userdb->get_one(array('userid' => $userid), 'isrolepriv');
        if ($user['isrolepriv'] == 1) {
        	$privwhere['userid'] = $userid;
        }else {
        	$privdb->set_model(0);
        	$privwhere['roleid'] = $roleid;
        }

        $wherearr = array('m'=>ROUTE_M, 'c'=>ROUTE_C, 'v'=>$action);
        $wherearr = array_merge($wherearr, $privwhere);

        $r = $privdb->get_one($wherearr);
		if(!$r) {
			if (common::is_ajax()) {
				self::exitHtml(0);
			}else {
				showmessage('您没有权限操作该项','blank');
			}
		}
	}
	/**
	 * 权限
	 */
	final public function has_priv($m, $v, $c) {
		if($_SESSION['roleid'] == 1) return true;
		$m = !empty($m) ? $m : ROUTE_M;
		$c = !empty($c) ? $c : ROUTE_C;
		$v = !empty($v) ? $v : ROUTE_V;
		 $privdb = common::load_model('priv_model');
        $userdb = common::load_model('user_model');
        $userid = $_SESSION['userid'];
        $roleid = $_SESSION['roleid'];

        $user = $userdb->get_one(array('userid' => $userid), 'isrolepriv');
        if ($user['isrolepriv'] == 1) {
        	$privwhere['userid'] = $userid;
        }else {
        	$privdb->set_model(0);
        	$privwhere['roleid'] = $roleid;
        }

        $wherearr = array('m'=>ROUTE_M, 'c'=>ROUTE_C, 'v'=>$action);
        $wherearr = array_merge($wherearr, $privwhere);

        $r = $privdb->get_one($wherearr);
		return $r ? true : false;
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
						return strpos($pcid, ','.$cid.',') !== false ? true : output_json(1, 'No access permission.');
					}
					$pcid = trim($pcid, ',');
					return "WHERE cid IN ($pcid)";
				}

				if (strpos($sids, ',') !== false){	
					if ($sid > 0){
						return strpos($sids, ','.$sid.',') !== false ? true : output_json(1, 'No access permission.');
					}
					$sids = trim($sids, ',');
					return !empty($sids) ? 'WHERE sid IN ('.$sids.')' : '';
				}
				$pcid = trim($pcid, ',');
				return "WHERE cid IN ($pcid)";
			}

			output_json(1, 'No access permission.');
		}else if ($roleid > 2 && $roleid != 5){
			$gid = $pr['gid'];
			if ($gid > 0){
				$platformdb->set_model(1);
				$gr = $platformdb->get_one(array('gid'=>$gid));
				if ($gr && strpos($gr['cids'], ',') !== false){
					if ($cid > 0){
						return strpos($gr['cids'], ','.$cid.',') !== false ? true : output_json(1, 'No access permission.');
					}

					$gr['cids'] = trim($gr['cids'], ',');
					return !empty($gr['cids']) ? 'WHERE cid IN ('.$gr['cids'].')' : '';
				}
				output_json(1, 'No access permission.');
			}

			output_json(1, 'No access permission.');
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
