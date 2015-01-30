<?php

class api
{
    private $socket;

    public function __construct($ip, $port) {
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        
        socket_connect($socket, $ip, $port);
        
        $this->socket = $socket;
    }
    
    public function __destruct() {
        $this->dispose();
    }

    public function __get($name) {
        return new api_module(self::$spec[$name]);
    }
    
    public function dispose() {
        socket_close($this->socket);
    }
    
    private static $spec = array(
        'item' => array(
            'buy' => array(
                
            )
        )
    );
}

class api_module
{
    private $spec;
    
    public function __construct($module_spec) {
        $this->spec = $module_spec;
    }

    public function __call($name, $params) {
        echo "\$api->item->buy()";
    }
    
    function invoke_api ($module, $action, $params, $result_format) {
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        
        socket_connect($socket, self::$SERVER, self::$PORT);
        
        $packet      = self::pack_action($module, $action) . implode($params);
        $packet_len  = strlen($packet);
        $packet_head = self::pack_int($packet_len);
        $message     = $packet_head.$packet;
        $message_len = strlen($message);

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
                    $result_pack = self::unpack_result($result, $result_format, $recv_len, $parse_cursor);
                    
                    socket_close($socket);
                
                    return $result_pack;
                }
            }
        }
        
        return null;
    }
    
    private static function pack_action ($module, $action) {
        return pack("cc", $module, $action);
    }

    private static function pack_short ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 4 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    private static function pack_int ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 8 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    private static function pack_string ($str) {
        $str_len = strlen($str);
        return self::pack_short($str_len) . pack("a*", $str);
    }
    
    private static function pack_array ($arr, $fmt) {
        $data = self::pack_short(count($arr));
        
        for ($j = 0; $j < count($arr); $j ++) {
            $data .= self::pack_object($arr[$j][$key], $fmt);
        }
        
        return $data;
    }
    
    private static function pack_object ($obj, $fmt) {
        $data = '';
        $keys = array_keys($fmt);
        
        for ($i = 0; $i < count($fmt); $i ++) {
            $key = $keys[$i];
            
            switch ($fmt[$key]) {
                case 'enum':
                case 'short':
                    $data .= self::pack_short($obj);
                    break;
                case 'int':
                    $data .= self::pack_int($obj);
                    break;
                case 'string':
                    $data .= self::pack_string($obj);
                    break;
                default:
                    if (is_array($fmt[$key])) {
                        $data .= self::pack_array($obj, $fmt[$key]);
                    } else {
                        throw new Exception("Unknow type: ".$fmt[$key]);
                    }
            }
        }
        
        return $data;
    }

    private static function unpack_enum ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    private static function unpack_short ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    private static function unpack_int ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    private static function unpack_string($bytes) {
        return unpack("a*", $bytes);
    }
    
    private function unpack_result ($result, $result_format, $recv_len, &$parse_cursor) {
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
                            $array[] = self::unpack_result($result, $result_format[$key], $recv_len, $parse_cursor);
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

$api = new api();

$api->item->buy();

$api->dispose();

/*
    static $SERVER = '192.168.1.120'; 			//数据操作接口地址
    static $PORT   = 8888;               	//数据操作接口端口
    static $ADMIN_PWD = 'ybybyb';		//数据操作接口密码

    function pack_action ($module, $action) {
        return pack("cc", $module, $action);
    }

    function pack_short ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 4 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    function pack_int ($num) {
        $str = dechex($num);
        $str = str_repeat('0', 8 - strlen($str)).$str;
        return pack("H*", $str);   
    } 

    function pack_string ($str) {
        $str_len = strlen($str);
        return pack_short($str_len) . pack("a*", $str);
    }
    
    function pack_array ($arr, $fmt) {
        $data = pack_short(count($arr));
        
        for ($j = 0; $j < count($arr); $j ++) {
            $keys = array_keys($fmt);
            
            for ($i = 0; $i < count($fmt); $i ++) {
                $key = $keys[$i];
                
                switch ($fmt[$key]) {
                    case 'enum':
                    case 'short':
                        $data .= pack_short($arr[$j][$key]);
                        break;
                    case 'int':
                        $data .= pack_int($arr[$j][$key]);
                        break;
                    case 'string':
                        $data .= pack_string($arr[$j][$key]);
                        break;
                    default:
                        if (is_array($fmt[$key])) {
                            $data .= pack_array($arr[$j][$key], $fmt[$key]);
                        } else {
                            throw new Exception("Unknow type: ".$fmt[$key]);
                        }
                }
            }
        }
        
        return $data;
    }

    function unpack_enum ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    function unpack_short ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    function unpack_int ($bytes) {
        $arr = unpack("H*", $bytes);
        return hexdec($arr[1]);
    }

    function unpack_string($bytes) {
        return unpack("a*", $bytes);
    }

    function invoke_api ($module, $action, $params, $result_format) {
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        
        socket_connect($socket, $SERVER, $PORT);
        
        $packet      = pack_action($module, $action) . implode($params);
        $packet_len  = strlen($packet);
        $packet_head = pack_int($packet_len);
        $message     = $packet_head.$packet;
        $message_len = strlen($message);

        socket_write($socket, $message, $message_len);
        
        if ($result_format) {
            $result_head = '';
            $recv_len    = socket_recv($socket, $result_head, 4, MSG_WAITALL);
            
            if ($recv_len == 4) {
                $result_len = unpack_int($result_head);
                $result     = '';
                $recv_len   = socket_recv($socket, $result, $result_len, MSG_WAITALL);
                
                if ($recv_len == $result_len) {
                    $parse_cursor = 2; //ignore module id and action id
                    $result_pack = parse_result($result, $result_format, $recv_len, $parse_cursor);
                    
                    socket_close($socket);
                
                    return $result_pack;
                }
            }
        }
        
        socket_close($socket);
        
        return null;
    }
    
    function parse_result ($result, $result_format, $recv_len, &$parse_cursor) {
        $result_pack = array();
        $i = 0;
        $keys = array_keys($result_format);
        
        while ($i < count($result_format)) {
            $key = $keys[$i];
            
            switch ($result_format[$key]) {
                case 'enum':
                    $bytes = substr($result, $parse_cursor, 1);
                    $parse_cursor += 1;
                    $result_pack[$key] = unpack_enum($bytes);
                    break;
                case 'short':
                    $bytes = substr($result, $parse_cursor, 2);
                    $parse_cursor += 2;
                    $result_pack[$key] = unpack_short($bytes);
                    break;
                case 'int':
                    $bytes = substr($result, $parse_cursor, 4);
                    $parse_cursor += 4;
                    $result_pack[$key] = unpack_int($bytes);
                    break;
                case 'string':
                    $bytes = substr($result, $parse_cursor, 2);
                    $parse_cursor += 2;
                    $str_len = unpack_short($bytes);
                    if ($str_len > 0) {
                        $bytes = substr($result, $parse_cursor, $str_len);
                        $parse_cursor += $str_len;
                        $result_pack[$key] = unpack_string($bytes);
                    } else {
                        $result_pack[$key] = '';
                    }
                    break;
                default:
                    if (is_array($result_format[$key])) {
                        $bytes = substr($result, $parse_cursor, 2);
                        $parse_cursor += 2;
                        $array_len = unpack_short($bytes);
                        $array = array();
                        for ($x = 0; $x < $array_len; $x ++) {
                            $array[] = parse_result($result, $result_format[$key], $recv_len, $parse_cursor);
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
    
    
    
    public static function add_player_gift_data ($player_id, $type, $ingot, $gift_id, $message, $item_list) {
        return invoke_api(ADMIN_API_MODULE, ADMIN_API_ADD_PLAYER_GIFT_DATA,
            array(
                'player_id' => $player_id,
                'type'      => $type,
                'ingot'     => $ingot,
                'gift_id'   => $gift_id,
                'message'   => $message,
                'item_list' => $item_list
            ),
            array(
                'in'  => array(
                    'player_id' => 'int',
                    'type'      => 'int',
                    'ingot'     => 'int',
                    'gift_id'   => 'int',
                    'message'   => 'string',
                    'item_list'  => array(
                        'item_id' => 'int',
                        'number'  => 'int'
                    )
                ),
                'out' => array(
                    'result' => 'enum',     //1-成功，0-失败
                )
            )
        );
    }
    
    
    api::admin::add_player_gift_data($ctx, array(
        'player_id' => $player_id,
        'type'      => $type,
        'ingot'     => $ingot,
        'gift_id'   => $gift_id,
        'message'   => $message,
        'item_list' => $item_list
    ));
    
    $add_player_gift_data = array(
        'module_id'  => 1,
        'action_id'  => 123,
        
        'in'  => array(
            'player_id' => 'int',
            'type'      => 'int',
            'ingot'     => 'int',
            'gift_id'   => 'int',
            'message'   => 'string',
            'item_list'  => array(
                'item_id' => 'int',
                'number'  => 'int'
            )
        ),
        
        'out' => array(
            'result' => 'enum',     //1-成功，0-失败
        )
    );
    
class api {
    private static $proto_spec = array(
        'item' => array(
        
        )
    );

    public function __construct($format) {
    }

    public function __get($name) {
        return 
    }
    
    public function __invoke($context, $params) {
    }
}
*/
?>