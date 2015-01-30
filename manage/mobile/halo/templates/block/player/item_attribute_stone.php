<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function getstoneList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=item_attribute_stone";
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
$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']); ?>"+'->仙石背包');
	}
	getstoneList();

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${grid_id_to_name(grid_id)}</td>
<td>${item_id_to_name(item_id)}</td>
<td>${number}</td>
<td>${item_id_to_lv(item_id)}</td>
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
				    <th style="width:120px;"><?php echo Lang('name'); ?></th>
				    <th style="width:80px;"><?php echo Lang('save_num'); ?></th>
				    <th style="width:50px;"><?php echo Lang('level'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
</div>