<?php 
//--运营
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
webAdmin('c_data');
include_once(UCTIME_ROOT."/mod/data.php");

switch (ReqStr('action')){
	case 'DataPayOrder': DataPayOrder();break;
	case 'DataPay': DataPay();break;
	case 'DataMonthPay': webAdmin('key_data_set');DataMonthPay();break;
	case 'DataDay': webAdmin('key_data_set');DataDay();break;
	case 'DataOnline':  webAdmin('key_data_set');DataOnline();break;
	case 'DataDayList': webAdmin('key_data_set');DataDayList();break;
	case 'DataHourList': webAdmin('key_data_set');DataHourList();break;
	case 'DataCompany': webAdmin('key_data_set');DataCompany();break;
	case 'DataConsume': webAdmin('key_data_set');DataConsume();break;
	case 'DataVipLevel': webAdmin('key_data_set');DataVipLevel();break;
	case 'DataPlayerOut': webAdmin('key_data_set');DataPlayerOut();break;
	case 'DataPayData': DataPayData();break;
	case 'DataData': DataData();break;
	case 'DataDayData': DataDayData();break;
	case 'DataDayServersData': DataDayServersData();break;
	case 'DataServers': DataServers();break;
	//default: DataServers();
}
?>