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
			$('#online').html(data+' 人');
			/*if ($('#online').is('span') == false) {
				clearInterval(sh);
			}*/
		},
		error: function() {
			$('#online').html('0 人');
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

		var personrank = <?php echo $personrank; ?>;
		var serverrank = <?php echo $serverrank; ?>;
		$('#personranklisttpl').tmpl(personrank, {index: function (item) {return $.inArray(item, personrank) + 1;}}).appendTo('#personranklist');
		$('#serverranklisttpl').tmpl(serverrank, {index: function (item) {return $.inArray(item, serverrank) + 1;}}).appendTo('#serverranklist');
		personrank = null;
		serverrank = null;
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
				url: '<?php echo INDEX; ?>?m=report&c=player&v=detail_info',
				data: {id: playerid, sid: sid, sname: sname, playername: playername},
				success: function(data) {
					dialog.content(data);
				}
			});
		}
	});
});
</script>

<script type="text/template" id="personranklisttpl">
<tr>
	<td>${$item.index($item.data)}</td>
	<td>${sid_to_name(sid)}</td>
	<td><a href="javascript:;" date-pid="${player_id}" data-sid="${sid}" data-pname="${username}" class="player_info" title="${nickname} - ${username}">${nickname}</a></td>
	<td>${amount}</td>
	<td>${pay_num}</td>
	<td>&nbsp;</td>
</tr>
</script>

<script type="text/template" id="serverranklisttpl">
<tr>
	<td>${$item.index($item.data)}</td>
	<td>${sid_to_name(sid)}</td>
	<td>${amount}</td>
	<td>${pay_num}</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('main'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div style="width: 100%;" id="more_row_content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table class="global" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
						<thead>
							<tr>
								<th style="text-align:left;width:120px;" rowspan="2">收入情况</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>今日收入</th>
								<td><span class="orangetitle"><?php echo $income['today']; ?> 元</span></td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>昨日收入</th>
								<td><?php echo $income['yesterday']; ?> 元</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>本月收入</th>
								<td><?php echo $income['month']; ?> 元</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>上月收入</th>
								<td><?php echo $income['yestermonth']; ?> 元</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>总 收 入</th>
								<td><span class="greentitle"><?php echo $income['total']; ?> 元</span></td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table class="global" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
						<thead>
							<tr>
								<th style="text-align:left;width:120px;">在线情况</th>
								<th style="width:200px;">&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>当前在线</th>
								<td><span class="orangetitle" id="online">0 人</span></td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>历史最高</th>
								<td id="max_online"><?php echo $max_online['max_online']; ?> 人<span class="graytitle">(<?php echo $max_online['max_online_time']; ?>)</span></td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>昨日登陆</th>
								<td id="yesterday_online"><?php echo $online['login']; ?> 人</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>总注册数</th>
								<td id="total_register"><?php echo $online['register']; ?>人</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>总创建数/总创建率</th>
								<td><span class="greentitle" id="total_create"><?php echo $online['create']; ?> 人 (<?php echo $online['createpercent']; ?>%)</span></td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table class="global" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
						<thead>
							<tr>
								<th style="text-align:left;width:120px;">开服情况</th>
								<th style="width:100px;">&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th style="width:120px;">共有平台</th>
								<td style="width:80px;"><span class="orangetitle" id="total_platform">0</span></td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th style="width:120px;">今日新开服</th>
								<td style="width:80px;" id="today_server">0</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th style="width:120px;">今日合服</th>
								<td style="width:80px;" id="today_combined">0</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th style="width:120px;">合服数量</th>
								<td style="width:80px;" id="total_combined">0</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th style="width:120px;">正式运营服</th>
								<td style="width:100px;"><span class="greentitle" id="total_server">0</span></td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<br class="clear">
	<br>
	<div class="onecolumn" id="pay_stat_row">
		<div class="header">
			<h2>时段充值统计</h2>
			<ul class="second_level_tab"></ul>
		</div>
		<div class="content nomargin">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="width:100px;">&nbsp;</th>
						<th style="width:100px;">今日收入</th>
						<?php echo $dateHtml; ?>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>全日充值</th>
						<?php for($i=0; $i < 8; $i++) { ?>
						<td>
							<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
							<?php echo $totalamount[$i] > 0 ? sprintf('%.2f', $totalamount[$i]) : '-'; ?>
							<?php echo $i==0 ? '</span>' : ''; ?>
						</td>
						<?php } ?>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>非Q点充值</th>
						<?php for($i=0; $i < 8; $i++) { ?>
						<td>
							<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
							<?php echo $totalamount3[$i] > 0 ? sprintf('%.2f', $totalamount3[$i]) : '-'; ?>
							<?php echo $i==0 ? '</span>' : ''; ?>
						</td>
						<?php } ?>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>0点 ~ <?php echo date('H:i'); ?></th>
						<?php for($i=0; $i < 8; $i++) { ?>
						<td>
							<?php echo $i==0 ? '<span class="orangetitle">' : ''; ?>
							<?php echo $totalamount2[$i] > 0 ? sprintf('%.2f', $totalamount2[$i]) : '-'; ?>
							<?php echo $i==0 ? '</span>' : ''; ?>
							<br><span class="graytitle"><?php echo $totalpaynum[$i]?$totalpaynum[$i]:0; ?>人</span>
						</td>
						<?php } ?>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<br class="clear">
	<div style="width: 100%;">
	<table class="global" width="100%" style="min-width:100%;" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div class="content nomargin" id="graph_wrapper" style="min-width:500px;"></div>
			</td>
			<td>
				<div class="content nomargin" id="graph_wrapper2" style="min-width:500px;"></div>
			</td>
		</tr>
	</table>
	</div>
	<br class="clear">
	<div style="width: 100%;" id="pay_rank_row">
		<table class="global" width="100%" style="min-width:100%;" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div class="onecolumn" style="min-width:100%;">
						<div class="header">
							<h2>今日个人充值排行</h2>
							<ul class="second_level_tab"></ul>
						</div>
						<div class="content nomargin" style="min-width:100%;">
						<table class="global nowrap" width="100%" style="min-width:100%;" cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th style="width:30px;">名次</th>
									<th style="width:120px;">服务器</th>
									<th style="width:120px;">玩家帐号</th>
									<th style="width:80px;">充值金额</th>
									<th style="width:80px;">充值次数</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody id="personranklist">
								
							</tbody>
						</table>
						</div>
					</div>
				</td>
				<td>
					<div class="onecolumn" style="min-width:100%;">
						<div class="header">
							<h2>今日单服充值排行</h2>
							<ul class="second_level_tab"></ul>
						</div>
						<div class="content nomargin" style="min-width:100%;">
						<table class="global nowrap" width="100%" style="min-width:100%;" cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th style="width:30px;">名次</th>
									<th style="width:120px;">服务器</th>
									<th style="width:80px;">充值金额</th>
									<th style="width:80px;">充值人数</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody id="serverranklist">
								
							</tbody>
						</table>
						</div>
					</div>
				</td>
			</tr>
		</table>
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
				text: '充值趋势对比'
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
	options.subtitle.text = '在线趋势对比';
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

