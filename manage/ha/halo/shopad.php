<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	/**
	 * 运营商
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}
	/**
	 * 切换平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
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
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null) {
			$.ajax({
					url: '<?php echo INDEX; ?>?m=operation&c=shopad&v=setting',
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
					},
					error: function() {
						$('#btnsubmit').removeAttr('disabled');
					}
				});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
		return false;
	});
});
</script>

<script type="text/template" id="serverchklisttpl">
<div>
	<li><input type="checkbox" name="sid[]" value="${sid}">${name}</li>
</div>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('shop_ad_setting') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="content" id="submit_area">
			<!-- Begin form elements -->			
				<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=shopad&v=setting" method="post">
					<table class="global" width="100%" cellpadding="0" cellspacing="0">
						<tr class="betop">
							<th style="width: 10%;"><?php echo Lang('company_platform') ?></th>
							<td style="width: 30%;"> 
								<select name="cid" id="cid">
									<option value="0"><?php echo Lang('company_name') ?></option>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th><?php echo Lang('server') ?></th>
							<td> 
								<select multiple name="sid[]" id="sid" style="width:200px;height:300px;">
									
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th>商城广告</th>
							<td> 
								<?php foreach ($shopitemlist as $key => $value) { ?>
									 <input type="checkbox" name="ads[]" value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?> <br>
								<?php } ?>
							</td>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td> 
								<p>
								<input type="hidden" name="doSubmit" value="1">
								<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
								<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
								</p>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
			    </form>
			    <div id="op_tips" style="display: none;"><p></p></div>
			<!-- End form elements -->
		</div>
	</div>
</div>