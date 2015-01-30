<?php
abstract class Module_DefaultAbstract extends ModuleAbstract
{
	public function getDependencies()
	{
		return new Dependency_Default();
	}
	/**
	 * 校验openid
	 * @param  [type]  $openid [description]
	 * @return boolean         [description]
	 */
	protected static function isOpenId($openid)
	{
		return (0 == preg_match('/^[0-9a-fA-F]{32}$/', $openid)) ? false : true;
	}
	/**
	 * 获取参数
	 * @return [type] [description]
	 */
	protected function getRequestHeaderParams()
	{
		$params = array();
		$params['uiPacketLen']    = null;
		$params['uiCmdid']        = null;
		$params['uiSeqid']        = null;
		$params['szServiceName']  = null;
		$params['uiSendTime']     = null;
		$params['uiVersion']      = null;
		$params['ucAuthenticate'] = null;
		$params['uiResult']       = null;
		$params['szRetErrMsg']    = null;

		$params['rspCmdid']		  = null;
		$params['rspBody']		  = null;

		$reqBody= null;

		$config = $this->config;
		if (!empty($config['module_input'])){
			$input = json_decode($config['module_input'], true);
			if (isset($input['head']['uiPacketLen'])){
				$params['uiPacketLen']    = $input['head']['uiPacketLen'];
			}
			if (isset($input['head']['uiCmdid'])){
				$params['uiCmdid']        = $input['head']['uiCmdid'];

				$params['rspCmdid'] = $config['route']['0x'.$params['uiCmdid']]['cmdid'];
				$params['rspBody']  = $config['route']['0x'.$params['uiCmdid']]['body'];

				$reqBody  = $config['route']['0x'.$params['uiCmdid']]['reqbody'];
			}
			if (isset($input['head']['uiSeqid'])){
				$params['uiSeqid']        = $input['head']['uiSeqid'];
			}
			if (isset($input['head']['szServiceName'])){
				$params['szServiceName']  = $input['head']['szServiceName'];
			}
			if (isset($input['head']['uiSendTime'])){
				$params['uiSendTime']     = $input['head']['uiSendTime'];
			}
			if (isset($input['head']['uiVersion'])){
				$params['uiVersion']      = $input['head']['uiVersion'];
			}
			if (isset($input['head']['ucAuthenticate'])){
				$params['ucAuthenticate'] = $input['head']['ucAuthenticate'];
			}
			if (isset($input['head']['uiResult'])){
				$params['uiResult']       = $input['head']['uiResult'];
			}
			if (isset($input['head']['szRetErrMsg'])){
				$params['szRetErrMsg']    = $input['head']['szRetErrMsg'];
			}

			if (isset($input['body']) && !is_null($reqBody) && is_array($input['body']) && !empty($input['body'])){
				$reqBody = explode(',', $reqBody);
				foreach ($input['body'] as $key => $value) {
					if (in_array($key, $reqBody)){
						$params[$key] = $value;
					}
				}
			}
		}

		return $params;
	}
	/**
	 * GZIP处理
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function processResponse($content)
	{
		if(empty($content))
		{
			$acceptEncoding = Base::getRequestHeader('Accept-Encoding');

			if($this->config['gzip'] === true && strpos($acceptEncoding, 'gzip') !== false)
			{
				header('Content-Encoding: gzip');

				$content = gzencode($content, 9);
			}

			return $content;
		}
		else
		{
			return $content;
		}
	}

	/**
	 * 设置参数
	 * @param Data_RecordInterface $record     [description]
	 * @param [type]               $writerType [description]
	 * @param integer              $code       [description]
	 */
	protected function setResponse($body, $errmsg = null, $result_code = 0)
	{
		$response = array();
		$response['head']['uiPacketLen']    = null;
		$response['head']['uiCmdid']        = null;
		$response['head']['uiSeqid']        = null;
		$response['head']['szServiceName']  = null;
		$response['head']['uiSendTime']     = null;
		$response['head']['uiVersion']      = null;
		$response['head']['ucAuthenticate'] = null;
		$response['head']['uiResult']       = null;
		$response['head']['szRetErrMsg']    = null;

		$reqheader = $this->getRequestHeaderParams();
		$response['head']['uiCmdid']        = $reqheader['rspCmdid'];
		$response['head']['uiSeqid']        = $reqheader['uiSeqid'];
		$response['head']['szServiceName']  = $reqheader['szServiceName'];
		$response['head']['uiSendTime']     = strtotime(date('Y-m-d'));
		$response['head']['uiVersion']      = $reqheader['uiVersion'];
		$response['head']['ucAuthenticate'] = $reqheader['ucAuthenticate'];
		$response['head']['szRetErrMsg']    = $errmsg;

		$rspBody = $reqheader['rspBody'];
		if (!empty($body) && is_array($body) && !empty($rspBody)){
			$rspBody = explode(',', $rspBody);
			foreach ($rspBody as $key) {
				if (array_key_exists($key, $body)){
					$response['body'][$key] = $body[$key];
				}else {
					$response['body'][$key] = null;
				}
			}
		}else {
			$result_code = 1;
		}

		$response['head']['uiResult']       = $result_code;

		return $response;
	}

	public static function load_api_class($classname, $version = '' ,$initialize = 0) {
		if (empty($version)) return false;
		return self::_load_class($classname, 'Gameapi'.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR, $initialize);
	}

	private static function _load_class($classname, $path = '', $initialize = 1) {
		static $classes = array();
		if (empty($path)) return false;
		$key = md5($path.$classname);
		
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(PATH_LIBRARY.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
			include PATH_LIBRARY.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$classname.'.class.php';
			$name = $classname;
			if ($initialize) {
				$classes[$key] = new $name;
			} else {
				$classes[$key] = true;
			}
			return $classes[$key];
		} else {
			return false;
		}
	}
}

