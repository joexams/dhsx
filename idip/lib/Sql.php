<?php 
class Sql
{
	const LOW_PRIORITY  = 0x1;
	const DELAYED       = 0x2;
	const HIGH_PRIORITY = 0x4;
	const QUICK         = 0x8;
	const IGNORE        = 0x16;

	const FETCH_ASSOC   = 0x0;
	const FETCH_OBJECT  = 0x1;

	const SELECT_ALL    = 0x0;
	const SELECT_ROW    = 0x1;
	const SELECT_COL    = 0x2;
	const SELECT_FIELD  = 0x3;

	const SORT_ASC      = 0x0;
	const SORT_DESC     = 0x1;

	private $driver;
	private $count = 0;
	private $list  = array();

	public function __construct(Sql_DriverInterface $driver, $host, $user, $pw, $db, $port)
	{
		$this->driver = $driver;

		if(!($this->driver->connect($host, $user, $pw, $db, $port)))
		{
			throw new Exception('Couldnt connect to database!');
		}
		else
		{
			// set default charset
			$this->exec('SET NAMES "utf8"');
		}
	}

	/**
	 * Main method for data selection. It returns either an associative array
	 * with the data or false. It uses prepared statments so you can write
	 * questionmarks in your query and provide the values in the $params array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array|false
	 */
	public function assoc($sql, array $params = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$last_error = $stmt->error();

		if(!empty($last_error))
		{
			throw new Exception($last_error);
		}


		$this->count++;

		$content = false;

		if($stmt->numRows() > 0)
		{
			$content = $stmt->fetchAssoc();
		}

		return $content;
	}

	/**
	 * Method for data selection. This is an ORM like method where you get an
	 * array of instances of the $class. As third argument you can give an array
	 * of variables wich are passed to the constructor of the $class. This
	 * method is maybe slower then assoc because it creates for each record an
	 * instance of $class
	 *
	 * @param string $sql
	 * @param string $class
	 * @param array $args
	 * @param array $params
	 * @return array|false
	 */
	public function object($sql, array $params = array(), $class = 'stdClass', array $args = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$last_error = $stmt->error();

		if(!empty($last_error))
		{
			throw new Exception($last_error);
		}


		$this->count++;

		$content = false;

		if($stmt->numRows() > 0)
		{
			$content = $stmt->fetchObject($class, $args);
		}

		return $content;
	}

	/**
	 * Main method for data manipulation (INSERT, UPDATE, REPLACE, DELETE). It
	 * returns the number of affected rows. It uses prepared statments so you
	 * can write questionmarks in your query and provide the values in the
	 * $params array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return boolean
	 */
	public function query($sql, array $params = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$lastError = $stmt->error();

		if(!empty($lastError))
		{
			throw new Exception($lastError);
		}


		$this->count++;

		return $stmt->numRows();
	}

	/**
	 * Main method for executing queries where you dont need prepared statments
	 * i.e. setting the charset or selecting another database.
	 *
	 * @param string $sql
	 * @return boolean
	 */
	public function exec($sql)
	{
		if($this->driver->exec($sql) === false)
		{
			throw new Exception($this->driver->error());
		}
		else
		{
			$this->count++;

			return true;
		}
	}

	/**
	 * Returns the result of the query as an array where each row is an
	 * associative where array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getAll($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
	{
		$result = $this->getResult($sql, $params, $mode, $class, $args);

		if(!empty($result))
		{
			return $result;
		}

		return array();
	}

	/**
	 * Returns a single row as associative array where
	 * array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getRow($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
	{
		$content = array();
		$result  = $this->getResult($sql, $params, $mode, $class, $args);

		if(!empty($result))
		{
			$content = current($result);

			unset($result);
		}

		return $content;
	}

	/**
	 * Returns all values from a column as array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getCol($sql, array $params = array())
	{
		$content = array();
		$result  = $this->getResult($sql, $params, self::FETCH_ASSOC);

		if(!empty($result))
		{
			foreach($result as $row)
			{
				$content[] = current($row);
			}

			unset($result);
		}

		return $content;
	}

	/**
	 * Returns the first value an result
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getField($sql, array $params = array())
	{
		$content = false;
		$result  = $this->getResult($sql, $params, self::FETCH_ASSOC);

		if(!empty($result))
		{
			$row = current($result);

			unset($result);

			$content = current($row);
		}

		return $content;
	}

	public function getResult($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
	{
		$result = null;

		switch($mode)
		{
			case self::FETCH_ASSOC:

				$result = $this->assoc($sql, $params);

				break;

			case self::FETCH_OBJECT:

				$result = $this->object($sql, $params, $class, $args);

				break;

			default:

				throw new Exception('Invalid mode');
		}

		return $result;
	}

	/**
	 * Selects all $fields from the $table with the $condition. Calls depending
	 * on the $select value the getAll, getRow, getCol or getField method
	 *
	 * @param string $table
	 * @param array $fields
	 * @param Sql_Condition $condition
	 * @return array
	 */
	public function select($table, array $fields, Sql_Condition $condition = null, $select = 0, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		if(!empty($fields))
		{
			if($condition !== null)
			{
				$sql    = 'SELECT ' . implode(', ', array_map(__CLASS__ . '::helpQuote', $fields)) . ' FROM `' . $table . '` ' . $condition->getStatment() . ' ';
				$params = $condition->getValues();
			}
			else
			{
				$sql    = 'SELECT ' . implode(', ', array_map(__CLASS__ . '::helpQuote', $fields)) . ' FROM `' . $table . '` ';
				$params = array();
			}

			if($sortBy !== null)
			{
				$sql.= 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == self::SORT_ASC ? 'ASC' : 'DESC') . ' ';
			}

			if($startIndex !== null)
			{
				if($select === self::SELECT_ROW || $select === self::SELECT_FIELD)
				{
					$sql.= 'LIMIT 0, 1';
				}
				else
				{
					$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);
				}
			}

			$result = null;

			switch($select)
			{
				case self::SELECT_ALL:

					$result = $this->getAll($sql, $params);

					break;

				case self::SELECT_ROW:

					$result = $this->getRow($sql, $params);

					break;

				case self::SELECT_COL:

					$result = $this->getCol($sql, $params);

					break;

				case self::SELECT_FIELD:

					$result = $this->getField($sql, $params);

					break;
			}

			return $result;
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	/**
	 * Inserts into the $table the values $params
	 *
	 * @param string $table
	 * @param array $params
	 * @param int $modifier
	 * @return boolean
	 */
	public function insert($table, array $params, $modifier = 0)
	{
		if(!empty($params))
		{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}
			elseif($modifier & self::DELAYED)
			{
				$keywords.= ' DELAYED ';
			}
			elseif($modifier & self::HIGH_PRIORITY)
			{
				$keywords.= ' HIGH_PRIORITY ';
			}

			if($modifier & self::IGNORE)
			{
				$keywords.= ' IGNORE ';
			}

			$keys = array_keys($params);
			$sql  = 'INSERT ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->query($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	/**
	 * Update the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param Sql_Condition $condition
	 * @param int $modifier
	 * @return int
	 */
	public function update($table, array $params, Sql_Condition $condition = null, $modifier = 0)
	{
		if(!empty($params))
		{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}

			if($modifier & self::IGNORE)
			{
				$keywords.= ' IGNORE ';
			}

			$keys = array_keys($params);

			if($condition !== null)
			{
				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys)) . ' ' . $condition->getStatment();
				$params = array_merge(array_values($params), $condition->getValues());
			}
			else
			{
				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));
				$params = array_values($params);
			}

			return $this->query($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	/**
	 * Replace the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param Sql_Condition $condition
	 * @param int $modifier
	 * @return int
	 */
	public function replace($table, array $params, Sql_Condition $condition = null, $modifier = 0)
	{
		if(!empty($params))
		{
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

			if($condition !== null)
			{
				$sql    = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys)) . ' ' . $condition->getStatment();
				$params = array_merge(array_values($params), $condition->getValues());
			}
			else
			{
				$sql    = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));
				$params = array_values($params);
			}

			return $this->query($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	/**
	 * Deletes the record on the $table with the $condition
	 *
	 * @param string $table
	 * @param Sql_Condition $condition
	 * @param int $modifier
	 * @return int
	 */
	public function delete($table, Sql_Condition $condition = null, $modifier = 0)
	{
		$keywords = '';

		if($modifier & self::LOW_PRIORITY)
		{
			$keywords.= ' LOW_PRIORITY ';
		}

		if($modifier & self::QUICK)
		{
			$keywords.= ' QUICK ';
		}

		if($modifier & self::IGNORE)
		{
			$keywords.= ' IGNORE ';
		}

		if($condition !== null)
		{
			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '`';
			$params = array();
		}

		return $this->query($sql, $params);
	}

	/**
	 * Returns the count of rows from the $table with the $condition
	 *
	 * @return integer
	 */
	public function count($table, Sql_Condition $condition = null)
	{
		if($condition !== null)
		{
			$sql    = 'SELECT COUNT(*) FROM `' . $table . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT COUNT(*) FROM `' . $table . '`';
			$params = array();
		}

		return (integer) $this->getField($sql, $params);
	}

	public function beginTransaction()
	{
		$this->driver->beginTransaction();
	}

	public function commit()
	{
		$this->driver->commit();
	}

	public function rollback()
	{
		$this->driver->rollback();
	}

	public function close()
	{
		$this->driver->close();
	}

	public function getLastInsertId()
	{
		return $this->driver->lastInsertId();
	}

	public function quote($string)
	{
		return $this->driver->quote($string);
	}

	public function getCount()
	{
		return $this->count;
	}

	public function getDriver()
	{
		return $this->driver;
	}

	public function prepare($sql)
	{
		$key = md5($sql);

		if(!isset($this->list[$key]))
		{
			$stmt = $this->driver->prepare($sql);

			$this->list[$key] = $stmt;
		}
		else
		{
			$stmt = $this->list[$key];
		}

		return $stmt;
	}

	public static function helpQuote($str)
	{
		return '`' . $str . '`';
	}

	public static function helpPrepare($str)
	{
		return '`' . $str . '` = ?';
	}
}