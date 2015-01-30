<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	$('#post_pop_submit').on('submit', function(e){
		e.preventDefault();
		$('#pop_btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=company&v=setting',
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
						$('#pop_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#pop_btnsubmit').removeAttr('disabled');
				}
			});
	});
});
</script>

<div id="bgwrap">
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
			<form id="post_pop_submit" action="<?php echo INDEX; ?>?m=develop&c=company&a=setting" method="post">
			<div id="op_tips" style="display: none;"><p></p></div>
			<table class="global" width="600" cellpadding="0" cellspacing="0">
				<thead>
				<tr class="selected">
					<th colspan="2"><?php echo Lang('company_info_setting') ?></th>
				</tr>
				</thead>
				<tr>
					<td></td>
					<td>
						<p>
							<input type="hidden" name="cid" value="<?php echo $data['info']['cid']; ?>">
							<input type="hidden" name="doSubmit" value="1">
							<input type="submit" id="pop_btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="pop_cancelsubmit" style="cursor:pointer" onclick="dialog.close();" class="button" value="<?php echo Lang('cancel'); ?>">
						</p>
					</td>
				</tr>
				<tr>
					<th style="width: 20%"><?php echo Lang('company_platform_type'); ?>：</th>
					<td>
						<select name="type" id="type">
							<option value="1"<?php echo $data['info']['type'] == 1 ? ' selected' : '' ?>>1<?php echo Lang('company_platform_type_item') ?></option>
							<option value="2"<?php echo $data['info']['type'] == 2 ? ' selected' : '' ?>>2<?php echo Lang('company_platform_type_item') ?></option>
							<option value="3"<?php echo $data['info']['type'] == 3 ? ' selected' : '' ?>>3<?php echo Lang('company_platform_type_item') ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('company_name'); ?>：</th>
					<td><input type="text" name="name" id="name" value="<?php echo $data['info']['name']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_short_name'); ?>：</th>
					<td><input type="text" name="slug" id="slug" value="<?php echo $data['info']['slug']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_domain'); ?>：</th>
					<td><input type="text" name="web" id="web" value="<?php echo $data['info']['web']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_game_title'); ?>：</th>
					<td><input type="text" name="game_name" id="game_name" value="<?php echo $data['info']['game_name']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_pay_changer'); ?>：</th>
					<td>
						<select name="money_type" id="money_type">
							<option value="0"><?php echo Lang('no_setting') ?></option>
							<?php echo $data['money_type_select'] ?>
						</select>
						  <?php echo Lang('company_pay_changer_tips'); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('company_pay_coins_rate'); ?>：</th>
					<td><input type="text" name="coins_rate" id="coins_rate" value="<?php echo $data['info']['coins_rate']; ?>" style="width:20%">  <?php echo Lang('company_pay_coins_rate_tips') ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_test_num_setting') ?></th>
					<td><input type="text" name="t_player" id="t_player" value="<?php echo $data['info']['t_player'] ?>" style="width: 20%;"> <?php echo Lang('person') ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_communications_key') ?></th>
					<td><input type="text" name="key" id="key" value="<?php echo $data['info']['key'] ?>" style="width: 50%;">  <?php echo Lang('company_communications_key_tips') ?></td>
				</tr>
				<tr>
					<th style="vertical-align: top;"><?php echo Lang('company_allow_pay_ip') ?></th>
					<td>
						<textarea name="charge_ips" id="charge_ips" style="width: 40%;height: 80px;"><?php echo $data['info']['charge_ips']; ?></textarea>
						<br><?php echo Lang('company_allow_pay_ip_tips') ?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('company_game_description') ?></th>
					<td><input type="text" name="game_text" id="game_text" value="<?php echo $data['info']['game_text'] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_website') ?></th>
					<td><input type="text" name="link[]" id="website" value="<?php echo $data['info']['link'][0] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_bbs_url') ?></th>
					<td><input type="text" name="link[]" id="bbs_url" value="<?php echo $data['info']['link'][1] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_pay_url') ?></th>
					<td><input type="text" name="link[]" id="pay_url" value="<?php echo $data['info']['link'][2] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_vip_url') ?></th>
					<td><input type="text" name="link[]" id="pay_url" value="<?php echo $data['info']['link'][3] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_gm_url') ?></th>
					<td><input type="text" name="link[]" id="gm_url" value="<?php echo $data['info']['link'][4] ?>" style="width: 90%;"></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_fav_title') ?></th>
					<td><input type="text" name="link[]" id="fav_title" value="<?php echo $data['info']['link'][5] ?>" style="width: 50%;">  <?php echo Lang('company_fav_title_tips') ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_fav_url') ?></th>
					<td><input type="text" name="link[]" id="fav_url" value="<?php echo $data['info']['link'][6] ?>" style="width: 50%;">  <?php echo Lang('company_fav_url_tips') ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('company_fcm_url') ?></th>
					<td><input type="text" name="link[]" id="fcm_url" value="<?php echo $data['info']['link'][7] ?>" style="width: 50%;">  <?php echo Lang('company_fcm_url_tips') ?></td>
				</tr>
				<tr class="selected">
					<th><?php echo Lang('company_url_args') ?></th>
					<td><?php echo Lang('company_url_args_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('language') ?></th>
					<td><input type="text" name="locale" id="locale" value="<?php echo $data['info']['locale'] ?>" style="width: 50%;"></td>
				</tr>
				<tr>
					<td><?php echo Lang('timeoffset') ?></td>
					<td></td>
				</tr>
			</table>
			</form>
		<!-- End form elements -->
	</div>
</div>