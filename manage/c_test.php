<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('test');
include_once(UCTIME_ROOT."/mod/test.php");
switch (ReqStr('action'))
{
	case 'SetTestGift':SetTestGift();break;
	case 'ReTest':webAdmin('key_data_set');ReTest();break;
	case 'ClearTest':webAdmin('key_data_set');ClearTest();break;
	default: Test();
	
}
?> 