<?php
	//ִ����ȡ���պϷ��Ĳ��Ժ�
	$pass = isset($argv[1]) ? trim($argv[1]) : null;
	if ($pass != 'cron') {
		exit('invalid args');
	}
	include_once(dirname(dirname(__FILE__))."/config.inc.php");

	$query = $db->query("
	select 
		cid,
		combined_to
	from 
		servers_merger
	where 
		date_format(open_date, '%Y-%m-%d') = curdate()
	");
	if($db->num_rows($query))
	{
		while($rs = $db->fetch_array($query))
		{
			ReServerTest($rs['cid'],$rs['combined_to']);
			//echo $rs['cid'].'|'.$rs['combined_to'].'<br />';
	
		}
	}
	insertServersAdminData(0,0,0,'������','ִ����ȡ�Ϸ�����Ժżƻ�����');//���������¼

?> 