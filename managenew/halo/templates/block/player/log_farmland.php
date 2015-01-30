<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, list, typelist;
function getsearchlogfarmlandList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_farmland_submit').serialize(),
			success: showlogfarmlandList
		});
	});
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

function herb_id_to_name(id){
	if (typelist != undefined){
		if (typelist.herbs != undefined && typelist.herbs.length > 0){
			for (var key in typelist.herbs){
				if (typelist.herbs[key].id == id){
					return typelist.herbs[key].name;
				}
			}
		}
	}
	return '';
}

function showlogfarmlandList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typelist = data.type;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		$( "#farmlandpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogfarmlandList });

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
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('farmland').Lang('log'); ?>');
	}

	$('.h_lib_nav').on('click', 'a.farmland_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_farmland_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_farmland_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogfarmlandList(1);
	});

	$('#get_search_farmland_submit').submit();
});
</script>
<script type="text/template" id="farmlandtypetpl">
<li><a href="javascript:;" data-id="${id}" class="farmland_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', harvest_time)}</td>
<td>${date('Y-m-d H:i', ripe_time)}</td>
<td>${herb_id_to_name(herbs_id)}</td>
<td>${exp}</td>
<td>{{if add_exp>0}}<span class="greentitle">+${add_exp}</span>{{else}}${add_exp}{{/if}}</td>
<td>${date('Y-m-d H:i', timestamp)}</td>
<td>${role_id_to_name(player_role_id)}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_farmland_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<input type="hidden" name="key" value="farmland">
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
				    <th style="width:120px;"><?php echo Lang('mature_time'); ?></th>
				    <th style="width:120px;"><?php echo Lang('required_time'); ?></th>
				    <th style="width:20%"><?php echo Lang('herb'); ?></th>
				    <th style="width:100px;"><?php echo Lang('should_get_exp'); ?></th>
				    <th style="width:100px;"><?php echo Lang('actual_plus_exp'); ?></th>
				    <th style="width:120px;"><?php echo Lang('op_time'); ?></th>
				    <th><?php echo Lang('role'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
		<div class="pagination" id="farmlandpager"></div>
	</div>

</div>
