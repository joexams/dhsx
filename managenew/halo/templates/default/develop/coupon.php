<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid1').val(), tblname = $.trim($('#table_name').val()), comment = $.trim($('#comment').val());
		var objform = $(this);
		$('#btnsubmit').attr('disabled', 'disabled');
		if (cid > 0 && tblname != '' && comment != '') {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=coupon&v=add_active',
				data: objform.serialize(),
				dataType: 'json',
				type: 'post',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success';
							document.getElementById('post_submit').reset();
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function(data) {

					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#btnsubmit').removeAttr('disabled');
		}
	});
	
	/**
	 * 追加
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#again_post_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid2').val(), tblname = $.trim($('#again_table').val()), num = isNaN(parseInt($('#again_num').val())) ? 0 : parseInt($('#again_num').val());
		var objform = $(this);
		$('#again_btnsubmit').attr('disabled', 'disabled');
		if (cid > 0 && tblname != '0' && num > 0) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=coupon&v=add_again_active',
				data: objform.serialize(),
				dataType: 'json',
				type: 'post',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							document.getElementById('again_post_submit').reset();
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#again_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function(data) {

					$('#again_btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#again_btnsubmit').removeAttr('disabled');
		}
	});

	/**
	 * 切换
	 * @return {[type]} [description]
	 */
	$('.first_level_tab').on('click', 'a.coupon', function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
		var index = $(this).attr('data-type');
		$('.onecolumn').children('div.content').not(':hidden').hide();
		$('.onecolumn').children('div.content').eq(index).show();
	});
});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('active_coupon') ?></span></a></li>
	</ul>
	<br class="clear">
	<ul class="first_level_tab">
		<li><a href="javascript:;" data-type="0" class="coupon active"><?php echo Lang('新增兑换券'); ?></a></li>
		<li><a href="javascript:;" data-type="1" class="coupon"><?php echo Lang('追加兑换券'); ?></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=report&c=coupon&v=add_active" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
						<td>
							<select name="cid" class="cid" id="cid1">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('表后缀'); ?></th>
						<td><input type="text" name="table_name" id="table_name" value="qq" onkeyup="$('#tblsuffix').html(this.value)">对应表名code_party_<span class="greentitle" id="tblsuffix">qq</span></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('表描述'); ?></th>
						<td><input type="text" name="comment" id="comment" value=""></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('generate_num'); ?></th>
						<td><input type="text" name="num" id="num" value="0">一次输入最高请勿超过100000，超过100000请提交后再追加</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="btncancel" class="button" style="display: none;" value="<?php echo Lang('edit_cancel'); ?>">
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
		<div class="content" style="display:none">
				<!-- Begin form elements -->
				<form name="again_post_submit" id="again_post_submit" action="<?php echo INDEX; ?>?m=report&c=coupon&v=add_again_active" method="post">
					<input type="hidden" name="doSubmit" value="1">
					<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
						<tbody>
						<tr>
							<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
							<td>
								<select name="cid" class="cid" id="cid2">
									<option value="0"><?php echo Lang('operation_platform') ?></option>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th style="width:100px">活动</th>
							<td>
								<select name="table_name" id="again_table" class="table_name">
									<option value="0">请选择活动</option>
									<?php foreach ($activelist as $key => $value) {
										echo '<option value="'.$value['table_name'].'">'.$value['table_comment'].'</option>';
									} ?>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th>追加数量</th>
							<td><input type="text" name="num" id="again_num" value="0">一次输入最高请勿超过100000，超过100000请提交后再追加</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td> 
								<p>
								<input type="submit" id="again_btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
								<input type="reset" id="again_btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
								</p>
							</td>
							<td>&nbsp;</td>
						</tr>
						</tbody>
					</table>
			    </form>
				<!-- End form elements -->
		</div>

		<div id="op_tips" style="display: none;width:100%"><p></p></div>
	</div>
</div>