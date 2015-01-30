<?php defined('IN_G') or exit('No permission resources.'); ?>
<h2><span id="tt"><?php echo Lang('max_level_stat') ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="chart_column">
	<div class="flashChart_title">趋势图</div>	
	<div id="flashChart"></div>
	</div>

    <div id="table_column" class="column cf">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th class="num"><?php echo Lang('level'); ?></th>
			<th><?php echo Lang('server_num'); ?></th>
			<th><?php echo Lang('percent'); ?></th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($data['list'] as $key => $value) { ?>
			<tr>
				<td class="num"><?php echo $data['categories'][$key]; ?></td>
				<td><?php echo $value; ?></td>
				<td><?php echo round($value * 100/$data['sum'], 2); ?>%</td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		</div>
    </div>
</div>
<script type="text/javascript">
$(function(){
	$("#flashChart").multiChart(<?php echo json_encode($chartData); ?>);
});
</script>