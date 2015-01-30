<?php
$ver = isset($argv[1]) ? trim($argv[1]) : null;
if (!$ver) {
    exit('invalid args');
}
include_once(dirname(dirname(__FILE__))."/config.inc.php");
require_once callApiVer($ver);
$dir = UCTIME_ROOT."/order/";
$query = $db->query("
select 
	cid,
	sid,
	name,
	server,
	api_server,
	api_port,
	api_pwd
from 
	servers A
where 
	A.server_ver = '$ver'
	and A.open_date < now()
	AND A.combined_to = 0
	and A.test = 0 
	and A.open = 1
order by
	A.open_date asc,
	A.sid asc
");	
if($db->num_rows($query))
{

	while($rs = $db->fetch_array($query))
	{
		if($rs['api_server'] && $rs['api_port'] && $rs['api_pwd'] && $rs['server'])
		{
		
			api_base::$SERVER = $rs['api_server'];
			api_base::$PORT   = $rs['api_port'];
			api_base::$ADMIN_PWD   = $rs['api_pwd'];
			$server = explode(',',$rs['server']);	
			//-----------------------------------------------等级----------------------------------------------------------------------------------------------------------------------------------------------------------
			
			$plt_return = '';
			$plt_json = '';
			$plt_list = '';
			$filename = '';
			$plt_return = api_admin::get_player_level_ranking(10);
			$plt_list = unserialize($plt_return);
			if ($plt_list){
				SetServerMaxLevel($plt_list[1]['level'],$rs['cid'],$rs['sid']);//更新最高等级
				$plt_json = json_encode($plt_list);
				$filename = $server[0]."_plt.json";//文件名
				//writetofile($filename,$plt_json,'w',$dir);//写入
			}
			//----------------------------------------------声望-----------------------------------------------------------------------------------------------------------------------------------------------------------
			
			$pft_return = '';
			$pft_json = '';
			$pft_list = '';
			$filename = '';
			$pft_return = api_admin::get_player_fame_ranking(10);
			$pft_list = unserialize($pft_return);
			if ($pft_list){
				$pft_json = json_encode($pft_list);
				$filename = $server[0]."_pft.json";//文件名
				//writetofile($filename,$pft_json,'w',$dir);//写入
			}
			
			//---------------------------------------------------副本------------------------------------------------------------------------------------------------------------------------------------------------------
			
			$mt_return = '';
			$mt_json = '';
			$mt_list = '';
			$filename = '';
			$mt_return = api_admin::get_player_mission_ranking(10);
			$mt_list = unserialize($mt_return);
			if ($mt_list){
				$mt_json = json_encode($mt_list);
				$filename = $server[0]."_mt.json";//文件名
				//writetofile($filename,$mt_json,'w',$dir);//写入
			}
			//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------		
	
		}
		
	}

}
//print "--------------------------------------\n";
$db->close();

?>