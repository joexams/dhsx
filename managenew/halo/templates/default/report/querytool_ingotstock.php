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
				url: '<?php echo INDEX; ?>?m=report&c=querytool&v=ingotstock',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data) {
					if (data.status == 0) {
						$('#totalpayingot').html(parseInt(data.totalpayingot));
						$('#overpayingot').html(data.overpayingot);
						$('#overgiveingot').html(data.overgiveingot);
						$('#search_view').show();
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
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('ingotstock'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('ingotstock'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('display'); ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=report&c=querytool&v=ingotstock" method="get">
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

	<div class="content" id="search_view" style="display:none">
		<div id="list_op_tips" style="display: none;width:100%"><p>正在努力加载中...</p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<th style="width:280px;">充值元宝（不含赠送）：</th>
					<td style="width:150px;" id="totalpayingot">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>充值元宝存量：</th>
					<td id="overpayingot">-</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th>赠送元宝存量：</th>
					<td id="overgiveingot">-</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>