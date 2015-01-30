<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	Ha.common.ajax('<?php echo INDEX; ?>?m=server&c=get&v=player_role', 'json', "sid=<?php echo $sid ?>", 'get', 'container', function(data){
		if (data.status == 0){
			$('#rolelist').empty().append($('#rolelisttpl').tmpl(data.list)).show();
		}else {
			$('#rolelist').html('<tr><td colspan="6" style="text-align: left">没有找到数据...。</td></tr>');
		}
	}, 1);
});
</script>

<script type="text/template" id="rolelisttpl">
<tr>
    <td class="num">${role_name}{{if fame>0}}<span class="graytitle">（声望将）</span>{{/if}}</td>
    <td>${role_count > 0 ? role_count : '-'}</td>
    <td>${role_in_count > 0 ? role_in_count : '-'}</td>
    <td>${role_out_count > 0 ? role_out_count : '-'}</td>
    <td>${role_out_count > 0 ? (role_out_count*100/role_count).toFixed(2) + '%' : '-'}</td>
	<td>&nbsp;</td>
</tr>
</script>

<h2><span id="tt">伙伴统计</span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num">伙伴</th>
			<th>招募人数</th>
			<th>在队人数</th>
			<th>离队人数</th>
			<th>离队比例</th>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody id="rolelist">
			<tr><td colspan="6" style="text-align: left">数据加载中。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>