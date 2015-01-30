<?php 
abstract class Sql_TableAbstract implements Sql_TableInterface
{
	private static $aliasId = 0;

	protected $alias;
	protected $sql;
	protected $select;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;

		$this->setAlias(self::$aliasId++);
	}

	public function getAlias()
	{
		return $this->alias;
	}

	public function setAlias($alias)
	{
		$this->alias = $alias;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function getDisplayName()
	{
		$name = $this->getName();
		$pos  = strrpos($name, '_');

		return $pos !== false ? substr($name, strrpos($name, '_') + 1) : $name;
	}

	public function getPrimaryKey()
	{
		return $this->getFirstColumnWithAttr(self::PRIMARY_KEY);
	}

	public function getFirstColumnWithAttr($searchAttr)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if($attr & $searchAttr)
			{
				return $column;
			}
		}

		return null;
	}

	public function getFirstColumnWithType($searchType)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if(((($attr >> 20) & 0xFF) << 20) === $searchType)
			{
				return $column;
			}
		}

		return null;
	}

	public function getValidColumns(array $columns)
	{
		return array_intersect($columns, array_keys($this->getColumns()));
	}

	public function select(array $columns = array(), $prefix = null)
	{
		$this->select = new Sql_Table_Select($this, $prefix);

		if(in_array('*', $columns))
		{
			$this->select->setColumns(array_keys($this->getColumns()));
		}
		else
		{
			$this->select->setColumns($columns);
		}

		return $this->select;
	}

	public function getLastSelect()
	{
		return $this->select;
	}

	public function getRecord($id = null)
	{
		$class = $this->getDefaultRecordClass();
		$args  = $this->getDefaultRecordArgs();

		if($id !== null)
		{
			$fields = implode(', ', array_map('PSX_Sql::helpQuote', array_keys($this->getColumns())));
			$sql    = 'SELECT ' . $fields . ' FROM `' . $this->getName() . '` WHERE `' . $this->getPrimaryKey() . '` = ?';
			$record = $this->sql->getRow($sql, array($id), PSX_Sql::FETCH_OBJECT, $class, $args);

			if(empty($record))
			{
				throw new Exception('Invalid record id');
			}
		}
		else
		{
			$ref    = new ReflectionClass($class);
			$record = $ref->newInstanceArgs($args);
		}

		return $record;
	}

	public function getAll(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_ALL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getRow(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_ROW, $sortBy, $sortOrder);
	}

	public function getCol(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_COL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getField($field, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns(array($field));

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_FIELD, $sortBy, $sortOrder);
	}

	public function count(Sql_Condition $condition = null)
	{
		return $this->sql->count($this->getName(), $condition);
	}

	public function insert(array $params, $modifier = 0)
	{
		$params = array_intersect_key($params, $this->getColumns());

		return $this->sql->insert($this->getName(), $params, $modifier);
	}

	public function update(array $params, Sql_Condition $condition = null, $modifier = 0)
	{
		$params = array_intersect_key($params, $this->getColumns());

		return $this->sql->update($this->getName(), $params, $condition, $modifier);
	}

	public function replace(array $params, Sql_Condition $condition = null, $modifier = 0)
	{
		$params = array_intersect_key($params, $this->getColumns());

		return $this->sql->replace($this->getName(), $params, $condition, $modifier);
	}

	public function delete(Sql_Condition $condition = null, $modifier = 0)
	{
		return $this->sql->delete($this->getName(), $condition, $modifier);
	}

	public function getDefaultRecordClass()
	{
		return substr(get_class($this), 0, -6);
	}

	public function getDefaultRecordArgs()
	{
		return array($this);
	}
}
