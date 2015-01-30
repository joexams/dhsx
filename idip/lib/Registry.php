<?php 
class Registry extends ArrayObject
{
	protected static $_instance;

	protected $container = array();

	public function __construct()
	{
		parent::__construct($this->container, parent::ARRAY_AS_PROPS);
	}

	public function clear()
	{
		$this->exchangeArray($this->container = array());
	}

	public static function getInstance()
	{
		if(self::$_instance === null)
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function get($key)
	{
		return self::getInstance()->offsetGet($key);
	}

	public static function set($key, $value)
	{
		self::getInstance()->offsetSet($key, $value);
	}

	public static function has($key)
	{
		return self::getInstance()->offsetExists($key);
	}
}

