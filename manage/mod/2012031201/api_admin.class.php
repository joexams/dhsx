<?php
require_once('api_base.class.php');

//管理员接口
class api_admin extends api_base {
    const ADMIN_API_MODULE = 99;                 //管理员模块ID
    
    const ADMIN_API_REFRESH_PLAYER = 0;          //刷新玩家数据，会将玩家踢出
    const ADMIN_API_INCREASE_INGOT = 1;          //赠送元宝
    const ADMIN_API_INCREASE_COINS = 2;          //赠送铜钱
    const ADMIN_API_CHARGE = 3;                  //玩家充值
    const ADMIN_API_FIND_PLAYER_BY_USERNAME = 4; //根据玩家名称获取玩家ID
    const ADMIN_API_FIND_PLAYER_BY_NICKNAME = 5; //根据玩家昵称获取玩家ID
    const ADMIN_API_ADD_AFFICHE = 6;             //添加公告
    const ADMIN_API_DELETE_AFFICHE = 7;          //删除公告
    const ADMIN_API_GET_AFFICHE_LIST = 8;        //获取公告列表
    const ADMIN_API_GIVE_ITEM = 9;               //送物品或装备
    const ADMIN_API_COUNT_ONLINE_PLAYER = 10;    //获取在线玩家数量
    const ADMIN_API_GIVE_EXP_TO_ALL = 11;        //给玩家所有角色加经验
	const ADMIN_API_DECREASE_INGOT = 12; 		 //扣除元宝
	const ADMIN_API_INCREASE_PLAYER_FAME = 13;   //增加威望
	const ADMIN_API_GIVE_FATE = 14; 			 //赠送命格
	const ADMIN_API_SET_PLAYER_VIP_LEVEL = 15;	 //设置玩家VIP等级
	const ADMIN_API_DISABLE_PLAYER_LOGIN = 16;
	const ADMIN_API_DISABLE_PLAYER_TALK  = 17;
	const ADMIN_API_INCREASE_PLAYER_POWER = 18;
	const ADMIN_API_SET_TESTER = 19;
    const ADMIN_API_ADD_PLAYER_GIFT_DATA = 20;
    const ADMIN_API_REPLY_PLAYER_BUG = 23;
    const ADMIN_API_OPERATION_PLAYER_BUG = 24;
    const ADMIN_API_GET_USERNAME_BY_NICKNAME = 25;
    const ADMIN_API_GET_TOWN_PLAYER_COUNT = 26;
    const ADMIN_API_GET_ALL_TOWN_PLAYER_COUNT = 27;
	const ADMIN_API_SET_GM = 28;
	const ADMIN_API_SET_NICKNAME = 29;
    const ADMIN_API_SYSTEM_SEND_INGOT = 30;
    const ADMIN_API_GET_NICKNAME_BY_USERNAME = 31;  //根据用户名查找昵称
    const ADMIN_API_GET_PLAYER_LEVEL_RANKING = 34;
    const ADMIN_API_GET_PLAYER_MISSION_RANKING = 35;
    const ADMIN_API_GET_PLAYER_FAME_RANKING = 36;
	const ADMIN_API_GET_WARNING_AFFICHE = 37;
	const ADMIN_API_ADD_WARNING_AFFICHE = 38;
	const ADMIN_API_DELETE_WARNING_AFFICHE = 39;
	const ADMIN_API_GIVE_SOUL = 41; 			    //赠送灵件
    const ADMIN_API_INCREASE_PLAYER_SKILL = 42;     //增加玩家阅历 
    const ADMIN_API_SET_STAR_ACCOUNT = 43;          //设置明星号
    const ADMIN_API_INCREASE_PLAYER_STATE_POINT = 44;//增加境界点
    const ADMIN_API_GIVE_ROLL_COUNT = 45;	//增加博饼次数  


    //强制刷新玩家数据，把在线玩家踢掉
    public static function refresh_player ($player_id) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_REFRESH_PLAYER,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id)
            ),
            array(
                'result' => 'enum'      //1-成功，0-失败
            )
        );
    }

    //增加玩家元宝数量，不增加玩家VIP等级
    public static function increase_player_ingot ($player_id, $ingot) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_INGOT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($ingot)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'new_ingot' => 'int'    //玩家剩余元宝
            )
        );
    }

    //增加玩家铜钱数量
    public static function increase_player_coins ($player_id, $coins) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_COINS,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($coins)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'new_coins' => 'int'    //玩家剩余铜钱
            )
        );
    }

    //玩家充值
    public static function charge ($player_id, $order_id, $ingot) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_CHARGE,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_string($order_id),
                self::pack_int($ingot)
            ),
            array(
                'result'    => 'enum',  //1-成功，0-失败
                'level_up'  => 'enum',  //1-vip升级，0-vip没升级
                'new_level' => 'int',   //玩家充值完后的VIP等级
				'nickname'  => 'string' //玩家昵称
            )
        );
    }

    //根据玩家登录名找玩家ID
    function find_player_by_username ($username) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_FIND_PLAYER_BY_USERNAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($username)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'player_id' => 'int'    //玩家ID
            )
        );
    }

    //根据玩家昵称找玩家ID
    function find_player_by_nickname ($username) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_FIND_PLAYER_BY_NICKNAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($username)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'player_id' => 'int'    //玩家ID
            )
        );
    }
    
    //添加公告
    function add_affiche ($content, $expired_time = 0) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_ADD_AFFICHE,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($content),
				self::pack_int($expired_time)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //删除公告
    function delete_affiche ($affiche_id) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DELETE_AFFICHE,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($affiche_id)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //获取公告列表
    function get_affiche_list () {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_AFFICHE_LIST,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'affiche_list' => array(
                    'id'           => 'int',     //公告ID
                    'content'      => 'string',  //公告内容
					'expired_time' => 'int'      //到期时间
                )
            )
        );
    }
	
	//获取惩罚公告
    function get_warning_affiche () {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_WARNING_AFFICHE,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'content'      => 'string'  //公告内容
            )
        );
    }
	
	//添加惩罚公告
    function add_warning_affiche ($content) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_ADD_WARNING_AFFICHE,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($content)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
	
	//清除惩罚公告
    function delete_warning_affiche () {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DELETE_WARNING_AFFICHE,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //赠送物品
    function give_item ($player_id, $item_id, $number, $item_level = 1) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_ITEM,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($item_id),
                self::pack_int($number),
				self::pack_int($item_level)
            ),
            array(
                'success_number' => 'int',  //实际获得数量
                'failure_number' => 'int'   //未获得的数量
            )
        );
    }
    
    //获取在线玩家数量
    function count_online_player () {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_COUNT_ONLINE_PLAYER,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'count' => 'int',  //在线玩家数量
            )
        );
    }
	
    //给玩家角色加经验($player_role_id == 0的时候给所有角色加经验)，$player_role_id对应player_role表主键
	function give_exp ($player_id, $player_role_id, $exp_value) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_EXP_TO_ALL,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($player_role_id),
                self::pack_int($exp_value)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
	}

    //扣除玩家元宝
    public static function decrease_player_ingot ($player_id, $ingot) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DECREASE_INGOT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($ingot)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'new_ingot' => 'int'    //玩家剩余元宝
            )
        );
    }

    //增加玩家威望
    public static function increase_player_fame ($player_id, $fame) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_PLAYER_FAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($fame)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'fame' => 'int',        //玩家当前威望
                'fame_level' => 'int'   //玩家当前威望等级
            )
        );
    }
	
    //赠送命格
    public static function give_fate ($player_id, $fate_id, $fate_level, $fate_number) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_FATE,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($fate_id),
				self::pack_short($fate_level),
                self::pack_short($fate_number)
            ),
            array(
                'result' => 'int',     //成功赠送个数
            )
        );
    }
	
    //赠送命格
    public static function set_player_vip_level ($player_id, $vip_level) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SET_PLAYER_VIP_LEVEL,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($vip_level)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
	
    //屏蔽玩家登录
    public static function disable_player_login ($player_id, $seconds) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DISABLE_PLAYER_LOGIN,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($seconds)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
	
    //屏蔽玩家发言
    public static function disable_player_talk ($player_id, $seconds) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DISABLE_PLAYER_TALK,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($seconds)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
	
	//增加玩家体力值
	public static function increase_player_power ($player_id, $power) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_PLAYER_POWER,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($power)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'new_power' => 'int'    //当前体力值
            )
        );
    }
	
	//设置测试帐号（$is_tester = 1 是普通测试帐号，$is_tester = 2 是可以跳过战斗动画的测试帐号，$is_tester = 0 非测试帐号）
	public static function set_tester ($player_id, $is_tester) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SET_TESTER,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($is_tester)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //赠送礼包
    //$type = 1 元宝礼包、$type = 2 物品礼包、$type = 3 元宝/铜钱等 + 物品礼包
    //$ingot 元宝数量
	//$coins 铜钱数量
    //$gift_id 对应的礼包物品ID，玩家领取完礼包后，将在背包中看到这个礼包
    //$message 玩家领取礼包时看到的提示信息
    //$item_list = array('item_id' => 'int', 'number' => 'int') 格式的列表
    public static function add_player_gift_data ($player_id, $type, $ingot, $coins, $gift_id, $message, $item_list) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_ADD_PLAYER_GIFT_DATA,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($type),
                self::pack_int($ingot),
				self::pack_int($coins),
                self::pack_int($gift_id),
                self::pack_string($message),
                self::pack_array($item_list, array(
                    'item_id' => 'int',
                    'number'  => 'int'
                ))
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //回复BUG记录
    public static function reply_player_bug ($player_bug_id, $reply_content, $reply_user) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_REPLY_PLAYER_BUG,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_bug_id),
                self::pack_string($reply_content),
                self::pack_string($reply_user)
	    ),
            array(
                'result' => 'short',     //1-成功，2-失败
            )
        );
    }
    
    //操作BUG记录，type = 1 删除，type = 2 屏蔽
    public static function operation_player_bug ($player_bug_id, $type) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_OPERATION_PLAYER_BUG,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_bug_id),
                self::pack_short($type)
            ),
            null
        );
    }
    
    //通过昵称获取帐号
    public static function get_username_by_nickname ($nickname) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_USERNAME_BY_NICKNAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($nickname)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'username' => 'string'  //玩家帐号
            )
        );
    }
	
	//获取城镇玩家数量
	public static function get_town_player_count($town_id) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_TOWN_PLAYER_COUNT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($town_id),
            ),
            array(
                'player_count' => 'int' //玩家数量
            )
        );
	}
	
	//获取所有城镇玩家数量
	public static function get_all_town_player_count() {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_ALL_TOWN_PLAYER_COUNT,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'player_count' => 'int' //玩家数量
            )
        );
	}
	
	//获取总帮派数量
	public static function get_all_faction_count() {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_ALL_FACTION_COUNT,
            array(
                self::pack_string(self::$ADMIN_PWD)
            ),
            array(
                'count' => 'int' //帮派数量
            )
        );
	}
	
	//获取指定级别帮派数量, $type -- 1:仅仅获取指定级别的, 2:获取大于等级指定级别的
	public static function get_level_faction_count($level, $type) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_LEVEL_FACTION_COUNT,
            array(
                self::pack_string(self::$ADMIN_PWD),
				self::pack_int($level),
				self::pack_int($type)
            ),
            array(
                'count' => 'int' //帮派数量
            )
        );
	}
	
	//设置GM号
	public static function set_gm($player_id, $nickname) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SET_GM,
            array(
                self::pack_string(self::$ADMIN_PWD),
				self::pack_int($player_id),
				self::pack_string($nickname)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
	}
	
	//设置昵称
	public static function set_nickname($player_id, $nickname) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SET_NICKNAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
				self::pack_int($player_id),
				self::pack_string($nickname)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
	}

    //系统赠送元宝
    public static function system_send_ingot ($player_id, $ingot) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SYSTEM_SEND_INGOT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($ingot)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
                'new_ingot' => 'int'    //玩家剩余元宝
            )
        );
    }

    //通过用户名找昵称
    public static function get_nickname_by_username($username) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_NICKNAME_BY_USERNAME,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_string($username)
            ),  
            array(
                'result'    => 'enum',     //1-成功，0-失败
                'player_id' => 'int',   //玩家ID
                'nickname'  => 'string'
            )   
        );  
    }   
  
    //获取玩家等级排名
    public static function get_player_level_ranking ($ranking_top) {
        $r = self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_PLAYER_LEVEL_RANKING,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_short($ranking_top)
            ),
            array(
                'player_level_ranking' => array(
                    'player_id'  => 'int',
                    'username'   => 'string',
                    'nickname'   => 'string',
                    'level'      => 'short',
                    'experience' => 'int'
                )
            )
        );
        
        $rtn = array();
        $i = 1;
        foreach ($r['player_level_ranking'] as $value) {
            $item             = $value;
            $item['username'] = $value['username'][1];
            $item['nickname'] = $value['nickname'][1];
            $rtn[$i]          = $item;
            $i++;
        }
        
        return serialize($rtn);
    }
    
    //获取玩家副本排名
    public static function get_player_mission_ranking ($ranking_top) {
        $r = self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_PLAYER_MISSION_RANKING,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_short($ranking_top)
            ),
            array(
                'player_mission_ranking' => array(
                    'player_id'  => 'int',
                    'nickname'   => 'string',
                    'mission_name' => 'string'
                )
            )
        );
        
        $rtn = array();
        $i = 1;
        foreach ($r['player_mission_ranking'] as $value) {
            $item = $value;
            $item['nickname']     = $value['nickname'][1];
            $item['mission_name'] = str_replace(array('(', ')'), array('', ''), $value['mission_name'][1]);
            $rtn[$i]              = $item;
            $i++;
        }
        
        return serialize($rtn);
    }
    
    //获取玩家声望排名
    public static function get_player_fame_ranking ($ranking_top) {
        $r = self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GET_PLAYER_FAME_RANKING,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_short($ranking_top)
            ),
            array(
                'player_fame_ranking' => array(
                    'player_id'  => 'int',
                    'nickname'   => 'string',
                    'fame'       => 'int',
                    'fame_level' => 'short'
                )
            )
        );
        
        $rtn = array();
        $i = 1;
        foreach ($r['player_fame_ranking'] as $value) {
            $item = $value;
            $item['nickname'] = $value['nickname'][1];
            $rtn[$i]          = $item;
            $i++;
        }
        
        return serialize($rtn);
    }
	
	//赠送灵件
    public static function give_soul ($player_id, $soul_id,$attributeid1,$attributevalue1,$attributeid2,$attributevalue2,$attributeid3,$attributevalue3,$key) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_SOUL,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($soul_id),
				self::pack_int($attributeid1),
				self::pack_int($attributevalue1),
				self::pack_int($attributeid2),
				self::pack_int($attributevalue2),
				self::pack_int($attributeid3),
				self::pack_int($attributevalue3),
				self::pack_int($key)
            ),
            array(
				'result' => 'enum'      //1-成功，0-失败
            )
        );
    }
    
    //增加玩家阅历
    public static function increase_player_skill($player_id,$skill){
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_PLAYER_SKILL,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($skill)
            ),
            array(
				'result' => 'enum'      //1-成功，0-失败
            )
        );
    }
    
    //设置明星账号（$is_star = 1 是明星账号 ，$is_star = 0 非明星账号）
	public static function set_star_account ($player_id, $is_star) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SET_STAR_ACCOUNT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($is_star)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
    
    //增加境界点
	public static function increase_player_state_point ($player_id, $state_point) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_INCREASE_PLAYER_STATE_POINT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($state_point)
            ),
            array(
                'result' => 'enum',     //1-成功，0-失败
            )
        );
    }
	
	//赠送博饼次数
    public static function give_roll_count ($player_id, $count) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_ROLL_COUNT,
            array(
                self::pack_string(self::$ADMIN_PWD),
                self::pack_int($player_id),
                self::pack_int($count)
            ),
            array(
				'result' => 'enum'      //1-成功，0-失败
            )
        );
    }	
}

//print_r(api_admin::give_roll_count(504, 1));
//print_r(api_admin::get_town_player_count(16));
//var_export(api_admin::find_player_by_nickname("sx10R"));
?>
