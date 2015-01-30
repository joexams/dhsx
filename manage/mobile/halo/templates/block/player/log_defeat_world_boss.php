<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, list, typeflag = 0, typelist;
function getsearchlogdefeat_world_bossList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_defeat_world_boss_submit').serialize()+'&typeflag='+typeflag,
			success: showlogdefeat_world_bossList
		});
	});
	return false;
}

function showlogdefeat_world_bossList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		$( "#defeat_world_bosspager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogdefeat_world_bossList });

		$( "#list" ).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list" );
			$( "#list" ).stop(true,true).hide().slideDown(400);
		}
	}
	return false;
}
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
	return '';
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo 'Boss'.Lang('log'); ?>');
	}

	$('.h_lib_nav').on('click', 'a.defeat_world_boss_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_defeat_world_boss_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_defeat_world_boss_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogdefeat_world_bossList(1);
	});

	$('#get_search_defeat_world_boss_submit').submit();
});
</script>
<script type="text/template" id="defeat_world_bosstypetpl">
<li><a href="javascript:;" data-id="${id}" class="defeat_world_boss_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${year}-${month}-${day}</td>
<td>${rank}</td>
<td>${is_defeat == 1 ? 'YES' :'-'}</td>
<td>${boss_id_to_name(world_boss_id)} (Lv.${monster_level})</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_defeat_world_boss_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<?php echo Lang('date'); ?>：
					<input name="datetime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" type="text" value="">
				</p>
			</li>
			<li class="nobg">
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="defeat_world_boss">
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
				    <th style="width:120px;"><?php echo Lang('date'); ?></th>
				    <th style="width:10%"><?php echo Lang('ranking'); ?></th>
				    <th style="width:10%"><?php echo Lang('killer'); ?></th>
				    <th style="width:10%"><?php echo Lang('Boss'); ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
		<div class="pagination" id="defeat_world_bosspager"></div>
	</div>
</div>