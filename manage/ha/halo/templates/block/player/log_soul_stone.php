<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist;
function getlogsoul_stoneList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record",
	params = {sid: '<?php echo $data['sid']; ?>', id: '<?php echo $data['id']; ?>', key: 'soul_stone', typeflag: typeflag, top: index, typeid: typeid, recordnum: recordNum};
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: params,
			success: showlogsoul_stoneList
		});
	});
	return false;
}
function getsearchlogsoul_stoneList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_soul_stone_submit').serialize(),
			success: function(data){
				showlogsoul_stoneList(data, 1);
			}
		});
	});
	return false;
}
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
	return '';
}
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
function showlogsoul_stoneList( data, type ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#soul_stonetypetpl').tmpl( data.type.cons ).appendTo('#soul_stonecons');
			$('#soul_stonetypetpl').tmpl( data.type.get ).appendTo('#soul_stoneget');
		}

		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;

		if (type != undefined && type == 1){
			$( "#soul_stonepager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogsoul_stoneList });
		}else {
			$( "#soul_stonepager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogsoul_stoneList });
		}

		$('#list_'+pid).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list_"+pid);
			$('#list_'+pid).stop(true,true).hide().slideDown(400);
		}
	}
	return false;
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('soul_stone_log'); ?>");
	}
	getlogsoul_stoneList( pageIndex );

	$('.h_lib_nav').on('click', 'a.soul_stone_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_soul_stone_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_soul_stone_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogsoul_stoneList(1);
	});
});
</script>
<script type="text/template" id="soul_stonetypetpl">
<li><a href="javascript:;" data-id="${id}" class="soul_stone_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', change_time)}</td>
<td>${player_soul_id}</td>
<td>{{if type_id_to_status(type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_soul_stone_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="soul_stonecons">
	</ul>
	<ul id="soul_stoneget">
	</ul>
	</div>
	<br class="clear">
	<div class="nav singlenav">
		<ul class="nav_li">
			<?php if ($data['id'] <= 0 && empty($_GET['title'])){ ?>
			<li>
				<p>
					<?php echo Lang('player'); ?>：
					<select name="playertype">
						<option value=""><?php echo Lang('player_name'); ?></option>
						<option value="1"><?php echo Lang('player_nick'); ?></option>
					</select>
					<input type="text" name="playername">
				</p>
			</li>
			<?php } ?>
			<li>
				<p>
					<?php echo Lang('between_date'); ?>：
				<input name="starttime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value="">
					<input name="endtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value=""></p>
			</li>
			<li class="nobg">
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="soul_stone">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
		</ul>
	</div>
	</form>
	<div class="content">
		<table class="global" width="100%" style="max-width:800px;min-width:600px;" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:120px;"><?php echo Lang('op_time') ?></th>
				    <th style="width:50px;"><?php echo Lang('player_soul_id'); ?></th>
				    <th style="width:80px;"><?php echo Lang('plus_sub'); ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
		<div class="pagination" id="soul_stonepager"></div>
	</div>
</div>
