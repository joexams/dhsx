<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
	/**
	 * 获取平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	/**
	 * 改变平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});
	/**
	 * 提交
	 * @return {[type]} [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
			url: '<?php echo INDEX; ?>?m=operation&c=premium&v=transfer',
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
				document.getElementById('post_submit').reset();
				setTimeout( function(){
					$('#op_tips').fadeOut();
					$('#btnsubmit').removeAttr('disabled');
				}, ( time * 1000 ) );
			}
		});
	});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('order_transfer') ?></span></a></li>
	</ul>

	<div class="clear"></div>
	<div class="content" id="submit_area">
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=premium&v=transfer" method="post">
			<div id="op_tips" style="display: none;"><p></p></div>
			<input type="hidden" name="doSubmit" value="1">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:8%">&nbsp;</td>
					<td style="width:10%;">&nbsp;</td>
					<th style="width:10%;"> 
						<select name="cid" id="cid">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
						</select>
					</th>
					<td style="width:8%;">&nbsp;</td>
					<td style="width:10%;">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td> 
						<select name="source_sid" class="sid">
							<option value="0"><?php echo Lang('all_server') ?></option>
						</select>
					</td>
					<th style="border-bottom:0;vertical-align:bottom;">-></th>
					<td>&nbsp;</td>
					<td>
						<select name="target_sid" class="sid">
							<option value="0"><?php echo Lang('all_server') ?></option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td style="font-weight:600px;align:right"><?php echo Lang('pay_order_no') ?></td>
					<td>
						<input type="text" name="oid" style="width:60%">
					</td>
					<th style="vertical-align:top">-></th>
					<td><?php echo Lang('player_name') ?></td>
					<td>
						<input type="text" name="playername" style="width:60%">
					</td>
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
					<td colspan="3">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
	</div>
</div>