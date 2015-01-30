<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('admin', '', 0);
class priv extends admin {
	private $menudb, $privdb, $platformdb, $pagesize;
	function __construct(){
		parent::__construct();
		$this->menudb  = common::load_model('menu_model');
		$this->privdb = common::load_model('priv_model');
		$this->pagesize = 10;
	}

	public function init(){

	}
	/**
	 * 权限列表显示
	 * 
	 */ 
	public function ajax_show(){
		$roleid = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
		$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
		
		$priv = $rolepriv = $userpriv = $allpriv = $select_priv = array();
		if ($roleid > 0){
			$priv = $this->privdb->select("roleid='$roleid'", 'mid');
			foreach ($priv as $key => $value) {
				$rolepriv[$value['mid']] = 1;
			}
		}
		$wherestr = '';
		if ($userid > 0){
			$this->privdb->set_model(1);
			$priv = $this->privdb->select("userid='$userid'", 'mid');
			if (!empty($priv)) {
				foreach ($priv as $key => $value) {
					$userpriv[$value['mid']] = 1;
				}
				if ($roleid > 2) {
					$wherestr = to_sqls(array_keys($priv), '', 'mid');
				}
			}
		}

		if (empty($userpriv)){
			$allpriv = $rolepriv;
		}else {
			$allpriv = array_intersect_key($userpriv, $rolepriv);
		}

		$list = $this->menudb->select($wherestr);
		$menulist = array();
		$i = 0;
		foreach ($list as $key => $value) {
			if ($roleid > 2 && $value['display'] == 1 && $_SESSION['roleid'] !=1) {
				continue;
			}

			$menulist[$value['mid']]['id'] = $value['mid'];
			$menulist[$value['mid']]['parentid'] = $value['parentid'];
			$menulist[$value['mid']]['name'] = $value['mname'];
			$menulist[$value['mid']]['level'] = $value['depth'];
			$menulist[$value['mid']]['parentid_node'] = ($value['parentid'])? '  data-tt-parent-id="'.$value['parentid'].'"' : '';

			if ($roleid > 2 && $userid > 0 && $value['isdistrib'] == 1) {
				if ($_SESSION['roleid'] != 1) $menulist[$value['mid']]['chkDisabled'] = 'disabled';
			}
			
			$menulist[$value['mid']]['checked'] = isset($allpriv[$value['mid']]) ? 'checked' : '';
		}

		$menu = common::load_class('tree');
		$menu->icon = array('','','');
		$menu->nbsp = '';
		$str  = "<tr data-tt-id='\$id' \$parentid_node>
					<td>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked \$chkDisabled onclick='javascript:checknode(this);'> \$name</td>
				</tr>";
		$menu->init($menulist);
		$categorys = $menu->get_tree(0, $str);

		unset($menulist, $rolepriv, $userpriv, $allpriv, $select_priv);
		include template('manage', 'priv');
	}

	/**
	 * 权限设置
	 * 
	 */ 
	public function setting(){
		if (isset($_POST['doSubmit'])){
			$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
			$roleid = isset($_POST['roleid']) ? intval($_POST['roleid']) : 0;

			$curroleid = intval($_SESSION['roleid']);
			if ($curroleid > 4 || $roleid < $curroleid) {
				output_json(1, Lang('no_permission'));
			}

			$userdb = common::load_model('user_model');
			$userflag= 0;
			if (is_array($_POST['menuid']) && count($_POST['menuid']) > 0) {
				$arrmids = array();
				if ($userid > 0 && $roleid > 0){
					$this->privdb->set_model(1);
					$oldlist = $this->privdb->select(array('userid'=>$userid), 'userid,mid');
					foreach ($oldlist as $key => $value) {
						$arrmids[] = $value['mid'];
					}
					foreach ($_POST['menuid'] as $key => $value) {
						if (!in_array($value, $arrmids)) {
							$info = array();
							$info = $this->menudb->get_one(array('mid' => $value), 'mid,m,v,c,data');
							$info['userid'] = $userid;
							$this->privdb->insert($info);
						}
					}
					$arrmids = array_diff($arrmids, $_POST['menuid']);
					if (count($arrmids) > 0) {
						$wherestr = to_sqls($arrmids, '', 'mid');
						$wherestr .= !empty($wherestr) ? ' AND userid='.$userid : 'userid='.$userid;
						$this->privdb->delete($wherestr);
					}
					$userdb->update(array('isrolepriv' => 1), array('userid' => $userid));
					$userflag = 1;
				}else {
					$oldlist = $this->privdb->select(array('roleid' => $roleid),  'roleid, mid');
					$userlist = $userdb->select(array('roleid'=>$roleid), 'userid,roleid,top');
					$arrids = $arrtopids = array();
					foreach ($userlist as $key => $value) {
						$arrids[] = $value['userid'];
						if ($value['top'] == 1) {
							$arrtopids[] = $value['userid'];
						}
					}
					$topidsnum = count($arrtopids);
					foreach ($oldlist as $key => $value) {
						$arrmids[] = $value['mid'];
					}
					foreach ($_POST['menuid'] as $key => $value) {
						if (!in_array($value, $arrmids)) {
							$info = array();
							$info = $this->menudb->get_one(array('mid' => $value), 'mid,m,v,c,data');
							$info['roleid'] = $roleid;
							$this->privdb->set_model(0);
							$this->privdb->insert($info);
							//个人权限不增加新增权限，只有顶级用户才增加
							/*if ($topidsnum > 0) {
								unset($info['roleid']);
								$this->privdb->set_model(1);
								foreach ($arrtopids as $key => $value) {
									$info['userid'] = $value;
									$this->privdb->insert($info);
								}
							}*/
						}
					}
					$arrmids = array_diff($arrmids, $_POST['menuid']);
					if (count($arrmids) > 0) {
						$wherestr = to_sqls($arrmids, '', 'mid');
						$wherestr .= !empty($wherestr) ? ' AND roleid='.$roleid : 'roleid='.$roleid;
						$this->privdb->set_model(0);
						$this->privdb->delete($wherestr);

						$this->privdb->set_model(1);
						$wherestr = to_sqls($arrids, '', 'userid');
						$wherestr .= !empty($wherestr) ? ' AND ' : '';
						foreach ($$arrmids as $key => $value) {
							$wherestr .= 'mid='.$value;
							$this->privdb->delete($wherestr);
						}
					}
				}
			} else {
				if ($userid > 0 && $roleid > 0){
					$this->privdb->set_model(1);
					$this->privdb->delete(array('userid'=>$userid));
					$userdb->update(array('isrolepriv' => 0), array('userid' => $userid));
				}else {
					$this->privdb->delete(array('roleid'=>$roleid));
					$userlist = $userdb->select(array('roleid'=>$roleid), 'userid,roleid');
					$arrids = array();
					foreach ($userlist as $key => $value) {
						$arrids[] = $value['userid'];
					}
					$wherestr = to_sqls($arrids, '', 'userid');
					$this->privdb->set_model(1);
					$this->privdb->delete($wherestr);
				}
			}

			$content = Lang('update_priv_success').($userid > 0 && $roleid > 0? '  用户权限设置，用户ID：'.$userid : '  角色权限设置，角色ID：'.$roleid);
			parent::op_log($content);
			output_json(0, Lang('update_priv_success'));
		}
	}
	/**
	 * 平台权限设置
	 * 
	 */ 
	public function setting_platform(){		
		$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
		$gid    = isset($_POST['gid']) ? intval($_POST['gid']) : 0;
		$sid    = isset($_POST['sid']) ? $_POST['sid'] : array();
		$cid    = isset($_POST['cid']) ? $_POST['cid'] : array();

		if ($userid > 0){
			$info['userid'] = $userid;
			$info['gid']    = $gid;
			if (count($cid) > 0) {
				$info['cids'] = ','.implode($cid, ',').',';
			}
			if (count($cid) > 0 && count($sid)) {
				$info['sids'] = ','.implode($sid, ',').',';
			}

			$this->platformdb = common::load_model('priv_platform_model');
			$r = $this->platformdb->get_one(array('userid'=>$userid));
			if ($r){
				$privid = $this->platformdb->update($info, array('privid'=>$r['privid']));
			}else {
				$privid = $this->platformdb->insert($info, true);
			}
			if ($privid){
				output_json(0, Lang('success'));
			}
			output_json(1, Lang('error'));
		}else {
			output_json(1, Lang('no_exits'));
		}
	}
	/**
	 * 平台权限
	 * 
	 */ 
	public function ajax_platform_show(){
		$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
		$roleid = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
		
		$this->platformdb = common::load_model('priv_platform_model');
		$r = $this->platformdb->get_one(array('userid'=>$userid));
		
		$data['gid']  = $r['gid'] > 0 ? $r['gid'] : 0;
		$data['cids']  =$r['cids'];
		$data['sids'] = $r['sids'];


		$mulit = true;
		//客服
		if ($roleid > 4){
			$mulit = false;
		}
		include template('manage', 'priv_platform');
	}
	/**
	 * 平台组权限
	 * 
	 */ 
	public function platform_group(){
		if (isset($_GET['format']) && $_GET['format'] == 'json') {
			$page = isset($_GET['top']) && intval($_GET['top']) > 0 ? intval($_GET['top']) : 1;
			$isall= isset($_GET['all']) ? 1: 0;
			$this->platformdb = common::load_model('priv_platform_model');
			$this->platformdb->set_model(1);
			if ($isall > 0){
				$data['list'] = $this->platformdb->select('');
				output_json(0, '', $data);
			}

			$recordnum = isset($_GET['recordnum']) ? intval($_GET['recordnum']) : 0;

			$list = $this->platformdb->get_list_page('', '*', '', $page, $this->pagesize);
			if ($recordnum <= 0){
				$recordnum = $this->platformdb->count('', 'gid');
			}

			$data['count']  = $recordnum;
			$data['list']   = $list;
			output_json(0, '', $data);
		}
		include template('manage', 'platform_group');
	}
	/**
	 * 删除平台分组
	 * @return [type] [description]
	 */
	public function platform_group_delete()
	{
		$gid   = intval($_GET['gid']);
		$this->platformdb = common::load_model('priv_platform_model');
		$this->platformdb->set_model(1);
		$status = $this->platformdb->delete(array('gid' => $gid));
		if ($status) {
			output_json(0, '删除操作成功');
		}
		output_json(1, Lang('删除操作失败'));
	} 
	/**
	 * 添加平台权限
	 * @return [type] [description]
	 */
	public function platform_group_add(){
		$this->platformdb = common::load_model('priv_platform_model');
		$this->platformdb->set_model(1);
		if (isset($_POST['doSubmit'])){
			$gid  = isset($_POST['gid']) ? intval($_POST['gid']) : 0;
			$info = array();
			$editflag = 0;

			$info['gname'] = trim($_POST['gname']);
			$info['description'] = trim($_POST['description']);
			$info['dateline'] = time();
			$cids = $_POST['cid'];
			if ($cids && is_array($cids)){
				$cids = ','.implode($cids, ',').',';
				$info['cids'] = $cids;
			}

			if ($gid > 0){
				$editflag = 1;
				$rtn = $this->platformdb->update($info, array('gid' => $gid));
				$gid = $rtn ? $gid : 0;
			}else {
				$gid = $this->platformdb->insert($info, true);
			}
			
			if ($gid > 0) {
				$msg                    = Lang('success');
				$data['editflag']       = $editflag;
				$data['info']           = $info;
				$data['info']['gid']    = $gid;

				output_json(0, $msg, $data);
			}
			output_json(1, Lang('error'));
		}else {
			$gid  = isset($_GET['gid']) ? intval($_GET['gid']) : 0;
			if ($gid > 0) {
				$platform = $this->platformdb->get_one(array('gid' => $gid));
			}
			include template('ajax', 'platform_group_add');
		}
	}
}
