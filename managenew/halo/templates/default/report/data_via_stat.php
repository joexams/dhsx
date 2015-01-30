<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	$('#viaid').on('change', function(){
		var viaid = $(this).val();
		if (viaid > 0) {
			$("#data_via_list").html('<tr><td colspan="9">数据加载中...</td></tr>');
			$("#data_via").load("<?php echo INDEX; ?>?m=report&c=data&v=via_stat&viaid="+viaid+"&rnd="+Math.random()+" #data_via_table");
		}
	});
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('data_via_total') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<ul class="nav_li">
		    <li class="nobg">
		        <p>
		        	<select name="viaid" id="viaid">
		        		<option value="1">任务集市渠道</option>
		        		<option value="2">via=dikou渠道</option>
		        	</select>
		        </p>
		    </li>
		</ul>
	</div>

	<div class="content"  id="data_via">
		<table class="global" width="100%" cellpadding="0" cellspacing="0"  id="data_via_table">
			<thead>
				<tr>
					<th style="width:100px">日期</th>
					<th style="width:80px">注册数</th>
					<th style="width:80px">创建数</th>
					<th style="width:80px">昨日存留率</th>
					<th style="width:80px">第2日存留率</th>
					<th style="width:80px">第3日存留率</th>
					<th style="width:80px">第7日存留率</th>
					<th style="width:80px">双周活跃</th>
					<th style="width:60px">月活跃</th>
					<th style="width:60px">付费人数</th>
					<th style="width:60px" class="orangetitle">金额(元)</th>
					<th style="width:60px">ARPU</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="data_via_list">
				<?php foreach ($list as $key => $value) { ?>
				<tr>
					<td><?php echo $key; ?>(<?php echo '周'.$weekarray[date('w', strtotime($key))]; ?>)</td>
					<td><?php echo $value['register']; ?></td>
					<td><?php echo $value['create']; ?></td>
					<td><span class="redtitle"><?php echo isset($value['daily_online']) ? round($value['daily_online']*100/$value['create'], 2).'%' : '--' ?></span></td>
					<td><?php echo isset($value['first']) ? round($value['first']*100/$value['create'], 2).'%' : '--' ?></td>
					<td><?php echo isset($value['second']) ? round($value['second']*100/$value['create'], 2).'%' : '--' ?></td>
					<td><?php echo isset($value['week']) ? round($value['week']*100/$value['create'], 2).'%' : '--' ?></td>
					<td><?php echo isset($value['twoweek']) ? round($value['twoweek']*100/$value['create'], 2).'%' : '--' ?></td>
					<td><?php echo isset($value['month']) ? round($value['month']*100/$value['create'], 2).'%' : '--' ?></td>
					<td><?php echo isset($value['paynum']) && $value['paynum'] > 0 ? $value['paynum'] : '--' ?></td>
					<td><span class="orangetitle"><?php echo isset($value['amount']) && $value['amount'] > 0 ? $value['amount'] : '--' ?></span></td>
					<td><span class="greentitle"><?php echo isset($value['paynum']) && $value['paynum'] > 0 ? round($value['amount']/$value['paynum'], 2) : '--' ?></span></td>
					<td>&nbsp;</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

</div>
