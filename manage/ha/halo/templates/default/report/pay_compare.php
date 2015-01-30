<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var chart;
$(function(){
	/**
	* 运营平台
	*/
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var obj = $(this);
		var url = '<?php echo INDEX; ?>?m=report&c=pay&v=ajax_compare_list';
		Ha.common.ajax(url, 'json', obj.serialize(), 'get', 'container', function(data){
			// if (data.status == 0 && data.count > 0) {
				$('#chart_column').show();
				$("#flashChart").multiChart(data.list);
			// }
		}, 1);
	});

	$('#get_search_submit').submit();
});
</script>


<h2><span id="tt"><?php echo Lang('pay_compare') ?></span></h2>
<div class="container" id="container">
	<div class="tool_date cf">
	    <div class="title cf">
	    	<form id="get_search_submit" method="get" name="form">
	        <div class="tool_group" id="div_date">
	        	<select name="cid" id="cid" class="ipt_select">
	        	    <option value="0"><?php echo Lang('company_platform'); ?></option>
	        	</select>
	        	<label><?php echo Lang('date'); ?>1：</label>
	        	<input name="date1" type="text" id="date1" value="" onclick="WdatePicker()" class="ipt_txt_s" readonly>
	        	<label><?php echo Lang('date'); ?>2：</label>
	        	<input name="date2" type="text" id="date2" value="<?php echo date('Y-m-d', time()-24*3600); ?>" onclick="WdatePicker()"  class="ipt_txt_s" readonly>

	        	<input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('compare_to_today'); ?>" class="btn_sbm">
	        	<input name="dogetSubmit" type="hidden" value="1">
	        </div>
	    	</form>
	    </div>
	</div>
    <div class="column cf" id="chart_column" style="display:none">
	<div class="title">趋势图</div>	
    <div id="flashChart"></div>
    </div>
</div>