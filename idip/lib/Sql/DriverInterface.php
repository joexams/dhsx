<?php 
interface Sql_DriverInterface
{
	public function connect($host, $user, $pw, $db, $port);
	public function exec($sql);
	public function lastInsertId();
	public function close();
	public function quote($string);
	public function error();
	public function prepare($sql);
	public function beginTransaction();
	public function commit();
	public function rollback();
}

