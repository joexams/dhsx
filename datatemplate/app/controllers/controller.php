<?php

namespace Controllers;
use \DB\SQL;
use \DB\SQL\Session;
use \Models\HandleModel;

class Controller
{
	protected $base = null;
	protected $view = null;

	function __construct()
	{
		$f3 = \Base::instance();
		$this->base = $f3;

		//连接数据库
		if ($f3->get('host')) {
			$this->base->set('db', new SQL('mysql:host='.$f3->get('host').';port='.$f3->get('port').';dbname='.$f3->get('dbname').'', $f3->get('dbroot'), $f3->get('dbpwd')));

			new Session($f3->get('db'), $f3->get('prefix').'sessions', false);
		}

		if (!in_array($this->base->get('PARAMS.0'), array('/logout', '/login'))) {
			$this->authLogin();
			$this->setMenu();
		}

		$this->view = \View::instance();
	}
	/**
	 * 登录状态验证
	 * @return [type] [description]
	 */
	function authLogin()
	{
		if (!$this->base->get('SESSION.user_id'))
			$this->base->reroute('/login');
		if ($this->base->get('SESSION.lastseen')+$this->base->get('expiry')*3600<time())
			$this->base->reroute('/logout');

		$this->base->set('SESSION.lastseen',time());
	}
	/**
	 * 设置菜单栏和侧边栏
	 * @return [type] [description]
	 */
	function setMenu()
	{

		$this->base->set('sidebar', '');
	}
	/**
	 * 提示信息页面跳转
	 * @param string $msg 提示信息
	 * @param mixed(string/array) $url_forward 跳转地址
	 * @param int $ms 跳转等待时间
	 * @return status 0=失败 1=成功
	 */
	public function showMessage($code = 0, $msg, $url_forward = '', $ms = 1250) {
		if ($this->base->get('AJAX')) {
			echo json_encode(array(
				'status' => $code,
				'text' => $msg,
				'forward' => $url_forward
			));
		}else {
			$this->base->set('msg', $msg);
			$this->base->set('url_forward', $url_forward);
			$this->base->set('ms', $ms);
			echo $this->view->render('message.htm');
		}
		die;
	}
	/**
	 * 记录操作日志
	 * @param array $insertarr
	 * @return bool|mixed
     */
	function handleLog($insertarr = array())
	{
		$hande = new HandleModel();
		$id = $hande->create($insertarr);
		return $id;
	}
}