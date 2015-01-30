<?php

namespace Models;

class UserModel extends Model {
    function __construct() {
    	$this->table_name = 'users';
        parent::__construct();
    }
    /**
     * 创建
     * @return [type] [description]
     */
    function create(){
    	$username = $this->base->get('POST.username');
    	$email = $this->base->get('POST.email');
    	$password = $this->base->get('POST.password');
		$user_info = $this->load(array('username=? OR email=?', $username, $email));

		if (!$user_info) {
	    	$this->userid='MAX(user_id)';
			$this->load();
			$user_id = $this->userid;

    		$this->reset();
			$this->user_id = $user_id ? $user_id + 1 : 10000+1;
			$this->username = trim($username);
			$this->email = trim($email);
			$this->realname = $this->base->get('POST.realname') ? $this->base->get('POST.realname') : '';
			$this->password = crypt($password);
			$this->group_id = $this->base->get('POST.group_id') ? $this->base->get('POST.group_id') : 3;
            $this->create_user_id = $this->base->get('SESSION.user_id');
			$this->creat_ip = $this->base->get('IP');
			$this->creat_at = time();
			$this->save();
			return $this->user_id;
		}
		return false;
    }
    /**
     * 通过Username查找用户
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    function get_info_by_username($username) {
		return $this->load(array('username=?', $username));
    }
    /**
     * 通过UID查找用户
     * @param  [type] $user_id [description]
     * @return [type]      [description]
     */
    function get_info_by_uid($user_id) {
    	return $this->load(array('user_id=?', $user_id));
    }
    /**
     * 获取玩家列表
     * @param  array  $user_ids [description]
     * @return [type]           [description]
     */
    function get_user_list($user_ids = array()){
        if (!$user_ids) return false;

        return $this->db->exec("SELECT user_id, realname, username FROM ".$this->table_prefix."users WHERE user_id IN (".implode(',', $user_ids).")");
    }
    /**
     * 更新
     * @return [type] [description]
     */
    function modify(){
    	$user_info = $this->get_info_by_uid($this->base->get('POST.user_id'));
    	if ($user_info) {
	    	$this->username = trim($this->base->get('POST.username'));
	    	$this->email = trim($this->base->get('POST.email'));
	    	$this->realname = $this->base->get('POST.realname') ? $this->base->get('POST.realname') : '';
	    	$this->nickname = $this->base->get('POST.nickname') ? $this->base->get('POST.nickname') : '';
	    	if ($this->base->get('POST.password')) {
	    		$this->password = crypt($this->base->get('POST.password'));
	    	}
	    	$this->group_id = $this->base->get('POST.group_id');
	    	$this->update_at = time();
	    	$this->save();
	    	return true;
	    }
	    return false;
    }
    /**
     * 删除
     * @param  [type] $user_id [description]
     * @return [type]      [description]
     */
    function delete(){
        $user_id = $this->base->get('GET.id');
    	$this->db->begin();
    	try{
            $rtn = $this->get_info_by_uid($user_id);
	    	$this->db->exec('DELETE FROM '.$this->table_prefix.'_users WHERE user_id="'.$user_id.'"');
            $this->db->commit();
	    	return $rtn;
    	}catch(Exception $e){
    		$this->db->rollback();
    	}
    	return false;
    }
}