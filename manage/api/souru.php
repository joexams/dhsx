<?php
include_once(dirname(dirname(__FILE__))."/config.inc.php");
include_once(UCTIME_ROOT."/conn.php");
$time = ReqStr('time');
$sign = ReqStr('sign');

if ($sign != md5($time.'_{7f6f69f2-6e8b-47d4-bb92-210a9419a1e5}')) {
	echo 'error!';
	exit();
}
$day_s = date("Y-m-d 00:00:00");
$day_e = date("Y-m-d 23:59:59");
$pay = $db->result($db->query("
select 		
	sum(A.amount)
from 
	pay_data A
where 
	A.dtime >= '$day_s' 
	and A.dtime <= '$day_e'
	and A.status <> 1	
	and A.success <> 0	
"),0);
echo "document.write('".round($pay,2)."');";

$db->close();
?>