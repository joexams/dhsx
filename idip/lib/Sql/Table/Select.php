<?php 
class Sql_Table_Select implements Sql_Table_SelectInterface
{
	protected $table;
	protected $sql;
	protected $condition;

	protected $joins   = array();
	protected $columns = array();
	protected $prefix;

	protected $selfColumns      = array();
	protected $availableColumns = array();

	protected $start;
	protected $count;

	protected $orderBy = array();
	protected $groupBy = array();

	public function __construct(Sql_TableInterface $table, $prefix = null)
	{
		$this->table     = $table;
		$this->sql       = $table->getSql();
		$this->condition = new Sql_Condition();

		$this->setPrefix($prefix);
	}

	public function join($type, $table, $cardinality = 'n:1', $foreignKey = null)
	{
		if($table instanceof Sql_TableInterface)
		{
		}
		else if($table instanceof Sql_Table_SelectInterface)
		{
			$table = $table->getTable();
		}
		else
		{
			throw new Exception('Invalid table must be instanceof Sql_TableInterface or Sql_Table_SelectInterface');
		}

		if($table->getLastSelect() === null)
		{
			throw new Exception('Nothing is selected on table ' . $table->getAlias());
		}

		$this->joins[] = new Sql_Join($type, $table, $cardinality, $foreignKey);

		$this->condition->merge($table->getLastSelect()->getCondition());

		$this->availableColumns = array_merge($this->availableColumns, $table->getLastSelect()->getAllColumns());

		return $this;
	}

	public function where($column, $operator, $value, $conjunction = 'AND')
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->condition->add($this->availableColumns[$column], $operator, $value, $conjunction);

		return $this;
	}

	public function groupBy($column)
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->groupBy[] = $this->availableColumns[$column];

		return $this;
	}

	public function orderBy($column, $sort = 0x1)
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->orderBy[] = array($column, $sort === Sql::SORT_ASC ? 'ASC' : 'DESC');

		return $this;
	}

	public function limit($start, $count = null)
	{
		if($count === null)
		{
			$this->start = 0;
			$this->count = (integer) $start;
		}
		else
		{
			$this->start = (integer) $start;
			$this->count = (integer) $count;
		}

		return $this;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function getCondition()
	{
		return $this->condition;
	}

	public function getJoins()
	{
		return $this->joins;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function setPrefix($prefix)
	{
		$this->prefix           = $prefix;
		$this->selfColumns      = $this->getSelfColumns();
		$this->availableColumns = $this->selfColumns;

		return $this;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function setColumns(array $columns)
	{
		$this->columns = $columns;

		// delete other column selections
		foreach($this->joins as $join)
		{
			$join->getTable()->getLastSelect()->setColumns(array());
		}

		return $this;
	}

	public function getSupportedFields()
	{
		return array_keys($this->availableColumns);
	}

	public function getSelfColumns()
	{
		$columns     = array();
		$selfColumns = $this->table->getColumns();

		foreach($selfColumns as $column => $attr)
		{
			$alias = $this->prefix !== null ? $this->prefix . ucfirst($column) : $column;
			$value = '`' . $this->table->getAlias() . '`.`' . $column . '`';

			$columns[$alias] = $value;
		}

		return $columns;
	}

	public function getAllColumns()
	{
		return $this->availableColumns;
	}

	public function getSelectedColumns()
	{
		$selectedColumns = array();
		$columns         = $this->table->getColumns();

		foreach($this->columns as $column)
		{
			if($this->prefix !== null && isset($columns[$column]))
			{
				$column = $this->prefix . ucfirst($column);
			}

			if(isset($this->availableColumns[$column]))
			{
				$selectedColumns[$column] = $this->availableColumns[$column];
			}
		}

		return $selectedColumns;
	}

	public function getAllSelectedColumns()
	{
		$selectedColumns = $this->getSelectedColumns();

		foreach($this->joins as $join)
		{
			$selectedColumns = array_merge($selectedColumns, $join->getTable()->getLastSelect()->getAllSelectedColumns());
		}

		return $selectedColumns;
	}

	public function getResultSet($startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, $filterBy = null, $filterOp = null, $filterValue = null, $updatedSince = null, $mode = 0, $class = null, array $args = array())
	{
		$start     = $startIndex !== null ? (integer) $startIndex : 0;
		$count     = $count      !== null ? (integer) $count      : 16;
		$sortBy    = $sortBy     !== null && isset($this->availableColumns[$sortBy]) ? $sortBy : current($this->columns);
		$sortOrder = $sortOrder  !== null ? (strcasecmp($sortOrder, 'ascending') == 0 ? Sql::SORT_ASC : Sql::SORT_DESC) : Sql::SORT_DESC;

		if(isset($this->availableColumns[$filterBy]))
		{
			switch($filterOp)
			{
				case 'contains':

					$this->where($filterBy, 'LIKE', '%' . $filterValue . '%');

					break;

				case 'equals':

					$this->where($filterBy, '=', $filterValue);

					break;

				case 'startsWith':

					$this->where($filterBy, 'LIKE', $filterValue . '%');

					break;

				case 'present':

					$this->where($filterBy, 'IS NOT', 'NULL', 'AND');
					$this->where($filterBy, 'NOT LIKE', '');

					break;
			}
		}

		if($updatedSince !== null)
		{
			// search datetime field
			$dateColumn = $this->table->getFirstColumnWithType(self::TYPE_DATETIME);

			if($dateColumn !== null)
			{
				$datetime = new DateTime($updatedSince);

				$this->where($dateColumn, '>', $datetime->format(PSX_Time::SQL));
			}
		}

		$this->orderBy($sortBy, $sortOrder);
		$this->limit($start, $count);

		$totalResults = $this->getTotalResults();
		$entries      = $this->getAll($mode, $class, $args);

		$resultSet = new PSX_Data_ResultSet($totalResults, $start, $count, $entries);

		return $resultSet;
	}

	public function getAll($mode = 0, $class = null, array $args = array())
	{
		if($mode === Sql::FETCH_OBJECT && $class === null && $args === array())
		{
			$class = $this->table->getDefaultRecordClass();
			$args  = $this->table->getDefaultRecordArgs();
		}

		return $this->sql->getAll($this->buildQuery(), $this->condition->getValues(), $mode, $class, $args);
	}

	public function getRow($mode = 0, $class = null, array $args = array())
	{
		if($mode === Sql::FETCH_OBJECT && $class === null && $args === array())
		{
			$class = $this->table->getDefaultRecordClass();
			$args  = $this->table->getDefaultRecordArgs();
		}

		$this->limit(1);

		return $this->sql->getRow($this->buildQuery(), $this->condition->getValues(), $mode, $class, $args);
	}

	public function getCol()
	{
		return $this->sql->getCol($this->buildQuery(), $this->condition->getValues());
	}

	public function getField()
	{
		$this->limit(1);

		return $this->sql->getField($this->buildQuery(), $this->condition->getValues());
	}

	public function getTotalResults()
	{
		return (integer) $this->sql->getField($this->buildCountQuery(), $this->condition->getValues());
	}

	public function buildJoins()
	{
		$sql = '';

		foreach($this->joins as $join)
		{
			$fk    = $join->getForeignKey();
			$table = $join->getTable();
			$cardi = $join->getCardinality();

			$sql.= $join->getType() . ' JOIN `' . $table->getName() . '` AS `' . $table->getAlias() . '` ON ';

			if($cardi[0] == '1')
			{
				$sql.= '`' . $this->table->getAlias() . '`.`' . $this->table->getPrimaryKey() . '` = ';
			}
			else if($cardi[0] == 'n')
			{
				$fk  = $fk === null ? $this->getForeignKeyByTable($this->table->getConnections(), $table->getName()) : $fk;
				$sql.= '`' . $this->table->getAlias() . '`.`' . $fk . '` = ';
			}

			if($cardi[1] == '1')
			{
				$sql.= '`' . $table->getAlias() . '`.`' . $table->getPrimaryKey() . '`';
			}
			else if($cardi[1] == 'n')
			{
				$fk  = $fk === null ? $this->getForeignKeyByTable($table->getConnections(), $this->table->getName()) : $fk;
				$sql.= '`' . $table->getAlias() . '`.`' . $fk . '`';
			}

			$sql.= ' ' . $table->getLastSelect()->buildJoins() . ' ';
		}

		return $sql;
	}

	protected function buildQuery()
	{
		$selectedColumns = $this->getAllSelectedColumns();

		if(empty($selectedColumns))
		{
			throw new Exception('No valid columns selected');
		}

		// select
		$sql = 'SELECT ';
		$i   = 0;
		$len = count($selectedColumns) - 1;

		foreach($selectedColumns as $alias => $column)
		{
			$sql.= $column . ' AS `' . $alias . '`' . ($i < $len ? ',' : '');

			$i++;
		}

		$sql.= ' FROM `' . $this->table->getName() . '` AS `' . $this->table->getAlias() . '` ' . $this->buildJoins();

		// where
		if($this->condition->hasCondition())
		{
			$sql.= $this->condition->getStatment();
		}

		// group by
		if(!empty($this->groupBy))
		{
			$sql.= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		// order
		if(!empty($this->orderBy))
		{
			$len = count($this->orderBy) - 1;
			$sql.= ' ORDER BY';

			foreach($this->orderBy as $key => $orderBy)
			{
				$sql.= ' ' . $orderBy[0] . ' ' . $orderBy[1] . ' ' . ($len == $key ? '' : ',');
			}
		}

		// limit
		if($this->start !== null)
		{
			$sql.= ' LIMIT ' . $this->start . ', ' . $this->count;
		}

		return $sql;
	}

	protected function buildCountQuery()
	{
		// select
		$sql = 'SELECT COUNT(*) FROM `' . $this->table->getName() . '` AS `' . $this->table->getAlias() . '` ' . $this->buildJoins();

		// condition
		if($this->condition->hasCondition())
		{
			$sql.= $this->condition->getStatment();
		}

		return $sql;
	}

	protected function getForeignKeyByTable(array $connections, $foreignTable)
	{
		foreach($connections as $column => $table)
		{
			if($table == $foreignTable)
			{
				return $column;
			}
		}

		throw new Exception($foreignTable . ' is not connected to ' . $this->table->getAlias());
	}
}
