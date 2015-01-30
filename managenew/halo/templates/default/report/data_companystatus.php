<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#companystatulist').html('<tr><td colspan="13">正在努力加载中...</td></tr>');

	$.ajax({
		url: '<?php echo INDEX; ?>?m=report&c=data&v=companystatus',
		data: 'doget=1',
		dataType: 'json',
		success: function(data) {
			$('#companystatulist').empty();
			if (data.status == 0 && data.list.length > 0){
				$('#companystatulisttpl').tmpl(data.list).appendTo('#companystatulist');
			}
		}
	})
});
</script>
<script type="text/template" id="companystatulisttpl">
	<tr>
		<td>${rank>0?rank:'-'}</td>
		<td>${name}</td>
		<td>${servernum}</td>
		<td>${combinednum?combinednum:'-'}</td>
		<td>${openednum}</td>
		<td>${todayopenednum?todayopenednum:'-'}</td>
		<td>${waitopennum>0?waitopennum:'-'}</td>
		<td><span class="orangetitle">${todayincome>0?parseFloat(todayincome).toFixed(2) : '-'}</span></td>
		<td>${yesterdayincome>0?parseFloat(yesterdayincome).toFixed(2) : '-'}</td>
		<td>${curmonthincome>0?parseFloat(curmonthincome).toFixed(2) : '-'}</td>
		<td>${totalincome>0?parseFloat(totalincome).toFixed(2):'-'}</td>
		<td>${totalincome>0?(totalincome/servernum).toFixed(2):'-'}</td>
		<td>&nbsp;</td>
	</tr>
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('company_status'); ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:5%"><?php echo Lang('ranking'); ?></th>
					<th style="width:10%"><?php echo Lang('company_platform'); ?></th>
					<th style="width:5%"><?php echo Lang('server'); ?></th>
					<th style="width:5%"><?php echo Lang('server_yes_combined'); ?></th>
					<th style="width:5%"><?php echo Lang('opened'); ?></th>
					<th style="width:5%"><?php echo Lang('today_open_server'); ?></th>
					<th style="width:5%"><?php echo Lang('wait_open_server'); ?></th>
					<th style="width:8%"><?php echo Lang('today_income'); ?></th>
					<th style="width:8%"><?php echo Lang('yesterday_income'); ?></th>
					<th style="width:8%"><?php echo Lang('curmonth_income'); ?></th>
					<th style="width:8%"><?php echo Lang('total_income'); ?></th>
					<th style="width:8%"><?php echo Lang('avg_server_income'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="companystatulist">
			</tbody>
		</table>
	</div>
</div>
