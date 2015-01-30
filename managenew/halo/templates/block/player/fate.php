<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function getfateList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&player_role_id=<?php echo $data['player_role_id']; ?>&key=fate";
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
$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']); ?>"+'->玩家命格');
	}
	getfateList();

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>{{html fate_id_to_name(fate_id)}}</td>
<td>${fate_level}</td>
<td>${experience}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>${grid}</td>
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
				    <th><?php echo Lang('fate') ?></th>
				    <th style="width:80px;"><?php echo Lang('level') ?></th>
				    <th style="width:80px;"><?php echo Lang('experience') ?></th>
				    <th><?php echo Lang('wear_role') ?></th>
				    <th style="width:80px;"><?php echo Lang('packet_index') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
</div>
