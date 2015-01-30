<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var max;
$(function(){
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this), sid = $('#sid').val(), cid = $('#cid').val();
		if (cid > 0 || sid > 0){
			if (sid == 0) {
				$('#channellist').html('<tr><td colspan="12">查询所有服务器，数据加载需较长时间，请耐心等待...</td></tr>');
			}else {
				$('#channellist').html('<tr><td colspan="12">数据正在加载中...</td></tr>');
			}
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=ajax_channel_list',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#channellist').empty();
					if (data.status == 0 && data.list.length>0){
						$('#channellisttpl').tmpl(data.list).appendTo('#channellist');
					}
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function(){
					$('#channellist').html('<tr><td colspan="12">数据加载失败，可能数据量过大加载超时...</td></tr>');
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>');
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

<script type="text/template" id="channellisttpl">
	<tr>
		<td>{{if typeof source != 'undefined'}}${source}{{else}}&nbsp{{/if}}</td>
		<td>${num}</td>
		<td>${createnum}</td>
		<td>${createnum>0?(createnum*100/num).toFixed(2)+'%':'-'}</td>
		<td>${level2}<span class="graptitle">${level2>0?'('+(level2*100/createnum).toFixed(2)+'%)':'-'}</span></td>
		<td>${level10}<span class="graptitle">${level10>0?'('+(level10*100/createnum).toFixed(2)+'%)':'-'}</span></td>
		<td>${level20}<span class="graptitle">${level20>0?'('+(level20*100/createnum).toFixed(2)+'%)':'-'}</span></td>
		<td>${level30}<span class="graptitle">${level30>0?'('+(level30*100/createnum).toFixed(2)+'%)':'-'}</span></td>
		<td>${level40}<span class="graptitle">${level40>0?'('+(level40*100/createnum).toFixed(2)+'%)':'-'}</span></td>
		<td>${paynum>0?paynum:'-'}</td>
		<td><span class="orangetitle">${amount>0? amount/10 : '-'}</span></td>
		<td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('channel_stat'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=ajax_channel_list" method="get" name="form">
	    <ul class="nav_li">
	        <li class="nobg">
	            <p>
	            	<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
	            	</select>
	            	<select name="sid" id="sid">
	            		<option value="0"><?php echo Lang('all_server') ?></option>
	            	</select>
	            	<?php echo Lang('channel'); ?>：
	            	<input name="source" type="text" id="source" value="" size="10">
	            	<?php echo Lang('between_date') ?>：
	                <input name="starttime" type="text" id="starttime" value="" onclick="WdatePicker()" size="15" readonly>
	                -
	                <input name="endtime" type="text" id="endtime" value="" onclick="WdatePicker()" size="15" readonly> 
	                <select name="type" id="">
	                	<option value=""><?php echo Lang('channel'); ?></option>
	                	<option value="1"><?php echo Lang('rollserver'); ?></option>
	                </select>
	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
	                <input name="dogetSubmit" type="hidden" value="1">
	            </p>
	        </li>
	    </ul>
	    </form>
	</div>  
	<br class="clear">
	<div class="content">
		<div id="op_tips" style="display: none;width:100%"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:5%"><?php echo Lang('channel'); ?></th>
					<th style="width:5%"><?php echo Lang('register_count'); ?></th>
					<th style="width:5%"><?php echo Lang('create_num'); ?></th>
					<th style="width:5%"><?php echo Lang('create_rate'); ?></th>
					<th style="width:5%">Lv.2/<?php echo Lang('percent'); ?></th>
					<th style="width:5%">Lv.10/<?php echo Lang('percent'); ?></th>
					<th style="width:5%">Lv.20/<?php echo Lang('percent'); ?></th>
					<th style="width:5%">Lv.30/<?php echo Lang('percent'); ?></th>
					<th style="width:5%">Lv.40/<?php echo Lang('percent'); ?></th>
					<th style="width:5%"><?php echo Lang('pay_person_num'); ?></th>
					<th style="width:5%"><?php echo Lang('pay_money'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="channellist">
			</tbody>
		</table>
	</div>
</div>
