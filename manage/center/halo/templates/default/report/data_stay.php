<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	Ha.common.ajax("<?php echo INDEX; ?>?m=server&c=get&v=player_stay", 'json', 'sid=<?php echo $sid ?>', 'get', 'container', function(data){
		if (data.status == 0){
			if (data.count > 0){
				$( "#data_stay_list" ).empty().append($( "#data_stay_list_tpl" ).tmpl( data.list )).show();
			}else {
				$( "#data_stay_list").html('<tr><td colspan="8" style="text-align: left"><?php echo Lang('no_find_data');?>。</td></tr>');
			}
		}
	}, 1);
});
</script>

<script type="text/template" id="data_stay_list_tpl">
<tr>
	<td>${regist_date}</td>
	<td>{{if regist_num>0}}${regist_num}{{else}}-{{/if}}</td>
	<td>{{if two_day_stay_per>0}}${two_day_stay_per}{{else}}-{{/if}}</td>
	<td>{{if three_day_stay_per>0}}${three_day_stay_per}{{else}}-{{/if}}</td>
	<td>{{if four_day_stay_per>0}}${four_day_stay_per}{{else}}-{{/if}}</td>
	<td>{{if five_day_stay_per>0}}${five_day_stay_per}{{else}}-{{/if}}</td>
	<td>{{if six_day_stay_per>0}}${six_day_stay_per}{{else}}-{{/if}}</td>
	<td>{{if seven_day_stay_per>0}}${seven_day_stay_per}{{else}}-{{/if}}</td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('new_player_stay');?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th><?php echo Lang('register_date');?></th>
		    <th><?php echo Lang('create_num');?></th>
		    <th>2<?php echo Lang('date_stay');?>%</th>
		    <th>3<?php echo Lang('date_stay');?>%</th>
		    <th>4<?php echo Lang('date_stay');?>%</th>
		    <th>5<?php echo Lang('date_stay');?>%</th>
		    <th>6<?php echo Lang('date_stay');?>%</th>
		    <th>7<?php echo Lang('date_stay');?>%</th>
		</tr>
		</thead>
		<tbody id="data_stay_list">
		<tr><td colspan="8" style="text-align: left"><?php echo Lang('data_is_loading');?>...</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>