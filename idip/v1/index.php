<?php 
require_once('../lib/Config.php');
require_once('../lib/Bootstrap.php');

$config    = new Config('../configuration.php');
$bootstrap = new Bootstrap($config);

try
{
	ob_start('responseProcess');

	$base = Base_Default::initInstance($config);

	$module = loadModule($base);

	$content = ob_get_contents();

	ob_end_clean();

	$response = $module->processResponse($content);
}
catch(Exception $e)
{
	$message = $e->getMessage();
	$trace   = '';

	if($config['debug'] === true)
	{
		$message.= ' in ' . $e->getFile() . ' on line ' . $e->getLine();
		$trace   = $e->getTraceAsString();
	}
	$res = array();
	$res['head']['uiResult'] = 1;
	$res['head']['szRetErrMsg'] = $message;

	$response = json_encode($res);
}

echo $response;

/**
 * 错误检查
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function responseProcess($content)
{
	$lastError = error_get_last();

	if($lastError)
	{
		return $lastError['message'] . ' in ' . $lastError['file'] . ' on line ' . $lastError['line'] . "\n";
	}

	return $content;
}

/**
 * 加载模块
 * @param  Base   $base [description]
 * @return [type]       [description]
 */
function loadModule(Base $base)
{
	$config  = $base->getConfig();
	$default = $config['module_default'];
	$input   = $config['module_input'];
	$length  = $config['module_input_length'];
	$route   = $config['route'];

	if(!empty($input))
	{
		$data_packet = json_decode($input, true);
		$result = intval($data_packet['head']['uiResult']);
		$cmdid = intval($data_packet['head']['uiCmdid']);	
	}
	else
	{
		throw new Exception("Invalid data packet");
	}

	if ($result != 0 || $cmdid < 1000){
		throw new Exception("Invalid argvs");
	}

	$routekey = '0x'.$cmdid;
	$x = $route[$routekey]['class'];

	if (empty($x)){
		throw new Exception('Invalid signs in input');
	}

	return $base->getLoader()->load($x);
}
