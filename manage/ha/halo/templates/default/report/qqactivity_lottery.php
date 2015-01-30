<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	var chart;
	$('#packageid').on('change', function(){
		var packageid = $(this).val();
		if (packageid > 0) {
			var charttitle = $(this).find('option:selected').text();
			$('#graph_wrapper').html('数据加载中...');
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=qqactivity&v=lottery',
				data: 'dogetSubmit=1&packageid='+packageid,
				dataType: 'json',
				success: function(data) {
					if (data.status == 1) {
						$('#graph_wrapper').html('数据加载失败...');
					}else {
						var type = 'bar';
						if (packageid >= 1034 && packageid <= 1036) {
							type = 'line';
						}
						chart = new Highcharts.Chart({
					        chart: {
					            renderTo: 'graph_wrapper',
					            type: type
					        },
					        title: {
					            text: charttitle
					        },
					        colors: [
					            '#ED561B',
					            '#058DC7',
					            '#55BF3B'
					        ],
					        xAxis: {
					            categories: data.categories,
					            title: {text:''}
					        },
					        yAxis: {
					            min: 0,
								title: {text: ''}
					        },
							plotOptions: {
		                        bar: {
		                            dataLabels: {
		                                enabled: true
		                            }
		                        }
		                    },
					        credits: {
					            enabled: false
					        },
					        series: [{
								name: charttitle,
					            data: data.datas
					        }]
					    });
					}
				},
				error: function() {
					$('#graph_wrapper').html('数据加载失败...');
				}
			});
		}
	});
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('qqactivity_lottery') ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="nav singlenav">
		<ul class="nav_li">
		    <li class="nobg">
		        <p>
		        	<select name="packageid" id="packageid">
		        		<option value="0"><?php echo Lang('select').Lang('qqactivity_lottery'); ?></option>
		        		<?php foreach ($activitylist as $key => $value){ ?>
		        		<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
		        		<?php } ?>
		        	</select>
		        </p>
		    </li>
		</ul>
	</div>

	<div class="content graph_wrapper" id="graph_wrapper"></div>
</div>
<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript" src="static/js/exporting.js"></script>
