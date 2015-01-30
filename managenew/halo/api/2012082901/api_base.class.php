<?php
class api_base {
    static $SERVER = '192.168.1.120'; 			//数据操作接口地址
    static $PORT   = 8011;               	//数据操作接口端口
    static $ADMIN_PWD = 'ybybyb';		//数据操作接口密码

    public static function pack_action ($module, $action) {
        return pack("cc", $module, $action);
    }

    public static function pack_short ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 4 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    public static function pack_int ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 8 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    public static function pack_string ($str) {
        $str_len = strlen($str);
        return self::pack_short($str_len) . pack("a*", $str);
    }
    
    public static function pack_array ($arr, $fmt) {
        $data = self::pack_short(count($arr));
        
        for ($j = 0; $j < count($arr); $j ++) {
            $keys = array_keys($fmt);
            
            for ($i = 0; $i < count($fmt); $i ++) {
                $key = $keys[$i];
                
                switch ($fmt[$key]) {
                    case 'enum':
                    case 'short':
                        $data .= self::pack_short($arr[$j][$key]);
                        break;
                    case 'int':
                        $data .= self::pack_int($arr[$j][$key]);
                        break;
                    case 'string':
                        $data .= self::pack_string($arr[$j][$key]);
                        break;
                    default:
                        if (is_array($fmt[$key])) {
                            $data .= self::pack_array($arr[$j][$key], $fmt[$key]);
                        } else {
                            throw new Exception("Unknow type: ".$fmt[$key]);
                        }
                }
            }
        }
        
        return $data;
    }

    public static function unpack_enum ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    public static function unpack_short ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    public static function unpack_int ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    public static function unpack_string($bytes) {
        return unpack("a*", $bytes);
    }

    public static function invoke_api($module, $action, $params, $result_format) {
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        
        socket_connect($socket, self::$SERVER, self::$PORT);
        
        $packet      = self::pack_action($module, $action) . implode($params);
        $packet_len  = strlen($packet);
        $packet_head = self::pack_int($packet_len);
        $message     = $packet_head.$packet;
        $message_len = strlen($message);

        $tgw = "GET / HTTP/1.1\r\nHost: " . self::$SERVER . ':' . self::$PORT . "\r\n\r\n";
        socket_write($socket, $tgw, strlen($tgw));
	   socket_recv($socket, $result_head, 1, MSG_WAITALL);
        socket_write($socket, $message, $message_len);
        
        if ($result_format) {
            $result_head = '';
            $recv_len    = socket_recv($socket, $result_head, 4, MSG_WAITALL);
            
            if ($recv_len == 4) {
                $result_len = self::unpack_int($result_head);
                $result     = '';
                $recv_len   = socket_recv($socket, $result, $result_len, MSG_WAITALL);
                
                if ($recv_len == $result_len) {
                    $parse_cursor = 2; //ignore module id and action id
                    $result_pack = self::parse_result($result, $result_format, $recv_len, $parse_cursor);
                    
                    socket_close($socket);
                
                    return $result_pack;
                }
            }
        }
        
        socket_close($socket);
        
        return null;
    }
    
    public static function parse_result ($result, $result_format, $recv_len, &$parse_cursor) {
        $result_pack = array();
        $i = 0;
        $keys = array_keys($result_format);
        
        while ($i < count($result_format)) {
            $key = $keys[$i];
            
            switch ($result_format[$key]) {
                case 'enum':
                    $bytes = substr($result, $parse_cursor, 1);
                    $parse_cursor += 1;
                    $result_pack[$key] = self::unpack_enum($bytes);
                    break;
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
                case 'string':
                    $bytes = substr($result, $parse_cursor, 2);
                    $parse_cursor += 2;
                    $str_len = self::unpack_short($bytes);
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
                        $bytes = substr($result, $parse_cursor, 2);
                        $parse_cursor += 2;
                        $array_len = self::unpack_short($bytes);
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
