<?php
class api_base {
    static $SERVER = '192.168.1.73'; 		//数据操作接口地址
    static $PORT   = 9527;               			//数据操作接口端口
    static $ADMIN_PWD = 'ybybyb';					//数据操作接口密码
	public $SocketConnection;		
    public $Connection;
	
	//------------------数据打包二进制 开始----------------------
	//----short---
    public static function pack_short ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 4 - strlen($str)).$str;
        return pack("H*", $str);   
    } 
	
    public static function unpack_short ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }
	
	//----int---
    public static function pack_int ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 8 - strlen($str)).$str;
        return pack("H*", $str);   
    }
	
    public static function unpack_int ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

	//----string-----
	public static function pack_string ($str) {
        $str_len = strlen($str);
        return self::pack_int($str_len) . pack("a*", $str);
    }
	
    public static function unpack_string($bytes) {
        return unpack("a*", $bytes)[1];
    }
	
	//----float-----
	public static function pack_float ($float_str) {
        $str_len = strlen($float_str);
        return self::pack_int($str_len) . pack("a*", $float_str);
    }
	
    public static function unpack_float($bytes) {
        return unpack("a*", $bytes)[1];
    }

	//----long-- 长整数参数必须是字符串类型---
	public static function pack_long($number) {

		$high_high 	= gmp_div_q(gmp_and(gmp_strval($number), "0xffff000000000000"),	"0x1000000000000"); //前1~2个字节
		$high_low 	= gmp_div_q(gmp_and(gmp_strval($number), 	"0xffff00000000"),		"0x100000000"); //前3~4个字节
		$low_high 	= gmp_div_q(gmp_and(gmp_strval($number), 		"0xffff0000"),			"0x10000"); //前5~6个字节
		$low_low 	= gmp_and(gmp_strval($number), 						"0xffff"); 						//前7~8个字节
		$Long1 = gmp_strval($high_high)."\n";
		$Long2 = gmp_strval($high_low)."\n";
		$Long3 = gmp_strval($low_high)."\n";
		$Long4 = gmp_strval($low_low)."\n";
		
		return pack('nnnn', $Long1, $Long2, $Long3, $Long4);
	}
	
	public static function unpack_long($bytes) {
        $LongArray[] = unpack("n*", $bytes);
		var_dump($LongArray);
		$v1 = gmp_mul(gmp_init($LongArray[0][1]),"0x1000000000000");
		$v2 = gmp_mul(gmp_init($LongArray[0][2]),	"0x100000000");
		$v3 = gmp_mul(gmp_init($LongArray[0][3]),		"0x10000");
		$v4 = gmp_init($LongArray[0][4]);
		$value = gmp_strval(gmp_add(gmp_add($v1,$v2),gmp_add($v3,$v4)));
		return $value;
    }
	
	//------------------数据打包二进制 结束----------------------
	
	
    public function __construct(){
        $this->socketConnection = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
		$this->Connection = socket_connect($this->socketConnection, self::$SERVER, self::$PORT);
    }
	
    public function sendMsg($msg){
        socket_write($this->socketConnection, $msg);
    }
	
	public function recvMsg(){
		socket_recv($this->socketConnection, $result_head, 4, MSG_WAITALL);
		$len = self::unpack_int($result_head);
		socket_recv($this->socketConnection, $result_mod, 4, MSG_WAITALL);
		$mod = self::unpack_int($result_mod);
		socket_recv($this->socketConnection, $result_api, 4, MSG_WAITALL);
		$api = self::unpack_int($result_api);
		socket_recv($this->socketConnection, $result, 4, MSG_WAITALL);
		$is_success = self::unpack_int($result);
		socket_recv($this->socketConnection, $player_id, 8, MSG_WAITALL);
		$long = self::unpack_long($player_id);
		//print_r($long);
		echo "data len:".$len."mod :".$mod."api :".$api."is_success :".$is_success."playerid :".$long;
	}
	
	//-------------------------------------------------
	//  module 模块id
	//  action api
	//  params 数组参数
	//  result_format 返回值的格式如果没有写null
	//-------------------------------------------------
    public static function invoke_api ($module, $action, $params, $result_format) {
		$socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp')); 
        socket_connect($socket, self::$SERVER, self::$PORT);
        $packet      = self::pack_int($module).self::pack_int($action).implode($params);
        $packet_len  = strlen($packet);
        $packet_head = self::pack_int($packet_len);
        $message     = $packet_head.$packet;
        $message_len = strlen($message);
		socket_write($socket, $message);
        if ($result_format) {
            $result_head = '';
            $recv_len    = socket_recv($socket, $result_head, 4, MSG_WAITALL);
            
            if ($recv_len == 4) {
                $result_len = self::unpack_int($result_head);
                $result     = '';
                $recv_len   = socket_recv($socket, $result, $result_len, MSG_WAITALL);
                
                if ($recv_len == $result_len) {
                    $parse_cursor = 8; //ignore module id and action id
                    $result_pack = self::parse_result($result, $result_format, $recv_len, $parse_cursor);
                    
                    socket_close($socket);
                
                    return $result_pack;
                }
            }
        }
        socket_close($socket);
    }
	
    public static function parse_result ($result, $result_format, $recv_len, &$parse_cursor) {
        $result_pack = array();
        $i = 0;
        $keys = array_keys($result_format);
        
        while ($i < count($result_format)) {
            $key = $keys[$i];
            
            switch ($result_format[$key]) {
                case 'short':
                    $bytes = substr($result, $parse_cursor, 2);
                    $parse_cursor += 2;
                    $result_pack[$key] = self::unpack_short($bytes);
                    break;
                case 'int':
                    $bytes = substr($result, $parse_cursor, 4);
                    $parse_cursor += 4;
                    $result_pack[$key] = self::unpack_int($bytes);
                    break;
                case 'long':
                    $bytes = substr($result, $parse_cursor, 8);
                    $parse_cursor += 8;
                    $result_pack[$key] = self::unpack_long($bytes);
                    break;
                case 'float':
                    $bytes = substr($result, $parse_cursor, 4);
                    $parse_cursor += 4;
                    $str_len = self::unpack_int($bytes);
                    if ($str_len > 0) {
                        $bytes = substr($result, $parse_cursor, $str_len);
                        $parse_cursor += $str_len;
                        $result_pack[$key] = self::unpack_float($bytes);
                    } else {
                        $result_pack[$key] = '';
                    }
                    break;
                case 'string':
                    $bytes = substr($result, $parse_cursor, 4);
                    $parse_cursor += 4;
                    $str_len = self::unpack_int($bytes);
                    if ($str_len > 0) {
                        $bytes = substr($result, $parse_cursor, $str_len);
                        $parse_cursor += $str_len;
                        $result_pack[$key] = self::unpack_string($bytes);
                    } else {
                        $result_pack[$key] = '';
                    }
                    break;
                default:
                    if (is_array($result_format[$key])) {
                        $bytes = substr($result, $parse_cursor, 4);
                        $parse_cursor += 4;
                        $array_len = self::unpack_int($bytes);
                        $array = array();
                        for ($x = 0; $x < $array_len; $x ++) {
                            $array[] = self::parse_result($result, $result_format[$key], $recv_len, $parse_cursor);
                        }
                        $result_pack[$key] = $array;
                    } else {
                        throw new Exception("Unknow result type: ".$result_format[$key]);
                    }
            }
            
            $i += 1;
        }
        
        return $result_pack;
    }
}
?>