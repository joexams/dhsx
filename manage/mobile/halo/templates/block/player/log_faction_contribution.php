<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, typelist, list;
function getsearchlogfactioncontriList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_factioncontri_submit').serialize()+'&typeflag='+typeflag,
			success: showlogfactioncontriList
		});
	});
	return false;
}

function showlogfactioncontriList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#factioncontristypetpl').tmpl( data.type.get ).appendTo('#factioncontrisget');
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		$( "#factioncontrispager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogfactioncontriList });
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
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('factioncontri_log'); ?>");
	}

	$('.h_lib_nav').on('click', 'a.factioncontris_type', function(){
		typeid = $(this).attr('data-id');
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_factioncontri_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_factioncontri_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogfactioncontriList(1);
	});

	$('#get_search_factioncontri_submit').submit();;
});
</script>
<script type="text/template" id="factioncontristypetpl">
<li><a href="javascript:;" data-id="${id}" class="factioncontris_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', time)}</td>
<td>${value}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="h_lib_nav">
	<ul id="factioncontrisget">
	</ul>
	</div>
	<br class="clear">
	<form id="get_search_factioncontri_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<input type="hidden" name="key" value="faction_contribution">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
			</ul>
		</div>
	</form>
	<br class="clear">

	<div class="content ">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:120px;"><?php echo Lang('op_time') ?></th>
				    <th style="width:20%"><?php echo Lang('faction_contribution') ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="factioncontrispager"></div>
</div>
