<?php

namespace Controllers;
use \Models\HandleModel;
use \Models\UserModel;

class LogController extends Controller
{
	private $handle;
	function beforeroute($f3)
	{
		$this->handle = new HandleModel();
	}

	/**
	 * 操作日志
     */
	function handle()
	{
		if (isset($_GET['page'])) {
			$rst = $this->handle->getPageList($this->base->get('page'));
			$user_ids = array();
			foreach ($rst['list'] as $key => $value) {
                $user_ids[$value['user_id']] = 1;

                $rst['list'][$key]['datetime'] = $value['create_time'] > 0 ? date('Y-m-d H:i:s', $value['create_time']) : '';
            }
            if ($user_ids) {
                $users = array();
                $user_list = (new UserModel())->get_user_list(array_keys($user_ids));
                if ($user_list) {
                    foreach ($user_list as $key => $value) {
                        $users[$value['user_id']] = $value['realname'] ?:$value['username'];
                    }
                    foreach ($rst['list'] as $key => $value) {
                        if ($value['user_id']) {
                            $rst['list'][$key]['create_user'] = $users[$value['user_id']];
                        }
                    }
                }
            }
			if ($rst) {
				$this->showmessage(1, $rst);
			}
			$this->showmessage(0, '');
		}
		echo $this->view->render('handle_view.htm');
	}
}