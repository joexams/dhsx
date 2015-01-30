<?php
defined('IN_G') or exit('No permission resources.');
common::load_class('model', '', 0);
class table_model extends model {
	public $table_name = '', $tree;
	public function __construct() {
		$this->db_config = common::load_config('database');
		$this->db_setting = 'tengyun';
//		$this->table_name = 'menu_sub';
		parent::__construct();
	}

	/**
	* 得到子级数组
	* @param int
	* @return array
	*/
	public function get_child($myid){
		$a = $newarr = array();
		if(is_array($this->tree)){
			foreach($this->tree as $id => $a){
				if ($a['parentid'] == $myid) $newarr[] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	/**
	* 得到当前数组
	* @param int
	* @return array
	*/
	public function get_tree_by_id($mid){
		$a = array();
		if(is_array($this->tree)){
			foreach($this->tree as $id => $a){
				if ($a['mid'] == $mid){
					return $a;
				}
			}
		}
		return false;
	}

	/**
	 * 获取 表在使用前设定
	 */
//	public function get_list($sql){
//		$sql = trim($sql);
//		if (empty($sql)) {
//			return array();
//		}
//		$this->db->query($sql);
//		$reslist = $this->fetch_array();
//		$this->db->free_result();
//		return $reslist;
//	}
	/**
	 * 获取数目,表在使用前设定
	 */
	public function get_count($sql){
		$sql = trim($sql);
		if (empty($sql)) {
			return 0;
		}
		$this->db->query($sql);
		$res = $this->db->fetch_next();
		$this->db->free_result();
		return $res['num'];
	}
}