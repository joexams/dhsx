<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var obj = $(this);
		var url = '<?php echo INDEX; ?>?m=report&c=data&v=ajax_compare_list';
		Ha.common.ajax(url, 'json', obj.serialize(), 'get', 'container', function(data){
			$("#flashChartPay").multiChart(data.list.pay); 
			$("#flashChartOnline").multiChart(data.list.online); 
		}, 1);
	});

	$('#get_search_submit').submit();

	$('.first_level_tab').on('click', 'a.comparetype', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		var type = $(this).attr('data-type');
		$('div[id^=table_column_]', $('#container')).each(function(){
			$(this).hide()
		});
		$('div[id^=toolbar_]', $('#container')).each(function(){
			$(this).hide()
		});
		$('#table_column_'+type).show();
		$('#toolbar_'+type).show();
		type = parseInt(type);
		switch(type) {
			case 0:

				break;
			case 1:
				changeData();
				break;
		}
	});

	$('#month').on('change', function(){
		changeData();
	});
});

function changeData()
{
	var month = $('#month').val();
	var url = '<?php echo INDEX; ?>?m=report&c=data&v=compare';
	Ha.common.ajax(url, 'html', 'doget=1&month='+month, 'get', 'container', function(data){
		$("#table_column_1").html(data); 
	}, 1);
}
</script>

<script type="text/template" id="cidtpl">
    <option value="${cid}">${fn} - ${name}</option>
</script>

<h2><span id="tt"><?php echo Lang('pay_online_compare'); ?></span></h2>
<div class="container" id="container">
	<div class="speed_result">
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:;" class="comparetype" data-type="0">趋势图</a></li>
	    		<li><a href="javascript:;" class="comparetype" data-type="1">详细数据</a></li>
	    	</ul>
	    </div>
	</div>

	<div class="toolbar" id="toolbar_0">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
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
	</div>

    <div class="column cf" id="table_column_0">
    	<div class="mod_tab_title first_level_tab">
            <ul>
                <li class="current"><a href="javascript:void(0);" data-type="0" class="trendtype">时段在线趋势图</a></li>
            </ul>
        </div>
    <div id="flashChartOnline"></div>
    	<div class="mod_tab_title first_level_tab">
            <ul>
                <li class="current"><a href="javascript:void(0);" data-type="0" class="trendtype">充值次数趋势图</a></li>
            </ul>
        </div>
    <div id="flashChartPay"></div>
    </div>

    <div class="toolbar" id="toolbar_1" style="display:none">
    	<div class="tool_date">
    		<div class="title cf">
    			<div class="tool_group">
    				<label>月份：</label>
    	        	<select name="month" class="ipt_select" id="month">
    	        		<?php foreach ($monthlist as $value): ?>
    	        			<option value="<?php echo $value ?>" <?php $value == $month ? 'selected' :'' ?>><?php echo $value ?></option>
    	        		<?php endforeach ?>
    	        	</select>
    			</div>
    		</div>
    	</div>
    </div>
    
    <div class="data-sheet table-group" id="table_column_1" style="display:none">
    </div>
</div>