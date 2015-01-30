<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	//添加
	$('#post_pop_submit').on('submit', function(e){
		e.preventDefault();
		$('#pop_btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=setting',
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
				}
			});
	});
	//测试数据库连接
	$('#submit_area').on('click', 'a.test_db_connect', function(){
		var dbhost = $('#db_server').val(), dbroot = $('#db_root').val(), dbname = $('#db_name').val(), dbpwd = $('#db_pwd').val();
		$.ajax({
		  url: '<?php echo INDEX; ?>?m=develop&c=server&v=test_db_connect',
		  data: 'dbhost='+dbhost+'&dbroot='+dbroot+'&dbname='+dbname+'&dbpwd='+dbpwd,
		  dataType: 'json',
		  success: function(data){
		  	var alertclassname = '', time = 2;
		  	switch (data.status){
		  		case 0: alertclassname = 'alert_success'; break;
		  		case 1: alertclassname = 'alert_error'; break;
		  	}
		  	$('#connect_tips').attr('class', alertclassname);
		  	$('#connect_tips').html(data.msg);
		  }
		});
	});
});


</script>

<div id="bgwrap">
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
			<form id="post_pop_submit" action="<?php echo INDEX; ?>?m=develop&c=server&a=setting" method="post">
			<table class="global" width="600" cellpadding="0" cellspacing="0">
				<thead>
				<tr class="selected">
					<th colspan="2"><?php echo Lang('server_base_info'); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th style="width: 15%"><?php echo Lang('server_o_name'); ?>：</th>
					<td><input type="text" name="name" id="name" value="<?php echo $data['info']['name'] ? $data['info']['name'] : $data['slug']; ?>" onblur="$('#db_name').val('gamedb_'+ this.value);" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_name'); ?>：</th>
					<td><input type="text" name="o_name" id="o_name" value="<?php echo $data['info']['o_name']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_game_url'); ?>：</th>
					<td>
						<textarea name="server" id="server" rows="3" cols="50"><?php echo $data['info']['server']; ?></textarea>
						<?php echo Lang('server_game_url_tips'); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('server_date'); ?>：</th>
					<td><input type="text" name="open_date" id="open_date" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})" value="<?php echo $data['info']['open_date']; ?>" style="width:90%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_version'); ?>：</th>
					<td>
						<select name="server_ver" id="server_ver">
							<option value="0"><?php echo Lang('no_setting'); ?></option>
							<?php echo $data['versionstring'] ?>
						</select>
					</td>
				</tr>
				<tr>
					<th style="width: 15%"><?php echo Lang('server_combined'); ?>：</th>
					<td>
						<select name="combined_to" id="combined_to">
							<?php if ($data['info']['combined_to'] > 0) { ?>
							<option value="<?php echo $data['info']['combined_to']; ?>"><?php echo $data['info']['combined_name'].'-'.$data['info']['combined_o_name']; ?></option>
							<?php } ?>
							<option value="0"><?php echo Lang('server_no_combined'); ?></option>
						</select>
						<?php echo Lang('server_combined_tips'); ?>
					</td>
				</tr>
				</tbody>
				<thead>
				<tr class="selected">
					<th colspan="2"><?php echo Lang('server_attr_title'); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th><?php echo Lang('server_is_open'); ?></th>
					<td><input type="radio" name="open" value="0"<?php echo $data['info']['open'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?>  <input type="radio" name="open" value="1" <?php echo $data['info']['open'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?>  <?php echo Lang('server_is_open_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_is_private'); ?></th>
					<td><input type="radio" name="private" value="0" <?php echo $data['info']['private'] == 0? ' checked': ''; ?>><?php echo Lang('private'); ?>  <input type="radio" name="private" value="1" <?php echo $data['info']['private'] == 1? ' checked': ''; ?>><?php echo Lang('public'); ?>  <?php echo Lang('server_is_private_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_is_test'); ?></th>
					<td><input type="radio" name="test" value="1" <?php echo $data['info']['test'] == 1? ' checked': ''; ?>><?php echo Lang('test'); ?>  <input type="radio" name="test" value="0" <?php echo $data['info']['test'] == 0? ' checked': ''; ?>><?php echo Lang('normal'); ?>  <?php echo Lang('server_is_test_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_test_player_num'); ?></th>
					<td><input type="text" name="test_player" id="test_player" style="width: 10%;" value="<?php echo $data['info']['test_player']; ?>"><?php echo Lang('server_test_player_num_item'); ?>    <?php echo Lang('server_test_player_num_tips'); ?></td>
				</tr>
				<tr class="selected">
					<td colspan="2"><?php echo Lang('server_active_setting'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_active_first_pay'); ?></th>
					<td><input type="radio" name="first_pay_act" value="0"<?php echo $data['info']['first_pay_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?>  <input type="radio" name="first_pay_act" value="1" <?php echo $data['info']['first_pay_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?>  <?php echo Lang('server_active_first_pay_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_active_level_order'); ?></th>
					<td><input type="radio" name="level_act" value="0"<?php echo $data['info']['level_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?>  <input type="radio" name="level_act" value="1" <?php echo $data['info']['level_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?>  <?php echo Lang('server_active_level_order_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_active_mission_order'); ?></th>
					<td><input type="radio" name="mission_act" value="0"<?php echo $data['info']['mission_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?>  <input type="radio" name="mission_act" value="1" <?php echo $data['info']['mission_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?>  <?php echo Lang('server_active_mission_order_tips'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_active_new_card'); ?></th>
					<td><input type="radio" name="new_card_act" value="0"<?php echo $data['info']['new_card_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?>  <input type="radio" name="new_card_act" value="1" <?php echo $data['info']['new_card_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?>  <?php echo Lang('server_active_new_card_tips'); ?></td>
				</tr>
				</tbody>
				<thead>
				<tr class="selected">
					<th colspan="2"><?php echo Lang('server_api_port_pwd'); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th><?php echo Lang('server_api_address'); ?></th>
					<td>
						<select name="api_server" id="api_server" onchange="$('#combined_server').val($(this).val() != '0' ? $(this).val(): '')">
							<option value="0"><?php echo Lang('no_setting'); ?></option>
							<?php echo $data['apistring'] ?>
						</select>
						<input type="text" name="combined_server" id="combined_server" value="<?php echo $data['info']['combined_server']; ?>">
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('port'); ?></th>
					<td><input type="text" name="api_port" id="api_port" value="<?php echo $data['info']['api_port']; ?>" style="width: 20%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('password'); ?></th>
					<td><input type="text" name="api_pwd" id="api_pwd" value="<?php echo $data['info']['api_pwd'] ? $data['info']['api_pwd'] : 'ybybyb'; ?>" style="width: 20%"></td>
				</tr>
				</tbody>
				<thead>
				<tr class="selected">
					<th colspan="2"><?php echo Lang('server_db_slave'); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th><?php echo Lang('server_db_address'); ?></th>
					<td>
						<select name="db_server" id="db_server">
							<option value="0"><?php echo Lang('no_setting'); ?></option>
							<?php echo $data['dbstring'] ?>
						</select>
						<a href="javascript:;" class="test_db_connect"><?php echo Lang('server_db_test'); ?></a>
						<span id="connect_tips"></span>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang('server_db_name'); ?></th>
					<td><input type="text" name="db_name" id="db_name" value="<?php echo $data['info']['db_name'] ? $data['info']['db_name'] : 'gamedb_'.$data['slug']; ?>" style="width: 20%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('server_db_root'); ?></th>
					<td><input type="text" name="db_root" id="db_root" value="<?php echo $data['info']['db_root']; ?>" style="width: 20%"></td>
				</tr>
				<tr>
					<th><?php echo Lang('password'); ?></th>
					<td><input type="text" name="db_pwd" id="db_pwd" value="<?php echo $data['info']['db_pwd']; ?>" style="width: 20%"></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<p>
							<input type="hidden" name="sid" value="<?php echo $data['info']['sid']; ?>">
							<input type="hidden" name="doSubmit" value="1">
							<input type="submit" id="pop_btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="pop_cancelsubmit" style="cursor:pointer" onclick="dialog.close();" class="button" value="<?php echo Lang('close'); ?>">
						</p>
					</td>
				</tr>
				</tbody>
			</table>
			<div id="op_tips" style="display: none;"><p></p></div>
			</form>
		<!-- End form elements -->
	</div>
</div>
