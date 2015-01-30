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
$(function(){
	var url = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
		data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=role_elixir";
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
				$( "#info_list").html('<tr><td colspan="3" style="text-align: left">没有找到数据。</td></tr>').show();
			}
		}
	}, 1);

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td class="num">${item_id}</td>
<td>${item_id_to_name(item_id)}</td>
<td>${times}</td>
</tr>
</script>


<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th>&nbsp;</th>
	     	<th><?php echo Lang('name'); ?></th>
			<th><?php echo Lang('times'); ?></th>
		</tr>
		</thead>
		<tbody id="info_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>