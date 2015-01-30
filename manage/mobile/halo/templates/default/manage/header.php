<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if IE 7]><meta http-equiv="X-UA-Compatible" content="ie=7" /><![endif]-->
<title><?php echo Lang('title'); ?></title>
<link type="text/css" rel="stylesheet" href="static/css/main.css" />
<link type="text/css" rel="stylesheet" href="static/css/blue.css" />
<link type="text/css" rel="stylesheet" href="static/css/zTreeStyle.css">
</head>
<body>
<div class="menuW">
	<div class="menuIn">
		<div class="menu">
			<div class="wrap clearfix">
				<div class="menuL" id="menuL">
					<ul id="mainmenu">

					</ul>
				</div>
				<div class="tempsearch">
					<input type="text" value="找功能" name="keyword" class="input" id="searchinput">
					<a href="javascript:;" class="btn"></a>
					<div id="followsearch" class="followsearch" style="top:30px;left:0px;display:none;"></div>
				</div>
				<p class="tempLink" id="loading" style="display:none;">
					<a><img alt="Loading..." src="static/images/loading.gif" width="20px" height="20px"></a>
				</p>
				<!-- <p><input type="text" class="search" /><input type="button" value="搜索"></p> -->
				<p class="menuR">
					<a><?php echo param::get_cookie('username')?></a>
					<a href="#app=2&url=<?php echo urlencode(WEB_URL.INDEX.'?m=manage&c=user&v=edit_password') ?>" class="c_txt" rel="<?php echo INDEX; ?>?m=manage&c=user&v=edit_password"><?php echo Lang('edit_password') ?></a>|<a href="<?php echo INDEX; ?>?m=manage&c=index&v=logout"><?php echo Lang('logout') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>

<!-- 中间 -->
<div class="leftmenu" id="submenu">

</div>

<div id="container">