<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeflag = 0, list, typelist, chklist;
function getlogitemList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record", tbl = $('#tbl').val()
	params = {sid: '<?php echo $data['sid']; ?>', id: '<?php echo $data['id']; ?>', key: 'item', typeflag: typeflag, top: index, recordnum: recordNum, tbl: tbl};
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: params,
			success: showlogitemList
		});
	});
	return false;
}
function getsearchlogitemList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_item_submit').serialize(),
			success: function(data){
				showlogitemList(data, 1);
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
function item_id_to_name(id){
	if (typelist != undefined){
		if (typelist.item != undefined && typelist.item.length > 0){
			for (var key in typelist.item){
				if (typelist.item[key].id == id){
					return typelist.item[key].name;
				}
			}
		}
	}
	return '';
}
function item_id_to_check(id) {
	if (typeof chklist != 'undefined') {

		for(var key in chklist) {
			if (chklist[key].id == id) {
				return true;
			}
		}
	}
	return false;
}
function showlogitemList( data, type ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#itemtypetpl').tmpl( data.type.cons ).appendTo('#itemget');
			$('#itemtypetpl').tmpl( data.type.get ).appendTo('#itemcons');
		}
		if (typeof data.chklist != 'undefined') {
			chklist = data.chklist;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		if (type != undefined && type == 1){
			$( "#itempager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogitemList });
		}else {
			$( "#itempager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogitemList });
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
var chkdialog;
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('item').Lang('log'); ?>');
	}
	getlogitemList( pageIndex );

	$('.h_lib_nav').on('click', 'a.item_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_item_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});
	//去除其他类别的提交影响
	if ($('#chk-submitarea').html() != null) {
		$('#chk-submitarea').remove();
	}
	/**
	 * 盗号找回
	 * @return {[type]} [description]
	 */
	$('#list').on('change', 'input:checkbox', function() {
		var num = $('#chk-record').html();
		var id = $(this).val(), name = $(this).attr('data-name'), level = $(this).attr('data-level');
		num = !isNaN(parseInt(num)) ? parseInt(num) : 0;
		if ($(this).is(':checked')) {
			if ($('#chk-submitarea').html() == null) {
				var strHtml = [
					'<div style="width: 100%;" id="chk-submitarea">',
					'<div style="bottom: 150px; right: 0px; z-index: 1001; width: 100%; position: fixed; ">',
						'<div class="gb_poptips" style="background: #EDF8FA;cursor:default">',
							'<div class="gb_poptips_btn" id="chk-input-box" style="width:300px;background: #EDF8FA;border: 1px solid #09C;border-right:0;cursor:default;">',
								'<form name="chk-postsubmit" id="chk-item-postsubmit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=retrieve" method="post">',
								'<div style="background: #38A3DB;height:30px;line-height: 30px;color:white;">',
									'<div style="float:left;padding-left:20px;font-weight: bold;text-shadow: -1px -1px 0 rgba(33, 79, 183, .7);">被盗找回</div>',
									'<a href="javascript:;" id="chk-close" style="float:right;width:20px;padding-right:10px;color:white;font-weight: bold;font-size:16px;height: 20px;"> × </a>',
								'</div>',
								'<div style="padding:10px;text-align: center;">',
									'<p>选中 <span class="orangetitle" style="font-weight: bold;font-size:16px;" id="chk-record">0</span> 条记录</p>',
									'<hr>',
									'<p style="text-align:left;padding-left:10px;padding-bottom:5px;font-weight:bold;">被盗找回说明</p>',
									'<p><textarea name="content" style="width:90%;height:80px;"></textarea></p>',
								'</div>',
								'<div style="padding-left:20px;" id="chk-hiddenarea">',
									'<input type="hidden" name="doSubmit" value="1" />',
									'<input type="hidden" name="sid" value="<?php echo $data['sid']; ?>" />',
									'<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />',
									'<input type="hidden" name="playername" value="<?php echo urldecode($_GET['title']); ?>">',
									'<input type="hidden" name="nickname" value="<?php echo urldecode($_GET['nickname']); ?>">',
									'<input type="submit" id="chk-btnsubmit" value="<?php echo Lang('post'); ?>">',
								'</div>',
								'</form>',
							'</div>',
							'<div class="gb_poptips_btn" id="chk-mini-box" style="width:30px;height:50px;background: #38A3DB;border: 1px solid #09C;border-right:0;text-align:center;display:none">',
								'<a href="javascript:;" style="width:30px;font-size:20px;color:white;line-height:50px;"> < </a>',
							'</div>',
						'</div>',
					'</div>',
				'</div>'
				].join('');
				$('#fixLayout').after(strHtml);
			}

			if ($('#item_'+id).html() == null) {
				var str = '';
				if (typeof list != 'undefined' && list.length > 0) {
					for(var key in list) {
						if (list[key].id == id) {
							var curitem = list[key];
							str = '{"id":'+curitem.id+',"name":"'+name+'","item_id":'+curitem.item_id+',"level":'+curitem.item_lv+',"number":1}';
							break;
						}
					}
				}
				$('#chk-hiddenarea').append('<input type="hidden" id="item_'+id+'" name="item[]" value=\''+str+'\'>');
				$('#chk-record').html(num+1);
			}
		}else {
			if (num > 0) {
				num = num-1;
				$('#chk-record').html(num);
				$('#item_'+id).remove();
			}
			if (num <= 0) {
				$('#chk-submitarea').remove();
			}
		}
	});

	$('#chk-item-postsubmit').live('submit', function(e) {
		e.preventDefault();
		$('#chk-btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
			url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=retrieve',
			data: objform.serialize(),
			type: 'post',
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					$('#chk-submitarea').remove();
				}
				$('#chk-btnsubmit').removeAttr('disabled')
			},
			error: function() {
				$('#chk-btnsubmit').removeAttr('disabled')
			}
		});
		return false;
	});

	$('#get_search_item_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogitemList(1);
	});

	$('#chk-close').live('click', function() {
		$("#list").find(':checked').removeAttr('checked');
		$('#chk-submitarea').remove();
	});
	$('#chk-mini-box').live('click', function() {
		$(this).fadeOut();
		$('#chk-input-box').fadeIn();
	});
});
</script>
<script type="text/template" id="itemtypetpl">
<li><a href="javascript:;" data-id="${id}" class="item_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', change_time)}</td>
<td>{{if (type == 4 || type == 9) && item_id_to_check(id) == false}}<input type="checkbox" name="item" value="${id}" data-name="${item_id_to_name(item_id)}" data-level="${item_lv}">{{/if}}  <strong>${item_id_to_name(item_id)}</strong>  (Lv.${item_lv})</td>
<td>{{if value>0}}+{{/if}}${value}</td>
<td>${type_id_to_name(type)}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_item_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('con_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
		<ul id="itemcons">
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
		<ul id="itemget">
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
					<?php echo Lang('item_name'); ?>：
				<input name="name" type="text" value="">
			</p>
			</li>
			<li>
				<p>
					<?php echo Lang('between_date'); ?>：
				<input name="starttime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value="">
					<input name="endtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value=""></p>
			</li>
			<li>
				<p>
					<?php echo Lang('db_table'); ?>：
					<select name="tbl" id="tbl">
						<option value="1"><?php echo Lang('new_db_table'); ?></option>
						<option value="0"><?php echo Lang('old_db_table'); ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid']; ?>">
					<input type="hidden" name="id" value="<?php echo $data['id']; ?>">
					<input type="hidden" name="key" value="item">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
				</p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('get_type').Lang('item'); ?>：<span class="orangetitle" id="getnum">0</span></p>
			</li>
			<li class="nobg">
				<p><?php echo Lang('con_type').Lang('item'); ?>：<span class="greentitle" id="connum">0</span></p>
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
				    <th style="width:25%"><?php echo Lang('item_name'); ?></th>
				    <th style="width:80px;"><?php echo Lang('plus_sub'); ?></th>
				    <th style="width:20%"><?php echo Lang('op_type'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="itempager"></div>

</div>
