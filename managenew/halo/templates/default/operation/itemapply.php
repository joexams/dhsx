<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0;
function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_itemapply_list&top="+index+"&recordnum="+recordNum;	
	pageIndex = index;
	$( "#applylist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: showList
		});
	});
}

function showList( data) {
	if (data.status == -1){
		$('#applylist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });

		$( "#applylist" ).empty();
		if (data.count > 0){
			$( "#applylisttpl" ).tmpl( data.list ).prependTo( "#applylist" );
			$( "#applylist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

function sid_to_name(sid) {
	if (typeof global_serverlist != 'undefined') {
		for(var key in global_serverlist) {
			if (sid == global_serverlist[key].sid) {
				return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
			}
		}
	}
	return '';
}

$(function() {
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
		}
	}, 250);

	$('.cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('.sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}
	});

	function get_source_tmpl(key, sid) {
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=template&v=ajax_info',
				data: 'sid='+sid+'&key='+key,
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						$('#sourcetmpl').html(data.info.content);
						$('#tid').val(data.info.tid);
					}else{
						var time = 2;
						$('#op_tips').attr('class', 'alert_error');
						$('#op_tips').children('p').html(data.msg);
						$('#op_tips').fadeIn();
						setTimeout( function(){
							$('#op_tips').fadeOut();
							$('#btnsubmit').removeAttr('disabled');
						}, ( time * 1000 ) );
					}
				}
			});
	}

	$('#type').on('change', function() {
		var key = $(this).val(), sid = $('#sid').val();
		if (key != '' && sid > 0) {
			get_source_tmpl(key, sid);
		}
	});

	$('#sid').on('change', function() {
		var sid = $(this).val(), key = $('#type').val();
		if (sid > 0 && key != '') {
			get_source_tmpl(key, sid);
		}else if (sid > 0 && key == ''){
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('select_add_item_type_tips'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
			}, ( 2 * 1000 ) );
		}
	});

	//展开
	$('#extentfold').on('click', function(){
		var hidden = '<?php echo Lang("hidden"); ?>', show = '<?php echo Lang("show"); ?>';
		var obj = $(this);
		$('#submit_area').toggle("normal", function(){
			if ($(this).is(':hidden')){
				obj.html(show);
			}else {
				obj.html(hidden);
			}
		});
	});

	getsearchList(1);

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var obj = $(this), sid = $('#sid').val(), key = $('#type').val();
		if (sid > 0 && key != '') {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=add_itemapply',
				type: 'post',
				data: obj.serialize(),
				dataType: 'json',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$( "#applylisttpl" ).tmpl( data.info ).prependTo( "#applylist" ).fadeIn(2000, function(){
						var obj = $(this);
						obj.css('background', '#E6791C');
						setTimeout( function(){	obj.css('background', ''); }, 2000 );
					});

					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('select_add_item_type_tips'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
	});
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});

	$('#all_check').on('change', function() {
		if ($(this).is(':checked')) {
			$('#applylist input:checkbox').attr('checked', 'checked');
		}else {
			$('#applylist input:checkbox').removeAttr('checked');
		}
	});
	<?php if (!$islimit) { ?>
	//审批
	$('#check_post_submit').on('submit', function(e) {
		e.preventDefault();
		var obj = $(this);
		$('#check_btnsubmit').attr('disabled', 'disabled');
		if ($(':radio[name="checktype"]').is(':checked') && $('#applylist :checked').size() > 0) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=check_itemapply',
				data: obj.serialize(),
				dataType: 'json',
				type: 'post',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							getsearchList(pageIndex);
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#check_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#check_btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#check_btnsubmit').removeAttr('disabled');
		}
	});
	<?php } ?>
});
</script>

<script type="text/template" id="applylisttpl">
<tr>
	<?php if (!$islimit) { ?>
	<td>{{if status == 1}}<input type="checkbox" name="aid[]" value="${aid}">{{else}}-{{/if}}</td>
	<?php } ?>
	<td>
	{{if status == 1}}
	<span class="redtitle"><?php echo Lang('not_handle'); ?></span>
	{{else status == 2}}
	<span class="greentitle"><?php echo Lang('handle'); ?></span>
	{{else status == 3}}
	<span class="graytitle"><?php echo Lang('closed'); ?></span>
	{{else status == 4}}
	<?php echo Lang('ignore'); ?>
	{{/if}}
	</td>
	<td>${sid_to_name(sid)}</td>
	<td>${player_name}</td>
	<td>${content!=''?content: '&nbsp;'}</td>
	<td>${case_content}</td>
	<td>${reply_content!=''?reply_content: '&nbsp;'}</td>
	<?php if (!$islimit) { ?>
	<td>
	{{if status == 1}}
	<a href="javascript:;"><?php echo Lang('approval'); ?></a>
	<a href="javascript:;"><?php echo Lang('reply'); ?></a>
	<a href="javascript:;"><?php echo Lang('revoke'); ?></a>
	{{else}}
	-
	{{/if}}
	</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('itemapply') ?></span></a></li>
	</ul>
	<br class="clear">
		<div class="onecolumn">
			<div class="header">
				<h2><?php echo Lang('add_itemapply'); ?></h2>
				<ul class="second_level_tab">
					<li><a href="javascript:;" id="extentfold"><?php echo Lang('show'); ?></a></li>
				</ul>
			</div>

		<div class="content" id="submit_area" style="display: none;">
			<!-- Begin form elements -->
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=add_itemapply" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width:100px;"><?php echo Lang('type'); ?></th>
						<td>
							<select name="type" id="type">
							 <option value=""><?php echo Lang('type'); ?></option>
							 <option value="give_item"><?php echo Lang('item_good'); ?></option>
							 <option value="give_soul"><?php echo Lang('soul'); ?></option>
							 <option value="system_send_ingot"><?php echo Lang('ingot'); ?></option>
							 <option value="increase_player_coins"><?php echo Lang('coins'); ?></option>
							 <option value="give_fate"><?php echo Lang('fate'); ?></option>
							 <option value="increase_player_skill"><?php echo Lang('skill'); ?></option>
							 <option value="increase_player_power"><?php echo Lang('power'); ?></option>
							 <option value="increase_player_state_point"><?php echo Lang('player_state_point'); ?></option>
							 <option value="set_player_vip_level">VIP</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('server'); ?></th>
						<td>
							<select name="cid" class="cid">
								<option value="0"><?php echo Lang('operation_platform'); ?></option>
							</select>
							<select name="sid" class="sid" id="sid">
								<option value="0"><?php echo Lang('all_server'); ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('player_name'); ?></th>
						<td>
							<input type="text" name="playername" id="playername">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tbody id="sourcetmpl">
						
					</tbody>
					<tr>
						<th><?php echo Lang('apply_case_content'); ?></th>
						<td><textarea name="case_content" id="case_content" style="width:400px;height:100px;"></textarea></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
		    </form>
		    <div id="op_tips" style="display: none;width:100%"><p></p></div>
			<!-- End form elements -->
		</div>
	</div>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_itemapply_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<select name="cid" class="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" class="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<select name="type">
						<option value=""><?php echo Lang('type') ?></option>
						<option value="give_item"><?php echo Lang('item_good'); ?></option>
						<option value="give_soul"><?php echo Lang('soul'); ?></option>
						<option value="system_send_ingot"><?php echo Lang('ingot'); ?></option>
						<option value="increase_player_coins"><?php echo Lang('coins'); ?></option>
						<option value="give_fate"><?php echo Lang('fate'); ?></option>
						<option value="increase_player_skill"><?php echo Lang('skill'); ?></option>
						<option value="increase_player_power"><?php echo Lang('power'); ?></option>
						<option value="increase_player_state_point"><?php echo Lang('player_state_point'); ?></option>
						<option value="set_player_vip_level">VIP</option>
					</select>
					<select name="status" id="status">
						<option value="0"><?php echo Lang('status') ?></option>
						<option value="1"><?php echo Lang('not_handle') ?></option>
						<option value="2"><?php echo Lang('handle') ?></option>
						<option value="3"><?php echo Lang('closed') ?></option>
						<option value="4"><?php echo Lang('ignore') ?></option>
					</select>
					<?php echo Lang('content_contain'); ?>： <input type="text" name="keyword" id="keyword" value="" />
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form id="check_post_submit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=check_itemapply" method="post" name="form">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:20px;"><?php echo Lang('select'); ?></th>
					<th style="width:60px;"><?php echo Lang('status'); ?></th>
					<th style="width:10%"><?php echo Lang('server'); ?></th>
					<th style="width:5%"><?php echo Lang('player_name'); ?></th>
					<th style="width:15%"><?php echo Lang('apply_content'); ?></th>
					<th style="width:15%"><?php echo Lang('apply_case_content'); ?></th>
					<th style="width:10%"><?php echo Lang('reply_content'); ?></th>
					<?php if (!$islimit) { ?>
					<th style="width:10%"><?php echo Lang('operation'); ?></th>
					 <?php } ?>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="applylist">

			</tbody>
			<?php if (!$islimit) { ?>
			<tbody>
				<tr>
					<td><input type="checkbox" id="all_check" value="1"></td>
					<td colspan="7">
						<input type="radio" name="checktype" value="2"><span class="greentitle"><?php echo Lang('approval'); ?></span>
						<input type="radio" name="checktype" value="3"><?php echo Lang('closed'); ?>
						<input type="radio" name="checktype" value="4"><?php echo Lang('ignore'); ?>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" id="check_btnsubmit" value="<?php echo Lang('submit'); ?>">
					</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
			<?php } ?>
		</table>
		</form>
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- End form elements -->
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
