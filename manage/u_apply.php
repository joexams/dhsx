<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/apply.php");
switch (ReqStr('action'))
{
	case 'FindData': webAdmin('u_apply');FindData();break;
	case 'ApplyAdd': webAdmin('u_apply');ApplyAdd();break;
	case 'FindPlayerData': webAdmin('u_apply'); FindPlayerData();break;
	case 'SaveApplyAdd': webAdmin('u_apply');SaveApplyAdd();break;
	case 'DelApply': webAdmin('u_apply');DelApply();break;
	case 'DelFindData': webAdmin('u_apply');DelFindData();break;
	default:  webAdmin('u_apply');Apply();
}
?> 