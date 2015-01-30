<?php
//-----客服
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/code.php");
switch (ReqStr('action'))
{

	default: webAdmin('code');Code();
}
?> 