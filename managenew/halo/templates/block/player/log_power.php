<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist;
function getsearchlogpowerList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_power_submit').serialize()+'&typeflag='+typeflag,
			success: showlogpowerList
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
function showlogpowerList( data ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#powertypetpl').tmpl( data.type.cons ).appendTo('#powercons');
			$('#powertypetpl').tmpl( data.type.get ).appendTo('#powerget');
		}

		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;

		$( "#powerpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogpowerList });
		$('#list_'+pid).empty();

		if (pageIndex == 1){
			$('#getnum').html('+'+(data.allnum.getnum?data.allnum.getnum:0));
			$('#connum').html((data.allnum.connum?data.allnum.connum:0));
		}
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
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('power_log'); ?>");
	}

	$('.h_lib_nav').on('click', 'a.power_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_power_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_power_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogpowerList(1);
	});

	$('#get_search_power_submit').submit();
});
</script>
<script type="text/template" id="powertypetpl">
<li><a href="javascript:;" data-id="${id}" class="power_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', op_time)}</td>
<td>{{if value>0}}<span class="greentitle">+${value}</span>{{else}}${value}{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_power_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="powercons">
	</ul>
	<ul id="powerget">
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
					<input type="hidden" name="key" value="power">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('get_type').Lang('power'); ?>：<span class="orangetitle" id="getnum">0</span></p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('con_type').Lang('power'); ?>：<span class="greentitle" id="connum">0</span></p>
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
				    <th style="width:80px;"><?php echo Lang('power'); ?></th>
				    <th style="20%"><?php echo Lang('op_type') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="powerpager"></div>
</div>
