<?php 
class Sql_Driver_Mysqli implements Sql_DriverInterface
{
	private $handle;
	private $stmt;

	public function connect($host, $user, $pw, $db, $port)
	{
		$this->handle = new mysqli($host, $user, $pw, $db, $port);

		if($this->handle->connect_error)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function exec($sql)
	{
		$result = $this->handle->query($sql);

		if($result === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function lastInsertId()
	{
		return $this->handle->insert_id;
	}

	public function close()
	{
		$this->handle->close();
	}

	public function quote($value)
	{
		return '\'' . $this->handle->real_escape_string(strval($value)) . '\'';
	}

	public function error()
	{
		return $this->handle->error;
	}

	public function prepare($sql)
	{
		$stmt = $this->handle->prepare($sql);

		if($stmt === false)
		{
			throw new Exception($this->error());
		}
		else
		{
			return new Sql_Driver_Mysqli_Stmt($stmt);
		}
	}

	public function beginTransaction()
	{
		$this->handle->autocommit(false);
	}

	public function commit()
	{
		$this->handle->commit();
	}

	public function rollback()
	{
		$this->handle->rollback();
	}
}