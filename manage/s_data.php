<?php 
//--开发
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}
webAdmin('data'); 
include_once(UCTIME_ROOT."/mod/data.php");

switch (ReqStr('action')){

	case 'DataPayOrder': DataPayOrder();break;
	case 'DataServersPay': DataServersPay();break;
	case 'DataPay': DataPay();break;
	case 'DataPayData': DataPayData();break;
	case 'DataDayData': DataDayData();break;
	case 'DataDayServersData': DataDayServersData();break;

	case 'DataDay': webAdmin('key_data_set');DataDay();break;
	case 'DataMonthPay': webAdmin('key_data_set');DataMonthPay();break;
	case 'DataOnline':  webAdmin('key_data_set');DataOnline();break;
	case 'DataConsume': webAdmin('key_data_set');DataConsume();break;
	case 'DataVipLevel': webAdmin('key_data_set');DataVipLevel();break;
	case 'DataMaxLevel': webAdmin('key_data_set');DataMaxLevel();break;
	case 'DataDayList': webAdmin('key_data_set');DataDayList();break;
	case 'DataHourList': webAdmin('key_data_set');DataHourList();break;
	case 'DataCompany': webAdmin('key_data_set');DataCompany();break;
	case 'DataNewUser': webAdmin('key_data_set');DataNewUser();break;
	case 'SetPaySuccess': webAdmin('key_data_set');SetPaySuccess();break;
	case 'SetPayStatus': webAdmin('key_data_set');SetPayStatus();break;
	case 'SetPayVIP': webAdmin('key_data_set');SetPayVIP();break;
	case 'DataPlayerOut': webAdmin('key_data_set');DataPlayerOut();break;
	
	default: DataServers();
}
?>