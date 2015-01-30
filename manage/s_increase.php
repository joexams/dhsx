<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('increase');
include_once(UCTIME_ROOT."/mod/increase.php");
switch (ReqStr('action'))
{
	case 'SetIncrease':SetIncrease();break;
	default: Increase();
}
?> 