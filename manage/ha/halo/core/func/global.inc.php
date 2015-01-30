<?php
/**
 * 模板调用
 * 
 * @param $module
 * @param $template
 * @param $istag
 * @return unknown_type
 */
function template($module, $template, $style = '') {
	if (empty($style) && defined('STYLE')) {
		$style = STYLE;
	} else {
		$style = $style;
	}
	if(!$style) $style = 'default';
	$module = empty($module) ? ROUTE_M : $module;
	if(empty($module)) return false;
	$tplfile = CORE_PATH.'templates'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
	if(!file_exists($tplfile)) {
		// return Lang('template_no_exist');
	}
	
	return $tplfile;
}
/**
 * 输出格式化
 * 
 */ 
function output_json($status, $msg='', $data = null){
	if (!empty($data) && !is_array($data)){
		$data['info'] = $data;
	}
	$data['status'] = $status === 0 ? 0 : $status;
	$data['msg']	= $msg;

	echo to_json($data);
	unset($data);
	exit;
}

/**
 * To JSON conversion
 *
 * @param   mixed  $data
 * @param   bool   wether to make the json pretty
 * @return  string
 */
function to_json($data = null, $pretty = false){
	if ($data == null){
		return ;
	}

	// To allow exporting ArrayAccess objects like Orm\Model instances they need to be
	// converted to an array first
	$data = (is_array($data) or is_object($data)) ? to_array($data) : $data;
	return $pretty ? pretty_json($data) : json_encode($data);
}
/**
 * To array conversion
 *
 * Goes through the input and makes sure everything is either a scalar value or array
 *
 * @param   mixed  $data
 * @return  array
 */
function to_array($data = null){
	if ($data === null)	{
		return array();
	}

	$array = array();

	if (is_object($data)){
		$data = get_object_vars($data);
	}

	if (empty($data)){
		return array();
	}

	foreach ($data as $key => $value){
		if (is_object($value) or is_array($value)){
			$array[$key] = to_array($value);
		}else{
			$array[$key] = $value;
		}
	}

	return $array;
}
/**
 * Makes json pretty the json output.
 * Barrowed from http://www.php.net/manual/en/function.json-encode.php#80339
 *
 * @param   string  $json  json encoded array
 * @return  string|false  pretty json output or false when the input was not valid
 */
function pretty_json($data){
	$json = json_encode($data);
	if ( ! $json){
		return false;
	}

	$tab = "\t";
	$newline = "\n";
	$new_json = "";
	$indent_level = 0;
	$in_string = false;
	$len = strlen($json);

	for ($c = 0; $c < $len; $c++){
		$char = $json[$c];
		switch($char){
			case '{':
			case '[':
				if ( ! $in_string)
				{
					$new_json .= $char.$newline.str_repeat($tab, $indent_level+1);
					$indent_level++;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '}':
			case ']':
				if ( ! $in_string)
				{
					$indent_level--;
					$new_json .= $newline.str_repeat($tab, $indent_level).$char;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ',':
				if ( ! $in_string)
				{
					$new_json .= ','.$newline.str_repeat($tab, $indent_level);
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ':':
				if ( ! $in_string)
				{
					$new_json .= ': ';
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '"':
				if ($c > 0 and $json[$c-1] !== '\\')
				{
					$in_string = ! $in_string;
				}
			default:
				$new_json .= $char;
				break;
		}
	}

	return $new_json;
}

/**
* 语言文件处理
*
* @param	string		$language	标示符
* @param	array		$pars	转义的数组,二维数组 ,'key1'=>'value1','key2'=>'value2',
* @param	string		$modules 多个模块之间用半角逗号隔开，如：member,guestbook
* @return	string		语言字符
*/
function Lang($language = 'no_language',$pars = array(), $modules = '') {
	static $LANG = array();
	static $LANG_MODULES = array();
	static $lang = '';
	$lang = common::load_config('system','lang');
	if(!$LANG) {
		require_once CORE_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'system.lang.php';
		if(file_exists(CORE_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php')) require CORE_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php';
	}
	if(!empty($modules)) {
		$modules = explode(',',$modules);
		foreach($modules AS $m) {
			if(!isset($LANG_MODULES[$m])) require CORE_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$m.'.lang.php';
		}
	}
	if(!array_key_exists($language,$LANG)) {
		return $language;
	} else {
		$language = $LANG[$language];
		if($pars) {
			foreach($pars AS $_k=>$_v) {
				$language = str_replace('{'.$_k.'}',$_v,$language);
			}
		}
		return $language;
	}
}


/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function ext_addslashes($string){
	if(!is_array($string)){
		return addslashes($string);
	}
	foreach($string as $key => $val){
		$string[$key] = ext_addslashes($val);
	}
	return $string;
}
/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function ext_stripslashes($string) {
	if(!is_array($string)){
		return stripslashes($string);
	}
	foreach($string as $key => $val){
		$string[$key] = ext_stripslashes($val);
	}
	return $string;
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	if($data == ''){
		return array();
	}
	eval("\$array = $data;");
	return $array;
}

/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用ext_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if($data == ''){
		return '';
	}
	if($isformdata){
		$data = ext_stripslashes($data);
	}
	return addslashes(var_export($data, TRUE));
}

/**
* 字符串加密、解密函数
*
*
* @param	string	$txt		字符串
* @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
* @param	string	$key		密钥：数字、字母、下划线
* @return	string
*/
function auth($txt, $operation = 'ENCODE', $key = '') {
	$key	= $key ? $key : common::load_config('system', 'auth_key');
	$txt	= $operation == 'ENCODE' ? (string)$txt : base64_decode($txt);
	$len	= strlen($key);
	$code	= '';
	for($i=0; $i<strlen($txt); $i++){
		$k		= $i % $len;
		$code  .= $txt[$i] ^ $key[$k];
	}
	$code = $operation == 'DECODE' ? $code : base64_encode($code);
	return $code;
}
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : random(6);
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
	$strlen = strlen($username);
	if(!preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( 25 < $strlen || $strlen < 3 ) {
		return false;
	}
	return true;
}
/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
/**
* 产生随机字符串
*
* @param    int        $length  输出长度
* @param    int 	   $numeric   可选的
* @return   string     字符串
*/
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}


function convertip($ip) {

	$return = '';

	if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {

		$iparray = explode('.', $ip);

		if($iparray[0] == 10 || $iparray[0] == 127 || ($iparray[0] == 192 && $iparray[1] == 168) || ($iparray[0] == 172 && ($iparray[1] >= 16 && $iparray[1] <= 31))) {
			$return = 'LAN';
		} elseif($iparray[0] > 255 || $iparray[1] > 255 || $iparray[2] > 255 || $iparray[3] > 255) {
			$return = 'Invalid IP Address';
		} else {
			$tinyipfile = CACHE_PATH.'./data/tinyipdata.dat';
			if(@file_exists($tinyipfile)) {
				$return = convertip_tiny($ip, $tinyipfile);
			}
		}
	}

	return $return;

}

function convertip_tiny($ip, $ipdatafile) {

	static $fp = NULL, $offset = array(), $index = NULL;

	$ipdot = explode('.', $ip);
	$ip    = pack('N', ip2long($ip));

	$ipdot[0] = (int)$ipdot[0];
	$ipdot[1] = (int)$ipdot[1];

	if($fp === NULL && $fp = @fopen($ipdatafile, 'rb')) {
		$offset = @unpack('Nlen', @fread($fp, 4));
		$index  = @fread($fp, $offset['len'] - 4);
	} elseif($fp == FALSE) {
		return  'Invalid IP data file';
	}

	$length = $offset['len'] - 1028;
	$start  = @unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);

	for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {

		if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
			$index_offset = @unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
			$index_length = @unpack('Clen', $index{$start + 7});
			break;
		}
	}

	@fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
	if($index_length['len']) {
		return @fread($fp, $index_length['len']);
	} else {
		return 'Unknown';
	}

}


/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	return $string;
}

/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
	if($in_column && is_array($data)) {
		$ids = '\''.implode('\',\'', $data).'\'';
		$sql = "$in_column IN ($ids)";
		return $sql;
	} else {
		if ($front == '') {
			$front = ' AND ';
		}
		if(is_array($data) && count($data) > 0) {
			$sql = '';
			foreach ($data as $key => $val) {
				$sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
			}
			return $sql;
		} else {
			return $data;
		}
	}
}

/**
 * 输出自定义错误
 *
 * @param $errno 错误号
 * @param $errstr 错误描述
 * @param $errfile 报错文件地址
 * @param $errline 错误行号
 * @return string 错误提示
 */
function my_error_handler($errno, $errstr, $errfile, $errline) {
	if($errno==8) return '';
	$errfile = str_replace(ROOT_PATH,'',$errfile);

	 if(common::load_config('system','errorlog')) {
	 	$errstr = str_replace(array("\r\n", "\n", "\r"), '', $errstr);
	 	error_log("<?php exit;?>\t".date('Y-m-d H:i:s',TIME)."\t".$errno."\t".$errstr."\t".$errfile."\t".$errline.PHP_EOL, 3, CACHE_PATH.'log/'.date('Ym').'_error_log.php');
	 } else {
		$str = '<div style="font-size:12px;text-align:left; border-bottom:1px solid #9cc9e0; border-right:1px solid #9cc9e0;padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;">errorno:' . $errno . ',str:' . $errstr . ',file:<font color="blue">' . $errfile . '</font>,line' . $errline .'</div>';
		echo $str;
	 }
}

//文件大小格式转换
function getfilesize($size){
	if($size>=1024*1024)//MB
	{
		$filesize=number_format($size/(1024*1024),2,'.','')." MB";
	}
	elseif($size>=1024)//KB
	{
		$filesize=number_format($size/1024,2,'.','')." KB";
	}
	else
	{
		$filesize=$size." Bytes";
	}
	return $filesize;
}

/**
 * 获取UCenter数据库配置
 */
function get_uc_database() {
	$config = common::load_config('system');
	return  array (
		'hostname' => $config['uc_dbhost'],
		'database' => $config['uc_dbname'],
		'username' => $config['uc_dbuser'],
		'password' => $config['uc_dbpw'],
		'tablepre' => $config['uc_dbtablepre'],
		'charset'  => $config['uc_dbcharset'],
		'type' => 'mysql',
		'debug' => true,
		'pconnect' => 0,
		'autoconnect' => 0
		);
}

/**
 * iconv 编辑转换
 */
if (!function_exists('iconv')) {
	function iconv($in_charset, $out_charset, $str) {
		$in_charset = strtoupper($in_charset);
		$out_charset = strtoupper($out_charset);
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($str, $out_charset, $in_charset);
		} else {
			common::load_func('iconv');
			$in_charset = strtoupper($in_charset);
			$out_charset = strtoupper($out_charset);
			if ($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312')) {
				return utf8_to_gbk($str);
			}
			if (($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8') {
				return gbk_to_utf8($str);
			}
			return $str;
		}
	}
}

function SearchReturnQwm($t){
	return sprintf("%02d%02d",ord($t[0])-160,ord($t[1])-160);
}

//转换字符串
function SearchReturnSaveStr($str){
	//所有汉字后添加ASCII的0字符,此法是为了排除特殊中文拆分错误的问题
	$str=preg_replace("/[\x80-\xff]{2}/","\\0".chr(0x00),$str);
	//拆分的分割符
	$search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`", "^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">", "\r", "\r\n", "$", "&", "%", "#", "@", "+", "=", "{", "}", "[", "]", "：", "）", "（", "．", "。", "，", "！", "；", "“", "”", "‘", "’", "〔", "〕", "、", "—", "　", "《", "》", "－", "…", "【", "】",);
	//替换所有的分割符为空格
	$str = str_replace($search,' ',$str);
	//用正则匹配半角单个字符或者全角单个字符,存入数组$ar
	preg_match_all("/[\x80-\xff]?./",$str,$ar);$ar=$ar[0];
	//去掉$ar中ASCII为0字符的项目
	for($i=0;$i<count($ar);$i++)
	{
		if($ar[$i]!=chr(0x00))
		{
			$ar_new[]=$ar[$i];
		}
	}
	$ar=$ar_new;
	unset($ar_new);
	$oldsw=0;
	//把连续的半角存成一个数组下标,或者全角的每2个字符存成一个数组的下标
	for($ar_str='',$i=0;$i<count($ar);$i++)
	{
		$sw=strlen($ar[$i]);
		if($i>0 and $sw!=$oldsw)
		{
			$ar_str.=" ";
		}
		if($sw==1)
		{
			$ar_str.=$ar[$i];
		}
		else
		{
			if(strlen($ar[$i+1])==2)
			{
				$ar_str.=SearchReturnQwm($ar[$i]).SearchReturnQwm($ar[$i+1]).' ';
			}
			elseif($oldsw==1 or $oldsw==0)
			{
				$ar_str.=SearchReturnQwm($ar[$i]);
			}
		}
		$oldsw=$sw;
	}
	//去掉连续的空格
	$ar_str=trim(preg_replace("# {1,}#i"," ",$ar_str));
	//返回拆分后的结果
	return $ar_str;
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 * @param $timeout 过期时间
 */
function setcache($name, $data, $filepath='', $type='memcache', $config='memcache', $timeout=0) {
	return false;
	common::load_class('cache_factory','',0);
	if($config) {
		$cacheconfig = common::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->set($name, $data, $timeout, '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcache($name, $filepath='', $type='memcache', $config='memcache') {
	return false;
	common::load_class('cache_factory','',0);
	if($config) {
		$cacheconfig = common::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->get($name, '', '', $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 */
function delcache($name, $filepath='', $type='memcache', $config='memcache') {
	common::load_class('cache_factory','',0);
	if($config) {
		$cacheconfig = common::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->delete($name, '', '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcacheinfo($name, $filepath='', $type='file', $config='') {
	common::load_class('cache_factory');
	if($config) {
		$cacheconfig = common::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->cacheinfo($name, '', '', $filepath);
}


/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>''));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	include(template('manage', 'message'));
	exit;
}
?>
