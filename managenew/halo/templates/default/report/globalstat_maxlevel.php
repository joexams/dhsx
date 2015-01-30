<?php defined('IN_G') or exit('No permission resources.'); ?>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('max_level_stat'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content graph_wrapper" id="graph_wrapper">
		
	</div>
	<div class="content">
		<table class="global iswrap" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:5%"><?php echo Lang('level'); ?></th>
					<th style="width:5%"><?php echo Lang('server_num'); ?></th>
					<th style="width:5%"><?php echo Lang('percent'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['list'] as $key => $value) { ?>
				<tr>
					<td><?php echo $data['categories'][$key]; ?></td>
					<td><?php echo $value; ?></td>
					<td><?php echo round($value * 100/$data['sum'], 2); ?>%</td>
					<td>&nbsp;</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<br class='clear'>
	<br class='clear'>
</div>
<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript">
var chart;
$(function(){
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'graph_wrapper',
			borderWidth: 1,
	        plotBorderColor: '#A47D7C',
	        plotBorderWidth: 1,
			type: 'column'
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
			text: '<?php echo Lang('max_level_stat'); ?>'
		},
		xAxis: {
			categories: <?php echo json_encode($data['categories']); ?>
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo Lang('server_num'); ?>'
			}
		},
		tooltip: {
			formatter: function() {
				return '<b>Lv.'+this.x+'：</b>'+this.y +' ('+ (this.y * 100/<?php echo $data['sum']; ?>).toFixed(2) +'%)';
			}
		},
		series: [{
			name: '<?php echo Lang('max_level_stat'); ?>',
			data: <?php echo json_encode($data['list']); ?>
		}]
	});
});
</script>