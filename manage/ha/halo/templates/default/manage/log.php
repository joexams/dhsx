<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'loglist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '没有找到日志数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=log&v=<?php echo ROUTE_V ?>&op=<?php echo $op; ?>";

$(function(){
	Ha.page.getList(1);

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
			var url = '<?php echo INDEX; ?>?m=manage&c=log&v=delete';
			var queryData = 'logid='+logid+'&op=<?php echo $op; ?>';
			Ha.common.ajax(url, 'json', queryData, 'post', 'container', function(data){
					if (data.status == 0) {
						obj.parent().parent('tr').remove();
					}
				}
			);
		}
		return false;
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});

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
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=player_info';
			var queryData = {id: playerid, sid: sid};
			Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
					Ha.Dialog.show(data, title, 880, 'player_info_'+playerid);
				}, 1
			);
		}
	});
	<?php } ?>
});
</script>

<script type="text/template" id="loglisttpl">
<tr>
	<?php if ($op != 'login'){ ?>
	<td class="num">${logid}</td>
	<?php } ?>
	<?php if ($op != 'active' && $op != 'cron') { ?>
	<td>${username}</td>
	<?php } ?>
	<?php if ($op == 'login'){ ?>
	<td>${password}</td>
	<?php } ?>
	<?php if ($op == 'source' || $op == 'active'){ ?>
	<td><a href="javascript:;" data-sid="${sid}" data-pid="${playerid}" class="player_info" title="${playernickname}">${playername}{{if playernickname != ''}}<br>${playernickname}{{/if}}</a></td>
	<?php } ?>
	<td>{{html nl2br(content)}}</td>
	<?php if ($op != 'active' && $op != 'cron') { ?>
	<td>${ip}</td>
	<td><span class="graytitle">${iparea}</span></td>
	<?php }else { ?>
	<td>${key}</td>
	<td>${crontime}</td>
	<?php } ?>
	<td>${date('Y-m-d H:i:s', dateline)}</td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('log_title'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					<?php if ($op == 'login'){ ?>
					 <label><?php echo Lang('account'); ?>：
						<input type="text" id="username" name="username" class="ipt_txt" value="">
					 </label>
					 <label>IP：
						<input type="text" id="ip" name="ip" class="ipt_txt_s" value="">
					 </label>
					 <?php }else if ($op == 'source'){ ?>
					<label>
					<select class="ipt_select" name="cid" id="cid" style="width:100px">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select></label>
					<label><select class="ipt_select" name="sid" id="sid" style="width:130px">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select></label>
					<label>
					<select class="ipt_select" name="key" style="width:130px">
						<option value="">所有操作项</option>
						<?php foreach ($keylist as $key => $value) { ?>
						<option value="<?php echo $value['key']; ?>"><?php echo $value['title']; ?></option>
						<?php } ?>
					</select></label>
					<label><?php echo Lang('player'); ?>：<input name="playername" class="ipt_txt" type="text" value=""></label>
					<?php } ?>
					 <label>包含内容：
						<input type="text" id="content" name="content" class="ipt_txt" value="">
					 </label>
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>

	<div class="column cf<?php if ($op == 'source') { ?> whitespace<?php } ?>" id="table_column">
		<div class="title"><?php echo Lang('log_'.ROUTE_V.'_list') ?></div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<?php if ($op != 'login'){ ?>
	    	<th>&nbsp;</th>
	    	<?php } ?>
	    	<?php if ($op != 'active' && $op != 'cron') { ?>
	    	<th><?php echo Lang('username'); ?></th>
	    	<?php } ?>
	    	<?php if ($op == 'login'){ ?>
	    	<th><?php echo Lang('password'); ?></th>
	    	<?php } ?>
	    	<?php if ($op == 'source' || $op == 'active'){ ?>
			<th><?php echo Lang('player') ?></th>
			<?php } ?>
	    	<th<?php if ($op == 'source') { ?> style="width:350px;"<?php } ?>><?php echo Lang('log_content'); ?></th>
	    	<?php if ($op != 'active' && $op != 'cron') { ?>
			<th><?php echo Lang('log_ip') ?></th>
			<th>&nbsp;</th>
			<?php }else { ?>
			<th><?php echo Lang('cron_key'); ?></th>
			<th><?php echo Lang('cron_time') ?></th>
			<?php } ?>
	    	<th><?php echo Lang('log_date'); ?></th>
		</tr>
		</thead>
		<tbody id="loglist">
			   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>