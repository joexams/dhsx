<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist, chklist;
function getsearchlogfateList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_fate_submit').serialize()+'&typeflag='+typeflag,
			success: showlogfateList
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
function fate_id_to_name(id, fate_experience){
	var str = '', level = '';
	fate_experience = parseInt(fate_experience);
	if (typelist != undefined){
		if (typelist.fate != undefined && typelist.fate.length > 0){
			for (var key in typelist.fate){
				if (typelist.fate[key].id == id){
					str = typelist.fate[key].name;
					if (fate_experience > 0 && fate_experience >= typelist.fate[key].request_experience) {
						level = ' (Lv.'+typelist.fate[key].level+')';
					}
				}
			}
		}
	}
	if (str != '' && level == '') {
		level = ' (Lv.1)';
	}
	return str+level;
}
function showlogfateList( data ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#fatetypetpl').tmpl( data.type.cons ).appendTo('#fatecons');
			$('#fatetypetpl').tmpl( data.type.get ).appendTo('#fateget');
		}
		if (typeof data.chklist != 'undefined') {
			chklist = data.chklist;
		}

		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;

		$( "#fatepager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogfateList });
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
function fate_id_to_check(id) {
	if (typeof chklist != 'undefined') {
		for(var key in chklist) {
			if (chklist[key].id == id) {
				return true;
			}
		}
	}
	return false;
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('fate_log'); ?>");
	}

	$('.h_lib_nav').on('click', 'a.fate_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_fate_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value=\''+typeid+'\'>');
			}
		}
		return false;
	});

	$('#get_search_fate_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogfateList(1);
	});
	$('#get_search_fate_submit').submit();
	//去除其他类别的提交影响
	if ($('#chk-submitarea').html() != null) {
		$('#chk-submitarea').remove();
	}
	/**
	 * 盗号找回
	 * @return {[type]} [description]
	 */
	$("#list_"+pid).on('change', 'input:checkbox', function() {
		var num = $('#chk-record').html();
		var id = $(this).val(), name = $(this).attr('data-name');
		num = !isNaN(parseInt(num)) ? parseInt(num) : 0;
		if ($(this).is(':checked')) {
			if ($('#chk-submitarea').html() == null) {
				var strHtml = [
					'<div style="width: 100%;" id="chk-submitarea">',
					'<div style="bottom: 150px; right: 0px; z-index: 1001; width: 100%; position: fixed; ">',
						'<div class="gb_poptips" style="background: #EDF8FA;cursor:default">',
							'<div class="gb_poptips_btn" id="chk-input-box" style="width:300px;background: #EDF8FA;border: 1px solid #09C;border-right:0;cursor:default;">',
								'<form name="chk-postsubmit" id="chk-fate-postsubmit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=retrieve" method="post">',
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
									'<input type="submit" name="" value="<?php echo Lang('post'); ?>">',
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

			if ($('#fate_'+id).html() == null) {
				var str = '';
				if (typeof list != 'undefined' && list.length > 0) {
					for(var key in list) {
						if (list[key].id == id) {
							var curitem = list[key];
							var level = 1;
							if (typelist != undefined){
								if (typelist.fate != undefined && typelist.fate.length > 0){
									for (var tkey in typelist.fate){
										if (typelist.fate[tkey].id == curitem.fate_id){
											curitem.fate_experience = isNaN(parseInt(curitem.fate_experience)) ? 0 : parseInt(curitem.fate_experience);
											if (curitem.fate_experience > 0 && curitem.fate_experience >= typelist.fate[tkey].request_experience) {
												level = typelist.fate[tkey].level;
											}
										}
									}
								}
							}

							str = '{"id":'+curitem.id+',"name":"'+name+'","fate_id":'+curitem.fate_id+',"actived_fate_id2":'+curitem.actived_fate_id2+',"actived_fate_id1":'+curitem.actived_fate_id1+',"level": '+level+',"number":1}';
							break;
						}
					}
				}
				$('#chk-hiddenarea').append('<input type="hidden" id="fate_'+id+'" name="fate[]" value=\''+str+'\'>');
				$('#chk-record').html(num+1);
			}
		}else {
			if (num > 0) {
				$('#chk-record').html(num-1);
				$('#fate_'+id).remove();
			}
		}
	});

	$('#chk-fate-postsubmit').live('submit', function(e) {
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
					$('input:checked', $('#list_<?php echo $data['id'] ?>')).each(function(){
						$(this).remove();
					});
				}
				$('#chk-btnsubmit').removeAttr('disabled');
				alert(data.msg);
			},
			error: function() {
				$('#chk-btnsubmit').removeAttr('disabled');
				alert('提交失败');
			}
		});
		return false;
	});

	$('#chk-close').live('click', function() {
		$("#list_"+pid).find(':checked').removeAttr('checked');
		$('#chk-submitarea').remove();
	});
	$('#chk-mini-box').live('click', function() {
		$(this).fadeOut();
		$('#chk-input-box').fadeIn();
	});
});
</script>
<script type="text/template" id="fatetypetpl">
<li><a href="javascript:;" data-id="${id}" class="fate_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', op_time)}</td>
<td>{{if op_type > 3 && op_type < 8  && fate_id_to_check(id) === false}}<input type="checkbox" name="fate" value="${id}" data-name="${fate_id_to_name(fate_id, fate_experience)}"> <strong>${fate_id_to_name(fate_id, fate_experience)}</strong>{{else}}${fate_id_to_name(fate_id, fate_experience)}{{/if}} </td>
<td><span class="greentitle">${fate_experience}</span></td>
<td>{{if op_type == 4}}被 <strong>${fate_id_to_name(merge_fate_id, merge_fate_experience)}</strong> {{/if}}${type_id_to_name(op_type)}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_fate_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="fatecons">
	</ul>
	<ul id="fateget">
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
					<?php echo Lang('fate'); ?>：
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
			<li class="nobg">
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="fate">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
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
				    <th style="width:120px;"><?php echo Lang('fate'); ?></th>
				    <th style="width:100px;"><?php echo Lang('fate').Lang('experience'); ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="fatepager"></div>
</div>
