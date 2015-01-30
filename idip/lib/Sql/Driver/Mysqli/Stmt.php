<?php 
class Sql_Driver_Mysqli_Stmt implements Sql_StmtInterface
{
	private $handle;
	private $params = array();

	private $isExecuted = false;
	private $length     = 0;

	public function __construct($stmt)
	{
		$this->handle = $stmt;
	}

	public function bindParam($value)
	{
		array_push($this->params, array(

			'type'  => self::getType($value),
			'value' => $value,

		));
	}

	public function execute()
	{
		$length = count($this->params);

		if($length > 0)
		{
			if($this->isExecuted === false)
			{
				$data  = array();
				$types = '';

				$data[] =& $types;

				foreach($this->params as $i => $param)
				{
					$key = 'var_' . $i;

					$this->$key = null;

					$data[] =& $this->$key;

					$types.= $param['type'];
				}

				call_user_func_array(array($this->handle, 'bind_param'), $data);

				$this->length = $length;

				$this->isExecuted = true;
			}

			if($this->length == $length)
			{
				foreach($this->params as $i => $param)
				{
					$key = 'var_' . $i;

					$this->$key = $param['value'];
				}

				$this->params = array();
			}
			else
			{
				throw new Exception('You must provide the same params in a reused stmt');
			}
		}

		$this->handle->execute();

		$this->handle->store_result();
	}

	public function numRows()
	{
		return $this->handle->num_rows;
	}

	public function fetchAssoc()
	{
		$meta   = $this->handle->result_metadata();
		$params = array();
		$row    = array();

		while($field = $meta->fetch_field())
		{
			$params[] =& $row[$field->name];
		}

		call_user_func_array(array($this->handle, 'bind_result'), $params);

		while($this->handle->fetch())
		{
			$copy = array();

			foreach($row as $key => $val)
			{
				$copy[$key] = $val;
			}

			$result[] = $copy;
		}

		$this->handle->free_result();

		return $result;
	}

	public function fetchObject($class = 'stdClass', array $args = array())
	{
		$meta = $this->handle->result_metadata();

		while($field = $meta->fetch_field())
		{
			$params[] =& $row[$field->name];
		}

		call_user_func_array(array($this->handle, 'bind_result'), $params);

		$reflection  = new ReflectionClass($class);
		$constructor = $reflection->getConstructor();

		if(empty($constructor))
		{
			while($this->handle->fetch())
			{
				$obj = $reflection->newInstance();

				foreach($row as $key => $val)
				{
					$obj->$key = $val;
				}

				$result[] = $obj;
			}
		}
		else
		{
			while($this->handle->fetch())
			{
				$obj = $reflection->newInstanceArgs($args);

				foreach($row as $key => $val)
				{
					$obj->$key = $val;
				}

				$result[] = $obj;
			}
		}

		$this->handle->free_result();

		return $result;
	}

	public function getHandle()
	{
		return $this->handle;
	}

	public function close()
	{
		$this->handle->close();
	}

	public function error()
	{
		return $this->handle->error;
	}

	private static function getType($var)
	{
		if(is_int($var))
		{
			return 'i';
		}
		elseif(is_double($var))
		{
			return 'd';
		}
		else
		{
			return 's';
		}
	}
}