<!-- 添加用户 -->
<script type="text/javascript">
$(document).ready(function(){
	$('#post_user_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=user&v=edit_password',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						document.getElementById('post_user_submit').reset();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
	});
	setTimeout( function(){
		$('#op_tips').fadeOut();
	}, ( 3000 ) );
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('edit_person_info'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form name="post_user_submit" id="post_user_submit" action="<?php echo INDEX; ?>?m=manage&c=user&v=edit_password" method="post">
			<input type="hidden" name="doSubmit" value="1">
			<input type="hidden" name="userid" id="userid" value="0">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<th style="width: 8%"><?php echo Lang('username'); ?>：</th>
					<td style="width: 20%"><?php echo $data['info']['username']; ?></td>
					<th style="width: 8%;"><?php echo Lang('select_language'); ?>：</th>
					<td style="width: 20%">
						<select name="lang" id="lang">
				      	    <option value="zh-cn"<?php echo $data['info']['lang'] == 'zh-cn' ? ' selected': '' ?>>简体中文</option>
				      	    <option value="zh-tw"<?php echo $data['info']['lang'] == 'zh-tw' ? ' selected': '' ?>>繁體中文</option>
				      	    <option value="en"<?php echo $data['info']['lang'] == 'en' ? ' selected': '' ?>>English</option>
				    	</select>
				    </td>
				    <td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('oldpassword'); ?>：</th>
					<td><input type="password" name="oldpassword" style="width:90%"></td>
					<th>&nbsp;</th>
					<td>&nbsp;</td>
				    <td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('password'); ?>：</th>
					<td><input type="password" name="password" style="width:90%"></td>
					<th><?php echo Lang('re_password'); ?>：</th>
					<td><input type="password" name="repassword" style="width:90%"></td>
				    <td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="3">
						<p>
						<input type="hidden" name="userid" value="<?php echo $data['info']['userid'] ?>" />
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('edit'); ?>">
						</p>
					</td>
				    <td>&nbsp;</td>
				</tr>
			</tbody>
			</table>
	    </form>
	    <div id="op_tips" class="alert_info"><p><?php echo Lang('edit_password_tips') ?></p></div>
		<!-- End form elements -->
	</div>
</div>
