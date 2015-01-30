<?php 
interface Sql_StmtInterface
{
	public function bindParam($value);
	public function execute();
	public function numRows();
	public function fetchAssoc();
	public function fetchObject($class = 'stdClass', array $args = array());
	public function getHandle();
	public function close();
	public function error();
}