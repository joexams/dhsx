<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var player_all = num_all = 0;
$(function(){
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var obj = $(this);
		Ha.common.ajax('<?php echo INDEX; ?>?m=server&c=get&v=player_fate', 'json', obj.serialize(), 'get', 'container', function(data){
			if (data.status == 0){
				player_all = data.list[0].player_count;
				num_all = data.list[0].num;
				$('#fatelist').empty().append($('#fatelisttpl').tmpl(data.list)).show();
			}else {
				$('#fatelist').html('<tr><td colspan="4" style="text-align: left">没有找到数据...。</td></tr>');
			}
		}, 1);
	});
	$("#get_search_submit").submit();
});
</script>

<script type="text/template" id="fatelisttpl">
<tr>
    <td class="num">{{if id>0}}${fate_name}<span class="graytitle">(${quality_name})</span>{{else}}<span class="redtitle">${fate_name}</span>{{/if}}</td>
    <td>{{if player_count > 0}}${player_count}{{if id > 0}}<span class="graytitle">（${(player_count*100/player_all).toFixed(2)}%）</span>{{/if}}{{else}}-{{/if}}</td>
    <td>{{if num > 0}}${num}{{if id > 0}}<span class="graytitle">（${(num*100/num_all).toFixed(2)}%）</span>{{/if}}{{else}}-{{/if}}</td>
	<td>&nbsp;</td>
</tr>
</script>

<h2><span id="tt">命格统计</span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					 <label>
					 玩家类型：
					 <select name="vip_type" id="vip_type" class="ipt_select">
					 	<option value="0">非VIP</option>
					 	<option value="1">VIP6级以下</option>
					 	<option value="2">VIP6级以上</option>
					 </select></label>
					 <input type="hidden" name="sid" value="<?php echo $sid ?>">
					 <input type="hidden" name="cid" value="<?php echo $cid ?>">
					 <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num">命格</th>
			<th>拥有人数</th>
			<th>命格数量</th>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody id="fatelist">
			<tr><td colspan="4" style="text-align: left">数据加载中。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>