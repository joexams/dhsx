<?php 
class Loader
{
	protected $base;
	protected $config;

	protected $loaded;
	protected $routes;

	public function __construct(Base $base)
	{
		$this->base   = $base;
		$this->config = $base->getConfig();

		$this->loaded = array();
		$this->routes = array();
	}

	public function load($path)
	{
		if(($rewritePath = $this->getRoute($path)) !== false)
		{
			$path = $rewritePath;
		}

		list($path, $file, $class, $method, $uriFragments) = $this->parsePath($path);

		if(!in_array($path, $this->loaded))
		{
			$handle = new $class($this->base, $path, $uriFragments);

			if($handle instanceof ModuleAbstract)
			{
				$this->loaded[] = $path;

				$handle->_ini();

				if($handle instanceof Module_PrivateInterface)
				{
					// we dont call any method if the class is private
				}
				else
				{
					if(!empty($method))
					{
						$handle->$method();
					}
				}
				
				return $handle;
			}
			else
			{
				throw new Exception('Class it is not an instance of ModuleAbstract');
			}
		}

		return false;
	}

	/**
	 * URL format: index.php?x=[path/to/the/file]/class/[method]
	 *
	 * The brackets in [] are optional. the [method] is only called when it
	 * is aviable. All values up to the questionmark are saved as uri
	 * fragment. You can access these values in your module with
	 * $this->getUriFragment() wich returns the values as an array where
	 * the values are exploded after '/'
	 */
	public function parsePath($x, $deep = 0)
	{
		$x   = empty($x) ? $this->config['module_default'] : $x;
		$x   = trim($x, '/');
		$fpc = self::getFPC($x);

		if($fpc !== false)
		{
			list($file, $path, $class) = $fpc;

			require_once($file);

			if(!class_exists($class, false))
			{
				throw new Exception('Class ' . $class . ' doesnt exist in ' . $file);
			}

			// control whether the method exists or not
			$method       = false;
			$rest         = substr($x, strlen($path) + 1);
			$classMethods = get_class_methods($class);
			$reserved     = array('onLoad', 'onPost', 'onPut', 'onDelete', 'onGet');

			if(!empty($rest))
			{
				$method = self::getPart($rest);

				if(in_array($method, $classMethods) && !in_array($method, $reserved))
				{
					$rest = substr($rest, strlen($method) + 1);
				}
				else
				{
					$method = false;
				}
			}

			// if we have no method look for an index
			if($method === false)
			{
				if(in_array('index', $classMethods))
				{
					$method = 'index';
				}
			}

			// get uri fragments
			$uriFragments = array();

			if(!empty($rest))
			{
				$uriFragments = explode('/', trim($rest, '/'));
			}
		}
		else
		{
			if($deep == 0)
			{
				$x = $this->config['module_default'] . '/' . $x;

				return $this->parsePath($x, ++$deep);
			}
			else
			{
				throw new Exception('Unkown module!');
			}
		}

		return array(

			$path,
			$file,
			$class,
			$method,
			$uriFragments,

		);
	}

	public function addRoute($path, $module)
	{
		$key = md5($path);

		$this->routes[$key] = $module;
	}

	public function getRoute($path)
	{
		$key = md5($path);

		return isset($this->routes[$key]) ? $this->routes[$key] : false;
	}

	public static function getPart($path)
	{
		$pos = strpos($path, '/');

		if($pos === false)
		{
			return $path;
		}
		else
		{
			return substr($path, 0, $pos);
		}
	}

	public static function getFPC($path)
	{
		$path = trim($path, '/');

		if(is_file(PATH_MODULE . '/' . $path . '.php'))
		{
			$file = PATH_MODULE . '/' . $path . '.php';
			$pos  = strrpos($path, '/');

			if($pos === false)
			{
				$class = $path;
			}
			else
			{
				$class = substr($path, $pos + 1);
			}

			return array($file, $path, $class);
		}
		elseif(is_file(PATH_MODULE . '/' . $path . '/' . 'index.php'))
		{
			$file  = PATH_MODULE . '/' . $path . '/' . 'index.php';
			$class = 'index';

			return array($file, $path, $class);
		}
		else
		{
			$pos  = strrpos($path, '/');
			$file = substr($path, 0, $pos);

			if($pos === false)
			{
				return false;
			}
			else
			{
				return self::getFPC($file);
			}
		}
	}
}