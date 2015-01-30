<?php
	//要合并的数据库列表(数据表结构必须全部一致), array(数据库服务器, 端口, 用户名, 密码, 数据库, 名称后缀, 合并服相隔天数)
	$DbList = array(
		array(
			"192.168.1.73",
			3306,
			"root",
			"ybybyb",
			"gamedb_qq_s1",
			".s1",//已合过的不需要
			0,   //天数
			1000000	//主服务的player表基数qq_s77 就填写77* 1000000 已和过的写0
		),
		array(
			"192.168.1.73",
			3306,
			"root",
			"ybybyb",
			"gamedb_qq_s2",
			".s2",
			1,
			2000000 	//主服务的player表基数qq_s93 就填写93* 1000000 已和过的写0
		)
	);
	$DbServer = "192.168.1.73";
	$DbServerPort = "3306";
	$DbUser = "root";
	$DbPwd = "ybybyb";
	$Db = "gamedb_qq_s01"; //目标数据库
	
	$Clear_Day = 60; //多少天未登陆玩家要清除
	$Clear_Level = 120; //小于多少级的要清除
	
	$Is_Need_Redeem = true; //是否要补偿, true:要, false:不要
	$Max_Redeem_Days = 10; //最大补偿隔的天数, 200w铜钱, 400体
	$Redeem_Coins = 200000; //每隔一天补偿的铜钱, 20w
	$Redeem_Power = 40; //每隔一天补偿的体力
	$Redeem_Must_Coins = 2000000; //公共要补偿的铜钱
	$Redeem_Must_Power = 0; //公共要补偿的体力
	$Redeem_Register_Day = 7; //注册多少天以上才有补偿
	
	$IgnoreLog = true; //忽略记录表
	
	$ShowSql = false; //执行过程中是否显示SQL语句
	$CopyNumber = 0; //合并数目, 0为全部.(方便测试用)

?>