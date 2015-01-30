<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	$.ajax({
		url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_daily_list',
		data: {isall: 1},
		dataType: 'json',
		success: function(data){
			if (data.status == 0){
                linetext = {title: '<?php echo Lang("data_active"); ?>', 'name1': '<?php echo Lang("register_count"); ?>', 'name2': '创建数', 'name3': '<?php echo Lang("max_online_count"); ?>', 'name4': '创建率'};
                createChart('graph_wrapper', linetext, data.list);
			}
		}
	});
});

function createChart(eleid, linetext, data){
    window.chart = new Highcharts.StockChart({
            chart : {
                renderTo : eleid
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
            xAxis: {
                tickColor: 'green',
                tickLength: 10,
                tickWidth: 3,
                tickPosition: 'inside',
                type: 'datetime',
                dateTimeLabelFormats: {
                    second: '%Y-%m-%d<br/>%H:%M:%S',
                    minute: '%Y-%m-%d<br/>%H:%M',
                    hour: '%Y-%m-%d<br/>%H:%M',
                    day: '%Y<br/>%m-%d',
                    week: '%Y<br/>%m-%d',
                    month: '%Y-%m',
                    year: '%Y'
                }
            },
            rangeSelector: {
                buttonTheme: {
                    style: {
                       color: '#039'
                   }
                },
                buttons: [{
                    type: 'month',
                    count: 1,
                    text: '1月'
                }, {
                    type: 'month',
                    count: 3,
                    text: '3月'
                }, {
                    type: 'month',
                    count: 6,
                    text: '6月'
                }, {
                    type: 'ytd',
                    text: '今年'
                }, {
                    type: 'year',
                    count: 1,
                    text: '1年'
                }, {
                    type: 'all',
                    text: '所有'
                }],
                inputDateFormat: '%Y-%m-%d',
                selected: 1
            },
            legend: {
                enabled: true,
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 100
            },
            scrollbar: {
                barBackgroundColor: 'gray',
                barBorderRadius: 7,
                barBorderWidth: 0,
                buttonBackgroundColor: 'gray',
                buttonBorderWidth: 0,
                buttonArrowColor: 'yellow',
                buttonBorderRadius: 7,
                rifleColor: 'yellow',
                trackBackgroundColor: 'white',
                trackBorderWidth: 1,
                trackBorderColor: 'silver',
                trackBorderRadius: 7
            },
            title : {
                text : linetext.title
            },
            exporting: {
                enabled: false
            },
            series : [{
                name : linetext.name1,
                data : data.register,
                marker : {
                    enabled : true,
                    radius : 2
                },
                shadow : false,
                lineWidth: 4,
                id : 'dataseries'
            },
            {
                name : linetext.name2,
                shadow : false,
                lineWidth: 2,
                data : data.create
            },
            {
                name : linetext.name3,
                shadow : false,
                lineWidth: 1.5,
                data : data.maxonline
            },
            {
                name : linetext.name4,
                shadow : false,
                lineWidth: 0.5,
                data : data.avgcreate,
                tooltip: {
    		    	pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}%</b><br/>',
    		    	valueDecimals: 2
    		    }
            }]
        });
}
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('data_active') ?></span></a></li>
	</ul>
	<br class="clear">
    <div class="content graph_wrapper" id="graph_wrapper"></div>

</div>
<script type="text/javascript" src="static/js/highstock.js"></script>
<script type="text/javascript" src="static/js/exporting.js"></script>