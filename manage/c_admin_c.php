<?php
//-----运营
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/admin.php");
switch (ReqStr('action'))
{
	
	case 'AdminC': webAdmin('c_admin_c');AdminC();break;
	case 'AddAdminC': webAdmin('c_admin_c');AddAdminC();break;
	case 'EditAdminC': webAdmin('c_admin_c');EditAdminC();break;
	
	case 'SaveAddAdminC': webAdmin('c_admin_c');SaveAddAdminC();break;
	case 'SaveEditAdminC': webAdmin('c_admin_c');SaveEditAdminC();break;
	//case 'LockAdminC': webAdmin('c_admin_c');LockAdminC();break;
}