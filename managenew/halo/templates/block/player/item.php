<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function getitemList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=item";
	$( "#info_list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: function(data){
				if (data.status == 1){
					$('#info_list').html(data.msg);
				}else {
					if (data.type != undefined){
						extendlist = data.type;
					}
					if (data.count > 0){
						$( "#listtpl" ).tmpl( data.list ).prependTo( "#info_list" );
						$( "#info_list" ).stop(true,true).hide().slideDown(400);
					}
				}
			}
		});
	});
	return false;
}
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
$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']); ?>"+'->背包仓库');
	}
	getitemList();

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${grid_id_to_name(grid_id)}</td>
<td>${item_id_to_name(item_id)}</td>
<td>{{if gold_oil_id > 0}}<span class="orangetitle">${gold_id_to_name(gold_oil_id)}</span>{{else}}-{{/if}}</td>
<td>
{{if attribute_stone_location_1 > 0}}<span class="graytitle">1:</span> ${item_id_to_name(attribute_stone_location_1)}{{else}}-{{/if}}
{{if attribute_stone_location_2 > 0}}<br><span class="graytitle">2:</span> ${item_id_to_name(attribute_stone_location_2)}{{/if}}
{{if attribute_stone_location_3 > 0}}<br><span class="graytitle">3:</span> ${item_id_to_name(attribute_stone_location_3)}{{/if}}
{{if attribute_stone_location_4 > 0}}<br><span class="graytitle">4:</span> ${item_id_to_name(attribute_stone_location_4)}{{/if}}
</td>
<td>${number}</td>
<td>${level_id_to_name(upgrade_level)}</td>
<td>${sell_lock==1?'YES':'NO'}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="onecolumn">
		<div class="header">
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="pop_refresh">刷新</a></li>
			</ul>
		</div>
	</div>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:100px;"><?php echo Lang('grid_postion'); ?></th>
				    <th><?php echo Lang('name'); ?></th>
                    <th><?php echo Lang('gold_oil'); ?></th>
                    <th><?php echo Lang('attribute_stone'); ?></th>
				    <th style="width:80px;"><?php echo Lang('save_num'); ?></th>
				    <th style="width:100px;"><?php echo Lang('upgrade_level'); ?></th>
				    <th style="width:50px;"><?php echo Lang('sell_lock'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
</div>
