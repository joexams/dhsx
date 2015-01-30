<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function getfateList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=gift";
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
function type_id_to_name(id){
	if (extendlist.gift != undefined && extendlist.gift.length > 0){
		for (var key in extendlist.gift){
			if (extendlist.gift[key].id == id){
				return extendlist.gift[key].name;
			}
		}
	}
	return '';
}
$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo urldecode($_GET['title']); ?>"+'玩家礼包');
	}
	getfateList();

	extendlist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${type_id_to_name(type)}</td>
<td><span class="orangetitle">${ingot}</span></td>
<td>${coins}</td>
<td>${fame}</td>
<td>${message}</td>
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
				    <th style="width:120px;"><?php echo Lang('type'); ?></th>
				    <th style="width:80px;"><?php echo Lang('ingot'); ?></th>
				    <th style="width:80px;"><?php echo Lang('coins'); ?></th>
				    <th style="width:80px;"><?php echo Lang('prestige'); ?></th>
				    <th><?php echo Lang('MSG'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
</div>