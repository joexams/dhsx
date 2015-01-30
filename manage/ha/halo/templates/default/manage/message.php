<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>错误提示</title>
<style type="text/css">
.error-main{margin:0 auto;width:440px;height:350px;position:relative;margin-top:70px;}
.error-main a:hover{text-decoration:underline;}
.error-wrap{position:absolute;right:0;top:45px;_width:281px;}
.error-text-title-wrap{font-weight:bold;font-size:14px;}
.error-text-title{float:left;margin-top:22px;width:122px;display:inline;line-height:16px;}
.error-text-content{color:#333;padding:10px 0;width:280px;line-height:14px;}
.error-number{font-weight:normal;font-size:60px;color:#00AEEF;font-style:normal;float:left;display:inline;font-family:Arial;}
.handle-way-list{color:#666;margin-top:20px;margin-left:5px;}
.handle-way-list li{line-height:2;}
.handle-way-list a{color:#06C;text-decoration:none;}
</style>
</head>
<body>
<div>
	<div class="error-main">
	<div class="error-wrap">
	    <div class="error-text-title-wrap clearfix"><div class="error-number">500，</div><div class="error-text-title"></div></div>
	    <p class="error-text-content"><?php echo $msg; ?></p>
	    <ul class="handle-way-list">
	        <li class="handle-way-item"><a href="<?php echo WEB_URL.INDEX ?>?m=manage&c=index&v=login">重新登录</a></li>
	    </ul>
	</div>
	</div>
</div>
</body>
</html>