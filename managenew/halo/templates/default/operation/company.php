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
	 * 获取对应信息
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var obj = $(this), cid = $(this).val();
		if (cid > 0){
			$.ajax({
			    url: '<?php echo INDEX; ?>?m=develop&c=company&v=ajax_info',
			    data: {cid: cid, format: 'json'},
			    dataType: 'json',
			    success: function (data) {
			       if (data.status == 0){
			       		$('#game_text').val(data.info.game_text);
			       		for(var i=0; i<8; i++){
			       			if (data.info.link[i] != ''){
			       				$('#submit_area').find('input[name^="link"]').eq(i).val(data.info.link[i]);
			       			}else {
			       				$('#submit_area').find('input[name^="link"]').eq(i).val('');
			       			}
			       		}
			       }
			    },
			    cache: false
			});
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val();
		if (cid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=company&v=setting',
				data: objform.serialize()+'&cid='+cid,
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
		}else {
			$('#btnsubmit').removeAttr('disabled');
		}
	});
});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('company_setting'); ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="content" id="submit_area">
		<form id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=company&a=setting" method="post">
		<div id="op_tips" style="display: none;"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			<tr class="selected">
				<th colspan="3"><?php echo Lang('company_info_setting') ?></th>
			</tr>
			</thead>
			<tr>
				<th style="width:10%"><?php echo Lang('company_title') ?></th>
				<td style="width:40%">
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('company_name') ?></option>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_game_description') ?></th>
				<td><input type="text" name="game_text" id="game_text" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_website') ?></th>
				<td><input type="text" name="link[]" id="website" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_bbs_url') ?></th>
				<td><input type="text" name="link[]" id="bbs_url" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_pay_url') ?></th>
				<td><input type="text" name="link[]" id="pay_url" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_vip_url') ?></th>
				<td><input type="text" name="link[]" id="pay_url" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_gm_url') ?></th>
				<td><input type="text" name="link[]" id="gm_url" value="" style="width: 90%;"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_fav_title') ?></th>
				<td><input type="text" name="link[]" id="fav_title" value="" style="width: 50%;">  <?php echo Lang('company_fav_title_tips') ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_fav_url') ?></th>
				<td><input type="text" name="link[]" id="fav_url" value="" style="width: 50%;">  <?php echo Lang('company_fav_url_tips') ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php echo Lang('company_fcm_url') ?></th>
				<td><input type="text" name="link[]" id="fcm_url" value="" style="width: 50%;">  <?php echo Lang('company_fcm_url_tips') ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr class="selected">
				<th><?php echo Lang('company_url_args') ?></th>
				<td><?php echo Lang('company_url_args_tips'); ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<p>
						<input type="hidden" name="cid" value="<?php echo $data['info']['cid']; ?>">
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						<input type="reset" value="<?php echo Lang('reset'); ?>">
					</p>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		</form>
	</div>
</div>