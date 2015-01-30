<?php 
/**
 *  modeli.class.php 数据模型基类
 *
 * @copyright			(C) 2010-2012
 */
defined('IN_G') or exit('No permission resources.');
common::load_class('DB_Factory', '', 0);
class model {
	
	//数据库配置
	protected $db_config = '';
	//数据库连接
	protected $db = '';
	//调用数据库的配置项
	protected $db_setting = 'default';
	//数据表名
	protected $table_name = '';
	//表前缀
	public  $db_tablepre = '';
	
	public function __construct() {
		if (!isset($this->db_config[$this->db_setting])) {
			$this->db_setting = 'default';
		}
		$this->table_name = $this->db_config[$this->db_setting]['tablepre'].$this->table_name;
		$this->db_tablepre = $this->db_config[$this->db_setting]['tablepre'];
		$this->db = DB_Factory::get_instance($this->db_config)->get_database($this->db_setting);
	}
	/**
	 * 查询SQL语句
	 */
	final public function query($sql, array $params = array())
	{
		return $this->db->query($sql, $params);
	}
	/**
	 * 获取单条记录查询
	 * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
	 * @param $table 		数据表
	 * @param $where 		查询条件
	 * @param $order 		排序方式	[默认按数据库默认方式排序]
	 * @param $group 		分组方式	[默认为空]
	 * @return array/null	数据查询结果集,如果不存在，则返回空
	 */
	final public function get_one($where, $data = '*', $order = '', $group = '') {
		

		if (is_array($where)) $where = $this->db->sqls($where);
		$where = $where == '' ? '' : ' WHERE '.$where;
		$order = $order == '' ? '' : ' ORDER BY '.$order;
		$group = $group == '' ? '' : ' GROUP BY '.$group;
		$limit = ' LIMIT 1';
		$field = explode( ',', $data);
		array_walk($field, array($this, 'add_special_char'));
		$data  = implode(',', $field);
		$sql = 'SELECT '.$data.' FROM `'.$this->db_config[$this->db_setting]['database'].'`.`'.$this->table_name.'`'.$where.$group.$order.$limit;
		return $this->db->getRow($sql);
	}
	/**
	 * 返回所有查询结果
	 */
	final public function select($where = '', $data = '*', $limit = '', $order = '', $group = '', $key='')
	{
		if (is_array($where)) $where = $this->db->sqls($where);
		$where = $where == '' ? '' : ' WHERE '.$where;
		$order = $order == '' ? '' : ' ORDER BY '.$order;
		$group = $group == '' ? '' : ' GROUP BY '.$group;
		$limit = $limit == '' ? '' : ' LIMIT '.$limit;
		$field = explode(',', $data);
		array_walk($field, array($this, 'add_special_char'));
		$data = implode(',', $field);

		$sql = 'SELECT '.$data.' FROM `'.$this->db_config[$this->db_setting]['database'].'`.`'.$this->table_name.'`'.$where.$group.$order.$limit;

		return $this->db->getAll($sql);
	}
	
	/**
	 * count查询
	 */
	final public function count($where , $data)
	{
		return $this->db->count($this->table_name, $where);
	}
	/**
	 * 写入
	 */
	final public function insert(array $params, $return_insert_id = false, $replace = false)
	{
		if ($replace) {
			$return = $this->db->replace($this->table_name, $params);
		}else {
			$return = $this->db->insert($this->table_name, $params);
		}

		return $return_insert_id ? $this->last_insert_id() : $return;
	}
	/**
	 * 更新
	 */
	final public function update(array $params, $where)
	{
		return $this->db->update($this->table_name, $params, $where);
	}
	/**
	 * 删除
	 */
	final public function delete($where)
	{
		return $this->db->delete($this->table_name, $where);
	}
	/**
	 * 获取最新ID
	 */
	final public function last_insert_id()
	{
		return $this->db->lastInsertId();
	}

	/**
	 * 对字段两边加反引号，以保证数据库安全
	 * @param $value 数组值
	 */
	protected function add_special_char(&$value) {
		if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`') || false !== strpos($value, 'as') || false !== strpos($value, 'AS')) {
			//不处理包含* 或者 使用了sql方法。
		} else {
			$value = '`'.trim($value).'`';
		}
		if (preg_match("/\b(select|insert|update|delete)\b/i", $value)) {
			$value = preg_replace("/\b(select|insert|update|delete)\b/i", '', $value);
		}
		return $value;
	}
	/**
	 * 直接执行SQL,返回所有查询结果
	 *
	 */
	final public function get_list($sql)
	{
		return $this->db->getAll($sql);
	}
	/**
	 * 分页查询,返回查询结果
	 *
	 */
	final public function get_list_page($where = '', $data='*', $order = '', $page = 1, $pagesize = 20) {
		if (is_array($where)) $where = $this->db->sqls($where);
		$where = $where == '' ? '' : ' WHERE '.$where;
		$pagesize 	 = intval($pagesize);
		$page = max(intval($page), 1);
		$offset = $pagesize*($page-1);
		return $this->select($where, $data, "$offset, $pagesize", $order);
	}
	/**
	 * 直接执行SQL,返回所有查询结果
	 *
	 */
	final public function get_one_sql($sql)
	{
		return $this->db->getRow($sql);
	}
	/**
	 * 获取最后数据库操作影响到的条数
	 * @return int
	 */
	final public function affected_rows() 
	{		
		return $this->db->affected_rows;
	}
	/**
	 * 获取数据表主键
	 * @return array
	 */
	final public function get_primary() {
		$list = $this->get_list("SHOW COLUMNS FROM $this->table_name");
		foreach ($list as $arr){
			if($arr['Key'] == 'PRI');
			return $arr['Field'];
		}		
	}
	/**
	 * 获取表字段
	 * @param string $table_name    表名
	 * @return array
	 */
	final public function get_fields($table_name = '') {
		if (empty($table_name)) {
			$table_name = $this->table_name;
		} else {
			$table_name = $this->db_tablepre.$table_name;
		}
		$fields = array();
		$this->get_list("SHOW COLUMNS FROM $table_name");
		foreach ($list as $r){
			$fields[$r['Field']] = $r['Type'];
		}
		return $fields;
	}
	/**
	 * 检查表是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	final public function table_exists($table){
		$tables = $this->list_tables();
		return in_array($this->db_tablepre.$table, $tables) ? 1 : 0;
	}
	final public function list_tables() {
		$tables = array();
		$list = $this->get_list("SHOW TABLES");
		
		foreach ($list as $r){
			$tables[] = $r['Tables_in_'.$this->db_config[$this->db_setting]['database']];
		}
		return $tables;
	}
	/**
	 * 检查字段是否存在
	 * @param $field 字段名
	 * @return boolean
	 */
	final public function field_exists($field) {
		$fields = $this->get_fields($this->table_name);
		return array_key_exists($field, $fields);
	}
	/**
	 * 返回数据库版本号
	 */
	final public function version() {
		return $this->db->server_info;
	}
	/**
	 * 返回数据结果集
	 * @param $query （mysql_query返回值）
	 * @return array
	 */
	final public function fetch_array() {
		$data = array();
		while($r = $this->db->fetch_array) {
			$data[] = $r;
		}
		return $data;
	}
}