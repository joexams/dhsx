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

	var options = {
			chart: {
				renderTo: 'graph_wrapper',
				/*borderWidth: 1,
				plotBorderColor: '#A47D7C',
          		plotBorderWidth: 1,*/
				type: 'line'
			},
			colors: [
				'#ED561B',
				'#058DC7',
				'#55BF3B',
				'#DDDF0D',
				'#7798BF', 
				'#80699B',
				'#3D96AE',
				'#A47D7C',
				'#B5CA92'
			],
			title: {
				text: ''
			},

			subtitle: {
				text: '<?php echo Lang('pay_compare') ?>'
			},
			yAxis: {
				title: {text:''},
				min: 0
			},

			xAxis: {
				categories: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00','06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
						 '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00']
			},
			tooltip: {
				shared: true,
				crosshairs: true
			},
			series: [{
				name: '<?php echo Lang('total_pay'); ?>',
				lineWidth: 4,
				shadow: false,
				marker: {
					radius: 1
				}
			}]
		};

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var obj = $(this);
		$.ajax({
			url: '<?php echo INDEX; ?>?m=report&c=pay&v=ajax_compare_list',
			data: obj.serialize(),
			dataType: 'json',
			success: function(data){
				if (data.status == 0){
					var serieslen = options.series.length;
					if (serieslen > 1){
						options.series.splice(1,serieslen);
					}
					
					options.series[0].data = data.list.today;
					var opseries;
					if (data.list.day1.length > 0){
						opseries={"name": $('#date1').val(), lineWidth: 2, shadow: false, "data": data.list.day1};
						options.series.push(opseries);
					}

					if (data.list.day2.length > 0){
						opseries = {};
						opseries={"name": $('#date2').val(), lineWidth: 1, shadow: false, "data": data.list.day2};
						options.series.push(opseries);
					}

					chart = new Highcharts.Chart(options);
				}
				$('#btnsubmit').removeAttr('disabled');
			},
			error: function(er){
				$('#btnsubmit').removeAttr('disabled');
			}
		});
	});

	$('#get_search_submit').submit();
});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_compare'); ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=pay&v=ajax_compare_list" method="get" name="form">
	    <ul class="nav_li">
	        <li>
	            <p>
	                <select name="cid" id="cid">
	                    <option value="0"><?php echo Lang('company_platform'); ?></option>
	                </select>
	                <?php echo Lang('date'); ?>1：
	                <input name="date1" type="text" id="date1" value="" onclick="WdatePicker()" size="20" readonly>
	                <?php echo Lang('date'); ?>2：
	                <input name="date2" type="text" id="date2" value="<?php echo date('Y-m-d', time()-24*3600); ?>" onclick="WdatePicker()" size="20" readonly>
	            </p>
	        </li>
	        <li class="nobg">
	            <p>
	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('compare_to_today'); ?>" class="button_link">
	                <input name="dogetSubmit" type="hidden" value="1">
	            </p>
	        </li>
	    </ul>
	    </form>
	</div>

	<div class="content graph_wrapper" id="graph_wrapper"></div>
</div>
<script type="text/javascript" src="static/js/highcharts.js"></script>
