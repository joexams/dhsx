<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function item_id_to_name(id){
		if (extendlist.item != undefined && extendlist.item.length > 0){
		for (var key in extendlist.item){
			if (extendlist.item[key].id == id){
				return extendlist.item[key].name;
			}
		}
	}
	return '';
}
function grid_id_to_name(id){
	if (extendlist.packet != undefined && extendlist.packet.length > 0){
		for (var key in extendlist.packet){
			if (extendlist.packet[key].id == id){
				return extendlist.packet[key].name;
			}
		}
	}
	return '';
}
function level_id_to_name(id){
	if (extendlist.upgrade != undefined && extendlist.upgrade.length > 0){
		for (var key in extendlist.upgrade){
			if (extendlist.upgrade[key].level == id){
				return extendlist.upgrade[key].name;
			}
		}
	}
	return '';
}
function gold_id_to_name(id){
    if (extendlist.goldoil != undefined && extendlist.goldoil.length > 0){
        for (var key in extendlist.goldoil){
            if (extendlist.goldoil[key].item_id == id){
                return '金油'+extendlist.goldoil[key].name;
            }
        }
    }
    return '';
}
$(function(){
	var url = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=item";
	Ha.common.ajax(url, 'json', data, 'get', 'container', function(data){
		if (data.status == 1){
			Ha.notify.show(data.msg, '', 'error');
		}else {
			if (data.type != undefined){
				extendlist = data.type;
			}
			if (data.count > 0){
				$( "#info_list" ).append($( "#listtpl" ).tmpl( data.list )).show();
			}else {
				$( "#info_list").html('<tr><td colspan="8" style="text-align: left">没有找到数据。</td></tr>').show();
			}
		}
	}, 1);
	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td class="num">${id}</td>
<td>${grid_id_to_name(grid_id)}</td>
<td>${item_id_to_name(item_id)}</td>
<td>{{if gold_oil_id > 0}}<span class="orangetitle">${gold_id_to_name(gold_oil_id)}</span>{{else}}--{{/if}}</td>
<td>
{{if attribute_stone_location_1 > 0}}<span class="graytitle">1:</span> ${item_id_to_name(attribute_stone_location_1)}{{else}}--{{/if}}
{{if attribute_stone_location_2 > 0}}<br><span class="graytitle">2:</span> ${item_id_to_name(attribute_stone_location_2)}{{/if}}
{{if attribute_stone_location_3 > 0}}<br><span class="graytitle">3:</span> ${item_id_to_name(attribute_stone_location_3)}{{/if}}
{{if attribute_stone_location_4 > 0}}<br><span class="graytitle">4:</span> ${item_id_to_name(attribute_stone_location_4)}{{/if}}
</td>
<td>${number}</td>
<td>${level_id_to_name(upgrade_level)}</td>
<td>${sell_lock==1?'YES':'NO'}</td>
</tr>
</script>

<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th>&nbsp;</th>
		    <th><?php echo Lang('grid_postion'); ?></th>
		    <th><?php echo Lang('name'); ?></th>
            <th><?php echo Lang('gold_oil'); ?></th>
            <th><?php echo Lang('attribute_stone'); ?></th>
		    <th><?php echo Lang('save_num'); ?></th>
		    <th><?php echo Lang('upgrade_level'); ?></th>
		    <th><?php echo Lang('sell_lock'); ?></th>
		</tr>
		</thead>
		<tbody id="info_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>