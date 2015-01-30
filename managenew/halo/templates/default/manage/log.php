<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, op = '<?php echo $op; ?>', loglist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=log&v=ajax_list&op="+op+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#loglist" ).fadeOut( "medium", function() {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function getsearchList(index) {
	var query = "<?php echo INDEX; ?>?m=manage&c=log&v=ajax_list&op="+op+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#loglist" ).fadeOut( "medium", function() {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function(data){
				showList(data, 1);
			}
		});
	});
}
function showList( data, type) {
	if (data.status == -1){
		$('#loglist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), loglist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}
		$( "#loglist" ).empty();
		if (data.count > 0){
			$( "#loglisttpl" ).tmpl( loglist ).prependTo( "#loglist" );
			$( "#loglist" ).stop(true,true).hide().slideDown(400);

			if (pageCount > 1){
				$( "#loglist" ).parent().parent('div.content').css('height', $('#loglist').parent('table.global').css('height'));
			}
		}
	}
}
function nl2br(str){
	var breakTag = '<br>';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

var dialog = dialog != undefined ? null : '';
$(document).ready(function(){
	getList( pageIndex );

	<?php if ($op == 'source'){ ?>
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}
	$('#cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});
	<?php } ?>


	//---------删除
	$('#loglist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var logid = obj.attr('data-logid');
		if (logid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=log&v=delete',
				data: 'logid='+logid+'&op='+op,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					obj.parent().parent('tr').remove();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
		return false;
	});

	<?php if ($op == 'login' || $op == 'source'){ ?>
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});
	<?php } ?>

	<?php if ($op == 'source' || $op == 'active'){ ?>
	$('#loglist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = $(this).attr('title');
		if (playerid > 0 && sid > 0) {
			var sname = '';
			for(var key in global_serverlist) {
				if (global_serverlist[key].sid == sid) {
					sname = global_serverlist[key].name;
					break;
				}
			}
			dialog = $.dialog({id: 'player_info_'+playerid, width: 880, title: title});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=detail_info',
				data: {id: playerid, sid: sid, sname: sname},
				success: function(data){
					dialog.content(data);
				}
			});
		}
	});
	<?php } ?>
	

	$('tr').on({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
		}
	});
});
</script>

<script type="text/template" id="loglisttpl">
<tr>
	<td>
		<input type="checkbox" value="${logid}">
	</td>
	<?php if ($op != 'login'){ ?>
	<td>${logid}</td>
	<?php } ?>
	<?php if ($op != 'active') { ?>
	<td>${userid}</td>
	<td>${username}</td>
	<?php } ?>
	<?php if ($op == 'login'){ ?>
	<td>${password}</td>
	<?php } ?>
	<?php if ($op == 'source' || $op == 'active'){ ?>
	<td><a href="javascript:;" data-sid="${sid}" data-pid="${playerid}" class="player_info" title="${playername}">${playername}{{if playernickname != ''}}-${playernickname}{{/if}}</a></td>
	<?php } ?>
	<td>{{html nl2br(content)}}</td>
	<?php if ($op != 'active') { ?>
	<td>${ip}</td>
	<?php }else { ?>
	<td>${key}</td>
	<td>${crontime}</td>
	<?php } ?>
	<td>${date('Y-m-d H:i:s', dateline)}</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('log_title'); ?></span></a></li>
	</ul>
	<br class="clear">
	<?php if ($op == 'login'){ ?>
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=log&v=ajax_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<?php echo Lang('account'); ?>：<input name="username" type="text" value="" size="20">
				</p>
			</li>
			<li>
				<p>
					IP：<input name="ip" type="text" value="" size="20">
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
				<p>
				</p>
			</li>
		</ul>
		</form>
	</div>
	<?php }else if ($op == 'source') { ?>
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=log&v=ajax_list" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<?php echo Lang('server') ?>：
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<?php echo Lang('player'); ?>：<input name="playername" type="text" value="" size="20">
					<?php echo Lang('operation'); ?>项：
					<select name="key">
						<option value="">所有操作项</option>
						<?php foreach ($keylist as $key => $value) { ?>
						<option value="<?php echo $value['key']; ?>"><?php echo $value['title']; ?></option>
						<?php } ?>
					</select>
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<?php } ?>
	<!-- 用户列表 -->
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('log_'.ROUTE_V.'_list') ?></h2>
		</div>	
	</div>

	<div class="content">
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<th style="width:30px;">
			    		<input type="checkbox" id="check_all" name="check_all">
			    	</th>
			    	<?php if ($op != 'login'){ ?>
			    	<th style="width:50px;">ID</th>
			    	<?php } ?>
			    	<?php if ($op != 'active') { ?>
			    	<th style="width:50px;"><?php echo Lang('userid'); ?></th>
			    	<th style="width:80px;"><?php echo Lang('username'); ?></th>
			    	<?php } ?>
			    	<?php if ($op == 'login'){ ?>
			    	<th style="width:50px;"><?php echo Lang('password'); ?></th>
			    	<?php } ?>
			    	<?php if ($op == 'source' || $op == 'active'){ ?>
					<th style="width:150px;"><?php echo Lang('player') ?></th>
					<?php } ?>
			    	<th style="width:30%"><?php echo Lang('log_content'); ?></th>
			    	<?php if ($op != 'active') { ?>
					<th style="width:100px;"><?php echo Lang('log_ip') ?></th>
					<?php }else { ?>
					<th style="width:80px;"><?php echo Lang('cron_key'); ?></th>
					<th style="width:120px;"><?php echo Lang('cron_time') ?></th>
					<?php } ?>
			    	<th style="width:120px;"><?php echo Lang('log_date'); ?></th>
			    	<th style="width:50px;"><?php echo Lang('delete'); ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="loglist">
			   
			</tbody>
		</table>
		
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- End pagination -->	
	</div>
	<div class="pagination pager" id="pager"></div>	
</div>
