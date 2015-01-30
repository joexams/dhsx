<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#cid option[value!=0]').remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && global_serverlist){
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});


	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid.length > 0) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=source&v=game',
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
					$('#sid option').remove();
					// setTimeout( function(){
					// 	$('#op_tips').fadeOut();
					// 	$('#btnsubmit').removeAttr('disabled');
					// }, ( time * 1000 ) );
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html(msg);
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
			$('#btnsubmit').removeAttr('disabled');
		}
		return false;
	});

	$('#op_tips').on('click', function() {
		$(this).fadeOut();
	});
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('game_setting') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content" id="submit_area">
	<!-- Begin form elements -->			

	    <div id="op_tips" style="display: none;"><p></p></div>
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=source&v=game_setting" method="post">
			<input type="hidden" name="doSubmit" value="1">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="3" style="text-align:left;"><?php echo Lang('server'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th style="width: 120px;"><?php echo Lang('company_platform'); ?></th>
						<td style="width: 380px;">
							<select name="cid" id="cid">
								<option value="0"><?php echo Lang('operation_platform'); ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('server'); ?></th>
						<td>
							<select name="sid[]" multiple="multiple" id="sid" style="width:250px;height:200px;"></select>
						</td>	
						<td>&nbsp;</td>
					</tr>				
				</tbody>
				<thead>
					<tr>
						<th colspan="3" style="text-align:left;">BOSS设置</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>BOSS</th>
						<td>
							<select name="world_boss_id">
								<option value=""><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">擎天木</option>
								<option value="2">赤炎兽</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('server_is_open'); ?></th>
						<td>
							<select name="bossoptype">
								<option value=""><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">开</option>
								<option value="2">关</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('level'); ?></th>
						<td>
							<input type="text" name="level" value="0">
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th colspan="3" style="text-align:left;">帮派战设置</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>帮派战</th>
						<td>
							<select name="faction_war_id">
								<option value="0"><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">白虎殿</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('server_is_open'); ?></th>
						<td>
							<select name="factionoptype">
								<option value="0"><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">开</option>
								<option value="2">关</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th colspan="3" style="text-align:left;">阵营战设置</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php echo Lang('server_is_open'); ?></th>
						<td>
							<select name="campoptype">
								<option value="-1"><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">开</option>
								<option value="0">关</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th colspan="3" style="text-align:left;">魔王试炼设置</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php echo Lang('server_is_open'); ?></th>
						<td>
							<select name="optype">
								<option value="-1"><?php echo Lang('does_not_operation'); ?></option>
								<option value="1">开</option>
								<option value="0">关</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				<tbody>
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
				</tbody>
			</table>
	    </form>
	<!-- End form elements -->
</div>
</div>