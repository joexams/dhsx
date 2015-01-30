<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo Lang('login_title'); ?></title>
<link type="text/css" href="static/css/style.css" rel="stylesheet" />
<link type="text/css" href="static/css/login.css" rel="stylesheet" />
</head>
<body>
<script type="text/javascript" src="static/js/jquery.min.js"></script>
<script type="text/javascript">
var username_err = '<?php echo Lang("username_length_error") ?>', password_err = '<?php echo Lang("password_length_error"); ?>';
$(document).ready(function(){
	$('#post_login_submit').submit(function(e){
		e.preventDefault();
		var username = $('#username').val(), password = $('#password').val();
		var objform = $(this), time = 2, msg = '';
		if (username == '' || username == 'username'){
			$('#username').click();
			return false;
		}
		if (password == '' || password == '12345'){
			$('#password').click();
			return false;
		}
		$('#btnsubmit').attr('disabled', 'disabled');
		if (username.length >= 3 && password.length >= 6){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=index&v=login',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '';
					switch (data.status){
						case 0: 
							if (data.url != undefined && data.url != ''){
								location.href = data.url;
							}
							return false;
							break;
						case 1: alertclassname = 'tips alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
		}else {

			if (username.length < 3){
				msg = username_err;
			}
			if (password.length < 6){
				msg += msg != '' ? '<br>'+password_err : password_err;
			}

			$('#op_tips').attr('class', 'tips alert_error');
			$('#op_tips').children('p').html(msg);
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( time * 1000 ) );
		}
	});


	$('#username').on({
		click: function(){
			if ($(this).val() == 'username'){
				$(this).val('');
				$(this).focus();
			}
		},
		blur: function(){
			if ($(this).val() == ''){
				$(this).val('username');
			}
		}
	});
	$('#password').on({
		click: function(){
			if ($(this).val() == '12345'){
				$(this).val('');
				$(this).focus();
			}
		},
		blur: function(){
			if ($(this).val() == ''){
				$(this).val('12345');
			}
		}
	});


});
</script>
<div id="background">
	<div id="container">
		<div id="logo">&nbsp;</div>
		<div id="box"> 
			<form id="post_login_submit" action="<?php echo INDEX; ?>?m=manage&c=index&v=login" method="POST"> 
				<div class="one_half">
					<p><input name="username" value="username" id="username" tabindex="1" class="field" /></p>
					<p><input type="checkbox" class="iphone" tabindex="3" />
						<label class="fix"><?php echo Lang('remember me');?></label>
					</p> 
				</div>
				<div class="one_half last">
					<p><input type="password" name="password" id="password" tabindex="2" value="12345" class="field" /></p>
					<p>
						<input type="hidden" name="doSubmit" value="Login"  />
						<input type="submit" id="btnsubmit" value="<?php echo Lang('Login') ?>" class="login" />
					</p>
				</div>
			</form> 
		</div>
		<div id="op_tips" class="tips alert_error" style="display: none;"><p></p></div>
	</div>
</div>
</body>
</html>