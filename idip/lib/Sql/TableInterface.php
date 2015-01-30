<?php 
interface Sql_TableInterface
{
	const PRIMARY_KEY     = 0x10000000;
	const AUTO_INCREMENT  = 0x20000000;
	const IS_NULL         = 0x40000000;
	const UNSIGNED        = 0x80000000;

	// integer
	const TYPE_TINYINT    = 0x100000;
	const TYPE_SMALLINT   = 0x200000;
	const TYPE_MEDIUMINT  = 0x300000;
	const TYPE_INT        = 0x400000;
	const TYPE_BIGINT     = 0x500000;

	const TYPE_DECIMAL    = 0x600000;
	const TYPE_FLOAT      = 0x700000;
	const TYPE_DOUBLE     = 0x800000;
	const TYPE_REAL       = 0x900000;

	const TYPE_BIT        = 0xA00000;
	const TYPE_BOOLEAN    = 0xB00000;
	const TYPE_SERIAL     = 0xC00000;

	// date
	const TYPE_DATE       = 0xD00000;
	const TYPE_DATETIME   = 0xE00000;
	const TYPE_TIMESTAMP  = 0xF00000;
	const TYPE_TIME       = 0x1000000;
	const TYPE_YEAR       = 0x1100000;

	// string
	const TYPE_CHAR       = 0x1200000;
	const TYPE_VARCHAR    = 0x1300000;

	const TYPE_BINARY     = 0x1400000;
	const TYPE_VARBINARY  = 0x1500000;

	const TYPE_TINYTEXT   = 0x1600000;
	const TYPE_TEXT       = 0x1700000;
	const TYPE_MEDIUMTEXT = 0x1800000;
	const TYPE_LONGTEXT   = 0x1900000;

	const TYPE_TINYBLOB   = 0x1A00000;
	const TYPE_MEDIUMBLOB = 0x1B00000;
	const TYPE_BLOB       = 0x1C00000;
	const TYPE_LONGBLOB   = 0x1D00000;

	const TYPE_ENUM       = 0x1E00000;
	const TYPE_SET        = 0x1F00000;

	/**
	 * Returns the relation between the table and columns
	 *
	 * array(
	 * 	'column' => 'table'
	 * )
	 *
	 * @return array
	 */
	public function getConnections();

	/**
	 * Returns the name of the table
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the columns of the table where the key is the name of the column
	 * and the value contains OR connected informations. I.e.:
	 *
	 * array(
	 *  'id'    => self::TYPE_INT | 10 | self::AUTO_INCREMENT | self::PRIMARY_KEY,
	 *  'title' => self::TYPE_VARCHAR | 256
	 * )
	 *
	 * For better understanding here an 32 bit integer representation of the
	 * example above:
	 *
	 *             UNAP     T                L
	 * array(      |||| |-------| |----------------------|
	 *  'id'    => 0011 0000 0100 0000 0000 0000 0000 1010
	 *  'title' => 0000 1100 0000 0000 0000 0001 0000 0000
	 * )
	 *
	 * L: Length of the column max value is 0xFFFFF (decimal: 1048575)
	 * T: Type of the column one of TYPE_* constant
	 * P: Whether its a primary key
	 * A: Whether its an auto increment value
	 * N: Whether the column can be NULL
	 * U: Whether the value is unsigned
	 *
	 * @return array
	 */
	public function getColumns();

	/**
	 * Returns the underling sql object
	 *
	 * @return PSX_Sql
	 */
	public function getSql();

	/**
	 * Returns the name of the record wich should contain only alpha characters.
	 * Table names are typically seperated with _ (underscore). This method
	 * should return the last part of the table name i.e. "amun_system_request"
	 * should return "request"
	 *
	 * @return string
	 */
	public function getDisplayName();

	/**
	 * Returns the name of the primary key column
	 *
	 * @return string
	 */
	public function getPrimaryKey();

	/**
	 * Returns the first column with a specific attribute
	 *
	 * @return string
	 */
	public function getFirstColumnWithAttr($searchAttr);

	/**
	 * Returns the first column from the type
	 *
	 * @return string
	 */
	public function getFirstColumnWithType($searchType);

	/**
	 * Returns an array containing all valid columns of the array $columns
	 *
	 * @return array
	 */
	public function getValidColumns(array $columns);

	/**
	 * Starts a new complex selection on this table
	 *
	 * @return Sql_Table_SelectInterface
	 */
	public function select(array $columns = array(), $prefix = null);

	/**
	 * Returns the last selection wich was created by the method select()
	 *
	 * @return Sql_Table_SelectInterface
	 */
	public function getLastSelect();

	/**
	 * Returns a new record for the table. The class name is build from this
	 * class without the "_Table" suffix. If an $id is provided the record
	 * contains all fields from the table. If the record does not exist an
	 * exception is thrown
	 *
	 * @return PSX_Data_RecordInterface
	 */
	public function getRecord($id = null);

	/**
	 *
	 *
	 * @return array
	 */
	public function getAll(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);

	/**
	 *
	 *
	 * @return array
	 */
	public function getRow(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0);

	/**
	 *
	 * @return array
	 */
	public function getCol(array $fields, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);

	/**
	 *
	 * @return string
	 */
	public function getField($field, Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0);

	/**
	 *
	 *
	 */
	public function count(Sql_Condition $condition = null);

	/**
	 *
	 * @return integer
	 */
	public function insert(array $params, $modifier = 0);

	/**
	 *
	 * @return integer
	 */
	public function update(array $params, Sql_Condition $condition = null, $modifier = 0);

	/**
	 *
	 * @return integer
	 */
	public function replace(array $params, Sql_Condition $condition = null, $modifier = 0);

	/**
	 *
	 * @return integer
	 */
	public function delete(Sql_Condition $condition = null, $modifier = 0);
}