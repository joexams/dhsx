<?php
require_once('api_base.class.php');

//管理员接口
class api_admin extends api_base {
	const PLAYER_API_MODULE = 0;          		//玩家数据模块
	const PLAYER_API_MODULE_CHAT = 1;          	//玩家数据模块-聊天
    const ADMIN_API_MODULE = 999;         		//管理员模块ID
	const ADMIN_API_CHANGE_NICKNAME = 0;        //管理员模块改名
	const ADMIN_API_GIVE_ROLE = 1;        		//管理员模块给予伙伴
	const ADMIN_API_SYSTEM_SAY = 2;        		//管理员公告
	const ADMIN_API_GIVE_ITEM = 3;              //管理员给物品
	const ADMIN_API_DELETE_ROLE = 4;            //删除伙伴
	
    //公告
    public static function system_say(
	        $System_name 	//发言人昵称
			,$Msg			//信息
			,$Year			//年
			,$Month			//月
			,$Day			//日
			,$Hour			//小时
			,$Minute		//分钟
			,$Second 		//秒
			,$Times 		//次数
		) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_SYSTEM_SAY,
            array(
				self::pack_string(self::$ADMIN_PWD)
                ,self::pack_string($System_name)
				,self::pack_string($Msg)
				,self::pack_int($Year)
				,self::pack_int($Month)
				,self::pack_int($Day)
				,self::pack_int($Hour)
				,self::pack_int($Minute)
				,self::pack_int($Second)
				,self::pack_int($Times)
            ),
            array(
				'result' => 'int'      		//信息
            )
        );
    }
	
    //改名
    public static function give_role($PlayerId,$RoleId) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_ROLE,
            array(
				self::pack_string(self::$ADMIN_PWD)
                ,self::pack_long($PlayerId)
				,self::pack_int($RoleId)
            ),
            array(
				'result' => 'int'      		//信息
            )
        );
    }
	
    //改名
    public static function change_nickname($PlayerId,$Nickname) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_CHANGE_NICKNAME,
            array(
				self::pack_string(self::$ADMIN_PWD)
                ,self::pack_long($PlayerId)
				,self::pack_string($Nickname)
            ),
            array(
				'result' => 'int'      		//信息
            )
        );
    }
  
    //强制刷新玩家数据，把在线玩家踢掉
    public static function chat($msg) {
        return self::invoke_api(
            self::PLAYER_API_MODULE,
            self::PLAYER_API_MODULE_CHAT,
            array(
                self::pack_string($msg)
            ),
            array(
				'name' => 'string',      //信息
                'chat' => 'string'      //信息
            )
        );
    }
	
    //强制刷新玩家数据，把在线玩家踢掉
    public static function test($a,$b) {
        return self::invoke_api(
            self::PLAYER_API_MODULE,
            2,
            array(
                self::pack_float($a),
				self::pack_float($b)
            ),
            array(
				array(
					'id' => 'int',      //信息
					'name' => 'string',      //信息
					'sex' => 'int',      //信息
					'job' => 'int',      //信息
					'x' => 'int',      //信息
					'y' => 'int',      //信息
					'z' => 'int'      //信息
				),
                'x' => 'float'      //信息
            )
        );
    }
	
	//给物品
    public static function give_item($PlayerId, $ItemId, $ItemNum) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_GIVE_ITEM,
            array(
				self::pack_string(self::$ADMIN_PWD)
                ,self::pack_long($PlayerId)
                ,self::pack_int($ItemId)
				,self::pack_int($ItemNum)
            ),
            array(
				'result' => 'int'      		//信息
            )
        );
    }
    
    //删除伙伴
    public static function delete_role($PlayerId, $RoleId) {
        return self::invoke_api(
            self::ADMIN_API_MODULE,
            self::ADMIN_API_DELETE_ROLE,
            array(
				self::pack_string(self::$ADMIN_PWD)
                ,self::pack_long($PlayerId)
                ,self::pack_int($RoleId)
            ),
            array(
				'result' => 'int'      		//信息
            )
        );
    }
}


//-------------------------测试代码---------------------

//print_r(api_admin::system_say("GM","gm 无敌天下",2014,12,3,10,22,0,3));
//print_r(api_admin::delete_role("10000002","114"));
//print_r(api_admin::chat("hello~!"));
//print_r(api_admin::test(1.2,5.0));

?>
