<?php
//-----客服
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('bug');
include_once(UCTIME_ROOT."/mod/bug.php");
switch (ReqStr('action'))
{
	case 'DelBug': DelBug();break;
	case 'ReplyBug': ReplyBug();break;
	default:Bug();
}
?> 