<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/style.css" rel="stylesheet" type="text/css" />

<title>LOGIN</title>
</head>

<body >
<div>
<table class="logintable" width="300">
   <form name="form" method="post" action="?action=login" >
  <tr>
    <th colspan="2" align="center"><strong>LOGIN</strong></th>
    </tr>
  <tr>
    <td align="right">ID：</td>
    <td><input type="text" name="Aname" value="" size="30"/></td>
  </tr>
  <tr>
    <td align="right">PWD：</td>
    <td><input type="password" name="ApassWord"  size="30"/></td>
  </tr>
  <tr>
    <td colspan="2">DataBase：<input type="radio" name="selectdb" value="alpha"/>QQ Alpha &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="selectdb" checked value='' />Trunk</td>
  </tr>
  <tr>
    <td >&nbsp;</td>
    <td><input type="submit" name="Submit" value=" 登 陆 " class="button"/></td>
  </tr>
  </form>
</table>
</div>
</body>
</html>