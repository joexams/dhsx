<?php
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/password.php");

switch (ReqStr('action'))
{
	case 'SaveEditPwd': SaveEditPwd();break;	
	default:  EditPwd();
}
?>