<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){

	Ha.common.ajax("<?php echo INDEX; ?>?m=server&c=get&v=player_townonline", 'json', 'sid=<?php echo $sid ?>', 'get', 'container', function(data){
		if (data.status == 0){
			if (data.count > 0){
				$( "#data_town_list" ).empty().append($( "#data_town_list_tpl" ).tmpl( data.list )).show();
				$('#townonline').html('（总：'+data.count+'人）');
			}else {
				$( "#data_town_list").html('<tr><td colspan="3" style="text-align: left">没有在线数据。</td></tr>');
			}
		}
	}, 1);
});
</script>


<script type="text/template" id="data_town_list_tpl">
<tr>
	<td class="num">${rank}</td>
	<td>${name}</td>
	<td>${player_count}</td>
</tr>
</script>

<h2><span id="tt">当前城镇在线人数</span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num">&nbsp;</th>
		    <th>城镇<span class="redtitle" id="townonline"></span></th>
		    <th>当前在线</th>
		</tr>
		</thead>
		<tbody id="data_town_list">
		<tr><td colspan="3" style="text-align: left">数据正在加载中...</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>