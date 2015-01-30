<?php 
if(!defined('IN_UCTIME')) 
{
	exit('Access Denied');
}
//取玩家数据
			//G.count as listeners_count

			//left join player_listener_count G on A.id = G.player_id

	$uid=ReqNum('uid');
	if(!empty($uid)) 
	{
		$query = $pdb->query("
		select 
			A.*,
			B.*,
			C.*,
			D.name as camp_name,
			E.level_up_time,
			E.ingot as ingot_vip,
			E.total_ingot,
			F.name as deploy_mode_name,
			G.ranking,
			H.*
		from 
			player A
			left join player_data B on A.id = B.player_id
			left join player_trace C on A.id = C.player_id
			left join camp D on B.camp_id = D.id
			left join player_charge_record E on A.id = E.player_id
			left join deploy_mode F on B.deploy_mode_id = F.id
			left join player_super_sport_ranking G on A.id = G.player_id
			left join player_tower H on A.id = H.player_id
		where 
			A.id = '$uid'
		");
		if($pdb->num_rows($query))
		{
			$player = $pdb->fetch_array($query);
		}
	}

?>