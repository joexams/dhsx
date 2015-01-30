<?php 
common::load_class('Stmt', '', 0);

class Sql {
	const LOW_PRIORITY  = 0x1;
	const DELAYED       = 0x2;
	const HIGH_PRIORITY = 0x4;
	const QUICK         = 0x8;
	const IGNORE        = 0x16;

	private $handle;
	private $count = 0;
	private $list  = array();

	public function __construct($host, $user, $pw, $db, $port)	{
		if(!($this->connect($host, $user, $pw, $db, $port))) {
			throw new Exception('Couldnt connect to database!');
		}else {
			$this->exec('SET NAMES "utf8"');
		}
	}
	/**
	 * 连接数据库
	 * @param  [type] $host [description]
	 * @param  [type] $user [description]
	 * @param  [type] $pw   [description]
	 * @param  [type] $db   [description]
	 * @param  [type] $port [description]
	 * @return [type]       [description]
	 */
	private function connect($host, $user, $pw, $db, $port)	{
		$this->handle = new mysqli($host, $user, $pw, $db, $port);

		if($this->handle->connect_error) {
			return false;
		}else {
			return true;
		}
	}
	/**
	 * 执行SQL语句
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function exec($sql) {
		$result = $this->handle->query($sql);
		if($result === false) {
			throw new Exception($this->error());
		}else {
			$this->count++;
			return true;
		}
	}
	/**
	 * 查询SQL语句
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function query($sql, array $params = array()) {
		$stmt = $this->prepare($sql);

		if(count($params) > 0) {
			foreach($params as $v) {
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();

		$lastError = $stmt->error();

		if(!empty($lastError)) {
			throw new Exception($lastError);
		}

		$this->count++;

		return $stmt->numRows();
	}
	/**
	 * 返回查询结果
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getResult($sql, array $params = array()) {
		$result = null;
		$result = $this->assoc($sql, $params);
		return $result;
	}
	/**
	 * 返回所有查询结果
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getAll($sql, array $params = array()) {
		$result = $this->getResult($sql, $params);

		if(!empty($result)) {
			return $result;
		}

		return array();
	}
	/**
	 * 返回当前查询结果
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getRow($sql, array $params = array()) {
		$content = array();
		$result  = $this->getResult($sql, $params);

		if(!empty($result))	{
			$content = current($result);

			unset($result);
		}

		return $content;
	}
	/**
	 * 返回当前字段记录
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getField($sql, array $params = array()) {
		$content = false;
		$result  = $this->getResult($sql, $params);

		if(!empty($result))	{
			$row = current($result);

			unset($result);

			$content = current($row);
		}

		return $content;
	}
	/**
	 * count查询
	 * @param  [type] $table     [description]
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function count($table, $where = null)
	{
		if($where !== null)	{
			$where = $this->sqls($where);
			$where = ' WHERE '.$where;

			$sql    = 'SELECT COUNT(*) FROM `' . $table . '` ' . $where;
		}else {
			$sql    = 'SELECT COUNT(*) FROM `' . $table . '`';
		}
		$params = array();

		return (integer) $this->getField($sql, $params);
	}
	/**
	 * 获取数据
	 * @param  [type] $sql    [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function assoc($sql, array $params = array()) {
		$stmt = $this->prepare($sql);

		if(count($params) > 0) {
			foreach($params as $v) {
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();
		$last_error = $stmt->error();

		if(!empty($last_error))	{
			throw new Exception($last_error);
		}

		$this->count++;
		$content = false;

		if($stmt->numRows() > 0) {
			$content = $stmt->fetchAssoc();
		}

		return $content;
	}

	public function prepare($sql) {
		$key = md5($sql);

		if(!isset($this->list[$key])) {
			$stmt = $this->handle->prepare($sql);

			if($stmt === false)	{
				throw new Exception($this->error());
			}else {
				$stmt = new Stmt($stmt);
			}

			$this->list[$key] = $stmt;
		}else {
			$stmt = $this->list[$key];
		}

		return $stmt;
	}

	/**
	 * 写入数据
	 * @param  [type]  $table    [description]
	 * @param  array   $params   [description]
	 * @param  integer $modifier [description]
	 * @return [type]            [description]
	 */
	public function insert($table, array $params, $modifier = 0) {
		if(!empty($params))	{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY) {
				$keywords.= ' LOW_PRIORITY ';
			}elseif ($modifier & self::DELAYED)	{
				$keywords.= ' DELAYED ';
			}elseif ($modifier & self::HIGH_PRIORITY) {
				$keywords.= ' HIGH_PRIORITY ';
			}

			if($modifier & self::IGNORE) {
				$keywords.= ' IGNORE ';
			}

			$keys = array_keys($params);
			$sql  = 'INSERT ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->query($sql, $params);
		}else {
			throw new Exception('Array must not be empty');
		}
	}
	/**
	 * 更新操作
	 * @param  [type]  $table     [description]
	 * @param  array   $params    [description]
	 * @param  [type]  $where     [description]
	 * @param  integer $modifier  [description]
	 * @return [type]             [description]
	 */
	public function update($table, array $params, $where = null, $modifier = 0)
	{
		if(!empty($params))	{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY) {
				$keywords.= ' LOW_PRIORITY ';
			}

			if($modifier & self::IGNORE) {
				$keywords.= ' IGNORE ';
			}

			$keys = array_keys($params);

			if($where !== null) {
				$where = $this->sqls($where);
				$where = ' WHERE '.$where;

				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys)) . ' ' . $where;
			}else {
				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));
			}
			$params = array_values($params);

			return $this->query($sql, $params);
		}else {
			throw new Exception('Array must not be empty');
		}
	}
	/**
	 * replace写入
	 * @param  [type]  $table     [description]
	 * @param  array   $params    [description]
	 * @param  [type]  $where     [description]
	 * @param  integer $modifier  [description]
	 * @return [type]             [description]
	 */
	public function replace($table, array $params, $where = null, $modifier = 0) {
		if(!empty($params)) {
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}
			else if($modifier & self::DELAYED)
			{
				$keywords.= ' DELAYED ';
			}

			$keys = array_keys($params);

			if($where !== null)	{
				$where = $this->sqls($where);
				$where = ' WHERE '.$where;

				$sql    = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys)) . ' ' . $where;
				$params = array_values($params);
			}else {
				$sql    = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));
			}
			$params = array_values($params);

			return $this->query($sql, $params);
		}else {
			throw new Exception('Array must not be empty');
		}
	}

	/**
	 * 删除
	 * @param  [type]  $table     [description]
	 * @param  [type]  $where     [description]
	 * @param  integer $modifier  [description]
	 * @return [type]             [description]
	 */
	public function delete($table, $where = null, $modifier = 0) {
		$keywords = '';

		if($modifier & self::LOW_PRIORITY) {
			$keywords.= ' LOW_PRIORITY ';
		}

		if($modifier & self::QUICK)	{
			$keywords.= ' QUICK ';
		}

		if($modifier & self::IGNORE) {
			$keywords.= ' IGNORE ';
		}

		if($where !== null)	{
			$where = $this->sqls($where);
			$where = ' WHERE '.$where;

			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '` ' . $where;
		}else {
			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '`';
		}

		$params = array();

		return $this->query($sql, $params);
	}


	public function sqls($where, $font = ' AND ') {
		if (is_array($where)) {
			$sql = '';
			foreach ($where as $key=>$val) {
				$sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
			}
			return $sql;
		} else {
			return $where;
		}
	}

	public function lastInsertId() {
		return $this->handle->insert_id;
	}

	public function getCount() {
		return $this->count;
	}

	private function error() {
		return $this->handle->error;
	}

	public function close() {
		$this->handle->close();
	}

	public static function helpQuote($str) {
		return '`' . $str . '`';
	}

	public static function helpPrepare($str) {
		return '`' . $str . '` = ?';
	}
}