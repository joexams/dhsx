<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin');
class user extends admin {
	private $userdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->userdb  = common::load_model('user_model');
		$this->pagesize = 15;
	}

	/**
	 * 用户列表
	 */
	public function init(){

		$roledb = common::load_model('role_model');

		$data['rolelist'] = $roledb->select('', 'roleid,rolename');
		$data['jsonrole'] = json_encode($data['rolelist']);
		
		$data['show']     = isset($_GET['show']) ? true : false;
		$data['roleid']   = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
		include template('manage', 'user');
	}
	/**
	 * 用户列表
	 */
	public function ajax_list() {
		$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
		$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;
		$username = isset($_GET['username']) && !empty($_GET['username']) ? safe_replace($_GET['username']) : '';

		$roleid = intval($_SESSION['roleid']);

		if (!empty($username)) {
			$wherestr = " username LIKE '%".trim($username)."%'";
		}
		$createuserid = 0;
		if ($roleid >= 2) {
			$createuserid = intval($_SESSION['userid']);
		}
		if ($createuserid > 0) {
			$wherestr .= !empty($wherestr) ? " AND (createuserid='$createuserid' OR roleid>$roleid) AND username<>'admin'": "(createuserid='$createuserid' OR roleid>$roleid) AND username<>'admin'";
		}

		$gid = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
		if (isset($_GET['roleid']) && $gid > 0) {
			if ($roleid == 1) {
				$wherestr .= !empty($wherestr) ? " AND roleid='$gid'": "roleid='$gid'";
			}else {
				$wherestr .= !empty($wherestr) ? " AND roleid='5' AND username<>'admin'": "roleid='5' AND username<>'admin'";
			}
		}

		$list = $this->userdb->get_list_page($wherestr, 'userid,username,lastloginip,lastlogintime,logintimes,roleid,status', '', $page, $this->pagesize);
		if ($recordnum <= 0) {
			$recordnum = $this->userdb->count($wherestr, 'userid');
		}

		$data['count']  = $recordnum;
		$data['list']   = $list;

		output_json(0, '', $data);
	}
	/**
	 * 增加用户
	 * 
	 */
	public function add(){
		if (isset($_POST['doSubmit'])) {

			$roleid = intval($_SESSION['roleid']);
			if ($roleid > 4) {
				output_json(1, Lang('no_permission'));
			}

			$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
			$info = array();
			$editflag = 0;

			$info['username'] = trim(safe_replace($_POST['username']));
			$info['password'] = trim($_POST['password']);
			$info['roleid']	  = isset($_POST['roleid']) ? intval($_POST['roleid']) : 0;
			$info['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
			$info['createuserid'] = intval($_SESSION['userid']);
			if ($info['roleid']	 < $roleid) {
				output_json(1, Lang('no_permission'));
			}
			//管理员号和开发号添加的号是顶级号
			if ($roleid < 3) {
				$info['top'] = 1;
			}
			$info['lang']	  = isset($_POST['lang']) ? safe_replace($_POST['lang']) : common::load_config('system', 'lang');

			if ($userid > 0) {
				$editflag = 1;
				if (!empty($info['password'])) {
					$info['encrypt']  = random(6);
					$info['password'] = md5(md5($info['password']).$info['encrypt']);
				}else {
                    unset($info['password']);
                }
				$rtn = $this->userdb->update($info, array('userid' => $userid));
				$userid = $rtn ? $userid : 0;
			}else {
				$info['encrypt']  = random(6);
				$info['password'] = md5(md5($info['password']).$info['encrypt']);
				$info['createip'] = ip();
				$info['dateline'] = TIME;
				$info['lastloginip'] = '';
				$info['lastlogintime'] = 0;
				$userid = $this->userdb->insert($info, true);
			}
			
			if ($userid > 0) {
				$msg                    = $editflag ? Lang('edit_user_success') : Lang('add_user_success');
				$data['editflag']       = $editflag;
				$data['info']           = $info;
				$data['info']['userid'] = $userid;

				$content = ($editflag ? Lang('edit_user_success') : Lang('add_user_success')).'  用户ID：'.$userid.'  用户名：'.$info['username'];
				parent::op_log($content);
				output_json(0, $msg, $data);
			}

			$msg	 = $editflag ? Lang('edit_user_error') : Lang('add_user_error');
			$content = ($editflag ? Lang('edit_user_error') : Lang('add_user_error')).'  用户ID：'.$userid.'  用户名：'.$info['username'];
			parent::op_log($content);
			output_json(1, $msg, $data);

		}else {
			$data['view'] = 'add';
			include template('manage', 'user');
		}
	}
	/**
	 * 获取用户详细信息
	 * 
	 */ 
	public function ajax_info(){
		$userid = intval($_GET['userid']);
		$data['info']  = array();

		if ($userid > 0){
			$data['info']  = $this->userdb->get_one(array('userid'=>$userid), 'userid,username,email,lastlogintime,lastloginip,status,lang,roleid');			
		}
		if (empty($data['info'] )){
			output_json(1, Lang('user_no_exits'));
		}

		output_json(0, '', $data);
	}
	/**
	 * 更改密码
	 * 
	 */ 
	public function edit_password(){

		if (isset($_POST['doSubmit'])){
			$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
			if ($userid > 0 && $userid == $_SESSION['userid']){
				$oldpassword = isset($_POST['oldpassword']) ? trim($_POST['oldpassword']) : '';
				if (!empty($_POST['password']) && !empty($oldpassword)){
					$userinfo = $this->userdb->get_one(array('userid'=>$userid), 'userid,username,email,lang, encrypt, password');
					if (md5(md5($oldpassword).$userinfo['encrypt']) != $userinfo['password']) {
						output_json(1,  '旧密码输入有误！');
					}
					if ($_POST['password'] != $_POST['repassword']) output_json(1,  '两次密码输入不一样！');

					$info['encrypt']  = random(6);
					$info['password'] = trim($_POST['password']);
					$info['password'] = md5(md5($info['password']).$info['encrypt']);
				}
				$info['lang']	  = isset($_POST['lang']) ? $_POST['lang'] : common::load_config('system', 'lang');
				$rtn = $this->userdb->update($info, array('userid' => $userid));

				if ($rtn){
					$content = '修改个人信息成功。用户ID：'.$userid.'  用户名：'.param::get_cookie('username');
					parent::op_log($content);
					output_json(0,  Lang('edit_user_success'));
				}

				$content = '修改个人信息失败。  用户ID：'.$userid.'  用户名：'.param::get_cookie('username');
				parent::op_log($content);
				output_json(1,  Lang('edit_user_error'));
			}

			$content = '修改个人信息出现异常，用户ID不存在。  用户ID：'.$_SESSION['userid'].'  用户名：'.param::get_cookie('username');
			parent::op_log($content);
			output_json(1,  Lang('user_no_exits'));

		}else {
			$userid = $_SESSION['userid'];
			$userinfo = $this->userdb->get_one(array('userid'=>$userid), 'userid,username,email,lang');
			$data['info'] = $userinfo;
			unset($userinfo);
			include template('manage', 'user_edit_info');
		}
	}

	/**
	 * 删除用户
	 * 
	 */
	public function delete(){
		/*$userid   = intval($_POST['userid']);
		$username = trim(safe_replace($_POST['username']));
		$adminuserid = common::load_config('system', 'adminuserid');
		if ($userid == $adminuserid) return true;

		$status = $this->userdb->delete(array('userid' => $userid));
		if ($status) {
			$content = Lang('delete_user_success').'  用户ID：'.$userid.'  用户名：'.$username;
			parent::op_log($content);
			output_json(0, Lang('delete_user_success').$username);
		}

		$content = Lang('delete_user_error').'  用户ID：'.$userid.'  用户名：'.$username;
		parent::op_log($content);
		output_json(1, Lang('delete_user_error'));*/
	}
}
