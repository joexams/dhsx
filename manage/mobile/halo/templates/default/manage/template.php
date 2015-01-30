<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'tmpllist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '<?php echo Lang('no_find_data')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=template&v=ajax_init_list";

$(function(){
	Ha.page.getList(1);
	
	//--------修改
	$('#tmpllist').on('click', 'a.edit', function(){
		var obj    = $(this);
		var tid = obj.attr('data-tid');
		if (tid > 0){
			templateManage(tid);
		}
	});

	$('#tmpllist').on('click', 'a.preview', function(){
		var strHtml = '<div class="frm_cont"><ul>'+$(this).siblings('textarea').text()+'</ul></div>';
		Ha.Dialog.show(strHtml, "<?php echo Lang('preview') ?>", 500, 'dialog_preview');
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		templateManage(0);
	});
	//----------删除----------
	$('#tmpllist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var tid = obj.attr('data-tid');
		var tname = obj.attr('data-rolename');
		if (tid > 0){
			if (confirm("<?php echo Lang('delete_template_confirm')?>")){
				var url = '<?php echo INDEX; ?>?m=manage&c=template&v=delete';
				var queryData = 'tid='+tid;
				Ha.common.ajax(url, 'json', queryData, 'post');
			}
		}
	});
	
});
function templateManage(tid){
	var url = '<?php echo INDEX; ?>?m=manage&c=template&v=add';
	tid = tid || 0;
	tid = parseInt(tid);
	var title = tid > 0 ? '<?php echo Lang('edit_template')?>' : '<?php echo Lang('add_template')?>';
	Ha.common.ajax(url, 'html', 'tid='+tid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'templateManageDialog');
	}, 1);
}
</script>

<script type="text/template" id="tmpllisttpl">
<tr>
	<td class="num">${tid}</td>
	<td>${title}</td>
	<td><strong class="greentitle">${key}</strong></td>
	<td>
	({{each args}}<br>$${$value.arg} //${$value.tips}{{/each}}<br>)
	</td>
	<td>
	({{each rtns}}<br>'${$value.rtn}' //${$value.tips}{{/each}}<br>)
	</td>
	<td>{{if version != ''}}${version}{{else}}&nbsp;{{/if}}</td>
	<td>
		<textarea style="display: none;">${content}</textarea>
		<a href="javascript:;" class="preview"><?php echo Lang('preview') ?></a>
		<a href="javascript:;" class="edit" data-tid="${tid}"><?php echo Lang('edit') ?></a>
		<a href="javascript:;" class="delete" data-tid="${tid}" data-rolename="${title}"><?php echo Lang('delete') ?></a>
	</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('template_title'); ?></span></h2>
<div class="container" id="container">
<div class="column whitespace cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add_template')?>">
	            </div>
	        </div>
	        <?php echo Lang('template_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th>&nbsp;</th>
			    	<th><?php echo Lang('template_form_title'); ?></th>
			    	<th><?php echo Lang('template_form_key'); ?></th>
			    	<th><?php echo Lang('template_form_key_args'); ?></th>
			    	<th><?php echo Lang('template_form_key_rtns'); ?></th>
			    	<th><?php echo Lang('version'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="tmpllist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
</div>
</div>