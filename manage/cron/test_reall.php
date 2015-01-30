<?php

	include_once(dirname(dirname(__FILE__))."/config.inc.php");
	include_once(UCTIME_ROOT."/conn.php");
	if ($adminWebType != 's')
	{
		showMsg('NOPOWER','login.php','web');
		exit();		
	}	
	$query = $db->query("
	select 
		A.sid,
		A.cid,
		B.combined_to
	from 
		servers_data A
		left join servers B on A.sid = B.sid
	where
		B.combined_to = 0
		and B.open = 1
	order by
		A.sid asc
	");
	if($db->num_rows($query))
	{
		while($srs = $db->fetch_array($query))
		{
			echo $srs['sid'].'<br />';

			ReServerTest($srs['cid'],$srs['sid']);

		}			
	
	}

?> 