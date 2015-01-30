<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/gift.php");
switch (ReqStr('action'))
{

	case 'GiftDataServersAdd': webAdmin('gift_set');GiftDataServersAdd();break;
	case 'SaveGiftDataServersAdd': webAdmin('gift_set');SaveGiftDataServersAdd();break;
	case 'GiftDataServersEdit': webAdmin('gift_set');GiftDataServersEdit();break;
	case 'SaveGiftDataServersEdit': webAdmin('gift_set');SaveGiftDataServersEdit();break;
	case 'DelGiftDataServers': webAdmin('gift_set');DelGiftDataServers();break;

	default: webAdmin('gift');GiftDataServers();
}
?> 