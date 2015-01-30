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
	
	case 'FindData': webAdmin('c_apply');FindData();break;
	case 'ApplyAdd': webAdmin('c_apply');ApplyAdd();break;
	case 'FindPlayerData': webAdmin('c_apply'); FindPlayerData();break;
	case 'SaveApplyAdd': webAdmin('c_apply');SaveApplyAdd();break;
	case 'DelApply': webAdmin('c_apply');DelApply();break;
	case 'DelFindData': webAdmin('c_apply');DelFindData();break;
	
	case 'SetFindData': webAdmin('c_apply_set');SetFindData();break;
	case 'SetApply': webAdmin('c_apply_set');SetApply();break;
	case 'ReplyApply': webAdmin('c_apply_set');ReplyApply();break;
	case 'SetApplyAll': webAdmin('c_apply_set');SetApplyAll();break;
	case 'CancelApply': webAdmin('c_apply_set');CancelApply();break;
	case 'CancelFindData': webAdmin('c_apply_set');CancelFindData();break;
	default:  webAdmin('c_apply');Apply();
}
?> 