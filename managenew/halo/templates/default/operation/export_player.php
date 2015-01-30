<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});

	$('#get_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		$('#btnsubmit').attr('disabled', 'disabled');
		if (cid > 0 && sid > 0) {
			// $.ajax({
			// 	url: '<?php echo INDEX; ?>?m=server&c=get&v=export_player',
			// 	data: objform.serialize(),
			// 	success: function(){

			// 	}
			// });
			var sname = $('#sid option:selected').text();
			sname = encodeURIComponent(sname);
			location.href = '<?php echo WEB_URL.INDEX;?>?m=server&c=get&v=export_player&sname='+sname+'&'+objform.serialize();
			$('#btnsubmit').removeAttr('disabled');
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
	});
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('export_player'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('export_player'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=export_player" method="get">
				<input type="hidden" name="dogetSubmit" value="1">
				<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
						<td>
							<select name="cid" id="cid">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
							<select name="sid" id="sid">
								<option value="0"><?php echo Lang('server'); ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('create_role'); ?></th>
						<td>
							<select name="ctype" id="ctype">
								<option value="1"><?php echo Lang('already_create'); ?></option>}
								<option value="2"><?php echo Lang('no_create'); ?></option>
								<option value="0"><?php echo Lang('all'); ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('channel'); ?></th>
						<td>
							<select name="source" id="source">
								<option value=""><?php echo Lang('all'); ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('register_date'); ?></th>
						<td>
							<input type="text" name="starttime" size="12" readonly onclick="WdatePicker()">
							-
							<input type="text" name="endtime" size="12" readonly onclick="WdatePicker()">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>VIP<?php echo Lang('level'); ?></th>
						<td>
							<input type="text" name="start_vip_level" size="5">
							-
							<input type="text" name="end_vip_level" size="5">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('role').Lang('level'); ?></th>
						<td>
							<input name="minlevel" type="text" value="0" size="3"> ~	
						    <input name="maxlevel" type="text" value="0" size="3">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('player_name'); ?></th>
						<td>
							<textarea name="username" style="width:200px;height:200px;"></textarea>
							<br>
							<span class="graptitle"><?php echo Lang('server_game_url_tips'); ?></span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('sure_export'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
				<div id="op_tips" style="display: none;width:100%"><p></p></div>
		    </form>
			<!-- End form elements -->
		</div>
	</div> 
	<br class="clear">
</div>