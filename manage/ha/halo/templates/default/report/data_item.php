<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	Ha.common.ajax('<?php echo INDEX; ?>?m=server&c=get&v=player_item', 'json', "sid=<?php echo $sid ?>", 'get', 'container', function(data){
		if (data.status == 0){
			$('#assetlist').empty().append($('#assetlisttpl').tmpl(data.list)).show();
		}else {
			$('#assetlist').html('<tr><td colspan="13" style="text-align: left">没有找到数据...。</td></tr>');
		}
	}, 1);
});
</script>

<script type="text/template" id="assetlisttpl">
<tr>
    <td class="num">{{if upgrade_level > 0}}Lv.${upgrade_level}{{else}}<span class="redtitle">总计</span>{{/if}}</td>
	<td>${num_1 > 0 ? num_1 : '-'}</td>
	<td>${player_1 > 0 ? player_1 : '-'}</td>
	<td>${num_2 > 0 ? num_2 : '-'}</td>
	<td>${player_2 > 0 ? player_2 : '-'}</td>
	<td>${num_3 > 0 ? num_3 : '-'}</td>
	<td>${player_3 > 0 ? player_3 : '-'}</td>
	<td>${num_4 > 0 ? num_4 : '-'}</td>
	<td>${player_4 > 0 ? player_4 : '-'}</td>
	<td>${num_5 > 0 ? num_5 : '-'}</td>
	<td>${player_5 > 0 ? player_5 : '-'}</td>
	<td>${num > 0 ? num : '-'}</td>
	<td>${player > 0 ? player : '-'}</td>
</tr>
</script>

<h2><span id="tt">装备统计</span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">元宝详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr class="thead_col">
			<th>&nbsp;</th>
            <th colspan="2" class="num">白色</th>
            <th colspan="2" class="num"><strong class="greentitle">绿色</strong></th>
            <th colspan="2" class="num"><strong class="bluetitle">蓝色</strong></th>
            <th colspan="2" class="num"><strong class="purpletitle">紫色</strong></th>
            <th colspan="2" class="num"><strong class="yellowtitle">黄色</strong></th>
            <th colspan="2" class="num"><strong class="redtitle">总计</strong></th>
        </tr>
		<tr id="dataTheadTr">
		    <th class="num">强化等级</th>
			<th>装备数量</th>
			<th>持有人数</th>
			<th>装备数量</th>
			<th>持有人数</th>
			<th>装备数量</th>
			<th>持有人数</th>
			<th>装备数量</th>
			<th>持有人数</th>
			<th>装备数量</th>
			<th>持有人数</th>
			<th>装备数量</th>
			<th>持有人数</th>
		</tr>
		</thead>
		<tbody id="assetlist">
			<tr><td colspan="13" style="text-align: left">数据加载中...</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>