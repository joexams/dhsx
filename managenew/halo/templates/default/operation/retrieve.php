<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_retrieve_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#retlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: showList
		});
	});
}

function showList( data ) {
	if (data.status == 1){
		$('#retlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#retlist" ).empty();
		if (data.count > 0){
			$( "#retlisttpl" ).tmpl( data.list ).prependTo( "#retlist" );
			$( "#retlist" ).stop(true,true).hide().slideDown(400);
		}
	}
}
function sid_to_name(cid, sid) {
	var str = '';
	if (typeof global_companylist != 'undefined') {
		for(var key in global_companylist) {
			if (global_companylist[key].cid == cid) {
				str = global_companylist[key].name;
				break;
			}
		}
	}
	if (typeof global_serverlist != 'undefined') {
		for(var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				str += '-'+global_serverlist[key].name;
				break;
			}
		}
	}
	return str;
}

var dialog = typeof dialog != 'undefined' ? null : '';
$(function() {
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('option[value!="0"]', $('#cid')).remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});
	 <?php if (!$islimit) { ?>
	/**
	 * 撤销
	 * @return {[type]} [description]
	 */
	$('#retlist').on('click', 'a.revoke', function() {
		var obj = $(this).parent('td'); id = obj.attr('data-id');
		if (id > 0 && confirm('确定撤销盗号找回申请吗？')) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=delete_retrieve',
				data: {id: id},
				dataType: 'json',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							obj.parent().remove();
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
	});
	/**
	 * 审批后撤销
	 * @return {[type]} [description]
	 */
	$('#retlist').on('click', 'a.again', function() {
		var obj = $(this).parent('td'); id = obj.attr('data-id');
		if (id > 0 && confirm('此申请已经审批通过，你确定撤销重新审批吗？')) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=again_retrieve',
				data: {id: id},
				dataType: 'json',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							getList(pageIndex);
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
	});
	 <?php } ?>
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		recordNum = 0;
		getList(1);
	});
	$('#get_search_submit').submit();

	/**
	 * 选择
	 * @return {[type]} [description]
	 */
	$('#seletedall').on('change', function() {
		if ($(this).is(':checked')) {
			$('#retlist :checkbox').attr('checked', 'checked');
		}else {
			$('#retlist :checkbox').removeAttr('checked');
		}
	});
	/**
	 * 提交审批
	 */
	 $('#post_submit').on('submit', function(e) {
	 	e.preventDefault();
	 	var obj = $(this);
	 	$('#post_btnsubmit').attr('disabled', 'disabled');
	 	if ($(':radio[name="checktype"]').is(':checked') && $('#retlist :checked').size() > 0) {
	 		$.ajax({
	 			url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=check_retrieve',
	 			data: obj.serialize(),
	 			dataType: 'json',
	 			type: 'post',
	 			success: function(data) {
	 				var alertclassname = '', time = 2;
	 				switch (data.status){
	 					case 0: 
	 						alertclassname = 'alert_success'; 
	 						getList(pageIndex);
	 						break;
	 					case 1: alertclassname = 'alert_error'; break;
	 				}
	 				$('#list_op_tips').attr('class', alertclassname);
	 				$('#list_op_tips').children('p').html(data.msg);
	 				$('#list_op_tips').fadeIn();
	 				setTimeout( function(){
	 					$('#list_op_tips').fadeOut();
	 					$('#post_btnsubmit').removeAttr('disabled');
	 				}, ( time * 1000 ) );
	 			},
	 			error: function() {
	 				$('#post_btnsubmit').removeAttr('disabled');
	 			}
	 		});
	 	}else {
	 		$('#post_btnsubmit').removeAttr('disabled');
	 	}
	 });

	$('#retlist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = $(this).attr('title');
		if (playerid > 0 && sid > 0){
			dialog = $.dialog({id: 'player_info_'+playerid, width: 880, title: title});
			var sname = '';
			if (typeof global_serverlist != 'undefined') {
				for(var key in global_serverlist) {
					if (global_serverlist[key].sid == sid) {
						sname = global_serverlist[key].name;
						break;
					}
				}
			}
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=detail_info',
				data: {id: playerid, sid: sid, sname: sname},
				success: function(data){
					dialog.content(data);
				}
			});
		}
	});
});
</script>

<script type="text/template" id="retlisttpl">
<tr>
	{{if status > 1}}
	<td>-</td>
	{{else}}
	<td><input type="checkbox" name="retid[]" value="${id}"></td>
	{{/if}}
	<td data-id="${id}">
	{{if status == 2}}
	<a href="javascript:;" class="again"><span class="greentitle">√已获批</span></a>
	{{else status == 3}}
	<span class="graytitle">关闭/拒绝</span>
	{{else}}<span class="redtitle">未处理</span>{{/if}}</td>
	<td><a href="javascript:;" data-sid="${sid}" data-pid="${player_id}" class="player_info" title="${playername}">${playername}{{if nickname!=''}}(${nickname}){{/if}}</a></td>
	<td>{{if key == 'item'}}物品装备{{else key == 'soul'}}灵件{{else key == 'fate'}}命格{{/if}}</td>
	<td>
	{{each data}}
		<span class="bluetitle">${name}</span> {{if typeof level !='undefined'}}<span class="greentitle">Lv.${level}</span>{{/if}}<br>
	{{/each}}
	</td>
	<td>${sid_to_name(cid, sid)}</td>
	<td>${content}
	<br>
	<span class="graytitle"><?php echo Lang('apply_user'); ?>：</span><strong>${username}</strong>   <span class="graytitle">${date('Y-m-d H:i', dateline)}</span>
	</td>
	<?php if (!$islimit) { ?>
	{{if status > 1}}
	<td>-</td>
	{{else}}
	<td data-id="${id}"><a href="javascript:;" class="revoke"><?php echo Lang('revoke'); ?></a></td>
	{{/if}}
	<?php } ?>
	<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('retrieve') ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_retrieve_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<select name="key">
						<option value=""><?php echo Lang('type'); ?></option>
						<option value="item">物品装备</option>
						<option value="fate">命格</option>
						<option value="soul">灵件</option>
					</select>
					<select name="status">
						<option value=""><?php echo Lang('status'); ?></option>
						<option value="1">未处理</option>
						<option value="2">已审批</option>
						<option value="3">关闭/拒绝</option>
						<option value="4">已忽略</option>
					</select>
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>

	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<form id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=check_retrieve" method="get" name="form">
		<table class="global" width="100%" style="min-width:800px;" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:20px;">&nbsp;</th>
				    <th style="width:60px;"><?php echo Lang('status'); ?></th>
				    <th style="width:120px;"><?php echo Lang('player_name'); ?></th>
				    <th style="width:100px;"><?php echo Lang('type'); ?></th>
				    <th style="width:150px;"><?php echo Lang('apply_content'); ?></th>
				    <th style="width:100px;"><?php echo Lang('server'); ?></th>
				    <th style="width:20%"><?php echo Lang('explain'); ?></th>
					<?php if (!$islimit) { ?>
				    <th style="width:100px;"><?php echo Lang('revoke'); ?></th>
					<?php } ?>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="retlist">
				
			</tbody>
			<?php if (!$islimit) { ?>
			<tbody>
				<tr>
					<td><input type="checkbox" id="seletedall" value="1"></td>
					<td colspan="7">
						<input type="radio" name="checktype" value="2"><span class="greentitle"><?php echo Lang('approval'); ?></span>
						<input type="radio" name="checktype" value="3"><?php echo Lang('closed'); ?>
						<input type="radio" name="checktype" value="4"><?php echo Lang('ignore'); ?>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" id="post_btnsubmit" value="<?php echo Lang('submit'); ?>">
					</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
			<?php } ?>
		</table>
		</form>
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
