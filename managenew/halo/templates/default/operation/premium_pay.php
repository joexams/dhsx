<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('option[value!="0"]', $('#cid')).remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').removeAttr('disabled');
		var objform = $(this), sid = $('#sid').val(), cid = $('#cid').val(), playername = objform.find('input[name="playername"]').val();
		if (sid > 0 && cid > 0 && playername != ''){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=premium&pay',
				data: objform.serialize(),
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
		}else {
			$('#btnsubmit').removeAttr('disabled');
		}
	});
});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_premium') ?></span></a></li>
	</ul>

	<div class="clear"></div>
	<div class="content" id="submit_area">
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=premium&v=transfer" method="post">
			<div id="op_tips" style="display: none;"><p></p></div>
			<input type="hidden" name="doSubmit" value="1">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<th style="width:10%"><?php echo Lang('server') ?></th>
					<td style="width:50%">
						<select name="cid" id="cid">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
						</select>
						<select name="sid" id="sid">
							<option value="0"><?php echo Lang('all_server') ?></option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('player_name') ?></th>
					<td><input type="text" name="playername" id="playername" style="width:40%"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('pay_ingot') ?></th>
					<td><input type="text" name="ingot" style="width:40%"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('pay_money') ?></th>
					<td><input type="text" name="amout" style="width:40%"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('pay_order_no') ?></th>
					<td><input type="text" name="oid" style="width:40%;"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('pay_time') ?></th>
					<td><input type="text" name="dtime_unix" style="width:40%;" value="<?php echo date('Y-m-d H:i:s', time()); ?>"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td> 
						<p>
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
						</p>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
	</div>
</div>