<?php 
//--运营
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
webAdmin('u_pay');
include_once(UCTIME_ROOT."/mod/data.php");

switch (ReqStr('action')){
	case 'DataPay': DataPay();break;
	default: DataPay();
}
?>