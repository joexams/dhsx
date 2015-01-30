<?php

namespace Controllers;
use \Models\UserModel;

class UserController extends Controller
{
	private $user;
	function beforeroute($f3)
	{
		$this->user = new UserModel();
	}

	function view(){
		if (isset($_GET['page'])) {
			$user_id = $this->base->get('SESSION.user_id');
			$filter = '';
			$filter = 'user_id='.$user_id.' OR create_user_id='.$user_id;

			$rst = $this->user->getPageList($this->base->get('page'), $filter);
			$user_ids = array();
			foreach ($rst['list'] as $key => $value) {
                if ($value['create_user_id']) {
                    $user_ids[] = $value['create_user_id'];
                }

                $rst['list'][$key]['datetime'] = $value['last_login_at'] > 0 ? date('Y-m-d H:i:s', $value['last_login_at']) : '';
            }
            if ($user_ids) {
                $users = array();
                $user_list = $this->user->get_user_list($user_ids);
                if ($user_list) {
                    foreach ($user_list as $key => $value) {
                        $users[$value['user_id']] = $value['username'];
                    }
                    foreach ($rst['list'] as $key => $value) {
                        if ($value['create_user_id']) {
                            $rst['list'][$key]['create_user'] = $users[$value['create_user_id']];
                        }
                        unset($rst['list'][$key]['create_user_id']);
                    }
                }
            }

			if ($rst) {
				$this->showmessage(1, $rst);
			}
			$this->showmessage(0, '');
		}
		echo $this->view->render('user_view.htm');
	}
	/**
	 * 创建
	 * @return [type] [description]
	 */
	function create(){

		if ($this->base->get('COOKIE.sent') && $this->base->get('POST.username')) {
			if ($this->base->get('SESSION.user_id') != $this->base->get('ADMINID')) {
				$this->showmessage(0, '没有权限创建账号');
			}
			$password = $this->base->get('POST.password');
			$repassword = $this->base->get('POST.repassword');
			if ($password != $repassword) {
				$this->showmessage(-3, '两次密码输入不一致');
			}

			$user_id = $this->user->create();

			$this->base->clear('COOKIE.sent');
			if ($user_id) {
				$this->handleLog(array('title' => '创建账号', 'content' => '创建成功，账号ID：'.$user_id.'，账号名：'.$this->base->get('POST.username')));
				$this->base->clear('COOKIE.sent');

				$this->showmessage(1, 'success', '/user/view');
			}
			$this->showmessage(0, '创建失败');
		}else {
			$this->base->set('COOKIE.sent',TRUE);
			echo $this->view->render('user_create.htm');
		}
	}
	/**
	 * 修改
	 * @return [type] [description]
	 */
	function modify(){
		if ($this->base->get('COOKIE.sent') && $this->base->get('POST.username') && $this->base->get('POST.id')) {

			$userinfo = $this->user->load('id='.$this->base->get('POST.id'));
			$rtn = $this->user->modify();

			$this->base->clear('COOKIE.sent');
			if ($rtn) {
				$this->handleLog(array('title' => '修改账号', 'content' => '修改成功，账号ID：'.$userinfo->user_id.'，账号名：'.$userinfo->username));

				$this->showmessage(1, 'success', '/user/view');
			}
			$this->showmessage(0, 'error');
		}else {
			$this->base->set('COOKIE.sent',TRUE);
			$user_info = $this->user->get_info_by_uid($this->base->get('GET.id'));
			$this->base->set('user_info', $user_info);
			echo $this->view->render('user_create.htm');
		}
	}
	/**
	 * 删除
	 * @return [type] [description]
	 */
	function delete(){
		$user_info = $this->user->delete();
		if ($user_info->user_id == $this->base->get('SESSION.user_id')) {
			$this->showmessage(0, '账号无法删除');
		}
		if ($user_info) {
			$this->handleLog(array('title' => '删除账号', 'content' => '删除成功，账号ID：'.$user_info->user_id.'，账号名：'.$user_info->username));
			$this->showmessage(1, 'success', '/user/view');
		}

		$this->showmessage(0, 'error');
	}
}