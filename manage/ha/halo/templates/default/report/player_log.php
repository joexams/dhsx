<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, typelist;
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'log_list';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '没有找到玩家记录。'
Ha.page.url = "<?php echo INDEX; ?>?m=server&c=get&v=player_record";
Ha.page.queryData = 'key=<?php echo $key ?>&sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&tbl=<?php echo $tbl_default; ?>&playername=<?php echo $data['playername']?>&typeflag='+typeflag;

function type_id_to_name(id){
	if (typelist != undefined){
		if (typelist.cons != undefined && typelist.cons.length > 0){
			for (var key in typelist.cons){
				if (typelist.cons[key].id == id){
					return typelist.cons[key].name;
				}
			}
		}
		if (typelist.get != undefined && typelist.get.length > 0){
			for (var key in typelist.get){
				if (typelist.get[key].id == id){
					return typelist.get[key].name;
				}
			}
		}
	}
	return '--';
}


<?php if ($key == 'soul') { ?>
function soul_id_to_name(id){
	if (typelist != undefined){
		if (typelist.soul != undefined && typelist.soul.length > 0){
			for (var key in typelist.soul){
				if (typelist.soul[key].id == id){
					return typelist.soul[key].name + '('+typelist.soul[key].qualityname+')';
				}
			}
		}
	}
	return '--';
}

function attr_id_to_name(id, value){
	var attrname = '';
	if (typelist != undefined){
		if (typelist.attribute != undefined && typelist.attribute.length > 0){
			for (var key in typelist.attribute){
				if (typelist.attribute[key].id == id){
					attrname =  typelist.attribute[key].name + '+'+value;
					return attrname;
				}
			}
		}
	}
	return '--';
}
<?php }else if ($key == 'item' || $key == 'elixir') { ?>
function item_id_to_name(id){
	if (typelist != undefined){
		if (typelist.item != undefined && typelist.item.length > 0){
			for (var key in typelist.item){
				if (typelist.item[key].id == id){
					return typelist.item[key].name;
				}
			}
		}
	}
	return '--';
}
function item_id_to_lv(item_id) {
	if (typelist != undefined){
		if (typelist.attribute != undefined && typelist.attribute.length > 0){
			for (var key in typelist.attribute){
				if (typelist.attribute[key].item_id == item_id){
					return typelist.attribute[key].lv;
				}
			}
		}
	}
	return '1';
}
<?php }else if ($key == 'fate') { ?>
function fate_id_to_name(id, fate_experience){
	var str = '', level = '';
	fate_experience = parseInt(fate_experience);
	if (typelist != undefined){
		if (typelist.fate != undefined && typelist.fate.length > 0){
			for (var key in typelist.fate){
				if (typelist.fate[key].id == id){
					str = typelist.fate[key].name;
					if (fate_experience > 0 && fate_experience >= typelist.fate[key].request_experience) {
						level = ' (Lv.'+typelist.fate[key].level+')';
					}
				}
			}
		}
	}
	if (str != '' && level == '') {
		level = ' (Lv.1)';
	}
	return str+level;
}

<?php }else if ($key == 'level_up') { ?>
function role_id_to_name(id){
	if (typelist != undefined){
		if (typelist.roles != undefined && typelist.roles.length > 0){
			for (var key in typelist.roles){
				if (typelist.roles[key].id == id){
					return typelist.roles[key].name;
				}
			}
		}
	}
	return '--';
}
<?php }else if ($key == 'defeat_world_boss') { ?>
function boss_id_to_name(id){
	if (typelist != undefined){
		if (typelist.boss != undefined && typelist.boss.length > 0){
			for (var key in typelist.boss){
				if (typelist.boss[key].id == id){
					return  typelist.boss[key].name;
				}
			}
		}
	}
	return '--';
}
<?php }else if ($key == 'long_yu_ling') { ?>
function type_id_to_status(id){
	if (typelist != undefined){
		if (typelist.cons != undefined && typelist.cons.length > 0){
			for (var key in typelist.cons){
				if (typelist.cons[key].id == id && typelist.cons[key].type == 0){
					return 1;
				}
			}
		}
	}
	return 0;
}
<?php }else if ($key == 'deploy_start') { ?>
function attr_id_to_name(id, value){
	var attrname = '';
	if (typelist != undefined){
		if (typelist.attribute != undefined && typelist.attribute.length > 0){
			for (var key in typelist.attribute){
				if (typelist.attribute[key].id == id){
					attrname =  typelist.attribute[key].name + '+'+value;
					if (typelist.attribute[key].unit < 1){
						attrname +='%';
					}
					return attrname;
				}
			}
		}
	}
	return '--';
}
<?php }else if ($key == 'flower_count') { ?>
function player_id_to_name(id){
	if (typelist != undefined){
		if (typelist.players != undefined && typelist.players.length > 0){
			var rtnname = '';
			for (var key in typelist.players){
				if (typelist.players[key].id == id){
					rtnname = '<p style="float: left"><span class="orangetitle">'+typelist.players[key].username + '</span>';
					if (typelist.players[key].nickname != ''){
						rtnname = rtnname + '('+typelist.players[key].nickname+')';
					}
					rtnname = rtnname + '</p><p style="float: right;"><span class="graptitle">';
					switch(typelist.players[key].is_tester){
						case '1':
							rtnname += '测试号';
							break;
						case '2':
							rtnname += '高级测试号';
							break;
						case '3':
							rtnname += 'GM';
							break;
						case '4':
							rtnname += '新手指导员';
							break;
					}
					rtnname += '</span></p>';
					return  rtnname;
				}
			}
		}
	}
	return '';
}
<?php }else if ($key == 'take_bible') { ?>
function player_id_to_name(id){
	if (typelist != undefined){
		if (typelist.players != undefined && typelist.players.length > 0){
			var rtnname = '';
			for (var key in typelist.players){
				if (typelist.players[key].id == id){
					rtnname = '<span class="orangetitle">'+typelist.players[key].username + '</span>';
					if (typelist.players[key].nickname != ''){
						rtnname = rtnname + '('+typelist.players[key].nickname+')';
					}
					return  rtnname;
				}
			}
		}
	}
	return '--';
}
function npc_id_to_name(id){
	var npc = {1: '白龙马', 2: '沙悟净', 3: '猪八戒', 4: '孙悟空', 5: '唐僧'};
	if (typeof npc[id] != 'undefined'){
		return npc[id];
	}
	return '';
}
<?php }else if ($key == 'farmland') { ?>
function herb_id_to_name(id){
	if (typelist != undefined){
		if (typelist.herbs != undefined && typelist.herbs.length > 0){
			for (var key in typelist.herbs){
				if (typelist.herbs[key].id == id){
					return typelist.herbs[key].name;
				}
			}
		}
	}
	return '--';
}
<?php }else if ($key == 'mission') { ?>
function mission_id_to_name(id){
	if (typelist != undefined){
		if (typelist != undefined){
			for (var key in typelist){
				if (key == id){
					return typelist[key].sectionname +"-"+ typelist[key].missionname;
				}
			}
		}
	}
	return '--';
}
<?php } ?>

<?php if (in_array($key, array('item', 'soul', 'fate'))) { ?>
var chklist;
function list_id_to_check(id) {
	if (typeof chklist != 'undefined') {
		for(var key in chklist) {
			if (chklist[key].id == id) {
				return true;
			}
		}
	}
	return false;
}
<?php } ?>

$(function(){
	
	Ha.page.getList(1, function(data){
//		var getnum = 0, connum = 0;
		if (data.status == 1){
			Ha.notify.show(data.msg, '', 'error');
		}else {
			if (data.type != undefined){
				typeflag = 1;
				typelist = data.type;
//				if (typeof(data.allnum) != 'undefined') {
//					if (typeof(data.allnum.getnum) != 'undefined') {
//						getnum = data.allnum.getnum;
//					}
//					if (typeof(data.allnum.connum) != 'undefined') {
//						connum = data.allnum.connum;
//					}
//				}
				<?php if (in_array($key, array('item', 'soul', 'fate'))) { ?>
				chklist = data.chklist;
				<?php } ?>
				$('#typetpl').tmpl( data.type.cons ).appendTo('#selectCon');
				$('#typetpl').tmpl( data.type.get ).appendTo('#selectGet');
			}
		}
//		$('#total_get').html(getnum);
//		$('#total_con').html(connum);
	});

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});

	$('#log_list').on('change', 'input:checkbox', function(e){
		var num = $('#chk-record').html();
		var id = $(this).val(), name = $(this).attr('data-name');
		var data = $(this).attr('data-json');
		num = !isNaN(parseInt(num)) ? parseInt(num) : 0;
		if ($(this).is(':checked')) {
			$('<input type="hidden" id="<?php echo $key ?>_'+id+'" value=\''+data+'\' data-name="'+name+'">').appendTo('#chk-area');
			$('#chk-record').html(num+1);
		}else {
			if (num > 0) {
				num = num-1;
				$('#chk-record').html(num);
				$('#<?php echo $key ?>_'+id).remove();
			}
			if (num <= 0) {
				$('#<?php echo $key ?>_'+id).remove();
			}
		}

	});

	$('#chk-btn').on('click', function(e){
		var num = $('#chk-record').html();
		num = !isNaN(parseInt(num)) ? parseInt(num) : 0;
		if (num > 0) {
			var url = "<?php echo INDEX; ?>?m=operation&c=interactive&v=add_retrieve";
			var queryData = 'sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&playername=<?php echo $_GET['title'] ?>&nickname=<?php echo $_GET['nickname'] ?>&num='+num;
			Ha.common.ajax(url, 'html', queryData, 'get', 'table_column', function(data){
				Ha.Dialog.show(data, '被盗找回', 500, 'rebackDlg');
			}, 1);
		}
	});

	$('#moreConditions').on('click', 'a.type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).hasClass('active') === true){
				$(this).removeClass('active');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).addClass('active');
				$('#get_search_type_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_type_submit').on('submit', function(e){
		e.preventDefault();

		Ha.page.queryData = 'key=<?php echo $key ?>&sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&tbl=<?php echo $tbl_default; ?>&playername=<?php echo $data['title']?>&typeflag='+typeflag+'&'+$('#get_search_type_submit').serialize();
		Ha.page.recordNum = 0;
		Ha.page.getList(1);
	});
});
</script>

<script type="text/template" id="typetpl">
<a href="javascript:;" data-id="${id}" class="type">${name}</a>
</script>
<script type="text/template" id="log_listtpl">

<?php if ($key == 'coin') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td>{{if value>0}}<strong class="greentitle">+${value}</strong>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(type)}</td>
</tr>
<?php }else if ($key == 'coin_tree_count') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if value>0}}<strong class="greentitle">+${value}</strong>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'crystal') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if value>0}}<strong class="greentitle">+${value}</strong>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'defeat_world_boss') { ?>
<tr>
<td class="num">${id}</td>
<td>${year}-${month}-${day}</td>
<td>${rank}</td>
<td>${is_defeat == 1 ? 'YES' :'-'}</td>
<td>${boss_id_to_name(world_boss_id)} (Lv.${monster_level})</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'deploy_start') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', opt_time)}</td>
<td>{{if start_type == 1}}阵碎星{{else}}陨星{{/if}}</td>
<td>${attr_id_to_name(war_attribute_type_1, war_attribute_value_1)}</td>
<td>${attr_id_to_name(war_attribute_type_2, war_attribute_value_2)}</td>
<td>${attr_id_to_name(war_attribute_type_3, war_attribute_value_3)}</td>
<td>${type_id_to_name(opt_type)}</td>
</tr>
<?php }else if ($key == 'elixir') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', time)}</td>
<td>${item_id_to_name(item_id)}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'ingot') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td><strong class="greentitle">{{if value>0}}+{{/if}}${value}</strong></td>
<td><strong class="redtitle">{{if change_charge_value != ''}}${change_charge_value}{{else}}0{{/if}}</strong></td>
<td>${after_change_ingot}</td>
<td>{{if new_charge_ingot>0}}${new_charge_ingot}{{else}}0{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'marry_favor') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>${value}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'fame') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td><span class="greentitle">+${value}</span></td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'power') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if value>0}}<span class="greentitle">+${value}</span>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'fate') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if op_type > 3 && op_type < 8  && list_id_to_check(id) === false}}<input type="checkbox" name="fate" value="${id}" data-name="${fate_id_to_name(fate_id, fate_experience)}" data-json='{"id":"${id}","name":"${name}","fate_id":"${fate_id}","actived_fate_id2":"${actived_fate_id2}","actived_fate_id1":"${actived_fate_id1}","level": "${level}","number":1}'> <strong>${fate_id_to_name(fate_id, fate_experience)}</strong>{{else}}${fate_id_to_name(fate_id, fate_experience)}{{/if}} </td>
<td><span class="greentitle">${fate_experience}</span></td>
<td>{{if op_type == 4}}被 <strong>${fate_id_to_name(merge_fate_id, merge_fate_experience)}</strong> {{/if}}${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'item') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td><label>{{if (type == 4 || type == 9) && list_id_to_check(id) == false}}<input type="checkbox" name="item" value="${id}" data-name="${item_id_to_name(item_id)}" data-level="${item_lv}" data-json='{"id":"${id}","name":"${item_id_to_name(item_id)}","item_id":"${item_id}","level":"${item_lv}","number":1}'>{{/if}}  <strong>${item_id_to_name(item_id)}</strong>  (Lv.${item_lv})</label></td>
<td>{{if value>0}}+{{/if}}${value}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'soul_stone') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td>${player_soul_id}</td>
<td>{{if type_id_to_status(type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'soul') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td><label>{{if type == 1 && list_id_to_check(id) === false}}<input type="checkbox" name="soul" value="${id}" data-name="${soul_id_to_name(soul_id)}" data-json='{"id":"${id}","name":"${soul_id_to_name(soul_id)}","soul_id":"${soul_id}","attributeid1":"${soul_attribute_id_location_1}","attributevalue1":"{{html soul_attribute_value_location_1*10}}","attributeid2":"${soul_attribute_id_location_2}","attributevalue2":"{{html soul_attribute_value_location_2*10}}","attributeid3":"${soul_attribute_id_location_3}","attributevalue3":"{{html soul_attribute_value_location_3*10}}","attributeid4":"${soul_attribute_id_location_4}","attributevalue4":"{{html soul_attribute_value_location_4*10}}","key":"${key}","number":1}'>{{/if}} ${soul_id_to_name(soul_id)}</label></td>
<td>${attr_id_to_name(soul_attribute_id_location_1, soul_attribute_value_location_1)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_2, soul_attribute_value_location_2)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_3, soul_attribute_value_location_3)}</td>
{{if typeof soul_attribute_id_location_4 != 'undefined'}}
<td>${attr_id_to_name(soul_attribute_id_location_4, soul_attribute_value_location_4)}</td>
{{else}}
<td>-</td>
{{/if}}
<td>${key}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'skill') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', op_time)}</td>
<td>{{if value>0}}<span class="greentitle">+${value}</span>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'take_bible') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', op_time)}</td>
<td>${npc_id_to_name(npc)}</td>
<td>${type_id_to_name(op_type)} {{if be_rob_player_id > 0}} {{html player_id_to_name(be_rob_player_id)}}</span>  {{html rob_result == 0 ? '<?php echo Lang('failure'); ?>': '<span class="greentitle"><?php echo Lang('success'); ?></span>'}}{{/if}}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'farmland') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', harvest_time)}</td>
<td>{{if ripe_time > 0}}${date('Y-m-d H:i', ripe_time)}{{else}}--{{/if}}</td>
<td>${herb_id_to_name(herbs_id)}</td>
<td>${exp}</td>
<td>{{if add_exp>0}}<span class="greentitle">+${add_exp}</span>{{else}}${add_exp}{{/if}}</td>
<td>${date('Y-m-d H:i', timestamp)}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'flower_count') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', send_time)}</td>
<td>${flower_count}</td>
<td>{{html player_id_to_name(from_player_id)}}</td>
<td>{{html player_id_to_name(player_id)}}</td>
<td>${player_id}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'state_point') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', time)}</td>
<td>{{if type_id_to_status(type) == 0}}+${value}{{else}}<span class="greentitle">-${value}</span>{{/if}}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'level_up') { ?>
<tr>
<td class="num">${id}</td>
<td><strong>${role_id_to_name(player_role_id)}</strong></td>
<td><span class="greentitle">${level}</span></td>
<td>${date('Y-m-d H:i', time)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'faction_contribution') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', time)}</td>
<td>${value}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'spirit') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td>{{if type_id_to_status(type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'item_attribute_stone') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', change_time)}</td>
<td><label>{{if type == 16 && item_id_to_check(id) == false}}<input type="checkbox" name="item_attribute_stone" value="${id}" data-name="${item_id_to_name(item_id)}">{{/if}}  <strong>${item_id_to_name(item_id)}</strong>  (Lv.${item_id_to_lv(item_id)})</label></td>
<td>{{if (type == 3 || type == 4 || type == 8 || type == 11 || type == 16 || type == 17)}}-${value}{{else}}<span class="greentitle">+${value}{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'mission') { ?>
<tr>
<td class="num">${mission_id}</td>
<td>${mission_id_to_name(mission_id)}</td>
<td>${times}</td>
<td>${failed_challenge}</td>
<td>${rank}</td>
<td>{{if is_finished>0}}<span class="greentitle">OK</span>{{else}}<span class="redtitle">NO</span>{{/if}}</td>
<td>${date('Y-m-d H:i',first_challenge_time)}</td>
<td>${date('Y-m-d H:i',challenge_time)}</td>
<td>${hero_remain_times}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'peach') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i', time)}</td>
<td><span class="greentitle">+${exp}</span></td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'xian_ling') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if type_id_to_status(op_type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'xianling_tree') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if type_id_to_status(op_type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php }else if ($key == 'long_yu_ling') { ?>
<tr>
<td class="num">${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if type_id_to_status(op_type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
</tr>
<?php } ?>
</script>

<?php if (in_array($key, array('item', 'soul', 'fate'))) { ?>
<div id="chk-btn" class="kf">
找<br>回<br><span class="orangetitle" id="chk-record">0</span><br>件
<div id="chk-area" style="display:none">
</div>
</div>
<?php } ?>

<div class="toolbar">
	<div class="tool_date cf">
		<form id="get_search_type_submit" action="" method="get" name="form">
		<div class="title cf">	
			<div class="tool_group">
				<label><?php echo Lang('between_date'); ?>：</label>
				<input name="starttime" class="ipt_txt" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value=""  style="width:125px;"> - 
				<input name="endtime" class="ipt_txt" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value=""  style="width:125px;">
				<?php if ($tbl_default == 1) { ?>
				<label><?php echo Lang('db_table'); ?>：</label>
				<select name="tbl" id="tbl" class="ipt_select" style="width:100px;">
					<option value="1"><?php echo Lang('new_db_table'); ?></option>
					<option value="0"><?php echo Lang('old_db_table'); ?></option>
				</select>
				<?php } ?>
				
				<input name="cid" type="hidden" value="<?php echo $data['cid'] ?>">
				<input name="sid" type="hidden" value="<?php echo $data['sid'] ?>">
				<input type="hidden" name="key" value="<?php echo $key ?>">
				<input type="hidden" name="typeflag" value="1">
				<input name="dogetSubmit" type="hidden" value="1">
				<input type="submit" class="btn_sbm" value="查询" id="query">
			</div>
			<div class="more">
				<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>类别筛选</a>
			</div>
		</div>
		<div class="control cf" id="moreConditions" style="display: none;">
			<div class="frm_cont">
				<ul>
					<li name="condition" id="selectGet">
						<label class="frm_info">获取类：</label>
					</li>
					<li name="condition" id="selectCon">
						<label class="frm_info">消耗类：</label>
					</li>
				</ul>
			</div>
		</div>
	</form>
</div>		
</div>

<div class="column cf" id="table_column">
	<div id="dataTable">
	<table>
	<thead>
	<?php if ($key == 'coin') { ?>
	<tr id="dataTheadTr">
	    <th>&nbsp;</th>
	    <th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('coins') ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	</tr>
	<?php }else if ($key == 'coin_tree_count') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
		<th><?php echo Lang('plus_sub'); ?></th>
		<th><?php echo Lang('op_type') ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'crystal') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('crystals') ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'defeat_world_boss') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('date'); ?></th>
	    <th><?php echo Lang('ranking'); ?></th>
	    <th><?php echo Lang('killer'); ?></th>
	    <th><?php echo Lang('Boss'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'deploy_start') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		 <th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('deploy_start') ?></th>
	    <th><?php echo Lang('attribute') ?>1</th>
	    <th><?php echo Lang('attribute') ?>2</th>
	    <th><?php echo Lang('attribute') ?>3</th>
	    <th style="width:50px;"><?php echo Lang('open_priv') ?></th>
	    <th style="width:20%"><?php echo Lang('op_type') ?></th>
	</tr>
	<?php }else if ($key == 'elixir') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('elixir'); ?></th>
	    <th><?php echo Lang('role'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'ingot') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
		<th><?php echo Lang('change_ingot'); ?></th>
		<th><?php echo Lang('change_charge_value'); ?></th>
		<th><?php echo Lang('after_change_ingot'); ?></th>
		<th><?php echo Lang('new_charge_ingot'); ?></th>
		<th><?php echo Lang('op_type'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'marry_favor') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('marry_favors') ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'fame') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'power') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('power'); ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'fate') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('fate'); ?></th>
	    <th><?php echo Lang('fate').Lang('experience'); ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'item') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('item_name'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'soul_stone') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('player_soul_id'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'soul_stone') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('player_soul_id'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'soul') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
		<th><?php echo Lang('soul') ?></th>
		<th><?php echo Lang('attribute') ?>1</th>
		<th><?php echo Lang('attribute') ?>2</th>
		<th><?php echo Lang('attribute') ?>3</th>
		<th><?php echo Lang('attribute') ?>4</th>
		<th><?php echo Lang('open_priv') ?></th>
		<th><?php echo Lang('op_type') ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'skill') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
		<th><?php echo Lang('skill'); ?></th>
		<th><?php echo Lang('op_type'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'take_bible') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
		<th><?php echo Lang('take_bible_npc'); ?></th>
		<th><?php echo Lang('op_type') ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'farmland') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('mature_time'); ?></th>
		<th><?php echo Lang('required_time'); ?></th>
		<th><?php echo Lang('herb'); ?></th>
		<th><?php echo Lang('should_get_exp'); ?></th>
		<th><?php echo Lang('actual_plus_exp'); ?></th>
		<th><?php echo Lang('op_time'); ?></th>
		<th><?php echo Lang('role'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'flower_count') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('send_time'); ?></th>
	    <th><?php echo Lang('flower_count'); ?></th>
	    <th><?php echo Lang('send_flower'); ?></th>
	    <th><?php echo Lang('receive_flower'); ?></th>
	    <th><?php echo Lang('receive_flower_id'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'state_point') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('role'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'level_up') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('role'); ?></th>
	    <th><?php echo Lang('level'); ?></th>
	    <th><?php echo Lang('level_time'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'faction_contribution') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time') ?></th>
	    <th><?php echo Lang('faction_contribution') ?></th>
	    <th><?php echo Lang('op_type') ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'spirit') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'item_attribute_stone') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('item_attribute_stone_name'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'mission') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('副本'); ?></th>
		<th><?php echo Lang('挑战次数'); ?></th>
		<th><?php echo Lang('战败次数'); ?></th>
		<th><?php echo Lang('评定'); ?></th>
		<th><?php echo Lang('完成'); ?></th>
		<th><?php echo Lang('第一过关时间'); ?></th>
		<th><?php echo Lang('最后过关时间'); ?></th>
		<th><?php echo Lang('英雄副本剩余次数'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'peach') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('plus_exp'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'xian_ling') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
		<th><?php echo Lang('plus_sub'); ?></th>
		<th><?php echo Lang('op_type'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'xianling_tree') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
		<th><?php echo Lang('plus_sub'); ?></th>
		<th><?php echo Lang('op_type'); ?></th>
		<th><?php echo Lang('player') ?></th>
	</tr>
	<?php }else if ($key == 'long_yu_ling') { ?>
	<tr id="dataTheadTr">
		<th>&nbsp;</th>
		<th><?php echo Lang('op_time'); ?></th>
	    <th><?php echo Lang('plus_sub'); ?></th>
	    <th><?php echo Lang('op_type'); ?></th>
	    <th><?php echo Lang('player') ?></th>
	</tr>
	<?php } ?>
	</thead>
	<tbody id="log_list">
		   
	</tbody>
	</table>
	</div>
	<div id="pageJs" class="page">
	    <div id="pager" class="page">
	    </div>
	</div>
</div>
