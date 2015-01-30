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
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	$('#get_submit').on('submit', function(e){
		e.preventDefault();
		$('#lossratelist').html('<tr><td colspan="11"><?php echo Lang('loading'); ?></td></tr>');
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val(), daynum = !isNaN(parseInt($('#daynum').val())) ? parseInt($('#daynum').val()) : 0;
		if (cid > 0 && sid != null && daynum > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=ajax_lossrate_list',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#lossratelist').empty();
					if (data.status == 0 && data.list != null){
						$('#extentfold').click();

						$('#lossratelisttpl').tmpl(data.list).appendTo('#lossratelist');
					}
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function(ex){
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>'+(daynum <= 0 ? '<?php echo Lang('input_daynum_tips'); ?>' : ''));
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#lossratelist').empty();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
	});
});
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="servertpl">
	<option value="${sid}" data-ver="${server_ver}">${name}-${o_name}</option>
</script>

<script type="text/template" id="lossratelisttpl">
<tr>
	<td>${level}</td>
	<td>${num>0?num:'-'}</td>
	<td>${lossnum>0?lossnum:'-'}</td>
	<td>${lossnum >0 ? (lossnum*100/num).toFixed(2) + '%' : '-'}</td>
	<td><span style="color:#058DC7">${paynum>0?paynum:'-'}</span></td>
	<td>${losspaynum>0?losspaynum:'-'}</td>
	<td>${losspaynum > 0 ? (losspaynum*100/paynum).toFixed(2) + '%' : '-'}</td>
	<td>${vipnum>0?vipnum:'-'}</td>
	<td>${lossvipnum>0?lossvipnum:'-'}</td>
	<td>${lossvipnum>0 ? (lossvipnum*100/vipnum).toFixed(2) + '%' : '-'}</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('lossrate_stat'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('lossrate_stat'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('display'); ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=ajax_lossrate_list" method="post">
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
						<th><?php echo Lang('between_day'); ?></th>
						<td><input type="text" name="daynum" value="5" id="daynum" size="3"><?php echo Lang('between_day_tips'); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('register_date'); ?></th>
						<td>
							<input type="text" name="starttime" readonl onclick="WdatePicker()">
							-
							<input type="text" name="endtime" readonly onclick="WdatePicker()">
							<?php echo Lang('register_date_tips'); ?>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('level'); ?></th>
						<td>
							<input type="text" name="start_level" size="5">
							-
							<input type="text" name="end_level" size="5">
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
						<td>&nbsp;</td>
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
				<div id="op_tips" style="display: none;width:100%"><p></p></div>
		    </form>
			<!-- End form elements -->
		</div>
	</div> 
	<br class="clear">

	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:5%"><?php echo Lang('level'); ?></th>
					<th style="width:5%"><?php echo Lang('create_num'); ?></th>
					<th style="width:5%"><?php echo Lang('losser_num'); ?></th>
					<th style="width:5%"><?php echo Lang('loss_rate'); ?></th>
					<th style="width:5%"><?php echo Lang('pay_person_num'); ?></th>
					<th style="width:5%"><?php echo Lang('pay').Lang('losser_num'); ?></th>
					<th style="width:5%"><?php echo Lang('pay').Lang('loss_rate'); ?></th>
					<th style="width:5%">VIP<?php echo Lang('person_num'); ?></th>
					<th style="width:5%">VIP<?php echo Lang('losser_num'); ?></th>
					<th style="width:5%">VIP<?php echo Lang('loss_rate'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="lossratelist">
			</tbody>
		</table>
	</div>
</div>