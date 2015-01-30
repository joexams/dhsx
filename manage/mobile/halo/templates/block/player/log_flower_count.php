<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, list, typelist;
function getsearchlogflower_countList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_flower_count_submit').serialize(),
			success: showlogflower_countList
		});
	});
	return false;
}

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

function showlogflower_countList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typelist = data.type;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		$( "#flower_countpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogflower_countList });

		$( "#list" ).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list" );
			$( "#list" ).stop(true,true).hide().slideDown(400);
		}
	}
	return false;
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('flower').Lang('log'); ?>');
	}

	$('.h_lib_nav').on('click', 'a.flower_count_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_flower_count_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_flower_count_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogflower_countList(1);
	});

	$('#get_search_flower_count_submit').submit();
});
</script>
<script type="text/template" id="flower_counttypetpl">
<li><a href="javascript:;" data-id="${id}" class="flower_count_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', send_time)}</td>
<td>${flower_count}</td>
<td>{{html player_id_to_name(from_player_id)}}</td>
<td>{{html player_id_to_name(player_id)}}</td>
<td>${player_id}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_flower_count_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<select name="player_type" id="player_type">
						<option value="0"><?php echo Lang('receive_flower'); ?></option>
						<option value="1"><?php echo Lang('send_flower'); ?></option>
					</select>
				</p>
			</li>
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
					<input type="hidden" name="key" value="flower_count">
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
				    <th style="width:120px;"><?php echo Lang('send_time'); ?></th>
				    <th style="width:80px;"><?php echo Lang('flower_count'); ?></th>
				    <th><?php echo Lang('send_flower'); ?></th>
				    <th><?php echo Lang('receive_flower'); ?></th>
				    <th style="width:50px;"><?php echo Lang('receive_flower_id'); ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
		<div class="pagination" id="flower_countpager"></div>
	</div>

</div>
