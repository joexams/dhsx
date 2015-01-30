<?php
class Bootstrap
{
	public function __construct(Config $config)
	{
		// define benchmark
		$GLOBALS['benchmark'] = microtime(true);

		// setting default headers
		header('Content-type: text/html; charset=UTF-8');
		header('X-Powered-By: HALO');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		// define paths
		define('PATH_CACHE', $config['path_cache']);
		define('PATH_LIBRARY', $config['path_library']);
		define('PATH_MODULE', $config['path_module']);

		// set include path
		if(!empty($config['path_library']))
		{
			set_include_path(PATH_LIBRARY . PATH_SEPARATOR . get_include_path());
		}

		require_once('Loader.php');
		// autoload register
		spl_autoload_register('Bootstrap::autoload');

		// error handling
		if($config['debug'] === true)
		{
			$errorReporting = E_ALL | E_STRICT;
		}
		else
		{
			$errorReporting = 0;
		}

		error_reporting($errorReporting);
		set_error_handler('Bootstrap::errorHandler');

		// ini settings
		ini_set('date.timezone', $config['timezone']);

		// define in IDIP
		define('IDIP', true);
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		if(error_reporting() == 0)
		{
			return false;
		}
		else
		{
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}
	}

	public static function autoload($className)
	{
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';

		if($lastNsPos = strripos($className, '\\'))
		{
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName.= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		require_once($fileName);
	}
}
