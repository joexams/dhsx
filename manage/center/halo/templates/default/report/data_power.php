<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	Ha.common.ajax('<?php echo INDEX; ?>?m=server&c=get&v=player_power', 'json', "sid=<?php echo $sid ?>", 'get', 'container', function(data){
		if (data.status == 0){
			$('#rolelist').empty().append($('#rolelisttpl').tmpl(data.list)).show();
		}else {
			$('#rolelist').html('<tr><td colspan="4" style="text-align: left">没有找到数据。</td></tr>');
		}
	}, 1);
});
</script>

<script type="text/template" id="rolelisttpl">
<tr>
    <td class="num">${name}</td>
    <td>${today}{{if today > 0}}<span class="graytitle">（${today_rate}%）</span>{{/if}}</td>
    <td>${pay}{{if pay > 0}}<span class="graytitle">（${pay_rate}%）</span>{{/if}}</td>
	<td>${all}{{if all > 0}}<span class="graytitle">（${all_rate}%）</span>{{/if}}</td>
</tr>
</script>
<h2><span id="tt">体力统计</span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num">体力范围</th>
			<th>今日登陆玩家/比例</th>
			<th>充值玩家/比例</th>
			<th>所有玩家/比例</th>
		</tr>
		</thead>
		<tbody id="rolelist">
			<tr><td colspan="4" style="text-align: left">数据加载中。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>