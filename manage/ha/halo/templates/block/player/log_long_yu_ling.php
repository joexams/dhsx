<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeflag = 0, list, typelist;
function getloglong_yu_lingList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record", tbl = $('#tbl').val()
	params = {sid: '<?php echo $data['sid']; ?>', id: '<?php echo $data['id']; ?>', key: 'long_yu_ling', typeflag: typeflag, top: index, recordnum: recordNum, tbl: tbl};
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: params,
			success: showloglong_yu_lingList
		});
	});
	return false;
}
function getsearchloglong_yu_lingList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_long_yu_ling_submit').serialize(),
			success: function(data){
				showloglong_yu_lingList(data, 1);
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
					return 0;
				}
			}
		}
	}
	return 1;
}

function showloglong_yu_lingList( data, type ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#long_yu_lingtypetpl').tmpl( data.type.get ).appendTo('#long_yu_lingget');
			$('#long_yu_lingtypetpl').tmpl( data.type.cons ).appendTo('#long_yu_lingcons');
		}
		if (typeof data.chklist != 'undefined') {
			chklist = data.chklist;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		if (type != undefined && type == 1){
			$( "#long_yu_lingpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchloglong_yu_lingList });
		}else {
			$( "#long_yu_lingpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getloglong_yu_lingList });
		}
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



$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('long_yu_ling').Lang('log'); ?>');
	}
	getloglong_yu_lingList( pageIndex );
	
	$('#get_search_long_yu_ling_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchloglong_yu_lingList(1);
	});
});
</script>
<script type="text/template" id="long_yu_lingtypetpl">
<li><a href="javascript:;" data-id="${id}" class="long_yu_ling_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i:s', op_time)}</td>
<td>{{if type_id_to_status(op_type) == 0}}-${value}{{else}}<span class="greentitle">+${value}</span>{{/if}}</td>
<td>${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_long_yu_ling_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('con_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
		<ul id="long_yu_lingcons">
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
		<ul id="long_yu_lingget">
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
					<input type="hidden" name="sid" value="<?php echo $data['sid']; ?>">
					<input type="hidden" name="id" value="<?php echo $data['id']; ?>">
					<input type="hidden" name="key" value="long_yu_ling">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
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
				    <th style="width:120px;"><?php echo Lang('op_time'); ?></th>
				    <th style="width:80px;"><?php echo Lang('plus_sub'); ?></th>
				    <th style="width:20%"><?php echo Lang('op_type'); ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="long_yu_lingpager"></div>

</div>
