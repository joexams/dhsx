<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
function sid_to_name(sid) {
	if (typeof global_serverlist != 'undefined') {
		for(var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
			}
		}
	}
	return '-';
}
function onlinerefresh() {
	$.ajax({
		url: '<?php echo INDEX; ?>?m=report&c=data&v=online',
		success: function(data) {
			$('#online').html(data);
			/*if ($('#online').is('span') == false) {
				clearInterval(sh);
			}*/
		},
		error: function() {
			$('#online').html('0');
		}
	});
}
var dialog;
$(function() {
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#total_platform').html(global_companylist.length);
		}

		if (typeof global_serverlist != 'undefined') {
			$('#total_server').html(global_serverlist.length);
		}

		var combined = newserver = newcombined = 0;
		var today = <?php echo strtotime(date('Y-m-d')); ?>;
		for(var key in global_serverlist) {
			if (global_serverlist[key].is_combined == 1) {
				combined = combined + 1;
				if (global_serverlist[key].opendate > today) {
					newcombined = newcombined + 1;
				}
			}

			if (global_serverlist[key].opendate > today) {
				newserver += 1;
			}
		}

		$('#today_server').html(newserver+'');
		$('#total_combined').html(combined+'');
		$('#today_combined').html(newcombined+'');
	}, 250);
	
	onlinerefresh();
	//sh = setInterval(onlinerefresh, 10000);

	$('#personranklist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = $(this).attr('title'), playername = $(this).attr('data-pname');
		if (playername != '' && sid > 0) {
			var sname = '';
			for(var key in global_serverlist) {
				if (global_serverlist[key].sid == sid) {
					sname = global_serverlist[key].name;
					break;
				}
			}
			dialog = $.dialog({id: 'player_info_'+playerid, width: 880, title: title});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=player_info',
				data: {id: playerid, sid: sid, sname: sname, playername: playername},
				success: function(data) {
					dialog.content(data);
				}
			});
		}
	});
});
</script>


<div class="summary cf">
    <div class="datalist">
        <ul>
            <li>
                <h3>今日收入（元）</h3>
                <h4 id="todayPV" class="up"><?php echo $income['today']; ?></h4>
                <div class="data" id="data_pv" style="width:160px;">
                    <p>昨日收入：<span class="bold"><?php echo $income['yesterday']; ?></span></p>
                    <p>本月收入：<span class="bold"><?php echo $income['month']; ?></span></p>
                    <p>上月收入：<span class="bold"><?php echo $income['yestermonth']; ?></span></p>
                    <p>&nbsp;&nbsp;&nbsp;总收入：<span class="greentitle bold"><?php echo $income['total']; ?></span></p>
                </div>
            </li>
            <li>
                <h3>今日在线（人）</h3>
                <h4 id="online" class="up">0</h4>
                <div class="data" id="data_iv">
                    <p>昨日登陆：<span class="bold"><?php echo $online['login']; ?></span></p>
                    <p>历史最高：<span class="bold" title="发生于：<?php echo date('Y-m-d', strtotime($max_online['max_online_time'])); ?>"><?php echo $max_online['max_online']; ?></span></p>
                    <p>总注册数：<span class="bold"><?php echo $online['register']; ?></span></p>
                    <p>总创建数/率：<span class="bold"><?php echo $online['create']; ?></span>&nbsp;/&nbsp;<span class="greentitle bold"><?php echo $online['createpercent']; ?>%</span></p>
                </div>
            </li>
            <li>
                <h3>今日开服（服）</h3>
                <h4 id="today_server" class="up">0</h4>
                <div class="data" id="data_uv" style="width:160px;">
                    <p>共有平台：<span class="bold" id="total_platform">0</span></p>
                    <p>今日合服：<span class="bold" id="today_combined">0</span></p>
                    <p>合服数量：<span class="bold" id="total_combined">0</span></p>
                    <p>正式运营服：<span class="greentitle bold" id="total_server">0</span></p>
                </div>
            </li>
        </ul>
    </div>
</div>
<br>
<div class="summary cf">
	<div class="column cf">
	    <div class="title">
	        时段充值统计
	    </div>
	    <div>
	    <table class="table tablesorter">
			<thead>
			<tr>
				<th style="width:100px;">&nbsp;</th>
				<?php echo $dateHtml; ?>
				<th style="width:100px;">今日收入</th>
			</tr>
			</thead>
	    	<tbody>
    		<tr>
    			<td class="num">全日充值</td>
    			<?php for($i=7; $i >= 0; $i--) { ?>
    			<td>
    				<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
    				<?php echo $totalamount[$i] > 0 ? sprintf('%.2f', $totalamount[$i]) : '--'; ?>
    				<?php echo $i==0 ? '</span>' : ''; ?>
    			</td>
    			<?php } ?>
    		</tr>
    		<tr>
    			<td class="num">非Q点充值</td>
    			<?php for($i=7; $i >= 0; $i--) { ?>
    			<td>
    				<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
    				<?php echo $totalamount3[$i] > 0 ? sprintf('%.2f', $totalamount3[$i]) : '--'; ?>
    				<?php echo $i==0 ? '</span>' : ''; ?>
    			</td>
    			<?php } ?>
    		</tr>
    		<tr>
    			<td class="num">0点 ~ <?php echo date('H:i'); ?></td>
    			<?php for($i=7; $i >= 0; $i--) { ?>
    			<td>
    				<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
    				<?php echo $totalamount2[$i] > 0 ? sprintf('%.2f', $totalamount2[$i]) : '--'; ?>
    				<?php echo $i==0 ? '</span>' : ''; ?>
    				<br><span class="graytitle"><?php echo $totalpaynum[$i]?$totalpaynum[$i]:0; ?>人</span>
    			</td>
    			<?php } ?>
    		</tr>
	    	</tbody>
	    </table>
		</div>
	</div>
</div>
<div class="container cf" id="my_stats" style="min-height: 230px;">
    <div id="con_1" class="mod_define mid_cont">
		<h4><i class="i_mod2"></i>充值趋势对比<span><a hidefocus="true" href="javascript:void(0);"><i class="i_blank" title="查看详细数据"></i></a></span></h4>
		<div id="graph_wrapper" style="min-height: 230px;">

		</div>
	</div>

    <div id="con_1" class="mod_define mid_cont">
		<h4><i class="i_mod2"></i>在线趋势对比<span><a hidefocus="true" href="javascript:void(0);"><i class="i_blank" title="查看详细数据"></i></a></span></h4>
		<div id="graph_wrapper2" style="min-height: 230px;">

		</div>
	</div>
</div>
<br>
<div class="summary cf">
	<div id="table_column" class="column cf">
	    <div class="title">
	        今日充值情况
	    </div>
	    <div id="dataTable">
	    <table id="sortTable" class="table tablesorter">
			<thead>
				<tr class="thead_col">
					<th>&nbsp;</th>
	                <th colspan="4" class="itemTips" item="pv">个人充值排行</th>
	                <th>&nbsp;</th>
	                <th colspan="3" class="itemTips" item="uv">单服充值排行</th>
		        </tr>
				<tr>
					<th class="num">名次</th>
					<th>服务器</th>
					<th>玩家帐号</th>
					<th>充值金额</th>
					<th>充值次数</th>
					<th>&nbsp;</th>
					<th>服务器</th>
					<th>充值金额</th>
					<th>充值人数</th>
				</tr>
			</thead>
	    	<tbody>
	    		<?php foreach ($randList as $key => $value): ?>
    			<tr>
    				<td class="bold"><?php echo $key ?></td>
    				<td><?php echo isset($value['selfSid']) ? $servList[$value['selfSid']] : '--'; ?></td>
    				<td><?php echo isset($value['selfNickname']) ? $value['selfUsername'] : '--' ?></td>
    				<td><?php echo isset($value['selfAmount']) ? $value['selfAmount'] : '--' ?></td>
    				<td>&nbsp;</td>
    				<td><?php echo isset($value['servSid']) ? $servList[$value['servSid']] : '--' ?></td>
    				<td><?php echo isset($value['servNickname']) ? $value['servNickname'] : '--' ?></td>
    				<td><?php echo isset($value['servAmount']) ? $value['servAmount'] : '--' ?></td>
    			</tr>
	    		<?php endforeach ?>
	    	</tbody>
	    </table>
		</div>
	</div>
</div>
<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript">
var chart;
$(function(){
	var options = {
			chart: {
				renderTo: 'graph_wrapper',
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
				text: ''
			},
			yAxis: {
                title: {
                    text: ''
                },
                min: 0
            },

			xAxis: {
				categories: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00','06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00',
						 '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
				labels: {
					step: 3
				}
			},
			tooltip: {
				shared: true,
				crosshairs: true
			},
			series: [{
				name: '今日充值',
				lineWidth: 4,
				shadow: false,
				marker: {
					radius: 0.5
				}
			}]
		};

	
	options.series[0].data = <?php echo $todaylist; ?>;
	var opseries={"name": '7日均线', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $sevenlist; ?>};
	options.series.push(opseries);
	opseries={"name": '30日均线', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $thirtylist; ?>,visible: false};
		options.series.push(opseries);
	chart = new Highcharts.Chart(options);

	options.chart.renderTo = 'graph_wrapper2';
	options.series.splice(0,3);
	opseries={"name": '今日在线', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $todayonline; ?>};
	options.series.push(opseries);
	opseries={"name": '7日均线', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $sevenonline; ?>};
	options.series.push(opseries);
	opseries={"name": '30日均线', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $thirtyonline; ?>,visible: false};
	options.series.push(opseries);
	chart2 = new Highcharts.Chart(options);
	//console.log('<?php echo $memcachecount.'-'.$memcachedate.'-'.date('Y-m-d H:i:s');?>');	
});
</script>

