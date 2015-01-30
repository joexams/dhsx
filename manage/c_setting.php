<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/setting.php");
include_once(UCTIME_ROOT."/mod/admin.php");
include_once(UCTIME_ROOT."/mod/code.php");
switch (ReqStr('action'))
{
	case 'GameData': webAdmin('game_data');GameData();break;
	case 'SaveGameData': webAdmin('game_data');SaveGameData();break;

	case 'GmBug': webAdmin('c_gm_bug');GmBug();break;
	case 'Bulletin': webAdmin('c_bulletin');Bulletin();break;
	case 'BulletinD': webAdmin('c_bulletin_cf');BulletinD();break;
	case 'Increase': Increase();break;
	case 'UpVip': webAdmin('c_upvip');UpVip();break;
	
	case 'Admin': webAdmin('c_admin');Admin();break;
	case 'AdminPj': webAdmin('admin_pj');AdminPj();break;
	case 'AddAdmin': webAdmin('c_admin');AddAdmin();break;
	case 'EditAdmin': webAdmin('c_admin');EditAdmin();break;
		
	case 'PlayerOut': webAdmin('c_out');PlayerOut();break;
	
	case 'PlayerTest': webAdmin('c_test');PlayerTest();break;
	case 'PlayerStar': webAdmin('star');PlayerStar();break;

	case 'ShopItemAd': webAdmin('s_shop_item_ad'); ShopItemAd(); break;
	case 'SetShopItemAd': webAdmin('s_shop_item_ad'); SetShopItemAd(); break;

	case 'SaveIncrease': SaveIncrease();break;	
	case 'SaveBulletin': webAdmin('c_bulletin');SaveBulletin();break;
	case 'DelBulletin': webAdmin('c_bulletin');DelBulletin();break;
	case 'SetDelBulletin': webAdmin('c_bulletin');SetDelBulletin();break;
	case 'SaveBulletinD': webAdmin('c_bulletin_cf');SaveBulletinD();break;
	case 'DelGmBug': webAdmin('c_gm_bug');DelGmBug();break;
	case 'SaveUpVip': webAdmin('c_upvip');SaveUpVip();break;
	
	case 'SaveAddAdmin': webAdmin('c_admin');SaveAddAdmin();break;
	case 'SaveEditAdmin': webAdmin('c_admin');SaveEditAdmin();break;
	case 'LockAdmin': webAdmin('c_admin');LockAdmin();break;
	
	case 'SetPlayerOut': webAdmin('c_out');SetPlayerOut();break;
	
	case 'SetPlayerTest': webAdmin('c_test');SetPlayerTest();break;
	case 'SetPlayerStar': webAdmin('star');SetPlayerStar();break;
	case 'ReplyGm': webAdmin('c_gm_bug');ReplyGm();break;
}
?> 