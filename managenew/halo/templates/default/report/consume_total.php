<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var consume_type;
$(function(){
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
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	$('#get_submit').on('submit', function(e){
		e.preventDefault();
		$('#consumelist').html('<tr><td colspan="4">正在努力加载中...</td></tr>');
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=consume',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#consumelist').empty();
					if (data.status == 0){
						$('#extentfold').click();
						consume_type = data.type;
						var allnum = data.allnum, alltotal = data.alltotal, allingot = data.allingot;
						for(var key in data.list){
							data.list[key].pertotal = (data.list[key].total * 100/alltotal).toFixed(2) + '%';
							data.list[key].peringot = (data.list[key].ingot * 100/allingot).toFixed(2) + '%';
						}
						$('#consumelist_tpl').tmpl(data.list).appendTo('#consumelist');

						if (allnum > 0 && alltotal > 0){
							var strHtml = [
									'<tr>',
										'<td>&nbsp;</td>',
										'<td>'+allnum+'</td>',
										'<td>'+alltotal+'</td>',
										'<td><span class="orangetitle">'+allingot+'</span></td>',
										'<td>&nbsp;</td>',
									'</tr>'
								].join('');
							$('#consumelist').prepend(strHtml);
						}
					}
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function(ex){
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
	});

});

function typeid_to_name(typeid){
	if (consume_type.length > 0 && typeid > 0){
		for(key in consume_type){
			if (consume_type[key].id == typeid){
				return consume_type[key].name;
			}
		}
	}
	return '';
}
</script>

<script type="text/template" id="consumelist_tpl">
	<tr>
		<th>${typeid_to_name(type)}</th>
		<td>${num}</td>
		<td>${total}<span class="graptitle">(${pertotal})</span></td>
		<td><span class="orangetitle">${ingot}</span><span class="graptitle">(${peringot})</span></td>
		<td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('consume_total'); ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('consume_total'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('display'); ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
			<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=consume" method="post">
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
						<th style="width:100px;"><?php echo Lang('type'); ?></th>
						<td>
							<select name="typeid" id="typeid">
								<option value="0"><?php echo Lang('register_date_tips') ?></option>
								<?php foreach ($typelist as $key => $value) { ?>
								<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
								<?php } ?>
							</select>
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
							<input type="text" name="start_level" size="5">
							-
							<input type="text" name="end_level" size="5">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('between_date'); ?></th>
						<td>
							<input type="text" name="starttime" readonl onclick="WdatePicker()">
							-
							<input type="text" name="endtime" readonly onclick="WdatePicker()">
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
					<th style="width:20%"><?php echo Lang('type'); ?></th>
					<th style="width:10%"><?php echo Lang('person_num'); ?></th>
					<th style="width:10%"><?php echo Lang('times'); ?></th>
					<th style="width:10%"><?php echo Lang('ingot'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="consumelist">
			</tbody>
		</table>
	</div>
</div>
