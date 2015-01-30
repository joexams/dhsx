<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeflag = 0, list, typelist;
function getlogingotList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=ingot&typeflag="+typeflag+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: showlogingotList
		});
	});
	return false;
}
function getsearchlogingotList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_ingot_submit').serialize(),
			success: function(data){
				showlogingotList(data, 1);
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
function showlogingotList( data, type ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#ingottypetpl').tmpl( data.type.cons ).appendTo('#ingotcons');
			$('#ingottypetpl').tmpl( data.type.get ).appendTo('#ingotget');
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		if (type != undefined && type == 1){
			$( "#ingotpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogingotList });
		}else {
			$( "#ingotpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogingotList });
		}
		$( "#list" ).empty();
		if (pageIndex == 1){
			$('#getnum').html('+'+(data.allnum.getnum?data.allnum.getnum:0));
			$('#connum').html((data.allnum.connum?data.allnum.connum:0));
		}
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
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('ingot').Lang('log'); ?>');
	}
	getlogingotList( pageIndex );

	$('.h_lib_nav').on('click', 'a.ingot_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_ingot_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_ingot_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogingotList(1);
	});
});
</script>
<script type="text/template" id="ingottypetpl">
<li><a href="javascript:;" data-id="${id}" class="ingot_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', change_time)}</td>
<td>{{if value>0}}+{{/if}}${value}</td>
<td>{{if change_charge_value != ''}}${change_charge_value}{{else}}0{{/if}}</td>
<td>${after_change_ingot}</td>
<td>{{if new_charge_ingot>0}}${new_charge_ingot}{{else}}0{{/if}}</td>
<td>${type_id_to_name(type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_ingot_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('con_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
		<ul id="ingotcons">
		</ul>
	</div>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('get_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
		<ul id="ingotget">
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
			<li>
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="ingot">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('get_type').Lang('ingot'); ?>：<span class="orangetitle" id="getnum">0</span></p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('con_type').Lang('ingot'); ?>：<span class="greentitle" id="connum">0</span></p>
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
				    <th style="width:120px;"><?php echo Lang('op_time'); ?></th>
				    <th style="width:80px;"><?php echo Lang('change_ingot'); ?></th>
				    <th style="width:80px;"><?php echo Lang('change_charge_value'); ?></th>
				    <th style="width:80px;"><?php echo Lang('after_change_ingot'); ?></th>
				    <th style="width:80px;"><?php echo Lang('new_charge_ingot'); ?></th>
				    <th style="20%"><?php echo Lang('op_type'); ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="ingotpager"></div>
</div>
