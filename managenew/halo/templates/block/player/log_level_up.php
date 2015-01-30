<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, list, typelist;
function getsearchloglevel_upList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_level_up_submit').serialize(),
			success: showloglevel_upList
		});
	});
	return false;
}

function showloglevel_upList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typelist = data.type;
		}
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		$( "#level_uppager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchloglevel_upList });

		$( "#list" ).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list" );
			$( "#list" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#list" ).parent().parent('div.content').css('height', $('#list').parent('table.global').css('height'));
			}
		}
	}
	return false;
}
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
	return '';
}

$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('level_up').Lang('log'); ?>');
	}

	$('.h_lib_nav').on('click', 'a.level_up_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_level_up_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_level_up_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchloglevel_upList(1);
	});

	$('#get_search_level_up_submit').submit();
});
</script>
<script type="text/template" id="level_uptypetpl">
<li><a href="javascript:;" data-id="${id}" class="level_up_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td><strong>${role_id_to_name(player_role_id)}</strong></td>
<td><span class="greentitle">${level}</span></td>
<td>${date('Y-m-d H:i', time)}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_level_up_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<input type="hidden" name="key" value="level_up">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
		</ul>
	</div>
	</form>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:10%"><?php echo Lang('role'); ?></th>
				    <th style="width:80px;"><?php echo Lang('level'); ?></th>
				    <th style="width:120px;"><?php echo Lang('level_time'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="level_uppager"></div>
</div>
