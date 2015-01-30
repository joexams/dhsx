<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/apply.php");
include_once(UCTIME_ROOT."/mod/setting.php");
switch (ReqStr('action'))
{
	case 'FindData': webAdmin('s_apply');FindData();break;
	case 'FindPlayerData': webAdmin('s_apply'); FindPlayerData();break;
	case 'DelFindData': webAdmin('s_apply');DelFindData();break;
	default:  webAdmin('s_apply');Apply();
}
?> 