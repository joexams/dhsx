<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
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
		var objform = $(this), sid = $('#sid').val(), cid = $('#cid').val();
		if (cid > 0 || sid > 0){
			if (sid == 0) {
				$('#channellist').html('<tr><td colspan="11">查询所有服务器，数据加载需较长时间，请耐心等待...</td></tr>');
			}
			var url = '<?php echo INDEX; ?>?m=report&c=data&v=channel';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0 && data.list.length>0){
					$('#channellist').empty().append($('#channellisttpl').tmpl(data.list)).show();
				}else {
					$('#channellist').html('<tr><td colspan="11" style="text-align: left">没有找到数据...。</td></tr>');
				}
			}, 1);
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
	});
	<?php if ($cid > 0 && $sid >0) { ?>
	$('#get_search_submit').submit();
	<?php } ?>
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
	</tr>
</script>


<h2><span id="tt"><?php echo Lang('channel_stat'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					<?php if ($cid > 0 && $sid >0) { ?>
						<input name="cid" id="cid" type="hidden" value="<?php echo $cid ?>">
						<input name="sid" id="sid" type="hidden" value="<?php echo $sid ?>">
					<?php }else { ?>
						<select name="cid" id="cid" class="ipt_select" style="width:100px;">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
		            	</select>
		            	<select name="sid" id="sid" class="ipt_select" style="width:120px;">
		            		<option value="0"><?php echo Lang('all_server') ?></option>
		            	</select>
					<?php } ?>
					<label><?php echo Lang('channel'); ?>：<input type="text" class="ipt_txt" name="source"></label>
					<label><?php echo Lang('between_date'); ?>：<input type="text" class="ipt_txt_s" name="starttime" onclick="WdatePicker()" readonly>
						-
						<input name="endtime" type="text" id="endtime" value="" class="ipt_txt_s" onclick="WdatePicker()" readonly> 
					</label>
					<select name="type" class="ipt_select" style="width:70px;">
	                	<option value=""><?php echo Lang('channel'); ?></option>
	                	<option value="1"><?php echo Lang('rollserver'); ?></option>
	                </select>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="查询" id="query"> 
					<input type="reset" class="btn_rst" value="重置" id="reset">
				</div>
			</div>
			</form>
		</div>		
	</div>
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th><?php echo Lang('channel'); ?></th>
			<th><?php echo Lang('register_count'); ?></th>
			<th><?php echo Lang('create_num'); ?></th>
			<th><?php echo Lang('create_rate'); ?></th>
			<th>Lv.2/<?php echo Lang('percent'); ?></th>
			<th>Lv.10/<?php echo Lang('percent'); ?></th>
			<th>Lv.20/<?php echo Lang('percent'); ?></th>
			<th>Lv.30/<?php echo Lang('percent'); ?></th>
			<th>Lv.40/<?php echo Lang('percent'); ?></th>
			<th><?php echo Lang('pay_person_num'); ?></th>
			<th><?php echo Lang('pay_money'); ?></th>
		</tr>
		</thead>
		<tbody id="channellist">
			<tr><td colspan="11" style="text-align: left">请先输入您要查询的条件进行查询。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>