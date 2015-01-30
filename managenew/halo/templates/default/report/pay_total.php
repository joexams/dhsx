<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var type=0, typecount = 1;
$(function(){
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#companytpl').tmpl(global_companylist).appendTo('#cid');
	}
	/**
	 * 改变平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', 'input.company', function(){
		var obj	= $(this), cid = obj.val(), cname = obj.attr('data');
		if (obj.is(':checked')){
			if (cid > 0 && typeof global_serverlist != 'undefined'){
				var str = '<optgroup label="'+cname+'" id="optgroup_'+cid+'">';
				$.each(getServerByCid(cid), function(i,item){
					str += '<option value="'+item.sid+'" data="'+item.name+'">'+item.name+'：'+item.o_name+'</option>';
				});
				str += '</optgroup>';
				$('#sid').append(str);
			}
		}else {
			$('#optgroup_'+cid).remove();
		}
	});
	/**
	 * 所有时间
	 * @return {[type]} [description]
	 */
	$('#timeall').on('change', function(){
		var obj = $(this);
		if (obj.is(':checked')){
			$('input[name="starttime"]').val('');
			$('input[name="endtime"]').val('');
		}
	});
	/**
	 * 平台全选
	 * @return {[type]} [description]
	 */
	// $('#cidall').on('change', function(){
	// 	if ($(this).is(':checked')){
	// 		$('#cid input:checkbox').each(function(){
	// 			$(this).not("input:checked").attr('checked', 'checked');
	// 			$(this).change();
	// 		});
	// 	}else {
	// 		$('#cid input:checked').removeAttr('checked');
	// 		$('#sid').empty();
	// 	}
	// });
	/**
	 * 服务器全选
	 * @return {[type]} [description]
	 */
	// $('#sidall').on('change', function(){
	// 	if ($(this).is(':checked')){
	// 		$('#sid option').not('option:selected').attr('selected', 'selected');
	// 	}else {
	// 		$('#sid option:selected').removeAttr('selected');
	// 		$('#sid').focus();
	// 	}
	// });
	/**
	 * 搜索提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#get_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var obj = $(this);
		$.ajax({
			url: "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_total_list",
			data: obj.serialize(),
			dataType: 'json',
			success: function(data){
				$('#btnsubmit').removeAttr('disabled');
				if (data.status == 0){
					type = data.type;
					typecount = data.typecount;

					if ($('#timeall').is(':checked')){
						$('#pay_total_list_2').hide();
						$('#pay_total_list').show();

						$('#pay_total_list_left').empty();
						$('#pay_total_list_right').empty();
						$('#pay_total_list').empty();
						$('#time_list_tpl').tmpl(data.list, {index: function (item) {return $.inArray(item, data.list) + 1;}}).appendTo('#pay_total_list');

						var total = num = pcount = tarpu =0;
						$.each(data.list, function(i, item){
							total += parseInt(item.pay_amount);
							if (typeof item.pay_num != 'undefined'){
								num += parseInt(item.pay_num);
							}
							if (typeof item.pay_player_count != 'undefined'){
								pcount += parseInt(item.pay_player_count);
							}
							if (typeof item.arpu != 'undefined'){
								tarpu += parseFloat(item.arpu);
							}
						});

						$('#total_pay_amount').html(total);
						$('#total_pay_num').html(num);
						$('#total_pay_player_count').html(pcount);
						$('#total_arpu').html(changeTwoDecimal(tarpu));

					}else {
						$('#pay_total_list_2').show();
						$('#pay_total_list').hide();
						$('#pay_total_list_left').empty();
						$('#pay_total_list_right').empty();

						var str = str_r = str_r1 = str_r2 = elid = '', iid = 0;
						var amount = num = playercount = tarpu = 0;
						var datenum = data.datelist.length;
						if (data.list.length > 0){

							if (datenum > 0){
								str = '<tr id="datelist">';
								$.each(data.datelist, function(i, item){
									str = str + '<th>'+item+'</th>';
								});
								str = str + '</tr>';

								$('#pay_total_list_right').html(str);
								$('#pay_total_list_left').html('<tr><th colspan="2">&nbsp;</th></tr>');
							}

							$.each(data.list, function(i, item){
								setTimeout(function(){
									if (type == 1){
										elid = item.cid;
									}else {
										elid = item.cid+'_'+item.sid;
									}
									
									for (var key in data.datelist){
										if (item.gdate == data.datelist[key]){
											iid = key;
											break;
										}
									}

									if ($('#amount_'+elid).html() != null){
										$('#amount_'+elid+' td').eq(iid).html('<span class="orangetitle">'+item.pay_amount+'</span>');
										amount += item.pay_amount;
									}
									if ($('#times_'+elid).html() != null){
										$('#times_'+elid+' td').eq(iid).html(item.pay_num);	
										num += item.pay_num;
									}
									if ($('#num_'+elid).html() != null){
										$('#num_'+elid+' td').eq(iid).html(item.pay_player_count);
										playercount += item.pay_player_count;
									}
									if ($('#arpu_'+elid).html() != null){
										$('#arpu_'+elid+' td').eq(iid).html('<span class="greentitle">'+changeTwoDecimal(item.arpu)+'</span>');
										tarpu += item.arpu;
									}

									//存在就不再往下执行
									if ($('#item_'+elid).html() != null){
										return true;
									}

									str = str_r = str_r1 = str_r2 = '';

									str += '<tr id="item_'+elid+'"><td rowspan="'+typecount+'">';
									if (type == 1){
										str += cid_to_name(item.cid);
									}else {
										str += sid_to_name(item.sid);
									}
									str += '</td><th>金额</th></tr>';

									if (typeof item.pay_num != 'undefined'){
										str += '<tr><th><?php echo Lang('times'); ?></th></tr>';
										str_r = '<tr id="times_'+elid+'">';
									}
									if (typeof item.pay_player_count != 'undefined'){
										str += '<tr><th><?php echo Lang('person_num'); ?></th></tr>';
										str_r1 = '<tr id="num_'+elid+'">';
									}
									if (typeof item.arpu != 'undefined'){
										str += '<tr><th>ARPU</th></tr>';
										str_r2 = '<tr id="arpu_'+elid+'">';
									}
									$('#pay_total_list_left').append(str);

									str = '<tr id="amount_'+elid+'">';
									for (var j=0; j<datenum; j++){
										if (j == 0){
											str = str + '<td><span class="orangetitle">'+item.pay_amount+'</span></td>';
											str_r += str_r != '' ? '<td>'+item.pay_num+'</td>' : '';
											str_r1 += str_r1 != '' ? '<td>'+item.pay_player_count+'</td>' : '';
											str_r2 += str_r2 != '' ? '<td><span class="greentitle">'+changeTwoDecimal(item.arpu)+'</span></td>' : '';
										}else {
											str = str + '<td>&nbsp;</td>';
											str_r += str_r != '' ? '<td>&nbsp;</td>' : '';
											str_r1 += str_r1 != '' ? '<td>&nbsp;</td>' : '';
											str_r2 += str_r2 != '' ? '<td>&nbsp;</td>' : '';
										}
									}
									str = str + '</tr>';
									str_r += str_r != '' ? '</tr>' : '';
									str_r1 += str_r1 != '' ? '</tr>' : '';
									str_r2 += str_r2 != '' ? '</tr>' : '';

									str = str + str_r + str_r1 + str_r2;
									$('#pay_total_list_right').append(str);	
								}, 50); 
							});
						}

						// $('#list_left_tpl').tmpl(data.list, {index: function (item) {return $.inArray(item, data.list) + 1;}}).appendTo('#pay_total_list_left');
					}
				}else {

					$('#list_op_tips').attr('class', 'alert_warning');
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					obj.parent().parent('tr').remove();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( 2 * 1000 ) );
				}
			},
			error: function(){
				$('#list_op_tips').attr('class', 'alert_warning');
				$('#list_op_tips').children('p').html('数据加载失败...');
				$('#list_op_tips').fadeIn();
				setTimeout( function(){
					$('#list_op_tips').fadeOut();
				}, 4000);
				$('#btnsubmit').removeAttr('disabled');
			}
		});
	});
});

function sid_to_name(sid){
	return $('#sid option[value="'+sid+'"]').attr('data');
}

function cid_to_name(cid){
	return $('#cid input:checkbox[value="'+cid+'"]').attr('data');
}

function changeTwoDecimal(x)
{
	var f_x = parseFloat(x);
	if (isNaN(f_x))
	{
		return 0;
	}
	f_x = Math.round(f_x *100)/100;
	return f_x;
}
</script>

<script type="text/template" id="companytpl">
	<input type="checkbox" name="cid[]" value="${cid}" class="company" data="${name}">${name}  <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
</script>
<!--
<script type="text/template" id="list_left_tpl">
    {{if $item.index($item.data)==1}}
	<tr>
		<th colspan="2">&nbsp;</th>
	</tr>
	{{/if}}
	<tr>
		<td rowspan="${typecount}">{{if type == 1}}${cid_to_name(cid)}{{else}}${sid_to_name(sid)}{{/if}}</td>
		<th>金额</th>
	</tr>
	{{if typeof pay_num != 'undefined'}}
	<tr>
	  <th>次数</th>
	</tr>
	{{/if}}
	{{if typeof pay_player_count != 'undefined'}}
	<tr>
	  <th>人数</th>
	</tr>
	{{/if}}
	{{if typeof arpu != 'undefined'}}
	<tr>
	  <th>ARPU</th>
	</tr>
	{{/if}}
</script>

<script type="text/template" id="list_right_tpl">
<tr>
	<td>&nbsp;</td>
</tr>
{{if typeof pay_num != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
{{if typeof pay_player_count != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
{{if typeof arpu != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
</script>
-->
<script type="text/template" id="time_list_tpl">
{{if $item.index($item.data)==1}}
    <tr>
      <td rowspan="${typecount}" style="width:10%">总计</td>
      <th style="width:10%">金额</th>
      <td style="width:10%" id="total_pay_amount"></td>
      <td>&nbsp;</td>
    </tr>
    {{if typeof pay_num != 'undefined'}}
	<tr>
	  <th>次数</th>
	  <td id="total_pay_num">&nbsp;</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
	{{if typeof pay_player_count != 'undefined'}}
	<tr>
	  <th>人数</th>
	  <td id="total_pay_player_count">&nbsp;</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
	{{if typeof arpu != 'undefined'}}
	<tr>
	  <th>ARPU</th>
	  <td id="total_arpu">&nbsp;</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
{{/if}}

	<tr>
	  <td rowspan="${typecount}" style="width:10%">{{if type == 1}}${cid_to_name(cid)}{{else}}${sid_to_name(sid)}{{/if}}</td>
	  <th style="width:10%">金额</th>
	  <td style="width:10%">${pay_amount}</td>
      <td>&nbsp;</td>
	</tr>
	{{if typeof pay_num != 'undefined'}}
	<tr>
	  <th>次数</th>
	  <td>${pay_num}</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
	{{if typeof pay_player_count != 'undefined'}}
	<tr>
	  <th>人数</th>
	  <td>${pay_player_count}</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
	{{if typeof arpu != 'undefined'}}
	<tr>
	  <th>ARPU</th>
	  <td>{{if arpu == null}}-{{else}}${changeTwoDecimal(arpu)}{{/if}}</td>
      <td>&nbsp;</td>
	</tr>
	{{/if}}
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_total') ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="content" id="submit_area">
	<form id="get_submit" action="<?php echo INDEX; ?>?m=report&c=pay&v=ajax_total_list" method="get">
	<table class="global" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr style="text-align: left;">
			    <th><!-- <input type="checkbox" name="cidall" value="1" id="cidall"> --> <?php echo Lang('company_platform'); ?></th>
			    <th>&nbsp;</th>
			</tr>
		</thead>

		<tr>
			<td id="cid">
				&nbsp;
			</td>
			<td>&nbsp;</td>
		</tr>

		<thead style="text-align: left;">
			<tr>
				<th><!-- <input type="checkbox" name="sidall" value="1" id="sidall"> --> <?php echo Lang('server_list'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>

		<tr>
			<td>
				<select name="sid[]" multiple="multiple" id="sid" style="width:350px;height:200px;">

				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				时间范围：<input type="text" name="starttime" onclick="WdatePicker()" value="<?php echo date('Y-m-01'); ?>" style="width:100px" readonly> - 
						<input type="text" name="endtime" onclick="WdatePicker()" value="<?php echo date('Y-m-d'); ?>" style="width:100px" readonly>
						<input type="checkbox" name="timeall" id="timeall" value="1"><?php echo Lang('all_time');?>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				数据展示：<input type="checkbox" name="type[]" value="1">次数  <input type="checkbox" name="type[]" value="2">人数  <input type="checkbox" name="type[]" value="3">ARPU
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<p>
				<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
				</p>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	</form>
	<div id="list_op_tips" style="display: none;"><p></p></div>
	<br class="clear">
	<table class="global" width="100%" cellpadding="0" cellspacing="0" id="pay_total_list">

	</table>

	<table class="global" width="100%" cellpadding="0" cellspacing="0" id="pay_total_list_2" style="display:none">
		<tr>
			<td style="margin-right:0;padding-right:0;overflow-x:scroll;width:100px;">
				<table class="global gleft" width="100" style="max-width:100px;min-width:100px;" cellpadding="0" cellspacing="0" id="pay_total_list_left">
					
				</table>
			</td>
			<td style="margin-left:0;padding-left:0;max-width:1000px;overflow-x:scroll;">
				<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" id="pay_total_list_right">
					
				</table>
			</td>
		</tr>
	</table>
	</div>
</div>
