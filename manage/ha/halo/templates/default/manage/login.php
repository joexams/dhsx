<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>大话神仙-登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--[if IE 7]><meta http-equiv="X-UA-Compatible" content="IE=8" /><![endif]-->
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-ie6.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/ie.css">
    <![endif]-->
    <style type="text/css">
	body {
		background:url('static/images/login_bg_stripe.png') repeat;
		padding-top: 40px;
		padding-bottom: 40px;
		background-color: #f5f5f5;
	}

	.form-signin {
		max-width: 390px;
		padding: 19px 29px 29px;
		margin: 0 auto 20px;
		background-color: #fff;
		border: 1px solid #e5e5e5;
		-webkit-border-radius: 5px;
		   -moz-border-radius: 5px;
		        border-radius: 5px;
		-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
		   -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
		        box-shadow: 0 1px 2px rgba(0,0,0,.05);
	}

	.form-horizontal .control-label {
		width: 60px;
	}
	.form-horizontal .controls, .form-horizontal .text-error {
		margin-left: 80px;
	}
    </style>
    <!--[if lte IE 6]>
    <style type="text/css">
    body {
		padding-top: 40px;
		padding-bottom: 40px;
		background-color: #333;
	}
    .form-signin {
		width: 390px;
		padding: 19px 29px 29px;
		margin: 0 auto 20px;
		background-color: #fff;
		border: 1px solid #e5e5e5;
	}
	.form-horizontal .control-label {
		width: 60px;
	}
	.form-horizontal .controls {
		margin-left: 10px;
	}
	.form-horizontal .text-error {
		margin-left: 90px;
	}
	</style>
    <![endif]-->
    <link href="static/css/bootstrap-responsive.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
      <script src="static/js/html5shiv.js"></script>
    <![endif]-->
  </head>
  <body>
	<div class="container">
		<form class="form-signin form-horizontal" id="post_login_submit">
		  <fieldset>
		  <legend>请登录</legend>
		  <div class="control-group info">
		    <label class="control-label" for="username">账号</label>
		    <div class="controls">
		      <input type="text" name="username" id="username" placeholder="账号">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label" for="password">密码</label>
		    <div class="controls">
		      <input type="password" name="password" id="password" placeholder="密码">
		    </div>
		  </div>
		  <p class="text-error" id="tips_error"></p>
		  <div class="control-group">
		    <div class="controls">
		      <input type="hidden" name="doSubmit" value="login">
		      <button type="submit" class="btn btn-large btn-primary" id="btnsubmit">登 录</button>
		    </div>
		  </div>
		  </fieldset>
		</form>
	</div>

	<script src="static/js/jquery.min.js"></script>
	<script type="text/javascript">
	var msg = {
		"error_username": "账号输入有误",
		"error_password": "密码输入有误"
	};
	$(document).ready(function(){
		$('#username').focus();
		$('#username').focusin(function() {
			$(this).parent().parent().attr('class', 'control-group info');
			$('#tips_error').html('');
		});
		$('#username').focusout(function() {
			$(this).parent().parent().removeClass('info');
		});
		$('#password').focusin(function() {
			$(this).parent().parent().attr('class', 'control-group info');
			$('#tips_error').html('');
		});
		$('#password').focusout(function() {
			$(this).parent().parent().removeClass('info');
		});

		$('#post_login_submit').submit(function(e){
			e.preventDefault();
			var username = $('#username').val(), password = $('#password').val();
			

			var objform = $(this), time = 2;
			if (username.length < 3){
				$('#tips_error').html(msg.error_username);
				$('#username').parent().parent().attr('class', 'control-group error');
				return false;
			}
			if (password.length < 6){
				$('#tips_error').html(msg.error_password);
				$('#password').parent().parent().attr('class', 'control-group error');
				return false;
			}
			$('#btnsubmit').attr('disabled', 'disabled');

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
					$('#tips_error').html(data.msg);
					$('#tips_error').fadeIn();
					setTimeout( function(){
						$('#tips_error').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		});
	});
	</script>
  </body>
</html>