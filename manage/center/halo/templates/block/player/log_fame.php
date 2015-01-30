<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist;
function getsearchlogfameList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_fame_submit').serialize()+'&typeflag='+typeflag,
			success: showlogfameList
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

function showlogfameList( data ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#fametypetpl').tmpl( data.type.cons ).appendTo('#famecons');
			$('#fametypetpl').tmpl( data.type.get ).appendTo('#fameget');
		}

		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;

		$( "#famepager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogfameList });
		$('#list_'+pid).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list_"+pid);
			$('#list_'+pid).stop(true,true).hide().slideDown(400);

			if (pageCount > 1){
				$('#list_'+pid).parent().parent('div.content').css('height', $('#list_'+pid).parent('table.global').css('height'));
			}
		}
	}
	return false;
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('fame_log'); ?>");
	}

	$('.h_lib_nav').on('click', 'a.fame_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_fame_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_fame_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogfameList(1);
	});

	$('#get_search_fame_submit').submit();
});
</script>
<script type="text/template" id="fametypetpl">
<li><a href="javascript:;" data-id="${id}" class="fame_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', op_time)}</td>
<td><span class="greentitle">+${value}</span></td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_fame_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="famecons">
	</ul>
	<ul id="fameget">
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
					<input type="hidden" name="key" value="fame">
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
				    <th style="width:10%"><?php echo Lang('plus_sub'); ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="famepager"></div>
</div>
