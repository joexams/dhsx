<?php
defined('IN_CMD') or exit('No permission resources.');
@header("Content-Type: application/json; charset=utf-8");
$list = array();
		$data['allnum'] = $data['alltotal'] = $data['allingot'] = 0;
		$starttime = strtotime(date('Y-m-d 00:00:00'));
		$endtime = strtotime(date('Y-m-d 23:59:59'));
		$pubdb = common::load_model('public_model');
		$sql = "select sid,name,o_name, is_combined from servers where open=1 and test=0 AND now()-open_date>2000 and combined_to=0 and cid=1 and sid != 5900";
		$serverlist = $pubdb->get_list($sql);
		foreach ($serverlist as $key => $value){
			$sid = $value['sid'];
			$getdb = $pubdb->set_db($sid);
			if ($getdb !== false){
				$sql = 'SELECT a.type,c.name, COUNT(DISTINCT player_id) AS num, COUNT(a.id) AS total, sum(change_charge_value+value) AS ingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id left join ingot_change_type c on c.id=a.type WHERE value < 0 AND is_tester=0 and change_time>'.$starttime.' and change_time<'.$endtime.' GROUP BY type';
				$numsql = 'SELECT COUNT(DISTINCT player_id) AS allnum, COUNT(a.id) AS alltotal, sum(change_charge_value+value) AS allingot FROM player_ingot_change_record a LEFT JOIN player b ON a.player_id=b.id WHERE value < 0 AND is_tester=0 and change_time>'.$starttime.' and change_time<'.$endtime;
				$tmplist = $getdb->get_list($sql);				
				$list = array_merge($list, $tmplist);
				$allcount = $getdb->get_list($numsql);
				$data['allnum'] += $allcount[0]['allnum'];
				$data['alltotal'] += $allcount[0]['alltotal'];
				$data['allingot'] += $allcount[0]['allingot'];
				$serlist[$key]['sname'] = $value['is_combined'] == 1 ? $value['name'].'('.$value['o_name'].')' : $value['o_name'];
				$serlist[$key]['ingot'] = $allcount[0]['allingot'];
				$serlist[$key]['num'] = $allcount[0]['allnum'];
			}
		}
		$alllist = array();
		foreach ($list as $value) {
			if (array_key_exists($value['type'], $alllist)) {
				$alllist[$value['type']]['num'] += $value['num'];
				$alllist[$value['type']]['total'] += $value['total'];
				$alllist[$value['type']]['ingot'] += $value['ingot'];
			}else {
				$alllist[$value['type']]['num'] = $value['num'];
				$alllist[$value['type']]['total'] = $value['total'];
				$alllist[$value['type']]['ingot'] = $value['ingot'];
			}
			$alllist[$value['type']]['type'] = $value['name'];
		}
		usort($alllist, 'cmp');
		usort($serlist, 'cmp');
		$serlist = array_slice($serlist,0,20);
		$alllist = array_slice($alllist,0,20);
		foreach ($alllist as $akey => $avalue){
			$alllist[$akey]['sname'] = $serlist[$akey]['sname'];
			$alllist[$akey]['seringot'] = $serlist[$akey]['ingot'];
			$alllist[$akey]['sernum'] = $serlist[$akey]['num'];
		}
		$data['list'] = $alllist;
		setcache('today_consume', $data, '', 'memcache', 'memcache', 3600);
	function cmp($a, $b)
	{
	    if ($a['ingot'] == $b['ingot']) {
	        return 0;
	    }
	    return ($a['ingot'] < $b['ingot']) ? -1 : 1;
	}