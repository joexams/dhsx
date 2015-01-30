<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
	$(function() {
        /**
        * 运营平台
        */
        if (typeof global_companylist != 'undefined') {
            $('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
        }

        $('.first_level_tab').on('click', 'a.trendtype', function(){
            $('.first_level_tab .active').removeClass('active');
            $(this).addClass('active');
            var obj = $(this), type = $(this).attr('data-type'), cid = 0;
            
            if (type == 1){
                $('#singlenav2').show();
                $('#singlenav1').hide();
                $('#graph_wrapper1').hide();
                $('#graph_wrapper2').hide();
                $('#graph_wrapper3').show();
                $('#graph_wrapper4').show();

                if ($('#graph_wrapper3').children().is('div') == false){
                    var opendate = $('#opendate').val();
                    cid = $('#get_search_submit').find('select.cid').val();
                    var url = '<?php echo INDEX; ?>?m=report&c=pay&v=ajax_trend_list&type=1&cid='+cid+'&opendate='+opendate;
					$.getJSON(url, function(data) {
                        // Create the chart
                        if (data.status == 0){
                            var linetext = {};
							if (data.today.list.length > 0){
                                linetext = {title: opendate+'当日开服', 'name': '<?php echo Lang('income'); ?>'};
                                createChartLine('graph_wrapper4', linetext, data.today);
                            }
                            if (data.all.list.length > 0){
                                linetext = {title: opendate+'前所有开服(含当日)', 'name': '<?php echo Lang('income'); ?>'};
                                createChartLine('graph_wrapper3', linetext, data.all);
                            }
                        }
                    });
                }
            }else {
                $('#singlenav2').hide();
                $('#singlenav1').show();
                $('#graph_wrapper3').hide();
                $('#graph_wrapper4').hide();
                $('#graph_wrapper1').show();
                $('#graph_wrapper2').show();
                cid = $('#cid1').val()
                if ($('#graph_wrapper1').children().is('div') == false || cid > 0){
                    $.getJSON('<?php echo INDEX; ?>?m=report&c=pay&v=ajax_trend_list&cid='+cid, function(data) {
                        // Create the chart
                        if (data.status == 0){
                            var linetext = {};
                            if (data.all.list.length > 0){
                                linetext = {title: '当日总收入趋势图', 'name1': '当日总收入', 'name2': '平均每服收入', 'name3': '总服数'};
                                createChart('graph_wrapper1', linetext, data.all);
                            }

                            if (data.today.list.length > 0){
                                linetext = {title: '当日新服收入趋势图', 'name1': '<?php echo Lang('income'); ?>', 'name2': '合服', 'name3': '开服'};
                                createChart('graph_wrapper2', linetext, data.today);
                            }
                        }
                    });
                }
            }


        });

        $('.first_level_tab .trendtype').eq(0).click();
        $('#cid1').on('change', function(){
            var cid = $(this).val();
            if (cid > 0){
                 $('.first_level_tab .trendtype').eq(0).click();
            }
        });
        $('#get_search_submit').on('submit', function(e){
            e.preventDefault();
            $('#graph_wrapper3').html('');
            $('.first_level_tab .trendtype').eq(1).click();
        });


	});

    function createChart(eleid, linetext, data){
        window.chart = new Highcharts.StockChart({
                chart : {
                    renderTo : eleid,
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
                colors: [
                    '#ED561B',
                    '#058DC7',
                    '#55BF3B',
                    '#B5CA92',
                    '#7798BF', 
                    '#80699B',
                    '#3D96AE',
                    '#A47D7C',
                    '#DDDF0D'
                ],
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
                    data : data.list,
                    marker : {
                        enabled : true,
                        radius : 2
                    },
                    lineWidth: 4,
                    shadow: false,
                    id : 'dataseries'
                },
                {
                    name : linetext.name2,
                    lineWidth: 2,
                    shadow: false,
                    data : data.avglist
                },
                {
                    name : linetext.name3,
                    lineWidth: 1,
                    shadow: false,
                    data : data.totalservlist
                },
                {
                    type : 'flags',
                    data : data.flags,
                    onSeries: 'dataseries',
                    shape: 'squarepin'
                }]
            });
    }

    function createChartLine(eleid, linetext, data){
        window.chart = new Highcharts.StockChart({
                chart : {
                    renderTo : eleid,
                    borderWidth: 1,
                    plotBorderColor: '#A47D7C',
                    plotBorderWidth: 1,
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
                title : {
                    text : linetext.title
                },
                exporting: {
                    enabled: false
                },
                series : [{
                    name : linetext.name,
                    data : data.list,
                    marker : {
                        enabled : true,
                        radius : 2
                    },
                    id : 'dataseries'
                },
                {
                    type : 'flags',
                    data : data.flags,
                    onSeries: 'dataseries',
                    shape: 'squarepin'
                }]
            });
    }
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_trend') ?></span></a></li>
	</ul>
	<br class="clear">
    <ul class="first_level_tab">
        <li><a href="javascript:;" data-type="0" class="trendtype active">总收入趋势图</a></li>
        <li><a href="javascript:;" data-type="1" class="trendtype">带条件收入趋势图</a></li>
    </ul>
    <div class="clear"></div>
    <div class="onecolumn" style="width: 100%">
        <div class="nav singlenav" id="singlenav1">
            <ul class="nav_li">
                <li class="nobg">
                    <p>
                        <select name="cid" id="cid1" class="cid">
                            <option value="0"><?php echo Lang('company_platform'); ?></option>
                        </select>
                    </p>
                </li>
            </ul>
        </div>

        <div class="nav singlenav" id="singlenav2" style="display:none">
            <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=pay&v=ajax_trend_list" method="get" name="form">
            <ul class="nav_li">
                <li>
                    <p>
                        <select name="cid" class="cid">
                            <option value="0"><?php echo Lang('company_platform'); ?></option>
                        </select>
                        开服时间： 
                        <input name="opendate" type="text" id="opendate" value="<?php echo date('Y-m-d', time()-24*3600); ?>" onclick="WdatePicker()" size="20" readonly>  
                    </p>
                </li>
                <li class="nobg">
                    <p>
                        <input type="submit" name="getsubmit" id="btnsubmit" value="搜索" class="button_link">
                        <input name="dogetSubmit" type="hidden" value="1">
                    </p>
                </li>
            </ul>
            </form>
        </div>
        <div class="content graph_wrapper" id="graph_wrapper1">数据加载中...</div>
        <div class="content graph_wrapper" id="graph_wrapper2"></div>
        <div class="content graph_wrapper" id="graph_wrapper3">数据加载中...</div>
        <div class="content graph_wrapper" id="graph_wrapper4"></div>
    </div>
</div>
<script type="text/javascript" src="static/js/highstock.js"></script>
<script type="text/javascript" src="static/js/exporting.js"></script>
