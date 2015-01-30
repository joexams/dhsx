<?php

namespace Controllers;
use Models\TableModel;
use Models\UserModel;

class IndexController extends Controller
{
	/**
	 * 默认首页
	 * @return [type] [description]
	 */
	function overView()
	{
		$tablemodel = new TableModel($this->db, $this->base);
		$tableList = $tablemodel->getTableList();

		$tableid = $this->base->get('PARAMS.tableid');
		$tableid = $tableid ?: 0;
		$currentid = 0;

		$tree = \Tree::instance();
		$tree->init($tableList);
		
		$navbar = $sidebar = $breadcrumb = array();
		foreach ($tableList as $key => $value) {
			if ($value['parentid'] === 0) {
				$navbar[$key] = $value;
			}
			if ($value['id'] == $tableid) {
				$currentid = $key;
				$sidebar = $tree->get_child($currentid);
				// $breadcrumb = $tree->get_position($current['id'], &$newarr);
			}
		}
		if ($sidebar) {
			foreach ($sidebar as $key => $value) {
				$sidebar[$key]['subside'] = $tree->get_child($value['id']);
			}
		}
		$this->base->set('navbar', $navbar);
		$this->base->set('sidebar', $sidebar);
		$this->base->set('currentid', $currentid);
		echo $this->view->render('overview.htm');
	}
	/**
	 * 登录
	 * @return [type] [description]
	 */
	function login()
	{
		if ($this->base->get('COOKIE.sent') && $this->base->get('POST.username')) {

			$username = $this->base->get('POST.username');
			$user = new UserModel();
			$user_info = $user->get_info_by_username($username);

			if (!$user_info) {
				$this->showmessage(-1, '用户名或密码错误');
			}else {
				$crypt = $user_info->password;

				if (crypt($this->base->get('POST.password'),$crypt)!=$crypt) {
					$this->showmessage(-2, '密码有误');
				}

				$user->previous_login_at = $user->last_login_at;
				$user->previous_login_ip = $user->last_login_ip;
				$user->last_login_at = time();
				$user->last_login_ip = $this->base->get('IP');
				$user->login_num++;
				if ($user_info->status == 2) {
					//信息被更新后需要重新登录
					$user->status = 0;
				}
				$user->save();

				$this->base->clear('COOKIE.sent');
				$this->base->set('SESSION.user_id', $user_info->user_id);
				$this->base->set('SESSION.group_id', $user_info->group_id);
				$this->base->set('SESSION.username', $username);
				$this->base->set('SESSION.crypt',$crypt);
				$this->base->set('SESSION.lastseen',time());

				$this->base->reroute('/');
			}
			$this->base->reroute('/login');

		}else {

			$this->base->clear('SESSION');
			$this->base->set('COOKIE.sent',TRUE);

			echo $this->view->render('login.htm');
		}
	}

	function logout()
	{
		$this->base->clear('SESSION');
		$this->base->reroute('/login');
	}
}