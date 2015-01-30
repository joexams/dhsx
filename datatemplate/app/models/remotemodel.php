<?php

namespace Models;
use \DB\SQL;
use \DB\SQL\Mapper;

class RemoteModel extends Mapper
{
    protected $db = '';
	protected $base;
	protected $table_name = '';

	public function __construct($table_name = '')
	{
		$this->table_name = $table_name;
        $this->base = \Base::instance();
        $this->db = new SQL('mysql:host='.$this->base->get('remote_host').';port='.$this->base->get('remote_port').';dbname='.$this->base->get('remote_dbname').'', $this->base->get('remote_dbroot'), $this->base->get('remote_dbpwd'));
		if ($this->table_name && $this->db) {
			parent::__construct($this->db, $this->table_name);
		}
	}
	/**
	 * 判断是否存在表
	 * @return [type] [description]
	 */
	function exists($table_name){
		return $this->db->exec("SHOW TABLES LIKE '$table_name'");
	}
	/**
	 * 获取表结构
	 * @param  [type] $table_name [description]
	 * @return [type]             [description]
	 */
	function getFields($table_name){
		$fields = array();
		if ($this->exists($table_name)) {
			$fields = $this->db->exec("SHOW full columns FROM ".$table_name);
		}
		return $fields;
	}
	/**
	 * 取分页信息
	 * @param  [type]  $table_name [description]
	 * @param  integer $pos        [description]
	 * @param  [type]  $filter     [description]
	 * @param  [type]  $orderby    [description]
	 * @return [type]              [description]
	 */
	function getPageRows($table_name, $pos=0, $filter = NULL, $orderby = '') {
		$this->table = $table_name;
		$total=$this->count($filter);

		if (!$total) {
			return false;
		}
		$size = $this->base->get('perpage');
		$count=ceil($total/$size);
		$pos=max(0,min($pos,$count-1));
		$offset = $pos*$size;
		$orderby = $orderby ? ' ORDER BY '.$orderby : '';
		$filter = $filter && !is_array($filter) ? ' WHERE '.$filter : '';
		$list = $this->db->exec("SELECT * FROM $table_name $filter $orderby LIMIT $offset, $size");

		$data = array(
			'list' => $list,
			'total' => $total,
			'limit' => $size,
			'count' => $count,
			'pos' => $pos<$count?$pos:0
		);
		return $data;
	}
}