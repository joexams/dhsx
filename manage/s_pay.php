<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('pay_add');
include_once(UCTIME_ROOT."/mod/pay.php");
switch (ReqStr('action'))
{
	case 'TransferOrder': TransferOrder();break;
	case 'SetTransferOrder': SetTransferOrder();break;
	case 'SavePayAdd':SavePayAdd();break;
	default: PayAdd();
}
?> 