<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	var chart;
	$('#discountid').on('change', function(){
		var discountid = $(this).val(),starttime = $("#starttime").val(),endtime = $("#endtime").val();
		if (discountid != '') {
			var charttitle = $(this).find('option:selected').text();
			$('#flashChart').html('数据加载中...');
			$('.trendtype').html(charttitle);
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=qqactivity&v=lottery',
				data: 'dogetSubmit=1&discountid='+discountid+'&starttime='+starttime+'&endtime='+endtime,
				dataType: 'json',
				success: function(data) {
					$('#chart_column').show();
					if (data.status == 1) {
						$('#flashChart').html(data.msg);
					}else {
						$("#flashChart").multiChart(data.list);
					}
				},
				error: function() {
					$('#flashChart').html('数据加载失败...');
				}
			});
		}
	});
});
</script>
<h2><span id="tt"><?php echo Lang('qqactivity_lottery'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
		<ul>
		    <li>
		        	<select name="discountid" id="discountid" class="ipt_select">
		        		<option value="0"><?php echo Lang('select').Lang('qqactivity_lottery'); ?></option>
		        		<?php foreach ($activitylist as $key => $value){ ?>
		        		<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
		        		<?php } ?>
		        	</select>
		        	<span style="padding-left:10px;font-size:16px;"><?php echo Lang('between_date'); ?>：</span>
		            <input type="text" name="starttime" id="starttime" class="ipt_txt_s" readonl onclick="WdatePicker()">
					-
					<input type="text" name="endtime" id="endtime" class="ipt_txt_s" readonl onclick="WdatePicker()">
		    </li>
		</ul>
		</div>
	</div>
	<div class="column cf" id="chart_column" style="display:none">
	<div class="speed_result">
        <div class="mod_tab_title first_level_tab">
            <ul>
                <li class="current"><a href="javascript:void(0);" data-type="0" class="trendtype"></a></li>
            </ul>
        </div>
    </div>
    <div class="column cf" id="chart_column">
    <div id="flashChart"></div>
    </div>
	</div>
</div>
