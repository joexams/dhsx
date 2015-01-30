<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/gift.php");
include_once(UCTIME_ROOT."/mod/giftt.php");

switch (ReqStr('action'))
{
	//case 'GiftDataCopy': webAdmin('gift_set');GiftDataCopy();break;
	case 'GiftDataAdd': webAdmin('gift_t');GiftDataAdd();break;
	case 'GiftDataEdit': webAdmin('gift_t');GiftDataEdit();break;	
	case 'GiftData': webAdmin('gift_t');GiftData();break;
	case 'DelGiftData': webAdmin('gift_t');DelGiftData();break;
	case 'SaveGiftDataAdd': webAdmin('gift_t');SaveGiftDataAdd();break;
	case 'SaveGiftDataEdit': webAdmin('gift_t');SaveGiftDataEdit();break;
	case 'SetGiftDataItem': webAdmin('gift_t');SetGiftDataItem();break;
	case 'SetGiftDataGold': webAdmin('gift_t');SetGiftDataGold();break;
	
	
	case 'GiftDataServersAdd': webAdmin('gift_set');GiftDataServersAdd();break;
	case 'SaveGiftDataServersAdd': webAdmin('gift_set');SaveGiftDataServersAdd();break;
	case 'GiftDataServersEdit': webAdmin('gift_set');GiftDataServersEdit();break;
	case 'SaveGiftDataServersEdit': webAdmin('gift_set');SaveGiftDataServersEdit();break;
	case 'DelGiftDataServers': webAdmin('gift_set');DelGiftDataServers();break;
	
	default: webAdmin('gift');GiftDataServers();
}
?> 