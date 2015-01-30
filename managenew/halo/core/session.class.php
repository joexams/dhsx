<?php
/**
 *  session mysql 数据库存储类
 *
 * @copyright			(C) 2010-2012 
 * @lastmodify			2012-03-11
 */
class session {
	var $lifetime = 1800;
	var $db;
	var $table;
/**
 * 构造函数
 * 
 */
    public function __construct() {
		$this->db = common::load_model('session_model');
		$this->lifetime = common::load_config('system','session_ttl');
    	session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
    	session_start();
    }
/**
 * session_set_save_handler  open方法
 * @param $save_path
 * @param $session_name
 * @return true
 */
    public function open($save_path, $session_name) {
		
		return true;
    }
/**
 * session_set_save_handler  close方法
 * @return bool
 */
    public function close() {
        return $this->gc($this->lifetime);
    } 
/**
 * 读取session_id
 * session_set_save_handler  read方法
 * @return string 读取session_id
 */
    public function read($id) {
		$r = $this->db->get_one(array('sessionid'=>$id), 'data');
		return $r ? $r['data'] : '';
    } 
/**
 * 写入session_id 的值
 * 
 * @param $id session
 * @param $data 值
 * @return mixed query 执行结果
 */
    public function write($id, $data) {
    	$uid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    	$roleid = isset($_SESSION['roleid']) ? $_SESSION['roleid'] : 0;
    	$groupid = isset($_SESSION['groupid']) ? $_SESSION['groupid'] : 0;
		$m = defined('ROUTE_M') ? ROUTE_M : '';
		$c = defined('ROUTE_C') ? ROUTE_C : '';
		$a = defined('ROUTE_V') ? ROUTE_V : '';
		if(strlen($data) > 255) $data = '';
		$ip = ip();
		$sessiondata = array(
							'sessionid'=>$id,
							'userid'=>$uid,
							'ip'=>$ip,
							'lastvisit'=>TIME,
							'roleid'=>$roleid,
							'groupid'=>$groupid,
							'm'=>$m,
							'c'=>$c,
							'v'=>$a,
							'data'=>$data,
						);
		return $this->db->insert($sessiondata, 1, 1);
    }
/** 
 * 删除指定的session_id
 * 
 * @param $id session
 * @return bool
 */
    public function destroy($id) {
		return $this->db->delete(array('sessionid'=>$id));
    }
/**
 * 删除过期的 session
 * 
 * @param $maxlifetime 存活期时间
 * @return bool
 */
   public function gc($maxlifetime) {
		$expiretime = TIME - $maxlifetime;
		return $this->db->delete("`lastvisit`<$expiretime");
    }
}
?>