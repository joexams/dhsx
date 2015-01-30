<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist, chklist;
function getlogsoulList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_soul_submit').serialize()+'&typeflag='+typeflag,
			success: showlogsoulList
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
function soul_id_to_name(id){
	if (typelist != undefined){
		if (typelist.soul != undefined && typelist.soul.length > 0){
			for (var key in typelist.soul){
				if (typelist.soul[key].id == id){
					return typelist.soul[key].name + '('+typelist.soul[key].qualityname+')';
				}
			}
		}
	}
	return '';
}
function attr_id_to_name(id, value){
	var attrname = '';
	if (typelist != undefined){
		if (typelist.attribute != undefined && typelist.attribute.length > 0){
			for (var key in typelist.attribute){
				if (typelist.attribute[key].id == id){
					attrname =  typelist.attribute[key].name + '+'+value;
					if (typelist.attribute[key].unit < 1){
						attrname +='%';
					}
					return attrname;
				}
			}
		}
	}
	return '-';
}
function showlogsoulList( data ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#soultypetpl').tmpl( data.type.cons ).appendTo('#soulcons');
			$('#soultypetpl').tmpl( data.type.get ).appendTo('#soulget');
		}
		if (typeof data.chklist != 'undefined') {
			chklist = data.chklist;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		$( "#soulpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogsoulList });
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

function soul_id_to_check(id) {
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
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'灵件记录');
	}
	
	$('.h_lib_nav').on('click', 'a.soul_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_soul_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_soul_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getlogsoulList( 1 );
	});
	$('#get_search_soul_submit').submit();
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
								'<form name="chk-soul-postsubmit" id="chk-postsubmit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=retrieve" method="post">',
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

				$('#chk-postsubmit').live('submit', function(e) {
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
				});
			}

			if ($('#soul_'+id).html() == null) {
				var str = '';
				if (typeof list != 'undefined' && list.length > 0) {
					for(var key in list) {
						if (list[key].id == id) {
							var curitem = list[key];
							if (typeof curitem.soul_attribute_id_location_4 == 'undefined') {
								curitem.soul_attribute_id_location_4 = 0;
								curitem.soul_attribute_value_location_4 = 0;
							}
							str = '{"id":'+curitem.id+',"name":"'+name+'","soul_id":'+curitem.soul_id+',"attributeid1":'+curitem.soul_attribute_id_location_1+',"attributevalue1":'+curitem.soul_attribute_value_location_1+',"attributeid2":'+curitem.soul_attribute_id_location_2+',"attributevalue2":'+curitem.soul_attribute_value_location_2+',"attributeid3":'+curitem.soul_attribute_id_location_3+',"attributevalue3":'+curitem.soul_attribute_value_location_3+',"attributeid4":'+curitem.soul_attribute_id_location_4+',"attributevalue4":'+curitem.soul_attribute_value_location_4+',"key":'+curitem.key+',"number":1}';
							break;
						}
					}
				}
				$('#chk-hiddenarea').append('<input type="hidden" id="soul_'+id+'" name="soul[]" value=\''+str+'\'>');
				$('#chk-record').html(num+1);
			}
		}else {
			if (num > 0) {
				$('#chk-record').html(num-1);
				$('#soul_'+id).remove();
			}
		}
	});

	$('#chk-soul-postsubmit').live('submit', function(e) {
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
<script type="text/template" id="soultypetpl">
<li><a href="javascript:;" data-id="${id}" class="soul_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', change_time)}</td>
<td>{{if type == 1 && soul_id_to_check(id) === false}}<input type="checkbox" name="soul" value="${id}" data-name="${soul_id_to_name(soul_id)}">{{/if}} ${soul_id_to_name(soul_id)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_1, soul_attribute_value_location_1)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_2, soul_attribute_value_location_2)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_3, soul_attribute_value_location_3)}</td>
{{if typeof soul_attribute_id_location_4 != 'undefined'}}
<td>${attr_id_to_name(soul_attribute_id_location_4, soul_attribute_value_location_4)}</td>
{{else}}
<td>-</td>
{{/if}}
<td>${key}</td>
<td>${type_id_to_name(type)}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_soul_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="soulcons">
	</ul>
	<ul id="soulget">
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
					<input type="hidden" name="key" value="soul">
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
				    <th><?php echo Lang('soul') ?></th>
				    <th><?php echo Lang('attribute') ?>1</th>
				    <th><?php echo Lang('attribute') ?>2</th>
				    <th><?php echo Lang('attribute') ?>3</th>
				    <th><?php echo Lang('attribute') ?>4</th>
				    <th style="width:50px;"><?php echo Lang('open_priv') ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="soulpager"></div>
</div>
