<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), sid = $('#sid').val(), cid = $('#cid').val();
		if (cid > 0 || sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=pay&v=consume';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0) {
					$('#consume').html(data.list.consume);
					$('#pay').html(data.list.pay);
					$('#rate').html(data.list.rate);
				}
			}, 1);
		}
	});
	$('#get_search_submit').submit();
});
</script>
<h2><span id="tt">消费充值比率</span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					<input name="cid" id="cid" type="hidden" value="<?php echo $cid ?>">
					<input name="sid" id="sid" type="hidden" value="<?php echo $sid ?>">
					<label><?php echo Lang('between_date'); ?>：<input type="text" class="ipt_txt_s" name="starttime" onclick="WdatePicker()" readonly>
						-
						<input name="endtime" type="text" id="endtime" value="" class="ipt_txt_s" onclick="WdatePicker()" readonly> 
					</label>
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
		    <th class="num">消费</th>
			<th class="num">充值金额</th>
			<th class="num">比率<span class="greentitle">(消费/充值)</span></th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="num"><strong id="consume" class="orangetitle">0</strong></td>
				<td class="num"><strong id="pay" class="orangetitle">0</strong></td>
				<td class="num"><strong id="rate" class="orangetitle">0</strong></td>
			</tr>
		</tbody>
		<tfoot>
			<tr class="sum">
		        <td></td>
		        <td><span class="bluetitle">仅统计付费玩家</span></td>
		        <td></td>
		    </tr></tfoot>
		</table>

		</div>
	</div>
</div>