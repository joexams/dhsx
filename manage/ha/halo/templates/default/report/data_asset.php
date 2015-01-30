<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var ingot_all = ingot_hold = coins_all = coins_hold = 0;
$(function(){
	Ha.common.ajax('<?php echo INDEX; ?>?m=server&c=get&v=player_asset', 'json', "sid=<?php echo $sid ?>", 'get', 'container', function(data){
		if (data.status == 0){
			ingot_all = data.list.ingot_all;
			ingot_hold = data.list.ingot_hold;
			coins_all = data.list.coins_all;
			coins_hold = data.list.coins_hold;
			$('#ingot_all').html(ingot_all);
			$('#ingot_hold').html(ingot_hold);
			$('#coins_all').html(coins_all);
			$('#coins_hold').html(coins_hold);
			$('#assetlist').empty().append($('#assetlisttpl').tmpl(data.list.ingot)).show();
			$('#assetlist1').empty().append($('#assetlist1tpl').tmpl(data.list.coins)).show();
		}else {
			$('#assetlist').html('<tr><td colspan="4" style="text-align: left">没有找到数据...。</td></tr>');
			$('#assetlist1').html('<tr><td colspan="4" style="text-align: left">没有找到数据...。</td></tr>');
		}
	}, 1);
});
</script>

<script type="text/template" id="assetlisttpl">
<tr>
    <td>${name}</td>
	<td><strong>${num}</strong>{{if num > 0}}<span class="graytitle">(${(num*100/ingot_all).toFixed(2)}%)</span>{{/if}}</td>
	<td><strong>${player}</strong>{{if player > 0}}<span class="graytitle">(${(player*100/ingot_hold).toFixed(2)}%)</span>{{/if}}</td>
	<td><strong>{{if player > 0}}${(num/player).toFixed(2)}{{else}}0{{/if}}</strong></td>
</tr>
</script>

<script type="text/template" id="assetlist1tpl">
<tr>
    <td>${name}</td>
	<td><strong>${num}</strong>{{if num > 0}}<span class="graytitle">(${(num*100/coins_all).toFixed(2)}%)</span>{{/if}}</td>
	<td><strong>${player}</strong>{{if player > 0}}<span class="graytitle">(${(player*100/coins_hold).toFixed(2)}%)</span>{{/if}}</td>
	<td><strong>{{if player > 0}}${(num/player).toFixed(2)}{{else}}0{{/if}}</strong></td>
</tr>
</script>


<h2><span id="tt">财富统计</span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="speed_result">
	        <div class="mod_tab_cont">
	            <div class="stime">
	                <ul>
	                    <li>元宝总数：<em><strong id="ingot_all">0</strong></em></li>
	                    <li><span class="graytitle">持有人数：</span><em><span id="ingot_hold" class="graytitle">0</span></em></li>
	                    <li>铜钱总数：<em><strong id="coins_all">0</strong></em></li>
	                    <li><span class="graytitle">持有人数：</span><em><span id="coins_hold" class="graytitle">0</span></em></li>
	                </ul>
	            </div>
	   		</div>
	   	</div>
		<div class="title">元宝详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th>元宝范围</th>
			<th>持有人数/比例</th>
			<th>持有数量/比例</th>
			<th>平均持有</th>
		</tr>
		</thead>
		<tbody id="assetlist">
			<tr><td colspan="4" style="text-align: left">数据加载中。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
	<div class="column cf">
		<div class="title">铜钱详细数据</div>
		<div>
		<table>
		<thead>
		<tr>
		    <th>铜钱范围</th>
			<th>持有人数/比例</th>
			<th>持有数量/比例</th>
			<th>平均持有</th>
		</tr>
		</thead>
		<tbody id="assetlist1">
			<tr><td colspan="4" style="text-align: left">数据加载中。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>