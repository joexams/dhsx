<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var dialog;
$(function() {
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
		if ($('#online').is('h4') == false) {
			clearInterval(refreshstatus);
		}else{
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=data&v=online',
				success: function(data) {
					$('#online').html(data);
					
				},
				error: function() {
					$('#online').html('0');
				}
			});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=data&v=today_amount',
				success: function(data) {
					$('#todayPV').html(data);
					/*if ($('#online').is('span') == false) {
						clearInterval(sh);
					}*/
				},
				error: function() {
					$('#todayPV').html('0');
				}
			});
		}
	}
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#total_platform').html(global_companylist.length);
		}

		if (typeof global_serverlist != 'undefined') {
			$('#total_server').html(global_serverlist.length);
		}

		var combined = newserver = newcombined = 0;
		var today = <?php echo strtotime(date('Y-m-d 10:00:00')); ?>;
		for(var key in global_serverlist) {
			if (global_serverlist[key].is_combined == 1) {
				combined = combined + 1;
				if (global_serverlist[key].opendate > today) {
					newcombined = newcombined + 1;
				}
			}

			if (global_serverlist[key].opendate == today) {
				newserver += 1;
			}
		}

		$('#today_server').html(newserver+'');
		$('#total_combined').html(combined+'');
		$('#today_combined').html(newcombined+'');
	}, 250);
	var refreshstatus;	
	onlinerefresh();
	refreshstatus = setInterval(onlinerefresh, 10000);

	var aurl = "<?php echo INDEX; ?>?m=manage&c=index&v=ajax_6months_pay_list";
    Ha.common.ajax(aurl, 'json', '', 'get', 'container', function(data){
        $("#graph_wrapper3").multiChart(data.list);
    }, 1);
    
    var aurl = "<?php echo INDEX; ?>?m=manage&c=index&v=ajax_6months_login_list";
    Ha.common.ajax(aurl, 'json', '', 'get', 'container', function(data){
        $("#graph_wrapper4").multiChart(data.list);
    }, 1);
	
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
                <h3><?php echo Lang('today_income')?>（<?php echo Lang('yuan')?>）</h3>
                <h4 id="todayPV" class="up"><?php echo $income['today']; ?></h4>
                <div class="data" id="data_pv" style="width:160px;">
                    <p><?php echo Lang('yesterday_income')?>：<span class="bold"><?php echo $income['yesterday']; ?></span></p>
                    <p><?php echo Lang('curmonth_income')?>：<span class="bold"><?php echo $income['month']; ?></span></p>
                    <p><?php echo Lang('last_month_income')?>：<span class="bold"><?php echo $income['yestermonth']; ?></span></p>
                    <p>&nbsp;&nbsp;&nbsp;<?php echo Lang('total_income')?>：<span class="greentitle bold"><?php echo $income['total']; ?></span></p>
                </div>
            </li>
            <li>
                <h3><?php echo Lang('today_online')?>（<?php echo Lang('person')?>）</h3>
                <h4 id="online" class="up">0</h4>
                <div class="data" id="data_iv">
                    <p><?php echo Lang('yesterday_login')?>：<span class="bold"><?php echo $online['login']; ?></span></p>
                    <p><?php echo Lang('history_top')?>：<span class="bold" title="<?php echo Lang('happen_to')?>：<?php echo date('Y-m-d', strtotime($max_online['max_online_time'])); ?>"><?php echo $max_online['max_online']; ?></span></p>
                    <p><?php echo Lang('total_register')?>：<span class="bold"><?php echo $online['register']; ?></span></p>
                    <p><?php echo Lang('total_create_num')?>：<span class="bold"><?php echo $online['create']; ?></span>&nbsp;/&nbsp;<span class="greentitle bold"><?php echo $online['createpercent']; ?>%</span></p>
                </div>
            </li>
            <li>
                <h3><?php echo Lang('today_open_server')?>（<?php echo Lang('ser')?>）</h3>
                <h4 id="today_server" class="up">0</h4>
                <div class="data" id="data_uv" style="width:160px;">
                    <p><?php echo Lang('total_platform')?>：<span class="bold" id="total_platform">0</span></p>
                    <p><?php echo Lang('today_combined')?>：<span class="bold" id="today_combined">0</span></p>
                    <p><?php echo Lang('total_combined')?>：<span class="bold" id="total_combined">0</span></p>
                    <p><?php echo Lang('total_server')?>：<span class="greentitle bold" id="total_server">0</span></p>
                </div>
            </li>
        </ul>
    </div>
</div>
<br>
<div class="summary cf">
	<div class="column cf">
	    <div class="title">
	        <?php echo Lang('time_pay_count')?>
	    </div>
	    <div>
	    <table class="table tablesorter">
			<thead>
			<tr>
				<th style="width:100px;">&nbsp;</th>
				<th style="width:100px;"><?php echo Lang('today_income')?></th>
				<?php echo $dateHtml; ?>
			</tr>
			</thead>
	    	<tbody>
    		<tr>
    			<td class="num"><?php echo Lang('all_day_pay')?></td>
    			<?php for($i=0; $i <= 7; $i++) { ?>
    			<td>
    				<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
    				<?php echo $totalamount[$i] > 0 ? sprintf('%.2f', $totalamount[$i]) : '--'; ?>
    				<?php echo $i==0 ? '</span>' : ''; ?>
    			</td>
    			<?php } ?>
    		</tr>
    		<tr>
    			<td class="num"><?php echo Lang('not_qpoint_pay')?></td>
    			<?php for($i=0; $i <= 7; $i++) { ?>
    			<td>
    				<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
    				<?php echo $totalamount3[$i] > 0 ? sprintf('%.2f', $totalamount3[$i]) : '--'; ?>
    				<?php echo $i==0 ? '</span>' : ''; ?>
    			</td>
    			<?php } ?>
    		</tr>
    		<tr>
    			<td class="num">0点 ~ <?php echo date('H:i'); ?></td>
    			<?php for($i=0; $i >= 7; $i++) { ?>
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
	<div id="con_1" class="mod_define mid_cont" style="height:350px;">
		<h4><i class="i_mod2"></i><?php echo Lang('recharge_trend_comparison')?><span><a hidefocus="true" href="javascript:void(0);"><i class="i_blank" title="<?php echo Lang('view_detail_data')?>"></i></a></span></h4>
		<div id="graph_wrapper" style="min-height: 300px;">
		</div>
	</div>
	<div id="con_3" class="mod_define mid_cont" style="height:350px;">
		<h4><i class="i_mod2"></i><?php echo Lang('sixmonth_pay_trend_comparison')?></h4>
		<div id="graph_wrapper3" style="min-height: 230px;">
		</div>
	</div>
	<div id="con_1" class="mod_define mid_cont" style="height:350px;">
		<h4><i class="i_mod2"></i><?php echo Lang('online_trend_comparison')?><span><a hidefocus="true" href="javascript:void(0);"><i class="i_blank" title="<?php echo Lang('view_detail_data')?>"></i></a></span></h4>
		<div id="graph_wrapper2" style="min-height: 300px;">
		</div>
	</div>
	<div id="con_3" class="mod_define mid_cont" style="height:350px;">
		<h4><i class="i_mod2"></i><?php echo Lang('sixmonth_login_trend_comparison')?></h4>
		<div id="graph_wrapper4" style="min-height: 230px;">
		</div>
	</div>
</div>
<br>
<div class="summary cf">
	<div id="table_column" class="column cf">
	    <div class="title">
	        <?php echo Lang('today_pay_situation')?>
	    </div>
	    <div id="dataTable">
	    <table id="sortTable" class="table tablesorter">
			<thead>
				<tr class="thead_col">
					<th>&nbsp;</th>
	                <th colspan="4" class="itemTips" item="pv" width="60%"><?php echo Lang('person_pay_order')?></th>
	                <th style="border-right:1px solid #e8e8e8;">&nbsp;</th>
	                <th colspan="3" class="itemTips" item="uv"><?php echo Lang('server_pay_order')?></th>
		        </tr>
				<tr>
					<th class="num"><?php echo Lang('ranking')?></th>
					<th><?php echo Lang('server')?></th>
					<th><?php echo Lang('player_name')?></th>
					<th><?php echo Lang('pay_money')?></th>
					<th><?php echo Lang('pay_times')?></th>
					<th style="border-right:1px solid #e8e8e8;">&nbsp;</th>
					<th><?php echo Lang('server')?></th>
					<th><?php echo Lang('pay_money')?></th>
					<th><?php echo Lang('pay_person_num')?></th>
				</tr>
			</thead>
	    	<tbody>
	    		<?php if ($randList){ foreach ($randList as $key => $value): ?>
    			<tr>
    				<td class="bold"><?php echo $key ?></td>
    				<td><?php echo isset($value['selfSid']) ? $servList[$value['selfSid']] : '--'; ?></td>
    				<td><?php echo isset($value['selfNickname']) ? $value['selfNickname'] : '--' ?></td>
    				<td><?php echo isset($value['selfAmount']) ? $value['selfAmount'] : '--' ?></td>
    				<td><?php echo isset($value['selfPaynum']) ? $value['selfPaynum'] : '--' ?></td>
    				<td style="border-right:1px solid #e8e8e8;">&nbsp;</td>
    				<td><?php echo isset($value['servSid']) ? $servList[$value['servSid']] : '--' ?></td>
    				<td><?php echo isset($value['servAmount']) ? $value['servAmount'] : '--' ?></td>
    				<td><?php echo isset($value['servPaynum']) ? $value['servPaynum'] : '--' ?></td>
    			</tr>
	    		<?php endforeach; }?>
	    	</tbody>
	    </table>
		</div>
	</div>
</div>

<br>
<div class="summary cf">
	<div id="table_column" class="column cf">
	    <div class="title">
	        <?php echo Lang('today_consume_situation')?>
	    </div>
	    <div id="dataTable">
	    <table id="sortTable" class="table tablesorter">
			<thead>
				<tr class="thead_col">
					<th>&nbsp;</th>
	                <th colspan="4" class="itemTips" item="pv" width="60%"><?php echo Lang('consume_type_order')?></th>
	                <th style="border-right:1px solid #e8e8e8;">&nbsp;</th>
	                <th colspan="3" class="itemTips" item="uv"><?php echo Lang('server_consume_order')?></th>
		        </tr>
				<tr>
					<th class="num"><?php echo Lang('ranking')?></th>
					<th><?php echo Lang('consumption').Lang('type')?></th>
					<th><?php echo Lang('consumption').Lang('person_num')?></th>
					<th><?php echo Lang('consumption').Lang('times')?></th>
					<th><?php echo Lang('consumption').Lang('ingot')?></th>
					<th style="border-right:1px solid #e8e8e8;">&nbsp;</th>
					<th><?php echo Lang('server')?></th>
					<th><?php echo Lang('consumption').Lang('ingot')?></th>
					<th><?php echo Lang('consumption').Lang('person_num')?></th>
				</tr>
			</thead>
	    	<tbody>
	    		<tr>
    				<td class="bold"></td>
    				<td><span class="redtitle"><?php echo Lang('total')?></span></td>
    				<td><span class="redtitle"><?php echo isset($today_consume['allnum']) ? $today_consume['allnum'] : '--' ?></span></td>
    				<td><span class="redtitle"><?php echo isset($today_consume['alltotal']) ? $today_consume['alltotal'] : '--' ?></span></td>
    				<td><span class="redtitle"><?php echo isset($today_consume['allingot']) ? $today_consume['allingot'] : '--' ?></span></td>
    				<td style="border-right:1px solid #e8e8e8;">&nbsp;</td>
    				<td></td>
    				<td></td>
    				<td></td>
    			</tr>
	    		<?php if ($today_consume){ foreach ($today_consume['list'] as $key => $value): ?>
    			<tr>
    				<td class="bold"><?php echo $key+1 ?></td>
    				<td><?php echo isset($value['type']) ? $value['type'] : '--'; ?></td>
    				<td><?php echo isset($value['num']) ? $value['num'] : '--' ?></td>
    				<td><?php echo isset($value['total']) ? $value['total'] : '--' ?></td>
    				<td><?php echo isset($value['ingot']) ? $value['ingot'].'('.round($value['ingot']/$today_consume['allingot']*100,2).'%)' : '--' ?></td>
    				<td style="border-right:1px solid #e8e8e8;">&nbsp;</td>
    				<td><?php echo isset($value['sname']) ? $value['sname'] : '--' ?></td>
    				<td><?php echo isset($value['seringot']) ? $value['seringot'] : '--' ?></td>
    				<td><?php echo isset($value['sernum']) ? $value['sernum'] : '--' ?></td>
    			</tr>
	    		<?php endforeach; }?>
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
				name: '<?php echo Lang('total_pay')?>',
				lineWidth: 4,
				shadow: false,
				marker: {
					radius: 0.5
				}
			}]
		};

	
	options.series[0].data = <?php echo $todaylist; ?>;
	var opseries={"name": '<?php echo Lang('seven_day_average')?>', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $sevenlist; ?>};
	options.series.push(opseries);
	opseries={"name": '<?php echo Lang('thirty_day_average')?>', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $thirtylist; ?>,visible: false};
		options.series.push(opseries);
	chart = new Highcharts.Chart(options);

	options.chart.renderTo = 'graph_wrapper2';
	options.series.splice(0,3);
	opseries={"name": '<?php echo Lang('today_online')?>', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $todayonline; ?>};
	options.series.push(opseries);
	opseries={"name": '<?php echo Lang('seven_day_average')?>', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $sevenonline; ?>};
	options.series.push(opseries);
	opseries={"name": '<?php echo Lang('thirty_day_average')?>', lineWidth: 2, shadow: false, marker: { radius: 1 }, "data": <?php echo $thirtyonline; ?>,visible: false};
	options.series.push(opseries);
	chart2 = new Highcharts.Chart(options);
	//console.log('<?php echo $memcachecount.'-'.$memcachedate.'-'.date('Y-m-d H:i:s');?>');	
});
</script>

