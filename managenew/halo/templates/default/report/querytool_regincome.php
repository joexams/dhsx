<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	//展开
	$('#extentfold').on('click', function(){
		var hidden = '<?php echo Lang("hidden"); ?>', show = '<?php echo Lang("show"); ?>';
		var obj = $(this);
		$('#submit_area').toggle("normal", function(){
			if ($(this).is(':hidden')){
				obj.html(show);
			}else {
				obj.html(hidden);
			}
		});
	});

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
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	$('#get_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid').val(), sid = $('#sid').val();
		var objform = $(this);
		$('#btnsubmit').attr('disabled', 'disabled');
		if (cid > 0 && sid.length > 0) {
			$('#list_op_tips').attr('class', 'alert_info');
			$('#list_op_tips').fadeIn();
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=querytool&v=regincome',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data) {
					if (data.status == 0) {
            			$('.enddate').html($('#enddate').val());
						$('.regdate').html($('#regdate').val());
						$('#regincome_view').show();

						$('#regcount').html(data.regcount);
						$('#paycount').html(data.paycount);
						$('#amount').html(data.amount);

            			$('#arpu').html(data.arpu);
            			$('#regincome').html(data.regincome);
            			$('#penetration').html(data.penetration);
					}
					$('#list_op_tips').fadeOut();
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
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
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('regincome'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('regincome'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('display'); ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=report&c=querytool&v=regincome" method="get">
				<input type="hidden" name="doSubmit" value="1">
				<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
						<td>
							<select name="cid" id="cid">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
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
					<tr>
						<th><?php echo Lang('register_date'); ?></th>
						<td>
							<input type="text" name="regdate" id="regdate" readonl onclick="WdatePicker()" size="10">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('end_date'); ?></th>
						<td>
							<input type="text" name="enddate" id="enddate" readonl onclick="WdatePicker()" size="10">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('channel').'via'; ?></th>
						<td>
							<input type="text" name="source" size="10">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('search'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
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

	<div class="content" id="regincome_view" style="display:none">
		<div id="list_op_tips" style="display: none;width:100%"><p>正在努力加载中...</p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<th style="width:280px;"><span class="regdate"></span> 注册人数：</th>
					<td style="width:150px;" id="regcount">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>截止 <span class="enddate"></span> 付费人数：</th>
					<td id="paycount">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>截止 <span class="enddate"></span> 充值总额：</th>
					<td id="payamount">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>ARPU（充值总额/付费人数）：</th>
					<td id="arpu">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>注收比（充值总额/注册人数）：</th>
					<td id="regincome">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>渗透率（付费人数/注册人数）：</th>
					<td id="penetration">-</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
</div> 