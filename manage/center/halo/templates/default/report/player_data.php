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

function role_id_to_name(id){
	if (extendlist.role != undefined && extendlist.role.length > 0){
		for (var key in extendlist.role){
			if (extendlist.role[key].id == id){
				return extendlist.role[key].name;
			}
		}
	}
	return '';
}
function fate_id_to_name(id){
	if (extendlist.fate != undefined && extendlist.fate.length > 0){
		for (var key in extendlist.fate){
			if (extendlist.fate[key].id == id){
				return (extendlist.fate[key].type == 2 ? '<span class="graytitle">【暗命格】</span>' : '') +extendlist .fate[key].name + '(' + extendlist.fate[key].qualityname + ')';
			}
		}
	}
	return '';
}

function soul_id_to_name(id){
	if (extendlist != undefined){
		if (extendlist.soul != undefined && extendlist.soul.length > 0){
			for (var key in extendlist.soul){
				if (extendlist.soul[key].id == id){
					return extendlist.soul[key].name + '('+extendlist.soul[key].qualityname+')';
				}
			}
		}
	}
	return '--';
}
function attr_id_to_name(id, value){
	var attrname = '';
	if (extendlist != undefined){
		if (extendlist.attribute != undefined && extendlist.attribute.length > 0){
			for (var key in extendlist.attribute){
				if (extendlist.attribute[key].id == id){
					attrname =  extendlist.attribute[key].name + '+'+value;
					if (extendlist.attribute[key].unit < 1){
						attrname +='%';
					}
					return attrname;
				}
			}
		}
	}
	return '--';
}
function type_id_to_name(id){
	if (extendlist != undefined){
		if (extendlist.soul != undefined && extendlist.soul.length > 0){
			for (var key in extendlist.soul){
				if (extendlist.soul[key].id == id){
					return extendlist.soul[key].typename;
				}
			}
		}
	}
	return '';
}
function gifttype_id_to_name(id){
	if (extendlist != undefined){
		if (extendlist.gift != undefined && extendlist.gift.length > 0){
			for (var key in extendlist.gift){
				if (extendlist.gift[key].id == id){
					return extendlist.gift[key].name;
				}
			}
		}
	}
	return '';
}

function item_id_to_lv(item_id){
	if (extendlist.attribute != undefined && extendlist.attribute.length > 0){
		for (var key in extendlist.attribute){
			if (extendlist.attribute[key].item_id == item_id){
				return extendlist.attribute[key].lv;
			}
		}
	}
	return '';
}
function friend_id_to_name(id){
	if (extendlist.friends != undefined && extendlist.friends.length > 0){
		for (var key in extendlist.friends){
			if (extendlist.friends[key].id == id){
				if (extendlist.friends[key].nickname != ''){
					return '<strong>'+extendlist.friends[key].username +'</strong>'+ '('+extendlist.friends[key].nickname+')';
				}else {
					return '<strong>'+extendlist.friends[key].username+'</strong>';
				}				
			}
		}
	}
	return '';
}
$(function(){
	var url = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=<?php echo $key ?>";
	Ha.common.ajax(url, 'json', data, 'get', 'container', function(data){
		if (data.status == 1){
			Ha.notify.show(data.msg, '', 'error');
		}else {
			if (data.type != undefined){
				extendlist = data.type;
			}
			if (data.count > 0){
				if (data.key == 'key'){
					$("#normal").hide();
					tagkey = 'key_functionlock';
						$('#'+tagkey).html(data.list.function);
						tagkey = 'key_function_playedlock';
						$('#'+tagkey).html(data.list.function_played);
						
						tagkey = 'key_mission_videolock';
						$('#'+tagkey).val(data.list.mission_video);
						tagkey = 'key_upgrade_queue_numberlock';
						$('#'+tagkey).val(data.list.upgrade_queue_number);
			
						tagkey = '';
						for(var key in extendlist) {
							str ='';
							selected = '';
							tagkey = 'key_'+key+'lock';
							if (typeof data.list[key] != 'undefined') {
								$('#'+tagkey).html(data.list[key]);
								for(var skey in extendlist[key]) {
if (key == 'quest') {
									selected = extendlist[key][skey].lock == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].lock+'" '+selected+'>'+extendlist[key][skey].title+'</option>';
}else if (key == 'pack_grid' || key == 'role_equi' || key == 'warehouse') {
									selected = extendlist[key][skey].unlock_level == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].unlock_level+'" '+selected+'>'+extendlist[key][skey].name+'</option>';

}else {
									selected = extendlist[key][skey].lock == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].lock+'" '+selected+'>'+extendlist[key][skey].name+'</option>';

}
								}
								$('#key_'+key+'list').append(str);

							}
						}
					$("#key").show();
				}else{
				$( "#info_list" ).append($( "#listtpl" ).tmpl( data.list )).show();
				}
			}else {
				$( "#info_list").html('<tr><td colspan="8" style="text-align: left">没有找到数据。</td></tr>').show();
			}
		}
	}, 1);
	extendlist = null;
});
</script>


<script type="text/template" id="listtpl">
<?php if ($key == 'item') { ?>
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

<?php }else if ($key == 'fate') { ?>
<tr>
<td class="num">${id}</td>
<td>{{html fate_id_to_name(fate_id)}}</td>
<td>${fate_level}</td>
<td>${experience}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${grid}</td>
</tr>

<?php }else if ($key == 'role_elixir') { ?>
<tr>
<td class="num">${item_id}</td>
<td>${item_id_to_name(item_id)}</td>
<td>${times}</td>
</tr>

<?php }else if ($key == 'research') { ?>
<tr>
<td class="num">${id}</td>
<td>${name}</td>
<td>${level}</td>
<td>${type}&nbsp;</td>
</tr>

<?php }else if ($key == 'soul') { ?>
<tr>
<td class="num">${id}</td>
<td>${type_id_to_name(soul_id)}</td>
<td>${soul_id_to_name(soul_id)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_1, soul_attribute_value_location_1)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_2, soul_attribute_value_location_2)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_3, soul_attribute_value_location_3)}</td>
{{if typeof soul_attribute_id_location_4 != 'undefined'}}
<td>${attr_id_to_name(soul_attribute_id_location_4, soul_attribute_value_location_4)}</td>
{{else}}
<td>--</td>
{{/if}}
<td>${soul_pack_location}</td>
<td>${key}</td>
</tr>

<?php }else if ($key == 'gift') { ?>
<tr>
<td class="num">${id}</td>
<td>${gifttype_id_to_name(type)}</td>
<td><span class="orangetitle">${ingot}</span></td>
<td>${coins}</td>
<td>${fame}</td>
<td>${message}</td>
</tr>

<?php }else if ($key == 'item_attribute_stone') { ?>
<tr>
<td class="num">${id}</td>
<td>${grid_id_to_name(grid_id)}</td>
<td>${item_id_to_name(item_id)}</td>
<td>${number}</td>
<td>${item_id_to_lv(item_id)}</td>
</tr>

<?php }else if ($key == 'friends') { ?>
<tr>
<td class="num">${friend_id}</td>
<td>{{html friend_id_to_name(friend_id)}}</td>
<td>${group_type}</td>
<td>${date('Y-m-d H:i:s', add_time)}</td>
</tr>
<?php } ?>
</script>


<div class="column cf" id="table_column">
	<div id="dataTable">
	<table id="key" style="display:none;">
	  <thead>
			  <tr align="center">
			    <th colspan="2">解锁权值情况</th>
			  </tr>
			  </thead>
			  <tbody>
			  <tr>
			    <td align="right">城镇</td>
				<td>
				<select name="town" id="key_townlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_townlock"></span>
				</td>
			  </tr>
			  <tr>
			    <td align="right">任务</td>
				<td>
				<select name="quest" id="key_questlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_questlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td align="right">副本</td>
				<td>
				<select name="section" id="key_sectionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_sectionlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td align="right">剧情</td>
				<td>
				<select name="mission" id="key_missionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_missionlock"></span>
				</td>	
			  </tr>
			  
			  <tr>	
			    <td align="right">背包格子</td>
				<td>
				<select name="pack_grid" id="key_pack_gridlist>
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_pack_gridlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td align="right">装备位置</td>
				<td>
				<select name="role_equi" id="key_role_equilist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_role_equilock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td align="right">仓库格子</td>
				<td>
				<select name="warehouse" id="key_warehouselist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_warehouselock"></span>
				</td>	
			  </tr>
			  
			  <tr>	
			    <td align="right">功能开放</td>
				<td>
				<select name="function" id="key_game_functionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_functionlock"></span>
				</td>	
			  </tr> 
			  <tr>	
			    <td align="right">功能开放播放</td>
				<td>
				<select name="function_played" >
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_function_playedlock"></span>
				</td>	
			  </tr>  
			  <tr>	
			    <td align="right">招募等级</td>
				<td>
				<select name="role" id="key_rolelist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_rolelock"></span>
				</td>	
			  </tr>      
			  <tr>	
			    <td align="right">剧情动画播放锁</td>
				<td><input name="mission_video" id="key_mission_videolock" type="text" value="-"  size="10"/></td>	
			  </tr>    
			  <tr>	
			    <td align="right">附增的强化队列个数</td>
				<td><input name="upgrade_queue_number" id="key_upgrade_queue_numberlock" type="text" value="-"  size="10"/></td>	
			  </tr>  
			</tbody>
		</table>
	<table id="normal">
	<thead>
	<?php if ($key == 'item') { ?>
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
	<?php }else if ($key == 'fate') { ?>
	<tr id="dataTheadTr">
	    <th>&nbsp;</th>
	    <th><?php echo Lang('fate') ?></th>
	    <th><?php echo Lang('level') ?></th>
	    <th><?php echo Lang('experience') ?></th>
	    <th><?php echo Lang('wear_role') ?></th>
	    <th><?php echo Lang('packet_index') ?></th>
	</tr>
	<?php }else if ($key == 'role_elixir') { ?>
	<tr id="dataTheadTr">
    <th>&nbsp;</th>
 	<th><?php echo Lang('name'); ?></th>
	<th><?php echo Lang('times'); ?></th>
	</tr>
	<?php }else if ($key == 'research') { ?>
    <tr>
    <th>&nbsp;</th>
    <th><?php echo Lang('name') ?></th>
    <th><?php echo Lang('level') ?></th>
    <th>阵法</th>
    </tr>
	<?php }else if ($key == 'soul') { ?>
    <tr>
    <th>&nbsp;</th>
    <th><?php echo Lang('soul'); ?></th>
    <th><?php echo Lang('type'); ?></th>
    <th><?php echo Lang('attribute'); ?>1</th>
    <th><?php echo Lang('attribute'); ?>2</th>
    <th><?php echo Lang('attribute'); ?>3</th>
    <th><?php echo Lang('attribute'); ?>4</th>
    <th><?php echo Lang('position'); ?></th>
    <th><?php echo Lang('open_priv'); ?></th>
	</tr>
	<?php }else if ($key == 'gift') { ?>
	<tr>
	<th>&nbsp;</th>
	<th><?php echo Lang('type'); ?></th>
	<th><?php echo Lang('ingot'); ?></th>
	<th><?php echo Lang('coins'); ?></th>
	<th><?php echo Lang('prestige'); ?></th>
	<th><?php echo Lang('MSG'); ?></th>
	</tr>

	<?php }else if ($key == 'item_attribute_stone') { ?>
	<tr>
	<th>&nbsp;</th>
	<th><?php echo Lang('grid_postion'); ?></th>
	<th><?php echo Lang('name'); ?></th>
	<th><?php echo Lang('save_num'); ?></th>
	<th><?php echo Lang('level'); ?></th>
	</tr>

	<?php }else if ($key == 'friends') { ?>
	<tr>
	<th>&nbsp;</th>
	<th><?php echo Lang('player'); ?></th>
	<th><?php echo Lang('group'); ?></th>
	<th><?php echo Lang('attention_date'); ?></th>
	</tr>
	<?php } ?>
	</thead>
	<tbody id="info_list">
		   
	</tbody>
	</table>
	</div>
</div>