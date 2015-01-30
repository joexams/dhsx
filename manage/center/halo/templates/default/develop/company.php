<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'companylist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '<?php echo Lang('not_find_company')?>。';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_list";

var dialog = dialog != undefined ? null : '';
$(function(){
	Ha.page.getList(1);

	$('#companylist').on('click', 'a.cd_setting', function(){
		var cid = $(this).attr('data-cid');
		if (cid > 0){
			var url = '<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_info';
			Ha.common.ajax(url, 'html', 'cid='+cid, 'get', 'container', function(data){
				Ha.Dialog.show(data, '<?php echo Lang('company_info_setting'); ?>', 600, 'dialog_c_setting_'+cid);
			}, 1);
		}
	});
	
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);

		var url = '<?php echo INDEX; ?>?m=develop&c=company&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'POST', 'container', function(data){
			var alertclassname = '';
			switch (data.status){
				case 0: 
					alertclassname = 'success'; 
					$('#companylisttpl').tmpl(data.info).prependTo('#companylist').fadeIn(2000, function(){
							var obj = $(this);
							obj.css('background', '#E6791C');
							setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
						});	
					break;
				case 1: alertclassname = 'error'; break;
				Ha.notify.show(data.msg, '', alertclassname);
			}
		}, 1);
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});
});
</script>
<script type="text/template" id="companylisttpl">
<tr>
	<td class="num">${cid}</td>
	<td class="num">${corder}</td>
	<td>{{if name!=''}}${name}{{else}}&nbsp;{{/if}}</td>
	<td>{{if slug!=''}}${slug}{{else}}&nbsp;{{/if}}</td>
	<td>{{if web!=''}}${web}{{else}}&nbsp;{{/if}}</td>
	<td>{{if game_name!=''}}${game_name}{{else}}&nbsp;{{/if}}</td>
	<td>${type}<?php echo Lang('company_platform_type_item') ?></td>
	<td><a href="javascript:;" data-cid="${cid}" class="cd_setting"><?php echo Lang('server_detail_setting') ?></a></td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('company_setting'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					 <label>
					 <?php echo Lang('company_name'); ?>：	
					 <input name="name" type="text" value="" class="ipt_txt_s"> </label>
					 <label> <?php echo Lang('company_website'); ?>：	
					 <input name="web" type="text" value="" class="ipt_txt"> </label>
					  <label><select name="type" class="ipt_select" style="width:100px">
					 	<option value=""><?php echo Lang('type'); ?></option>
					 	<option value="1">1<?php echo Lang('company_platform_type_item')?></option>
					 	<option value="2">2<?php echo Lang('company_platform_type_item')?></option>
					 	<option value="3">3<?php echo Lang('company_platform_type_item')?></option>
					 </select></label>
					 <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="<?php echo Lang('find')?>">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div class="title">
	        <?php echo Lang('company_list')?>
	    </div>
		<div id="dataTable">
		<form name="post_submit" id="post_submit" method="post">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th>&nbsp;</th>
			<th class="num"><?php echo Lang('listsort'); ?></th>
			<th><?php echo Lang('company_name'); ?></th>
			<th><?php echo Lang('company_short_name'); ?></th>
			<th><?php echo Lang('company_domain'); ?></th>
			<th><?php echo Lang('company_game_title'); ?></th>
			<th><?php echo Lang('company_platform_type'); ?></th>
			<th><?php echo Lang('server_detail_setting'); ?></th>
		</tr>
		</thead>
		<tbody id="companylist">
			   
		</tbody>
		<tfoot>
		<tr>
			<td class="num" colspan="2"><span class="greentitle"><?php echo Lang('add_new_record') ?></span></td>
			<td><input type="text" name="name" id="name" class="ipt_txt_s"></td>
			<td><input type="text" name="slug" id="slug" class="ipt_txt_s"></td>
			<td><input type="text" name="web" id="web"  class="ipt_txt"></td>
			<td><input type="text" name="game_name" id="game_name" class="ipt_txt_s"></td>
			<td><select name="type" id="type" class="ipt_select" style="width:120px;">
				<option value="1">1<?php echo Lang('company_platform_type_item') ?></option>
				<option value="2">2<?php echo Lang('company_platform_type_item') ?></option>
				<option value="3">3<?php echo Lang('company_platform_type_item') ?></option>
			</select></td>
			<td>
				<input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit')?>">
			</td>
		</tr>
		</tfoot>
		</table>
		</form>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>