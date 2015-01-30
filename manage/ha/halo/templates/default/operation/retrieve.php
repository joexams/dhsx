<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'retlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '尚未有盗号找回申请。';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_retrieve_list";


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
	<?php if ($has_check_priv) { ?>
	/**
	 * 撤销
	 * @return {[type]} [description]
	 */
	$('#retlist').on('click', 'a.revoke', function() {
		var obj = $(this).parent('td'); id = obj.attr('data-id');
		if (id > 0 && confirm('确定撤销盗号找回申请吗？')) {
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=delete_retrieve';
			Ha.common.ajax(url, 'json', {id: id}, 'get', 'container', function(data){
				obj.parent().remove();
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
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=again_retrieve';
			Ha.common.ajax(url, 'json', {id: id}, 'get', 'container', function(data){
				Ha.page.getList(Ha.page.pageIndex);
			});
		}
	});
	
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
	 	if ($(':radio[name="checktype"]').is(':checked') && $('#retlist :checked').size() > 0) {

	 		var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=check_retrieve';
			Ha.common.ajax(url, 'json', obj.serialize(), 'post', 'container', function(data){
				Ha.page.getList(Ha.page.pageIndex);
			});
		}else {
			Ha.notify.show('请选择需要审批的记录', '', 'error');
		}

	 });

 	<?php } ?>

	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});
	Ha.page.getList(1);

	$('#retlist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = $(this).attr('title');
		if (playerid > 0 && sid > 0){
			var sname = '';
			if (typeof global_serverlist != 'undefined') {
				for(var key in global_serverlist) {
					if (global_serverlist[key].sid == sid) {
						sname = global_serverlist[key].name;
						break;
					}
				}
			}

			var url = '<?php echo INDEX; ?>?m=report&c=player&v=player_info';
			Ha.common.ajax(url, 'html', {id: playerid, sid: sid, sname: sname}, 'get', 'container', function(data){
				Ha.Dialog.show(data, title, 800, 'player_info_'+playerid);
			}, 1);
		}
	});

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});
});
</script>

<script type="text/template" id="retlisttpl">
<tr>
	<?php if ($has_check_priv) { ?>
	{{if status > 1}}
	<td class="num">--</td>
	{{else}}
	<td class="num"><input type="checkbox" name="retid[]" value="${id}"></td>
	{{/if}}
	<?php } ?>
	<td data-id="${id}">
	{{if status == 2}}
	<?php if ($has_check_priv) { ?>
		<a href="javascript:;" class="again"><span class="greentitle">√已获批</span></a>
	<?php }else { ?>
		<span class="greentitle">√已获批</span>
	<?php } ?>
	{{else status == 3}}
	<span class="graytitle">关闭/拒绝</span>
	{{else}}<span class="redtitle">未处理</span>{{/if}}</td>
	<td><a href="javascript:;" data-sid="${sid}" data-pid="${player_id}" class="player_info" title="${playername}">${playername}{{if nickname!=''}}<br>${nickname}{{/if}}</a></td>
	<td>{{if key == 'item'}}物品装备{{else key == 'soul'}}灵件{{else key == 'fate'}}命格{{/if}}</td>
	<td>
	{{each data}}
		<span class="bluetitle">${name}</span> {{if typeof level !='undefined' && key != 'fate'}}<span class="greentitle">Lv.${level}</span>{{/if}}<br>
	{{/each}}
	</td>
	<td>${sid_to_name(cid, sid)}</td>
	<td>${content}
	<br><span class="graytitle"><?php echo Lang('apply_user'); ?>：</span><strong>${username}</strong> <br>  <span class="graytitle">${date('Y-m-d H:i', dateline)}</span>
	</td>
	<?php if ($has_check_priv) { ?>
	{{if status > 1}}
	<td>-</td>
	{{else}}
	<td data-id="${id}"><a href="javascript:;" class="revoke"><?php echo Lang('revoke'); ?></a></td>
	{{/if}}
	<?php } ?>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('retrieve'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">
				<div class="tool_group">
					<label>
					<?php echo Lang('player') ?>：<input type="text" class="ipt_txt" name="playername" value=""/>
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
						<select name="key" class="ipt_select">
							<option value=""><?php echo Lang('type'); ?></option>
							<option value="item">物品装备</option>
							<option value="fate">命格</option>
							<option value="soul">灵件</option>
						</select>
						<select name="status" class="ipt_select">
							<option value=""><?php echo Lang('status'); ?></option>
							<option value="1">未处理</option>
							<option value="2">已审批</option>
							<option value="3">关闭/拒绝</option>
							<option value="4">已忽略</option>
						</select>
					</li>
				</ul>
			</div>
			</div>
			</form>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">盗号找回列表</div>
		<div id="dataTable">
		<form id="post_submit" action="" name="form">
		<table>
			<thead>
			    <tr>
			    	<?php if ($has_check_priv) { ?>
					<th>&nbsp;</th>
					<?php } ?>
				    <th><?php echo Lang('status'); ?></th>
				    <th><?php echo Lang('player_name'); ?></th>
				    <th><?php echo Lang('type'); ?></th>
				    <th><?php echo Lang('apply_content'); ?></th>
				    <th><?php echo Lang('server'); ?></th>
				    <th><?php echo Lang('explain'); ?></th>
					<?php if ($has_check_priv) { ?>
				    <th><?php echo Lang('revoke'); ?></th>
					<?php } ?>
			    </tr>
			</thead>
			<tbody id="retlist">
			   
			</tbody>
			<?php if ($has_check_priv) { ?>
			<tfoot>
				<tr>
					<td class="num"><input type="checkbox" id="seletedall" value="1"></td>
					<td colspan="7">
						<input type="radio" name="checktype" value="2"><span class="greentitle"><?php echo Lang('approval'); ?></span>
						<input type="radio" name="checktype" value="3"><?php echo Lang('closed'); ?>
						<input type="radio" name="checktype" value="4"><?php echo Lang('ignore'); ?>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" class="btn_sbm" id="post_btnsubmit" value="<?php echo Lang('submit'); ?>">
					</td>
				</tr>
			</tfoot>
			<?php } ?>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>