<?php
defined('IN_CMD') or exit('No permission resources.');

$userdb = common::load_model('user_model');
$pubdb = common::load_model('public_model');
$pubdb->table_name = 'admin';

$userlist = $pubdb->select("adminCreateID<>0");
foreach($userlist as $user) {
	if ($user['adminID'] == 1) continue;
/*	$insertarr = array();
	$insertarr['username'] = $user['adminName'];
	$insertarr['encrypt']  = random(6);
	$insertarr['password'] = md5($user['adminPassWord'].$insertarr['encrypt']);
	switch ($user['adminType']) {
		case 's':
			$insertarr['roleid'] = 2;
			break;
		case 'u':
			$insertarr['roleid'] = 3;
			break;
		case 'c':
			$insertarr['roleid'] = 4;
			break;
	}
	$insertarr['createip'] = $user['adminLoingIP'];
	$insertarr['logintimes'] = $user['adminLoginHits'];
	$insertarr['lastloginip'] = $user['adminLoingIP'];
	$insertarr['lang'] = $user['adminLang'];
	$insertarr['lastlogintime'] = strtotime($user['adminLoingTime']);
	$userdb->insert($insertarr);
  */
$username = $user['adminName'];

	$createuser = $pubdb->get_one('adminId='.$user['adminCreateID']);
	$createusername = $createuser['adminName']; 

	$ur = $userdb->get_one(array('username'=>$createusername));
	$userdb->update(array('createuserid'=>$ur['userid']), array('username'=>$username));  
    //$userdb->update($insertarr, array('username'=>$insertarr['username']));
}

echo 'ok';
