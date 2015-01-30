<?php
	/************** 清档工具配置文件 ******************/
	
	$DbServer = "localhost";
	$DbServerPort = "3306";
	$DbUser = "root";
	$DbPwd = "ybybyb";
	$Db = "gamedb"; //目标数据库
	
	/* 以下清档条件为 AND 的关系 */
	$Clear_Day = 10; //多少天以上未登陆玩家要清除
	$Clear_Level = 20; //小于多少级的要清除
	$Clear_VIP_Level = 0; //小于等于VIP级别的要清除
?>