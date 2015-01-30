<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	var url = '<?php echo INDEX; ?>?m=report&c=data&v=companystatus';
	Ha.common.ajax(url, 'json', 'doget=1', 'get', 'container', function(data){
		if (data.status == 0 && data.list != ''){
			$( "#companystatulist" ).empty().append($( "#companystatulisttpl" ).tmpl( data.list )).show();
		}else {
			$( "#companystatulist").html('<tr><td colspan="12" style="text-align: left">没有找到数据。</td></tr>');
		}
	}, 1);
});
</script>
<script type="text/template" id="companystatulisttpl">
	<tr>
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
		<td>${totalincome>0?(totalincome/openednum).toFixed(2):'-'}</td>
	</tr>
</script>


<h2><span id="tt"><?php echo Lang('company_status'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th><?php echo Lang('company_platform'); ?></th>
			<th><?php echo Lang('server'); ?></th>
			<th><?php echo Lang('server_yes_combined'); ?></th>
			<th><?php echo Lang('opened'); ?></th>
			<th><?php echo Lang('today_open_server'); ?></th>
			<th><?php echo Lang('wait_open_server'); ?></th>
			<th><?php echo Lang('today_income'); ?></th>
			<th><?php echo Lang('yesterday_income'); ?></th>
			<th><?php echo Lang('curmonth_income'); ?></th>
			<th><?php echo Lang('total_income'); ?></th>
			<th><?php echo Lang('avg_server_income'); ?></th>
		</tr>
		</thead>
		<tbody id="companystatulist">
		</tbody>
		</table>
		</div>
	</div>
</div>