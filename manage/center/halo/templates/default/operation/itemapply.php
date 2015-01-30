<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'applylist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '<?php echo Lang('itemapply_empty_message')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_itemapply_list";

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

	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});
	Ha.page.getList(1);

	//撤销
	$('#applylist').on('click', 'a.revoke', function(){
		var obj = $(this);
		var aid = obj.parent('td').attr('data-id');
		if (aid > 0 && confirm('<?php echo Lang('confirm_to_del_itemapply')?>')) {

			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=del_itemapply';
			Ha.common.ajax(url, 'json', {aid: aid}, 'get', 'container', function(data){
				obj.parent().parent('tr').remove();
			});
		}
	});

	<?php if ($has_check_priv) { ?>
	$('#all_check').on('change', function() {
		if ($(this).is(':checked')) {
			$('#applylist input:checkbox').attr('checked', 'checked');
		}else {
			$('#applylist input:checkbox').removeAttr('checked');
		}
	});

	$('#applylist').on('click', 'a.approval', function(){
		var obj = $(this);
		var aid = obj.parent('td').attr('data-id');
		if (aid > 0) {
			$('#applylist input:checkbox').removeAttr('checked');
			$(':radio[name="checktype"][value="2"]').attr('checked','checked');
			obj.parent('td').siblings('td').find(':checkbox').attr('checked', 'checked');
			$('#check_post_submit').submit();
		}
	});
	//回复
	$('#applylist').on('click', 'a.reply', function(){
		var obj = $(this);
		var aid = obj.parent('td').attr('data-id');
		if (aid > 0) {
			$('#reply_aid').val(aid);
			var content = $( "#reply_submit_area" ).tmpl({"aid": aid}).html();
			Ha.Dialog.show(content, '<?php echo Lang('reply').Lang('itemapply') ?>', 600, 'reply_dlg', document.getElementById('reply_'+aid));
		}
	});
	//回复提交
	$('#post_reply_submit').live('click', function(e) {
		e.preventDefault();
		var aid = $('#reply_aid').val();
		var replycontent = $.trim($('#replycontent').val());
		if (aid > 0 && replycontent != '') {

			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=reply_itemapply';
			Ha.common.ajax(url, 'json', {aid: aid, replycontent: replycontent}, 'post', 'container', function(data){
				if (data.status == 0) {
					Ha.page.getList(Ha.page.pageIndex);
					$.dialog({id: 'reply_dlg'}).close();
				}
			});
			
		}
		return ;
	});

	//审批
	$('#check_post_submit').on('submit', function(e) {
		e.preventDefault();
		var obj = $(this);
		if ($(':radio[name="checktype"]').is(':checked') && $('#applylist :checked').size() > 0) {

			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=check_itemapply';
			Ha.common.ajax(url, 'json', obj.serialize(), 'post', 'container', function(data){
				Ha.page.getList(Ha.page.pageIndex);
			});
		}else {
			Ha.notify.show('<?php echo Lang('select_need_examine_applyrecord')?>', '', 'error');
		}
	});
	<?php } ?>

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		itemApplyManage();
	});
});

function itemApplyManage(){
	var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=add_itemapply';
	Ha.common.ajax(url, 'html', '', 'get', 'container', function(data){
		Ha.Dialog.show(data, '<?php echo Lang('add_itemapply')?>', 600, 'itemApplyManageDialog');
	}, 1);
}
</script>
<?php if ($has_check_priv) { ?>
<script type="text/template" id="reply_submit_area">
<div class="container">
	<div class="frm_cont">
    <ul>
        <li>
           <span class="frm_info"><em>*</em><?php echo Lang('reply_content') ?>：</span>
           <textarea name="replycontent" class="ipt_textarea" id="replycontent" style="width:450px;height:60px;"></textarea>
        </li>
        <li> 
			<span class="frm_info">&nbsp;</span>
        </li>
    </ul>
	</div>
	<div class="float_footer">    
		<div class="frm_btn"> 
		<input type="hidden" name="doSubmit" value="1">
        <input type="hidden" name="aid" id="reply_aid" value="${aid}">
        <input type="button" id="post_reply_submit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
		<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'reply_dlg'}).close();">
		</div>
	</div>
</div>
</script>
<?php } ?>
<script type="text/template" id="applylisttpl">
<tr>
	<?php if ($has_check_priv) { ?>
	<td class="num">{{if status == 1}}<input type="checkbox" name="aid[]" value="${aid}">{{else}}--{{/if}}</td>
	<?php } ?>
	<td>
	{{if status == 1}}
	<span class="redtitle"><?php echo Lang('not_handle'); ?></span>
	{{else status == 2}}
	<span class="greentitle">√<?php echo Lang('handle'); ?></span>
	{{else status == 3}}
	<span class="graytitle"><?php echo Lang('closed'); ?></span>
	{{else status == 4}}
	<?php echo Lang('ignore'); ?>
	{{/if}}
	</td>
	<td>{{html sid_to_name(sid)}}</td>
	<td>${player_name}</td>
	<td>${content!=''?content: '&nbsp;'}</td>
	<td>${case_content}<br><span class="graytitle"><?php echo Lang('apply_user'); ?>：</span><strong>${username}</strong><br><span class="graytitle">${date('Y-m-d H:i:s', dateline)}</span></td>
	<td>${reply_content!=''?reply_content: '&nbsp;'}{{if reply_time > 0}}<br><span class="graytitle">${date('Y-m-d H:i:s', reply_time)}</span>{{/if}}</td>
	
	<td data-id="${aid}">
	{{if status == 1}}
	<?php if ($has_check_priv) { ?>
	<a href="javascript:;" class="approval"><?php echo Lang('approval'); ?></a>
	<a href="javascript:;" class="reply" id="reply_${aid}"><?php echo Lang('reply'); ?></a>
	<?php } ?>
	<a href="javascript:;" class="revoke"><?php echo Lang('revoke'); ?></a>
	{{else}}
	--
	{{/if}}
	</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('itemapply'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">
				<div class="tool_group">
					<label>
					<?php echo Lang('player') ?>：<input type="text" class="ipt_txt" name="playername" value=""/>
					<?php echo Lang('content_contain'); ?>： <input type="text" class="ipt_txt" name="keyword" id="keyword" value="" />
					</label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="<?php echo Lang('find')?>">
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i><?php echo Lang('advanced_search')?></a>
				</div>	
			</div>
			<div class="control cf" id="moreConditions" style="display: none;">
			<div class="frm_cont">
				<ul>
					<li name="condition">
						<label class="frm_info"><?php echo Lang('more_conditions')?>：</label>
						<select name="cid" class="cid ipt_select">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>

					 	<select name="sid" class="sid ipt_select">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>

					<select name="type" class="ipt_select" style="width:80px">
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
					</select><

					 <select name="status" id="status" class="ipt_select" style="width:80px">
						<option value="0"><?php echo Lang('status') ?></option>
						<option value="1"><?php echo Lang('not_handle') ?></option>
						<option value="2"><?php echo Lang('handle') ?></option>
						<option value="3"><?php echo Lang('closed') ?></option>
						<option value="4"><?php echo Lang('ignore') ?></option>
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
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add_itemapply') ?>">
	            </div>
	        </div>
	        <?php echo Lang('itemapply_list')?>
	    </div>
		<div id="dataTable">
		<form id="check_post_submit" action="" name="form">
		<table>
			<thead>
			    <tr>
			    	<?php if ($has_check_priv) { ?>
			    	<th class="num"><?php echo Lang('select'); ?></th>
			    	 <?php } ?>
			    	<th><?php echo Lang('status'); ?></th>
			    	<th><?php echo Lang('server'); ?></th>
			    	<th><?php echo Lang('player_name'); ?></th>
			    	<th><?php echo Lang('apply_content'); ?></th>
			    	<th><?php echo Lang('apply_case_content'); ?></th>
			    	<th><?php echo Lang('reply_content'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="applylist">
			   
			</tbody>
			<?php if ($has_check_priv) { ?>
			<tfoot>
				<tr>
					<td class="num"><input type="checkbox" id="all_check" value="1"></td>
					<td colspan="7">
						<input type="radio" name="checktype" value="2"><span class="greentitle"><?php echo Lang('approval'); ?></span>
						<input type="radio" name="checktype" value="3"><?php echo Lang('closed'); ?>
						<input type="radio" name="checktype" value="4"><?php echo Lang('ignore'); ?>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" class="btn_sbm" id="check_btnsubmit" value="<?php echo Lang('submit'); ?>">
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