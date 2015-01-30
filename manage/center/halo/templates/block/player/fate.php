<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;

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
$(function(){
	var url = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=fate";
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
				$( "#info_list").html('<tr><td colspan="6" style="text-align: left">没有找到数据。</td></tr>').show();
			}
		}
	}, 1);

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td class="num">${id}</td>
<td>{{html fate_id_to_name(fate_id)}}</td>
<td>${fate_level}</td>
<td>${experience}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${grid}</td>
</tr>
</script>

<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th>&nbsp;</th>
		    <th><?php echo Lang('fate') ?></th>
		    <th><?php echo Lang('level') ?></th>
		    <th><?php echo Lang('experience') ?></th>
		    <th><?php echo Lang('wear_role') ?></th>
		    <th><?php echo Lang('packet_index') ?></th>
		</tr>
		</thead>
		<tbody id="info_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>
