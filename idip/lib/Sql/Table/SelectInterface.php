<?php 
interface Sql_Table_SelectInterface
{
	public function join($type, $table, $cardinality = 'n:1', $foreignKey = null);
	public function where($column, $operator, $value, $conjunction = 'AND');
	public function groupBy($column);
	public function orderBy($column, $sort = 0x1);
	public function limit($start, $count = null);

	public function getResultSet($startIndex = 0, $count = 32, $sortBy = null, $sortOrder = null, $filterBy = null, $filterOp = null, $filterValue = null, $updatedSince = null, $mode = 0, $class = null, array $args = array());
	public function getAll($mode = 0, $class = null, array $args = array());
	public function getRow($mode = 0, $class = null, array $args = array());
	public function getCol();
	public function getField();
	public function getTotalResults();

	public function getTable();
	public function getSql();
	public function getCondition();
	public function setPrefix($prefix);
	public function setColumns(array $columns);
	public function getSupportedFields();
	public function getSelfColumns();
	public function getAllColumns();
	public function getSelectedColumns();
	public function getAllSelectedColumns();
	public function buildJoins();
}

