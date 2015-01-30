<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo GAMETITLENAME?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<link href="style/style.css" rel="stylesheet" type="text/css" />

<script language = "javaScript" src = "include/js/common.js" type="text/javascript"></script>
<script language = "javaScript" src = "include/js/menu.js" type="text/javascript"></script>



<body>
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<div id="top">
 <div class="top_user">Hello <?php echo $adminWebName?> - <a href="login.php?action=out">退出</a></div><br>
  <ul class="view_menu">
<li class="manage_title"><?php echo GAMETITLENAME?></li>
<?php if(!webAdmin('s','y')) { ?>
<li 
<?php if($nowUrl == 's.php') { ?>
class="view_cur"
<?php } else { ?>
class="view_no_cur"
<?php } ?>
><a href="s.php">系统设置</a></li>
<?php } ?>
<?php if(!webAdmin('t','y')) { ?>
<li 
<?php if($nowUrl == 't.php') { ?>
class="view_cur"
<?php } else { ?>
class="view_no_cur"
<?php } ?>
><a href="t.php">模版数据</a></li>
<?php } ?>
 
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
 		<li 
<?php if($father_id == $rs['id']) { ?>
class="view_cur"
<?php } else { ?>
class="view_no_cur"
<?php } ?>
>

 		<a href="<?php echo $rs['url']?>"><?php echo $rs['describe']?></a>
  			<ul>
  
<?php if(is_array($rs['second_menu'])) { foreach($rs['second_menu'] as $srs) { ?>
  				<li><a href="#" class="hide"><?php echo $srs['describe']?></a>
   					<ul>
   
<?php if(is_array($srs['third_menu'])) { foreach($srs['third_menu'] as $trs) { ?>
   						<li><a href="<?php echo $trs['url']?>"><?php echo $trs['describe']?></a></li>
   						
   						
<?php } } ?>
   					</ul>
  		    	</li>
  		    	
<?php } } ?>
  			</ul>
 			</li>
 			
<?php } } ?>
  </ul>
</div>
<div id="wrap">