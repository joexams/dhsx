<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/setting.php");
include_once(UCTIME_ROOT."/mod/code.php");
switch (ReqStr('action'))
{
	case 'GameData': webAdmin('game_data');GameData();break;
	case 'SaveGameData': webAdmin('game_data');SaveGameData();break;
	case 'Increase': Increase();break;
	case 'Bulletin': webAdmin('s_bulletin');Bulletin();break;
	case 'UpVip': webAdmin('s_upvip');UpVip();break;
	case 'GmBug': webAdmin('s_gm_bug');GmBug();break;
	case 'PlayerOut': webAdmin('s_out');PlayerOut();break;
	
	case 'PlayerTest': webAdmin('s_test');PlayerTest();break;
	case 'PlayerStar': webAdmin('star');PlayerStar();break;

	case 'ShopItemAd': webAdmin('s_shop_item_ad'); ShopItemAd(); break;
	case 'SetShopItemAd': webAdmin('s_shop_item_ad'); SetShopItemAd(); break;

	case 'SaveIncrease': SaveIncrease();break;
	case 'SaveUpVip': webAdmin('s_upvip');SaveUpVip();break;
	case 'SaveBulletin': webAdmin('s_bulletin');SaveBulletin();break;
	case 'DelBulletin': webAdmin('s_bulletin');DelBulletin();break;
	case 'SetDelBulletin': webAdmin('s_bulletin');SetDelBulletin();break;
	case 'DelGmBug': webAdmin('s_gm_bug');DelGmBug();break;
	case 'SetPlayerOut': webAdmin('s_out');SetPlayerOut();break;

	case 'SetPlayerTest': webAdmin('s_test');SetPlayerTest();break;
	case 'SetPlayerStar': webAdmin('star');SetPlayerStar();break;
	case 'SetGiftSetting': //webAdmin('s_gift_setting');
		SetGiftSetting(); break;

	case 'ReplyGm': webAdmin('s_gm_bug');ReplyGm();break;

	case 'GiftSetting': //webAdmin('s_gift_setting'); 
		GiftSetting(); break;
	default:  Increase();

}
?> 