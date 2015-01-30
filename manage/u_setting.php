<?php
//-----客服
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/setting.php");
include_once(UCTIME_ROOT."/mod/code.php");
switch (ReqStr('action'))
{
	case 'KfPj': webAdmin('admin_pj');KfPj();break;
	case 'GmBug': webAdmin('u_gm_bug');GmBug();break;
	case 'Bulletin': webAdmin('u_bulletin');Bulletin();break;
	case 'Increase': Increase();break;
	case 'UpVip': webAdmin('u_upvip');UpVip();break;
	case 'PlayerOut': webAdmin('u_out');PlayerOut();break;
	case 'PlayerTest': webAdmin('u_test');PlayerTest();break;
	case 'PlayerStar': webAdmin('star');PlayerStar();break;
	
	case 'SaveIncrease': SaveIncrease();break;	
	case 'SaveBulletin': webAdmin('u_bulletin');SaveBulletin();break;
	case 'DelBulletin': webAdmin('u_bulletin');DelBulletin();break;
	case 'SetDelBulletin': webAdmin('u_bulletin');SetDelBulletin();break;
	case 'DelGmBug': webAdmin('u_gm_bug');DelGmBug();break;
	case 'SaveUpVip': webAdmin('u_upvip');SaveUpVip();break;
	case 'SetPlayerOut': webAdmin('u_out');SetPlayerOut();break;
	case 'SetPlayerTest': webAdmin('u_test');SetPlayerTest();break;
	case 'SetPlayerStar': webAdmin('star');SetPlayerStar();break;
	case 'ReplyGm': webAdmin('u_gm_bug');ReplyGm();break;

}
?> 