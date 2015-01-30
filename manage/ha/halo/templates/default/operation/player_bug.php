<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'buglist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '尚未有玩家反馈。';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_bug_list";

$(function(){
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

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});

	Ha.page.getList(1);
	/**
	 * 删除
	 * @return {[type]} [description]
	 */
	$('#buglist').on('click', 'a.delete', function(){
		var obj = $(this);
		var id = obj.attr('data-id');
		if (id > 0){
			if (confirm('确定要删除此玩家反馈吗？删除后将不可恢复！')){
				Ha.common.ajax('<?php echo INDEX; ?>?m=operation&c=interactive&v=delete_bug', 'json', {id: id}, 'get', 'container', function(data){
					if (data.status == 0){
						obj.parent().parent('tr').remove(); 
					}
				});
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
			Ha.common.ajax('<?php echo INDEX; ?>?m=operation&c=interactive&v=screen_bug', 'json', {id: id, flag: flag}, 'get', 'container', function(data){
				if (data.status == 0){
					for(var key in buglist){
						if (id == buglist[key].id){
							buglist[key].status = flag;
							obj.parent().parent('tr').html($('#buglisttpl').tmpl(buglist[key]).html());
							break;
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
					Ha.Dialog.show($('#replytpl').tmpl(buglist[key]).html(), '<?php echo Lang("reply") ?>', '', 'reply');
					break;
				}
			}
		}
	});

	$('#pop_post_submit').live('submit', function(e){
		e.preventDefault();
		var id = $('#bugid').val();
		if (id > 0){
			var objform = $(this);
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=reply_bug';
			Ha.common.ajax(url, 'json', objform.serialize(), 'POST', 'container', function(data){
				if (data.status == 0){
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
				}
			});
		}
		return false;
	});

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
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
	<td class="num">${id}</td>
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


<h2><span id="tt"><?php echo Lang('player_bug'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">
				<div class="tool_group">
					<label>
					<?php echo Lang('player') ?>：<input type="text" class="ipt_txt" name="playername" value="<?php echo urldecode($_GET['title']) ?>"/>
					</label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="查 询">
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>高级查询</a>
				</div>	
			</div>
			<div class="control cf" id="moreConditions" style="display: none;">
			<div class="frm_cont">
				<ul>
					<li name="condition">
						<label class="frm_info">更多条件：</label>
						<select name="cid" id="cid" class="ipt_select">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid" class="ipt_select">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<select name="type" id="type" class="ipt_select">
						<option value="0"><?php echo Lang('type') ?></option>
						<option value="1">BUG</option>
						<option value="2"><?php echo Lang('complaints') ?></option>
						<option value="3"><?php echo Lang('suggest') ?></option>
						<option value="4"><?php echo Lang('other') ?></option>
					</select>
					<select name="status" id="status" class="ipt_select">
						<option value="0"><?php echo Lang('new_post') ?></option>n
						<option value="1"><?php echo Lang('byreply') ?></option>
						<option value="-1"><?php echo Lang('byscreen') ?></option>
						<option value="2"><?php echo Lang('all') ?></option>
					</select>
					</li>
				</ul>
			</div>
			</div>
			</form>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">
	        玩家反馈数据
	    </div>
		<div id="dataTable">
		<form id="check_post_submit" action="" name="form">
		<table>
			<thead>
			    <tr>
			    	<th class="num">&nbsp;</th>
					<th><?php echo Lang('type'); ?></th>
					<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('player'); ?></th>
					<th><?php echo Lang('bug_content'); ?></th>
					<th><?php echo Lang('reply_content'); ?></th>
					<th><?php echo Lang('reply'); ?></th>
			    </tr>
			</thead>
			<tbody id="buglist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>