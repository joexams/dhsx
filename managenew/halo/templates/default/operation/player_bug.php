<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, buglist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_bug_list&top="+index+"&recordnum="+recordNum;	
	pageIndex = index;
	$( "#buglist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_bug_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#buglist" ).fadeOut( "medium", function () {
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
		$('#buglist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), buglist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}
		$( "#buglist" ).empty();
		if (data.count > 0){
			$( "#buglisttpl" ).tmpl( buglist ).prependTo( "#buglist" );
			$( "#buglist" ).stop(true,true).hide().slideDown(400);
		}
	}
}


var dialog;
$(document).ready(function(){
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('#sid')).remove();
			$('#servertpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});

	getList(1);

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});
	/**
	 * 删除
	 * @return {[type]} [description]
	 */
	$('#buglist').on('click', 'a.delete', function(){
		var obj = $(this);
		var id = obj.attr('data-id');
		if (id > 0){
			if (confirm('确定要删除此玩家反馈吗？删除后将不可恢复！')){
				$.ajax({
					url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=delete_bug',
					data: {id: id},
					dataType: 'json',
					success: function(data){
						if (data.status == 0){
							obj.parent().parent('tr').remove(); 
						}
					}
				})
			}
		}
		return false;
	});
	/**
	 * 屏蔽、取消屏蔽
	 * @return {[type]} [description]
	 */
	$('#buglist').on('click', 'a.screen', function(){
		var obj = $(this), id = obj.attr('data-id'), flag = obj.attr('data-flag');
		if (id > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=screen_bug',
				data: {id: id, flag: flag},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						for(var key in buglist){
							if (id == buglist[key].id){
								buglist[key].status = flag;
								obj.parent().parent('tr').html($('#buglisttpl').tmpl(buglist[key]).html());
								break;
							}
						}
					}
				}
			});
		}
		return false;
	});
	/**
	 * 回复
	 * @return {[type]} [description]
	 */
	$('#buglist').on('click', 'a.reply', function(){
		var obj = $(this), id = obj.attr('data-id');
		if (id > 0){
			for(var key in buglist){
				if (id == buglist[key].id){
					dialog = $.dialog({id: 'reply', width: 500, title: '<?php echo Lang("reply") ?>'});
					dialog.content($('#replytpl').tmpl(buglist[key]).html());
					break;
				}
			}
		}
	});

	$('#pop_post_submit').live('submit', function(e){
		e.preventDefault();
		var id = $('#bugid').val();
		if (id > 0){
			$('#pop_btnsubmit').attr('disabled', 'disabled');
			var objform = $(this);
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=reply_bug',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							for(var key in buglist){
								if (id == buglist[key].id){
									buglist[key].status = data.info.status;
									buglist[key].reply_content = data.info.reply_content;
									buglist[key].reply_time = data.info.reply_time;
									buglist[key].reply_user = data.info.reply_user;
									$('#bug_'+id).html($('#buglisttpl').tmpl(buglist[key]).html());
									break;
								}
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#pop_op_tips').attr('class', alertclassname);
					$('#pop_op_tips').children('p').html(data.msg);
					$('#pop_op_tips').fadeIn();
					setTimeout( function(){
						$('#pop_op_tips').fadeOut();
						$('#pop_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
		}
		return false;
	});

});
</script>

<script type="text/template" id="servertpl">
	<option value="${sid}" data-ver="${server_ver}" {{if sid == <?php echo $_GET['sid'] ? intval($_GET['sid']) : 0;?>}}selected{{/if}}>${name}-${o_name}</option>
</script>

<script type="text/template" id="replytpl">
<div>
<form id="pop_post_submit" method="post" action="<?php echo INDEX; ?>?m=operation&c=interactive&v=reply_bug">
<div id="pop_op_tips" style="display: none;"><p></p></div>
<table class="global" style="min-width: 520px" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<th><?php echo Lang('player') ?></th>
			<td>${username} (${nickname})</td>
		</tr>
		<tr>
			<th><?php echo Lang('bug_content') ?></th>
			<td>${content}<br><span style="color:#999">${date('Y-m-d H:i', submit_time)}</span></td>
		</tr>
		<tr>
			<th><?php echo Lang('reply') ?></th>
			<td>
				<textarea name="reply_content" style="width:350px;height:80px;">${reply_content}</textarea>
			</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td>
			<p>
				<input type="submit" value="提交" id="pop_btnsubmit">
				<input type="hidden" name="id" id="bugid" value="${id}">
				<input type="hidden" name="doSubmit" value="1">
				<input type="button" onclick="dialog.close();" class="cancel_btn" value="关闭">
			</p>
			</td>
		</tr>
	</tbody>
</table>
</form>
</div>
</script>

<script type="text/template" id="buglisttpl">
<tr id="bug_${id}">
	<td>${id}</td>
	<td>{{if type==1}}BUG{{else type == 2}}<?php echo Lang('complaints') ?>{{else type==3}}<?php echo Lang('suggest') ?>{{else type==4}}<?php echo Lang('other') ?>{{/if}}</td>
	<td>${server_o_name}：${server_name}</td>
	<td>${username} (${nickname})<span sty="color: red" id="player_${player_id}"></span></td>
	<td>${content}<br><span style="color:#999">${date('Y-m-d H:i', submit_time)}</span></td>
	<td>{{if reply_time>0}}${reply_content}<br><span style="color:#999">${date('Y-m-d H:i', reply_time)}{{else}}-{{/if}}</span></td>
	<td>
	    {{if status == 1}}
	    <span style="color:green"><?php echo Lang('byreply') ?></span>
	    <a href="javascript:;" data-id="${id}" data-flag="-1" class="screen"><?php echo Lang('screen') ?></a>
	    {{else status == -1}}
	    <span style="color:#999"><?php echo Lang('byscreen') ?></span>
	    <a href="javascript:;" data-id="${id}" data-flag="{{if reply_time>0}}1{{else}}0{{/if}}" class="screen"><?php echo Lang('cancelscreen') ?></a>
	    {{else status == 0}}
	    <span style="color:red"><?php echo Lang('new_post') ?></span>
	    <a href="javascript:;" data-id="${id}" class="reply"><?php echo Lang('reply') ?></a>
	    <a href="javascript:;" data-id="${id}" data-flag="-1" class="screen"><?php echo Lang('screen') ?></a>
	    {{/if}}
		<a href="javascript:;" data-id="${id}" class="delete"><?php echo Lang('delete') ?></a>
	</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('player_bug') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=server_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<select name="sid" id="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<select name="type" id="type">
						<option value="0"><?php echo Lang('type') ?></option>
						<option value="1">BUG</option>
						<option value="2"><?php echo Lang('complaints') ?></option>
						<option value="3"><?php echo Lang('suggest') ?></option>
						<option value="4"><?php echo Lang('other') ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<select name="status" id="status">
						<option value="0"><?php echo Lang('new_post') ?></option>n
						<option value="1"><?php echo Lang('byreply') ?></option>
						<option value="-1"><?php echo Lang('byscreen') ?></option>
						<option value="2"><?php echo Lang('all') ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<?php echo Lang('player'); ?>：<input type="text" name="playername" id="playername" value="<?php echo urldecode($_GET['title']) ?>" />
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input type="hidden" name="cid" value="-1">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
				<p>
				</p>
			</li>
		</ul>
		</form>
	</div>
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:50px;"><?php echo Lang('type'); ?></th>
					<th style="width:5%"><?php echo Lang('server'); ?></th>
					<th style="width:12%"><?php echo Lang('player'); ?></th>
					<th style="width:16%"><?php echo Lang('bug_content'); ?></th>
					<th style="width:15%"><?php echo Lang('reply_content'); ?></th>
					<th style="width:11%"><?php echo Lang('reply'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="buglist">

			</tbody>
		</table>
		<div class="pagination pager" id="pager"></div>
		<!-- End form elements -->
	</div>
</div>
