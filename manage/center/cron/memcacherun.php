<?php 
/**
 * stats run day
 */

defined('IN_CMD') or exit('No permission resources.');

$skey = 'server';
$cidlist = array(1, 176);
$gidlist = array(1, 2);

$pubdb = common::load_model('public_model');
$platformdb = common::load_model('priv_platform_model');
$platformdb->set_model(1);

foreach ($cidlist as $ckey => $cid) {
	foreach ($gidlist as $gkey => $gid) {
		$gr = $platformdb->get_one(array('gid'=>$gid));
		if ($gr && strpos($gr['cids'], ',') !== false){
			if ($cid > 0){
				if (strpos($gr['cids'], ','.$cid.',') === false)	continue;
				$wherestr = true;
				$cid = intval($cid);
				$memkey = md5('total_list_'.$wherestr.$cid);
				delcache($memkey);
				
				$wherestr = str_ireplace('cid', 'a.cid', $wherestr);
				$wherestr = str_ireplace('sid', 'a.sid', $wherestr);
				$wherestr =  "WHERE a.cid='$cid' AND open_date<'".date('Y-m-d 00:00:00')."'";

				$sql = "SELECT a.sid,name,o_name,open_date,SUM(`pay_amount`) AS pay_amount,SUM(`new_player`) AS newer_count, SUM(pay_player_count) AS pay_player_count, SUM(`pay_num`) AS pay_num". 
			   ", SUM(`register_count`) AS register_count,SUM(`create_count`) AS create_count, SUM(login_count) AS login_count".
			   ", SUM(`avg_online_count`) AS online_count, SUM(`max_online_count`) AS max_online_count, SUM(consume) AS consume".
			   " FROM servers a LEFT JOIN game_data b ON a.sid=b.sid $wherestr GROUP BY a.sid ORDER BY a.sid DESC";
				$list = $pubdb->get_list($sql);
				if (count($list) > 0){
					foreach ($list as $key => $value) {
						$list[$key]['opendate'] = ceil((time() -strtotime($value['open_date'])) / (24*3600));
						$list[$key]['avg_create_count'] = $value['register_count'] > 0 ? round($value['create_count']/$value['register_count'], 2) * 100 : 0;
						$list[$key]['arpu'] = $value['pay_player_count'] > 0 ? round($value['pay_amount']/$value['pay_player_count'], 2) : 0;
						$list[$key]['pay_amount'] = round($value['pay_amount'], 2);
					}
				}
				$lifttime = strtotime(date('Y-m-d 23:59:59')) - time();
				setcache($memkey, $list, '', 'memcache', 'memcache', $lifttime);
			}
		}
	}
}
echo 'ok';
