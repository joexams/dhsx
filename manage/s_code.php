<?php
//-----开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
include_once(UCTIME_ROOT."/mod/code.php");
switch (ReqStr('action'))
{

	case 'Code': webAdmin('code');Code();break;
	case 'CodeBatch': webAdmin('code');CodeBatch();break;
	case 'CodeAdd': webAdmin('code_set');CodeAdd();break;
	case 'CodeAddAgain': webAdmin('code_set');CodeAddAgain();break;

	case 'Pcode': webAdmin('code');Pcode();break;
	case 'CodePartyAdd': webAdmin('code_set');CodePartyAdd();break;
	case 'CodePartyTable': webAdmin('code_set');CodePartyTable();break;
	
	case 'SaveCodePartyAdd': webAdmin('code_set');SaveCodePartyAdd();break;
	case 'SaveCodePartyTable': webAdmin('code_set');SaveCodePartyTable();break;
	case 'SaveCodeAdd': webAdmin('code_set');SaveCodeAdd();break;
	case 'SaveCodeAddAgain': webAdmin('code_set');SaveCodeAddAgain();break;
	case 'DelCode': webAdmin('code_set');DelCode();break;
	case 'CancelCode': webAdmin('s_code_set');CancelCode();break;

	default: webAdmin('code');CodeBatch();
}
?> 