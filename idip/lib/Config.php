<?php 
class Config extends ArrayIterator
{
	/**
	 * The container for the config array
	 *
	 * @var array
	 */
	private $container = array();

	public function __construct($file)
	{
		if(is_array($file))
		{
			$config = $file;
		}
		else
		{
			include($file);
		}

		if(isset($config))
		{
			// assign container
			parent::__construct($config);
		}
		else
		{
			throw new Exception('Couldnt find config in file');
		}
	}
}
