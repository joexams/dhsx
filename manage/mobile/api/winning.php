<?php
defined('IN_G') or exit('No permission resources.');

$truename = isset($_GET['truename']) ? trim($_GET['truename']) : 0;
$idcard = isset($_GET['idcard']) ? trim($_GET['idcard']) : '';
$mobile = isset($_GET['mobile']) ? trim($_GET['mobile']) : '';
$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$address = isset($_GET['address']) ? trim($_GET['address']) : '';
$zip = isset($_GET['zip']) ? trim($_GET['zip']) : '';
$province = isset($_GET['province']) ? trim($_GET['province']) : '';

$zoneid = isset($_GET['zoneid']) ? intval($_GET['zoneid']) : 0;
$openid = isset($_GET['openid']) ? trim($_GET['openid']) : 0;
$openkey = isset($_GET['openkey']) ? trim($_GET['openkey']) : 0;

