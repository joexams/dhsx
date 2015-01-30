<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var dialog = dialog != undefined ? null : '';
var chart;
$(function(){
	var options = {
			chart: {
				borderWidth: 1,
				plotBorderColor: '#A47D7C',
          		plotBorderWidth: 1,
				type: 'line'
			},
			colors: [
				'#ED561B',
				'#058DC7',
				'#55BF3B',
				'#DDDF0D'
			],
			plotOptions:{
				spline: {
					shadow: false
				}
			},
			xAxis: {
				categories: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00','06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
						 '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
	            tickColor: 'green',
	            tickLength: 10,
	            tickWidth: 3,
	            tickPosition: 'inside'
	        },
			yAxis: {
		        title: {text: ''},
		        min: 0
		    },
			title: {
				text: ''
			},
			tooltip: {
				shared: true,
				crosshairs: true
			},
			series: [{
				lineWidth: 4,
				marker: {
					radius: 2
				}
			}]
		};

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var obj = $(this);
		$.ajax({
			url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_online_compare_list',
			data: obj.serialize(),
			dataType: 'json',
			success: function(data){
				if (data.status == 0){
					options.chart.renderTo = 'graph_wrapper';
					options.title.text = '<?php echo Lang('online_compare') ?>';
					var serieslen = options.series.length;
					if (serieslen > 1){
						options.series.splice(1,serieslen);
					}
					options.series[0].name = '今日在线';
					options.series[0].data = data.list.online.today;
					var opseries;
					if (data.list.online.day1.length > 0){
						opseries={"name": $('#date1').val(), lineWidth: 1.5, "data": data.list.online.day1};
						options.series.push(opseries);
					}
					if (data.list.online.day2.length > 0){
						opseries = {};
						opseries={"name": $('#date2').val(), lineWidth: 1, "data": data.list.online.day2};
						options.series.push(opseries);
					}
					chart = new Highcharts.Chart(options);

					options.chart.renderTo = 'graph_wrapper1';
					options.title.text = '<?php echo Lang('pay_count_compare') ?>';
					var serieslen = options.series.length;
					if (serieslen > 1){
						options.series.splice(1,serieslen);
					}

					options.series[0].name = '今日充值次数';
					options.series[0].data = data.list.paycount.today;
					var opseries;
					if (data.list.paycount.day1.length > 0){
						opseries={"name": $('#date1').val(), lineWidth: 1.5, "data": data.list.paycount.day1};
						options.series.push(opseries);
					}

					if (data.list.paycount.day2.length > 0){
						opseries = {};
						opseries={"name": $('#date2').val(), lineWidth: 1, "data": data.list.paycount.day2};
						options.series.push(opseries);
					}

					chart = new Highcharts.Chart(options);
				}else {
					$('#graph_wrapper').html('数据加载失败');
				}
				$('#btnsubmit').removeAttr('disabled');
			},
			error: function(er){
				$('#graph_wrapper').html('数据加载失败');
				$('#btnsubmit').removeAttr('disabled');
			}
		});
	});

	$('#get_search_submit').submit();

	function getMonthData(){
		var month = $('#month').val();
		$.ajax({
			url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_online_list',
			data: {month: month},
			dataType: 'json',
			success: function(data){
				if (data.status == 0){
					window.chart = new Highcharts.StockChart({
					        chart : {
					            renderTo : 'graph_wrapper2',
	            				borderWidth: 1,
	            				plotBorderColor: '#A47D7C',
	                      		plotBorderWidth: 1,
	            				type: 'line'
					        },
					        xAxis: {
					            tickColor: 'green',
					            tickLength: 10,
					            tickWidth: 3,
					            tickPosition: 'inside',
					            type: 'datetime',
					            dateTimeLabelFormats: {
					                minute: '%Y-%m-%d<br/>%H:%M',
					                hour: '%Y-%m-%d<br/>%H:%M',
					                day: '%Y<br/>%m-%d'
					            }
					        },
					        colors: [
					            '#ED561B',
					            '#058DC7',
					            '#55BF3B',
					            '#B5CA92'
					        ],
					        rangeSelector: {
					            buttonTheme: {
					                style: {
					                   color: '#039'
					               }
					            },
					            buttons: [{
					                type: 'day',
					                count: 1,
					                text: '1天'
					            }, {
					                type: 'day',
					                count: 3,
					                text: '3天'
					            }, {
					                type: 'day',
					                count: 6,
					                text: '6天'
					            }, {
					                type: 'all',
					                text: '所有'
					            }],
					            inputDateFormat: '%Y-%m-%d',
					            selected: 1
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
					        plotOptions: {
			                    series: {
			                    	cursor: 'pointer',
			                        events: {
			                            click: function(event) {
			                            	getMonthDay(event);
			                            }
			                        }
			                    }
			                },
					        title : {
					            text : month+'在线人数与充值次数对比'
					        },
					        exporting: {
					            enabled: false
					        },
					        series : [{
					            name : '在线人数',
					            data : data.list.online,
					            marker : {
					                enabled : true,
					                radius : 2
					            },
					            lineWidth: 4,
					            shadow: false
					        },{
					            name : '充值次数',
					            data : data.list.paycount,
					            marker : {
					                enabled : true,
					                radius : 2
					            },
					            lineWidth: 2,
					            shadow: false
					        }]
					    });
				}else {
					$('#graph_wrapper2').html('数据加载失败');
				}
			},
			error: function() {
				$('#graph_wrapper2').html('数据加载失败');
			}
		});
	}

	function getMonthDay(event){
    	var dateline = event.point.x, day=date('Y-m-d', dateline/1000);
    	if (typeof charts != 'undefined'){
    		if (charts.title.text == day){
    			return false;
    		}
    	}
		$.ajax({
			url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_online_list',
    	    data: 'day='+dateline,
    	    dataType: 'json',
			success: function(data){
				if (data.status == 0){
					window.charts = new Highcharts.StockChart({
					        chart : {
					            renderTo : 'graph_wrapper3',
	            				borderWidth: 1,
	            				plotBorderColor: '#A47D7C',
	                      		plotBorderWidth: 1,
	            				type: 'line'
					        },
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
							yAxis: {
		                        title: {text: ''},
		                        min: 0
		                    },
					        colors: [
					            '#ED561B',
					            '#058DC7',
					            '#55BF3B'
					        ],
					        rangeSelector: {
					            buttonTheme: {
					                style: {
					                   color: '#039'
					               }
					            },
					           	enabled: false,
					            inputDateFormat: '%Y-%m-%d',
					            selected: 1
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
					            text : day+'在线人数'
					        },
					        exporting: {
					            enabled: false
					        },
					        series : [{
					            name : day+'在线人数',
					            data : data.list,
					            marker : {
					                enabled : true,
					                radius : 2
					            },
					            lineWidth: 3,
					            shadow: false
					        }]
					    });
				}else {
					$('#graph_wrapper3').html('数据加载失败');
				}
			},
			error: function() {
				$('#graph_wrapper3').html('数据加载失败');
			}
		});
	}

	$('#month').on('change', function(){
		getMonthData();
	});

	$('.first_level_tab').on('click', 'a.trendtype', function(){
	    $('.first_level_tab .active').removeClass('active');
	    $(this).addClass('active');
	    var obj = $(this), type = $(this).attr('data-type');

	    if (type == 1){
	    	$('#column1').hide();
	    	$('#column2').show();

			if ($('#graph_wrapper2').children().is('div') == false) {
	    		getMonthData();
	    	}
	    }else {
	    	$('#column2').hide();
	    	$('#column1').show();

	    	if ($.trim($('#graph_wrapper').html()) == ''){
	    		$('#get_search_submit').submit();
	    	}
	    }
	});
});
</script>

<script type="text/template" id="cidtpl">
    <option value="${cid}">${fn} - ${name}</option>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_online_compare'); ?></span></a></li>
	</ul>
	<br class="clear">
    <ul class="first_level_tab">
        <li><a href="javascript:;" data-type="0" class="trendtype active">与今日对比</a></li>
        <li><a href="javascript:;" data-type="1" class="trendtype">详细数据</a></li>
    </ul>
    <div class="clear"></div>

    <div class="onecolumn" style="width: 100%">
    	<div id="column1">
			<div class="nav singlenav">
			    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=ajax_online_compare_list" method="get" name="form">
			    <ul class="nav_li">
			        <li class="nobg">
			            <p>
			            	日期1：
			                <input name="date1" type="text" id="date1" value="" onclick="WdatePicker()" size="20" readonly>
			                日期2：
			                <input name="date2" type="text" id="date2" value="<?php echo date('Y-m-d', time()-24*3600); ?>" onclick="WdatePicker()" size="20" readonly>  
			                <input type="submit" name="getsubmit" id="btnsubmit" value="与今日对比" class="button_link">
			                <input name="dogetSubmit" type="hidden" value="1">
			            </p>
			        </li>
			    </ul>
			    </form>
			</div>
			<div class="content graph_wrapper" id="graph_wrapper">数据加载中，请稍候...</div>
			<div class="content graph_wrapper" id="graph_wrapper1">数据加载中，请稍候...</div>
		</div>

		<div id="column2" style="display:none">
			<div class="nav singlenav">
				<ul class="nav_li">
				    <li class="nobg">
				        <p>
				        	<select name="month" id="month">
				        		<?php foreach ($data['monthlist'] as $key => $value){ ?>
				        		<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
				        		<?php } ?>
				        	</select>
				        </p>
				    </li>
				</ul>
			</div>

			<div class="content graph_wrapper" id="graph_wrapper2">数据加载中，请稍候...</div>
			<div class="content graph_wrapper" id="graph_wrapper3"></div>
		</div>
	</div>
</div>

<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript" src="static/js/highstock.js"></script>
<script type="text/javascript" src="static/js/exporting.js"></script>
